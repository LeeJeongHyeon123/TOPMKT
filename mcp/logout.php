<?php
// 세션 시작
session_start();

// 로그인 이력 저장
if (isset($_SESSION['user_id'])) {
    require_once 'includes/Database.php';
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // 로그아웃 시간 기록
        $stmt = $conn->prepare("
            UPDATE users 
            SET last_login_at = NOW() 
            WHERE id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log("Logout Error: " . $e->getMessage());
    }
}

// 세션 데이터 삭제
$_SESSION = array();

// 세션 쿠키 삭제
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// 세션 파괴
session_destroy();

// 메인 페이지로 리다이렉트
header('Location: /');
exit; 