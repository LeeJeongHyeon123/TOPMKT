// Firebase 설정
const firebaseConfig = {
    apiKey: "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
    authDomain: "topmkt-832f2.firebaseapp.com",
    projectId: "topmkt-832f2",
    storageBucket: "topmkt-832f2.firebasestorage.app",
    messagingSenderId: "856114239779",
    appId: "1:856114239779:web:d8dd9049a9723ac8835496"
};

// reCAPTCHA 설정
const recaptchaConfig = {
    siteKey: "6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"
};

// Firebase 초기화
firebase.initializeApp(firebaseConfig);

// 전역 변수
let recaptchaVerifier;

// DOM 요소
const phoneForm = document.getElementById('phoneForm');
const verificationForm = document.getElementById('verificationForm');
const errorMessage = document.getElementById('errorMessage');

// reCAPTCHA 초기화
function initRecaptcha() {
    grecaptcha.enterprise.ready(function() {
        console.log('reCAPTCHA Enterprise 초기화 완료');
    });
}

// reCAPTCHA 토큰 가져오기
async function getRecaptchaToken(action) {
    try {
        const token = await grecaptcha.enterprise.execute(recaptchaConfig.siteKey, {action: action});
        return token;
    } catch (error) {
        console.error('reCAPTCHA 토큰 생성 실패:', error);
        throw error;
    }
}

// 에러 메시지 표시
function showError(message) {
    errorMessage.textContent = message;
    errorMessage.style.display = 'block';
}

// 에러 메시지 숨기기
function hideError() {
    errorMessage.style.display = 'none';
}

// 휴대폰 번호 전송 처리
phoneForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();

    const phoneNumber = document.getElementById('phone').value;

    try {
        // reCAPTCHA 토큰 가져오기
        const recaptchaToken = await getRecaptchaToken('PHONE_VERIFICATION');

        // 서버에 인증번호 전송 요청
        const response = await fetch('/api/send-verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                phone: phoneNumber,
                recaptcha_token: recaptchaToken
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || '인증번호 전송에 실패했습니다.');
        }

        // 인증번호 입력 폼 표시
        phoneForm.style.display = 'none';
        verificationForm.style.display = 'block';

        // sessionInfo 저장
        window.sessionInfo = data.sessionInfo;

    } catch (error) {
        console.error('Error:', error);
        showError(error.message);
    }
});

// 인증번호 확인 처리
verificationForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();

    const code = document.getElementById('code').value;
    
    // 인증 진행 중 UI 표시
    const submitBtn = verificationForm.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = '처리 중...';

    try {
        console.log('인증 코드 검증 요청:', {
            sessionInfo: window.sessionInfo,
            code: code
        });
        
        // 서버에 인증번호 확인 요청
        const response = await fetch('http://www.topmktx.com/api/verify-code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sessionInfo: window.sessionInfo,
                code: code
            })
        });
        
        console.log('응답 상태:', response.status, response.statusText);
        
        // 응답 텍스트 먼저 읽기
        const responseText = await response.text();
        console.log('응답 내용:', responseText);
        
        // 응답이 비어있거나 JSON이 아닌 경우 처리
        if (!responseText) {
            throw new Error('서버로부터 빈 응답이 수신되었습니다.');
        }
        
        // JSON 파싱 시도
        let data;
        try {
            data = JSON.parse(responseText);
        } catch (jsonError) {
            console.error('JSON 파싱 오류:', jsonError);
            throw new Error('서버 응답을 처리할 수 없습니다: ' + responseText.substring(0, 100));
        }

        if (!response.ok) {
            // 서버에서 반환한 오류 메시지 사용
            throw new Error(data.error || '인증번호 확인에 실패했습니다.');
        }

        // 인증 성공 처리
        if (data.success) {
            console.log('인증 성공:', data);
            
            // 로컬 스토리지에 인증 정보 저장
            localStorage.setItem('authToken', data.idToken);
            localStorage.setItem('refreshToken', data.refreshToken);
            localStorage.setItem('phoneNumber', data.phoneNumber);
            
            // 로그인 성공 메시지 표시
            alert('인증이 완료되었습니다.');
            
            // 메인 페이지로 이동
            window.location.href = '/';
        } else {
            // 성공 응답이지만 success 필드가 없는 경우
            throw new Error('인증은 완료되었으나 로그인 정보가 올바르지 않습니다.');
        }

    } catch (error) {
        console.error('인증 오류:', error);
        showError(error.message);
    } finally {
        // 버튼 상태 복원
        submitBtn.disabled = false;
        submitBtn.textContent = originalBtnText;
    }
});

// 페이지 로드 시 reCAPTCHA 초기화
document.addEventListener('DOMContentLoaded', initRecaptcha); 