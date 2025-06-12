#!/bin/bash

# 도커 환경용 데이터베이스 백업 스크립트
# 사용법: ./docker_db_backup.sh [backup_directory]

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

# 백업 디렉토리 설정
BACKUP_DIR=${1:-"/tmp/db_backup"}
DATE=$(date '+%Y%m%d_%H%M%S')
mkdir -p "$BACKUP_DIR"

log_info "=== 도커 환경 데이터베이스 백업 시작 ==="
log_info "백업 디렉토리: $BACKUP_DIR"

# 1. 도커 컨테이너 확인
log_info "1. 도커 컨테이너 확인..."

# 일반적인 데이터베이스 컨테이너 이름들 확인
POSSIBLE_CONTAINERS=(
    "mysql"
    "mariadb" 
    "db"
    "database"
    "topmkt_db"
    "topmkt-db"
    "topmkt_mysql"
    "topmkt-mysql"
    "topmkt_mariadb"
    "topmkt-mariadb"
)

DB_CONTAINER=""
DB_TYPE=""

# 실행 중인 컨테이너에서 데이터베이스 찾기
for container in "${POSSIBLE_CONTAINERS[@]}"; do
    if docker ps --format "table {{.Names}}" 2>/dev/null | grep -q "^${container}$"; then
        DB_CONTAINER="$container"
        log_success "데이터베이스 컨테이너 발견: $container"
        break
    fi
done

# 컨테이너 이름으로 찾지 못한 경우, 이미지로 검색
if [[ -z "$DB_CONTAINER" ]]; then
    log_info "컨테이너 이름으로 찾지 못했습니다. 이미지로 검색 중..."
    
    # MySQL/MariaDB 이미지를 사용하는 컨테이너 찾기
    MYSQL_CONTAINERS=$(docker ps --format "table {{.Names}}\t{{.Image}}" 2>/dev/null | grep -E "(mysql|mariadb)" | awk '{print $1}' | head -1)
    
    if [[ -n "$MYSQL_CONTAINERS" ]]; then
        DB_CONTAINER="$MYSQL_CONTAINERS"
        log_success "MySQL/MariaDB 컨테이너 발견: $DB_CONTAINER"
    fi
fi

if [[ -z "$DB_CONTAINER" ]]; then
    log_error "데이터베이스 컨테이너를 찾을 수 없습니다."
    log_info "실행 중인 모든 컨테이너:"
    docker ps --format "table {{.Names}}\t{{.Image}}\t{{.Status}}" 2>/dev/null || echo "도커가 실행되지 않았거나 권한이 없습니다."
    exit 1
fi

# 2. 데이터베이스 타입 확인
log_info "2. 데이터베이스 타입 확인..."

DB_IMAGE=$(docker inspect "$DB_CONTAINER" --format='{{.Config.Image}}' 2>/dev/null)
if [[ "$DB_IMAGE" == *"mysql"* ]]; then
    DB_TYPE="mysql"
    DB_DUMP_CMD="mysqldump"
    DB_CLIENT_CMD="mysql"
elif [[ "$DB_IMAGE" == *"mariadb"* ]]; then
    DB_TYPE="mariadb"
    DB_DUMP_CMD="mysqldump"
    DB_CLIENT_CMD="mysql"
else
    log_warning "알 수 없는 데이터베이스 타입: $DB_IMAGE. MySQL로 시도합니다."
    DB_TYPE="mysql"
    DB_DUMP_CMD="mysqldump"
    DB_CLIENT_CMD="mysql"
fi

log_success "데이터베이스 타입: $DB_TYPE"
log_info "컨테이너 이미지: $DB_IMAGE"

# 3. 데이터베이스 연결 정보 확인
log_info "3. 데이터베이스 연결 정보 확인..."

# 환경 변수에서 DB 정보 가져오기
DB_NAME=${DB_NAME:-"topmkt"}
DB_USER=${DB_USER:-"root"}
DB_PASSWORD=""

# 컨테이너의 환경 변수에서 정보 시도
CONTAINER_ENV=$(docker inspect "$DB_CONTAINER" --format='{{range .Config.Env}}{{println .}}{{end}}' 2>/dev/null)

if [[ -n "$CONTAINER_ENV" ]]; then
    # MySQL root 비밀번호 찾기
    ROOT_PASSWORD=$(echo "$CONTAINER_ENV" | grep -E "MYSQL_ROOT_PASSWORD|MARIADB_ROOT_PASSWORD" | cut -d'=' -f2 | head -1)
    if [[ -n "$ROOT_PASSWORD" ]]; then
        DB_PASSWORD="$ROOT_PASSWORD"
        log_success "루트 비밀번호를 컨테이너 환경변수에서 찾았습니다."
    fi
    
    # 데이터베이스 이름 찾기
    CONTAINER_DB=$(echo "$CONTAINER_ENV" | grep -E "MYSQL_DATABASE|MARIADB_DATABASE" | cut -d'=' -f2 | head -1)
    if [[ -n "$CONTAINER_DB" ]]; then
        DB_NAME="$CONTAINER_DB"
        log_success "데이터베이스 이름: $DB_NAME"
    fi
fi

# 4. 데이터베이스 목록 확인
log_info "4. 데이터베이스 목록 확인..."

