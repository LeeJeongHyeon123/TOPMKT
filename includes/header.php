<?php
// 현재 언어 설정 확인
$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko');

// 언어 변경 시 세션에 저장
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

header("Content-Security-Policy: default-src 'self'; frame-src 'self' https://www.google.com https://accounts.google.com; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://accounts.google.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' https://www.google.com https://accounts.google.com;");
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>탑마케팅 - 전 세계 네트워크 마케팅 리더들이 모이는 No.1 커뮤니티</title>
    
    <!-- 파비콘 -->
    <link rel="icon" type="image/x-icon" href="/resources/images/favicon.ico">
    
    <!-- 웹 폰트 로드 (서버에 저장된 폰트 사용) -->
    <link rel="stylesheet" href="/resources/fonts/noto-sans-kr.css">
    
    <!-- CSS 파일 로드 -->
    <link rel="stylesheet" href="/resources/css/main.css">
    <link rel="stylesheet" href="/resources/css/auth.css">
    
    <!-- reCAPTCHA Enterprise -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
    
    <!-- 실시간 메시지 업데이트 -->
    <?php if (isset($_SESSION['user_id']) && !strpos($_SERVER['REQUEST_URI'], '/auth')): ?>
    <script>
        // DOM이 완전히 로드된 후 실행
        window.addEventListener('load', function() {
            const badge = document.getElementById('unreadMessageCount');
            if (!badge) return; // 배지 요소가 없으면 종료

            let updateTimeout;
            let isUpdating = false;

            function updateUnreadCount() {
                if (isUpdating) return;
                isUpdating = true;

                fetch('/api/messages/unread-count.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            badge.textContent = data.count > 99 ? '99+' : data.count;
                            badge.classList.add('show');
                        } else {
                            badge.textContent = '';
                            badge.classList.remove('show');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        isUpdating = false;
                    });
            }

            // 초기 카운트 업데이트
            updateUnreadCount();

            // 30초마다 카운트 업데이트
            function scheduleUpdate() {
                updateTimeout = setTimeout(() => {
                    updateUnreadCount();
                    scheduleUpdate();
                }, 30000);
            }
            scheduleUpdate();

            // 페이지 언로드 시 타이머 정리
            window.addEventListener('beforeunload', function() {
                if (updateTimeout) {
                    clearTimeout(updateTimeout);
                }
            });
        });
    </script>
    <?php endif; ?>

    <!-- Firebase 초기화 -->
    <script>
    // Firebase 초기화 상태 추적
    let firebaseInitialized = false;
    let pendingAuthCallbacks = [];

    // Firebase 초기화 함수
    function initializeFirebase() {
        if (firebaseInitialized) return Promise.resolve();
        
        return new Promise((resolve, reject) => {
            try {
                const firebaseConfig = {
                    // Firebase 설정
                    apiKey: "AIzaSyDXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
                    authDomain: "topmkt.firebaseapp.com",
                    projectId: "topmkt",
                    storageBucket: "topmkt.appspot.com",
                    messagingSenderId: "XXXXXXXXXXXX",
                    appId: "1:XXXXXXXXXXXX:web:XXXXXXXXXXXXXXXX"
                };

                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                }
                
                firebaseInitialized = true;
                resolve();
            } catch (error) {
                console.error('Firebase 초기화 오류:', error);
                reject(error);
            }
        });
    }

    // 인증 상태 변경 리스너 설정
    function setupAuthStateListener() {
        if (!firebaseInitialized) {
            pendingAuthCallbacks.push(setupAuthStateListener);
            return;
        }

        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                // 사용자가 로그인한 경우
                console.log('사용자 로그인됨:', user.uid);
            } else {
                // 사용자가 로그아웃한 경우
                console.log('사용자 로그아웃됨');
            }
        });
    }

    // 페이지 로드 시 Firebase 초기화
    document.addEventListener('DOMContentLoaded', function() {
        initializeFirebase()
            .then(() => {
                // 대기 중인 콜백 실행
                pendingAuthCallbacks.forEach(callback => callback());
                pendingAuthCallbacks = [];
            })
            .catch(error => {
                console.error('Firebase 초기화 실패:', error);
            });
    });

    // 페이지 언로드 시 정리
    window.addEventListener('beforeunload', function() {
        if (firebaseInitialized) {
            // 필요한 정리 작업 수행
            firebase.auth().signOut().catch(console.error);
        }
    });
    </script>
