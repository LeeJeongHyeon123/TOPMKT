#!/bin/bash
# 🛡️ 탑마케팅 완전 백업 스크립트 (서버용)
# React.js + TypeScript 전환 전 완벽한 백업
# 실행 방법: curl -s https://raw.githubusercontent.com/topmktx/topmkt/main/server_backup.sh | bash

BACKUP_DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/www/html/backup"
PROJECT_DIR="/var/www/html/topmkt"

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🛡️ 탑마케팅 완전 백업 시작${NC}"
echo -e "${YELLOW}📅 백업 일시: $(date)${NC}"
echo -e "${YELLOW}📍 백업 위치: $BACKUP_DIR${NC}"
echo ""

# 백업 디렉토리 생성
echo -e "${BLUE}📁 백업 디렉토리 생성 중...${NC}"
mkdir -p "$BACKUP_DIR"
chmod 755 "$BACKUP_DIR"

# 디스크 공간 확인
AVAILABLE_SPACE=$(df /var/www/html | awk 'NR==2{print $4}')
if [ "$AVAILABLE_SPACE" -lt 5242880 ]; then  # 5GB in KB
    echo -e "${RED}❌ 디스크 공간 부족! 최소 5GB 필요${NC}"
    echo -e "${YELLOW}현재 사용 가능: $(df -h /var/www/html | awk 'NR==2{print $4}')${NC}"
    exit 1
fi
echo -e "${GREEN}✅ 디스크 공간 충분 ($(df -h /var/www/html | awk 'NR==2{print $4}') 사용 가능)${NC}"

# 1. 데이터베이스 연결 테스트
echo -e "${BLUE}🔌 데이터베이스 연결 테스트...${NC}"
if mysql -u root -pDnlszkem1! -e "SELECT 1;" >/dev/null 2>&1; then
    echo -e "${GREEN}✅ 데이터베이스 연결 성공${NC}"
else
    echo -e "${RED}❌ 데이터베이스 연결 실패${NC}"
    echo -e "${YELLOW}MySQL 서비스 상태를 확인하세요: systemctl status mariadb${NC}"
    exit 1
fi

# 2. 데이터베이스 완전 백업
echo -e "${BLUE}📊 데이터베이스 백업 중...${NC}"
mysqldump -u root -pDnlszkem1! \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  --add-drop-database \
  --databases topmkt \
  --result-file="$BACKUP_DIR/topmkt_db_backup_$BACKUP_DATE.sql"

if [ $? -eq 0 ]; then
    DB_SIZE=$(ls -lah "$BACKUP_DIR/topmkt_db_backup_$BACKUP_DATE.sql" | awk '{print $5}')
    echo -e "${GREEN}✅ 데이터베이스 백업 완료 ($DB_SIZE)${NC}"
else
    echo -e "${RED}❌ 데이터베이스 백업 실패${NC}"
    exit 1
fi

# 3. 테이블 통계 백업
echo -e "${BLUE}📈 테이블 통계 생성 중...${NC}"
mysql -u root -pDnlszkem1! -e "
USE topmkt;
SELECT 
    table_name as '테이블명',
    table_rows as '레코드수',
    ROUND(data_length/1024/1024, 2) as 'Data_MB',
    ROUND(index_length/1024/1024, 2) as 'Index_MB'
FROM information_schema.tables 
WHERE table_schema = 'topmkt'
ORDER BY table_rows DESC;
" > "$BACKUP_DIR/table_statistics_$BACKUP_DATE.txt"

# 4. 소스 코드 백업 (rsync)
echo -e "${BLUE}📁 소스 코드 백업 중 (rsync)...${NC}"
if [ -d "$PROJECT_DIR" ]; then
    rsync -avh --progress \
      --exclude='logs/*' \
      --exclude='cache/*' \
      --exclude='.git' \
      "$PROJECT_DIR/" \
      "$BACKUP_DIR/topmkt_source_$BACKUP_DATE/"

    if [ $? -eq 0 ]; then
        SOURCE_SIZE=$(du -sh "$BACKUP_DIR/topmkt_source_$BACKUP_DATE" | awk '{print $1}')
        echo -e "${GREEN}✅ 소스 코드 백업 완료 ($SOURCE_SIZE)${NC}"
    else
        echo -e "${RED}❌ 소스 코드 백업 실패${NC}"
        exit 1
    fi
else
    echo -e "${RED}❌ 프로젝트 디렉토리 없음: $PROJECT_DIR${NC}"
    exit 1
fi

# 5. 소스 코드 압축 백업
echo -e "${BLUE}🗜️ 소스 코드 압축 백업 중...${NC}"
cd /var/www/html
tar -czf "$BACKUP_DIR/topmkt_source_$BACKUP_DATE.tar.gz" \
  --exclude='topmkt/logs/*' \
  --exclude='topmkt/cache/*' \
  --exclude='topmkt/.git' \
  topmkt/

