#!/bin/bash

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 사용법 표시
usage() {
    echo -e "${BLUE}사용법:${NC} $0 <백업_디렉토리_경로>"
    echo ""
    echo "예시:"
    echo "  $0 /backup/git/topmkt_20250522_171301"
    echo ""
    exit 1
}

# 백업 디렉토리가 지정되지 않으면 종료
if [ -z "$1" ]; then
    echo -e "${RED}오류: 백업 디렉토리 경로가 필요합니다.${NC}"
    usage
fi

BACKUP_DIR="$1"

# 현재 시간 출력 함수
print_time() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# 성공 메시지 출력 함수
print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

# 경고 메시지 출력 함수
print_warning() {
    echo -e "${YELLOW}⚠️ $1${NC}"
}

# 오류 메시지 출력 함수
print_error() {
    echo -e "${RED}❌ $1${NC}"
}

# 진행 표시 함수
show_progress() {
    local current=$1
    local total=$2
    local percent=$((current * 100 / total))
    local progress=$((current * 40 / total))
    local bar="["
    
    for ((i=0; i<40; i++)); do
        if [ $i -lt $progress ]; then
            bar+="="
        else
            bar+=" "
        fi
    done
    
    bar+="] ${percent}% (${current}/${total})"
    echo -ne "\r${bar}"
}

# 중요 파일 목록 (검증에 필수적인 파일들)
CRITICAL_FILES=(
    "index.php"
    "auth.php"
    "config/config.php"
    "config/firebase/config.php"
    "includes/header.php"
    "includes/footer.php"
    "public/assets/css/main.css"
    "public/assets/css/auth.css"
    "public/assets/js/auth.js"
    "public/assets/js/firebase-config.js"
    "shScript/manual_backup.sh"
    "shScript/verify_backup.sh"
    "기본정책.md"
    "기획서.md"
    "디렉토리구조.md"
)

# 필수 디렉토리 목록
CRITICAL_DIRS=(
    "public"
    "includes"
    "config"
    "resources"
    "api"
    "scripts"
    "shScript"
)

# 시작 시간 기록
START_TIME=$(date +%s)
print_time "[검증] 검증 작업을 시작합니다..."

# 백업 디렉토리 존재 여부 확인
print_time "[검증] 백업 디렉토리 확인 중..."
if [ ! -d "$BACKUP_DIR" ]; then
    print_error "오류: 백업 디렉토리($BACKUP_DIR)가 존재하지 않습니다."
    exit 1
fi
print_success "[검증] 백업 디렉토리 확인 완료"

# 백업 정보 파일 확인
print_time "[검증] 백업 정보 파일 확인 중..."
if [ ! -f "${BACKUP_DIR}/backup_info.txt" ]; then
    print_error "오류: 백업 정보 파일이 존재하지 않습니다."
    exit 1
fi
print_success "[검증] 백업 정보 파일 확인 완료"

# 백업 정보 출력
print_time "[검증] 백업 정보 확인 중..."
echo "----------------------------------------"
cat "${BACKUP_DIR}/backup_info.txt"
echo "----------------------------------------"
print_success "[검증] 백업 정보 확인 완료"

