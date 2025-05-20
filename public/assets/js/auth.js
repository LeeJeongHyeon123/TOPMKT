// 인증 메인 엔트리 파일
// - 각 모듈 import 및 초기화만 담당

// reCAPTCHA 설정
var recaptchaConfig = {
    sitekey: '6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4',
    callback: function() {
        console.log('reCAPTCHA가 준비되었습니다.');
        initializeElements();
    }
};

var recaptchaVerifier;
var verificationTimer = null;

// 초기화 상태 추적을 위한 변수
var isInitialized = false;

// DOM 요소 초기화 함수
function initializeElements() {
    if (isInitialized) {
        console.log('이미 초기화되어 있습니다.');
        return;
    }
    console.log('DOM 요소 초기화 시작');
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');
    var errorMessage = document.getElementById('errorMessage');
    var loginTab = document.getElementById('loginTab');
    var registerTab = document.getElementById('registerTab');
    var registerNickname = document.getElementById('registerNickname');
    var loginCountrySelect = document.getElementById('loginCountrySelect');
    var loginCountryDropdown = document.getElementById('loginCountryDropdown');
    var loginCountryFlag = document.getElementById('loginCountryFlag');
    var loginCountryCode = document.getElementById('loginCountryCode');
    var loginPhone = document.getElementById('loginPhone');
    var registerCountrySelect = document.getElementById('registerCountrySelect');
    var registerCountryDropdown = document.getElementById('registerCountryDropdown');
    var registerCountryFlag = document.getElementById('registerCountryFlag');
    var registerCountryCode = document.getElementById('registerCountryCode');
    var registerPhone = document.getElementById('registerPhone');
    var loginSendCodeBtn = document.getElementById('loginSendCodeBtn');
    var registerSendCodeBtn = document.getElementById('registerSendCodeBtn');
    var loginVerificationGroup = document.getElementById('loginVerificationGroup');
    var registerVerificationGroup = document.getElementById('registerVerificationGroup');
    var loginSubmitBtn = document.getElementById('loginSubmitBtn');
    var registerSubmitBtn = document.getElementById('registerSubmitBtn');
    // 국가 선택 드롭다운 초기화
    if (typeof initializeCountryDropdowns === 'function') {
        initializeCountryDropdowns();
    }
    // 탭 클릭 이벤트 리스너
    if (loginTab) {
        loginTab.onclick = function() { switchTab('login'); };
    }
    if (registerTab) {
        registerTab.onclick = function() { switchTab('register'); };
    }
    // 전화번호 입력 이벤트 리스너
    if (loginPhone && loginCountryCode) {
        loginPhone.oninput = function() { handlePhoneInput(loginPhone, loginCountryCode.textContent); };
    }
    if (registerPhone && registerCountryCode) {
        registerPhone.oninput = function() { handlePhoneInput(registerPhone, registerCountryCode.textContent); };
    }
    // 폼 제출 이벤트 리스너
    if (loginForm) {
        loginForm.onsubmit = handleLoginSubmit;
    }
    if (registerForm) {
        registerForm.onsubmit = handleRegisterSubmit;
    }
    // 인증번호 받기 버튼 클릭 이벤트
    if (loginSendCodeBtn) {
        loginSendCodeBtn.onclick = function() { handleSendVerificationCode('login'); };
    }
    if (registerSendCodeBtn) {
        registerSendCodeBtn.onclick = function() { handleSendVerificationCode('register'); };
    }
    isInitialized = true;
    console.log('DOM 요소 초기화 완료');
}

// 로딩 오버레이 제어 함수
function setLoading(isLoading) {
    console.log('setLoading 호출됨:', isLoading);
    var loadingOverlay = document.getElementById('loadingOverlay');
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
window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM이 로드되었습니다. 요소 초기화 시작');
    setLoading(true);
    initializeElements();
    initializeRecaptcha().then(() => {
        console.log('reCAPTCHA 초기화 완료');
    }).catch((error) => {
        console.error('reCAPTCHA 초기화 실패:', error);
    });
});
window.addEventListener('load', function() {
    setTimeout(function() { setLoading(false); }, 500);
});
window.setLoading = setLoading;

