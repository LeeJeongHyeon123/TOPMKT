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
                        <li><a href="/" class="<?= $current_page === 'home' ? 'active' : '' ?>">홈</a></li>
                        <li><a href="/community" class="<?= $current_page === 'community' ? 'active' : '' ?>">커뮤니티</a></li>
                        <li><a href="/education" class="<?= $current_page === 'education' ? 'active' : '' ?>">교육</a></li>
                        <li><a href="/tools" class="<?= $current_page === 'tools' ? 'active' : '' ?>">도구</a></li>
                        <li><a href="/about" class="<?= $current_page === 'about' ? 'active' : '' ?>">소개</a></li>
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
                                    <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
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
    .nav-auth {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .login-btn {
        color: white;
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
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .user-menu {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .user-menu:hover {
        background: rgba(255, 255, 255, 0.15);
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
        color: white;
        font-weight: bold;
        font-size: 14px;
    }
    
    .user-name {
        color: white;
        font-size: 14px;
        font-weight: 500;
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .user-menu i {
        color: rgba(255, 255, 255, 0.7);
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