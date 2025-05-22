#!/bin/bash
# 회원가입 테스트 데이터 초기화 쉘 스크립트
# 사용법: ./cleanup_user.sh [전화번호]

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[0;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 현재 디렉토리 경로
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# 스크립트 경로
PYTHON_SCRIPT="$SCRIPT_DIR/cleanup_test_users.py"
PHP_SCRIPT="$SCRIPT_DIR/cleanup_test_users.php"

# 함수: 사용법 출력
usage() {
    echo -e "${BLUE}회원가입 테스트 데이터 초기화 스크립트${NC}"
    echo
    echo -e "${YELLOW}사용법:${NC}"
    echo "  ./cleanup_user.sh [전화번호]"
    echo
    echo -e "${YELLOW}예시:${NC}"
    echo "  ./cleanup_user.sh                # 모든 사용자 데이터 삭제 (확인 메시지 표시)"
    echo "  ./cleanup_user.sh 01012345678    # 특정 전화번호의 사용자 데이터 삭제"
    echo
    exit 1
}

# 실행 권한 확인 및 설정
if [ -f "$PYTHON_SCRIPT" ] && [ ! -x "$PYTHON_SCRIPT" ]; then
    echo -e "${YELLOW}Python 스크립트에 실행 권한 부여 중...${NC}"
    chmod +x "$PYTHON_SCRIPT"
fi

if [ -f "$PHP_SCRIPT" ] && [ ! -x "$PHP_SCRIPT" ]; then
    echo -e "${YELLOW}PHP 스크립트에 실행 권한 부여 중...${NC}"
    chmod +x "$PHP_SCRIPT"
fi

# Python 사용 가능 여부 확인
PYTHON_AVAILABLE=false
if command -v python3 &>/dev/null && [ -f "$PYTHON_SCRIPT" ]; then
    # Python 스크립트를 실행해보고 오류가 없는지 확인
    if python3 -c "import pymysql, firebase_admin" &>/dev/null; then
        PYTHON_AVAILABLE=true
    else
        echo -e "${YELLOW}필요한 Python 모듈이 설치되어 있지 않습니다. PHP 스크립트를 사용합니다.${NC}"
    fi
else
    echo -e "${YELLOW}Python이 설치되어 있지 않거나 Python 스크립트가 없습니다. PHP 스크립트를 사용합니다.${NC}"
fi

# PHP 사용 가능 여부 확인
PHP_AVAILABLE=false
if command -v php &>/dev/null && [ -f "$PHP_SCRIPT" ]; then
    PHP_AVAILABLE=true
else
    echo -e "${YELLOW}PHP가 설치되어 있지 않거나 PHP 스크립트가 없습니다.${NC}"
fi

# 스크립트 실행
if [ "$PYTHON_AVAILABLE" = true ]; then
    # Python 스크립트 실행
    if [ -z "$1" ]; then
        # 매개변수가 없는 경우 - 모든 사용자 삭제
        echo -e "${YELLOW}모든 사용자 데이터를 삭제합니다... (Python)${NC}"
        python3 "$PYTHON_SCRIPT" --all --confirm
    else
        # 전화번호가 제공된 경우 - 특정 사용자 삭제
        echo -e "${YELLOW}전화번호 '$1'의 사용자 데이터를 삭제합니다... (Python)${NC}"
        python3 "$PYTHON_SCRIPT" --phone "$1" --confirm
    fi
elif [ "$PHP_AVAILABLE" = true ]; then
    # PHP 스크립트 실행
    if [ -z "$1" ]; then
        # 매개변수가 없는 경우 - 모든 사용자 삭제
        echo -e "${YELLOW}모든 사용자 데이터를 삭제합니다... (PHP)${NC}"
        php "$PHP_SCRIPT" --all --confirm
    else
        # 전화번호가 제공된 경우 - 특정 사용자 삭제
        echo -e "${YELLOW}전화번호 '$1'의 사용자 데이터를 삭제합니다... (PHP)${NC}"
        php "$PHP_SCRIPT" --phone="$1" --confirm
    fi
else
    echo -e "${RED}오류: Python과 PHP 스크립트를 모두 실행할 수 없습니다.${NC}"
    echo -e "${RED}필요한 스크립트가 존재하는지 확인하고 필요한 모듈을 설치하세요.${NC}"
    exit 1
fi

# 스크립트 종료 메시지
echo
echo -e "${GREEN}데이터 초기화 작업이 완료되었습니다.${NC}"
echo -e "${BLUE}이제 새로운 테스트 계정으로 회원가입을 진행할 수 있습니다.${NC}" 