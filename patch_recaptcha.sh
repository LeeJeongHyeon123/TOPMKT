#!/bin/bash

# 날짜 형식 생성
DATE=$(date +%Y%m%d_%H%M%S)
SRC_DIR="/var/www/html/topmkt/vendor/google/cloud-recaptcha-enterprise/src/V1"

echo "[reCAPTCHA 패치] 시작: $DATE"

# 수정할 파일 목록
FILES_TO_PATCH=(
    "Event.php"
    "RelatedAccountGroupMembership.php"
    "SearchRelatedAccountGroupMembershipsRequest.php"
    "AnnotateAssessmentRequest.php"
)

# 백업 스크립트 실행
echo "[reCAPTCHA 패치] 백업 스크립트 실행 중..."
bash /var/www/html/topmkt/backup_recaptcha.sh
if [ $? -ne 0 ]; then
    echo "[reCAPTCHA 패치] 오류: 백업 실패. 패치를 중단합니다."
    exit 1
fi
echo "[reCAPTCHA 패치] 백업 완료"

# 파일 패치
echo "[reCAPTCHA 패치] 파일 패치 중..."
for file in "${FILES_TO_PATCH[@]}"; do
    file_path="${SRC_DIR}/${file}"
    if [ -f "$file_path" ]; then
        echo "[reCAPTCHA 패치] ${file} 패치 중..."
        
        # 임시 파일 생성
        tmp_file=$(mktemp)
        
        # trigger_error 호출을 주석 처리
        sed 's/\/\/ @trigger_error/\/\/\/ @trigger_error/' "$file_path" > "$tmp_file"
        
        # 파일 교체
        mv "$tmp_file" "$file_path"
        
        if [ $? -eq 0 ]; then
            echo "[reCAPTCHA 패치] ${file} 패치 완료"
        else
            echo "[reCAPTCHA 패치] 오류: ${file} 패치 실패"
            exit 1
        fi
    else
        echo "[reCAPTCHA 패치] 오류: ${file}이 존재하지 않습니다"
        exit 1
    fi
done

echo "[reCAPTCHA 패치] 패치 검증 중..."
for file in "${FILES_TO_PATCH[@]}"; do
    file_path="${SRC_DIR}/${file}"
    if grep -q "// @trigger_error" "$file_path"; then
        echo "[reCAPTCHA 패치] 오류: ${file}에 여전히 주석 처리되지 않은 trigger_error가 있습니다"
        exit 1
    fi
done

echo "[reCAPTCHA 패치] 모든 파일 패치 완료"
echo "[reCAPTCHA 패치] 종료: $(date +%Y%m%d_%H%M%S)"

# 패치 로그 생성
LOG_DIR="/var/www/html/topmkt/logs"
mkdir -p "$LOG_DIR"
LOG_FILE="${LOG_DIR}/recaptcha_patch_${DATE}.log"

cat > "$LOG_FILE" << EOL
패치 시간: $(date)
패치 유형: reCAPTCHA Enterprise 라이브러리 파일 수정
패치된 파일:
$(for file in "${FILES_TO_PATCH[@]}"; do echo "- ${file}"; done)
패치 내용: hashed_account_id deprecated 관련 trigger_error 호출 주석 처리
패치 사유: PHP 7.4 환경에서 경고 메시지 발생 방지
EOL

echo "[reCAPTCHA 패치] 패치 로그 생성 완료: $LOG_FILE" 