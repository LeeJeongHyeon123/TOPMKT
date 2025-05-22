#!/bin/bash

# 날짜 형식 생성
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/git/topmkt_${DATE}"

# 1. Git 백업
echo "Git 백업 시작..."
git add .
git commit -m "[BACKUP] ${DATE} 백업"
git push origin master
echo "Git 백업 완료"

# 2. 전체 프로젝트 백업
echo "전체 프로젝트 백업 시작..."
mkdir -p "${BACKUP_DIR}"
cp -rf /var/www/html/topmkt/* "${BACKUP_DIR}"

# 백업 정보 파일 생성
cat > "${BACKUP_DIR}/backup_info.txt" << EOL
백업 시간: $(date)
백업 유형: 전체 프로젝트
백업 크기: $(du -sh "${BACKUP_DIR}" | cut -f1)
EOL

echo "전체 프로젝트 백업 완료"
echo "백업 위치: ${BACKUP_DIR}" 