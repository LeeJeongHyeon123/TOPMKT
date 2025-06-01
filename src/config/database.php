<?php
/**
 * 탑마케팅 데이터베이스 설정 파일
 */

// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'topmkt');
define('DB_USER', 'topmkt_user');
define('DB_PASS', 'secure_password_here');
define('DB_CHARSET', 'utf8mb4');

/**
 * 데이터베이스 연결 함수
 *
 * @return PDO 데이터베이스 연결 객체
 */
function getDbConnection() {
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    
    try {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        // 에러 로깅
        error_log('Database connection error: ' . $e->getMessage());
        
        // 개발 모드에서만 오류 표시
        if (APP_DEBUG) {
            echo 'Connection failed: ' . $e->getMessage();
        } else {
            echo 'Database error occurred. Please try again later.';
        }
        
        exit;
    }
} 