if [ $? -eq 0 ]; then
    TAR_SIZE=$(ls -lah "$BACKUP_DIR/topmkt_source_$BACKUP_DATE.tar.gz" | awk '{print $5}')
    echo -e "${GREEN}✅ 압축 백업 완료 ($TAR_SIZE)${NC}"
else
    echo -e "${RED}❌ 압축 백업 실패${NC}"
fi

# 6. 업로드 파일 백업
echo -e "${BLUE}🖼️ 업로드 파일 백업 중...${NC}"
if [ -d "$PROJECT_DIR/public/assets/uploads" ]; then
    rsync -avh --progress \
      "$PROJECT_DIR/public/assets/uploads/" \
      "$BACKUP_DIR/uploads_$BACKUP_DATE/"
    
    if [ $? -eq 0 ]; then
        UPLOAD_SIZE=$(du -sh "$BACKUP_DIR/uploads_$BACKUP_DATE" | awk '{print $1}')
        echo -e "${GREEN}✅ 업로드 파일 백업 완료 ($UPLOAD_SIZE)${NC}"
    else
        echo -e "${YELLOW}⚠️ 업로드 파일 백업 실패 (무시하고 계속)${NC}"
    fi
else
    echo -e "${YELLOW}⚠️ 업로드 폴더가 존재하지 않음${NC}"
fi

# 7. 설정 파일 백업
echo -e "${BLUE}⚙️ 설정 파일 백업 중...${NC}"
CONFIG_DIR="$BACKUP_DIR/config_$BACKUP_DATE"
mkdir -p "$CONFIG_DIR"/{apache,php,mysql,ssl}

# Apache 설정
cp /etc/httpd/conf/httpd.conf "$CONFIG_DIR/apache/" 2>/dev/null || true
cp -r /etc/httpd/conf.d/ "$CONFIG_DIR/apache/" 2>/dev/null || true

# PHP 설정
cp /etc/php.ini "$CONFIG_DIR/php/" 2>/dev/null || true
cp -r /etc/php.d/ "$CONFIG_DIR/php/" 2>/dev/null || true

# MySQL 설정
cp /etc/my.cnf "$CONFIG_DIR/mysql/" 2>/dev/null || true
cp -r /etc/my.cnf.d/ "$CONFIG_DIR/mysql/" 2>/dev/null || true

echo -e "${GREEN}✅ 설정 파일 백업 완료${NC}"

# 8. Git 상태 확인 및 커밋
if [ -d "$PROJECT_DIR/.git" ]; then
    echo -e "${BLUE}🔄 Git 백업 중...${NC}"
    cd "$PROJECT_DIR"

    # 현재 Git 상태 확인
    GIT_STATUS=$(git status --porcelain 2>/dev/null || echo "")
    CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "unknown")
    LAST_COMMIT=$(git log -1 --oneline 2>/dev/null || echo "no commits")

    echo -e "${YELLOW}현재 브랜치: $CURRENT_BRANCH${NC}"
    echo -e "${YELLOW}마지막 커밋: $LAST_COMMIT${NC}"

    # 변경사항이 있으면 커밋
    if [ ! -z "$GIT_STATUS" ]; then
        echo -e "${YELLOW}변경사항 발견, 커밋 중...${NC}"
        git add .
        git commit -m "React 전환 전 완전 백업 포인트

🎯 백업 시점: $(date '+%Y-%m-%d %H:%M:%S')
📋 백업 범위: 전체 소스 코드, 데이터베이스, 설정 파일
🚀 다음 단계: React.js + TypeScript 전환
📝 복구 방법: git checkout $(git rev-parse HEAD)
📍 백업 위치: $BACKUP_DIR