# 중요 파일 존재 확인
print_time "[검증] 중요 파일 존재 여부 확인 중..."
missing_files=""
total_files=${#CRITICAL_FILES[@]}
current=0

for file in "${CRITICAL_FILES[@]}"; do
    current=$((current + 1))
    show_progress $current $total_files
    
    if [ ! -f "${BACKUP_DIR}/${file}" ]; then
        missing_files="${missing_files} ${file}"
    fi
done
echo ""

if [ ! -z "$missing_files" ]; then
    print_error "오류: 다음 중요 파일이 누락되었습니다: ${missing_files}"
    exit 1
fi
print_success "[검증] 중요 파일 존재 확인 완료"

# 디렉토리 구조 검증
print_time "[검증] 디렉토리 구조 검증 중..."
missing_dirs=""
for dir in "${CRITICAL_DIRS[@]}"; do
    if [ ! -d "${BACKUP_DIR}/${dir}" ]; then
        missing_dirs="${missing_dirs} ${dir}"
    else
        print_success "   ${dir} 디렉토리 확인 완료"
    fi
done

if [ ! -z "$missing_dirs" ]; then
    print_error "오류: 다음 필수 디렉토리가 누락되었습니다: ${missing_dirs}"
    exit 1
fi
print_success "[검증] 디렉토리 구조 검증 완료"

# 파일 시스템 무결성 검사
print_time "[검증] 파일 시스템 무결성 검사 준비 중..."
print_time "[검증] 모든 코드 파일의 체크섬 계산 중 (시간이 오래 걸릴 수 있습니다)..."
print_time "[검증] 병렬 처리로 PHP, JS, CSS 파일 체크섬 계산 중..."

# PHP 파일 MD5 계산 (병렬 처리)
find "$BACKUP_DIR" -type f -name "*.php" -print0 | xargs -0 -P 4 -I {} md5sum {} > "${BACKUP_DIR}/md5sum_php.txt" &
php_pid=$!

# JS 파일 MD5 계산 (병렬 처리)
find "$BACKUP_DIR" -type f -name "*.js" -print0 | xargs -0 -P 4 -I {} md5sum {} > "${BACKUP_DIR}/md5sum_js.txt" &
js_pid=$!

# CSS 파일 MD5 계산 (병렬 처리)
find "$BACKUP_DIR" -type f -name "*.css" -print0 | xargs -0 -P 4 -I {} md5sum {} > "${BACKUP_DIR}/md5sum_css.txt" &
css_pid=$!

# 진행 상황 표시
while kill -0 $php_pid 2>/dev/null || kill -0 $js_pid 2>/dev/null || kill -0 $css_pid 2>/dev/null; do
    echo -ne "\r[진행 중] PHP: $([ -f ${BACKUP_DIR}/md5sum_php.txt ] && wc -l < ${BACKUP_DIR}/md5sum_php.txt || echo "0") 파일, JS: $([ -f ${BACKUP_DIR}/md5sum_js.txt ] && wc -l < ${BACKUP_DIR}/md5sum_js.txt || echo "0") 파일, CSS: $([ -f ${BACKUP_DIR}/md5sum_css.txt ] && wc -l < ${BACKUP_DIR}/md5sum_css.txt || echo "0") 파일"
    sleep 1
done
echo ""

# 모든 체크섬 파일 합치기
cat "${BACKUP_DIR}/md5sum_php.txt" "${BACKUP_DIR}/md5sum_js.txt" "${BACKUP_DIR}/md5sum_css.txt" > "${BACKUP_DIR}/md5sum_all.txt"
print_success "[검증] 파일 무결성 검사 완료"

# 권한 검증
print_time "[검증] 파일 권한 검증 중..."
non_executable_scripts=$(find "$BACKUP_DIR" -type f -name "*.sh" ! -executable | wc -l)
if [ "$non_executable_scripts" -gt 0 ]; then
    print_warning "주의: ${non_executable_scripts}개의 스크립트 파일에 실행 권한이 없습니다."
    if [ "$non_executable_scripts" -lt 10 ]; then
        find "$BACKUP_DIR" -type f -name "*.sh" ! -executable -exec echo "  - {}" \;
    fi
else
    print_success "[검증] 모든 스크립트 파일에 실행 권한이 올바르게 설정되어 있습니다."
fi

# 종료 시간 기록 및 소요 시간 계산
END_TIME=$(date +%s)
ELAPSED_TIME=$((END_TIME - START_TIME))
MINUTES=$((ELAPSED_TIME / 60))
SECONDS=$((ELAPSED_TIME % 60))

# 최종 결과 출력
echo "----------------------------------------"
print_success "[검증] 모든 검증이 성공적으로 완료되었습니다."
print_time "[검증] 백업 디렉토리: $BACKUP_DIR"
print_time "[검증] 소요 시간: ${MINUTES}분 ${SECONDS}초"
echo "----------------------------------------" 