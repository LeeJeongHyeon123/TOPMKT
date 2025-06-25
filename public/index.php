<?php
/**
 * 탑마케팅 애플리케이션 진입점
 * 
 * 모든 요청은 이 파일을 통해 처리됩니다.
 */

// 강의 등록 디버깅을 위한 로그
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/lectures/store') !== false) {
    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "=== INDEX.PHP에서 캐치 ===\n", FILE_APPEND);
    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND);
    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "POST 데이터 존재: " . (empty($_POST) ? 'NO' : 'YES') . "\n", FILE_APPEND);
    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "FILES 데이터 존재: " . (empty($_FILES) ? 'NO' : 'YES') . "\n", FILE_APPEND);
}

// 오류 표시 (프로덕션에서는 비활성화)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

try {
    // 상대 경로 설정
    define('ROOT_PATH', dirname(__DIR__));
    define('SRC_PATH', ROOT_PATH . '/src');
    define('CONFIG_PATH', SRC_PATH . '/config');

    // 설정 파일 로드
    require_once CONFIG_PATH . '/config.php';
    require_once CONFIG_PATH . '/database.php';
    require_once CONFIG_PATH . '/routes.php';

    // 기본 세션 시작
    if (session_status() === PHP_SESSION_NONE) {
        // 세션 설정을 먼저 적용
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // HTTPS 환경에서만 1
        ini_set('session.gc_maxlifetime', 2592000); // 30일
        ini_set('session.cookie_lifetime', 0); // 브라우저 종료시
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1000);
        
        // PHP 버전에 따른 세션 시작
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => false, // HTTPS 환경에서만 true
                'cookie_samesite' => 'Strict',
                'gc_maxlifetime' => 2592000, // 30일
                'cookie_lifetime' => 0 // 브라우저 종료시
            ]);
        } else {
            // PHP 7.3 미만에서는 cookie_samesite 지원 안함
            session_start();
        }
    }
    
    // 기존 Remember Token 쿠키가 있는 경우 정리 (마이그레이션 호환성)
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    }
    
    // JWT 토큰을 통한 자동 인증은 AuthMiddleware에서 처리

    // 라우팅 처리
    $router = new Router();
    $router->dispatch();
    
} catch (Exception $e) {
    echo "<h1>오류 발생</h1>";
    echo "<p>오류 메시지: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>파일: " . $e->getFile() . "</p>";
    echo "<p>라인: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Error $e) {
    echo "<h1>치명적 오류 발생</h1>";
    echo "<p>오류 메시지: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>파일: " . $e->getFile() . "</p>";
    echo "<p>라인: " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>