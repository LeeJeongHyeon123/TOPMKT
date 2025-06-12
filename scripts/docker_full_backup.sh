#!/bin/bash

# 도커 환경 완전 백업 스크립트
# 데이터베이스, 볼륨, 업로드 파일, 설정 파일을 모두 백업합니다.
# 사용법: ./docker_full_backup.sh [백업_디렉토리]

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

log_warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')] [WARNING]${NC} $1"
}

# 설정
BACKUP_BASE_DIR=${1:-"/tmp/topmkt_backup"}
DATE=$(date '+%Y%m%d_%H%M%S')
BACKUP_DIR="$BACKUP_BASE_DIR/$DATE"
PROJECT_DIR=$(pwd)

mkdir -p "$BACKUP_DIR"/{database,volumes,files,config,git}

log_info "=== 탑마케팅 도커 환경 완전 백업 시작 ==="
log_info "백업 디렉토리: $BACKUP_DIR"
log_info "프로젝트 디렉토리: $PROJECT_DIR"

# 1. Git 백업
log_info "1. Git 저장소 백업..."

cd "$PROJECT_DIR"

# 변경사항이 있으면 자동 커밋
if ! git diff --quiet || ! git diff --cached --quiet || [ -n "$(git status --porcelain)" ]; then
    log_info "변경사항 감지됨. 자동 커밋 생성 중..."
    git add -A
    git commit -m "자동 백업 전 커밋: $(date '+%Y-%m-%d %H:%M:%S')" || log_warning "커밋할 변경사항이 없거나 커밋 실패"
fi

# Git 번들 생성
git bundle create "$BACKUP_DIR/git/topmkt_${DATE}.bundle" --all
log_success "Git 백업 완료"

# 2. 도커 컨테이너 상태 확인
log_info "2. 도커 환경 확인..."

# 도커가 실행 중인지 확인
if ! command -v docker &> /dev/null; then
    log_error "Docker가 설치되지 않았습니다."
    exit 1
fi

if ! docker info &> /dev/null; then
    log_error "Docker 데몬이 실행되지 않았습니다."
    exit 1
fi

# 실행 중인 컨테이너 확인
RUNNING_CONTAINERS=$(docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}")
if [[ -z "$RUNNING_CONTAINERS" ]]; then
    log_warning "실행 중인 도커 컨테이너가 없습니다."
else
    log_success "실행 중인 컨테이너 발견"
    echo "$RUNNING_CONTAINERS" > "$BACKUP_DIR/config/running_containers.txt"
fi

# 3. 데이터베이스 백업
log_info "3. 데이터베이스 백업..."

# 데이터베이스 컨테이너 찾기
DB_CONTAINERS=$(docker ps --format "{{.Names}}" | grep -E "(mysql|mariadb|db)" | head -1)

