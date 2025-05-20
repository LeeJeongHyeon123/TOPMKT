<?php
// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 세션 시작
session_start();

// 헤더 인클루드
require_once __DIR__ . '/includes/header.php';
?>

<main class="error-container">
    <div class="error-content">
        <h1>404 Not Found</h1>
        <p>요청하신 페이지를 찾을 수 없습니다.</p>
        <p>페이지가 이동되었거나 삭제되었을 수 있습니다.</p>
        <a href="/" class="btn btn-primary">홈으로 돌아가기</a>
    </div>
</main>

<style>
.error-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    padding: 2rem;
}

.error-content {
    text-align: center;
    max-width: 600px;
}

.error-content h1 {
    color: #dc3545;
    margin-bottom: 1rem;
}

.error-content p {
    margin-bottom: 1rem;
    color: #666;
}

.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-primary:hover {
    background-color: #0056b3;
}
</style>

<?php
// 푸터 인클루드
require_once __DIR__ . '/includes/footer.php';
?> 