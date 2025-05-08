document.addEventListener('DOMContentLoaded', function() {
// JS 활성화 시 body에 js-enabled 클래스 추가
    document.body.classList.add('js-enabled');

    // reCAPTCHA 로드 콜백 함수
    window.onRecaptchaLoad = function() {
        console.log('reCAPTCHA Enterprise가 로드되었습니다.');
    };

    // 로그인/회원가입 탭 전환
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    if (loginTab && registerTab && loginForm && registerForm) {
        loginTab.addEventListener('click', () => {
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            loginForm.style.display = '';
            registerForm.style.display = 'none';
        });
        registerTab.addEventListener('click', () => {
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            registerForm.style.display = '';
            loginForm.style.display = 'none';
        });
    }

    // 인증번호 받기 버튼 클릭 시 reCAPTCHA 실행 및 인증번호 요청
    function sendCodeWithRecaptcha(phone, mode) {
        if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
            console.error('reCAPTCHA Enterprise가 로드되지 않았습니다.');
            alert('보안 검증을 초기화하는 중입니다. 잠시 후 다시 시도해주세요.');
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
                alert('보안 검증 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
            });
        });
    }

    // 로그인 인증번호 요청
    function requestLoginCode(phone, recaptchaToken) {
        const group = document.getElementById('loginVerificationGroup');
        const btn = document.getElementById('loginSubmitBtn');
        if (group) group.style.display = '';
        if (btn) btn.style.display = '';
        alert('인증번호가 전송되었습니다.');
    }
    // 회원가입 인증번호 요청
    function requestRegisterCode(phone, recaptchaToken) {
        const group = document.getElementById('registerVerificationGroup');
        const btn = document.getElementById('registerSubmitBtn');
        if (group) group.style.display = '';
        if (btn) btn.style.display = '';
        alert('인증번호가 전송되었습니다.');
    }

    // 로그인 인증번호 받기 버튼
    const loginSendCodeBtn = document.getElementById('loginSendCodeBtn');
    if (loginSendCodeBtn) {
        loginSendCodeBtn.addEventListener('click', function() {
            const phone = document.getElementById('loginPhone') ? document.getElementById('loginPhone').value.trim() : '';
            if (!phone) {
                alert('휴대폰 번호를 입력하세요.');
                return;
            }
            sendCodeWithRecaptcha(phone, 'login');
        });
    }
    // 회원가입 인증번호 받기 버튼
    const registerSendCodeBtn = document.getElementById('registerSendCodeBtn');
    if (registerSendCodeBtn) {
        registerSendCodeBtn.addEventListener('click', function() {
            const phone = document.getElementById('registerPhone') ? document.getElementById('registerPhone').value.trim() : '';
            if (!phone) {
                alert('휴대폰 번호를 입력하세요.');
                return;
            }
            sendCodeWithRecaptcha(phone, 'register');
        });
    }

    // 로그인 폼 제출(인증번호 확인 및 로그인)
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('로그인 처리(서버 연동 필요)');
        });
    }
    // 회원가입 폼 제출(인증번호 확인 및 회원가입)
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('회원가입 처리(서버 연동 필요)');
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
}); 