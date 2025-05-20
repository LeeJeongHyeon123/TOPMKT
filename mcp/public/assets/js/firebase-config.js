/**
 * Firebase 클라이언트 설정 파일
 * 
 * 탑마케팅 프로젝트의 Firebase 설정을 관리합니다.
 * server/config/firebase-config.php에서 정의된 설정과 동일한 값을 사용합니다.
 * 
 * @version 1.0.0
 * @author TOPMKT Development Team
 */

// Firebase 초기화 상태 관리
const FirebaseManager = {
    initialized: false,
    config: {
        apiKey: "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
        authDomain: "topmkt-832f2.firebaseapp.com",
        projectId: "topmkt-832f2",
        storageBucket: "topmkt-832f2.appspot.com",
        messagingSenderId: "856114239779",
        appId: "1:856114239779:web:1234567890abcdef",
        measurementId: "G-4SJNZ4X3JY"
    },
    
    // Firebase SDK 로드 대기
    async waitForFirebase() {
        return new Promise((resolve) => {
            if (typeof firebase !== 'undefined') {
                resolve();
                return;
            }
            
            const checkFirebase = setInterval(() => {
                if (typeof firebase !== 'undefined') {
                    clearInterval(checkFirebase);
                    resolve();
                }
            }, 100);
        });
    },
    
    // Firebase 초기화
    async initialize() {
        try {
            // 이미 초기화된 경우
            if (this.initialized) {
                console.log('[Firebase] 이미 초기화되어 있습니다.');
                return;
            }
            
            // Firebase SDK 로드 대기
            await this.waitForFirebase();
            
            console.log('[Firebase] 설정 초기화 시작');
            console.log('[Firebase] 설정값:', this.config);
            
            // Firebase 앱 초기화
            if (!firebase.apps.length) {
                firebase.initializeApp(this.config);
                console.log('[Firebase] 초기화 성공');
            } else {
                console.log('[Firebase] 이미 초기화된 앱 사용');
            }
            
            // 서비스 초기화
            try {
                window.firebaseAuth = firebase.auth();
                console.log('[Firebase] Auth 서비스 초기화 성공');
            } catch (error) {
                console.error('[Firebase] Auth 서비스 초기화 실패:', error);
                throw new Error('Auth 서비스 초기화 실패');
            }
            
            try {
                window.firebaseFirestore = firebase.firestore();
                console.log('[Firebase] Firestore 서비스 초기화 성공');
            } catch (error) {
                console.error('[Firebase] Firestore 서비스 초기화 실패:', error);
            }
            
            try {
                window.firebaseStorage = firebase.storage();
                console.log('[Firebase] Storage 서비스 초기화 성공');
            } catch (error) {
                console.error('[Firebase] Storage 서비스 초기화 실패:', error);
            }
            
            // 초기화 완료
            this.initialized = true;
            console.log('[Firebase] 모든 서비스 초기화 완료');
            
        } catch (error) {
            console.error('[Firebase] 초기화 중 오류 발생:', error);
            this.initialized = false;
            throw error;
        }
    }
};

// 페이지 로드 시 Firebase 초기화
document.addEventListener('DOMContentLoaded', () => {
    FirebaseManager.initialize().catch(error => {
        console.error('[Firebase] 초기화 실패:', error);
    });
});

// 전역 변수로 FirebaseManager 노출
window.FirebaseManager = FirebaseManager; 