// reCAPTCHA 초기화
function initializeRecaptcha() {
    return new Promise((resolve, reject) => {
        try {
            if (typeof grecaptcha === 'undefined') {
                console.error('[reCAPTCHA] grecaptcha가 정의되지 않았습니다.');
                reject(new Error('reCAPTCHA가 로드되지 않았습니다.'));
                return;
            }

            // reCAPTCHA Enterprise 사용
            if (typeof grecaptcha.enterprise === 'undefined') {
                console.error('[reCAPTCHA] Enterprise 버전이 로드되지 않았습니다.');
                reject(new Error('reCAPTCHA Enterprise가 로드되지 않았습니다.'));
                return;
            }

            // 기본정책에 따른 초기화
            grecaptcha.enterprise.ready(function() {
                console.log('[reCAPTCHA] Enterprise 초기화 완료');
                resolve();
            });
        } catch (error) {
            console.error('[reCAPTCHA] 초기화 오류:', error);
            reject(error);
        }
    });
}

// reCAPTCHA 토큰 가져오기
async function getRecaptchaToken(action) {
    try {
        if (typeof grecaptcha.enterprise === 'undefined') {
            throw new Error('reCAPTCHA Enterprise가 초기화되지 않았습니다.');
        }

        const actionName = action.toUpperCase();
        return await grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', {action: actionName});
    } catch (error) {
        console.error('[reCAPTCHA] 토큰 생성 오류:', error);
        throw new Error('reCAPTCHA 토큰을 생성할 수 없습니다.');
    }
}

