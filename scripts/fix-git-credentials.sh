#!/bin/bash

# Git 자격 증명 수정 스크립트
# Git credentials 파일에서 잘못된 GitHub URL 수정

echo "Git 자격 증명 파일 찾는 중..."

# 가능한 Git credentials 파일 경로들
CREDENTIAL_PATHS=(
    "/root/.git-credentials"
    "/home/$(whoami)/.git-credentials" 
    "$(git config --global credential.helper | grep store | sed 's/store --file=//')"
)

# 프로젝트 디렉토리에서 실행
cd /var/www/html/topmkt

# Git config에서 credential helper 확인
CREDENTIAL_HELPER=$(git config --get credential.helper 2>/dev/null)
echo "현재 credential helper: $CREDENTIAL_HELPER"

if [[ "$CREDENTIAL_HELPER" == "store"* ]]; then
    # store 방식인 경우 기본 파일 경로 확인
    if [[ "$CREDENTIAL_HELPER" == *"--file="* ]]; then
        CREDENTIAL_FILE=$(echo "$CREDENTIAL_HELPER" | sed 's/.*--file=//')
        CREDENTIAL_PATHS+=("$CREDENTIAL_FILE")
    fi
fi

# 각 경로에서 credentials 파일 찾기
for path in "${CREDENTIAL_PATHS[@]}"; do
    if [[ -f "$path" ]]; then
        echo "Git credentials 파일 발견: $path"
        echo "현재 내용:"
        cat "$path"
        
        # 잘못된 URL 수정 (github.com 대신 github.com으로)
        if grep -q "https://.*@github.com$" "$path"; then
            echo "잘못된 GitHub URL 발견. 수정 중..."
            sed -i 's/@github\.com$/@github.com/' "$path"
            echo "수정 완료!"
            echo "수정된 내용:"
            cat "$path"
        else
            echo "GitHub URL은 정상입니다."
        fi
        
        exit 0
    fi
done

echo "Git credentials 파일을 찾을 수 없습니다."
echo "Git push 테스트를 진행합니다..."

# Git push 테스트
git push origin master --tags 2>&1 | tee /tmp/git_push_test.log

echo "Git push 테스트 결과:"
cat /tmp/git_push_test.log