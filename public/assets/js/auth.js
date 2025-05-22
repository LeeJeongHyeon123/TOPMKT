document.addEventListener('DOMContentLoaded', function() {
    console.log('[DEBUG] DOMContentLoaded 이벤트 발생');
    
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
    
    // 탭 버튼 및 폼 요소 가져오기
    const elements = {
        loginTab: document.getElementById('loginTab'),
        registerTab: document.getElementById('registerTab'),
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        sendCodeBtn: document.getElementById('sendCodeBtn'),
        loginSendCodeBtn: document.getElementById('loginSendCodeBtn'),
        loginSubmitBtn: document.getElementById('loginSubmitBtn'),
        registerSubmitBtn: document.getElementById('registerSubmitBtn'),
        nickname: document.getElementById('nickname'),
        registerPhone: document.getElementById('registerPhone'),
        loginPhone: document.getElementById('loginPhone'),
        registerCode: document.getElementById('registerCode'),
        loginCode: document.getElementById('loginCode'),
        registerCountrySelect: document.getElementById('registerCountrySelect'),
        registerCountryDropdown: document.getElementById('registerCountryDropdown'),
        registerCountryFlag: document.getElementById('registerCountryFlag'),
        registerCountryCode: document.getElementById('registerCountryCode'),
        loginCountrySelect: document.getElementById('loginCountrySelect'),
        loginCountryDropdown: document.getElementById('loginCountryDropdown'),
        loginCountryFlag: document.getElementById('loginCountryFlag'),
        loginCountryCode: document.getElementById('loginCountryCode'),
        errorMessage: document.getElementById('errorMessage'),
        registerVerificationGroup: document.getElementById('registerVerificationGroup'),
        loginVerificationGroup: document.getElementById('loginVerificationGroup'),
        authPolicy: document.getElementById('authPolicy'),
        authSubtitle: document.getElementById('authSubtitle'),
        idToken: document.getElementById('idToken')
    };
    
    console.log('[DEBUG] DOM 요소:', elements);

    // 국가별 전화번호 플레이스홀더 및 정규식
    const phonePlaceholders = {
        '+82': '010-1234-1234', // 한국
        '+1': '201-555-0123',  // 미국
        '+86': '138-1234-5678', // 중국
        '+886': '0912-345-678', // 대만
        '+81': '090-1234-5678'  // 일본
    };
    const phoneRegex = {
        '+82': /^01[0-9]{8,9}$/, // 한국
        '+1': /^[2-9][0-9]{2}[2-9][0-9]{2}[0-9]{4}$/, // 미국
        '+86': /^1[3-9][0-9]{9}$/, // 중국
        '+886': /^09[0-9]{8}$/, // 대만
        '+81': /^0[789]0[0-9]{8}$/ // 일본
    };

    // 에러/성공 메시지 표시 함수
    function showMessage(message, type = 'error') {
        if (!elements.errorMessage) {
            console.error('[ERROR] 에러 메시지 요소를 찾을 수 없음');
            return;
        }
        elements.errorMessage.textContent = message;
        elements.errorMessage.style.display = 'block';
        elements.errorMessage.style.color = (type === 'success') ? '#1976d2' : '#e74c3c';
    }
    
    function hideMessage() {
        if (!elements.errorMessage) {
            console.error('[ERROR] 에러 메시지 요소를 찾을 수 없음');
            return;
        }
        elements.errorMessage.textContent = '';
        elements.errorMessage.style.display = 'none';
    }

    // 전화번호 유효성 검사 함수
    function validatePhoneByCountry(phone, countryCode) {
        const regex = phoneRegex[countryCode];
        if (!regex) return false;
        return regex.test(phone.replace(/[^0-9]/g, ''));
    }

    // 국가 선택 드롭다운 초기화 함수
    function initCountryDropdown(selectElement, dropdownElement, flagElement, codeElement, phoneInput) {
        if (!selectElement || !dropdownElement || !flagElement || !codeElement || !phoneInput) {
            console.error('[ERROR] 국가 선택 드롭다운 요소를 찾을 수 없음');
            return;
        }
        
        console.log('[DEBUG] 국가 선택 드롭다운 초기화:', selectElement.id);
        
        // 드롭다운 토글
        selectElement.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownElement.style.display = (dropdownElement.style.display === 'block') ? 'none' : 'block';
        });
        
        // 옵션 클릭 시 선택 반영
        const countryOptions = dropdownElement.querySelectorAll('.country-option');
        if (countryOptions) {
            countryOptions.forEach(function(option) {
                option.addEventListener('click', function() {
                    flagElement.textContent = this.getAttribute('data-flag');
                    codeElement.textContent = this.getAttribute('data-code');
                    dropdownElement.style.display = 'none';
                    
                    // 플레이스홀더 변경
                    phoneInput.placeholder = phonePlaceholders[this.getAttribute('data-code')] || '전화번호 입력';
                });
            });
        }
    }

    // 전화번호 입력 포맷팅 함수
    function initPhoneNumberFormatting(phoneInput, countryCodeElement) {
        if (!phoneInput || !countryCodeElement) {
            console.error('[ERROR] 전화번호 입력 요소를 찾을 수 없음');
            return;
        }
        
        console.log('[DEBUG] 전화번호 입력 포맷팅 초기화:', phoneInput.id);
        
        phoneInput.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, ''); // 숫자만 남김
            const country = countryCodeElement.textContent.trim();
            let formatted = val;
            
            if (country === '+82') { // 한국
                if (val.length <= 3) {
                    formatted = val;
                } else if (val.length <= 7) {
                    formatted = val.slice(0,3) + '-' + val.slice(3);
                } else if (val.length <= 11) {
                    formatted = val.slice(0,3) + '-' + val.slice(3,7) + '-' + val.slice(7,11);
                }
            } else if (country === '+1') { // 미국
                if (val.length <= 3) {
                    formatted = val;
                } else if (val.length <= 6) {
                    formatted = val.slice(0,3) + '-' + val.slice(3);
                } else {
                    formatted = val.slice(0,3) + '-' + val.slice(3,6) + '-' + val.slice(6,10);
                }
            } else if (country === '+86') { // 중국
                if (val.length <= 3) {
                    formatted = val;
                } else if (val.length <= 7) {
                    formatted = val.slice(0,3) + '-' + val.slice(3);
                } else {
                    formatted = val.slice(0,3) + '-' + val.slice(3,7) + '-' + val.slice(7,11);
                }
            } else if (country === '+886') { // 대만
                if (val.length <= 4) {
                    formatted = val;
                } else if (val.length <= 7) {
                    formatted = val.slice(0,4) + '-' + val.slice(4);
                } else {
                    formatted = val.slice(0,4) + '-' + val.slice(4,7) + '-' + val.slice(7,10);
                }
            } else if (country === '+81') { // 일본
                if (val.length <= 3) {
                    formatted = val;
                } else if (val.length <= 7) {
                    formatted = val.slice(0,3) + '-' + val.slice(3);
                } else {
                    formatted = val.slice(0,3) + '-' + val.slice(3,7) + '-' + val.slice(7,11);
                }
            }
            this.value = formatted;
        });
    }

    // 인증번호 타이머 생성 함수
    function createVerificationTimer(codeInputParent, seconds = 180) {
        if (!codeInputParent) {
            console.error('[ERROR] 인증번호 입력 필드 부모 요소를 찾을 수 없음');
            return null;
        }
        
        // 정확한 부모 요소 확인
        // 타이머는 verification-input-group 내부에 위치해야 함
        let targetParent = codeInputParent;
        if (!targetParent.classList.contains('verification-input-group')) {
            targetParent = codeInputParent.querySelector('.verification-input-group');
            if (!targetParent) {
                console.error('[ERROR] verification-input-group을 찾을 수 없음');
                return null;
            }
        }
        
        // 기존 타이머 제거
        const existingTimer = document.querySelectorAll('.verification-timer');
        existingTimer.forEach(timer => {
            if (timer && timer.parentNode) {
                timer.parentNode.removeChild(timer);
            }
        });
        
        // 타이머 요소 생성
        const timerElement = document.createElement('span');
        timerElement.className = 'verification-timer';
        timerElement.style.position = 'absolute';
        timerElement.style.right = '10px';
        timerElement.style.top = '50%';
        timerElement.style.transform = 'translateY(-50%)';
        timerElement.style.color = '#dc3545';
        timerElement.style.fontSize = '14px';
        timerElement.style.fontWeight = '500';
        
        // 타이머 요소 추가
        targetParent.appendChild(timerElement);
        
        let timerSeconds = seconds;
        
        // 타이머 업데이트 함수
        const updateTimer = () => {
            const minutes = Math.floor(timerSeconds / 60);
            const seconds = timerSeconds % 60;
            timerElement.textContent = `유효시간 ${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timerSeconds <= 0) {
                clearInterval(timerInterval);
                timerElement.textContent = '인증번호가 만료되었습니다';
                return false;
            }
            
            timerSeconds--;
            return true;
        };
        
        // 초기 타이머 업데이트
        updateTimer();
        
        // 타이머 인터벌 설정
        const timerInterval = setInterval(updateTimer, 1000);
        
        return {
            element: timerElement,
            interval: timerInterval,
            stop: () => {
                clearInterval(timerInterval);
                timerElement.remove();
            }
        };
    }

    // 인증번호 발송 함수
    function sendVerificationCode(phoneInput, countryCodeElement, verificationGroup, isLogin = false) {
        if (!phoneInput || !countryCodeElement || !verificationGroup) {
            console.error('[ERROR] 인증번호 발송에 필요한 요소를 찾을 수 없음');
            showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
            return;
        }
        
        const phone = phoneInput.value.trim();
        const country = countryCodeElement.textContent.trim();
        
        if (!phone) {
            showMessage('휴대폰 번호를 입력하세요.');
            console.error('[ERROR] 휴대폰 번호 미입력');
            return;
        }
        
        if (!validatePhoneByCountry(phone, country)) {
            showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
            console.error('[ERROR] 전화번호 형식 오류', { phone, country });
            return;
        }
        
        // 닉네임 확인 (회원가입 시에만)
        if (!isLogin) {
            const nicknameValue = elements.nickname.value.trim();
            if (!nicknameValue) {
                showMessage('닉네임을 입력하세요.');
                console.error('[ERROR] 닉네임 미입력');
                return;
            }
        }
        
        const sendCodeBtn = isLogin ? elements.loginSendCodeBtn : elements.sendCodeBtn;
        
        if (sendCodeBtn) sendCodeBtn.disabled = true;
        showMessage('인증번호 발송 중...', 'success');
        
        if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
            showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
            if (sendCodeBtn) sendCodeBtn.disabled = false;
            console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
            return;
        }
        
        grecaptcha.enterprise.ready(function() {
            grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'send_code' })
                .then(async function(token) {
                    console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                    if (!token) {
                        showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                        if (sendCodeBtn) sendCodeBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 토큰 없음');
                        return;
                    }
                    try {
                        console.log('[DEBUG] 인증번호 fetch 요청', { phone, country, recaptcha_token: token });
                        const response = await fetch('/api/auth/send-code.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ phone, country: country, recaptcha_token: token })
                        });
                        const text = await response.text();
                        console.log('[DEBUG] 인증번호 fetch 응답', text);
                        if (!text) throw new Error('서버 오류: 빈 응답');
                        const data = JSON.parse(text);
                        
                        if (data.success) {
                            showMessage(data.message, 'success');
                            verificationGroup.style.display = '';
                            console.log('[DEBUG] 인증번호 발송 성공', data);
                            
                            // 인증번호 입력 필드
                            const codeInput = isLogin ? elements.loginCode : elements.registerCode;
                            const codeInputParent = isLogin ? 
                                document.querySelector('#loginVerificationGroup .verification-input-group') : 
                                document.querySelector('#registerVerificationGroup .verification-input-group');
                            
                            if (!codeInputParent) {
                                console.error('[ERROR] 인증번호 입력 필드의 부모 요소를 찾을 수 없음:', isLogin ? 'login' : 'register');
                                
                                // 로그인 버전의 경우 입력 필드를 감싸는 컨테이너 생성
                                if (isLogin && elements.loginCode && elements.loginCode.parentNode) {
                                    const parentNode = elements.loginCode.parentNode;
                                    const inputGroup = document.createElement('div');
                                    inputGroup.className = 'verification-input-group';
                                    
                                    // 기존 input 요소를 새 컨테이너로 이동
                                    parentNode.removeChild(elements.loginCode);
                                    inputGroup.appendChild(elements.loginCode);
                                    parentNode.appendChild(inputGroup);
                                    
                                    // 업데이트된 부모 요소
                                    const updatedParent = document.querySelector('#loginVerificationGroup .verification-input-group');
                                    if (updatedParent) {
                                        // 타이머 생성
                                        const timer = createVerificationTimer(updatedParent);
                                        window.verificationTimerInterval = timer.interval;
                                    }
                                }
                            } else {
                                // 타이머 생성
                                const timer = createVerificationTimer(codeInputParent);
                                window.verificationTimerInterval = timer.interval;
                            }
                            
                            // 완료 버튼 표시 (회원가입 시에만)
                            if (!isLogin && elements.registerCompleteGroup) {
                                elements.registerCompleteGroup.style.display = 'block';
                            }
                            
                            // 로그인 폼 제출 버튼 표시 (로그인 시에만)
                            if (isLogin && elements.loginSubmitBtn) {
                                // 인증번호 확인 버튼 생성
                                const verificationBtnGroup = document.createElement('div');
                                verificationBtnGroup.className = 'verification-button-group';
                                verificationBtnGroup.style.marginTop = '10px';
                                verificationBtnGroup.style.textAlign = 'center';
                                
                                const verifyLoginCodeBtn = document.createElement('button');
                                verifyLoginCodeBtn.type = 'button';
                                verifyLoginCodeBtn.className = 'auth-button secondary';
                                verifyLoginCodeBtn.id = 'verifyLoginCodeBtn';
                                verifyLoginCodeBtn.textContent = '인증번호 확인';
                                
                                verificationBtnGroup.appendChild(verifyLoginCodeBtn);
                                
                                // 버튼 그룹을 올바른 위치에 추가
                                const loginVerificationGroup = document.getElementById('loginVerificationGroup');
                                if (loginVerificationGroup) {
                                    loginVerificationGroup.appendChild(verificationBtnGroup);
                                } else {
                                    console.error('[ERROR] loginVerificationGroup을 찾을 수 없음');
                                    if (codeInputParent && codeInputParent.parentNode) {
                                        codeInputParent.parentNode.appendChild(verificationBtnGroup);
                                    }
                                }
                                
                                // 인증번호 확인 이벤트 추가
                                verifyLoginCodeBtn.addEventListener('click', function() {
                                    verifyCode(elements.loginPhone, elements.loginCountryCode, elements.loginCode, true);
                                });
                                
                                elements.loginSendCodeBtn.style.display = 'none';
                            }
                            
                            // sessionInfo 저장 (인증번호 확인에 필요)
                            if (data.data && data.data.sessionInfo) { // data.data.sessionInfo 로 경로 수정
                                localStorage.setItem('verification_session_info', data.data.sessionInfo);
                                console.log('[DEBUG] sessionInfo 저장 완료', data.data.sessionInfo.substring(0, 10) + '...');
                            } else {
                                console.log('[WARN] sessionInfo를 서버 응답의 data 필드에서 찾을 수 없음', data);
                            }
                            
                            // idToken 저장 및 전화번호 추출 (send-code.php 응답에는 보통 idToken이 없음, verify-code.php 응답에서 처리)
                            // 이 부분은 verifyCode 함수의 응답 처리 로직에 있어야 더 적합합니다.
                            // 만약 send-code.php 응답에도 idToken이 포함된다면 data.data.idToken으로 경로 수정 필요
                            try {
                                if (elements.idToken && data.data && data.data.idToken) { // data.data.idToken 으로 경로 수정 (만약 존재한다면)
                                    elements.idToken.value = data.data.idToken;
                                    console.log('[DEBUG] idToken 저장 완료 (send-code)', data.data.idToken.substring(0, 10) + '...');
                                    
                                    const tokenData = JSON.parse(atob(data.data.idToken.split('.')[1]));
                                    const tokenPhone = tokenData.phone_number || '';
                                    console.log('[DEBUG] 토큰에서 추출한 전화번호:', tokenPhone);
                                    
                                    // 추출한 전화번호 저장
                                    localStorage.setItem('verified_phone', tokenPhone);

                                    // 로그인을 위한 Firebase ID 토큰 저장
                                    localStorage.setItem('firebase_id_token', data.data.idToken);
                                }
                            } catch (err) {
                                console.error('[ERROR] 토큰 파싱 또는 전화번호 추출 오류:', err);
                            }
                            
                            // 인증번호 확인 버튼 활성화
                            const verifyCodeBtn = isLogin ? document.getElementById('verifyLoginCodeBtn') : document.getElementById('verifyCodeBtn');
                            if (verifyCodeBtn) verifyCodeBtn.disabled = false;
                            
                            // 인증번호 입력 필드 활성화
                            if (codeInput) codeInput.readOnly = false;
                        } else {
                            showMessage(data.message);
                            console.error('[ERROR] 인증번호 발송 실패', data);
                        }
                    } catch (err) {
                        showMessage('서버 오류: ' + err.message);
                        console.error('[ERROR] 인증번호 fetch 예외', err);
                    } finally {
                        if (sendCodeBtn) sendCodeBtn.disabled = false;
                    }
                })
                .catch(function(err) {
                    showMessage('reCAPTCHA 실행 오류: ' + err.message);
                    if (sendCodeBtn) sendCodeBtn.disabled = false;
                    console.error('[ERROR] reCAPTCHA 실행 오류', err);
                });
        });
    }

    // 인증번호 확인 함수
    // isLogin: true이면 로그인 시 인증, false이면 회원가입 시 인증
    async function verifyCode(phoneInput, countryCodeElement, codeInput, isLogin = false) {
        console.log(`[DEBUG] verifyCode 호출 시작. isLogin: ${isLogin}, 호출 스택(일부):`, (new Error()).stack.substring(0, 300)); // isLogin 값 및 호출 스택 일부 로그 추가

        const phone = phoneInput.value.replace(/[^0-9]/g, '');
        const country = countryCodeElement.textContent.trim();
        
        // 닉네임 확인 (회원가입 시에만)
        let nicknameValue = '';
        if (!isLogin && elements.nickname) {
            nicknameValue = elements.nickname.value.trim();
        }
        
        console.log('[DEBUG] 인증번호 확인 버튼 클릭', { 
            phone, 
            country, 
            code: codeInput.value.trim(),
            nickname: nicknameValue,
            isLogin: isLogin 
        });
        
        if (!phone || !codeInput.value.trim()) {
            showMessage('휴대폰 번호와 인증번호를 입력하세요.');
            console.error('[ERROR] 필수 항목 미입력', { phone, code: codeInput.value.trim() });
            return;
        }
        
        if (!validatePhoneByCountry(phone, country)) {
            showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
            console.error('[ERROR] 전화번호 형식 오류', { phone, country });
            return;
        }
        
        const verifyCodeBtn = isLogin ? document.getElementById('verifyLoginCodeBtn') : document.getElementById('verifyCodeBtn');
        
        if (verifyCodeBtn) verifyCodeBtn.disabled = true;
        showMessage('인증번호 확인 중...', 'success');
        
        if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
            showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
            if (verifyCodeBtn) verifyCodeBtn.disabled = false;
            console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
            return;
        }
        
        // 세션 정보 확인
        const sessionInfo = localStorage.getItem('verification_session_info');
        if (!sessionInfo) {
            showMessage('인증 세션 정보가 없습니다. 인증번호를 다시 요청해주세요.');
            if (verifyCodeBtn) verifyCodeBtn.disabled = false;
            console.error('[ERROR] 세션 정보 없음');
            return;
        }
        
        grecaptcha.enterprise.ready(function() {
            grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'verify_code' })
                .then(async function(token) {
                    console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                    if (!token) {
                        showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                        if (verifyCodeBtn) verifyCodeBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 토큰 없음');
                        return;
                    }
                    try {
                        console.log('[DEBUG] 인증번호 확인 fetch 요청', { phone, country, code: codeInput.value.trim(), sessionInfo });
                        const response = await fetch('/api/auth/verify-code.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ 
                                phone, 
                                country: country, 
                                code: codeInput.value.trim(), 
                                sessionInfo, 
                                recaptcha_token: token 
                            })
                        });
                        const text = await response.text();
                        console.log('[DEBUG] 인증번호 확인 fetch 응답', text);
                        if (!text) throw new Error('서버 오류: 빈 응답');
                        const data = JSON.parse(text);
                        
                        if (data.success) {
                            showMessage('✓ 인증이 완료되었습니다', 'success'); 
                            console.log('[DEBUG] 인증번호 확인 성공', data);

                            if (window.verificationTimerInterval) {
                                clearInterval(window.verificationTimerInterval);
                                console.log('[DEBUG] 인증번호 타이머 정지');
                            }

                            // idToken 저장 및 전화번호 추출
                            // sessionInfo는 이 단계의 응답에 없으므로 관련 로직 제거
                            try {
                                if (elements.idToken && data.idToken) { // 'data.data.idToken'에서 'data.idToken'으로 직접 접근
                                    elements.idToken.value = data.idToken;
                                    console.log('[DEBUG] idToken 저장 완료 (verifyCode)', data.idToken.substring(0, 10) + '...');
                                    
                                    // firebase_id_token을 localStorage에 저장 (로그인 시 사용)
                                    localStorage.setItem('firebase_id_token', data.idToken);
                                    console.log('[DEBUG] localStorage에 firebase_id_token 저장 완료');

                                    const tokenData = JSON.parse(atob(data.idToken.split('.')[1]));
                                    const tokenPhone = tokenData.phone_number || '';
                                    console.log('[DEBUG] 토큰에서 추출한 전화번호:', tokenPhone);
                                    
                                    // 추출한 전화번호 저장
                                    localStorage.setItem('verified_phone', tokenPhone);
                                    console.log('[DEBUG] localStorage에 verified_phone 저장 완료');
                                } else {
                                    console.log('[WARN] idToken을 서버 응답에서 찾을 수 없음', data);
                                }
                            } catch (err) {
                                console.error('[ERROR] 토큰 파싱 또는 전화번호 추출 오류 (verifyCode):', err);
                            }

                            // UI 업데이트
                            if (verifyCodeBtn) {
                                verifyCodeBtn.style.display = 'none';
                                console.log('[DEBUG] 인증번호 확인 버튼 숨김 처리 완료');
                                
                                // 인증 완료 메시지 표시
                                const verificationSuccessMsg = document.createElement('div');
                                verificationSuccessMsg.innerHTML = '<strong style="color: #28a745; font-size: 14px;">✓ 인증이 완료되었습니다</strong>';
                                verificationSuccessMsg.className = 'verification-success-message';
                                verificationSuccessMsg.style.marginTop = '10px';
                                verificationSuccessMsg.style.textAlign = 'center';
                                
                                // 버튼이 있던 위치에 메시지 표시
                                if (verifyCodeBtn.parentNode) {
                                    verifyCodeBtn.parentNode.appendChild(verificationSuccessMsg);
                                }
                            }
                            
                            // 휴대폰 번호 입력 필드 읽기 전용 설정
                            if (phoneInput) {
                                phoneInput.readOnly = true;
                                phoneInput.classList.add('verified-success');
                                console.log('[DEBUG] 휴대폰 번호 입력 필드 읽기 전용 설정 완료');
                            }
                            
                            // 국가 선택 드롭다운 비활성화
                            const countrySelect = isLogin ? elements.loginCountrySelect : elements.registerCountrySelect;
                            if (countrySelect) {
                                countrySelect.style.pointerEvents = 'none';
                                countrySelect.style.opacity = '0.7';
                                console.log('[DEBUG] 국가 선택 드롭다운 비활성화 완료');
                            }
                            
                            // 인증번호 받기 버튼 숨기기
                            const sendCodeBtn = isLogin ? elements.loginSendCodeBtn : elements.sendCodeBtn;
                            if (sendCodeBtn) {
                                sendCodeBtn.style.display = 'none';
                                console.log('[DEBUG] 인증번호 받기 버튼 숨김 처리 완료');
                            }
                            
                            // 인증번호 입력 필드 읽기 전용으로 설정
                            if (codeInput) {
                                codeInput.readOnly = true;
                                codeInput.classList.add('input-success');
                            }
                            
                            // 회원가입 완료 버튼 표시 (회원가입 시에만)
                            if (!isLogin) {
                                const registerCompleteGroup = document.getElementById('registerCompleteGroup');
                                if (registerCompleteGroup) {
                                    registerCompleteGroup.style.display = 'block';
                                }
                            }
                            
                            // 로그인 버튼 표시 (로그인 시에만)
                            if (isLogin && elements.loginSubmitBtn) {
                                elements.loginSubmitBtn.style.display = 'block';
                                elements.loginSubmitBtn.style.opacity = '1';
                                elements.loginSubmitBtn.style.transition = 'opacity 0.3s ease';
                            }
                        } else {
                            // 실패 시 서버에서 받은 정보 사용
                            let shouldDisableButton = false;
                            
                            // 차단되었거나 남은 시도 횟수가 0이면 버튼 비활성화
                            shouldDisableButton = (data.remainingAttempts !== undefined && data.remainingAttempts <= 0) || 
                                                  data.isBlocked === true;
                            
                            // 서버에서 받은 남은 시도 횟수 정보 출력
                            if (data.remainingAttempts !== undefined) {
                                console.log('[DEBUG] 서버에서 받은 남은 시도 횟수:', data.remainingAttempts, '차단 여부:', data.isBlocked);
                            }
                            
                            // 실패 메시지 표시
                            showMessage(data.message);
                            console.error('[ERROR] 인증번호 확인 실패', data);
                            
                            // 버튼 상태 업데이트
                            if (shouldDisableButton) {
                                if (verifyCodeBtn) {
                                    verifyCodeBtn.disabled = true;
                                    verifyCodeBtn.textContent = '인증 차단됨';
                                }
                                if (codeInput) codeInput.readOnly = true;
                            } else {
                                if (verifyCodeBtn) verifyCodeBtn.disabled = false;
                            }
                        }
                    } catch (err) {
                        showMessage('서버 오류: ' + err.message);
                        console.error('[ERROR] 인증번호 확인 fetch 예외', err);
                    } finally {
                        if (verifyCodeBtn && !verifyCodeBtn.disabled) {
                            verifyCodeBtn.disabled = false;
                        }
                    }
                })
                .catch(function(err) {
                    showMessage('reCAPTCHA 실행 오류: ' + err.message);
                    if (verifyCodeBtn) verifyCodeBtn.disabled = false;
                    console.error('[ERROR] reCAPTCHA 실행 오류', err);
                });
        });
    }

    // 바깥 클릭 시 드롭다운 닫기
    document.addEventListener('click', function() {
        if (elements.registerCountryDropdown) elements.registerCountryDropdown.style.display = 'none';
        if (elements.loginCountryDropdown) elements.loginCountryDropdown.style.display = 'none';
    });
    
    // 국가 선택 드롭다운 초기화
    initCountryDropdown(
        elements.registerCountrySelect, 
        elements.registerCountryDropdown, 
        elements.registerCountryFlag, 
        elements.registerCountryCode, 
        elements.registerPhone
    );
    
    initCountryDropdown(
        elements.loginCountrySelect, 
        elements.loginCountryDropdown, 
        elements.loginCountryFlag, 
        elements.loginCountryCode, 
        elements.loginPhone
    );
    
    // 전화번호 입력 포맷팅 초기화
    initPhoneNumberFormatting(elements.registerPhone, elements.registerCountryCode);
    initPhoneNumberFormatting(elements.loginPhone, elements.loginCountryCode);

    // 탭 클릭 이벤트
    if (elements.loginTab) {
        console.log('[DEBUG] 로그인 탭 요소 찾음, 클릭 리스너 추가');
        elements.loginTab.addEventListener('click', function() {
            console.log('[DEBUG] 로그인 탭 클릭됨');
            if (elements.loginTab) elements.loginTab.classList.add('active');
            if (elements.registerTab) elements.registerTab.classList.remove('active');
            if (elements.loginForm) elements.loginForm.style.display = '';
            if (elements.registerForm) elements.registerForm.style.display = 'none';
            
            // 서브타이틀 변경
            if (elements.authSubtitle) {
                console.log('[DEBUG] 서브타이틀 변경: 로그인');
                elements.authSubtitle.textContent = '휴대폰 번호로 간편하게 로그인하세요';
            } else {
                console.error('[ERROR] authSubtitle 요소를 찾을 수 없음');
            }
            
            hideMessage();

            // 회원가입 폼 관련 UI 초기화
            if (window.verificationTimerInterval) {
                clearInterval(window.verificationTimerInterval);
                const timerEl = document.querySelector('#registerVerificationGroup .verification-timer');
                if (timerEl && timerEl.parentNode) timerEl.parentNode.removeChild(timerEl);
            }
            const registerVerifyBtn = document.getElementById('verifyCodeBtn');
            if (registerVerifyBtn && registerVerifyBtn.parentNode) {
                 const successMsg = registerVerifyBtn.parentNode.querySelector('.verification-success-message');
                 if(successMsg) successMsg.remove();
                 registerVerifyBtn.style.display = 'block'; // 다시 보이도록 설정
                 registerVerifyBtn.disabled = false;
            }
            if(elements.registerCode) elements.registerCode.value = '';
            if(elements.registerVerificationGroup) elements.registerVerificationGroup.style.display = 'none';
            if(elements.sendCodeBtn) elements.sendCodeBtn.style.display = 'block'; // 인증번호 발송 버튼 다시 보이도록
            if(elements.registerPhone) {
                elements.registerPhone.readOnly = false;
                elements.registerPhone.classList.remove('verified-success');
            }
            if(elements.registerCountrySelect) {
                elements.registerCountrySelect.style.pointerEvents = 'auto';
                elements.registerCountrySelect.style.opacity = '1';
            }
            const registerCompleteGroup = document.getElementById('registerCompleteGroup');
            if (registerCompleteGroup) registerCompleteGroup.style.display = 'none';

        });
    } else {
        console.error('[ERROR] 로그인 탭 요소를 찾을 수 없음');
    }

    if (elements.registerTab) {
        console.log('[DEBUG] 회원가입 탭 요소 찾음, 클릭 리스너 추가');
        elements.registerTab.addEventListener('click', function() {
            console.log('[DEBUG] 회원가입 탭 클릭됨');
            if (elements.registerTab) elements.registerTab.classList.add('active');
            if (elements.loginTab) elements.loginTab.classList.remove('active');
            if (elements.registerForm) elements.registerForm.style.display = '';
            if (elements.loginForm) elements.loginForm.style.display = 'none';
            
            // 서브타이틀 변경
            if (elements.authSubtitle) {
                console.log('[DEBUG] 서브타이틀 변경: 회원가입');
                elements.authSubtitle.textContent = '휴대폰 번호로 간편하게 회원가입하세요';
            } else {
                console.error('[ERROR] authSubtitle 요소를 찾을 수 없음');
            }
            
            hideMessage();

            // 로그인 폼 관련 UI 초기화
            if (window.verificationTimerInterval) { // 로그인 타이머도 동일 변수 사용 가정
                clearInterval(window.verificationTimerInterval);
                const timerEl = document.querySelector('#loginVerificationGroup .verification-timer');
                if (timerEl && timerEl.parentNode) timerEl.parentNode.removeChild(timerEl);
            }
            const loginVerifyBtn = document.getElementById('verifyLoginCodeBtn');
            if (loginVerifyBtn && loginVerifyBtn.parentNode) loginVerifyBtn.parentNode.remove(); // 버튼 그룹 전체를 제거
            
            if(elements.loginCode) elements.loginCode.value = '';
            if(elements.loginVerificationGroup) elements.loginVerificationGroup.style.display = 'none';
            if(elements.loginSendCodeBtn) elements.loginSendCodeBtn.style.display = 'block';
            if(elements.loginSubmitBtn) elements.loginSubmitBtn.style.display = 'none';
            if(elements.loginPhone) {
                elements.loginPhone.readOnly = false;
                elements.loginPhone.classList.remove('verified-success');
            }
            if(elements.loginCountrySelect) {
                elements.loginCountrySelect.style.pointerEvents = 'auto';
                elements.loginCountrySelect.style.opacity = '1';
            }
        });
    } else {
        console.error('[ERROR] 회원가입 탭 요소를 찾을 수 없음');
    }

    // 닉네임 실시간 중복 체크
    if (elements.nickname) {
        console.log('[DEBUG] 닉네임 요소 찾음, blur 리스너 추가');
        
        // 닉네임 중복확인 버튼 클릭 이벤트
        const checkNicknameBtn = document.getElementById('checkNicknameBtn');
        if (checkNicknameBtn) {
            checkNicknameBtn.addEventListener('click', function() {
                const nicknameValue = elements.nickname.value.trim();
                if (!nicknameValue) {
                    showMessage('닉네임을 입력하세요.');
                    return;
                }
                
                console.log('[DEBUG] 닉네임 중복 체크 시작', { nickname: nicknameValue });
                
                // 중복확인 버튼 비활성화 및 로딩 표시
                checkNicknameBtn.disabled = true;
                checkNicknameBtn.textContent = '확인 중...';
                
                fetch('/api/auth/check-nickname.php?nickname=' + encodeURIComponent(nicknameValue))
                    .then(res => {
                        console.log('[DEBUG] 닉네임 중복 체크 응답 상태', res.status);
                        if (!res.ok) throw new Error('서버 오류');
                        return res.text();
                    })
                    .then(text => {
                        console.log('[DEBUG] 닉네임 중복 체크 응답 텍스트', text);
                        if (!text) throw new Error('서버 오류: 빈 응답');
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('[ERROR] JSON 파싱 실패', e);
                            throw new Error('서버 응답 형식 오류');
                        }
                    })
                    .then(data => {
                        console.log('[DEBUG] 닉네임 중복 체크 결과', data);
                        if (!data.success) {
                            throw new Error(data.message || '서버 오류');
                        }
                        
                        const nicknameFeedback = document.getElementById('nicknameFeedback');
                        
                        if (data.exists) {
                            // 이미 사용 중인 닉네임
                            showMessage('이미 사용 중인 닉네임입니다.');
                            if (elements.nickname) elements.nickname.classList.add('input-error');
                            if (nicknameFeedback) {
                                nicknameFeedback.textContent = '이미 사용 중인 닉네임입니다.';
                                nicknameFeedback.className = 'feedback-message error';
                            }
                        } else {
                            // 사용 가능한 닉네임
                            showMessage('사용 가능한 닉네임입니다.', 'success');
                            if (elements.nickname) {
                                elements.nickname.classList.remove('input-error');
                                elements.nickname.classList.add('input-success');
                                elements.nickname.readOnly = true;
                            }
                            
                            if (nicknameFeedback) {
                                nicknameFeedback.textContent = '사용 가능한 닉네임입니다.';
                                nicknameFeedback.className = 'feedback-message success';
                            }
                            
                            // 닉네임 수정 버튼 추가
                            checkNicknameBtn.textContent = '수정하기';
                            checkNicknameBtn.disabled = false;
                            checkNicknameBtn.onclick = function() {
                                if (elements.nickname) {
                                    elements.nickname.readOnly = false;
                                    elements.nickname.classList.remove('input-success');
                                    elements.nickname.focus();
                                }
                                checkNicknameBtn.textContent = '중복확인';
                                
                                // 휴대폰 번호 섹션 숨기기
                                const phoneSection = document.getElementById('phoneSection');
                                if (phoneSection) phoneSection.style.display = 'none';
                                
                                const codeButtonSection = document.getElementById('codeButtonSection');
                                if (codeButtonSection) codeButtonSection.style.display = 'none';
                                
                                // 정책 안내 메시지 숨기기
                                if (elements.authPolicy) elements.authPolicy.style.display = 'none';
                                
                                // sendCodeBtn 비활성화
                                if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = true;
                                
                                // 원래 중복확인 이벤트로 되돌리기
                                checkNicknameBtn.onclick = null;
                            };
                            
                            // 휴대폰 번호 섹션 표시
                            const phoneSection = document.getElementById('phoneSection');
                            if (phoneSection) {
                                console.log('[DEBUG] 휴대폰 번호 섹션 표시');
                                phoneSection.style.display = '';
                            } else {
                                console.error('[ERROR] 휴대폰 번호 섹션 요소를 찾을 수 없음');
                            }
                            
                            // 인증번호 받기 버튼 섹션 표시
                            const codeButtonSection = document.getElementById('codeButtonSection');
                            if (codeButtonSection) {
                                console.log('[DEBUG] 인증번호 받기 버튼 섹션 표시');
                                codeButtonSection.style.display = '';
                            } else {
                                console.error('[ERROR] 인증번호 받기 버튼 섹션 요소를 찾을 수 없음');
                            }
                            
                            // 정책 안내 메시지 표시
                            if (elements.authPolicy) {
                                console.log('[DEBUG] 정책 안내 메시지 표시');
                                elements.authPolicy.style.display = '';
                            } else {
                                console.error('[ERROR] 정책 안내 메시지 요소를 찾을 수 없음');
                            }
                            
                            // sendCodeBtn 활성화
                            if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error('[ERROR] 닉네임 중복 체크 실패', err);
                        showMessage('서버 오류: ' + err.message);
                        if (elements.nickname) elements.nickname.classList.add('input-error');
                        
                        const nicknameFeedback = document.getElementById('nicknameFeedback');
                        if (nicknameFeedback) {
                            nicknameFeedback.textContent = '서버 오류: ' + err.message;
                            nicknameFeedback.className = 'feedback-message error';
                        }
                    })
                    .finally(() => {
                        if (elements.nickname && elements.nickname.readOnly !== true) {
                            checkNicknameBtn.disabled = false;
                            checkNicknameBtn.textContent = '중복확인';
                        }
                    });
            });
        } else {
            console.error('[ERROR] 닉네임 중복확인 버튼 요소를 찾을 수 없음');
        }
        
        // 닉네임 입력 필드 이벤트
        elements.nickname.addEventListener('input', function() {
            const nicknameFeedback = document.getElementById('nicknameFeedback');
            if (nicknameFeedback) nicknameFeedback.textContent = '';
            
            // 입력 시 에러 클래스 제거
            if (elements.nickname) {
                elements.nickname.classList.remove('input-error');
                elements.nickname.classList.remove('input-success');
            }
            
            // 휴대폰 번호 섹션 숨기기 (닉네임 입력 중에는)
            if (elements.nickname && elements.nickname.readOnly !== true) {
                const phoneSection = document.getElementById('phoneSection');
                if (phoneSection) phoneSection.style.display = 'none';
                
                const codeButtonSection = document.getElementById('codeButtonSection');
                if (codeButtonSection) codeButtonSection.style.display = 'none';
                
                // 정책 안내 메시지 숨기기
                if (elements.authPolicy) elements.authPolicy.style.display = 'none';
                
                // sendCodeBtn 비활성화
                if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = true;
            }
        });
    } else {
        console.error('[ERROR] 닉네임 요소를 찾을 수 없음');
    }

    // 인증번호 발송 버튼 클릭 이벤트 - 회원가입
    if (elements.sendCodeBtn) {
        console.log('[DEBUG] 회원가입 인증번호 발송 버튼 요소 찾음, 클릭 리스너 추가');
        elements.sendCodeBtn.addEventListener('click', function() {
            sendVerificationCode(elements.registerPhone, elements.registerCountryCode, elements.registerVerificationGroup);
        });
    } else {
        console.error('[ERROR] 회원가입 인증번호 발송 버튼 요소를 찾을 수 없음');
    }

    // 인증번호 발송 버튼 클릭 이벤트 - 로그인
    if (elements.loginSendCodeBtn) {
        console.log('[DEBUG] 로그인 인증번호 발송 버튼 요소 찾음, 클릭 리스너 추가');
        elements.loginSendCodeBtn.addEventListener('click', function() {
            sendVerificationCode(elements.loginPhone, elements.loginCountryCode, elements.loginVerificationGroup, true);
        });
    } else {
        console.error('[ERROR] 로그인 인증번호 발송 버튼 요소를 찾을 수 없음');
    }

    // 인증번호 확인 버튼 클릭 이벤트 - 회원가입
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    if (verifyCodeBtn) {
        console.log('[DEBUG] 회원가입 인증번호 확인 버튼 요소 찾음, 클릭 리스너 추가');
        verifyCodeBtn.addEventListener('click', function() {
            // 이 시점에서 isLogin은 false여야 함.
            const intendedIsLogin = false; 
            console.log('[DEBUG] verifyCodeBtn 클릭됨. intendedIsLogin:', intendedIsLogin);
            verifyCode(elements.registerPhone, elements.registerCountryCode, elements.registerCode, intendedIsLogin);
        });
    } else {
        console.error('[ERROR] 회원가입 인증번호 확인 버튼 요소를 찾을 수 없음');
    }

    // 회원가입 폼 제출
    if (elements.registerForm) {
        console.log('[DEBUG] 회원가입 폼 요소 찾음, submit 리스너 추가');
        elements.registerForm.addEventListener('submit', async function(e) { // async 키워드 추가
            e.preventDefault();
            
            if (!elements.registerPhone || !elements.registerCountryCode || !elements.nickname || !elements.registerCode) {
                console.error('[ERROR] 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = elements.registerPhone.value.trim().replace(/-/g, ''); // 하이픈 제거
            const country = elements.registerCountryCode.textContent.trim();
            const nicknameValue = elements.nickname.value.trim();
            // const code = elements.registerCode.value.trim(); // 회원가입 시에는 이 code는 사용되지 않을 수 있음 (이미 verifyCode로 idToken 확보)
            
            // console.log('[DEBUG] 회원가입 폼 제출 시도', { phone, country, nickname: nicknameValue });
            
            // localStorage에서 인증된 E.164 형식 전화번호 가져오기
            const verifiedPhoneFromStorage = localStorage.getItem('verified_phone');
            if (!verifiedPhoneFromStorage) {
                showMessage('인증된 전화번호 정보를 찾을 수 없습니다. 페이지를 새로고침 후 다시 시도해주세요.');
                console.error('[ERROR] 회원가입 시도: localStorage에 verified_phone 없음.');
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                return;
            }
            const phoneForAPI = verifiedPhoneFromStorage; // E.164 형식 사용

            console.log('[DEBUG] 회원가입 폼 제출 시도', { phone: phoneForAPI, country, nickname: nicknameValue });

            // idToken이 있는지 확인 (verifyCode를 통해 이미 인증 완료된 상태여야 함)
            const idTokenFromStorage = localStorage.getItem('firebase_id_token');
            if (!idTokenFromStorage) {
                showMessage('인증 절차를 먼저 완료해주세요. (인증번호 확인)');
                console.error('[ERROR] 회원가입 시도: idToken 없음. verifyCode를 먼저 실행해야 합니다.');
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                return;
            }
            
            if (!phone || !nicknameValue) { // 인증코드는 idToken으로 대체되므로 체크 불필요
                showMessage('휴대폰 번호와 닉네임을 입력하세요.');
                console.error('[ERROR] 필수 항목 미입력 (회원가입 시)', { phone, nickname: nicknameValue });
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                return;
            }
            
            if (!validatePhoneByCountry(phone, country)) {
                showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
                console.error('[ERROR] 전화번호 형식 오류', { phone, country });
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                return;
            }
            
            if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = true;
            showMessage('회원가입 처리 중...', 'success');
            
            if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
                showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
                return;
            }
            
            try {
                const recaptchaToken = await grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'register' });
                console.log('[DEBUG] 회원가입 reCAPTCHA 토큰 발급', recaptchaToken);
                if (!recaptchaToken) {
                    showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                    if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                    console.error('[ERROR] reCAPTCHA 토큰 없음 (회원가입)');
                    return;
                }

                // register.php에 idToken을 보내 사용자 최종 등록
                const registerResponse = await fetch('/api/auth/register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        phone: phoneForAPI, // 수정된 부분: E.164 형식 전화번호 사용
                        country: country, 
                        nickname: nicknameValue, 
                        idToken: idTokenFromStorage, // verifyCode에서 저장한 idToken 사용
                        recaptcha_token: recaptchaToken 
                    })
                });

                const registerDataText = await registerResponse.text();
                console.log('[DEBUG] /api/auth/register.php 응답 텍스트:', registerDataText);
                const registerData = JSON.parse(registerDataText);
                console.log('[DEBUG] /api/auth/register.php 응답 JSON:', registerData);

                if (registerData.success) {
                    showMessage('회원가입 성공! 자동으로 로그인합니다.', 'success');
                    console.log('[DEBUG] 회원가입 성공, 서버 응답 데이터:', JSON.stringify(registerData.data, null, 2)); // 서버 응답 데이터 상세 로깅

                    // localStorage에 주요 정보 저장
                    if (registerData.data && registerData.data.user_id && registerData.data.nickname) {
                        localStorage.setItem('user_id', registerData.data.user_id);
                        localStorage.setItem('user_nickname', registerData.data.nickname);
                        console.log('[DEBUG] localStorage 저장: user_id =', registerData.data.user_id, ', user_nickname =', registerData.data.nickname);
                    } else {
                        console.warn('[WARN] registerData.data에 user_id 또는 nickname 없음:', registerData.data);
                    }
                    
                    // Firebase ID 토큰은 verifyCode 단계에서 이미 저장되어 있어야 하며,
                    // register.php 응답의 idToken은 verifyCode에서 사용한 토큰과 동일한지 확인용으로 볼 수 있음.
                    // 여기서는 localStorage에 있는 firebase_id_token을 다시 저장할 필요는 없음.
                    // 만약 서버가 새로운 세션 토큰(auth_token)을 발급한다면 여기서 저장.
                    if (registerData.data && registerData.data.idToken) { // sessionToken 대신 idToken 확인
                        localStorage.setItem('auth_token', registerData.data.idToken); // sessionToken 대신 idToken 사용
                         console.log('[DEBUG] localStorage 저장: auth_token =', registerData.data.idToken.substring(0,10) + "..."); // 로그도 idToken으로
                    } else if (registerData.data && registerData.data.sessionToken) { // 혹시 모를 sessionToken도 대비 (하위 호환 또는 다른 케이스)
                        localStorage.setItem('auth_token', registerData.data.sessionToken); 
                        console.log('[DEBUG] localStorage 저장 (fallback): auth_token from sessionToken =', registerData.data.sessionToken);
                    }


                    localStorage.setItem('login_timestamp', Date.now());
                    localStorage.setItem('keep_login', 'true'); // 자동 로그인 유지 플래그
                    console.log('[DEBUG] localStorage 저장: login_timestamp, keep_login 설정 완료');

                    // 저장된 값 확인 로깅
                    console.log('[DEBUG] localStorage 저장 확인: user_id =', localStorage.getItem('user_id'));
                    console.log('[DEBUG] localStorage 저장 확인: user_nickname =', localStorage.getItem('user_nickname'));
                    console.log('[DEBUG] localStorage 저장 확인: auth_token =', localStorage.getItem('auth_token'));
                    console.log('[DEBUG] localStorage 저장 확인: firebase_id_token =', localStorage.getItem('firebase_id_token') ? localStorage.getItem('firebase_id_token').substring(0,10) + "..." : "없음");
                    console.log('[DEBUG] localStorage 저장 확인: keep_login =', localStorage.getItem('keep_login'));
                    console.log('[DEBUG] localStorage 저장 확인: login_timestamp =', localStorage.getItem('login_timestamp'));


                    if (window.updateHeaderUI) {
                        console.log('[DEBUG] 회원가입 성공 후 window.updateHeaderUI 호출 시도');
                        window.updateHeaderUI();
                        console.log('[DEBUG] window.updateHeaderUI 호출 완료');
                    } else {
                        console.warn('[WARN] window.updateHeaderUI 함수를 찾을 수 없음');
                    }
                    
                    // 회원가입 성공 후 바로 메인 페이지로 리다이렉트 (또는 로그인된 상태로 현재 페이지 유지)
                    setTimeout(() => {
                        window.location.href = '/'; // 메인 페이지로 이동
                    }, 1500);

                } else {
                    // register.php 실패 시
                    showMessage(registerData.message || '회원가입에 실패했습니다. 다시 시도해주세요.');
                    console.error('[ERROR] /api/auth/register.php 처리 실패:', registerData);
                    if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                }

            } catch (err) {
                showMessage('회원가입 처리 중 오류 발생: ' + err.message);
                console.error('[ERROR] 회원가입 폼 제출 중 예외 발생:', err);
                if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
            }
        });
    } else {
        console.error('[ERROR] 회원가입 폼 요소를 찾을 수 없음');
    }

    // 로그인 폼 제출
    if (elements.loginForm) {
        console.log('[DEBUG] 로그인 폼 요소 찾음, submit 리스너 추가');
        elements.loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!elements.loginPhone || !elements.loginCountryCode || !elements.loginCode) {
                console.error('[ERROR] 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = elements.loginPhone.value.trim().replace(/[^0-9]/g, ''); // 숫자만 추출
            const country = elements.loginCountryCode.textContent.trim();
            const code = elements.loginCode.value.trim();
            console.log('[DEBUG] 로그인 폼 제출', { phone, country, code });
            
            if (!phone || !code) {
                showMessage('휴대폰 번호와 인증번호를 입력하세요.');
                console.error('[ERROR] 필수 항목 미입력', { phone, code });
                return;
            }
            
            if (!validatePhoneByCountry(phone, country)) {
                showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
                console.error('[ERROR] 전화번호 형식 오류', { phone, country });
                return;
            }
            
            if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = true;
            showMessage('로그인 처리 중...', 'success');
            
            if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
                showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
                if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
                return;
            }
            
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'login' })
                    .then(async function(token) {
                        console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                        if (!token) {
                            showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                            if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                            console.error('[ERROR] reCAPTCHA 토큰 없음');
                            return;
                        }
                        try {
                            // 세션 정보 확인
                            const sessionInfo = localStorage.getItem('verification_session_info') || '';
                            if (!sessionInfo) {
                                console.warn('[WARN] 세션 정보가 없습니다. 인증이 완료되지 않은 상태일 수 있습니다.');
                            }
                            
                            // 저장된 Firebase idToken 가져오기
                            const idToken = localStorage.getItem('firebase_id_token') || '';
                            if (!idToken) {
                                console.warn('[WARN] Firebase ID 토큰이 없습니다. 인증이 완료되지 않은 상태일 수 있습니다.');
                            }
                            
                            // 정규화된 전화번호 생성
                            const normalizedPhone = phone.replace(/[^0-9]/g, '');
                            
                            // API 요청 데이터 준비
                            const requestData = {
                                phone: normalizedPhone, 
                                country: country,
                                code: code,
                                sessionInfo: sessionInfo,
                                idToken: idToken,
                                recaptcha_token: token
                            };
                            
                            console.log('[DEBUG] 로그인 fetch 요청', requestData);
                            
                            try {
                                const response = await fetch('/api/auth/login.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/json' },
                                    body: JSON.stringify(requestData)
                                });
                                
                                console.log('[DEBUG] 로그인 응답 상태:', response.status);
                                console.log('[DEBUG] 로그인 응답 헤더:', {
                                    'content-type': response.headers.get('content-type'),
                                    'x-powered-by': response.headers.get('x-powered-by')
                                });
                                
                                // 서버 오류 (500)
                                if (response.status === 500) {
                                    console.error('[ERROR] 서버 내부 오류 발생 (500)');
                                    
                                    try {
                                        const text = await response.text();
                                        console.log('[DEBUG] 서버 오류 응답:', text);
                                        
                                        let errorData;
                                        try {
                                            errorData = JSON.parse(text);
                                            showMessage(errorData.message || '서버 내부 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                                        } catch (jsonError) {
                                            console.error('[ERROR] 서버 오류 응답이 JSON 형식이 아닙니다:', jsonError);
                                            showMessage('서버 내부 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                                        }
                                    } catch (readError) {
                                        console.error('[ERROR] 서버 오류 응답을 읽는 중 오류 발생:', readError);
                                        showMessage('서버 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                                    }
                                    
                                    if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                                    return;
                                }
                                
                                // 권한 오류 (403)
                                if (response.status === 403) {
                                    console.error('[ERROR] 접근 권한 없음 (403)');
                                    showMessage('서버 접근 권한이 없습니다. 관리자에게 문의하세요.');
                                    
                                    // 개발 환경일 경우 Firebase ID 토큰으로 직접 로그인 시도
                                    const idToken = localStorage.getItem('firebase_id_token');
                                    if (idToken) {
                                        console.log('[DEBUG] Firebase ID 토큰으로 직접 로그인 시도');
                                        
                                        try {
                                            // Firebase ID 토큰에서 사용자 정보 추출
                                            const tokenParts = idToken.split('.');
                                            if (tokenParts.length >= 2) {
                                                const base64 = tokenParts[1].replace(/-/g, '+').replace(/_/g, '/');
                                                const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                                                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                                                }).join(''));
                                                
                                                const payload = JSON.parse(jsonPayload);
                                                console.log('[DEBUG] ID 토큰 페이로드:', payload);
                                                
                                                if (payload && payload.user_id) {
                                                    // 사용자 정보 임시 설정
                                                    localStorage.setItem('user_id', payload.user_id);
                                                    localStorage.setItem('user_nickname', payload.phone_number || '사용자');
                                                    localStorage.setItem('login_timestamp', Date.now());
                                                    localStorage.setItem('keep_login', 'true');
                                                    localStorage.setItem('auth_token', idToken);
                                                    
                                                    // 로그인 성공 메시지
                                                    showMessage('토큰을 사용하여 로그인되었습니다.', 'success');
                                                    
                                                    // 홈으로 리다이렉션
                                                    setTimeout(() => {
                                                        window.location.href = '/';
                                                    }, 1500);
                                                    return;
                                                }
                                            }
                                        } catch (tokenError) {
                                            console.error('[ERROR] ID 토큰 처리 오류:', tokenError);
                                        }
                                    }
                                    
                                    if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                                    return;
                                }
                                
                                // 인증 실패 (401)
                                if (response.status === 401) {
                                    console.error('[ERROR] 인증 실패 (401)');
                                    
                                    try {
                                        const text = await response.text();
                                        console.log('[DEBUG] 인증 실패 응답:', text);
                                        
                                        let errorData;
                                        try {
                                            errorData = JSON.parse(text);
                                            showMessage(errorData.message || '인증에 실패했습니다. 인증번호를 확인해주세요.');
                                        } catch (jsonError) {
                                            console.error('[ERROR] 인증 실패 응답이 JSON 형식이 아닙니다:', jsonError);
                                            showMessage('인증에 실패했습니다. 인증번호를 확인해주세요.');
                                        }
                                    } catch (readError) {
                                        console.error('[ERROR] 인증 실패 응답을 읽는 중 오류 발생:', readError);
                                        showMessage('인증에 실패했습니다.');
                                    }
                                    
                                    if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                                    return;
                                }
                                
                                // 그 외 성공이 아닌 모든 응답
                                if (!response.ok) {
                                    console.error('[ERROR] API 오류 응답:', response.status, response.statusText);
                                    showMessage('서버 오류: ' + response.statusText);
                                    if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                                    return;
                                }
                                
                                // 응답 내용을 텍스트로 가져오기
                                const text = await response.text();
                                console.log('[DEBUG] 회원가입 fetch 응답 원본:', text);
                                
                                if (!text) {
                                    throw new Error('서버 오류: 빈 응답');
                                }
                                
                                // JSON 파싱 시도
                                let data;
                                try {
                                    data = JSON.parse(text);
                                    console.log('[DEBUG] 회원가입 fetch 응답(파싱 후):', data);
                                } catch (e) {
                                    console.error('[ERROR] JSON 파싱 실패:', e, text);
                                    console.error('[ERROR] 응답이 유효한 JSON 형식이 아닙니다:', text.substring(0, 100));
                                    throw new Error('서버 응답 형식 오류: 유효한 JSON이 아닙니다');
                                }
                                
                                if (data.success) {
                                    showMessage(data.message, 'success');
                                    
                                    // 사용자 데이터 확인
                                    if (data.data && data.data.user) {
                                        console.log('[DEBUG] 로그인 성공 - 사용자 정보:', data.data.user);
                                        localStorage.setItem('user_id', data.data.user.id);
                                        localStorage.setItem('user_nickname', data.data.user.nickname);
                                        
                                        // 로그인 시간 기록 (세션 유지를 위해)
                                        localStorage.setItem('login_timestamp', Date.now());
                                        
                                        // 로그아웃 전까지 로그인 상태 유지 플래그
                                        localStorage.setItem('keep_login', 'true');
                                    }
                                    
                                    // 세션 토큰 저장
                                    if (data.data && data.data.sessionToken) {
                                        localStorage.setItem('auth_token', data.data.sessionToken);
                                        console.log('[DEBUG] 세션 토큰 저장 완료');
                                    } else if (data.sessionToken) {
                                        localStorage.setItem('auth_token', data.sessionToken);
                                        console.log('[DEBUG] 세션 토큰 저장 완료');
                                    }
                                    
                                    // Firebase ID 토큰 저장
                                    if (data.data && data.data.token) {
                                        localStorage.setItem('firebase_id_token', data.data.token);
                                        console.log('[DEBUG] Firebase ID 토큰 업데이트 완료');
                                    } else if (data.idToken) {
                                        localStorage.setItem('firebase_id_token', data.idToken);
                                        console.log('[DEBUG] Firebase ID 토큰 업데이트 완료');
                                    }
                                    
                                    // 추가 확인: idToken이 localStorage에 저장되어 있는지 확인
                                    const storedToken = localStorage.getItem('firebase_id_token');
                                    console.log('[DEBUG] localStorage에 저장된 idToken 확인:', storedToken ? '저장됨' : '없음');
                                    
                                    // 리다이렉션
                                    setTimeout(() => {
                                        window.location.href = '/';
                                    }, 1500);
                                } else {
                                    showMessage(data.message || '로그인에 실패했습니다.');
                                    console.error('[ERROR] 로그인 실패', data);
                                    if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                                }
                            } catch (err) {
                                showMessage('서버 오류: ' + err.message);
                                console.error('[ERROR] 회원가입 fetch 예외', err);
                                if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                            }
                        } catch (err) {
                            showMessage('처리 오류: ' + err.message);
                            console.error('[ERROR] 로그인 처리 전체 예외', err);
                            if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                        }
                    })
                    .catch(function(err) {
                        showMessage('reCAPTCHA 실행 오류: ' + err.message);
                        if (elements.loginSubmitBtn) elements.loginSubmitBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 실행 오류', err);
                    });
            });
        });
    } else {
        console.error('[ERROR] 로그인 폼 요소를 찾을 수 없음');
    }
    
    // 에러 상황에서 사용할 직접 로그인 시도 함수
    function tryDirectLogin() {
        // Firebase ID 토큰 확인
        const idToken = localStorage.getItem('firebase_id_token');
        if (!idToken) {
            console.log('[INFO] 직접 로그인 시도: Firebase ID 토큰이 없습니다.');
            return false;
        }
        
        try {
            // 토큰에서 사용자 정보 추출 시도
            const tokenParts = idToken.split('.');
            if (tokenParts.length < 2) {
                console.log('[INFO] 직접 로그인 시도: 유효하지 않은 토큰 형식입니다.');
                return false;
            }
            
            const base64 = tokenParts[1].replace(/-/g, '+').replace(/_/g, '/');
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));
            
            const payload = JSON.parse(jsonPayload);
            console.log('[DEBUG] ID 토큰 페이로드:', payload);
            
            if (!payload || !payload.phone_number) {
                console.log('[INFO] 직접 로그인 시도: 토큰에서 전화번호를 찾을 수 없습니다.');
                return false;
            }
            
            // 토큰이 만료되었는지 확인
            const now = Math.floor(Date.now() / 1000);
            if (payload.exp && payload.exp < now) {
                console.log('[INFO] 직접 로그인 시도: 토큰이 만료되었습니다.', { expiry: payload.exp, now: now });
                return false;
            }
            
            // 사용자 정보 저장
            localStorage.setItem('user_id', payload.user_id || payload.sub || '');
            localStorage.setItem('user_nickname', payload.name || payload.phone_number || '사용자');
            localStorage.setItem('login_timestamp', Date.now());
            localStorage.setItem('keep_login', 'true');
            localStorage.setItem('auth_token', idToken);
            
            console.log('[INFO] 직접 로그인 성공: 토큰을 사용하여 로그인되었습니다.');
            return true;
        } catch (e) {
            console.error('[ERROR] 직접 로그인 시도 중 오류:', e);
            return false;
        }
    }
    
    // 페이지 로드 완료 후 자동 로그인 시도 (URL 파라미터에 auto_login=true가 있는 경우)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('auto_login') === 'true') {
        if (tryDirectLogin()) {
            // 성공 메시지 표시
            showToast('토큰을 사용하여 자동으로 로그인되었습니다.', 'success');
            
            // 리다이렉션
            setTimeout(() => {
                // auto_login 파라미터를 제거하고 홈으로 이동
                window.location.href = '/';
            }, 1500);
        }
    }

    // 초기화 함수
    function init() {
        console.log('[DEBUG] init() 함수 시작');
        // ... (다른 초기화 코드 생략) ...

        // 기존 이벤트 리스너 제거 (중복 방지)
        // verifyCodeBtn (회원가입)
        const oldVerifyCodeBtn = document.getElementById('verifyCodeBtn');
        if (oldVerifyCodeBtn) {
            console.log('[DEBUG] 기존 verifyCodeBtn 리스너 제거 시도');
            const newVerifyCodeBtn = oldVerifyCodeBtn.cloneNode(true);
            oldVerifyCodeBtn.parentNode.replaceChild(newVerifyCodeBtn, oldVerifyCodeBtn);
            elements.verifyCodeBtn = newVerifyCodeBtn; // elements 객체 업데이트
            console.log('[DEBUG] verifyCodeBtn 교체 완료. 새 버튼:', elements.verifyCodeBtn);
        } else {
            console.warn('[WARN] init()에서 verifyCodeBtn을 찾을 수 없음');
        }

        // verifyLoginCodeBtn (로그인)
        const oldVerifyLoginCodeBtn = document.getElementById('verifyLoginCodeBtn');
        if (oldVerifyLoginCodeBtn) {
            console.log('[DEBUG] 기존 verifyLoginCodeBtn 리스너 제거 시도');
            const newVerifyLoginCodeBtn = oldVerifyLoginCodeBtn.cloneNode(true);
            oldVerifyLoginCodeBtn.parentNode.replaceChild(newVerifyLoginCodeBtn, oldVerifyLoginCodeBtn);
            elements.verifyLoginCodeBtn = newVerifyLoginCodeBtn; // elements 객체 업데이트
            console.log('[DEBUG] verifyLoginCodeBtn 교체 완료. 새 버튼:', elements.verifyLoginCodeBtn);
        } else {
            // 로그인 시에는 동적으로 생성되므로, 이 시점에 없을 수 있음. 경고 수준 낮춤.
            console.log('[INFO] init()에서 verifyLoginCodeBtn을 찾을 수 없음 (정상일 수 있음)');
        }
        
        // 회원가입 인증번호 확인 버튼 이벤트 리스너
        if (elements.verifyCodeBtn) {
            console.log('[DEBUG] init()에서 회원가입 인증번호 확인 버튼 리스너 추가 시도');
            elements.verifyCodeBtn.addEventListener('click', async function(event) {
                event.preventDefault();
                console.log('[DEBUG] 회원가입 "인증번호 확인" 버튼 클릭됨 (init 리스너)');
                const phone = elements.registerPhone.value;
                const country = elements.registerCountryCode.textContent.trim();
                const code = elements.registerCode.value;
                const nickname = elements.nickname ? elements.nickname.value : '';
                // isLogin을 명시적으로 false로 설정
                console.log('[DEBUG] 회원가입 인증번호 확인 버튼 클릭 (verifyCode 호출 직전)', { phone, country, code, nickname, isLogin: false });
                await verifyCode(elements.registerPhone, elements.registerCountryCode, elements.registerCode, false); 
            });
            console.log('[DEBUG] init()에서 회원가입 인증번호 확인 버튼 리스너 추가 완료');
        } else {
            console.warn('[WARN] init()에서 elements.verifyCodeBtn을 찾을 수 없어 리스너를 추가하지 못함');
        }

        // 로그인 인증번호 확인 버튼 이벤트 리스너
        // 로그인 폼의 '인증번호 확인' 버튼은 sendVerificationCode 성공 후 동적으로 생성될 수 있으므로,
        // 이 시점에 리스너를 추가하는 것보다 sendVerificationCode 내에서 버튼 생성 시점에 추가하는 것이 더 안정적일 수 있음.
        // 그러나, 페이지 로드 시점에 버튼이 이미 존재할 경우를 대비하여 여기서도 시도.
        if (elements.verifyLoginCodeBtn) {
            console.log('[DEBUG] init()에서 로그인 인증번호 확인 버튼 리스너 추가 시도');
            elements.verifyLoginCodeBtn.addEventListener('click', async function(event) {
                event.preventDefault();
                console.log('[DEBUG] 로그인 "인증번호 확인" 버튼 클릭됨 (init 리스너)');
                const phone = elements.loginPhone.value;
                const country = elements.loginCountryCode.textContent.trim();
                const code = elements.loginCode.value;
                 // isLogin을 명시적으로 true로 설정
                console.log('[DEBUG] 로그인 인증번호 확인 버튼 클릭 (verifyCode 호출 직전)', { phone, country, code, isLogin: true });
                await verifyCode(elements.loginPhone, elements.loginCountryCode, elements.loginCode, true);
            });
            console.log('[DEBUG] init()에서 로그인 인증번호 확인 버튼 리스너 추가 완료');
        } else {
             console.log('[INFO] init()에서 elements.verifyLoginCodeBtn을 찾을 수 없음 (정상일 수 있음, 동적 생성 버튼)');
        }
        console.log('[DEBUG] init() 함수 종료');
    }

    // DOMContentLoaded 후 즉시 초기화 함수 호출
    init(); 

    // 페이지 로드 시 기본적으로 로그인 탭을 활성화 (만약 elements.loginTab이 존재한다면)
    if (elements.loginTab) {
        console.log('[DEBUG] 페이지 로드 시 로그인 탭 활성화 시도');
        elements.loginTab.click(); 
        console.log('[DEBUG] 페이지 로드 시 로그인 탭 활성화 완료');
    } else {
        console.error('[ERROR] 페이지 로드 시 loginTab 요소를 찾을 수 없음');
    }

}); 