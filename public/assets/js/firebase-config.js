// Firebase 초기화 상태 추적
window.firebaseInitialized = window.firebaseInitialized || false;
let pendingAuthCallbacks = [];
let recaptchaVerifier = null;

// Firebase 초기화 함수
async function initializeFirebase() {
    if (window.firebaseInitialized) {
        console.log('[Firebase] 이미 초기화되어 있습니다.');
        return Promise.resolve();
    }
    
    return new Promise((resolve, reject) => {
        try {
            const firebaseConfig = {
                apiKey: "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
                authDomain: "topmkt-832f2.firebaseapp.com",
                projectId: "topmkt-832f2",
                storageBucket: "topmkt-832f2.appspot.com",
                messagingSenderId: "856114239779",
                appId: "1:856114239779:web:d8dd9049a9723ac8835496"
            };

            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
                console.log('[Firebase] 초기화 성공');
                
                // Firebase 초기화 후 reCAPTCHA 설정
                recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                    'size': 'invisible',
                    'callback': (response) => {
                        console.log('reCAPTCHA 검증 완료');
                    }
                });
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