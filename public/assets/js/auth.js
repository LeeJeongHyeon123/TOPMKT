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
        registerSubmitBtn: document.getElementById('registerSubmitBtn'),
        nickname: document.getElementById('nickname'),
        registerPhone: document.getElementById('registerPhone'),
        registerCode: document.getElementById('registerCode'),
        registerCountrySelect: document.getElementById('registerCountrySelect'),
        registerCountryDropdown: document.getElementById('registerCountryDropdown'),
        registerCountryFlag: document.getElementById('registerCountryFlag'),
        registerCountryCode: document.getElementById('registerCountryCode'),
        errorMessage: document.getElementById('errorMessage'),
        registerVerificationGroup: document.getElementById('registerVerificationGroup'),
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
        });
    } else {
        console.error('[ERROR] 회원가입 탭 요소를 찾을 수 없음');
    }

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

    // 인증번호 발송 버튼 클릭
    if (elements.sendCodeBtn) {
        console.log('[DEBUG] 인증번호 발송 버튼 요소 찾음, 클릭 리스너 추가');
        elements.sendCodeBtn.addEventListener('click', function() {
            if (!elements.registerPhone || !elements.registerCountryCode || !elements.nickname) {
                console.error('[ERROR] 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = elements.registerPhone.value.trim();
            const country = elements.registerCountryCode.textContent.trim();
            const nicknameValue = elements.nickname.value.trim();
            console.log('[DEBUG] 인증번호 발송 버튼 클릭', { phone, country, nickname: nicknameValue });
            
            // reCAPTCHA 디버깅 정보
            console.log('[DEBUG] window.grecaptcha:', window.grecaptcha);
            console.log('[DEBUG] typeof grecaptcha:', typeof grecaptcha);
            
            if (window.grecaptcha) {
                console.log('[DEBUG] typeof grecaptcha.enterprise:', typeof grecaptcha.enterprise);
                if (grecaptcha.enterprise) {
                    console.log('[DEBUG] typeof grecaptcha.enterprise.ready:', typeof grecaptcha.enterprise.ready);
                    console.log('[DEBUG] typeof grecaptcha.enterprise.execute:', typeof grecaptcha.enterprise.execute);
                    console.log('[DEBUG] grecaptcha.enterprise keys:', Object.keys(grecaptcha.enterprise));
                }
                console.log('[DEBUG] grecaptcha keys:', Object.keys(grecaptcha));
            } else {
                console.error('[ERROR] grecaptcha 객체가 window에 없습니다.');
            }
            
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
            
            if (!nicknameValue) {
                showMessage('닉네임을 입력하세요.');
                console.error('[ERROR] 닉네임 미입력');
                return;
            }
            
            if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = true;
            showMessage('인증번호 발송 중...', 'success');
            
            if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
                showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
                if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
                console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
                return;
            }
            
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'send_code' })
                    .then(async function(token) {
                        console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                        if (!token) {
                            showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                            if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
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
                                if (elements.registerVerificationGroup) elements.registerVerificationGroup.style.display = '';
                                if (elements.registerSubmitBtn) elements.registerSubmitBtn.style.display = '';
                                console.log('[DEBUG] 인증번호 발송 성공', data);
                                // 완료 버튼 표시
                                if (elements.registerCompleteGroup) {
                                    elements.registerCompleteGroup.style.display = 'block';
                                }
                                
                                // sessionInfo 저장 (인증번호 확인에 필요)
                                if (data.sessionInfo) {
                                    localStorage.setItem('verification_session_info', data.sessionInfo);
                                    console.log('[DEBUG] sessionInfo 저장 완료', data.sessionInfo.substring(0, 10) + '...');
                                } else {
                                    // 인증 성공 후에는 sessionInfo가 필요 없으므로 경고만 기록
                                    console.log('[WARN] 인증 성공 후 sessionInfo 없음 - 정상적인 상황');
                                }
                                
                                // idToken 저장 (인증 확인 시에만 받는 값이므로 여기서는 체크하지 않음)
                                if (elements.idToken && data.idToken) {
                                    elements.idToken.value = data.idToken;
                                    console.log('[DEBUG] idToken 저장 완료', data.idToken.substring(0, 10) + '...');
                                }
                                
                                if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                                if (elements.registerCode) elements.registerCode.readOnly = false;
                            } else {
                                showMessage(data.message);
                                console.error('[ERROR] 인증번호 발송 실패', data);
                            }
                        } catch (err) {
                            showMessage('서버 오류: ' + err.message);
                            console.error('[ERROR] 인증번호 fetch 예외', err);
                        } finally {
                            if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
                        }
                    })
                    .catch(function(err) {
                        showMessage('reCAPTCHA 실행 오류: ' + err.message);
                        if (elements.sendCodeBtn) elements.sendCodeBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 실행 오류', err);
                    });
            });
        });
    } else {
        console.error('[ERROR] 인증번호 발송 버튼 요소를 찾을 수 없음');
    }

    // 로그인 인증번호 발송 버튼 클릭
    if (elements.loginSendCodeBtn) {
        console.log('[DEBUG] 로그인 인증번호 발송 버튼 요소 찾음, 클릭 리스너 추가');
        elements.loginSendCodeBtn.addEventListener('click', function() {
            // 로그인 인증번호 발송 로직 추가
            console.log('[DEBUG] 로그인 인증번호 발송 버튼 클릭');
            
            const loginPhone = document.getElementById('loginPhone');
            const loginCountryCode = document.getElementById('loginCountryCode');
            
            if (!loginPhone || !loginCountryCode) {
                console.error('[ERROR] 로그인 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = loginPhone.value.trim();
            const country = loginCountryCode.textContent.trim();
            
            if (!phone) {
                showMessage('휴대폰 번호를 입력하세요.');
                return;
            }
            
            if (!validatePhoneByCountry(phone, country)) {
                showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
                return;
            }
            
            // 인증번호 발송 로직은 여기에 추가
        });
    }

    // 회원가입 폼 제출
    if (elements.registerForm) {
        console.log('[DEBUG] 회원가입 폼 요소 찾음, submit 리스너 추가');
        elements.registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!elements.registerPhone || !elements.registerCountryCode || !elements.nickname || !elements.registerCode) {
                console.error('[ERROR] 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = elements.registerPhone.value.trim();
            const country = elements.registerCountryCode.textContent.trim();
            const nicknameValue = elements.nickname.value.trim();
            const code = elements.registerCode.value.trim();
            console.log('[DEBUG] 회원가입 폼 제출', { phone, country, nickname: nicknameValue, code });
            
            // reCAPTCHA 디버깅 정보
            console.log('[DEBUG] window.grecaptcha:', window.grecaptcha);
            console.log('[DEBUG] typeof grecaptcha:', typeof grecaptcha);
            
            if (window.grecaptcha) {
                console.log('[DEBUG] typeof grecaptcha.enterprise:', typeof grecaptcha.enterprise);
                if (grecaptcha.enterprise) {
                    console.log('[DEBUG] typeof grecaptcha.enterprise.ready:', typeof grecaptcha.enterprise.ready);
                    console.log('[DEBUG] typeof grecaptcha.enterprise.execute:', typeof grecaptcha.enterprise.execute);
                    console.log('[DEBUG] grecaptcha.enterprise keys:', Object.keys(grecaptcha.enterprise));
                }
                console.log('[DEBUG] grecaptcha keys:', Object.keys(grecaptcha));
            } else {
                console.error('[ERROR] grecaptcha 객체가 window에 없습니다.');
            }
            
            if (!phone || !nicknameValue || !code) {
                showMessage('모든 필수 항목을 입력하세요.');
                console.error('[ERROR] 필수 항목 미입력', { phone, nickname: nicknameValue, code });
                return;
            }
            
            if (!validatePhoneByCountry(phone, country)) {
                showMessage('국가별 올바른 전화번호 형식이 아닙니다.');
                console.error('[ERROR] 전화번호 형식 오류', { phone, country });
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
            
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'register' })
                    .then(async function(token) {
                        console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                        if (!token) {
                            showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                            if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                            console.error('[ERROR] reCAPTCHA 토큰 없음');
                            return;
                        }
                        try {
                            console.log('[DEBUG] 회원가입 fetch 요청', { phone, country, nickname: nicknameValue, idToken: elements.idToken.value });
                            const response = await fetch('/api/auth/register.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ 
                                    phone, 
                                    country: country, 
                                    nickname: nicknameValue, 
                                    idToken: elements.idToken.value,
                                    recaptcha_token: token 
                                })
                            });
                            
                            console.log('[DEBUG] 회원가입 응답 상태:', response.status);
                            if (!response.ok) {
                                console.error('[ERROR] 회원가입 API 오류 응답:', response.status, response.statusText);
                            }
                            
                            const text = await response.text();
                            console.log('[DEBUG] 회원가입 fetch 응답', text);
                            if (!text) throw new Error('서버 오류: 빈 응답');
                            const data = JSON.parse(text);
                            if (data.success) {
                                showMessage(data.message, 'success');
                                setTimeout(() => {
                                    window.location.href = '/';
                                }, 2000);
                            } else {
                                showMessage(data.message);
                                console.error('[ERROR] 회원가입 실패', data);
                            }
                        } catch (err) {
                            showMessage('서버 오류: ' + err.message);
                            console.error('[ERROR] 회원가입 fetch 예외', err);
                        } finally {
                            if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                        }
                    })
                    .catch(function(err) {
                        showMessage('reCAPTCHA 실행 오류: ' + err.message);
                        if (elements.registerSubmitBtn) elements.registerSubmitBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 실행 오류', err);
                    });
            });
        });
    } else {
        console.error('[ERROR] 회원가입 폼 요소를 찾을 수 없음');
    }

    // 드롭다운 토글
    if (elements.registerCountrySelect && elements.registerCountryDropdown) {
        console.log('[DEBUG] 국가 선택 드롭다운 요소 찾음, 클릭 리스너 추가');
        elements.registerCountrySelect.addEventListener('click', function(e) {
            e.stopPropagation();
            if (elements.registerCountryDropdown) {
                elements.registerCountryDropdown.style.display = (elements.registerCountryDropdown.style.display === 'block') ? 'none' : 'block';
            }
        });
        
        // 옵션 클릭 시 선택 반영
        const countryOptions = elements.registerCountryDropdown.querySelectorAll('.country-option');
        if (countryOptions) {
            countryOptions.forEach(function(option) {
                option.addEventListener('click', function() {
                    if (elements.registerCountryFlag) elements.registerCountryFlag.textContent = this.getAttribute('data-flag');
                    if (elements.registerCountryCode) elements.registerCountryCode.textContent = this.getAttribute('data-code');
                    if (elements.registerCountryDropdown) elements.registerCountryDropdown.style.display = 'none';
                    // 플레이스홀더 변경
                    if (elements.registerPhone) {
                        elements.registerPhone.placeholder = phonePlaceholders[this.getAttribute('data-code')] || '전화번호 입력';
                    }
                });
            });
        }
        
        // 바깥 클릭 시 드롭다운 닫기
        document.addEventListener('click', function() {
            if (elements.registerCountryDropdown) elements.registerCountryDropdown.style.display = 'none';
        });
    } else {
        console.error('[ERROR] 국가 선택 드롭다운 요소를 찾을 수 없음');
    }

    // 전화번호 입력 시 국가별 하이픈 자동 포맷팅 및 숫자만 입력 제한
    if (elements.registerPhone && elements.registerCountryCode) {
        console.log('[DEBUG] 전화번호 입력 요소 찾음, input 리스너 추가');
        elements.registerPhone.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, ''); // 숫자만 남김
            const country = elements.registerCountryCode.textContent.trim();
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
    } else {
        console.error('[ERROR] 전화번호 입력 요소를 찾을 수 없음');
    }

    // 전화번호 유효성 검사 함수
    function validatePhoneByCountry(phone, countryCode) {
        const regex = phoneRegex[countryCode];
        if (!regex) return false;
        return regex.test(phone.replace(/[^0-9]/g, ''));
    }

    // 인증번호 확인 버튼 클릭
    const verifyCodeBtn = document.getElementById('verifyCodeBtn');
    if (verifyCodeBtn) {
        console.log('[DEBUG] 인증번호 확인 버튼 요소 찾음, 클릭 리스너 추가');
        verifyCodeBtn.addEventListener('click', function() {
            if (!elements.registerPhone || !elements.registerCountryCode || !elements.nickname || !elements.registerCode) {
                console.error('[ERROR] 필요한 요소를 찾을 수 없음');
                showToast('요소를 찾을 수 없습니다. 페이지를 새로고침 해주세요.', 'error');
                return;
            }
            
            const phone = elements.registerPhone.value.trim();
            const country = elements.registerCountryCode.textContent.trim();
            const nicknameValue = elements.nickname.value.trim();
            const code = elements.registerCode.value.trim();
            console.log('[DEBUG] 인증번호 확인 버튼 클릭', { phone, country, code, nickname: nicknameValue });
            
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
            
            // 로컬 스토리지 사용 제거 - 서버 측에서만 인증 시도 횟수 관리
            if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = true;
            showMessage('인증번호 확인 중...', 'success');
            
            if (!window.grecaptcha || !grecaptcha.enterprise || typeof grecaptcha.enterprise.ready !== 'function') {
                showMessage('reCAPTCHA Enterprise 스크립트가 정상적으로 로드되지 않았습니다. 새로고침 후 다시 시도해 주세요.');
                if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                console.error('[ERROR] grecaptcha.enterprise.ready is not a function');
                return;
            }
            
            // 세션 정보 확인 (이 부분은 유지)
            const sessionInfo = localStorage.getItem('verification_session_info');
            if (!sessionInfo) {
                showMessage('인증 세션 정보가 없습니다. 인증번호를 다시 요청해주세요.');
                if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                console.error('[ERROR] 세션 정보 없음');
                return;
            }
            
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4', { action: 'verify_code' })
                    .then(async function(token) {
                        console.log('[DEBUG] reCAPTCHA 토큰 발급', token);
                        if (!token) {
                            showMessage('reCAPTCHA 인증에 실패했습니다. 새로고침 후 다시 시도해 주세요.');
                            if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                            console.error('[ERROR] reCAPTCHA 토큰 없음');
                            return;
                        }
                        try {
                            console.log('[DEBUG] 인증번호 확인 fetch 요청', { phone, country, code, sessionInfo });
                            const response = await fetch('/api/auth/verify-code.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ 
                                    phone, 
                                    country: country, 
                                    code, 
                                    sessionInfo, 
                                    recaptcha_token: token 
                                })
                            });
                            const text = await response.text();
                            console.log('[DEBUG] 인증번호 확인 fetch 응답', text);
                            if (!text) throw new Error('서버 오류: 빈 응답');
                            const data = JSON.parse(text);
                            
                            if (data.success) {
                                showMessage(data.message, 'success');
                                console.log('[DEBUG] 인증번호 확인 성공', data);
                                
                                // 인증번호 확인 버튼 숨기기
                                if (elements.verifyCodeBtn) {
                                    elements.verifyCodeBtn.style.display = 'none';
                                }
                                
                                // 인증번호 받기 버튼 숨기기
                                if (elements.sendCodeBtn) {
                                    elements.sendCodeBtn.style.display = 'none';
                                }
                                
                                // 인증번호 입력 필드 비활성화
                                if (elements.registerCode) {
                                    elements.registerCode.readOnly = true;
                                    elements.registerCode.classList.add('input-success');
                                }
                                
                                // 회원가입 완료 버튼 추가 또는 표시
                                const registerCompleteGroup = document.getElementById('registerCompleteGroup');
                                if (registerCompleteGroup) {
                                    registerCompleteGroup.style.display = 'block';
                                } else {
                                    // 회원가입 완료 버튼이 없는 경우 생성
                                    const completeBtn = document.createElement('button');
                                    completeBtn.id = 'registerCompleteBtn';
                                    completeBtn.className = 'auth-button';
                                    completeBtn.textContent = '회원가입 완료';
                                    completeBtn.type = 'submit';
                                    
                                    // 폼에 버튼 추가
                                    if (elements.registerForm) {
                                        const submitGroup = document.createElement('div');
                                        submitGroup.id = 'registerCompleteGroup';
                                        submitGroup.className = 'form-group';
                                        submitGroup.style.marginTop = '20px';
                                        submitGroup.appendChild(completeBtn);
                                        
                                        elements.registerForm.appendChild(submitGroup);
                                        console.log('[DEBUG] 회원가입 완료 버튼 생성 및 추가');
                                    }
                                }
                                
                                // sessionInfo 저장 (인증번호 확인에 필요)
                                if (data.sessionInfo) {
                                    localStorage.setItem('verification_session_info', data.sessionInfo);
                                    console.log('[DEBUG] sessionInfo 저장 완료', data.sessionInfo.substring(0, 10) + '...');
                                } else {
                                    // 인증 성공 후에는 sessionInfo가 필요 없으므로 경고만 기록
                                    console.log('[WARN] 인증 성공 후 sessionInfo 없음 - 정상적인 상황');
                                }
                                
                                // idToken 저장 (인증 확인 시에만 받는 값이므로 여기서는 체크하지 않음)
                                if (elements.idToken && data.idToken) {
                                    elements.idToken.value = data.idToken;
                                    console.log('[DEBUG] idToken 저장 완료', data.idToken.substring(0, 10) + '...');
                                }
                                
                                if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                                if (elements.registerCode) elements.registerCode.readOnly = false;
                            } else {
                                // 실패 시 서버에서 받은 정보만 사용
                                let shouldDisableButton = false;
                                
                                // 차단되었거나 남은 시도 횟수가 0이면 버튼 비활성화
                                shouldDisableButton = (data.remainingAttempts !== undefined && data.remainingAttempts <= 0) || 
                                                      data.isBlocked === true;
                                
                                // 서버에서 받은 남은 시도 횟수 정보 출력
                                if (data.remainingAttempts !== undefined) {
                                    console.log('[DEBUG] 서버에서 받은 남은 시도 횟수:', data.remainingAttempts, '차단 여부:', data.isBlocked);
                                }
                                
                                // 실패 메시지 표시 (서버에서 받은 메시지 그대로 사용)
                                showMessage(data.message);
                                console.error('[ERROR] 인증번호 확인 실패', data);
                                
                                // 버튼 상태 업데이트
                                if (shouldDisableButton) {
                                    if (elements.verifyCodeBtn) {
                                        elements.verifyCodeBtn.disabled = true;
                                        elements.verifyCodeBtn.textContent = '인증 차단됨';
                                    }
                                    if (elements.registerCode) elements.registerCode.readOnly = true;
                                } else {
                                    if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                                }
                            }
                        } catch (err) {
                            showMessage('서버 오류: ' + err.message);
                            console.error('[ERROR] 인증번호 확인 fetch 예외', err);
                        } finally {
                            if (elements.verifyCodeBtn && !elements.verifyCodeBtn.disabled) {
                                elements.verifyCodeBtn.disabled = false;
                            }
                        }
                    })
                    .catch(function(err) {
                        showMessage('reCAPTCHA 실행 오류: ' + err.message);
                        if (elements.verifyCodeBtn) elements.verifyCodeBtn.disabled = false;
                        console.error('[ERROR] reCAPTCHA 실행 오류', err);
                    });
            });
        });
    } else {
        console.error('[ERROR] 인증번호 확인 버튼 요소를 찾을 수 없음');
    }
}); 