// 탭 전환 함수
function switchTab(tab) {
    var loginTab = document.getElementById('loginTab');
    var registerTab = document.getElementById('registerTab');
    var loginForm = document.getElementById('loginForm');
    var registerForm = document.getElementById('registerForm');
    var errorMessage = document.getElementById('errorMessage');
    var authTitle = document.getElementById('authTitle');
    var authSubtitle = document.getElementById('authSubtitle');
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

// 인증번호 발송 처리
async function handleSendVerificationCode(type) {
    console.log('인증번호 발송 시작:', type);
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorMessage = document.getElementById('errorMessage');
    const phone = document.getElementById(type === 'login' ? 'loginPhone' : 'registerPhone');
    const countryCode = document.getElementById(type === 'login' ? 'loginCountryCode' : 'registerCountryCode');
    const verificationGroup = document.getElementById(type === 'login' ? 'loginVerificationGroup' : 'registerVerificationGroup');
    const codeInput = document.getElementById(type === 'login' ? 'loginCode' : 'verificationCode');
    const verifyBtn = document.getElementById('verifyCodeBtn');

    // 기존 인증코드 초기화
    if (codeInput) {
        codeInput.value = '';
    }

    try {
        // 로딩 오버레이 표시
        setLoading(true);
        
        // 입력값 가져오기
        const phoneNumber = phone.value;
        const countryCodeValue = countryCode.textContent;

        // 전화번호 유효성 검사
        if (!validatePhoneNumber(phoneNumber, countryCodeValue)) {
            throw new Error('올바른 전화번호 형식이 아닙니다.');
        }

        // reCAPTCHA 토큰 가져오기
        const recaptchaToken = await getRecaptchaToken('SEND_CODE');

        // API 요청 데이터 구성
        const data = {
            phone: phoneNumber.replace(/[^0-9]/g, ''),
            country_code: countryCodeValue,
            recaptcha_token: recaptchaToken
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
            throw new Error(result.message || '인증번호 발송에 실패했습니다.');
        }

        // 인증번호 입력 UI 표시
        handleSendCode(type);
        
        // 인증번호 확인 버튼 표시
        if (verifyBtn) {
            verifyBtn.style.display = 'block';
            // 인증번호 확인 버튼 이벤트 리스너 추가
            verifyBtn.onclick = async () => {
                await verifyCode(type);
            };
        }
        
        // 성공 메시지 표시
        showVerificationMessage('인증번호가 발송되었습니다. 3분 이내에 입력해주세요.', 'success');

    } catch (error) {
        console.error('인증번호 발송 오류:', error);
        showError(error.message);
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
async function handleRegisterSubmit(event) {
    event.preventDefault();
    
    const loadingOverlay = document.getElementById('loadingOverlay');
    const errorMessage = document.getElementById('registerErrorMessage');
    
    try {
        // 로딩 표시
        setLoading(true);
        
        // 필수 입력값 확인
        const phone = document.getElementById('registerPhone');
        const countryCode = document.getElementById('registerCountryCode');
        const verificationCode = document.getElementById('verificationCode');
        const nickname = document.getElementById('registerNickname');
        const country = document.getElementById('registerCountry');
        const language = document.getElementById('registerLanguage');

        if (!phone || !phone.value.trim()) {
            throw new Error('전화번호를 입력해주세요.');
        }
        if (!countryCode || !countryCode.textContent.trim()) {
            throw new Error('국가 코드를 선택해주세요.');
        }
        if (!verificationCode || !verificationCode.value.trim()) {
            throw new Error('인증번호를 입력해주세요.');
        }
        if (!nickname || !nickname.value.trim()) {
            throw new Error('닉네임을 입력해주세요.');
        }
        if (!country || !country.value) {
            throw new Error('국가를 선택해주세요.');
        }
        if (!language || !language.value) {
            throw new Error('언어를 선택해주세요.');
        }

        // API 요청 데이터 구성
        const data = {
            phone: phone.value.replace(/[^0-9]/g, ''),
            country_code: countryCode.textContent.trim(),
            code: verificationCode.value.trim(),
            nickname: nickname.value.trim(),
            country: country.value,
            language: language.value
        };
        
        // reCAPTCHA 토큰 가져오기
        try {
            const recaptchaToken = await getRecaptchaToken('register');
            data.recaptcha_token = recaptchaToken;
        } catch (recaptchaError) {
            console.error('reCAPTCHA 토큰 획득 실패:', recaptchaError);
            throw new Error('보안 인증에 실패했습니다. 페이지를 새로고침하고 다시 시도해주세요.');
        }
        
        console.log('회원가입 요청 데이터:', data);

        // API 호출
        try {
            const response = await fetch('/api/auth/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            console.log('회원가입 응답:', result);

            if (!response.ok) {
                throw new Error(result.message || '회원가입에 실패했습니다.');
            }

            // 성공 처리
            showVerificationMessage('회원가입이 완료되었습니다. 로그인 페이지로 이동합니다.', 'success');
            setTimeout(() => {
                window.location.href = '/auth.php';
            }, 2000);
        } catch (apiError) {
            console.error('API 호출 오류:', apiError);
            throw new Error('서버 연결에 실패했습니다. 잠시 후 다시 시도해주세요.');
        }

    } catch (error) {
        console.error('회원가입 중 오류 발생:', error);
        if (errorMessage) {
            errorMessage.textContent = error.message;
            errorMessage.style.display = 'block';
            errorMessage.style.backgroundColor = '#f8d7da';
            errorMessage.style.color = '#721c24';
            errorMessage.style.border = '1px solid #f5c6cb';
        }
        showVerificationMessage(error.message, 'error');
    } finally {
        setLoading(false);
    }
}

// 회원가입 폼 초기화
function initializeRegisterForm() {
    const registerForm = document.getElementById('registerForm');
    const registerLanguage = document.getElementById('registerLanguage');
    
    if (registerForm && registerLanguage) {
        // 현재 선택된 언어 가져오기
        const currentLang = document.documentElement.lang || 'ko';
        
        // 언어 선택 초기화
        registerLanguage.value = currentLang;
        
        // 폼 제출 이벤트 처리
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // 거주 국가 선택 확인
            const country = document.getElementById('registerCountry').value;
            if (!country) {
                alert('거주 국가를 선택해주세요.');
                return;
            }
            
            // 여기에 회원가입 처리 로직 추가
        });
    }
}

// 페이지 로드 시 초기화
document.addEventListener('DOMContentLoaded', function() {
    initializeRegisterForm();
});

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

// 인증번호 받기 버튼 클릭 이벤트
const btn = document.getElementById('registerSendCodeBtn');
if (btn) {
    btn.addEventListener('click', async () => {
        const phoneInput = document.getElementById('registerPhone');
        const countryCode = document.getElementById('registerCountryCode').textContent;
        const phoneNumber = `${countryCode}${phoneInput.value.replace(/-/g, '')}`;
        
        try {
            // Firebase 인증번호 발송
            const provider = new firebase.auth.PhoneAuthProvider();
            window.verificationId = await provider.verifyPhoneNumber(phoneNumber, window.recaptchaVerifier);
            
            // 인증번호 입력 UI 표시
            handleSendCode('register');
            
            // 인증번호 확인 버튼 표시
            const verifyBtn = document.getElementById('verifyCodeBtn');
            if (verifyBtn) {
                verifyBtn.style.display = 'block';
            }
            
            showVerificationMessage('인증번호가 발송되었습니다.', 'success');
        } catch (error) {
            console.error('인증번호 발송 실패:', error);
            showVerificationMessage('인증번호 발송에 실패했습니다.', 'error');
        }
    });
}

// 인증번호 확인 버튼 클릭 이벤트
const verifyBtn = document.getElementById('verifyCodeBtn');
if (verifyBtn) {
    verifyBtn.addEventListener('click', async () => {
        await verifyCode('register');
    });
}

// 회원가입 폼 제출 이벤트
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // 인증번호 확인 검증
        if (!validateRegistrationForm()) {
            return;
        }
        
        // 나머지 회원가입 처리 로직...
    });
} 