<?php
// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 세션 시작
session_start();

// 인증 상태 확인
if (isset($_SESSION['user_id'])) {
    // 이미 로그인된 경우 메인 페이지로 리다이렉트
    header('Location: /');
    exit;
}

// 인증 페이지 HTML 출력
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - 탑마케팅</title>
</head>
<body>
    <h1>로그인</h1>
    <form action="send-verification.php" method="post">
        <div>
            <label for="phone">전화번호:</label>
            <input type="tel" id="phone" name="phone" required>
        </div>
        <button type="submit">인증번호 받기</button>
    </form>
</body>
</html> 