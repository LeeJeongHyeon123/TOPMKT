#!/bin/bash

# 날짜 형식 생성
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/www/html/topmkt/backup/vendor/google/cloud-recaptcha-enterprise/src/V1"
SRC_DIR="/var/www/html/topmkt/vendor/google/cloud-recaptcha-enterprise/src/V1"

echo "[reCAPTCHA 백업] 시작: $DATE"

# 디렉토리 확인 및 생성
if [ ! -d "$BACKUP_DIR" ]; then
    echo "[reCAPTCHA 백업] 백업 디렉토리 생성 중..."
    mkdir -p "$BACKUP_DIR"
fi

# 수정해야 하는 파일 목록
FILES_TO_BACKUP=(
    "Event.php"
    "RelatedAccountGroupMembership.php"
    "SearchRelatedAccountGroupMembershipsRequest.php"
    "AnnotateAssessmentRequest.php"
)

# 파일 백업
echo "[reCAPTCHA 백업] 파일 백업 중..."
for file in "${FILES_TO_BACKUP[@]}"; do
    if [ -f "${SRC_DIR}/${file}" ]; then
        echo "[reCAPTCHA 백업] ${file} 백업 중..."
        cp "${SRC_DIR}/${file}" "${BACKUP_DIR}/${file}.bak_${DATE}"
        if [ $? -eq 0 ]; then
            echo "[reCAPTCHA 백업] ${file} 백업 완료"
        else
            echo "[reCAPTCHA 백업] 오류: ${file} 백업 실패"
            exit 1
        fi
    else
        echo "[reCAPTCHA 백업] 오류: ${file}이 존재하지 않습니다"
        exit 1
    fi
done

# 백업 정보 파일 생성
echo "[reCAPTCHA 백업] backup_info.txt 생성 중..."
cat > "${BACKUP_DIR}/backup_info.txt" << EOL
백업 시간: $(date)
백업 유형: reCAPTCHA Enterprise 라이브러리 파일
백업된 파일:
$(for file in "${FILES_TO_BACKUP[@]}"; do echo "- ${file} -> ${file}.bak_${DATE}"; done)
백업 사유: hashed_account_id deprecated 관련 수정 전 백업
EOL

echo "[reCAPTCHA 백업] 백업 완료"
echo "[reCAPTCHA 백업] 백업 위치: ${BACKUP_DIR}"

# 백업 검증
echo "[reCAPTCHA 백업] 백업 검증 시작..."

# 백업 파일 존재 확인
for file in "${FILES_TO_BACKUP[@]}"; do
    backup_file="${BACKUP_DIR}/${file}.bak_${DATE}"
    if [ ! -f "$backup_file" ]; then
        echo "[reCAPTCHA 백업] 오류: 백업 파일($backup_file)이 존재하지 않습니다."
        exit 1
    fi
    
    # 원본과 백업 파일 비교
    diff "${SRC_DIR}/${file}" "$backup_file" > /dev/null
    if [ $? -ne 0 ]; then
        echo "[reCAPTCHA 백업] 오류: 원본과 백업 파일이 다릅니다: $file"
        exit 1
    fi
done

echo "[reCAPTCHA 백업] 백업 정보 파일 확인 중..."
if [ ! -f "${BACKUP_DIR}/backup_info.txt" ]; then
    echo "[reCAPTCHA 백업] 오류: 백업 정보 파일이 존재하지 않습니다."
    exit 1
fi

echo "[reCAPTCHA 백업] 모든 검증이 성공적으로 완료되었습니다."
echo "[reCAPTCHA 백업] 종료: $(date +%Y%m%d_%H%M%S)" 