if [[ -n "$DB_CONTAINERS" ]]; then
    log_success "데이터베이스 컨테이너 발견: $DB_CONTAINERS"
    
    # 데이터베이스 백업 실행
    DB_BACKUP_FILE="$BACKUP_DIR/database/topmkt_${DATE}.sql"
    
    # 백업 시도 (여러 방법)
    BACKUP_SUCCESS=false
    
    # 방법 1: topmkt 데이터베이스 백업
    if docker exec "$DB_CONTAINERS" mysqldump -u root -p --single-transaction topmkt > "$DB_BACKUP_FILE" 2>/dev/null; then
        BACKUP_SUCCESS=true
        log_success "topmkt 데이터베이스 백업 완료"
    # 방법 2: 환경변수에서 비밀번호 찾아서 백업
    elif docker exec "$DB_CONTAINERS" sh -c 'mysqldump -u root -p$MYSQL_ROOT_PASSWORD --single-transaction topmkt' > "$DB_BACKUP_FILE" 2>/dev/null; then
        BACKUP_SUCCESS=true
        log_success "환경변수 비밀번호로 백업 완료"
    # 방법 3: 모든 데이터베이스 백업
    elif docker exec "$DB_CONTAINERS" sh -c 'mysqldump -u root -p$MYSQL_ROOT_PASSWORD --all-databases' > "$DB_BACKUP_FILE" 2>/dev/null; then
        BACKUP_SUCCESS=true
        log_success "전체 데이터베이스 백업 완료"
    fi
    
    if $BACKUP_SUCCESS; then
        # 백업 파일 압축
        gzip "$DB_BACKUP_FILE"
        
        # 백업 크기 확인
        BACKUP_SIZE=$(du -h "$DB_BACKUP_FILE.gz" | cut -f1)
        log_success "데이터베이스 백업 압축 완료 (크기: $BACKUP_SIZE)"
        
        # 백업 정보 저장
        {
            echo "=== 데이터베이스 백업 정보 ==="
            echo "백업 일시: $(date)"
            echo "컨테이너: $DB_CONTAINERS"
            echo "백업 파일: $(basename $DB_BACKUP_FILE).gz"
            echo "파일 크기: $BACKUP_SIZE"
            echo ""
            echo "=== 복구 명령어 ==="
            echo "gunzip -c $(basename $DB_BACKUP_FILE).gz | docker exec -i $DB_CONTAINERS mysql -u root -p topmkt"
        } > "$BACKUP_DIR/database/backup_info.txt"
    else
        log_error "데이터베이스 백업 실패"
        log_info "수동 백업 명령어:"
        echo "docker exec -it $DB_CONTAINERS mysql -u root -p"
    fi
else
    log_warning "데이터베이스 컨테이너를 찾을 수 없습니다."
fi

# 4. 볼륨 백업
log_info "4. 도커 볼륨 백업..."

VOLUMES=$(docker volume ls -q | grep -E "(topmkt|db|mysql|mariadb)" | head -5)

if [[ -n "$VOLUMES" ]]; then
    for volume in $VOLUMES; do
        log_info "볼륨 백업 중: $volume"
        
        # 임시 컨테이너를 사용해 볼륨 백업
        docker run --rm -v "$volume":/backup-volume -v "$BACKUP_DIR/volumes":/backup alpine tar czf "/backup/${volume}_${DATE}.tar.gz" -C /backup-volume . 2>/dev/null
        
        if [[ $? -eq 0 ]]; then
            log_success "볼륨 백업 완료: $volume"
        else
            log_error "볼륨 백업 실패: $volume"
        fi
    done
else
    log_warning "백업할 볼륨을 찾을 수 없습니다."
fi

# 5. 업로드 파일 백업
log_info "5. 업로드 파일 백업..."

# 로컬 업로드 디렉토리가 있는지 확인
if [[ -d "$PROJECT_DIR/public/assets/uploads" ]]; then
    tar czf "$BACKUP_DIR/files/uploads_local_${DATE}.tar.gz" -C "$PROJECT_DIR/public/assets/uploads" . 2>/dev/null
    log_success "로컬 업로드 파일 백업 완료"
fi

# 도커 컨테이너에서 업로드 파일 백업
WEB_CONTAINERS=$(docker ps --format "{{.Names}}" | grep -E "(web|nginx|apache|php)" | head -1)

if [[ -n "$WEB_CONTAINERS" ]]; then
    log_info "웹 컨테이너에서 업로드 파일 백업: $WEB_CONTAINERS"
    
    # 컨테이너에서 업로드 디렉토리 복사
    docker cp "$WEB_CONTAINERS:/var/www/html/public/assets/uploads" "$BACKUP_DIR/files/uploads_container_${DATE}" 2>/dev/null
    
    if [[ $? -eq 0 ]]; then
        tar czf "$BACKUP_DIR/files/uploads_container_${DATE}.tar.gz" -C "$BACKUP_DIR/files" "uploads_container_${DATE}" 2>/dev/null
        rm -rf "$BACKUP_DIR/files/uploads_container_${DATE}"
        log_success "컨테이너 업로드 파일 백업 완료"
    else
        log_warning "컨테이너에서 업로드 파일을 찾을 수 없습니다."
    fi
