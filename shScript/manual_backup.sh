#!/bin/bash

# 날짜 형식 생성
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/git/topmkt_${DATE}"

# 백업 설명 메시지(옵션)
DESC="$1"
if [ -z "$DESC" ]; then
  DESC="(설명 없음)"
fi

echo "[백업] 시작: $DATE"

# 1. Git 로컬 커밋만 수행 (푸시 없음)
echo "[백업] Git add ."
cd /var/www/html/topmkt
git add .
echo "[백업] Git commit"
git commit -m "[BACKUP] ${DATE} 백업 - ${DESC}"
echo "[백업] Git 로컬 커밋 완료 (푸시는 수행되지 않음)"

# 2. 전체 프로젝트 백업
echo "[백업] 전체 프로젝트 백업 시작..."
mkdir -p "${BACKUP_DIR}"

# 제외할 디렉토리/파일 목록
EXCLUDE_LIST=(
    ".git"
    "node_modules"
    "vendor"
    "*.log"
    "*.log.*"
    "*.tmp"
    "*.temp"
    "*.swp"
    "*.swo"
)

# 제외 옵션 생성
echo "[백업] 제외 옵션 생성..."
EXCLUDE_OPTIONS=""
for item in "${EXCLUDE_LIST[@]}"; do
    EXCLUDE_OPTIONS+="--exclude='${item}' "
done

echo "[백업] rsync 시작..."
eval "rsync -av --delete ${EXCLUDE_OPTIONS} /var/www/html/topmkt/ ${BACKUP_DIR}/"
echo "[백업] rsync 완료"

# 백업 정보 파일 생성
echo "[백업] backup_info.txt 생성..."
cat > "${BACKUP_DIR}/backup_info.txt" << EOL
백업 시간: $(date)
백업 유형: 전체 프로젝트
백업 크기: $(du -sh "${BACKUP_DIR}" | cut -f1)
제외된 항목: ${EXCLUDE_LIST[*]}
백업 설명: ${DESC}
로컬 커밋: 예 (Git 태그 및 푸시 없음)
EOL

echo "[백업] 전체 프로젝트 백업 완료"
echo "[백업] 백업 위치: ${BACKUP_DIR}"
echo "[백업] 종료: $(date +%Y%m%d_%H%M%S)" 