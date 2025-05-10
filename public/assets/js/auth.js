// 인증 메인 엔트리 파일
// - 각 모듈 import 및 초기화만 담당

import { phoneFormats, handlePhoneInput, updatePhoneInput, toggleCountryDropdown, initializeCountryDropdowns } from './auth-country.js';
import { startVerificationTimer, stopVerificationTimer, showVerificationMessage } from './auth-verification.js';
import { showError, hideError } from './auth-form.js';

// reCAPTCHA 설정
const recaptchaConfig = {
    sitekey: '6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4',
    callback: function() {
        console.log('reCAPTCHA가 준비되었습니다.');
        initializeElements();
    }
};

let recaptchaVerifier;
let verificationTimer = null;

// 초기화 상태 추적을 위한 변수
let isInitialized = false;

// DOM 요소 초기화 함수
export function initializeElements() {
    // 이미 초기화되었다면 중복 실행 방지
    if (isInitialized) {
        console.log('이미 초기화되어 있습니다.');
        return;
    }
    
    console.log('DOM 요소 초기화 시작');
    
    // DOM 요소
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const errorMessage = document.getElementById('errorMessage');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const registerNickname = document.getElementById('registerNickname');

    // 국가 선택 관련 DOM 요소
    const loginCountrySelect = document.getElementById('loginCountrySelect');
    const loginCountryDropdown = document.getElementById('loginCountryDropdown');
    const loginCountryFlag = document.getElementById('loginCountryFlag');
    const loginCountryCode = document.getElementById('loginCountryCode');
    const loginPhone = document.getElementById('loginPhone');

    const registerCountrySelect = document.getElementById('registerCountrySelect');
    const registerCountryDropdown = document.getElementById('registerCountryDropdown');
    const registerCountryFlag = document.getElementById('registerCountryFlag');
    const registerCountryCode = document.getElementById('registerCountryCode');
    const registerPhone = document.getElementById('registerPhone');

    console.log('국가 선택 요소 확인:', {
        loginCountrySelect: !!loginCountrySelect,
        loginCountryDropdown: !!loginCountryDropdown,
        registerCountrySelect: !!registerCountrySelect,
        registerCountryDropdown: !!registerCountryDropdown
    });

    // 인증번호 받기 버튼 관련 DOM 요소
    const loginSendCodeBtn = document.getElementById('loginSendCodeBtn');
    const registerSendCodeBtn = document.getElementById('registerSendCodeBtn');
    const loginVerificationGroup = document.getElementById('loginVerificationGroup');
    const registerVerificationGroup = document.getElementById('registerVerificationGroup');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');
    const registerSubmitBtn = document.getElementById('registerSubmitBtn');

    // 국가 선택 드롭다운 초기화
    initializeCountryDropdowns();

    // 탭 클릭 이벤트 리스너
    if (loginTab) {
        loginTab.removeEventListener('click', () => switchTab('login'));
        loginTab.addEventListener('click', () => switchTab('login'));
    }
    
    if (registerTab) {
        registerTab.removeEventListener('click', () => switchTab('register'));
        registerTab.addEventListener('click', () => switchTab('register'));
    }

    // 전화번호 입력 이벤트 리스너
    if (loginPhone && loginCountryCode) {
        loginPhone.removeEventListener('input', () => handlePhoneInput(loginPhone, loginCountryCode.textContent));
        loginPhone.addEventListener('input', () => handlePhoneInput(loginPhone, loginCountryCode.textContent));
    }

    if (registerPhone && registerCountryCode) {
        registerPhone.removeEventListener('input', () => handlePhoneInput(registerPhone, registerCountryCode.textContent));
        registerPhone.addEventListener('input', () => handlePhoneInput(registerPhone, registerCountryCode.textContent));
    }

    // 폼 제출 이벤트 리스너
    if (loginForm) {
        loginForm.removeEventListener('submit', handleLoginSubmit);
        loginForm.addEventListener('submit', handleLoginSubmit);
    }

    if (registerForm) {
        registerForm.removeEventListener('submit', handleRegisterSubmit);
        registerForm.addEventListener('submit', handleRegisterSubmit);
    }

    // 인증번호 받기 버튼 클릭 이벤트
    if (loginSendCodeBtn) {
        loginSendCodeBtn.removeEventListener('click', () => handleSendVerificationCode('login'));
        loginSendCodeBtn.addEventListener('click', () => handleSendVerificationCode('login'));
    }

    if (registerSendCodeBtn) {
        registerSendCodeBtn.removeEventListener('click', () => handleSendVerificationCode('register'));
        registerSendCodeBtn.addEventListener('click', () => handleSendVerificationCode('register'));
    }

    // 초기화 완료 표시
    isInitialized = true;
    console.log('DOM 요소 초기화 완료');
}

