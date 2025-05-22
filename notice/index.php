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

// 데이터베이스 연결
$db = Database::getInstance();
$conn = $db->getConnection();

// 페이징 설정
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$recordsPerPage = 10; // 페이지당 게시물 수
$offset = ($page - 1) * $recordsPerPage;

// 전체 공지사항 수 가져오기
$totalStmt = $conn->prepare("SELECT COUNT(*) FROM notices");
$totalStmt->execute();
$totalRecords = $totalStmt->fetchColumn();
$totalPages = ceil($totalRecords / $recordsPerPage);

// 현재 페이지의 공지사항 목록 가져오기
$stmt = $conn->prepare("SELECT n.id, n.title, u.nickname as author, n.created_at, n.view_count FROM notices n JOIN users u ON n.user_id = u.id ORDER BY n.created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $recordsPerPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('notice.list.title') ?> - <?= __('common.site_title') ?></title>
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/notice.css"> 
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
</head>
<body>
    <?php include_once '/var/www/html/topmkt/includes/header.php'; ?>

    <main class="notice-list-container">
        <div class="container">
            <h1 class="page-title"><?= __('notice.list.header') ?></h1>

            <?php if (empty($notices)): ?>
                <p class="no-notices"><?= __('notice.list.no_notices') ?></p>
            <?php else: ?>
                <table class="notice-table">
                    <thead>
                        <tr>
                            <th class="notice-id"><?= __('notice.list.table.id') ?></th>
                            <th class="notice-title"><?= __('notice.list.table.title') ?></th>
                            <th class="notice-author"><?= __('notice.list.table.author') ?></th>
                            <th class="notice-date"><?= __('notice.list.table.date') ?></th>
                            <th class="notice-views"><?= __('notice.list.table.views') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices as $notice): ?>
                            <tr>
                                <td class="notice-id"><?= htmlspecialchars($notice['id']) ?></td>
                                <td class="notice-title"><a href="/notice/view.php?id=<?= $notice['id'] ?>"><?= htmlspecialchars($notice['title']) ?></a></td>
                                <td class="notice-author"><?= htmlspecialchars($notice['author']) ?></td>
                                <td class="notice-date"><?= date('Y-m-d', strtotime($notice['created_at'])) ?></td>
                                <td class="notice-views"><?= htmlspecialchars($notice['view_count']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- 페이징 -->
                <nav class="pagination">
                    <ul>
                        <?php if ($page > 1): ?>
                            <li><a href="?page=<?= $page - 1 ?>"><?= __('common.pagination.prev') ?></a></li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="<?= ($i == $page) ? 'active' : '' ?>">
                                <a href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li><a href="?page=<?= $page + 1 ?>"><?= __('common.pagination.next') ?></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </main> <?php // </main> 태그를 footer.php include 직전에 위치시킴 ?>

    <?php include_once '/var/www/html/topmkt/includes/footer.php'; ?>
    <?php // <script src="/public/assets/js/main.js" defer></script> // 이 줄 삭제 ?>
<?php // </body> // 이 줄 삭제 ?>
<?php // </html> // 이 줄 삭제 ?> 