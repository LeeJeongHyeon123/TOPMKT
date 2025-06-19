#!/bin/bash

# 직접 Docker 명령어로 Claude CLI 환경 구성
echo "🚀 Claude CLI 환경 설정 (직접 방식)..."

# 기존 컨테이너 정리
docker stop claude-cli-direct 2>/dev/null
docker rm claude-cli-direct 2>/dev/null

# 새 컨테이너 실행
echo "📦 컨테이너 실행 중..."
docker run -d \
  --name claude-cli-direct \
  --network host \
  -v $(pwd):/workspace \
  -w /workspace \
  node:18-bullseye-slim \
  tail -f /dev/null

# 패키지 설치
echo "⏳ MySQL 클라이언트 및 PHP 설치 중..."
docker exec claude-cli-direct bash -c "
  apt-get update && 
  apt-get install -y default-mysql-client php php-mysqli php-pdo-mysql -qq
"

# 설치 확인
echo "✅ 설치 완료! 확인 중..."
echo "📋 PHP 확장:"
docker exec claude-cli-direct php -m | grep -E "(mysqli|pdo_mysql|mysqlnd)"

echo ""
echo "🔌 MySQL 연결 테스트:"
docker exec claude-cli-direct mysql -h 211.110.140.147 -u root --password='Dnlszkem1!' -e "SELECT 'MySQL 연결 성공!' as status;"

echo ""
echo "🎯 사용법:"
echo "  docker exec -it claude-cli-direct bash" 