// 로딩 오버레이 제어 함수
function setLoading(isLoading) {
    console.log('setLoading 호출됨:', isLoading);
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay) {
        console.error('로딩 오버레이를 찾을 수 없습니다.');
        return;
    }
    
    try {
        if (isLoading) {
            loadingOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            console.log('로딩 오버레이 표시됨');
        } else {
            loadingOverlay.classList.add('hidden');
            document.body.style.overflow = '';
            console.log('로딩 오버레이 숨겨짐');
        }
    } catch (error) {
        console.error('로딩 오버레이 상태 변경 중 오류 발생:', error);
    }
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM이 로드되었습니다. 요소 초기화 시작');
    
    // 로딩 오버레이 초기 상태 설정
    setLoading(true);
    
    // 요소 초기화
    initializeElements();
    
    // reCAPTCHA 초기화
    initRecaptcha();
});

// 페이지 완전 로드 시
window.addEventListener('load', () => {
    console.log('window.load 이벤트 발생');
    
    // 로딩 오버레이 숨기기
    setTimeout(() => {
        console.log('타이머 완료 - 로딩 오버레이 숨기기');
        setLoading(false);
    }, 500);
});

// 전역 스코프에 함수 노출
window.setLoading = setLoading;

// reCAPTCHA 초기화 함수
function initRecaptcha() {
    if (typeof grecaptcha === 'undefined') {
        console.log('reCAPTCHA 로드 대기 중...');
        setTimeout(initRecaptcha, 100);
        return;
    }
    
    if (typeof grecaptcha.ready === 'function') {
        grecaptcha.ready(() => {
            console.log('reCAPTCHA가 준비되었습니다.');
        });
    } else {
        console.error('reCAPTCHA ready 함수를 찾을 수 없습니다.');
    }
}

// 탭 전환 함수
function switchTab(tab) {
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const errorMessage = document.getElementById('errorMessage');
    const authTitle = document.getElementById('authTitle');
    const authSubtitle = document.getElementById('authSubtitle');

    // 요소가 없으면 함수 종료
    if (!loginTab || !registerTab || !loginForm || !registerForm) {
        console.error('탭 전환에 필요한 요소를 찾을 수 없습니다.');
        return;
    }

    if (tab === 'login') {
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        authTitle.textContent = '로그인';
        authSubtitle.textContent = '휴대폰 번호로 간편하게 로그인하세요';
    } else {
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        authTitle.textContent = '회원가입';
        authSubtitle.textContent = '휴대폰 번호로 간편하게 회원가입하세요';
    }

    if (errorMessage) {
        errorMessage.style.display = 'none';
    }
}

// 전화번호 유효성 검사 함수
function validatePhoneNumber(value, countryCode) {
    console.log('전화번호 검증 시작:', { value, countryCode });
    
    const format = phoneFormats[countryCode];
    if (!format) {
        console.error('지원하지 않는 국가 코드:', countryCode);
        return false;
    }
    
    // 숫자만 추출
    const numbers = value.replace(/[^0-9]/g, '');
    console.log('숫자만 추출된 전화번호:', numbers);
    
    // 국가별 길이 검증
    const isValid = format.validate(value);
    console.log('전화번호 유효성 검사 결과:', isValid);
    
    return isValid;
}

