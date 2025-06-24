<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . (APP_NAME ?? '탑마케팅') : (APP_NAME ?? '탑마케팅') ?></title>
    <meta name="description" content="<?= $page_description ?? '글로벌 네트워크 마케팅 리더들의 커뮤니티' ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="<?= $og_type ?? 'website' ?>">
    <meta property="og:url" content="<?= 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:title" content="<?= $og_title ?? ($page_title ? $page_title . ' - 탑마케팅' : '탑마케팅 - 마케팅 전문가들의 지식 공유 플랫폼') ?>">
    <meta property="og:description" content="<?= $og_description ?? ($page_description ?? '마케팅 전문가들이 모여 지식을 공유하고 함께 성장하는 플랫폼입니다. 세미나, 워크샵, 커뮤니티를 통해 최신 마케팅 트렌드를 만나보세요.') ?>">
    <meta property="og:image" content="<?= $og_image ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/topmkt-og-image.png?v=' . date('Ymd') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="탑마케팅">
    <meta property="og:locale" content="ko_KR">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
    <meta property="twitter:title" content="<?= $og_title ?? ($page_title ? $page_title . ' - 탑마케팅' : '탑마케팅 - 마케팅 전문가들의 지식 공유 플랫폼') ?>">
    <meta property="twitter:description" content="<?= $og_description ?? ($page_description ?? '마케팅 전문가들이 모여 지식을 공유하고 함께 성장하는 플랫폼입니다. 세미나, 워크샵, 커뮤니티를 통해 최신 마케팅 트렌드를 만나보세요.') ?>">
    <meta property="twitter:image" content="<?= $og_image ?? 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/topmkt-og-image.png?v=' . date('Ymd') ?>">
    
    <!-- 추가 메타 태그 -->
    <meta name="keywords" content="<?= $keywords ?? '마케팅, 네트워크 마케팅, 세미나, 워크샵, 커뮤니티, 마케팅 교육, 온라인 강의, 탑마케팅, TopMKT, 비즈니스 매칭, 마케팅 플랫폼' ?>">
    <meta name="author" content="(주)윈카드">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="theme-color" content="#6366f1">
    <meta name="msapplication-navbutton-color" content="#6366f1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <?php if (isset($_SESSION['user_id'])): ?>
    <meta name="user-id" content="<?= $_SESSION['user_id'] ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?= 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">
    
    <!-- 파비콘 - 모든 페이지 통일 -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico?v=20250609">
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg?v=20250609">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png?v=20250609">
    <link rel="shortcut icon" href="/favicon.ico?v=20250609">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/loading.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- 레이지 로딩 스타일 -->
    <?php
    if (file_exists(SRC_PATH . '/helpers/LazyLoadHelper.php')) {
        require_once SRC_PATH . '/helpers/LazyLoadHelper.php';
        echo LazyLoadHelper::getStyles();
    }
    ?>
    
    <!-- JavaScript -->
    <script src="/assets/js/loading.js"></script>
    <script src="/assets/js/main.js" defer></script>
    
    <!-- Firebase SDK (채팅 알림용) -->
    <?php if (isset($_SESSION['user_id']) && $_SERVER['REQUEST_URI'] !== '/chat'): ?>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-database-compat.js"></script>
    <script src="/assets/js/chat-notifications.js"></script>
    <?php endif; ?>
    
    <!-- 구조화 데이터 (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "탑마케팅",
        "alternateName": "TopMKT",
        "url": "https://www.topmktx.com",
        "description": "글로벌 네트워크 마케팅 리더들의 커뮤니티 플랫폼",
        "publisher": {
            "@type": "Organization",
            "name": "(주)윈카드",
            "logo": {
                "@type": "ImageObject",
                "url": "https://www.topmktx.com/assets/images/logo.png"
            }
        },
        "potentialAction": {
            "@type": "SearchAction",
            "target": "https://www.topmktx.com/community?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <?php if (isset($structured_data)): ?>
    <script type="application/ld+json">
    <?= json_encode($structured_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
    </script>
    <?php endif; ?>
</head>
<body>
    <header class="main-header modern-header">
        <div class="container">
            <div class="header-content">
                <!-- 로고 -->
                <div class="header-left">
                    <h1 class="logo">
                        <a href="/" class="logo-link">
                            <div class="logo-icon">
                                <i class="fas fa-rocket header-rocket"></i>
                            </div>
                            <span class="logo-text"><?= APP_NAME ?? '탑마케팅' ?></span>
                        </a>
                    </h1>
                </div>

                <!-- 메인 네비게이션 -->
                <nav class="main-nav" id="main-nav">
                    <ul class="nav-menu">
                        <li><a href="/" class="<?= ($pageSection ?? '') === 'home' ? 'active' : '' ?>">홈</a></li>
                        <li><a href="/community" class="<?= ($pageSection ?? '') === 'community' ? 'active' : '' ?>">커뮤니티</a></li>
                        <li><a href="/lectures" class="<?= ($pageSection ?? '') === 'lectures' ? 'active' : '' ?>">강의 일정</a></li>
                        <li><a href="/events" class="<?= ($pageSection ?? '') === 'events' ? 'active' : '' ?>">행사 일정</a></li>
                    </ul>
                </nav>

                <!-- 로그인 상태별 우측 메뉴 -->
                <div class="nav-auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- 로그인된 사용자 메뉴 -->
                        <div class="user-menu">
                            <div class="user-avatar">
                                <?php 
                                require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
                                $profileImage = AuthMiddleware::getCurrentUserProfileImage();
                                $defaultImage = '/assets/images/default-avatar.png';
                                $imageUrl = $profileImage ? $profileImage : $defaultImage;
                                ?>
                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="프로필" 
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-fallback">
                                    👤
                                </div>
                            </div>
                            <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? '사용자') ?></span>
                            <i class="fas fa-chevron-down"></i>
                            
                            <div class="user-dropdown">
                                <div class="dropdown-header">
                                    <div class="user-info">
                                        <span class="user-display-name"><?= htmlspecialchars($_SESSION['username'] ?? '사용자') ?></span>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="/profile" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>프로필</span>
                                </a>
                                <a href="/chat" class="dropdown-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>채팅</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="/auth/logout" class="dropdown-item logout-item" onclick="return confirmLogout()">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <span>로그아웃</span>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- 비로그인 사용자 메뉴 -->
                        <a href="/auth/login" class="nav-link login-btn">
                            <i class="fas fa-sign-in-alt"></i>
                            로그인
                        </a>
                        <a href="/auth/signup" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            회원가입
                        </a>
                    <?php endif; ?>
                </div>

                <!-- 모바일 메뉴 토글 -->
                <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </div>
        </div>

        <!-- 모바일 메뉴 오버레이 -->
        <div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>
    </header>
    
    <main class="main-content">
        <!-- 알림 메시지 -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <span><?= $_SESSION['error'] ?></span>
                    <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <div class="alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="alert-content">
                    <span><?= $_SESSION['success'] ?></span>
                    <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <!-- 페이지 컨텐츠 시작 -->

    <!-- 사용자 메뉴 스타일 -->
    <style>
    
    /* 🚀 헤더 로켓 애니메이션 */
    .header-rocket {
        display: inline-block;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: center bottom;
        position: relative;
        color: #3b82f6;
        font-size: 1.8rem;
    }
    
    /* 페이지 로딩 시 로켓 착륙 애니메이션 */
    .header-rocket {
        animation: rocketLanding 2.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards,
                   headerRocketFloat 4s ease-in-out infinite 2.5s;
    }
    
    @keyframes rocketLanding {
        0% {
            transform: translateX(-150vw) translateY(-50vh) rotate(-45deg) scale(0.3);
            opacity: 0;
            filter: blur(3px);
        }
        20% {
            opacity: 0.3;
            filter: blur(2px);
        }
        40% {
            transform: translateX(-80vw) translateY(-20vh) rotate(-30deg) scale(0.5);
            opacity: 0.6;
            filter: blur(1px);
        }
        60% {
            transform: translateX(-20vw) translateY(-5vh) rotate(-15deg) scale(0.8);
            opacity: 0.8;
            filter: blur(0.5px);
        }
        80% {
            transform: translateX(-5vw) translateY(-1vh) rotate(-5deg) scale(0.95);
            opacity: 0.9;
            filter: blur(0px);
        }
        90% {
            transform: translateX(0) translateY(2px) rotate(5deg) scale(1.1);
            opacity: 1;
        }
        95% {
            transform: translateX(0) translateY(-2px) rotate(-2deg) scale(1.05);
        }
        100% {
            transform: translateX(0) translateY(0) rotate(0deg) scale(1);
            opacity: 1;
            filter: blur(0px);
        }
    }
    
    /* 기본 헤더 로켓 애니메이션 - 우주에서 떠다니는 느낌 (착륙 후) */
    
    @keyframes headerRocketFloat {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        20% {
            transform: translateY(-2px) rotate(3deg);
        }
        40% {
            transform: translateY(-4px) rotate(0deg);
        }
        60% {
            transform: translateY(-2px) rotate(-3deg);
        }
        80% {
            transform: translateY(-1px) rotate(1deg);
        }
    }
    
    /* 로고 링크 호버 시 로켓 특수 효과 */
    .logo-link {
        position: relative;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    /* 반짝이는 라인선 제거됨 */
    
    /* 착륙 시 추진 효과 */
    .logo-icon::before {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        width: 0;
        height: 0;
        background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, #ef4444, transparent);
        transform: translateX(-50%);
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .logo-icon::after {
        content: '💨';
        position: absolute;
        left: -35px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        font-size: 0.8rem;
        animation: landingSmoke 2.5s ease-out;
    }
    
    /* 착륙 추진 효과 애니메이션 */
    @keyframes landingThruster {
        0%, 70% {
            width: 0;
            height: 0;
            opacity: 0;
        }
        75% {
            width: 30px;
            height: 3px;
            opacity: 0.8;
            box-shadow: 0 0 10px #fbbf24, 0 0 20px #f59e0b;
        }
        85% {
            width: 40px;
            height: 5px;
            opacity: 1;
            box-shadow: 0 0 15px #fbbf24, 0 0 30px #f59e0b, 0 0 45px #ef4444;
        }
        95% {
            width: 20px;
            height: 2px;
            opacity: 0.5;
        }
        100% {
            width: 0;
            height: 0;
            opacity: 0;
        }
    }
    
    /* 착륙 연기 효과 */
    @keyframes landingSmoke {
        0%, 60% {
            opacity: 0;
            left: -35px;
        }
        70% {
            opacity: 0.8;
            left: -25px;
            content: '💨';
        }
        80% {
            opacity: 1;
            left: -20px;
            content: '💨💨';
        }
        90% {
            opacity: 0.6;
            left: -15px;
            content: '💨💨💨';
        }
        100% {
            opacity: 0;
            left: -10px;
        }
    }
    
    /* 착륙 시 로고 아이콘에 추진 효과 적용 */
    .logo-icon {
        position: relative;
        overflow: visible;
    }
    
    .logo-icon::before {
        animation: landingThruster 2.5s ease-out;
    }
    
    /* 착륙 완료 시 충격파 효과 */
    @keyframes landingShockwave {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
        }
        25% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0.5);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 20px rgba(59, 130, 246, 0.3);
        }
        75% {
            transform: scale(1.02);
            box-shadow: 0 0 0 30px rgba(59, 130, 246, 0.1);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 40px rgba(59, 130, 246, 0);
        }
    }
    
    /* 착륙 완료 시 로고 아이콘에 충격파 적용 */
    .logo-icon {
        animation: landingShockwave 1s ease-out 2.3s;
    }
    
    /* 호버 시 로켓 엔진 점화! */
    .logo-link:hover .header-rocket {
        animation: headerRocketIgnition 0.8s ease-in-out;
        transform: translateY(-3px) rotate(-8deg) scale(1.1);
        color: #1d4ed8;
        filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.4));
    }
    
    @keyframes headerRocketIgnition {
        0% {
            transform: translateY(0px) rotate(0deg) scale(1);
        }
        30% {
            transform: translateY(-1px) rotate(-4deg) scale(1.05);
        }
        60% {
            transform: translateY(-2px) rotate(-6deg) scale(1.08);
        }
        100% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
        }
    }
    
    /* 호버 시 반짝이는 효과 제거됨 */
    
    /* 클릭 시 로켓 발사! */
    .logo-link:active .header-rocket {
        animation: headerRocketLaunch 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        transform: translateY(-8px) rotate(-20deg) scale(1.15);
    }
    
    @keyframes headerRocketLaunch {
        0% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
        }
        40% {
            transform: translateY(-5px) rotate(-15deg) scale(1.12);
        }
        70% {
            transform: translateY(-10px) rotate(-18deg) scale(1.18);
        }
        100% {
            transform: translateY(-8px) rotate(-20deg) scale(1.15);
        }
    }
    
    /* 로고 텍스트 호버 효과 */
    .logo-text {
        transition: all 0.3s ease;
        color: #1f2937;
        font-weight: 700;
        font-size: 1.5rem;
        opacity: 0;
        animation: logoTextAppear 2.8s ease-out forwards;
    }
    
    @keyframes logoTextAppear {
        0%, 60% {
            opacity: 0;
            transform: translateY(10px);
        }
        70% {
            opacity: 0.3;
            transform: translateY(5px);
        }
        80% {
            opacity: 0.6;
            transform: translateY(2px);
        }
        90% {
            opacity: 0.8;
            transform: translateY(-1px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .logo-link:hover .logo-text {
        color: #1d4ed8;
        text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
    }
    
    /* 로고 아이콘 컨테이너 */
    .logo-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(29, 78, 216, 0.05));
        transition: all 0.3s ease;
        margin-right: 12px;
        position: relative;
        overflow: visible;
        opacity: 0;
        animation: logoIconAppear 2.6s ease-out forwards,
                   landingShockwave 1s ease-out 2.3s;
    }
    
    @keyframes logoIconAppear {
        0%, 50% {
            opacity: 0;
            transform: scale(0.8);
        }
        70% {
            opacity: 0.5;
            transform: scale(0.9);
        }
        85% {
            opacity: 0.8;
            transform: scale(1.05);
        }
        100% {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .logo-link:hover .logo-icon {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(29, 78, 216, 0.1));
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
    }
    
    /* 모바일 반응형 */
    @media (max-width: 768px) {
        .header-rocket {
            font-size: 1.5rem;
        }
        
        .logo-text {
            font-size: 1.3rem;
        }
        
        .logo-icon {
            width: 35px;
            height: 35px;
            margin-right: 8px;
        }
        
        .logo-link::after {
            right: -20px;
            font-size: 0.6rem;
        }
    }
    
    /* 헤더 레이아웃 개선 */
    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 15px 0;
    }
    
    .header-left {
        flex: 0 0 auto;
    }
    
    .main-nav {
        flex: 1;
        display: flex;
        justify-content: center;
        margin: 0 40px;
    }
    
    .nav-auth {
        flex: 0 0 auto;
    }
    
    /* 메인 네비게이션 메뉴 스타일 수정 */
    .nav-menu a {
        color: #374151 !important;
        text-decoration: none;
        padding: 10px 15px;
        border-radius: 4px;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 16px;
    }
    
    .nav-menu a:hover {
        background-color: rgba(59, 130, 246, 0.1);
        color: #1d4ed8 !important;
        text-decoration: none;
    }
    
    .nav-menu a.active {
        background-color: rgba(59, 130, 246, 0.15);
        color: #1d4ed8 !important;
    }
    
    .nav-auth {
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .login-btn {
        color: #374151;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 6px;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }
    
    .login-btn:hover {
        background-color: rgba(59, 130, 246, 0.1);
        color: #1d4ed8;
    }
    
    .user-menu {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: rgba(0, 0, 0, 0.05);
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .user-menu:hover {
        background: rgba(0, 0, 0, 0.08);
    }
    
    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        overflow: hidden;
        position: relative;
    }
    
    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-fallback {
        width: 100%;
        height: 100%;
        background: #667eea;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .user-name {
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .user-menu i {
        color: rgba(0, 0, 0, 0.5);
        font-size: 12px;
        transition: transform 0.3s ease;
    }
    
    .user-menu.active i {
        transform: rotate(180deg);
    }
    
    /* 드롭다운 메뉴 */
    .user-dropdown {
        position: absolute !important;
        top: calc(100% + 10px) !important;
        right: 0 !important;
        min-width: 200px !important;
        background: white !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        transition: all 0.3s ease !important;
        z-index: 1000 !important;
        border: 1px solid #e5e7eb !important;
        display: block !important;
        pointer-events: none !important;
    }
    
    .user-menu.active .user-dropdown {
        opacity: 0 !important;
        visibility: hidden !important;
        transform: translateY(-10px) !important;
        display: none !important;
        z-index: 1000 !important;
        pointer-events: none !important;
    }
    
    .dropdown-header {
        padding: 15px;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .user-display-name {
        display: block;
        font-weight: 600;
        color: #1f2937;
        font-size: 14px;
    }
    
    .dropdown-divider {
        height: 1px;
        background: #f3f4f6;
        margin: 0;
    }
    
    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: #374151;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.2s ease;
        position: relative;
    }
    
    .dropdown-item:hover {
        background-color: #f9fafb;
    }
    
    .dropdown-item i {
        width: 16px;
        color: #6b7280;
    }
    
    .logout-item {
        color: #dc2626;
    }
    
    .logout-item:hover {
        background-color: #fef2f2;
    }
    
    .logout-item i {
        color: #dc2626;
    }
    
    .notification-badge {
        background: #ef4444;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: auto;
        font-weight: bold;
        min-width: 16px;
        text-align: center;
        position: static;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    /* 반응형 */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
            padding: 10px 0;
        }
        
        .main-nav {
            margin: 0;
            order: 2;
        }
        
        .nav-auth {
            order: 3;
        }
        
        .user-name {
            display: none;
        }
        
        .user-dropdown {
            min-width: 180px;
        }
        
        .nav-auth {
            gap: 10px;
        }
        
        .login-btn {
            padding: 6px 12px;
            font-size: 13px;
        }
    }

    /* 모바일에서 드롭다운 위치 조정 */
    @media (max-width: 480px) {
        .user-dropdown {
            right: -10px;
            min-width: 160px;
        }
    }
    </style>

    <script>
    <?php
    // 관리자 권한 확인을 위한 JavaScript 변수 설정
    require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
    $isAdmin = AuthMiddleware::isAdmin();
    ?>
    
    // 관리자 여부를 JavaScript 변수로 전달
    const isAdmin = <?= $isAdmin ? 'true' : 'false' ?>;
    
    document.addEventListener('DOMContentLoaded', function() {
        // 사용자 메뉴 드롭다운 토글
        const userMenu = document.querySelector('.user-menu');
        
        if (userMenu) {
            // 클릭으로 드롭다운 토글
            userMenu.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // 기존 드롭다운 확인
                let existingDropdown = document.getElementById('floating-user-dropdown');
                
                if (existingDropdown) {
                    // 드롭다운이 이미 있으면 제거
                    existingDropdown.remove();
                    this.classList.remove('active');
                } else {
                    // 새 드롭다운 생성
                    this.classList.add('active');
                    const rect = this.getBoundingClientRect();
                    
                    // 현재 읽지 않은 메시지 수 가져오기
                    const currentBadge = document.getElementById('chatNotificationBadge');
                    const unreadCount = currentBadge ? parseInt(currentBadge.textContent) || 0 : 0;
                    const badgeHtml = unreadCount > 0 ? `<span class="notification-badge dropdown-chat-badge" style="background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: auto; font-weight: bold; min-width: 16px; text-align: center;">${unreadCount}</span>` : '';
                    
                    // 관리자 메뉴 HTML 생성
                    const adminMenuHtml = isAdmin ? `
                        <a href="/admin" class="dropdown-item admin-item">
                            <i class="fas fa-cog"></i>
                            <span>관리자 페이지</span>
                        </a>
                        <div class="dropdown-divider"></div>` : '';
                    
                    const floatingDropdown = document.createElement('div');
                    floatingDropdown.id = 'floating-user-dropdown';
                    floatingDropdown.innerHTML = `
                        <div class="dropdown-header">
                            <div class="user-info">
                                <span class="user-display-name"><?= htmlspecialchars($_SESSION['username'] ?? '사용자') ?></span>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="/profile" class="dropdown-item">
                            <i class="fas fa-user"></i>
                            <span>프로필</span>
                        </a>
                        <a href="/chat" class="dropdown-item">
                            <i class="fas fa-envelope"></i>
                            <span>채팅</span>
                            ${badgeHtml}
                        </a>
                        ${adminMenuHtml}
                        <div class="dropdown-divider"></div>
                        <a href="/auth/logout" class="dropdown-item logout-item" onclick="return confirmLogout()">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>로그아웃</span>
                        </a>
                    `;
                    
                    floatingDropdown.style.cssText = `
                        position: fixed !important;
                        top: ${rect.bottom + 10}px !important;
                        right: ${window.innerWidth - rect.right}px !important;
                        width: 200px !important;
                        background: white !important;
                        border-radius: 8px !important;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
                        z-index: 999999 !important;
                        border: 1px solid #e5e7eb !important;
                        display: block !important;
                        opacity: 1 !important;
                        visibility: visible !important;
                        padding: 0 !important;
                        margin: 0 !important;
                        font-family: 'Noto Sans KR', sans-serif !important;
                        color: #374151 !important;
                    `;
                    
                    // 드롭다운 내부 요소들에 대한 스타일 적용
                    floatingDropdown.querySelectorAll('.dropdown-header').forEach(el => {
                        el.style.cssText = 'padding: 15px; border-bottom: 1px solid #f3f4f6;';
                    });
                    floatingDropdown.querySelectorAll('.user-display-name').forEach(el => {
                        el.style.cssText = 'display: block; font-weight: 600; color: #1f2937; font-size: 14px;';
                    });

                    floatingDropdown.querySelectorAll('.dropdown-divider').forEach(el => {
                        el.style.cssText = 'height: 1px; background: #f3f4f6; margin: 0;';
                    });
                    floatingDropdown.querySelectorAll('.dropdown-item').forEach(el => {
                        el.style.cssText = 'display: flex; align-items: center; gap: 12px; padding: 12px 15px; color: #374151; text-decoration: none; font-size: 14px; transition: background-color 0.2s ease;';
                        el.addEventListener('mouseenter', () => el.style.backgroundColor = '#f9fafb');
                        el.addEventListener('mouseleave', () => el.style.backgroundColor = 'transparent');
                    });
                    floatingDropdown.querySelectorAll('.logout-item').forEach(el => {
                        el.style.color = '#dc2626';
                        el.addEventListener('mouseenter', () => el.style.backgroundColor = '#fef2f2');
                        el.addEventListener('mouseleave', () => el.style.backgroundColor = 'transparent');
                    });
                    
                    // 관리자 메뉴 아이템 스타일
                    floatingDropdown.querySelectorAll('.admin-item').forEach(el => {
                        el.style.color = '#7c3aed';
                        el.addEventListener('mouseenter', () => el.style.backgroundColor = '#f3f0ff');
                        el.addEventListener('mouseleave', () => el.style.backgroundColor = 'transparent');
                        
                        // 관리자 아이콘 스타일
                        const icon = el.querySelector('i');
                        if (icon) {
                            icon.style.color = '#7c3aed';
                        }
                    });
                    floatingDropdown.querySelectorAll('.notification-badge').forEach(el => {
                        el.style.cssText = 'background: #ef4444; color: white; font-size: 10px; padding: 2px 6px; border-radius: 10px; margin-left: auto; font-weight: bold; min-width: 16px; text-align: center;';
                    });
                    
                    document.body.appendChild(floatingDropdown);
                }
                
            });
            
            // 호버 이벤트 제거 - 클릭만 사용
            
            // 외부 클릭 시 드롭다운 닫기
            document.addEventListener('click', function(e) {
                const existingDropdown = document.getElementById('floating-user-dropdown');
                if (existingDropdown && !userMenu.contains(e.target) && !existingDropdown.contains(e.target)) {
                    existingDropdown.remove();
                    userMenu.classList.remove('active');
                }
            });
            
            // ESC 키로 드롭다운 닫기
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const existingDropdown = document.getElementById('floating-user-dropdown');
                    if (existingDropdown) {
                        existingDropdown.remove();
                        userMenu.classList.remove('active');
                    }
                }
            });
        } else {
        }
        
        // 로그아웃 확인
        window.confirmLogout = function() {
            return confirm('정말 로그아웃하시겠습니까?');
        };
        
        // 전역 에러 핸들러 (브라우저 확장 프로그램 에러 방지)
        window.addEventListener('error', function(e) {
            // 브라우저 확장 프로그램 관련 에러는 무시
            if (e.message && e.message.includes('message channel closed')) {
                e.preventDefault();
                return false;
            }
            if (e.message && e.message.includes('asynchronous response')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Promise rejection 에러 핸들러
        window.addEventListener('unhandledrejection', function(e) {
            // 브라우저 확장 프로그램 관련 에러는 무시
            if (e.reason && e.reason.message && 
                (e.reason.message.includes('message channel closed') || 
                 e.reason.message.includes('asynchronous response'))) {
                e.preventDefault();
                return false;
            }
        });
    });
    </script> 