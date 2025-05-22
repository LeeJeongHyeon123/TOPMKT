<?php
// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 설정 파일 로드
require_once '/var/www/html/topmkt/config/config.php';
require_once '/var/www/html/topmkt/includes/Database.php';
require_once '/var/www/html/topmkt/includes/functions.php';

// 현재 로그인 상태 확인
$isLoggedIn = isset($_SESSION['user_id']);

// 다국어 처리
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
// require_once "/var/www/html/topmkt/resources/lang/{$currentLang}/messages.php"; // 이 줄을 삭제하거나 주석 처리

// 공지사항 ID 가져오기
$noticeId = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($noticeId <= 0) {
    // 잘못된 ID인 경우, 목록 페이지로 리디렉션 또는 에러 메시지 표시
    header('Location: /notice/index.php');
    exit;
}

// 데이터베이스 연결
$db = Database::getInstance();
$conn = $db->getConnection();

// 조회수 증가 처리
$updateViewCountStmt = $conn->prepare("UPDATE notices SET view_count = view_count + 1 WHERE id = :id");
$updateViewCountStmt->bindParam(':id', $noticeId, PDO::PARAM_INT);
$updateViewCountStmt->execute();

// 공지사항 상세 정보 가져오기
$stmt = $conn->prepare("SELECT n.id, n.title, n.content, u.nickname as author, n.created_at, n.view_count FROM notices n JOIN users u ON n.user_id = u.id WHERE n.id = :id");
$stmt->bindParam(':id', $noticeId, PDO::PARAM_INT);
$stmt->execute();
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notice) {
    // 공지사항이 없는 경우, 목록 페이지로 리디렉션 또는 에러 메시지 표시
    // 여기서는 간단히 404 페이지나 에러 메시지를 표시할 수 있습니다.
    // 지금은 목록으로 리디렉션합니다.
    header('Location: /notice/index.php?error=not_found');
    exit;
}

?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($notice['title']) ?> - <?= __('notice.list.title') ?> - <?= __('common.site_title') ?></title>
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/notice.css">
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
</head>
<body>
    <?php include_once '/var/www/html/topmkt/includes/header.php'; ?>

    <main class="notice-view-container">
        <div class="container">
            <div class="notice-header">
                <h1 class="notice-title"><?= htmlspecialchars($notice['title']) ?></h1>
                <div class="notice-meta">
                    <span class="author"><?= __('notice.view.author') ?>: <?= htmlspecialchars($notice['author']) ?></span>
                    <span class="date"><?= __('notice.view.date') ?>: <?= date('Y-m-d H:i', strtotime($notice['created_at'])) ?></span>
                    <span class="views"><?= __('notice.view.views') ?>: <?= htmlspecialchars($notice['view_count']) ?></span>
                </div>
            </div>
            <hr class="notice-divider">
            <div class="notice-content">
                <?= nl2br(htmlspecialchars($notice['content'])) // nl2br은 HTML에서 줄바꿈을 <br>로 변경합니다. 보안을 위해 htmlspecialchars와 함께 사용합니다. ?>
            </div>
            
            <div class="notice-actions">
                <a href="/notice/index.php" class="btn btn-list"><?= __('notice.view.go_list') ?></a>
            </div>
        </div>
    </main>

    <?php include_once '/var/www/html/topmkt/includes/footer.php'; ?>
</body>
</html> 