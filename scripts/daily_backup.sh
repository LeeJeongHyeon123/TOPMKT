#!/bin/bash

# 탑마케팅 플랫폼 일일 자동 백업 스크립트
# 사용법: ./daily_backup.sh
# Cron 설정 예시: 0 2 * * * /var/www/topmkt/scripts/daily_backup.sh >> /var/log/topmkt_backup.log 2>&1

set -e

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 로그 함수
log_info() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')] [INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')] [SUCCESS]${NC} $1"
}

log_error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')] [ERROR]${NC} $1"
}

# 설정 변수
PROJECT_DIR="/var/www/topmkt"
BACKUP_DIR="/var/backups/topmkt"
DATE=$(date '+%Y%m%d_%H%M%S')
RETENTION_DAYS=30

# 백업 디렉토리 생성
mkdir -p $BACKUP_DIR/{git,database,files}

log_info "=== 탑마케팅 플랫폼 일일 백업 시작 ==="

# 1. Git 백업 (코드 변경사항 커밋 및 푸시)
log_info "1. Git 백업 시작..."

cd $PROJECT_DIR

# 변경사항이 있는지 확인
if ! git diff --quiet || ! git diff --cached --quiet || [ -n "$(git status --porcelain)" ]; then
    log_info "변경사항 감지됨. 커밋 생성 중..."
    
    # 모든 변경사항 추가
    git add -A
    
    # 자동 커밋 생성
    git commit -m "일일 자동 백업: $(date '+%Y-%m-%d %H:%M:%S')

📊 변경사항:
$(git status --porcelain | head -20)

🤖 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Daily Backup Script <backup@topmktx.com>" || log_info "커밋할 변경사항이 없습니다."

    # 원격 저장소에 푸시 시도
    if git push origin master; then
        log_success "Git 원격 저장소 푸시 완료"
    else
        log_error "Git 원격 저장소 푸시 실패"
    fi
else
    log_info "변경사항이 없습니다."
fi

# 로컬 Git 백업 생성
git bundle create $BACKUP_DIR/git/topmkt_${DATE}.bundle --all
log_success "Git 로컬 백업 완료: topmkt_${DATE}.bundle"

# 2. 데이터베이스 백업
log_info "2. 데이터베이스 백업 시작..."

# .env 파일에서 데이터베이스 정보 읽기
if [[ -f "$PROJECT_DIR/.env" ]]; then
    DB_HOST=$(grep ^DB_HOST= $PROJECT_DIR/.env | cut -d '=' -f2)
    DB_NAME=$(grep ^DB_NAME= $PROJECT_DIR/.env | cut -d '=' -f2)
    DB_USERNAME=$(grep ^DB_USERNAME= $PROJECT_DIR/.env | cut -d '=' -f2)
    DB_PASSWORD=$(grep ^DB_PASSWORD= $PROJECT_DIR/.env | cut -d '=' -f2)
    
    # 데이터베이스 덤프 생성
    if mysqldump -h${DB_HOST:-localhost} -u$DB_USERNAME -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/database/topmkt_${DATE}.sql; then
        log_success "데이터베이스 백업 완료: topmkt_${DATE}.sql"
        
        # 압축
        gzip $BACKUP_DIR/database/topmkt_${DATE}.sql
        log_success "데이터베이스 백업 압축 완료"
    else
        log_error "데이터베이스 백업 실패"
    fi
else
    log_error ".env 파일을 찾을 수 없습니다."
fi

# 3. 업로드 파일 백업
log_info "3. 업로드 파일 백업 시작..."

if [[ -d "$PROJECT_DIR/public/assets/uploads" ]]; then
    tar -czf $BACKUP_DIR/files/uploads_${DATE}.tar.gz -C $PROJECT_DIR/public/assets/uploads .
    log_success "업로드 파일 백업 완료: uploads_${DATE}.tar.gz"
else
    log_info "업로드 디렉토리가 없습니다."
fi

# 4. 설정 파일 백업
log_info "4. 설정 파일 백업 시작..."

# 중요한 설정 파일들 백업
tar -czf $BACKUP_DIR/files/configs_${DATE}.tar.gz \
    -C $PROJECT_DIR \
    .env \
    src/config/ \
    public/.htaccess \
    scripts/ \
    2>/dev/null || log_info "일부 설정 파일이 없을 수 있습니다."