</head>
<body>
    <!-- 헤더 -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/">
                    <span class="logo-text">탑마케팅</span>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="/vision"><?= __('menu.vision', [], $currentLang) ?></a></li>
                    <li><a href="/knowhow"><?= __('menu.knowhow', [], $currentLang) ?></a></li>
                    <li><a href="/recruiting"><?= __('menu.recruiting', [], $currentLang) ?></a></li>
                    <li><a href="/events"><?= __('menu.events', [], $currentLang) ?></a></li>
                    <li><a href="/lecture"><?= __('menu.lecture', [], $currentLang) ?></a></li>
                    <li><a href="/community"><?= __('menu.community', [], $currentLang) ?></a></li>
                    <li><a href="/notice"><?= __('menu.notice', [], $currentLang) ?></a></li>
                </ul>
            </nav>

            <div class="header-right">
                <a href="<?php echo isset($_SESSION['user_id']) ? '/mypage' : '/auth?redirect=mypage'; ?>" class="nav-mypage">
                    <span class="nav-icon">👤</span><?= __('menu.mypage', [], $currentLang) ?>
                </a>
                <a href="<?php echo isset($_SESSION['user_id']) ? '/chat' : '/auth?redirect=chat'; ?>" class="nav-chat">
                    <span class="nav-icon">💬</span><?= __('menu.messages', [], $currentLang) ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="unread-badge" id="unreadMessageCount"></span>
                    <?php endif; ?>
                </a>
                <div class="language-selector">
                    <button class="language-btn" onclick="toggleLanguageDropdown()">
                        <span class="current-lang">
                            <?php
                            $langFlags = [
                                'ko' => '🇰🇷',
                                'en' => '🇺🇸',
                                'zh' => '🇨🇳',
                                'ja' => '🇯🇵',
                                'vi' => '🇻🇳',
                                'th' => '🇹🇭'
                            ];
                            $langNames = [
                                'ko' => '한국어',
                                'en' => 'English',
                                'zh' => '中文',
                                'ja' => '日本語',
                                'vi' => 'Tiếng Việt',
                                'th' => 'ไทย'
                            ];
                            echo $langFlags[$currentLang] . ' ' . $langNames[$currentLang];
                            ?>
                        </span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="language-dropdown" id="languageDropdown">
                        <a href="?lang=ko" class="language-option <?= $currentLang === 'ko' ? 'active' : '' ?>">
                            <span class="flag">🇰🇷</span> 한국어
                        </a>
                        <a href="?lang=en" class="language-option <?= $currentLang === 'en' ? 'active' : '' ?>">
                            <span class="flag">🇺🇸</span> English
                        </a>
                        <a href="?lang=zh" class="language-option <?= $currentLang === 'zh' ? 'active' : '' ?>">
                            <span class="flag">🇨🇳</span> 中文
                        </a>
                        <a href="?lang=ja" class="language-option <?= $currentLang === 'ja' ? 'active' : '' ?>">
                            <span class="flag">🇯🇵</span> 日本語
                        </a>
                        <a href="?lang=vi" class="language-option <?= $currentLang === 'vi' ? 'active' : '' ?>">
                            <span class="flag">🇻🇳</span> Tiếng Việt
                        </a>
                        <a href="?lang=th" class="language-option <?= $currentLang === 'th' ? 'active' : '' ?>">
                            <span class="flag">🇹🇭</span> ไทย
                        </a>
                    </div>
                </div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/profile" class="btn-profile"><?= __('menu.profile', [], $currentLang) ?></a>
                    <a href="/logout" class="btn-logout"><?= __('menu.logout', [], $currentLang) ?></a>
                <?php else: ?>
                    <a href="/auth" class="btn-login"><?= __('menu.login', [], $currentLang) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main> 

    <script>
    function toggleLanguageDropdown() {
        const dropdown = document.getElementById('languageDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // 드롭다운 외부 클릭 시 닫기
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('languageDropdown');
        const languageBtn = document.querySelector('.language-btn');
        
        if (!languageBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
    </script>
</body>
</html> 