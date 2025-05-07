document.addEventListener('DOMContentLoaded', function() {
// JS 활성화 시 body에 js-enabled 클래스 추가
    document.body.classList.add('js-enabled');

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
                action: 'send_code',
                // 서드파티 쿠키 정책 대응을 위한 추가 옵션
                useRecaptchaNet: true,
                enterprise: true
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

    // 국가 선택 드롭다운 동작 (로그인/회원가입 공통)
    function setupCountrySelector(selectId, dropdownId, flagId, codeId) {
        const select = document.getElementById(selectId);
        const dropdown = document.getElementById(dropdownId);
        const flag = document.getElementById(flagId);
        const code = document.getElementById(codeId);
        if (!select || !dropdown || !flag || !code) return;

        select.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
        dropdown.querySelectorAll('.country-option').forEach(option => {
            option.addEventListener('click', function() {
                flag.textContent = this.dataset.flag;
                code.textContent = this.dataset.code;
                dropdown.style.display = 'none';
            });
        });
        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    setupCountrySelector('loginCountrySelect', 'loginCountryDropdown', 'loginCountryFlag', 'loginCountryCode');
    setupCountrySelector('registerCountrySelect', 'registerCountryDropdown', 'registerCountryFlag', 'registerCountryCode');

    // 전화번호 형식 지정 함수
    function formatPhoneNumber(value, countryCode) {
        // 숫자만 추출
        let numbers = value.replace(/[^\d]/g, '');
        
        // 국가별 형식 처리
        switch(countryCode) {
            case '+82': // 한국
                if (numbers.length > 0) {
                    // 010으로 시작하는 경우
                    if (numbers.startsWith('010')) {
                        if (numbers.length <= 3) {
                            return numbers;
                        } else if (numbers.length <= 7) {
                            return numbers.slice(0, 3) + '-' + numbers.slice(3);
                        } else {
                            return numbers.slice(0, 3) + '-' + numbers.slice(3, 7) + '-' + numbers.slice(7, 11);
                        }
                    }
                    // 10으로 시작하는 경우
                    else if (numbers.startsWith('10')) {
                        if (numbers.length <= 2) {
                            return numbers;
                        } else if (numbers.length <= 6) {
                            return numbers.slice(0, 2) + '-' + numbers.slice(2);
                        } else {
                            return numbers.slice(0, 2) + '-' + numbers.slice(2, 6) + '-' + numbers.slice(6, 10);
                        }
                    }
                    // 그 외의 경우 (0으로 시작하는 경우 0 제거)
                    else if (numbers.startsWith('0')) {
                        numbers = numbers.substring(1);
                        if (numbers.length <= 2) {
                            return numbers;
                        } else if (numbers.length <= 6) {
                            return numbers.slice(0, 2) + '-' + numbers.slice(2);
                        } else {
                            return numbers.slice(0, 2) + '-' + numbers.slice(2, 6) + '-' + numbers.slice(6, 10);
                        }
                    }
                }
                break;
            case '+1': // 미국/캐나다
                if (numbers.length > 0) {
                    if (numbers.length <= 3) {
                        return numbers;
                    } else if (numbers.length <= 6) {
                        return numbers.slice(0, 3) + '-' + numbers.slice(3);
                    } else {
                        return numbers.slice(0, 3) + '-' + numbers.slice(3, 6) + '-' + numbers.slice(6, 10);
                    }
                }
                break;
            case '+86': // 중국
                if (numbers.length > 0) {
                    if (numbers.length <= 3) {
                        return numbers;
                    } else if (numbers.length <= 7) {
                        return numbers.slice(0, 3) + '-' + numbers.slice(3);
                    } else {
                        return numbers.slice(0, 3) + '-' + numbers.slice(3, 7) + '-' + numbers.slice(7, 11);
                    }
                }
                break;
            case '+81': // 일본
                if (numbers.length > 0) {
                    if (numbers.length <= 2) {
                        return numbers;
                    } else if (numbers.length <= 6) {
                        return numbers.slice(0, 2) + '-' + numbers.slice(2);
                    } else {
                        return numbers.slice(0, 2) + '-' + numbers.slice(2, 6) + '-' + numbers.slice(6, 10);
                    }
                }
                break;
            case '+886': // 대만
                if (numbers.length > 0) {
                    if (numbers.length <= 2) {
                        return numbers;
                    } else if (numbers.length <= 6) {
                        return numbers.slice(0, 2) + '-' + numbers.slice(2);
                    } else {
                        return numbers.slice(0, 2) + '-' + numbers.slice(2, 6) + '-' + numbers.slice(6, 10);
                    }
                }
                break;
            default:
                return numbers;
        }
        return numbers;
    }

    // 전화번호 입력 이벤트 처리 함수
    function setupPhoneInput(inputId, countryCodeId) {
        const input = document.getElementById(inputId);
        const countryCode = document.getElementById(countryCodeId);
        
        if (input && countryCode) {
            input.addEventListener('input', function(e) {
                const value = e.target.value;
                const currentCountryCode = countryCode.textContent;
                
                // 숫자만 입력 가능하도록 처리
                if (!/^\d*$/.test(value.replace(/-/g, ''))) {
                    e.target.value = value.replace(/[^\d-]/g, '');
                    return;
                }
                
                // 형식 적용
                e.target.value = formatPhoneNumber(value, currentCountryCode);
            });

            // 붙여넣기 이벤트 처리
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const numbers = pastedText.replace(/[^\d]/g, '');
                const currentCountryCode = countryCode.textContent;
                e.target.value = formatPhoneNumber(numbers, currentCountryCode);
            });
        }
    }

    // 로그인과 회원가입 폼의 전화번호 입력 설정
    setupPhoneInput('loginPhone', 'loginCountryCode');
    setupPhoneInput('registerPhone', 'registerCountryCode');
}); 