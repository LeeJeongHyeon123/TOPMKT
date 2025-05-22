<?php
// api/auth/logout.php

// Composer autoloader (필요한 경우 - 현재는 특별한 클래스 사용 X)
// require_once __DIR__ . '/../../vendor/autoload.php';

// PHP 세션을 사용하는 경우 세션 파기 (주석 처리됨, 필요시 활성화)
/*
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 모든 세션 변수 제거
$_SESSION = array();

// 세션 쿠키 삭제
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 세션 파괴
// session_destroy();
*/

// CORS 설정
header('Access-Control-Allow-Origin: *'); // 실제 운영 환경에서는 특정 도메인으로 제한하는 것이 좋습니다.
header('Content-Type: application/json');

// 클라이언트에게 성공 응답 전송
echo json_encode([
    'success' => true,
    'message' => '성공적으로 로그아웃되었습니다.'
]);

exit;
?> 