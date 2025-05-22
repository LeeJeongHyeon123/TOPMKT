<?php
// Firebase 초기화 스크립트
?>
<script>
async function initializeFirebase() {
    try {
        const firebaseConfig = {
            apiKey: "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
            authDomain: "topmkt-832f2.firebaseapp.com",
            projectId: "topmkt-832f2",
            storageBucket: "topmkt-832f2.appspot.com",
            messagingSenderId: "123456789012",
            appId: "1:123456789012:web:abcdef1234567890",
            measurementId: "G-4SJNZ4X3JY"
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
            firebase.analytics();
        }
    } catch (error) {
        console.error("Firebase 초기화 실패:", error);
    }
}

function setupAuthStateListener() {
    firebase.auth().onAuthStateChanged(function(user) {
        if (user) {
            // 로그인 상태 처리
            document.querySelector('.auth-buttons').style.display = 'none';
            document.querySelector('.user-menu').style.display = 'flex';
        } else {
            // 로그아웃 상태 처리
            document.querySelector('.auth-buttons').style.display = 'flex';
            document.querySelector('.user-menu').style.display = 'none';
        }
    });
}

// Firebase 초기화 및 인증 상태 리스너 설정
document.addEventListener('DOMContentLoaded', function() {
    initializeFirebase().then(() => {
        setupAuthStateListener();
    });
});
</script> 