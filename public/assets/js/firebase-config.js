// Firebase 설정
console.log('[Firebase] 설정 초기화 시작');

const firebaseConfig = {
    apiKey: "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
    authDomain: "topmkt-832f2.firebaseapp.com",
    projectId: "topmkt-832f2",
    storageBucket: "topmkt-832f2.appspot.com",
    messagingSenderId: "856114239779",
    appId: "1:856114239779:web:d8dd9049a9723ac8835496"
};

console.log('[Firebase] 설정값:', firebaseConfig);

try {
    // Firebase 초기화
    firebase.initializeApp(firebaseConfig);
    console.log('[Firebase] 초기화 성공');
} catch (error) {
    console.error('[Firebase] 초기화 실패:', error);
}

// Firebase 초기화 상태 확인
console.log('[Firebase] 현재 초기화 상태:', firebase.apps.length > 0 ? '초기화됨' : '초기화되지 않음'); 