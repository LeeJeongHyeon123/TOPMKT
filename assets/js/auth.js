document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event fired'); // 디버깅용 로그

    // JS 활성화 시 body에 js-enabled 클래스 추가
    document.body.classList.add('js-enabled');

    // Toast 메시지 표시 함수
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        // 애니메이션을 위한 setTimeout
        setTimeout(() => {
            toast.classList.add('show');
        }, 100);
        
        // 3초 후 제거
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }

    // reCAPTCHA 로드 콜백 함수
    window.onRecaptchaLoad = function() {
        console.log('reCAPTCHA Enterprise가 로드되었습니다.');
    };

    // DOM 요소 초기화
    const elements = {
        loginTab: document.getElementById('loginTab'),
        registerTab: document.getElementById('registerTab'),
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        authSubtitle: document.getElementById('authSubtitle'),
        loginSendCodeBtn: document.getElementById('loginSendCodeBtn'),
        sendCodeBtn: document.getElementById('sendCodeBtn'),
        checkNicknameBtn: document.getElementById('checkNicknameBtn'),
        nicknameInput: document.getElementById('nickname'),
        nicknameFeedback: document.getElementById('nicknameFeedback'),
        loginPhone: document.getElementById('loginPhone'),
        registerPhone: document.getElementById('registerPhone'),
        loginCountryCode: document.getElementById('loginCountryCode'),
        registerCountryCode: document.getElementById('registerCountryCode'),
        loginVerificationGroup: document.getElementById('loginVerificationGroup'),
        registerVerificationGroup: document.getElementById('registerVerificationGroup'),
        loginSubmitBtn: document.getElementById('loginSubmitBtn'),
        registerSubmitBtn: document.getElementById('registerSubmitBtn')
    };

    // 디버깅을 위한 요소 존재 여부 확인
    console.log('DOM Elements:', elements);

    // 탭 전환 함수
    function switchTab(isLogin) {
        console.log('switchTab called with isLogin:', isLogin);

        // 탭 버튼 상태 변경
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        
        if (loginTab && registerTab) {
            if (isLogin) {
                loginTab.classList.add('active');
                registerTab.classList.remove('active');
            } else {
                registerTab.classList.add('active');
                loginTab.classList.remove('active');
            }
        }

        // 폼 표시 상태 변경
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        
        if (loginForm && registerForm) {
            loginForm.style.display = isLogin ? '' : 'none';
            registerForm.style.display = isLogin ? 'none' : '';
        }

        // 서브타이틀 메시지 변경
        const authSubtitle = document.getElementById('authSubtitle');
        console.log('authSubtitle element:', authSubtitle); // 디버깅용 로그

        if (authSubtitle) {
            const message = isLogin ? 
                '휴대폰 번호로 간편하게 로그인하세요' : 
                '휴대폰 번호로 간편하게 회원가입하세요';
            console.log('Changing subtitle to:', message);
            authSubtitle.textContent = message;
            console.log('New subtitle text:', authSubtitle.textContent); // 디버깅용 로그
        } else {
            console.error('authSubtitle element not found');
        }
    }

    // 탭 클릭 이벤트 리스너 등록
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');

    if (loginTab) {
        console.log('Login tab found, adding click listener');
        loginTab.addEventListener('click', function() {
            console.log('Login tab clicked');
            switchTab(true);
        });
    } else {
        console.error('Login tab not found');
    }

    if (registerTab) {
        console.log('Register tab found, adding click listener');
        registerTab.addEventListener('click', function() {
            console.log('Register tab clicked');
            alert('테스트');
            switchTab(false);
        });
    } else {
        console.error('Register tab not found');
    }

    // 초기 탭 상태 설정
    switchTab(true);

    // 인증번호 받기 버튼 클릭 시 reCAPTCHA 실행 및 인증번호 요청
    function sendCodeWithRecaptcha(phone, mode) {
        if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
            console.error('reCAPTCHA Enterprise가 로드되지 않았습니다.');
            showToast('보안 검증을 초기화하는 중입니다. 잠시 후 다시 시도해주세요.', 'error');
            return;
        }

        grecaptcha.enterprise.ready(function() {
            grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', {
                action: 'send_code'
            }).then(function(token) {
                if (mode === 'login') {
                    requestLoginCode(phone, token);
                } else {
                    requestRegisterCode(phone, token);
                }
            }).catch(function(error) {
                console.error('reCAPTCHA 실행 중 오류 발생:', error);
                showToast('보안 검증 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.', 'error');
            });
        });
    }

    // 로그인 인증번호 요청
    function requestLoginCode(phone, recaptchaToken) {
        if (elements.loginVerificationGroup) elements.loginVerificationGroup.style.display = '';
        if (elements.loginSubmitBtn) elements.loginSubmitBtn.style.display = '';
        showToast('인증번호가 전송되었습니다.', 'success');
    }
    // 회원가입 인증번호 요청
    function requestRegisterCode(phone, recaptchaToken) {
        if (elements.registerVerificationGroup) elements.registerVerificationGroup.style.display = '';
        if (elements.registerSubmitBtn) elements.registerSubmitBtn.style.display = '';
        showToast('인증번호가 전송되었습니다.', 'success');
    }

    // 로그인 인증번호 받기 버튼
    if (elements.loginSendCodeBtn) {
        elements.loginSendCodeBtn.addEventListener('click', function() {
            const phone = elements.loginPhone?.value.trim();
            if (!phone) {
                showToast('휴대폰 번호를 입력하세요.', 'error');
                return;
            }
            sendCodeWithRecaptcha(phone, 'login');
        });
    }
    // 회원가입 인증번호 받기 버튼
    if (elements.sendCodeBtn) {
        elements.sendCodeBtn.addEventListener('click', function() {
            const phone = elements.registerPhone?.value.trim();
            const country = elements.registerCountryCode?.textContent;
            const nickname = elements.nicknameInput?.value.trim();
            
            if (!phone || !nickname) {
                showToast('전화번호와 닉네임을 모두 입력해주세요.', 'error');
                return;
            }
            
            if (!elements.nicknameInput?.classList.contains('is-valid')) {
                showToast('닉네임 중복 확인을 해주세요.', 'error');
                return;
            }
            
            sendCodeWithRecaptcha(phone, 'register');
        });
    }

    // 로그인 폼 제출(인증번호 확인 및 로그인)
    if (elements.loginForm) {
        elements.loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('로그인 처리(서버 연동 필요)', 'info');
        });
    }
    // 회원가입 폼 제출(인증번호 확인 및 회원가입)
    if (elements.registerForm) {
        elements.registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            showToast('회원가입 처리(서버 연동 필요)', 'info');
        });
    }

    // 국가별 전화번호 형식 정의
    const phoneFormats = {
        '+82': { // 한국
            placeholder: '010-1234-1234',
            pattern: /^0?1[0-9]-?\d{3,4}-?\d{4}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                if (num.startsWith('0')) num = num.substring(1);
                if (num.startsWith('1')) {
                    return `0${num.slice(0, 2)}-${num.slice(2, 6)}-${num.slice(6, 10)}`;
                }
                return num;
            }
        },
        '+1': { // 미국/캐나다
            placeholder: '123-456-7890',
            pattern: /^[2-9]\d{2}-?\d{3}-?\d{4}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                return `${num.slice(0, 3)}-${num.slice(3, 6)}-${num.slice(6, 10)}`;
            }
        },
        '+86': { // 중국
            placeholder: '123-4567-8901',
            pattern: /^1[3-9]\d{9}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                return `${num.slice(0, 3)}-${num.slice(3, 7)}-${num.slice(7, 11)}`;
            }
        },
        '+81': { // 일본
            placeholder: '90-1234-5678',
            pattern: /^0?[789]0-?\d{4}-?\d{4}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                if (num.startsWith('0')) num = num.substring(1);
                return `${num.slice(0, 2)}-${num.slice(2, 6)}-${num.slice(6, 10)}`;
            }
        },
        '+886': { // 대만
            placeholder: '912-345-678',
            pattern: /^0?9\d{2}-?\d{3}-?\d{3}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                if (num.startsWith('0')) num = num.substring(1);
                return `${num.slice(0, 3)}-${num.slice(3, 6)}-${num.slice(6, 9)}`;
            }
        },
        '+84': { // 베트남
            placeholder: '123-456-7890',
            pattern: /^0?[1-9]\d{2}-?\d{3}-?\d{4}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                if (num.startsWith('0')) num = num.substring(1);
                return `${num.slice(0, 3)}-${num.slice(3, 6)}-${num.slice(6, 10)}`;
            }
        },
        '+66': { // 태국
            placeholder: '81-234-5678',
            pattern: /^0?[689]\d{1}-?\d{3}-?\d{4}$/,
            format: (num) => {
                num = num.replace(/[^\d]/g, '');
                if (num.startsWith('0')) num = num.substring(1);
                return `${num.slice(0, 2)}-${num.slice(2, 5)}-${num.slice(5, 9)}`;
            }
        }
    };

    // 전화번호 입력 이벤트 처리 함수
    function setupPhoneInput(inputId, countryCodeId) {
        const input = document.getElementById(inputId);
        const countryCode = document.getElementById(countryCodeId);
        
        if (input && countryCode) {
            // 국가 코드 변경 시 플레이스홀더 업데이트
            function updatePlaceholder() {
                const code = countryCode.textContent;
                const format = phoneFormats[code];
                if (format) {
                    input.placeholder = format.placeholder;
                }
            }
            
            // 초기 플레이스홀더 설정
            updatePlaceholder();

            input.addEventListener('input', function(e) {
                const value = e.target.value;
                const currentCountryCode = countryCode.textContent;
                const format = phoneFormats[currentCountryCode];
                
                if (format) {
                    // 숫자만 입력 가능하도록 처리
                    if (!/^\d*$/.test(value.replace(/-/g, ''))) {
                        e.target.value = value.replace(/[^\d-]/g, '');
                        return;
                    }
                    
                    // 형식 적용
                    e.target.value = format.format(value);
                }
            });

            // 붙여넣기 이벤트 처리
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const currentCountryCode = countryCode.textContent;
                const format = phoneFormats[currentCountryCode];
                
                if (format) {
                    e.target.value = format.format(pastedText);
                }
            });
        }
    }

    // 국가 선택 드롭다운 동작 (로그인/회원가입 공통)
    function setupCountrySelector(selectId, dropdownId, flagId, codeId, phoneInputId) {
        const select = document.getElementById(selectId);
        const dropdown = document.getElementById(dropdownId);
        const flag = document.getElementById(flagId);
        const code = document.getElementById(codeId);
        const phoneInput = document.getElementById(phoneInputId);
        
        if (!select || !dropdown || !flag || !code || !phoneInput) return;

        select.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        dropdown.querySelectorAll('.country-option').forEach(option => {
            option.addEventListener('click', function() {
                flag.textContent = this.dataset.flag;
                code.textContent = this.dataset.code;
                dropdown.style.display = 'none';
                
                // 국가 변경 시 전화번호 입력 필드 초기화 및 플레이스홀더 업데이트
                phoneInput.value = '';
                const format = phoneFormats[this.dataset.code];
                if (format) {
                    phoneInput.placeholder = format.placeholder;
                }
            });
        });

        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    // 로그인과 회원가입 폼의 전화번호 입력 설정
    setupPhoneInput('loginPhone', 'loginCountryCode');
    setupPhoneInput('registerPhone', 'registerCountryCode');

    // 국가 선택 드롭다운 설정
    setupCountrySelector('loginCountrySelect', 'loginCountryDropdown', 'loginCountryFlag', 'loginCountryCode', 'loginPhone');
    setupCountrySelector('registerCountrySelect', 'registerCountryDropdown', 'registerCountryFlag', 'registerCountryCode', 'registerPhone');

    // 닉네임 중복 확인 버튼 클릭 이벤트
    if (elements.checkNicknameBtn && elements.nicknameInput && elements.nicknameFeedback) {
        elements.checkNicknameBtn.addEventListener('click', function() {
            const nickname = elements.nicknameInput.value.trim();
            
            if (!nickname) {
                elements.nicknameFeedback.className = 'feedback-message error';
                elements.nicknameFeedback.textContent = '닉네임을 입력해주세요.';
                return;
            }
            
            // 닉네임 중복 확인 요청
            fetch('/api/auth/check-nickname.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ nickname: nickname })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.exists) {
                        elements.nicknameFeedback.className = 'feedback-message error';
                        elements.nicknameFeedback.textContent = '이미 사용 중인 닉네임입니다.';
                        elements.nicknameInput.classList.add('is-invalid');
                        if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = true;
                    } else {
                        elements.nicknameFeedback.className = 'feedback-message success';
                        elements.nicknameFeedback.textContent = '사용 가능한 닉네임입니다.';
                        elements.nicknameInput.classList.remove('is-invalid');
                        elements.nicknameInput.classList.add('is-valid');
                        if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
                    }
                } else {
                    elements.nicknameFeedback.className = 'feedback-message error';
                    elements.nicknameFeedback.textContent = data.message || '닉네임 확인 중 오류가 발생했습니다.';
                }
            })
            .catch(error => {
                console.error('닉네임 중복 확인 중 오류:', error);
                elements.nicknameFeedback.className = 'feedback-message error';
                elements.nicknameFeedback.textContent = '닉네임 확인 중 오류가 발생했습니다.';
            });
        });

        // 닉네임 입력 필드 변경 시
        elements.nicknameInput.addEventListener('input', function() {
            this.classList.remove('is-valid', 'is-invalid');
            elements.nicknameFeedback.className = 'feedback-message';
            elements.nicknameFeedback.textContent = '';
            if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = true;
        });
    }
}); 