fi

# 6. 설정 파일 백업
log_info "6. 설정 파일 백업..."

# 로컬 설정 파일들
IMPORTANT_FILES=(
    ".env"
    "docker-compose.yml"
    "docker-compose.backup.yml"
    "Dockerfile"
    ".htaccess"
    "nginx.conf"
    "php.ini"
)

for file in "${IMPORTANT_FILES[@]}"; do
    if [[ -f "$PROJECT_DIR/$file" ]]; then
        cp "$PROJECT_DIR/$file" "$BACKUP_DIR/config/"
        log_success "설정 파일 백업: $file"
    fi
done

# 전체 설정 아카이브 생성
tar czf "$BACKUP_DIR/config/all_configs_${DATE}.tar.gz" -C "$PROJECT_DIR" \
    --exclude="node_modules" \
    --exclude=".git" \
    --exclude="vendor" \
    --exclude="public/assets/uploads" \
    . 2>/dev/null

log_success "전체 설정 파일 아카이브 생성 완료"

# 7. 도커 환경 정보 백업
log_info "7. 도커 환경 정보 백업..."

{
    echo "=== 도커 환경 정보 백업 ==="
    echo "백업 일시: $(date)"
    echo ""
    echo "=== 실행 중인 컨테이너 ==="
    docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}"
    echo ""
    echo "=== 모든 컨테이너 ==="
    docker ps -a --format "table {{.Names}}\t{{.Image}}\t{{.Status}}"
    echo ""
    echo "=== 도커 이미지 ==="
    docker images --format "table {{.Repository}}\t{{.Tag}}\t{{.Size}}"
    echo ""
    echo "=== 도커 볼륨 ==="
    docker volume ls
    echo ""
    echo "=== 도커 네트워크 ==="
    docker network ls
    echo ""
    echo "=== 시스템 정보 ==="
    docker info
} > "$BACKUP_DIR/config/docker_environment.txt"

log_success "도커 환경 정보 백업 완료"

# 8. 백업 완료 요약
log_info "8. 백업 요약 생성..."

{
    echo "=== 탑마케팅 도커 환경 완전 백업 요약 ==="
    echo "백업 일시: $(date)"
    echo "백업 디렉토리: $BACKUP_DIR"
    echo ""
    echo "=== 백업 파일 목록 ==="
    find "$BACKUP_DIR" -type f -exec ls -lh {} \;
    echo ""
    echo "=== 전체 백업 크기 ==="
    du -sh "$BACKUP_DIR"
    echo ""
    echo "=== 복구 절차 ==="
    echo "1. Git 복원: git clone https://github.com/LeeJeongHyeon123/topmkt.git"
    echo "2. 설정 복원: 이 백업의 config/ 디렉토리 참조"
    echo "3. DB 복원: database/ 디렉토리의 백업 파일 사용"
    echo "4. 파일 복원: files/ 디렉토리의 업로드 파일 복원"
    echo "5. 볼륨 복원: volumes/ 디렉토리의 볼륨 백업 복원"
} > "$BACKUP_DIR/backup_summary.txt"

# 전체 백업 크기 계산
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)

log_success "=== 도커 환경 완전 백업 완료! ==="
echo
log_info "백업 위치: $BACKUP_DIR"
log_info "전체 크기: $TOTAL_SIZE"
echo
log_info "백업 내용:"
log_info "  - Git 저장소 번들"
log_info "  - 데이터베이스 덤프"
log_info "  - 도커 볼륨"
log_info "  - 업로드 파일"
log_info "  - 설정 파일"
log_info "  - 도커 환경 정보"
echo
log_info "복구 가이드: $BACKUP_DIR/backup_summary.txt"
log_success "백업 완료!"