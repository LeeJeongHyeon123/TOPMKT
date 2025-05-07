// Firebase 설정
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "topmkt-832f2.firebaseapp.com",
    projectId: "topmkt-832f2",
    storageBucket: "topmkt-832f2.appspot.com",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
};

// Firebase 초기화
firebase.initializeApp(firebaseConfig);

// Firebase 인증 객체
const auth = firebase.auth();

// 전화번호 인증 설정
auth.useDeviceLanguage();
auth.settings.appVerificationDisabledForTesting = false; // 테스트 환경에서만 true로 설정 