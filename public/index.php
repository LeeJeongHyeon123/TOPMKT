<!DOCTYPE html>
<html lang="ko">
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
    <link rel="stylesheet" href="/public/assets/css/loading-overlay.css">
    
    <!-- reCAPTCHA Enterprise -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-storage-compat.js"></script>
    
    <!-- 실시간 메시지 업데이트 -->
    
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
    <!-- 로딩 오버레이 -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <div class="loading-text">로딩 중...</div>
    </div>

    <header class="main-header">
        <div class="header-container">
            <div class="header-left">
                <a href="/" class="logo">
                    <img src="/resources/images/logo.png" alt="탑마케팅">
                </a>
                <nav class="nav-menu">
                    <a href="/leaders" class="nav-item">추천 리더</a>
                    <a href="/vision" class="nav-item">회사/비전 소개</a>
                    <a href="/knowhow" class="nav-item">노하우 공유</a>
                    <a href="/recruiting" class="nav-item">팀 리쿠르팅 모집</a>
                    <a href="/events" class="nav-item">행사 일정</a>
                    <a href="/lectures" class="nav-item">강의 일정</a>
                    <a href="/community" class="nav-item">자유 커뮤니티</a>
                    <a href="/notice" class="nav-item">공지사항</a>
                </nav>
            </div>
            <div class="header-right">
                <div class="auth-buttons">
                    <a href="/auth.php" class="btn-login">로그인</a>
                    <a href="/auth.php?tab=register" class="btn-register">회원가입</a>
                </div>
                <div class="language-selector">
                    <button class="language-button">
                        <span class="current-language">🇰🇷</span>
                    </button>
                    <div class="language-dropdown">
                        <a href="?lang=ko" class="language-option active">🇰🇷 한국어</a>
                        <a href="?lang=en" class="language-option">🇺🇸 English</a>
                        <a href="?lang=zh" class="language-option">🇨🇳 中文</a>
                        <a href="?lang=ja" class="language-option">🇯🇵 日本語</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
    </main>

    <!-- Custom Scripts -->
    <script src="/public/assets/js/main.js"></script>
    <script src="/public/assets/js/auth.js"></script>

</body>
</html> 