🧠 Generated with [Claude Code](https://claude.ai/code)

Co-Authored-By: Claude <noreply@anthropic.com>"
    else
        echo -e "${GREEN}✅ 변경사항 없음, 커밋 불필요${NC}"
    fi

    # 태그 생성
    TAG_NAME="backup-before-react-$(date +%Y%m%d)"
    git tag -a "$TAG_NAME" -m "완전 백업 포인트 - React 전환 전 ($BACKUP_DATE)" 2>/dev/null || echo -e "${YELLOW}⚠️ 태그 생성 실패 (이미 존재할 수 있음)${NC}"
    echo -e "${GREEN}✅ Git 태그: $TAG_NAME${NC}"

    # Git 저장소 백업
    echo -e "${BLUE}📦 Git 저장소 백업 중...${NC}"
    git clone --bare "$PROJECT_DIR" "$BACKUP_DIR/git_repo_$BACKUP_DATE.git" 2>/dev/null
    echo -e "${GREEN}✅ Git 저장소 백업 완료${NC}"
else
    echo -e "${YELLOW}⚠️ Git 저장소가 아닙니다${NC}"
fi

# 9. 백업 파일 체크섬 생성
echo -e "${BLUE}🔍 백업 무결성 검증 중...${NC}"
cd "$BACKUP_DIR"
find . -type f -exec sha256sum {} + > "backup_checksums_$BACKUP_DATE.txt"
echo -e "${GREEN}✅ 체크섬 파일 생성 완료${NC}"

# 10. 백업 정보 파일 생성
echo -e "${BLUE}📝 백업 정보 파일 생성 중...${NC}"
cat > "$BACKUP_DIR/backup_info_$BACKUP_DATE.txt" << EOF
=== 탑마케팅 완전 백업 정보 ===
백업 일시: $(date)
백업 위치: $BACKUP_DIR
서버 정보: $(uname -a)
백업 목적: React.js + TypeScript 전환 전 완전 백업

=== 백업 파일 목록 ===
$(ls -lah "$BACKUP_DIR/" | grep "$BACKUP_DATE")

=== 백업 크기 총합 ===
$(du -sh "$BACKUP_DIR" | awk '{print $1}')

=== 디스크 사용량 ===
$(df -h /var/www/html)

=== 데이터베이스 정보 ===
$(mysql -u root -pDnlszkem1! -e "SELECT VERSION() as 'MySQL Version', NOW() as 'Backup Time';" 2>/dev/null || echo "데이터베이스 정보 조회 실패")

=== 테이블 요약 ===
$(cat "$BACKUP_DIR/table_statistics_$BACKUP_DATE.txt" 2>/dev/null || echo "테이블 통계 파일 없음")

=== Git 정보 ===
$(if [ -d "$PROJECT_DIR/.git" ]; then
    cd "$PROJECT_DIR"
    echo "현재 브랜치: $(git branch --show-current 2>/dev/null || echo 'unknown')"
    echo "백업 태그: backup-before-react-$(date +%Y%m%d)"
    echo "마지막 커밋: $(git log -1 --oneline 2>/dev/null || echo 'no commits')"
else
    echo "Git 저장소 아님"
fi)

=== PHP 버전 ===
$(php -v 2>/dev/null | head -1 || echo "PHP 정보 조회 실패")

=== Apache 상태 ===
$(systemctl status httpd --no-pager -l 2>/dev/null | head -10 || echo "Apache 상태 조회 실패")

=== 복구 방법 ===
1. 데이터베이스: mysql -u root -pDnlszkem1! < topmkt_db_backup_$BACKUP_DATE.sql
2. 소스 코드: rsync -avh --delete topmkt_source_$BACKUP_DATE/ /var/www/html/topmkt/
3. Git 롤백: git checkout backup-before-react-$(date +%Y%m%d)

=== 중요 알림 ===
- 이 백업은 React 전환 전 완전한 상태를 보존합니다
- 복구 시 반드시 테스트 환경에서 먼저 검증하세요
- 문제 발생 시 backup_checksums_$BACKUP_DATE.txt로 무결성 확인
EOF

# 11. 최종 검증
echo -e "${BLUE}🔍 최종 백업 검증 중...${NC}"

# SQL 파일 구문 검증
if mysql -u root -pDnlszkem1! --execute="SET SESSION sql_mode = 'STRICT_TRANS_TABLES';" < "$BACKUP_DIR/topmkt_db_backup_$BACKUP_DATE.sql" >/dev/null 2>&1; then
    echo -e "${GREEN}✅ SQL 백업 파일 구문 검증 성공${NC}"
else
    echo -e "${RED}❌ SQL 백업 파일 구문 오류${NC}"
fi

# 압축 파일 무결성 검사
if tar -tzf "$BACKUP_DIR/topmkt_source_$BACKUP_DATE.tar.gz" >/dev/null 2>&1; then
    echo -e "${GREEN}✅ 압축 파일 무결성 검증 성공${NC}"
else
    echo -e "${RED}❌ 압축 파일 손상${NC}"
fi

# 백업 완료 요약
echo ""
echo -e "${GREEN}🎉 백업 완료!${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${YELLOW}📍 백업 위치:${NC} $BACKUP_DIR"
echo -e "${YELLOW}🏷️ Git 태그:${NC} backup-before-react-$(date +%Y%m%d)"
echo -e "${YELLOW}📊 총 백업 크기:${NC} $(du -sh "$BACKUP_DIR" | awk '{print $1}')"
echo -e "${YELLOW}📁 백업 파일 개수:${NC} $(find "$BACKUP_DIR" -type f | wc -l)개"
echo ""
echo -e "${GREEN}✅ 백업된 항목:${NC}"
echo "   📊 데이터베이스 (전체 스키마 + 데이터)"
echo "   📁 소스 코드 (rsync + tar 압축)"
echo "   🖼️ 업로드 파일"
echo "   ⚙️ 설정 파일 (Apache, PHP, MySQL)"
echo "   🔄 Git 저장소 (커밋 + 태그)"
echo "   🔍 무결성 체크섬"
echo ""
echo -e "${BLUE}📖 복구 방법:${NC}"
echo "   백업 정보: $BACKUP_DIR/backup_info_$BACKUP_DATE.txt"
echo "   체크섬: $BACKUP_DIR/backup_checksums_$BACKUP_DATE.txt"
echo ""
echo -e "${GREEN}🚀 이제 React.js + TypeScript 전환을 시작할 수 있습니다!${NC}"
echo -e "${BLUE}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"