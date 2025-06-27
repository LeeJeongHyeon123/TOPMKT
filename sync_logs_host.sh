#!/bin/bash

echo "=== 호스트용 로그 동기화 스크립트 ==="
echo

SOURCE_LOG="/var/log/php-fpm/www-error.log"
TARGET_LOG="/var/www/html/topmkt/current_logs.log"

echo "소스: $SOURCE_LOG"
echo "타겟: $TARGET_LOG"
echo

# 소스 파일 확인
if [[ ! -f "$SOURCE_LOG" ]]; then
    echo "❌ 소스 로그 파일이 없습니다: $SOURCE_LOG"
    exit 1
fi

# 타겟 디렉토리 확인
TARGET_DIR=$(dirname "$TARGET_LOG")
echo "📁 타겟 디렉토리: $TARGET_DIR"
ls -la "$TARGET_DIR" | head -3

# 파일 복사
echo
echo "📋 로그 파일 복사..."
cp "$SOURCE_LOG" "$TARGET_LOG"

if [[ $? -eq 0 ]]; then
    echo "✅ 복사 성공!"
    
    # 권한 설정
    chmod 644 "$TARGET_LOG"
    
    # 파일 정보
    echo
    echo "📊 복사된 파일 정보:"
    ls -la "$TARGET_LOG"
    
    FILE_SIZE=$(stat -c%s "$TARGET_LOG" 2>/dev/null)
    FILE_SIZE_MB=$((FILE_SIZE / 1024 / 1024))
    echo "크기: ${FILE_SIZE_MB}MB"
    
    TOTAL_LINES=$(wc -l < "$TARGET_LOG" 2>/dev/null)
    echo "라인 수: ${TOTAL_LINES}"
    
    echo
    echo "📄 마지막 5줄 미리보기:"
    echo "---------------------"
    tail -5 "$TARGET_LOG"
    
    echo
    echo "✅ Claude Code에서 다음 명령으로 로그 확인 가능:"
    echo "   cat /workspace/current_logs.log"
    echo "   tail -20 /workspace/current_logs.log"
    
else
    echo "❌ 복사 실패"
    
    # 대안: 최근 로그만 복사
    echo
    echo "🔄 대안: 최근 1000줄만 복사..."
    tail -1000 "$SOURCE_LOG" > "$TARGET_LOG"
    
    if [[ $? -eq 0 ]]; then
        echo "✅ 부분 복사 성공!"
        chmod 644 "$TARGET_LOG"
        ls -la "$TARGET_LOG"
    else
        echo "❌ 부분 복사도 실패"
    fi
fi