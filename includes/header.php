<?php
// 세션 시작 (header 설정 전에 필요)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Content-Security-Policy 헤더 설정
header("Content-Security-Policy: default-src 'self'; frame-src 'self' https://www.google.com https://accounts.google.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://accounts.google.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' https://www.google.com https://accounts.google.com;");

// 현재 언어 설정 확인
$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko');

// 언어 변경 시 세션에 저장
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
?>
<header class="main-header">
    <div class="header-container">
        <div class="header-left">
            <a href="/" class="logo" style="text-decoration:none;">
                <span style="font-weight:700;font-size:1.7rem;color:#000;letter-spacing:1px;">TOPMKT</span>
            </a>
            <nav class="main-nav">
                <ul>
                    <li><a href="/#leaders">추천 리더</a></li>
                    <li><a href="/#vision">회사/비전 소개</a></li>
                    <li><a href="/#knowhow">노하우 공유</a></li>
                    <li><a href="/#recruiting">팀 리쿠르팅</a></li>
                    <li><a href="/#events">행사 일정</a></li>
                    <li><a href="/#lectures">강의 일정</a></li>
                    <li><a href="/#community">커뮤니티</a></li>
                    <li><a href="/#notice">공지사항</a></li>
                </ul>
            </nav>
        </div>
        <div class="header-right">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu">
                    <button class="user-menu-button">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? '/public/assets/images/default-profile.png'); ?>" 
                             alt="프로필" 
                             class="user-avatar">
                    </button>
                    <div class="user-dropdown">
                        <a href="/profile.php" class="dropdown-item">프로필</a>
                        <a href="/messages.php" class="dropdown-item">메시지</a>
                        <a href="/settings.php" class="dropdown-item">설정</a>
                        <a href="/logout.php" class="dropdown-item">로그아웃</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/auth.php" class="btn-login">로그인</a>
                </div>
            <?php endif; ?>
            <div class="language-selector">
                <button class="language-button">
                    <span class="flag">
                        <?php
                        $langFlags = [
                            'ko' => '🇰🇷',
                            'en' => '🇺🇸',
                            'zh' => '🇨🇳',
                            'ja' => '🇯🇵'
                        ];
                        echo $langFlags[$currentLang] ?? '🇰🇷';
                        ?>
                    </span>
                    <span class="lang-code"><?php echo strtolower($currentLang); ?></span>
                </button>
                <div class="language-dropdown">
                    <a href="?lang=ko" class="language-option<?php echo $currentLang === 'ko' ? ' active' : ''; ?>"><span class="flag">🇰🇷</span> <span class="lang-code">ko</span></a>
                    <a href="?lang=en" class="language-option<?php echo $currentLang === 'en' ? ' active' : ''; ?>"><span class="flag">🇺🇸</span> <span class="lang-code">en</span></a>
                    <a href="?lang=zh" class="language-option<?php echo $currentLang === 'zh' ? ' active' : ''; ?>"><span class="flag">🇨🇳</span> <span class="lang-code">zh</span></a>
                    <a href="?lang=ja" class="language-option<?php echo $currentLang === 'ja' ? ' active' : ''; ?>"><span class="flag">🇯🇵</span> <span class="lang-code">ja</span></a>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const langBtn = document.querySelector('.language-button');
    const langDropdown = document.querySelector('.language-dropdown');
    if (!langBtn || !langDropdown) return;

    // 드롭다운 토글
    langBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        langDropdown.classList.toggle('show');
    });

    // 바깥 클릭 시 닫기
    document.addEventListener('click', function(e) {
        if (!langDropdown.contains(e.target) && !langBtn.contains(e.target)) {
            langDropdown.classList.remove('show');
        }
    });

    // ESC로 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            langDropdown.classList.remove('show');
        }
    });

    // 언어 선택 시 닫기
    langDropdown.querySelectorAll('a').forEach(function(a) {
        a.addEventListener('click', function() {
            langDropdown.classList.remove('show');
        });
    });
});
</script> 