<?php
// 세션 설정
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.gc_maxlifetime', 0); // 세션 시간 무제한
    session_start();
}

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');
ini_set('error_prepend_string', "\n[ERROR] ");
ini_set('error_append_string', "\n");
ini_set('log_errors_max_len', 0); // 로그 길이 제한 없음

// XSS 방지를 위한 출력 이스케이프 함수
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// CSRF 토큰 생성
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF 토큰 검증
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        die('CSRF 토큰이 유효하지 않습니다.');
    }
    return true;
}

// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_NAME', 'TOPMKT');
define('DB_USER', 'root');
define('DB_PASS', 'Dnlszkem1!');

// Firebase 설정
define('FIREBASE_API_KEY', 'AIzaSyDxXxXxXxXxXxXxXxXxXxXxXxXxXxXxXx');
define('FIREBASE_AUTH_DOMAIN', 'topmkt-xxxxx.firebaseapp.com');
define('FIREBASE_PROJECT_ID', 'topmkt-xxxxx');
define('FIREBASE_STORAGE_BUCKET', 'topmkt-xxxxx.appspot.com');
define('FIREBASE_MESSAGING_SENDER_ID', '123456789012');
define('FIREBASE_APP_ID', '1:123456789012:web:abcdef1234567890');

// 기타 설정
define('SITE_URL', 'https://www.topmkt.co.kr');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx']); 