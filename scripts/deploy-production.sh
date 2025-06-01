#!/bin/bash
# 탑마케팅 프로덕션 배포 스크립트

# 프로덕션 서버 정보
PRODUCTION_SERVER="www.topmktx.com"
PRODUCTION_USER="deploy"
PRODUCTION_PATH="/var/www/html/topmkt"

# 배포 시간 기록
DEPLOY_TIME=$(date +"%Y-%m-%d %H:%M:%S")
echo "프로덕션 배포 시작: $DEPLOY_TIME"

# 배포 전 백업
echo "기존 프로덕션 백업 중..."
BACKUP_DIR="/var/backups/topmkt/$(date +"%Y%m%d%H%M%S")"
# 백업 명령어 추가
# 예: ssh $PRODUCTION_USER@$PRODUCTION_SERVER "mkdir -p $BACKUP_DIR && cp -R $PRODUCTION_PATH/* $BACKUP_DIR/"

# 프로덕션 설정 파일 복사
echo "프로덕션 환경 설정 파일 적용 중..."
cp environments/production/config.php src/config/config.php

# 로컬 빌드
echo "빌드 중..."
# 필요한 빌드 명령어 추가

# 서버에 배포
echo "프로덕션 서버에 배포 중..."
# rsync나 SCP 등을 이용한 배포 명령어 추가
# 예: rsync -avz --delete --exclude='.git' --exclude='environments' ./ $PRODUCTION_USER@$PRODUCTION_SERVER:$PRODUCTION_PATH

# 데이터베이스 마이그레이션
echo "데이터베이스 마이그레이션 중..."
# 마이그레이션 명령어 추가

# 캐시 초기화
echo "캐시 초기화 중..."
# 캐시 초기화 명령어 추가

# 배포 후 알림
echo "배포 알림 발송 중..."
# 슬랙, 이메일 등 알림 명령어 추가

echo "프로덕션 배포 완료: $(date +"%Y-%m-%d %H:%M:%S")"
exit 0 