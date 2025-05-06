<!DOCTYPE html>
<html lang="<?= $_SESSION['locale'] ?? 'ko' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($data['title']) ? $data['title'] : '탑마케팅(TOPMKT)' ?></title>
    
    <!-- 메타 태그 -->
    <meta name="description" content="탑마케팅(TOPMKT) - 마케팅 서비스">
    <meta name="keywords" content="마케팅, 광고, 서비스">
    
    <!-- CSS 파일 -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/responsive.css">
    
    <!-- 파비콘 -->
    <link rel="icon" href="<?= base_url() ?>/assets/images/logo/favicon.ico">
    
    <!-- 폰트 -->
    <link rel="stylesheet" href="<?= base_url() ?>/assets/fonts/font.css">
</head>
<body>
    <!-- 헤더 영역 -->
    <header class="site-header">
        <div class="container">
            <div class="logo">
                <a href="<?= base_url() ?>">
                    <img src="<?= base_url() ?>/assets/images/logo/logo.png" alt="탑마케팅 로고">
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="<?= base_url() ?>"><?= trans('nav.home') ?></a></li>
                    <li><a href="<?= base_url() ?>/about"><?= trans('nav.about') ?></a></li>
                    <li><a href="<?= base_url() ?>/services"><?= trans('nav.services') ?></a></li>
                    <li><a href="<?= base_url() ?>/contact"><?= trans('nav.contact') ?></a></li>
                </ul>
            </nav>
            
            <div class="user-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= base_url() ?>/profile" class="btn btn-sm"><?= trans('nav.profile') ?></a>
                    <a href="<?= base_url() ?>/logout" class="btn btn-sm btn-outline"><?= trans('nav.logout') ?></a>
                <?php else: ?>
                    <a href="<?= base_url() ?>/login" class="btn btn-sm"><?= trans('nav.login') ?></a>
                    <a href="<?= base_url() ?>/register" class="btn btn-sm btn-primary"><?= trans('nav.register') ?></a>
                <?php endif; ?>
                
                <!-- 언어 선택 -->
                <div class="language-selector">
                    <select id="language" onchange="changeLanguage(this.value)">
                        <option value="ko" <?= ($_SESSION['locale'] ?? 'ko') == 'ko' ? 'selected' : '' ?>>한국어</option>
                        <option value="en" <?= ($_SESSION['locale'] ?? 'ko') == 'en' ? 'selected' : '' ?>>English</option>
                        <option value="zh-CN" <?= ($_SESSION['locale'] ?? 'ko') == 'zh-CN' ? 'selected' : '' ?>>简体中文</option>
                        <option value="zh-TW" <?= ($_SESSION['locale'] ?? 'ko') == 'zh-TW' ? 'selected' : '' ?>>繁體中文</option>
                        <option value="ja" <?= ($_SESSION['locale'] ?? 'ko') == 'ja' ? 'selected' : '' ?>>日本語</option>
                    </select>
                </div>
            </div>
            
            <!-- 모바일 메뉴 토글 버튼 -->
            <button class="mobile-menu-toggle" aria-label="메뉴 열기">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    
    <!-- 메인 콘텐츠 영역 -->
    <main class="site-content">
        <div class="container"> 