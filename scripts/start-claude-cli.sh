#!/bin/bash

# 탑마케팅 Claude CLI 시작 스크립트
# MySQL 연결 지원 및 PHP MySQLi 자동 설치

echo "🚀 탑마케팅 Claude CLI 환경 시작..."

# 기존 컨테이너가 실행 중이면 중지
if [ $(docker ps -q -f name=claude-cli-session) ]; then
    echo "⏹️  기존 Claude CLI 컨테이너 중지 중..."
    docker stop claude-cli-session
    docker rm claude-cli-session
fi

# 새로운 컨테이너 시작
echo "📦 새 Claude CLI 컨테이너 시작 중..."
docker-compose up -d claude-cli

# 설치 진행 상황 확인
echo "⏳ MySQL 클라이언트 및 PHP MySQLi 설치 중..."
sleep 10

# 컨테이너 상태 확인
if [ $(docker ps -q -f name=claude-cli-session) ]; then
    echo "✅ Claude CLI 컨테이너가 성공적으로 시작되었습니다!"
    echo ""
    echo "📋 설치된 확장:"
    docker exec claude-cli-session php -m | grep -E "(mysqli|pdo_mysql|mysqlnd)"
    echo ""
    echo "🔌 MySQL 연결 테스트:"
    docker exec claude-cli-session mysql -h 211.110.140.147 -u root -p'Dnlszkem1!' -e "SELECT 'MySQL 연결 성공!' as status;"
    echo ""
    echo "🎯 사용법:"
    echo "  docker exec -it claude-cli-session bash"
    echo "  또는"  
    echo "  docker exec -it claude-cli-session mysql -h 211.110.140.147 -u root -p'Dnlszkem1!' topmkt"
else
    echo "❌ Claude CLI 컨테이너 시작에 실패했습니다."
    echo "로그 확인: docker logs claude-cli-session"
    exit 1
fi 