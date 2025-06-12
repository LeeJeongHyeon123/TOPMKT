#!/bin/bash

# 탑마케팅 플랫폼 자동 복구 스크립트
# 사용법: ./restore.sh [도메인명]
# 예시: ./restore.sh topmktx.com

set -e  # 오류 발생 시 스크립트 중단

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 로그 함수
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 사용법 출력
show_usage() {
    echo "사용법: $0 [도메인명]"
    echo "예시: $0 topmktx.com"
    exit 1
}

# 도메인 파라미터 확인
if [ $# -eq 0 ]; then
    log_warning "도메인명이 제공되지 않았습니다."
    show_usage
fi

DOMAIN=$1
PROJECT_DIR="/var/www/topmkt"
BACKUP_DIR="/tmp/topmkt_backup"

log_info "탑마케팅 플랫폼 복구 시작..."
log_info "도메인: $DOMAIN"
log_info "프로젝트 디렉토리: $PROJECT_DIR"

# 1. 기본 패키지 설치 확인
log_info "1. 시스템 패키지 확인 및 설치..."

# OS 감지
if [[ -f /etc/os-release ]]; then
    . /etc/os-release
    OS=$NAME
fi

case $OS in
    *"Ubuntu"*|*"Debian"*)
        PACKAGE_MANAGER="apt"
        PHP_FPM_SOCKET="/var/run/php/php8.1-fpm.sock"
        ;;
    *"CentOS"*|*"Red Hat"*|*"Rocky"*)
        PACKAGE_MANAGER="yum"
        PHP_FPM_SOCKET="/var/run/php-fpm/www.sock"
        ;;
    *)
        log_error "지원되지 않는 OS입니다: $OS"
        exit 1
        ;;
esac

# 필수 패키지 설치
if [[ $PACKAGE_MANAGER == "apt" ]]; then
    sudo apt update
    sudo apt install -y git nginx php php-fpm php-mysql php-curl php-json php-mbstring mariadb-server curl
elif [[ $PACKAGE_MANAGER == "yum" ]]; then
    sudo yum update -y
    sudo yum install -y git nginx php php-fpm php-mysql php-curl php-json php-mbstring mariadb-server curl
fi

log_success "시스템 패키지 설치 완료"

# 2. Git 저장소 클론
log_info "2. Git 저장소 클론..."

if [[ -d $PROJECT_DIR ]]; then
    log_warning "프로젝트 디렉토리가 이미 존재합니다. 백업 후 제거합니다."
    sudo mv $PROJECT_DIR $BACKUP_DIR
fi

sudo git clone https://github.com/LeeJeongHyeon123/topmkt.git $PROJECT_DIR
log_success "Git 저장소 클론 완료"

# 3. 환경 설정 파일 생성
log_info "3. 환경 설정 파일 생성..."

cd $PROJECT_DIR
sudo cp .env.example .env

log_warning "환경 설정 파일(.env)을 수동으로 편집해야 합니다."
log_info "편집할 파일: $PROJECT_DIR/.env"

# 4. 권한 설정
log_info "4. 파일 권한 설정..."

sudo chown -R www-data:www-data $PROJECT_DIR
sudo chmod -R 755 $PROJECT_DIR

# 업로드 디렉토리 생성 및 권한 설정
sudo mkdir -p $PROJECT_DIR/public/assets/uploads
sudo mkdir -p $PROJECT_DIR/logs
sudo chmod -R 777 $PROJECT_DIR/public/assets/uploads
sudo chmod -R 777 $PROJECT_DIR/logs

log_success "파일 권한 설정 완료"

# 5. MariaDB 설정
log_info "5. MariaDB 설정..."

sudo systemctl start mariadb
sudo systemctl enable mariadb

log_warning "MariaDB 보안 설정을 실행합니다. root 비밀번호를 설정하세요."
sudo mysql_secure_installation

# 데이터베이스 스키마 복구
log_info "데이터베이스 스키마 복구 중..."
read -p "MariaDB root 비밀번호를 입력하세요: " -s MYSQL_ROOT_PASSWORD
echo

mysql -u root -p$MYSQL_ROOT_PASSWORD < $PROJECT_DIR/database/schema.sql
log_success "데이터베이스 스키마 복구 완료"

