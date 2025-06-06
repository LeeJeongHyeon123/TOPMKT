<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . (APP_NAME ?? '탑마케팅') : (APP_NAME ?? '탑마케팅') ?></title>
    <meta name="description" content="<?= $page_description ?? '글로벌 네트워크 마케팅 리더들의 커뮤니티' ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/loading.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="/assets/js/loading.js"></script>
    <script src="/assets/js/main.js" defer></script>
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
                        <li><a href="/education" class="<?= ($pageSection ?? '') === 'education' ? 'active' : '' ?>">교육</a></li>
                        <li><a href="/tools" class="<?= ($pageSection ?? '') === 'tools' ? 'active' : '' ?>">도구</a></li>
                        <li><a href="/about" class="<?= ($pageSection ?? '') === 'about' ? 'active' : '' ?>">소개</a></li>
                    </ul>
                </nav>

                <!-- 로그인 상태별 우측 메뉴 -->
                <div class="nav-auth">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- 로그인된 사용자 메뉴 -->
                        <div class="user-menu">
                            <div class="user-avatar">
                                <img src="/assets/images/default-avatar.png" alt="프로필" 
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
                                        <span class="user-role"><?= ucfirst(strtolower($_SESSION['user_role'] ?? 'GENERAL')) ?> 멤버</span>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="/profile" class="dropdown-item">
                                    <i class="fas fa-user"></i>
                                    <span>프로필</span>
                                </a>
                                <a href="/dashboard" class="dropdown-item">
                                    <i class="fas fa-chart-pie"></i>
                                    <span>대시보드</span>
                                </a>
                                <a href="/messages" class="dropdown-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>메시지</span>
                                    <span class="notification-badge">3</span>
                                </a>
                                <a href="/settings" class="dropdown-item">
                                    <i class="fas fa-cog"></i>
                                    <span>설정</span>
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
    </main>

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
        position: absolute;
        top: calc(100% + 10px);
        right: 0;
        min-width: 200px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 1000;
        border: 1px solid #e5e7eb;
    }
    
    .user-menu.active .user-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
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
    
    .user-role {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
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
    document.addEventListener('DOMContentLoaded', function() {
        // 사용자 메뉴 드롭다운 토글
        const userMenu = document.querySelector('.user-menu');
        const userDropdown = document.querySelector('.user-dropdown');
        
        if (userMenu && userDropdown) {
            userMenu.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                this.classList.toggle('active');
            });
            
            // 외부 클릭 시 드롭다운 닫기
            document.addEventListener('click', function() {
                userMenu.classList.remove('active');
            });
            
            // 드롭다운 내부 클릭 시 이벤트 버블링 방지
            userDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // 로그아웃 확인
        window.confirmLogout = function() {
            return confirm('정말 로그아웃하시겠습니까?');
        };
    });
    </script>
</body>
</html> 