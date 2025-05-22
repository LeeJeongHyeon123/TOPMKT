#!/bin/bash

# 백업 디렉토리가 지정되지 않으면 종료
if [ -z "$1" ]; then
    echo "사용법: $0 <백업_디렉토리_경로>"
    exit 1
fi

BACKUP_DIR="$1"

# 백업 디렉토리 존재 여부 확인
echo "[검증] 백업 디렉토리 확인 중..."
if [ ! -d "$BACKUP_DIR" ]; then
    echo "오류: 백업 디렉토리($BACKUP_DIR)가 존재하지 않습니다."
    exit 1
fi
echo "[검증] 백업 디렉토리 확인 완료"

# 백업 정보 파일 확인
echo "[검증] 백업 정보 파일 확인 중..."
if [ ! -f "${BACKUP_DIR}/backup_info.txt" ]; then
    echo "오류: 백업 정보 파일이 존재하지 않습니다."
    exit 1
fi
echo "[검증] 백업 정보 파일 확인 완료"

# 백업 정보 출력
echo "[검증] 백업 정보 확인 중..."
echo "----------------------------------------"
cat "${BACKUP_DIR}/backup_info.txt"
echo "----------------------------------------"
echo "[검증] 백업 정보 확인 완료"

# 파일 시스템 검증
echo "[검증] 파일 시스템 무결성 검사 중..."
find "$BACKUP_DIR" -type f -exec md5sum {} \; > "${BACKUP_DIR}/md5sum.txt"
if [ $? -ne 0 ]; then
    echo "오류: MD5 체크섬 생성 실패"
    exit 1
fi
echo "[검증] 파일 시스템 무결성 검사 완료"

# 디렉토리 구조 검증
echo "[검증] 디렉토리 구조 검증 중..."
if [ ! -d "${BACKUP_DIR}/scripts" ] || [ ! -d "${BACKUP_DIR}/public" ]; then
    echo "오류: 필수 디렉토리가 누락되었습니다."
    exit 1
fi
echo "[검증] 디렉토리 구조 검증 완료"

# 권한 검증
echo "[검증] 파일 권한 검증 중..."
find "$BACKUP_DIR" -type f -name "*.sh" -exec test -x {} \;
if [ $? -ne 0 ]; then
    echo "오류: 실행 권한이 없는 스크립트 파일이 있습니다."
    exit 1
fi
echo "[검증] 파일 권한 검증 완료"

echo "[검증] 모든 검증이 성공적으로 완료되었습니다."
echo "[검증] 백업 디렉토리: $BACKUP_DIR" 