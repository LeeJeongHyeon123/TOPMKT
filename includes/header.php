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
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    
    <!-- reCAPTCHA Enterprise -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-storage-compat.js"></script>
    
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
    window.firebaseInitialized = window.firebaseInitialized || false;
    let pendingAuthCallbacks = [];
    
    // Firebase 초기화 함수
    async function initializeFirebase() {
        if (window.firebaseInitialized) {
            console.log('[Firebase] 이미 초기화되어 있습니다.');
            return Promise.resolve();
        }
        
        return new Promise((resolve, reject) => {
            try {
                const firebaseConfig = {
                    apiKey: "AIzaSyDXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
                    authDomain: "topmkt.firebaseapp.com",
                    projectId: "topmkt",
                    storageBucket: "topmkt.appspot.com",
                    messagingSenderId: "XXXXXXXXXXXX",
                    appId: "1:XXXXXXXXXXXX:web:XXXXXXXXXXXXXXXX"
                };

                if (!firebase.apps.length) {
                    firebase.initializeApp(firebaseConfig);
                    console.log('[Firebase] 초기화 성공');
                } else {
                    console.log('[Firebase] 이미 초기화된 앱 사용');
                }
                
                window.firebaseInitialized = true;
                resolve();
            } catch (error) {
                console.error('[Firebase] 초기화 오류:', error);
                window.firebaseInitialized = false;
                reject(error);
            }
        });
    }

    // 인증 상태 변경 리스너 설정
    function setupAuthStateListener() {
        if (!window.firebaseInitialized) {
            pendingAuthCallbacks.push(setupAuthStateListener);
            return;
        }

        firebase.auth().onAuthStateChanged(function(user) {
            if (user) {
                // 사용자가 로그인한 경우
                console.log('[Firebase] 사용자 로그인됨:', user.uid);
            } else {
                // 사용자가 로그아웃한 경우
                console.log('[Firebase] 사용자 로그아웃됨');
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
                console.error('[Firebase] 초기화 실패:', error);
            });
    });

    // 페이지 언로드 시 정리
    window.addEventListener('beforeunload', function() {
        if (window.firebaseInitialized) {
            // 필요한 정리 작업 수행
            firebase.auth().signOut().catch(console.error);
        }
    });
    </script>
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="header-left">
                <a href="/" class="logo">
                    <img src="/resources/images/logo.png" alt="탑마케팅">
                </a>
                <nav class="main-nav">
                    <ul>
                        <li><a href="/#leaders">추천 리더</a></li>
                        <li><a href="/#vision">회사/비전 소개</a></li>
                        <li><a href="/#knowhow">노하우 공유</a></li>
                        <li><a href="/#community">커뮤니티</a></li>
                    </ul>
                </nav>
            </div>
            <div class="header-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="user-menu">
                        <button class="user-menu-button">
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? '/resources/images/default-profile.png'); ?>" 
                                 alt="프로필" 
                                 class="profile-image">
                            <span class="user-name"><?php echo htmlspecialchars($_SESSION['nickname']); ?></span>
                        </button>
                        <div class="user-dropdown">
                            <a href="/profile.php" class="dropdown-item">프로필</a>
                            <a href="/messages.php" class="dropdown-item">
                                메시지
                                <?php if (isset($_SESSION['unread_messages']) && $_SESSION['unread_messages'] > 0): ?>
                                    <span class="badge"><?php echo $_SESSION['unread_messages']; ?></span>
                                <?php endif; ?>
                            </a>
                            <a href="/settings.php" class="dropdown-item">설정</a>
                            <a href="/logout.php" class="dropdown-item">로그아웃</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="/auth.php" class="btn-login">로그인</a>
                        <a href="/auth.php?tab=register" class="btn-register">회원가입</a>
                    </div>
                <?php endif; ?>
                <div class="language-selector">
                    <button class="language-button">
                        <span class="current-language">
                            <?php
                            $langFlags = [
                                'ko' => '🇰🇷',
                                'en' => '🇺🇸',
                                'zh' => '🇨🇳',
                                'ja' => '🇯🇵'
                            ];
                            echo $langFlags[$currentLang] ?? '🌐';
                            ?>
                        </span>
                    </button>
                    <div class="language-dropdown">
                        <a href="?lang=ko" class="language-option <?php echo $currentLang === 'ko' ? 'active' : ''; ?>">
                            🇰🇷 한국어
                        </a>
                        <a href="?lang=en" class="language-option <?php echo $currentLang === 'en' ? 'active' : ''; ?>">
                            🇺🇸 English
                        </a>
                        <a href="?lang=zh" class="language-option <?php echo $currentLang === 'zh' ? 'active' : ''; ?>">
                            🇨🇳 中文
                        </a>
                        <a href="?lang=ja" class="language-option <?php echo $currentLang === 'ja' ? 'active' : ''; ?>">
                            🇯🇵 日本語
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
</body>
</html> 