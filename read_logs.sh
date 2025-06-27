#!/bin/bash

echo "=== 탑마케팅 로그 읽기 스크립트 ==="
echo

# 기본 옵션 설정
LINES=50
FILTER=""

# 파라미터 처리
while [[ $# -gt 0 ]]; do
    case $1 in
        -n|--lines)
            LINES="$2"
            shift 2
            ;;
        -f|--filter)
            FILTER="$2"
            shift 2
            ;;
        -h|--help)
            echo "사용법: $0 [옵션]"
            echo "옵션:"
            echo "  -n, --lines N     최근 N줄 출력 (기본값: 50)"
            echo "  -f, --filter STR  특정 문자열로 필터링"
            echo "  -h, --help        도움말 출력"
            echo
            echo "예시:"
            echo "  $0                    # 최근 50줄 출력"
            echo "  $0 -n 100            # 최근 100줄 출력"
            echo "  $0 -f SMS            # SMS 관련 로그만 출력"
            echo "  $0 -n 200 -f 인증번호  # 최근 200줄 중 인증번호 관련만"
            exit 0
            ;;
        *)
            echo "알 수 없는 옵션: $1"
            echo "도움말을 보려면 $0 -h 를 실행하세요."
            exit 1
            ;;
    esac
done

# 가능한 로그 파일 경로들 확인
LOG_PATHS=(
    "/var/log/php-fpm/www-error.log"
    "/var/log/httpd/error_log"
    "/var/log/apache2/error.log"
    "/var/www/html/topmkt/logs/topmkt_errors.log"
)

echo "로그 파일 경로 확인 중..."
echo "========================"

FOUND_LOG=""
for LOG_PATH in "${LOG_PATHS[@]}"; do
    if [[ -f "$LOG_PATH" ]]; then
        FILE_SIZE=$(stat -c%s "$LOG_PATH" 2>/dev/null || echo "0")
        FILE_SIZE_MB=$((FILE_SIZE / 1024 / 1024))
        echo "✅ $LOG_PATH (${FILE_SIZE_MB}MB)"
        if [[ $FILE_SIZE -gt 0 && -z "$FOUND_LOG" ]]; then
            FOUND_LOG="$LOG_PATH"
        fi
    else
        echo "❌ $LOG_PATH (파일 없음)"
    fi
done

if [[ -z "$FOUND_LOG" ]]; then
    echo
    echo "❌ 사용 가능한 로그 파일을 찾을 수 없습니다."
    exit 1
fi

echo
echo "사용할 로그 파일: $FOUND_LOG"
echo "============================================"

# 파일 정보 출력
FILE_SIZE=$(stat -c%s "$FOUND_LOG" 2>/dev/null)
FILE_SIZE_MB=$((FILE_SIZE / 1024 / 1024))
echo "📊 파일 크기: ${FILE_SIZE_MB}MB (${FILE_SIZE} bytes)"

TOTAL_LINES=$(wc -l < "$FOUND_LOG" 2>/dev/null)
echo "📝 총 로그 라인 수: ${TOTAL_LINES}"
echo

# 로그 출력
if [[ -n "$FILTER" ]]; then
    echo "🔍 필터: '$FILTER' (최근 $LINES 줄에서 검색)"
    echo "----------------------------------------"
    tail -n "$LINES" "$FOUND_LOG" | grep -i "$FILTER" | tail -20
else
    echo "📄 최근 $LINES 줄:"
    echo "----------------"
    tail -n "$LINES" "$FOUND_LOG"
fi

echo
echo "✅ 로그 읽기 완료!"