log_success "설정 파일 백업 완료: configs_${DATE}.tar.gz"

# 5. 시스템 정보 백업
log_info "5. 시스템 정보 백업..."

{
    echo "=== 시스템 정보 백업: $(date) ==="
    echo
    echo "--- 시스템 버전 ---"
    cat /etc/os-release
    echo
    echo "--- 설치된 패키지 (PHP, Nginx, MariaDB) ---"
    php --version 2>/dev/null || echo "PHP 버전 확인 실패"
    nginx -v 2>&1 || echo "Nginx 버전 확인 실패"
    mysql --version 2>/dev/null || echo "MySQL 버전 확인 실패"
    echo
    echo "--- 디스크 사용량 ---"
    df -h
    echo
    echo "--- 메모리 사용량 ---"
    free -h
    echo
    echo "--- 서비스 상태 ---"
    systemctl is-active nginx mariadb php8.1-fpm 2>/dev/null || systemctl is-active nginx mariadb php-fpm
    echo
} > $BACKUP_DIR/files/system_info_${DATE}.txt

log_success "시스템 정보 백업 완료"

# 6. 백업 파일 크기 및 상태 확인
log_info "6. 백업 상태 확인..."

echo
log_info "생성된 백업 파일:"
ls -lh $BACKUP_DIR/*/ | grep $DATE

# 전체 백업 크기 계산
TOTAL_SIZE=$(du -sh $BACKUP_DIR | cut -f1)
log_info "전체 백업 크기: $TOTAL_SIZE"

# 7. 오래된 백업 파일 정리
log_info "7. 오래된 백업 파일 정리 (${RETENTION_DAYS}일 이상)..."

find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete
DELETED_COUNT=$(find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -print | wc -l)

if [[ $DELETED_COUNT -gt 0 ]]; then
    log_success "$DELETED_COUNT 개의 오래된 백업 파일 삭제 완료"
else
    log_info "삭제할 오래된 백업 파일이 없습니다."
fi

# 8. 백업 무결성 검증
log_info "8. 백업 무결성 검증..."

# Git 번들 검증
if git bundle verify $BACKUP_DIR/git/topmkt_${DATE}.bundle >/dev/null 2>&1; then
    log_success "Git 백업 무결성 검증 통과"
else
    log_error "Git 백업 무결성 검증 실패"
fi

# 데이터베이스 백업 검증 (압축 파일 테스트)
if gzip -t $BACKUP_DIR/database/topmkt_${DATE}.sql.gz 2>/dev/null; then
    log_success "데이터베이스 백업 무결성 검증 통과"
else
    log_error "데이터베이스 백업 무결성 검증 실패"
fi

# 9. 백업 완료 알림
log_info "9. 백업 완료 처리..."

# 백업 성공 여부 확인
SUCCESS=true
[[ -f "$BACKUP_DIR/git/topmkt_${DATE}.bundle" ]] || SUCCESS=false
[[ -f "$BACKUP_DIR/database/topmkt_${DATE}.sql.gz" ]] || SUCCESS=false

if $SUCCESS; then
    log_success "=== 일일 백업 완료! ==="
    
    # 백업 요약 생성
    {
        echo "탑마케팅 플랫폼 백업 요약"
        echo "========================="
        echo "백업 일시: $(date '+%Y-%m-%d %H:%M:%S')"
        echo "백업 위치: $BACKUP_DIR"
        echo "백업 크기: $TOTAL_SIZE"
        echo
        echo "백업 파일:"
        ls -lh $BACKUP_DIR/*/ | grep $DATE
        echo
        echo "시스템 상태:"
        systemctl is-active nginx mariadb php8.1-fpm 2>/dev/null || systemctl is-active nginx mariadb php-fpm
        echo
        echo "디스크 사용량:"
        df -h /var/www /var/backups
    } > $BACKUP_DIR/backup_summary_${DATE}.txt
    
    log_success "백업 요약 파일 생성: backup_summary_${DATE}.txt"
else
    log_error "=== 백업 중 오류 발생! ==="
    exit 1
fi

# 권한 설정
chmod -R 600 $BACKUP_DIR
chown -R root:root $BACKUP_DIR

log_success "백업 완료! 다음 백업: $(date -d 'tomorrow 2:00' '+%Y-%m-%d %H:%M:%S')"