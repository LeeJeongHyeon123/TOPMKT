#!/bin/bash

echo "=== Apache 로그 설정 진단 스크립트 ==="
echo

echo "1. Apache 에러 로그 설정 확인:"
echo "================================"
grep -n "ErrorLog" /etc/httpd/conf/httpd.conf
echo

echo "2. SSL 에러 로그 설정 확인:"
echo "=========================="
grep -n "ErrorLog" /etc/httpd/conf.d/ssl.conf
echo

echo "3. PHP 모듈 설정 확인:"
echo "==================="
grep -n "php" /etc/httpd/conf/httpd.conf
echo

echo "4. Apache 로그 디렉토리 확인:"
echo "=========================="
ls -la /var/log/httpd/ 2>/dev/null || echo "httpd log directory not found"
echo

echo "5. 현재 로그 파일들 확인:"
echo "====================="
find /var/log -name "*error*" -type f 2>/dev/null | head -10
echo

echo "6. Apache 로그 파일 권한 확인:"
echo "==========================="
ls -la /var/log/httpd/error_log 2>/dev/null || echo "error_log file not found"
ls -la /var/log/httpd/access_log 2>/dev/null || echo "access_log file not found"
echo

echo "7. PHP-FPM 로그 확인:"
echo "==================="
ls -la /var/log/php-fpm/
tail -5 /var/log/php-fpm/www-error.log 2>/dev/null || echo "PHP-FPM error log is empty or not accessible"
echo

echo "8. 디스크 공간 확인:"
echo "=================="
df -h /var/log
echo

echo "9. Apache 프로세스 사용자 확인:"
echo "============================"
ps aux | grep httpd | grep -v grep | head -3
echo

echo "진단 완료!"