#!/bin/bash
# 탑마케팅 스테이징 배포 스크립트

# 스테이징 서버 정보
STAGING_SERVER="staging.topmktx.com"
STAGING_USER="deploy"
STAGING_PATH="/var/www/html/staging.topmktx.com"

# 배포 시간 기록
DEPLOY_TIME=$(date +"%Y-%m-%d %H:%M:%S")
echo "스테이징 배포 시작: $DEPLOY_TIME"

# 스테이징 설정 파일 복사
echo "스테이징 환경 설정 파일 적용 중..."
cp environments/staging/config.php src/config/config.php

# 로컬 빌드
echo "빌드 중..."
# 필요한 빌드 명령어 추가

# 서버에 배포
echo "스테이징 서버에 배포 중..."
# rsync나 SCP 등을 이용한 배포 명령어 추가
# 예: rsync -avz --delete --exclude='.git' --exclude='environments' ./ $STAGING_USER@$STAGING_SERVER:$STAGING_PATH

# 데이터베이스 마이그레이션
echo "데이터베이스 마이그레이션 중..."
# 마이그레이션 명령어 추가

# 캐시 초기화
echo "캐시 초기화 중..."
# 캐시 초기화 명령어 추가

echo "스테이징 배포 완료: $(date +"%Y-%m-%d %H:%M:%S")"
exit 0 