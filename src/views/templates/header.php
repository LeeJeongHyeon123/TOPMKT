<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' . (APP_NAME ?? '탑마케팅') : (APP_NAME ?? '탑마케팅') ?></title>
    <meta name="description" content="<?= $page_description ?? '글로벌 네트워크 마케팅 리더들의 커뮤니티' ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
    <header class="main-header modern-header">
        <div class="container">
            <div class="header-content">
                <!-- 로고 -->
                <div class="header-left">
                    <h1 class="logo">
                        <a href="/">
                            <div class="logo-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <span class="logo-text"><?= APP_NAME ?? '탑마케팅' ?></span>
                        </a>
                    </h1>
                </div>

                <!-- 메인 네비게이션 -->
                <nav class="main-nav" id="main-nav">
                    <ul class="nav-menu">
                        <li>
                            <a href="/" class="nav-link <?= ($current_page ?? '') === 'home' ? 'active' : '' ?>">
                                <i class="fas fa-home"></i>
                                <span>홈</span>
                            </a>
                        </li>
                        <li>
                            <a href="/posts" class="nav-link <?= ($current_page ?? '') === 'posts' ? 'active' : '' ?>">
                                <i class="fas fa-comments"></i>
                                <span>커뮤니티</span>
                            </a>
                        </li>
                        <li>
                            <a href="/events" class="nav-link <?= ($current_page ?? '') === 'events' ? 'active' : '' ?>">
                                <i class="fas fa-calendar-alt"></i>
                                <span>행사 일정</span>
                            </a>
                        </li>
                        <li>
                            <a href="/lectures" class="nav-link <?= ($current_page ?? '') === 'lectures' ? 'active' : '' ?>">
                                <i class="fas fa-graduation-cap"></i>
                                <span>강의 일정</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- 사용자 메뉴 -->
                <div class="header-right">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- 로그인된 사용자 -->
                        <div class="user-menu dropdown">
                            <button class="dropdown-toggle user-profile-btn">
                                <div class="user-avatar">
                                    <?php if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])): ?>
                                        <img src="/assets/uploads/profiles/<?= $_SESSION['profile_image'] ?>" alt="프로필">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <span class="user-name"><?= $_SESSION['username'] ?? '사용자' ?></span>
                                <i class="fas fa-chevron-down dropdown-arrow"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="/users/me" class="dropdown-item">
                                        <i class="fas fa-user"></i> 
                                        <span>내 프로필</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/posts/create" class="dropdown-item">
                                        <i class="fas fa-edit"></i> 
                                        <span>글쓰기</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="/users/settings" class="dropdown-item">
                                        <i class="fas fa-cog"></i> 
                                        <span>설정</span>
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <form action="/auth/logout" method="post" class="logout-form">
                                        <button type="submit" class="dropdown-item logout-btn">
                                            <i class="fas fa-sign-out-alt"></i> 
                                            <span>로그아웃</span>
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- 로그인되지 않은 사용자 -->
                        <div class="auth-buttons">
                            <a href="/auth/login" class="btn btn-primary-gradient">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>로그인</span>
                            </a>
                        </div>
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