// 인증번호 받기 함수 수정
async function handleSendVerificationCode(type) {
    console.log('인증번호 발송 시작:', type);
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorMessage = document.getElementById('errorMessage');
    const phone = document.getElementById(`${type}Phone`);
    const countryCode = document.getElementById(`${type}CountryCode`);
    const verificationGroup = document.querySelector('.verification-group');
    const verificationMessage = document.querySelector('.verification-message');
    const sendCodeBtn = document.getElementById(`${type}SendCodeBtn`);

    // 필수 DOM 요소 확인
    if (!loadingOverlay || !errorMessage || !phone || !countryCode || !verificationGroup || !sendCodeBtn) {
        console.error('필수 DOM 요소를 찾을 수 없습니다:', {
            loadingOverlay: !!loadingOverlay,
            errorMessage: !!errorMessage,
            phone: !!phone,
            countryCode: !!countryCode,
            verificationGroup: !!verificationGroup,
            sendCodeBtn: !!sendCodeBtn
        });
        return;
    }

    try {
        // 로딩 오버레이 표시
        setLoading(true);

        // 전화번호 유효성 검사
        const phoneNumber = phone.value;
        const countryCodeValue = countryCode.textContent;
        
        if (!phoneNumber || !countryCodeValue) {
            throw new Error('전화번호를 입력해주세요.');
        }

        if (!validatePhoneNumber(phoneNumber, countryCodeValue)) {
            throw new Error('올바른 전화번호 형식이 아닙니다.');
        }

        // reCAPTCHA 토큰 생성
        const token = await grecaptcha.execute(recaptchaConfig.sitekey, { action: recaptchaConfig.action });
        
        // API 요청 데이터 구성
        const data = {
            phone: phoneNumber.replace(/[^0-9]/g, ''),
            country_code: countryCodeValue,
            recaptcha_token: token,
            action: recaptchaConfig.action
        };

        // API 호출
        const response = await fetch('/api/auth/send-code.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || '인증번호 발송 중 오류가 발생했습니다.');
        }

        // 인증번호 입력 UI 표시
        console.log('인증번호 입력 UI 표시 시작');
        verificationGroup.style.display = 'block';
        sendCodeBtn.style.display = 'none';
        
        // 타이머 시작
        startVerificationTimer();
        
        // 성공 메시지 표시
        if (verificationMessage) {
            verificationMessage.textContent = '인증번호가 발송되었습니다.';
            verificationMessage.className = 'verification-message success';
            verificationMessage.style.display = 'block';
        }

        // 인증번호 입력 필드 포커스
        const verificationCode = document.getElementById('verificationCode');
        if (verificationCode) {
            verificationCode.focus();
        }

        console.log('인증번호 입력 UI 표시 완료');

    } catch (error) {
        console.error('인증번호 발송 오류:', error);
        if (verificationMessage) {
            verificationMessage.textContent = error.message;
            verificationMessage.className = 'verification-message error';
            verificationMessage.style.display = 'block';
        }
    } finally {
        setLoading(false);
    }
}

// Firebase 서비스 레퍼런스 가져오기
function getFirebaseServices() {
    return {
        auth: window.firebaseAuth,
        firestore: window.firebaseFirestore,
        storage: window.firebaseStorage
    };
}

// reCAPTCHA 토큰 가져오기
async function getRecaptchaToken(action) {
    try {
        console.log('reCAPTCHA 토큰 생성 시작:', action);
        console.log('reCAPTCHA 설정:', recaptchaConfig);
        
        const token = await grecaptcha.execute(recaptchaConfig.sitekey, {
            action: action || recaptchaConfig.action
        });
        
        console.log('reCAPTCHA 토큰 생성 성공:', token);
        return token;
    } catch (error) {
        console.error('reCAPTCHA 토큰 생성 실패:', error);
        throw new Error('reCAPTCHA 검증에 실패했습니다.');
    }
}