if [[ -n "$DB_PASSWORD" ]]; then
    DATABASES=$(docker exec "$DB_CONTAINER" $DB_CLIENT_CMD -u"$DB_USER" -p"$DB_PASSWORD" -e "SHOW DATABASES;" 2>/dev/null | grep -v -E "^(Database|information_schema|performance_schema|mysql|sys)$" || true)
else
    # 비밀번호 없이 시도
    DATABASES=$(docker exec "$DB_CONTAINER" $DB_CLIENT_CMD -u"$DB_USER" -e "SHOW DATABASES;" 2>/dev/null | grep -v -E "^(Database|information_schema|performance_schema|mysql|sys)$" || true)
fi

if [[ -n "$DATABASES" ]]; then
    log_success "발견된 데이터베이스:"
    echo "$DATABASES" | while read -r db; do
        [[ -n "$db" ]] && log_info "  - $db"
    done
    
    # topmkt 데이터베이스가 있는지 확인
    if echo "$DATABASES" | grep -q "topmkt"; then
        DB_NAME="topmkt"
        log_success "topmkt 데이터베이스 발견!"
    else
        # 첫 번째 데이터베이스 사용
        DB_NAME=$(echo "$DATABASES" | head -1 | tr -d '\r\n')
        log_warning "topmkt 데이터베이스를 찾지 못했습니다. '$DB_NAME' 사용."
    fi
fi

# 5. 데이터베이스 백업 실행
log_info "5. 데이터베이스 백업 실행..."

BACKUP_FILE="$BACKUP_DIR/topmkt_docker_${DATE}.sql"

# 백업 명령어 구성
if [[ -n "$DB_PASSWORD" ]]; then
    DUMP_CMD="$DB_DUMP_CMD -u$DB_USER -p$DB_PASSWORD --single-transaction --routines --triggers $DB_NAME"
else
    DUMP_CMD="$DB_DUMP_CMD -u$DB_USER --single-transaction --routines --triggers $DB_NAME"
fi

log_info "백업 실행 중... (컨테이너: $DB_CONTAINER, DB: $DB_NAME)"

if docker exec "$DB_CONTAINER" $DUMP_CMD > "$BACKUP_FILE" 2>/dev/null; then
    log_success "데이터베이스 백업 완료: $(basename $BACKUP_FILE)"
    
    # 파일 크기 확인
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    log_info "백업 파일 크기: $BACKUP_SIZE"
    
    # 백업 파일 내용 간단 검증
    if grep -q "CREATE TABLE" "$BACKUP_FILE"; then
        log_success "백업 파일 검증 통과 (테이블 정의 포함)"
    else
        log_warning "백업 파일에 테이블 정의가 없습니다. 확인 필요."
    fi
    
    # 압축
    gzip "$BACKUP_FILE"
    log_success "백업 파일 압축 완료: $(basename $BACKUP_FILE).gz"
    
else
    log_error "데이터베이스 백업 실패"
    
    # 수동 백업 가이드 제공
    log_info "수동 백업 명령어:"
    echo "docker exec -it $DB_CONTAINER $DB_CLIENT_CMD -u$DB_USER -p"
    echo "SHOW DATABASES;"
    echo "USE $DB_NAME;"
    echo "SHOW TABLES;"
    echo ""
    echo "수동 덤프:"
    echo "docker exec $DB_CONTAINER $DB_DUMP_CMD -u$DB_USER -p$DB_PASSWORD $DB_NAME > backup.sql"
    
    exit 1
fi

# 6. 추가 백업 정보 생성
log_info "6. 백업 정보 파일 생성..."

BACKUP_INFO="$BACKUP_DIR/backup_info_${DATE}.txt"
{
    echo "=== 도커 데이터베이스 백업 정보 ==="
    echo "백업 일시: $(date '+%Y-%m-%d %H:%M:%S')"
    echo "컨테이너: $DB_CONTAINER"
    echo "이미지: $DB_IMAGE"
    echo "데이터베이스: $DB_NAME"
    echo "사용자: $DB_USER"
    echo "백업 파일: $(basename $BACKUP_FILE).gz"
    echo "파일 크기: $BACKUP_SIZE"
    echo ""
    echo "=== 복구 명령어 ==="
    echo "# 컨테이너에 복구:"
    echo "gunzip -c $(basename $BACKUP_FILE).gz | docker exec -i $DB_CONTAINER $DB_CLIENT_CMD -u$DB_USER -p$DB_PASSWORD $DB_NAME"
    echo ""
    echo "# 새 환경에 복구:"
    echo "gunzip -c $(basename $BACKUP_FILE).gz | mysql -h localhost -u root -p $DB_NAME"
    echo ""
    echo "=== 컨테이너 정보 ==="
    docker inspect "$DB_CONTAINER" --format='{{json .Config.Env}}' 2>/dev/null | jq -r '.[]' 2>/dev/null | grep -E "(MYSQL|MARIADB|DB)" || echo "환경 변수 정보 없음"
} > "$BACKUP_INFO"

log_success "백업 정보 파일 생성: $(basename $BACKUP_INFO)"

# 7. 백업 완료 요약
log_success "=== 도커 데이터베이스 백업 완료! ==="
echo
log_info "백업 파일:"
ls -lh "$BACKUP_DIR"/*_${DATE}*
echo
log_info "복구 방법:"
echo "gunzip -c $BACKUP_DIR/$(basename $BACKUP_FILE).gz | docker exec -i $DB_CONTAINER $DB_CLIENT_CMD -u$DB_USER -p $DB_NAME"
echo
log_info "백업 정보: $BACKUP_INFO"