# 6. Nginx 설정
log_info "6. Nginx 설정..."

# Nginx 설정 파일 생성
sudo tee /etc/nginx/sites-available/topmkt > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN www.$DOMAIN;
    root $PROJECT_DIR/public;
    index index.php index.html;

    # 보안 헤더
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # 메인 라우팅
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP 처리
    location ~ \\.php\$ {
        fastcgi_pass unix:$PHP_FPM_SOCKET;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    # 정적 파일 캐싱
    location ~* \\.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)\$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # 업로드 파일 접근 제한
    location ^~ /assets/uploads/ {
        location ~ \\.(php|phtml|pl|py)\$ {
            deny all;
        }
    }

    # 숨김 파일 접근 차단
    location ~ /\\. {
        deny all;
    }

    # 로그 파일
    access_log /var/log/nginx/topmkt_access.log;
    error_log /var/log/nginx/topmkt_error.log;
}
EOF

# 사이트 활성화
sudo ln -sf /etc/nginx/sites-available/topmkt /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

# Nginx 설정 테스트
sudo nginx -t
if [[ $? -eq 0 ]]; then
    sudo systemctl reload nginx
    log_success "Nginx 설정 완료"
else
    log_error "Nginx 설정에 오류가 있습니다."
    exit 1
fi

# 7. PHP-FPM 설정
log_info "7. PHP-FPM 설정..."

sudo systemctl start php8.1-fpm 2>/dev/null || sudo systemctl start php-fpm
sudo systemctl enable php8.1-fpm 2>/dev/null || sudo systemctl enable php-fpm

log_success "PHP-FPM 설정 완료"

# 8. 서비스 상태 확인
log_info "8. 서비스 상태 확인..."

services=("nginx" "mariadb")
if systemctl is-active --quiet php8.1-fpm; then
    services+=("php8.1-fpm")
elif systemctl is-active --quiet php-fpm; then
    services+=("php-fpm")
fi

for service in "${services[@]}"; do
    if systemctl is-active --quiet $service; then
        log_success "$service 서비스 실행 중"
    else
        log_error "$service 서비스가 실행되지 않습니다."
        sudo systemctl status $service
    fi
done

# 9. 최종 테스트
log_info "9. 최종 테스트..."

# HTTP 응답 테스트
if curl -f -s -o /dev/null http://localhost; then
    log_success "웹 서버 응답 정상"
else
    log_warning "웹 서버 응답 확인 필요"
fi

# 권한 재확인
if [[ -w "$PROJECT_DIR/public/assets/uploads" ]]; then
    log_success "업로드 디렉토리 권한 정상"
else
    log_warning "업로드 디렉토리 권한 확인 필요"
fi

# 완료 메시지
echo
log_success "======================="
log_success "🎉 복구 완료!"
log_success "======================="
echo
log_info "다음 단계를 완료하세요:"
echo
echo "1. 환경 설정 편집:"
echo "   sudo nano $PROJECT_DIR/.env"
echo
echo "2. 데이터베이스 사용자 생성:"
echo "   mysql -u root -p"
echo "   CREATE USER 'topmkt_user'@'localhost' IDENTIFIED BY 'your_password';"
echo "   GRANT ALL PRIVILEGES ON topmkt.* TO 'topmkt_user'@'localhost';"
echo "   FLUSH PRIVILEGES;"
echo
echo "3. Firebase 설정:"
echo "   - Firebase 콘솔에서 새 프로젝트 생성"
echo "   - 설정 정보를 .env 파일에 업데이트"
echo
echo "4. SSL 인증서 설정 (Let's Encrypt):"
echo "   sudo apt install certbot python3-certbot-nginx"
echo "   sudo certbot --nginx -d $DOMAIN -d www.$DOMAIN"
echo
echo "5. 사이트 접속 테스트:"
echo "   http://$DOMAIN"
echo
log_info "로그 파일 위치:"
echo "   - Nginx: /var/log/nginx/topmkt_*.log"
echo "   - PHP-FPM: /var/log/php*-fpm.log"
echo
log_success "복구 스크립트 실행 완료!"