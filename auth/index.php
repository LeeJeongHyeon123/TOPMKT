<?php
// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 현재 URL이 /auth.php가 아닐 경우에만 리다이렉트
if (!preg_match('/\/auth\.php$/', $_SERVER['REQUEST_URI'])) {
    header('Location: /auth');
    exit;
} 