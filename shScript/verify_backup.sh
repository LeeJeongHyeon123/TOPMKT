#!/bin/bash

# 백업 디렉토리가 지정되지 않으면 종료
if [ -z "$1" ]; then
    echo "사용법: $0 <백업_디렉토리_경로>"
    exit 1
fi

BACKUP_DIR="$1"

# 현재 시간 출력 함수
print_time() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1"
}

# 시작 시간 기록
print_time "[검증] 검증 작업을 시작합니다..."

# 백업 디렉토리 존재 여부 확인
print_time "[검증] 백업 디렉토리 확인 중..."
if [ ! -d "$BACKUP_DIR" ]; then
    print_time "오류: 백업 디렉토리($BACKUP_DIR)가 존재하지 않습니다."
    exit 1
fi
print_time "[검증] 백업 디렉토리 확인 완료"

# 백업 정보 파일 확인
print_time "[검증] 백업 정보 파일 확인 중..."
if [ ! -f "${BACKUP_DIR}/backup_info.txt" ]; then
    print_time "오류: 백업 정보 파일이 존재하지 않습니다."
    exit 1
fi
print_time "[검증] 백업 정보 파일 확인 완료"

# 백업 정보 출력
print_time "[검증] 백업 정보 확인 중..."
echo "----------------------------------------"
cat "${BACKUP_DIR}/backup_info.txt"
echo "----------------------------------------"
print_time "[검증] 백업 정보 확인 완료"

# 파일 시스템 검증 (파일 수 계산 및 진행상황 표시) - 중요 파일만 검사
print_time "[검증] 파일 시스템 무결성 검사 준비 중..."
print_time "[검증] 중요 파일의 MD5 체크섬을 계산합니다 (시간이 오래 걸릴 수 있습니다)..."

# 특정 확장자 파일만 선택적으로 검사
echo "[검증] PHP, JS, CSS 파일만 체크섬 계산..."
find "$BACKUP_DIR" -type f \( -name "*.php" -o -name "*.js" -o -name "*.css" \) -print0 | 
while IFS= read -r -d '' file; do
    md5sum "$file" >> "${BACKUP_DIR}/md5sum_important.txt"
done

if [ $? -ne 0 ]; then
    print_time "오류: 중요 파일 MD5 체크섬 생성 실패"
    exit 1
fi
print_time "[검증] 중요 파일 무결성 검사 완료"

# 디렉토리 구조 검증
print_time "[검증] 디렉토리 구조 검증 중..."
missing_dirs=""
for dir in "scripts" "public" "includes" "config" "resources" "api"; do
    if [ ! -d "${BACKUP_DIR}/${dir}" ]; then
        missing_dirs="${missing_dirs} ${dir}"
    else
        print_time "[검증] ${dir} 디렉토리 확인 완료"
    fi
done

if [ ! -z "$missing_dirs" ]; then
    print_time "오류: 다음 필수 디렉토리가 누락되었습니다: ${missing_dirs}"
    exit 1
fi
print_time "[검증] 디렉토리 구조 검증 완료"

# 권한 검증
print_time "[검증] 파일 권한 검증 중..."
non_executable_scripts=$(find "$BACKUP_DIR" -type f -name "*.sh" ! -executable | wc -l)
if [ "$non_executable_scripts" -gt 0 ]; then
    print_time "오류: ${non_executable_scripts}개의 스크립트 파일에 실행 권한이 없습니다."
    find "$BACKUP_DIR" -type f -name "*.sh" ! -executable -exec echo "  - {}" \;
    exit 1
fi
print_time "[검증] 파일 권한 검증 완료"

# 종료 시간 기록
print_time "[검증] 모든 검증이 성공적으로 완료되었습니다."
print_time "[검증] 백업 디렉토리: $BACKUP_DIR" 