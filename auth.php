<?php
// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// Firebase 설정 파일 포함
require_once __DIR__ . '/config/firebase/config.php';

// 세션 시작
session_start();

// 함수 포함 (다국어 함수 등)
include_once __DIR__ . '/includes/functions.php';

// 다국어 메시지 로드
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
$messages = require __DIR__ . "/resources/lang/{$currentLang}/messages.php";
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - 탑마케팅</title>
    
    <!-- CSS 파일 로드 -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    <link rel="stylesheet" href="/public/assets/css/loading-overlay.css">
    
    <style>
    /* 기본 스타일 */
    body {
        margin: 0;
        padding: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        line-height: 1.5;
        color: #333;
        background-color: #fff;
    }

    /* 에러 메시지 스타일 */
    .error-message {
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        display: none;
    }

    /* 로딩 오버레이 스타일 */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 20px;
        opacity: 1;
        visibility: visible;
        transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
    }

    .loading-overlay.hidden {
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
    }

    .loading-overlay .spinner {
        width: 60px;
        height: 60px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-overlay .loading-text {
        color: #3498db;
        font-size: 16px;
        font-weight: 500;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* 인증번호 입력 그룹 스타일 */
    .verification-input-group {
        position: relative;
        margin-bottom: 15px;
    }

    .verification-timer {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-size: 14px;
    }

    .verification-button-group {
        margin-top: 10px;
        text-align: center;
    }

    .verification-group {
        margin: 15px 0;
    }

    .register-complete-group {
        margin-top: 20px;
        text-align: center;
    }

    /* 인증번호 입력 필드 스타일 */
    </style>
</head>
<body>
<!-- 로딩 오버레이 -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">로딩 중...</div>
</div>

<?php
// 헤더 포함
include_once __DIR__ . '/includes/header.php';
?>

<main class="auth-container">
    <div class="auth-form-container">
        <div class="auth-header">
            <h1 id="authTitle">로그인</h1>
            <p id="authSubtitle">휴대폰 번호로 간편하게 로그인하세요</p>
        </div>
        <div class="auth-tabs">
            <button id="loginTab" class="auth-tab active">로그인</button>
            <button id="registerTab" class="auth-tab">회원가입</button>
        </div>
        <div class="auth-tab-content">
            <!-- 에러 메시지 표시 영역 -->
            <div id="errorMessage" class="error-message"></div>
            
            <!-- 로그인 폼 -->
            <form class="auth-form" id="loginForm">
                <div class="form-group">
                    <label for="loginPhone">전화번호</label>
                    <div class="phone-input-group">
                        <div class="country-select" id="loginCountrySelect">
                            <span id="loginCountryFlag">🇰🇷</span>
                            <span id="loginCountryCode">+82</span>
                        </div>
                        <div class="country-dropdown" id="loginCountryDropdown">
                            <div class="country-option" data-code="+82" data-flag="🇰🇷">대한민국 (+82)</div>
                            <div class="country-option" data-code="+1" data-flag="🇺🇸">미국 (+1)</div>
                            <div class="country-option" data-code="+86" data-flag="🇨🇳">중국 (+86)</div>
                            <div class="country-option" data-code="+886" data-flag="🇹🇼">대만 (+886)</div>
                            <div class="country-option" data-code="+81" data-flag="🇯🇵">일본 (+81)</div>
                        </div>
                        <input type="tel" id="loginPhone" name="phone" placeholder="010-1234-1234" required>
                    </div>
                </div>
                <div class="form-group verification-group" id="loginVerificationGroup" style="display:none;">
                    <label for="loginCode">인증번호</label>
                    <input type="text" id="loginCode" name="verificationCode" placeholder="인증번호 6자리" maxlength="6" pattern="\d*" inputmode="numeric">
                </div>
                <button type="button" class="auth-button" id="loginSendCodeBtn">인증번호 받기</button>
                <button type="submit" class="auth-button" id="loginSubmitBtn" style="opacity:0;transition:none;display:none;">로그인</button>
            </form>
            <!-- 회원가입 폼 -->
            <form class="auth-form" id="registerForm" style="display:none;">
                <div class="form-group">
                    <label for="registerPhone">전화번호</label>
                    <div class="phone-input-group">
                        <div class="country-select" id="registerCountrySelect">
                            <span id="registerCountryFlag">🇰🇷</span>
                            <span id="registerCountryCode">+82</span>
                        </div>
                        <div class="country-dropdown" id="registerCountryDropdown">
                            <div class="country-option" data-code="+82" data-flag="🇰🇷">대한민국 (+82)</div>
                            <div class="country-option" data-code="+1" data-flag="🇺🇸">미국 (+1)</div>
                            <div class="country-option" data-code="+86" data-flag="🇨🇳">중국 (+86)</div>
                            <div class="country-option" data-code="+886" data-flag="🇹🇼">대만 (+886)</div>
                            <div class="country-option" data-code="+81" data-flag="🇯🇵">일본 (+81)</div>
                        </div>
                        <input type="tel" id="registerPhone" name="phone" placeholder="010-1234-1234" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="registerNickname">닉네임</label>
                    <input type="text" id="registerNickname" placeholder="닉네임을 입력하세요 (2~20자)" required>
                    <small class="form-text text-muted">한글, 영문, 숫자만 사용 가능합니다.</small>
                </div>
                <div class="verification-message" style="display: none;"></div>
                <button type="button" class="auth-button" id="registerSendCodeBtn">인증번호 받기</button>
                <div class="verification-group" style="display: none;">
                    <div class="verification-message"></div>
                    <div class="verification-input-group">
                        <input type="text" id="verificationCode" class="form-control" placeholder="인증번호 6자리 입력" maxlength="6">
                        <div class="verification-timer"></div>
                    </div>
                    <div class="verification-button-group">
                        <button type="button" class="auth-button" id="verifyCodeBtn">인증번호 확인</button>
                    </div>
                </div>
                <div class="register-complete-group" style="display: none;">
                    <button type="button" class="auth-button" id="registerCompleteBtn">회원가입 완료</button>
                </div>
            </form>
        </div>
        <div class="auth-policy">
            <ul>
                <li>인증번호를 1시간 내 5회 이상 잘못 입력하실 경우, 24시간 동안 인증이 제한됩니다.</li>
            </ul>
        </div>
    </div>
</main>

<?php
// 푸터 포함
include_once __DIR__ . '/includes/footer.php';
?>

<!-- reCAPTCHA 컨테이너 -->
<div id="recaptcha-container"></div>

<!-- reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>

<!-- 인증 관련 스크립트 -->
<script src="/public/assets/js/firebase-config.js"></script>
<script src="/public/assets/js/loading-overlay.js"></script>
<script type="module">
    import { initializeElements } from '/public/assets/js/auth.js';
    
    // 즉시 실행
    (function() {
        // 로딩 오버레이 제어
        function setLoading(isLoading) {
            console.log('setLoading 호출됨:', isLoading);
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            console.log('로딩 오버레이 요소:', loadingOverlay);
            
            if (!loadingOverlay) {
                console.error('로딩 오버레이를 찾을 수 없습니다.');
                return;
            }
            
            if (isLoading) {
                loadingOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                console.log('로딩 오버레이 표시됨');
            } else {
                loadingOverlay.classList.add('hidden');
                document.body.style.overflow = '';
                console.log('로딩 오버레이 숨겨짐');
            }
        }

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
                    // reCAPTCHA 초기화 후 요소 초기화
                    initializeElements();
                });
            } else {
                console.error('reCAPTCHA ready 함수를 찾을 수 없습니다.');
            }
        }

        // 페이지 로드 시 초기 로딩 상태 설정
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded 이벤트 발생');
            setLoading(true);
            // reCAPTCHA 초기화 시작
            initRecaptcha();
        });

        // 페이지 완전 로드 시
        window.addEventListener('load', function() {
            console.log('window.load 이벤트 발생');
            // 타임아웃 시간을 500ms로 줄여 더 빨리 로딩 완료 표시
            setTimeout(() => {
                console.log('타이머 완료 - 로딩 오버레이 숨기기');
                setLoading(false);
            }, 500);
        });

        // 전역 스코프에 함수 노출
        window.setLoading = setLoading;

        // 초기 로그
        console.log('로딩 오버레이 스크립트 초기화 완료');
        
        // 즉시 로딩 오버레이 표시
        setLoading(true);
    })();
</script>
</body>
</html> 