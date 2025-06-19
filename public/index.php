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

// 오류 표시
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
        // PHP 버전에 따른 세션 설정
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
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 0);
            ini_set('session.gc_maxlifetime', 2592000);
            ini_set('session.cookie_lifetime', 0);
            session_start();
        }
    }
    
    // Remember Token을 통한 자동 로그인 처리
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        // User 모델 로드
        require_once SRC_PATH . '/models/User.php';
        $userModel = new User();
        
        $token = $_COOKIE['remember_token'];
        $user = $userModel->findByRememberToken($token);
        
        if ($user) {
            // 자동 로그인 처리
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nickname'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['user_role'] = $user['role'] ?? 'GENERAL';
            $_SESSION['last_activity'] = time();
            
            // 세션 ID 재생성
            session_regenerate_id(true);
            
            // 새로운 remember 토큰 생성 및 업데이트
            $newToken = bin2hex(random_bytes(32));
            $expires = time() + 2592000; // 30일
            
            // 데이터베이스 업데이트
            try {
                $userModel->updateRememberToken($user['id'], $newToken, date('Y-m-d H:i:s', $expires));
                
                // 쿠키 업데이트
                setcookie(
                    'remember_token',
                    $newToken,
                    $expires,
                    '/',
                    '',
                    false, // HTTPS 환경에서만 true
                    true // httponly
                );
            } catch (Exception $e) {
                error_log('Remember token 업데이트 실패: ' . $e->getMessage());
            }
        } else {
            // 유효하지 않은 토큰 삭제
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }

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