// 로그인 폼 제출 핸들러
async function handleLoginSubmit(event) {
    event.preventDefault();
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorMessage = document.getElementById('errorMessage');
    const loginPhone = document.getElementById('loginPhone');
    const loginCountryCode = document.getElementById('loginCountryCode');
    const loginCode = document.getElementById('loginCode');

    try {
        // 로딩 오버레이 표시
        loadingOverlay.style.display = 'flex';
        
        const phoneNumber = loginPhone.value;
        const countryCode = loginCountryCode.textContent;
        const verificationCode = loginCode.value;
        
        // 전화번호 유효성 검사
        if (!validatePhoneNumber(phoneNumber, countryCode)) {
            throw new Error('올바른 전화번호 형식이 아닙니다.');
        }
        
        // 인증번호 유효성 검사
        if (!verificationCode || verificationCode.length !== 6) {
            throw new Error('인증번호 6자리를 정확히 입력해주세요.');
        }

        // API 요청 데이터 구성
        const data = {
            phone: phoneNumber.replace(/[^0-9]/g, ''),
            country_code: countryCode,
            code: verificationCode
        };

        // API 호출
        const response = await fetch('/api/auth/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || '로그인에 실패했습니다.');
        }

        // 세션 저장
        localStorage.setItem('auth', JSON.stringify({
            idToken: result.data.idToken,
            refreshToken: result.data.refreshToken,
            phoneNumber: result.data.phoneNumber,
            nickname: result.data.nickname,
            timestamp: Date.now()
        }));

        // 메인 페이지로 리다이렉트
        window.location.href = '/';

    } catch (error) {
        console.error('로그인 오류:', error);
        errorMessage.textContent = error.message;
        errorMessage.style.color = '#dc3545';
        errorMessage.style.display = 'block';
    } finally {
        loadingOverlay.style.display = 'none';
    }
}

// 인증번호 확인 함수 수정
async function handleVerifyCode() {
    const verificationCode = document.getElementById('verificationCode').value;
    const verificationMessage = document.querySelector('.verification-message');
    const registerCompleteGroup = document.querySelector('.register-complete-group');

    if (!verificationCode) {
        showVerificationMessage('인증번호를 입력해주세요.', 'error');
        return;
    }

    try {
        setLoading(true);
        const response = await fetch('/api/verify-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code: verificationCode })
        });

        const data = await response.json();

        if (data.success) {
            showVerificationMessage('인증이 완료되었습니다.', 'success');
            stopVerificationTimer();
            registerCompleteGroup.style.display = 'block';
        } else {
            showVerificationMessage(data.message || '인증번호가 일치하지 않습니다.', 'error');
        }
    } catch (error) {
        console.error('인증번호 확인 중 오류 발생:', error);
        showVerificationMessage('인증번호 확인 중 오류가 발생했습니다.', 'error');
    } finally {
        setLoading(false);
    }
}

// 회원가입 제출 처리
async function handleRegisterSubmit() {
    const form = document.getElementById('registerForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        setLoading(true);
        const recaptchaToken = await grecaptcha.execute('6LcXXXXXXXXXXXXXXXX', { action: 'register' });
        data.recaptchaToken = recaptchaToken;

        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            showVerificationMessage('회원가입이 완료되었습니다. 로그인 페이지로 이동합니다.', 'success');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            showVerificationMessage(result.message || '회원가입 중 오류가 발생했습니다.', 'error');
        }
    } catch (error) {
        console.error('회원가입 중 오류 발생:', error);
        showVerificationMessage('회원가입 중 오류가 발생했습니다.', 'error');
    } finally {
        setLoading(false);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 인증번호 확인 버튼 이벤트 리스너
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    if (verifyCodeBtn) {
        verifyCodeBtn.addEventListener('click', handleVerifyCode);
    }

    // 회원가입 완료 버튼 이벤트 리스너
    const registerCompleteBtn = document.getElementById('registerCompleteBtn');
    if (registerCompleteBtn) {
        registerCompleteBtn.addEventListener('click', handleRegisterSubmit);
    }
}); 