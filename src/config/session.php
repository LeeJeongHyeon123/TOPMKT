<?php
/**
 * 세션 설정 파일
 * 로그인 상태 유지 및 세션 관리 설정
 */

/**
 * 세션 시작 및 설정
 * @param bool $rememberMe 로그인 상태 유지 여부
 */
function initializeSession($rememberMe = false) {
    // 세션이 이미 시작된 경우 반환
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    
    // HTTPS 환경 확인
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    // 기본 세션 수명 설정
    $sessionLifetime = $rememberMe ? 2592000 : 1800; // 30일 : 30분
    $cookieLifetime = $rememberMe ? 2592000 : 0; // 30일 : 브라우저 종료시
    
    // 세션 설정
    ini_set('session.gc_maxlifetime', $sessionLifetime);
    ini_set('session.cookie_lifetime', $cookieLifetime);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', $isHttps ? 1 : 0);
    ini_set('session.cookie_samesite', 'Strict');
    
    // 세션 시작
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => $isHttps,
        'cookie_samesite' => 'Strict',
        'gc_maxlifetime' => $sessionLifetime,
        'cookie_lifetime' => $cookieLifetime
    ]);
}

/**
 * 세션 유효성 확인
 * Remember Me 토큰이 있는 경우 자동 로그인 처리
 */
function validateSession() {
    // 세션에 user_id가 없고 remember 쿠키가 있는 경우
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // User 모델로 토큰 검증
        require_once SRC_PATH . '/models/User.php';
        $userModel = new User();
        $user = $userModel->findByRememberToken($token);
        
        if ($user) {
            // 자동 로그인 처리
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nickname'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['user_role'] = $user['role'];
            
            // 새로운 remember 토큰 생성
            $newToken = bin2hex(random_bytes(32));
            $expires = time() + 2592000; // 30일
            
            // 토큰 업데이트
            $userModel->updateRememberToken($user['id'], $newToken, date('Y-m-d H:i:s', $expires));
            
            // 쿠키 업데이트
            setcookie(
                'remember_token',
                $newToken,
                $expires,
                '/',
                '',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                true
            );
            
            return true;
        } else {
            // 유효하지 않은 토큰 삭제
            setcookie('remember_token', '', time() - 3600, '/');
        }
    }
    
    return isset($_SESSION['user_id']);
}

/**
 * 세션 갱신
 * 세션 수명을 연장하고 필요시 세션 ID 재생성
 */
function refreshSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        // 10분마다 세션 ID 재생성 (세션 고정 공격 방지)
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 600) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // 세션 활동 시간 업데이트
        $_SESSION['last_activity'] = time();
    }
}

/**
 * 세션 타임아웃 확인
 * @param int $timeout 타임아웃 시간 (초)
 * @return bool 타임아웃 여부
 */
function isSessionTimeout($timeout = 1800) {
    if (isset($_SESSION['last_activity'])) {
        return (time() - $_SESSION['last_activity']) > $timeout;
    }
    return false;
}

/**
 * 세션 종료
 * 로그아웃 시 세션 및 쿠키 완전 삭제
 */
function destroySession() {
    // 세션 변수 초기화
    $_SESSION = [];
    
    // 세션 쿠키 삭제
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Remember Me 쿠키 삭제
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // 세션 파괴
    session_destroy();
}
?>