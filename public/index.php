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

// 오류 표시 (개발용으로 활성화)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// topmkt 프로젝트 전용 로그 시스템 설정
$topmktLogPath = '/var/www/html/topmkt/logs/topmkt_errors.log';

// 로그 디렉토리 생성
$logDir = dirname($topmktLogPath);
if (!file_exists($logDir)) {
    @mkdir($logDir, 0755, true);
}

// topmkt 프로젝트 전체에서 사용할 로그 경로 설정
ini_set('log_errors', 1);
ini_set('error_log', $topmktLogPath);

// 전역 상수로 정의 (다른 파일에서도 사용 가능)
define('TOPMKT_LOG_PATH', $topmktLogPath);

// 로그 시스템 테스트
error_log("=== 탑마케팅 애플리케이션 시작 ===");
error_log("현재 시간: " . date('Y-m-d H:i:s'));
error_log("요청 URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A'));
error_log("요청 메서드: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A'));
error_log("로그 경로: " . $topmktLogPath);

try {
    // CORS 헤더 설정
    require_once __DIR__ . '/cors-headers.php';
    
    // 상대 경로 설정
    define('ROOT_PATH', dirname(__DIR__));
    define('SRC_PATH', ROOT_PATH . '/src');
    define('CONFIG_PATH', SRC_PATH . '/config');
    
    // 보안 미들웨어 적용
    require_once SRC_PATH . '/middlewares/SecurityMiddleware.php';
    SecurityMiddleware::setSecurityHeaders();
    
    // Rate Limiting (IP 기반)
    $clientIp = SecurityMiddleware::getClientIp();
    if (!SecurityMiddleware::rateLimit($clientIp, 1000, 3600)) { // 시간당 1000회 제한
        http_response_code(429);
        echo 'Too Many Requests';
        exit;
    }

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