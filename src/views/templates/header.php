<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <h1 class="logo"><a href="/"><?= APP_NAME ?></a></h1>
            <nav class="main-nav">
                <ul>
                    <li><a href="/">홈</a></li>
                    <li><a href="/posts">게시판</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/users/me">내 프로필</a></li>
                        <li>
                            <form action="/auth/logout" method="post" class="logout-form">
                                <button type="submit">로그아웃</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="/auth/login">로그인</a></li>
                        <li><a href="/auth/signup">회원가입</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?> 