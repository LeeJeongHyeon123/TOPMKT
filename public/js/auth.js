$(document).ready(function() {
    console.log("[DEBUG] auth.js loaded");

    const loginForm = $('#loginForm');
    const registerForm = $('#registerForm');
    const sendCodeBtn = $('#sendCodeBtn');
    const verifyCodeBtn = $('#verifyCodeBtn');
    const verificationCodeInput = $('#verificationCode');
    const phoneInput = $('#phone'); // 로그인 폼의 전화번호 입력 필드
    const registerPhoneInput = $('#registerPhone'); // 회원가입 폼의 전화번호 입력 필드

    let firebaseAuth;
    let recaptchaVerifier;
    let confirmationResult;

    // 국가 코드 포맷터 (libphonenumber-js 라이브러리 필요)
    function formatPhoneNumberToE164(phoneNumber, countryCode = 'KR') {
        try {
            const phoneUtil = window.libphonenumber.PhoneNumberUtil.getInstance();
            const number = phoneUtil.parseAndKeepRawInput(phoneNumber, countryCode);
            if (phoneUtil.isValidNumber(number)) {
                return phoneUtil.format(number, window.libphonenumber.PhoneNumberFormat.E164);
            }
            return null;
        } catch (e) {
            console.error("Error formatting phone number:", e);
            return null;
        }
    }
    
    async function initFirebaseAuth() {
        if (firebaseAuth) return; // 이미 초기화되었으면 반환

        try {
            console.log("[DEBUG] auth.js - Initializing Firebase Auth...");
            // Firebase 앱 초기화 (이미 초기화되어 있다면 이 부분은 앱 설정에 따라 달라질 수 있음)
            if (!firebase.apps.length) {
                // Firebase 설정 값은 실제 프로젝트의 값으로 대체해야 합니다.
                // 이 값들은 /js/firebase-config.js 와 같은 파일에서 로드하거나 직접 입력할 수 있습니다.
                // 여기서는 전역 변수 firebaseConfig가 있다고 가정합니다.
                if (typeof firebaseConfig === 'undefined') {
                    console.error('Firebase config is not defined.');
                    alert('Firebase 설정이 로드되지 않았습니다.');
                    return;
                }
                firebase.initializeApp(firebaseConfig);
                console.log("[DEBUG] auth.js - Firebase App initialized.");
            } else {
                console.log("[DEBUG] auth.js - Firebase App already initialized.");
            }
            
            firebaseAuth = firebase.auth();
            console.log("[DEBUG] auth.js - Firebase Auth instance created.");

            // reCAPTCHA verifier 초기화
            // reCAPTCHA 컨테이너가 항상 표시되도록 CSS 수정 필요할 수 있음
            // (예: visibility: hidden, display: none 대신 투명하게 처리)
            if ($('#recaptcha-container').length === 0) {
                console.log("[DEBUG] auth.js - recaptcha-container not found, creating one.");
                $('body').append('<div id="recaptcha-container" style="position: fixed; bottom: 0; right: 0; z-index: 9999;"></div>');
            }
            
            recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                'size': 'invisible', // 또는 'normal'
                'callback': function(response) {
                    // reCAPTCHA 해결됨.
                    console.log("[DEBUG] auth.js - reCAPTCHA solved, response:", response);
                },
                'expired-callback': function() {
                    // 응답 만료. 사용자가 다시 reCAPTCHA를 풀어야 함.
                    console.warn("[DEBUG] auth.js - reCAPTCHA expired.");
                    alert('reCAPTCHA 인증이 만료되었습니다. 다시 시도해주세요.');
                    recaptchaVerifier.render().then(function(widgetId) {
                        grecaptcha.reset(widgetId);
                    });
                }
            });
            console.log("[DEBUG] auth.js - RecaptchaVerifier initialized.");
            await recaptchaVerifier.render(); // reCAPTCHA 렌더링
            console.log("[DEBUG] auth.js - RecaptchaVerifier rendered.");

        } catch (error) {
            console.error("[DEBUG] auth.js - Firebase Auth initialization error:", error);
            alert('Firebase 인증 초기화 중 오류가 발생했습니다: ' + error.message);
        }
    }


    // 탭 전환 시 UI 초기화 및 이벤트 리스너 재설정 방지
    let isInitialized = false;

    function init() {
        if (isInitialized) return;
        isInitialized = true;

        console.log("[DEBUG] auth.js - init() called");

        // Firebase Auth 초기화
        initFirebaseAuth();


        // 인증번호 발송 버튼 이벤트 리스너
        sendCodeBtn.off('click').on('click', async function() {
            console.log("[DEBUG] auth.js - Send code button clicked");
            const currentForm = $(this).closest('form');
            const isLogin = currentForm.attr('id') === 'loginForm';
            const phoneField = isLogin ? phoneInput : registerPhoneInput;
            const phoneNumber = phoneField.val();

            if (!phoneNumber) {
                alert('전화번호를 입력해주세요.');
                return;
            }

            const e164PhoneNumber = formatPhoneNumberToE164(phoneNumber, 'KR');
            if (!e164PhoneNumber) {
                alert('유효한 전화번호 형식이 아닙니다. (예: 010-1234-5678 또는 01012345678)');
                return;
            }
            
            console.log(`[DEBUG] auth.js - Sending code to ${e164PhoneNumber}, isLogin: ${isLogin}`);

            if (!firebaseAuth || !recaptchaVerifier) {
                alert('Firebase 인증이 준비되지 않았습니다. 잠시 후 다시 시도해주세요.');
                console.error("[DEBUG] auth.js - firebaseAuth or recaptchaVerifier not ready.");
                await initFirebaseAuth(); // 재초기화 시도
                if(!firebaseAuth || !recaptchaVerifier) return; // 그래도 안되면 중단
            }
            
            try {
                $(this).prop('disabled', true).text('발송 중...');
                confirmationResult = await firebaseAuth.signInWithPhoneNumber(e164PhoneNumber, recaptchaVerifier);
                console.log("[DEBUG] auth.js - Code sent, confirmationResult:", confirmationResult);
                
                // 확인 결과를 localStorage에 임시 저장 (isLogin 정보 포함)
                localStorage.setItem('verification_session_info', JSON.stringify({ 
                    phoneNumber: e164PhoneNumber, 
                    isLoginContext: isLogin // 로그인 컨텍스트인지 회원가입 컨텍스트인지 저장
                }));

                alert('인증번호가 발송되었습니다.');
                verificationCodeInput.prop('disabled', false);
                verifyCodeBtn.prop('disabled', false);
                phoneField.prop('disabled', true); // 전화번호 수정 방지

            } catch (error) {
                console.error('[DEBUG] auth.js - Error sending verification code:', error);
                let errorMessage = '인증번호 발송 중 오류가 발생했습니다.';
                if (error.code === 'auth/too-many-requests') {
                    errorMessage = '요청 횟수가 너무 많습니다. 잠시 후 다시 시도해주세요.';
                } else if (error.code === 'auth/invalid-phone-number') {
                    errorMessage = '잘못된 전화번호 형식입니다.';
                } else if (error.message.includes('reCAPTCHA')) {
                    errorMessage = 'reCAPTCHA 확인에 실패했습니다. 페이지를 새로고침하고 다시 시도해주세요.';
                     // reCAPTCHA 재설정 시도
                    if (recaptchaVerifier) {
                        try {
                            const widgetId = recaptchaVerifier.widgetId; // 직접 접근은 비권장될 수 있음
                            if (widgetId !== undefined && grecaptcha && grecaptcha.reset) {
                                grecaptcha.reset(widgetId);
                                console.log("[DEBUG] auth.js - reCAPTCHA reset attempted.");
                            } else {
                                // 컨테이너를 다시 만들고 render
                                $('#recaptcha-container').remove();
                                $('body').append('<div id="recaptcha-container" style="position: fixed; bottom: 0; right: 0; z-index: 9999;"></div>');
                                await initFirebaseAuth(); // recaptchaVerifier 재초기화
                            }
                        } catch (rcError) {
                            console.error("[DEBUG] auth.js - Error resetting reCAPTCHA:", rcError);
                        }
                    }
                }
                alert(errorMessage);
            } finally {
                $(this).prop('disabled', false).text('인증번호 발송');
            }
        });

        // 인증번호 확인 버튼 이벤트 리스너
        verifyCodeBtn.off('click').on('click', async function() {
            console.log("[DEBUG] auth.js - Verify code button clicked");
            const code = verificationCodeInput.val();
            if (!code) {
                alert('인증번호를 입력해주세요.');
                return;
            }

            if (!confirmationResult) {
                alert('인증번호 발송을 먼저 진행해주세요.');
                return;
            }
            
            const currentForm = $(this).closest('form');
            const isLogin = currentForm.attr('id') === 'loginForm'; // 현재 폼 컨텍스트
            console.log(`[DEBUG] auth.js - Verifying code, isLogin context from button: ${isLogin}`);


            try {
                $(this).prop('disabled', true).text('확인 중...');
                const credential = await confirmationResult.confirm(code);
                const user = credential.user;
                console.log("[DEBUG] auth.js - Phone number verified, user:", user);
                
                const idToken = await user.getIdToken();
                console.log("[DEBUG] auth.js - Firebase ID Token:", idToken);

                // 저장된 세션 정보 가져오기
                const storedSessionInfo = JSON.parse(localStorage.getItem('verification_session_info'));
                if (!storedSessionInfo) {
                    alert('인증 세션 정보를 찾을 수 없습니다. 다시 시도해주세요.');
                    $(this).prop('disabled', false).text('인증번호 확인');
                    return;
                }
                const e164PhoneNumber = storedSessionInfo.phoneNumber;
                // const isLoginContextFromStorage = storedSessionInfo.isLoginContext; // 로그인 컨텍스트인지 회원가입 컨텍스트인지 확인

                // 서버로 idToken과 전화번호, isLogin 상태 전송하여 최종 처리
                // isLogin: true -> 로그인 시도 (login.php)
                // isLogin: false -> 회원가입 폼 활성화 준비 (register.php는 폼 제출 시 호출됨)
                $.ajax({
                    url: '/api/auth/verify-code.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ 
                        idToken: idToken, 
                        phone: e164PhoneNumber,
                        isLogin: isLogin // 현재 폼의 컨텍스트를 전달
                    }),
                    success: function(response) {
                        console.log('[DEBUG] auth.js - verify-code.php response:', response);
                        if (response.success) {
                            localStorage.setItem('verification_session_info', JSON.stringify({
                                idToken: idToken,
                                phoneNumber: e164PhoneNumber,
                                isVerified: true,
                                // isLoginContext: isLoginContextFromStorage // 원래 컨텍스트 유지
                            }));
                            alert('전화번호 인증이 완료되었습니다.');
                            
                            if (isLogin) {
                                // 로그인 시나리오: 바로 로그인 처리 또는 폼 자동 제출
                                console.log("[DEBUG] auth.js - Verified in login context. Attempting login.");
                                // 여기서 loginForm을 직접 submit 할 수도 있음.
                                // 또는 login.php에서 토큰 기반으로 바로 로그인 처리.
                                // 현재는 login.php가 id/pw를 받으므로, 인증 성공 후 사용자가 로그인 버튼을 누르게 함.
                                // 필드 활성화/비활성화 관리
                                $('#phone').prop('readonly', true);
                                $('#verificationCode').prop('readonly', true);
                                $('#sendCodeBtn').hide();
                                $('#verifyCodeBtn').hide();
                                $('#loginPassword').focus(); // 비밀번호 필드로 포커스
                                
                                // 로그인 폼에 필요한 정보 미리 채우거나, 로그인 버튼 활성화
                                // loginForm.submit(); // 이렇게 자동 제출도 가능
                            } else {
                                // 회원가입 시나리오: 회원가입 폼의 나머지 필드 활성화
                                console.log("[DEBUG] auth.js - Verified in register context. Enabling register form fields.");
                                $('#registerPhone').prop('readonly', true);
                                $('#verificationCode').prop('readonly', true);
                                $('#sendCodeBtn').hide(); // 회원가입 폼 내의 sendCodeBtn
                                $('#verifyCodeBtn').hide(); // 회원가입 폼 내의 verifyCodeBtn
                                $('#registerNickname').prop('disabled', false).focus();
                                $('#registerPassword').prop('disabled', false);
                                $('#registerPasswordConfirm').prop('disabled', false);
                                $('#keepLoginRegister').prop('disabled', false);
                                $('#registerSubmitBtn').prop('disabled', false);
                            }
                        } else {
                            alert('인증 실패: ' + response.message);
                            localStorage.removeItem('verification_session_info');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('[DEBUG] auth.js - Error verifying code with server:', xhr.responseText);
                        alert('서버 통신 중 오류가 발생했습니다: ' + (xhr.responseJSON ? xhr.responseJSON.message : '알 수 없는 오류'));
                        localStorage.removeItem('verification_session_info');
                    },
                    complete: function() {
                        verifyCodeBtn.prop('disabled', false).text('인증번호 확인');
                    }
                });

            } catch (error) {
                console.error('[DEBUG] auth.js - Error confirming code:', error);
                let errorMessage = '인증번호가 올바르지 않습니다.';
                if (error.code === 'auth/invalid-verification-code') {
                    errorMessage = '인증번호가 잘못되었습니다.';
                } else if (error.code === 'auth/code-expired') {
                    errorMessage = '인증번호가 만료되었습니다. 다시 요청해주세요.';
                }
                alert(errorMessage);
                verificationCodeInput.val('');
                verifyCodeBtn.prop('disabled', false).text('인증번호 확인');
                localStorage.removeItem('verification_session_info');
            }
        });
        
        loginForm.on('submit', function(e) {
            e.preventDefault();
            console.log("[DEBUG] auth.js - Login form submitted");
            const phone = $('#phone').val();
            const password = $('#password').val();
            const keepLogin = $('#keepLogin').is(':checked');

            // 전화번호 인증이 완료되었는지 확인 (idToken 기반)
            const verificationInfo = JSON.parse(localStorage.getItem('verification_session_info'));
            
            // 로그인 시에는 Firebase 인증 후 받은 idToken을 사용해야 함.
            // 하지만 현재 login.php는 id/pw 기반. 
            // 만약 verify-code.php의 isLogin:true에서 바로 로그인 처리를 한다면, 이 로직은 필요 없을 수 있음.
            // 여기서는 id/pw로 로그인하는 전통적인 방식을 따른다고 가정.
            // 전화번호 인증은 했지만, 최종 로그인은 id/pw로 하는 시나리오.
            // 만약 idToken만으로 로그인한다면, verify-code.php의 isLogin:true 성공 콜백에서 로그인 처리.
            
            console.log("[DEBUG] auth.js - Attempting login with phone/password");

            $.ajax({
                url: '/api/auth/login.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    phone: formatPhoneNumberToE164(phone, 'KR'), // E.164 형식으로 변환하여 전송
                    password: password 
                }),
                success: function(loginData) {
                    console.log('[DEBUG] auth.js - login success:', loginData);
                    if (loginData.success) {
                        localStorage.setItem('auth_token', loginData.data.sessionToken); // 서버에서 받은 세션 토큰
                        localStorage.setItem('user_id', loginData.data.user.id);
                        localStorage.setItem('user_nickname', loginData.data.user.nickname);
                        localStorage.setItem('keep_login', keepLogin ? 'true' : 'false');
                        localStorage.removeItem('verification_session_info'); // 로그인 성공 후 인증 세션 정보 삭제

                        alert('로그인 성공! 메인 페이지로 이동합니다.');
                        window.location.href = '/';
                    } else {
                        alert('로그인 실패: ' + loginData.message);
                        $('#loginPassword').val(''); // 비밀번호 필드 초기화
                    }
                },
                error: function(xhr, status, error) {
                    console.error("[DEBUG] auth.js - Login error:", xhr.responseText);
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        alert('로그인 오류: ' + (errorData.message || '알 수 없는 오류가 발생했습니다.'));
                    } catch (e) {
                        alert('로그인 중 오류가 발생했습니다. 서버 응답을 확인해주세요.');
                    }
                    $('#loginPassword').val(''); // 비밀번호 필드 초기화
                }
            });
        });

        registerForm.on('submit', async function(e) {
            e.preventDefault();
            console.log("[DEBUG] auth.js - Register form submitted");
            setLoading(true, 'register'); // 로딩 시작

            const nickname = $('#registerNickname').val();
            const password = $('#registerPassword').val(); // 현재 서버에서 사용 안함
            const passwordConfirm = $('#registerPasswordConfirm').prop('value');
            const keepLogin = $('#keepLoginRegister').is(':checked');

            if (password !== passwordConfirm) {
                alert('비밀번호가 일치하지 않습니다.');
                setLoading(false, 'register');
                return;
            }
            if (password.length < 6) { // 예시: 최소 6자리
                alert('비밀번호는 최소 6자리 이상이어야 합니다.');
                setLoading(false, 'register');
                return;
            }
            if (!nickname.trim()) {
                alert('닉네임을 입력해주세요.');
                setLoading(false, 'register');
                return;
            }

            const verificationSessionInfo = JSON.parse(localStorage.getItem('verification_session_info'));

            if (!verificationSessionInfo || !verificationSessionInfo.idToken || !verificationSessionInfo.isVerified || !verificationSessionInfo.phoneNumber) {
                alert('전화번호 인증이 완료되지 않았습니다. 먼저 인증을 완료해주세요.');
                setLoading(false, 'register');
                return;
            }

            const verifiedIdToken = verificationSessionInfo.idToken;
            const verifiedPhoneNumber = verificationSessionInfo.phoneNumber; // 인증된 E.164 전화번호 사용
            // 국가 코드는 verificationSessionInfo 에 저장되어 있지 않다면, 기본값 또는 다른 방법으로 설정해야 함
            const verifiedCountryCode = verificationSessionInfo.countryCode || '+82'; // 예시: KR에 해당하는 +82
            
            console.log("[DEBUG] auth.js - Registering with E.164 phone from session:", verifiedPhoneNumber, "Country Code:", verifiedCountryCode);

            // reCAPTCHA v3 토큰 가져오기 (회원가입용)
            let recaptchaTokenRegister;
            try {
                if (!window.grecaptcha || !window.grecaptcha.execute) {
                    console.error("grecaptcha.execute is not available. Make sure reCAPTCHA v3 is loaded.");
                    alert("보안 모듈 로드에 실패했습니다. 페이지를 새로고침 해주세요.");
                    setLoading(false, 'register');
                    return;
                }
                // firebase-config.js 에 firebaseConfig.recaptchaSiteKeyV3 가 정의되어 있다고 가정
                const siteKey = (window.firebaseConfig && window.firebaseConfig.recaptchaSiteKeyV3) ? window.firebaseConfig.recaptchaSiteKeyV3 : 'YOUR_RECAPTCHA_V3_SITE_KEY'; 
                if (siteKey === 'YOUR_RECAPTCHA_V3_SITE_KEY') {
                     console.warn('reCAPTCHA V3 Site Key is not configured in firebaseConfig.recaptchaSiteKeyV3. Using placeholder.');
                     // 프로덕션에서는 이 경우 오류 처리 또는 기본 키 사용 중단 필요
                }
                recaptchaTokenRegister = await grecaptcha.execute(siteKey, { action: 'register' });
                console.log('[DEBUG] 회원가입 reCAPTCHA 토큰 발급:', recaptchaTokenRegister ? recaptchaTokenRegister.substring(0, 30) + '...' : 'undefined');
            } catch (error) {
                console.error('[DEBUG] 회원가입 reCAPTCHA 토큰 발급 오류:', error);
                alert('보안 토큰 발급에 실패했습니다. 잠시 후 다시 시도해주세요.');
                setLoading(false, 'register');
                return;
            }

            if (!recaptchaTokenRegister) {
                alert('보안 토큰을 가져오지 못했습니다. 다시 시도해주세요.');
                setLoading(false, 'register');
                return;
            }

            const requestData = {
                phone: verifiedPhoneNumber, 
                country: verifiedCountryCode, 
                nickname: nickname,
                idToken: verifiedIdToken,     
                recaptcha_token: recaptchaTokenRegister 
            };

            console.log('[DEBUG] /api/auth/register.php 요청 데이터:', JSON.stringify(requestData, (key, value) => key === 'idToken' || key === 'recaptcha_token' ? (value ? value.substring(0,30) + '...': value) : value , 2));

            $.ajax({
                url: '/api/auth/register.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(requestData),
                success: function(registerData) {
                    console.log('[DEBUG] auth.js - register success RAW response:', registerData);
                    if (registerData && registerData.success && registerData.data) {
                        console.log('[DEBUG] auth.js - registerData.data before setting items:', registerData.data);
                        
                        localStorage.setItem('user_nickname', registerData.data.nickname);
                        localStorage.setItem('user_id', registerData.data.user_id); 
                        
                        const receivedIdToken = registerData.data.idToken; // 변수명 명확히
                        console.log('[DEBUG] auth.js - Received idToken from register.php response:', receivedIdToken);
                        
                        if (receivedIdToken) {
                            localStorage.setItem('auth_token', receivedIdToken); 
                        } else {
                            console.error('[FATAL DEBUG] auth.js - idToken is MISSING or NULL in registerData.data from register.php!');
                            localStorage.setItem('auth_token', 'ERROR_ID_TOKEN_WAS_NULL'); // 문제 발생 시 명확한 값으로 저장
                        }
                        
                        const keepLoginValue = $('#keepLoginRegister').is(':checked'); 
                        localStorage.setItem('keep_login', keepLoginValue ? 'true' : 'false');
                        
                        localStorage.removeItem('verification_session_info');
                        // localStorage.removeItem('firebase_id_token'); // 기존 firebase_id_token 키는 삭제 또는 주석처리 (auth_token으로 통일)
                        // localStorage.removeItem('verified_phone'); // 이것도 정리

                        console.log('[DEBUG] auth.js - localStorage user_id after set:', localStorage.getItem('user_id'));
                        console.log('[DEBUG] auth.js - localStorage auth_token after set:', localStorage.getItem('auth_token'));
                        console.log('[DEBUG] auth.js - localStorage keep_login after set:', localStorage.getItem('keep_login'));

                        alert('회원가입이 완료되었습니다. 메인 페이지로 이동합니다.');
                        // window.updateHeaderUI(); // 페이지 이동 전에 UI 업데이트 시도 (선택적)
                        window.location.href = '/';
                    } else {
                        let alertMsg = '회원가입 실패: ';
                        if (registerData && registerData.message) {
                            alertMsg += registerData.message;
                        } else if (registerData && registerData.data && registerData.data.message) {
                            alertMsg += registerData.data.message;
                        } else {
                            alertMsg += '알 수 없는 오류 또는 응답 형식 문제입니다.';
                        }
                        console.error('[DEBUG] auth.js - Register failed or malformed response:', registerData);
                        alert(alertMsg);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("[DEBUG] auth.js - Register error:", xhr.responseText);
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        alert('회원가입 오류: ' + (errorData.message || '알 수 없는 오류가 발생했습니다.'));
                    } catch (e) {
                        alert('회원가입 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.');
                    }
                },
                complete: function() {
                    setLoading(false, 'register'); // 로딩 종료
                }
            });
        });


        // 초기 탭 상태에 따라 UI 업데이트
        if ($('#login-tab').hasClass('active')) {
            console.log("[DEBUG] auth.js - Initial tab: Login");
            // 로그인 폼 관련 초기화 (필요시)
        } else if ($('#register-tab').hasClass('active')) {
            console.log("[DEBUG] auth.js - Initial tab: Register");
            // 회원가입 폼 관련 초기화 (필요시)
            $('#registerNickname').prop('disabled', true);
            $('#registerPassword').prop('disabled', true);
            $('#registerPasswordConfirm').prop('disabled', true);
            $('#keepLoginRegister').prop('disabled', true);
            $('#registerSubmitBtn').prop('disabled', true);
        }

        // 탭 변경 시 로컬 스토리지 정리 및 UI 초기화 (선택적)
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr("href"); // 활성화된 탭의 href (e.g., "#login")
            console.log(`[DEBUG] auth.js - Tab changed to: ${targetTab}`);
            
            // 이전 탭의 인증 컨텍스트 정리 (예: 전화번호 입력 필드 초기화 등)
            // localStorage.removeItem('verification_session_info'); // 탭 전환 시 무조건 삭제할 지 결정 필요
            // verificationCodeInput.val('').prop('disabled', true);
            // verifyCodeBtn.prop('disabled', true);
            // phoneInput.val('').prop('disabled', false).prop('readonly', false);
            // registerPhoneInput.val('').prop('disabled', false).prop('readonly', false);
            // sendCodeBtn.prop('disabled', false).text('인증번호 발송');

            if (targetTab === "#login") {
                console.log("[DEBUG] auth.js - Switched to Login tab");
                // 로그인 폼 UI 초기화
                $('#registerForm').find('input, button').prop('readonly', false).prop('disabled', false); // 회원가입 폼 필드 초기화 (혹시 모르니)
                $('#registerSubmitBtn').prop('disabled', true); // 가입 버튼은 초기에 비활성화
                 $('#registerNickname, #registerPassword, #registerPasswordConfirm, #keepLoginRegister').prop('disabled',true);


            } else if (targetTab === "#register") {
                console.log("[DEBUG] auth.js - Switched to Register tab");
                // 회원가입 폼 UI 초기화
                 $('#loginForm').find('input, button').prop('readonly', false).prop('disabled', false); // 로그인 폼 필드 초기화

                $('#registerNickname').prop('disabled', true);
                $('#registerPassword').prop('disabled', true);
                $('#registerPasswordConfirm').prop('disabled', true);
                $('#keepLoginRegister').prop('disabled', true);
                $('#registerSubmitBtn').prop('disabled', true);
            }
             // 공통 UI 초기화
            confirmationResult = null; // 이전 인증 결과 초기화
            if(recaptchaVerifier && recaptchaVerifier.clear) recaptchaVerifier.clear(); // reCAPTCHA 클리어
            
            // 현재 활성화된 폼의 전화번호 필드와 버튼들만 초기화
            const activeForm = $(`${targetTab}Form`); // 예: #loginForm 또는 #registerForm (ID 규칙 일치 필요)
            if (activeForm.length) {
                 activeForm.find('.phone-auth-field').val('').prop('disabled', false).prop('readonly', false); // 전화번호 입력 필드 (공통 클래스 사용 권장)
                 activeForm.find('.verification-code-field').val('').prop('disabled', true); // 인증번호 입력 필드
                 activeForm.find('.send-code-btn').prop('disabled', false).text('인증번호 발송');
                 activeForm.find('.verify-code-btn').prop('disabled', true).text('인증번호 확인');
            } else { // ID 규칙이 다를 경우 개별 처리
                if (targetTab === "#login") {
                    phoneInput.val('').prop('disabled', false).prop('readonly', false);
                    verificationCodeInput.filter('[form="loginForm"]').val('').prop('disabled', true); // 로그인 폼의 인증번호
                    sendCodeBtn.filter('[form="loginForm"]').prop('disabled', false).text('인증번호 발송');
                    verifyCodeBtn.filter('[form="loginForm"]').prop('disabled', true).text('인증번호 확인');
                } else if (targetTab === "#register") {
                    registerPhoneInput.val('').prop('disabled', false).prop('readonly', false);
                    verificationCodeInput.filter('[form="registerForm"]').val('').prop('disabled', true); // 회원가입 폼의 인증번호
                    sendCodeBtn.filter('[form="registerForm"]').prop('disabled', false).text('인증번호 발송');
                    verifyCodeBtn.filter('[form="registerForm"]').prop('disabled', true).text('인증번호 확인');
                }
            }

             // reCAPTCHA 재렌더링 (필요한 경우)
            if (recaptchaVerifier && typeof grecaptcha !== 'undefined' && grecaptcha.reset) {
                 try {
                    // 기존 위젯 ID로 리셋 시도
                    const widgetId = recaptchaVerifier.widgetId; 
                    if (widgetId !== undefined) {
                        grecaptcha.reset(widgetId);
                        console.log("[DEBUG] auth.js - reCAPTCHA reset on tab switch.");
                    } else {
                         recaptchaVerifier.render().then(() => console.log("[DEBUG] auth.js - reCAPTCHA re-rendered on tab switch."));
                    }
                } catch (e) {
                    console.warn("[DEBUG] auth.js - Minor error resetting reCAPTCHA on tab switch, might re-initialize.", e);
                    // 필요시 initFirebaseAuth() 다시 호출해서 verifier 재생성
                    initFirebaseAuth().then(() => console.log("[DEBUG] auth.js - Re-initialized Firebase Auth for reCAPTCHA."));
                }
            } else {
                // grecaptcha가 없거나 verifier가 제대로 초기화되지 않은 경우 대비
                initFirebaseAuth().then(() => console.log("[DEBUG] auth.js - Initialized Firebase Auth for reCAPTCHA on tab switch."));
            }
        });
    }

    // DOM 로드 후 즉시 초기화 함수 호출
    init();

    // libphonenumber-js 로드 확인 (선택적)
    if (typeof window.libphonenumber === 'undefined') {
        console.warn("libphonenumber-js is not loaded. Phone number formatting might not work as expected.");
        // 필요하다면 여기서 스크립트를 동적으로 로드할 수 있습니다.
    } else {
        console.log("[DEBUG] auth.js - libphonenumber-js loaded.", window.libphonenumber.PhoneNumberFormat.E164);
    }

}); 