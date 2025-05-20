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
    <meta http-equiv="Content-Security-Policy" content="frame-ancestors 'self' https://www.google.com">
    <title><?= __('auth.login.title') ?> - 탑마케팅</title>
    
    <!-- CSS 파일 링크 -->
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
            <h1><?= __('auth.login.title') ?></h1>
            <p><?= __('auth.login.subtitle') ?></p>
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
                    <label for="loginPhone">휴대폰 번호</label>
                    <div class="phone-input-group">
                        <div class="country-select" id="loginCountrySelect">
                            <span id="loginCountryFlag">🇰🇷</span>
                            <span id="loginCountryCode">+82</span>
                        </div>
                        <input type="tel" id="loginPhone" name="phone" placeholder="010-1234-1234" required>
                    </div>
                    <div class="country-dropdown" id="loginCountryDropdown" style="display:none;">
                        <div class="country-option" data-flag="🇰🇷" data-code="+82">🇰🇷 한국 (+82)</div>
                        <div class="country-option" data-flag="🇺🇸" data-code="+1">🇺🇸 미국 (+1)</div>
                        <div class="country-option" data-flag="🇨🇳" data-code="+86">🇨🇳 중국 (+86)</div>
                        <div class="country-option" data-flag="🇹🇼" data-code="+886">🇹🇼 대만 (+886)</div>
                        <div class="country-option" data-flag="🇯🇵" data-code="+81">🇯🇵 일본 (+81)</div>
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
                    <label for="registerPhone">휴대폰 번호</label>
                    <div class="phone-input-group">
                        <div class="country-select" id="registerCountrySelect">
                            <span id="registerCountryFlag">🇰🇷</span>
                            <span id="registerCountryCode">+82</span>
                        </div>
                        <input type="tel" id="registerPhone" name="phone" placeholder="010-1234-1234" required>
                    </div>
                    <div class="country-dropdown" id="registerCountryDropdown" style="display:none;">
                        <div class="country-option" data-flag="🇰🇷" data-code="+82">🇰🇷 한국 (+82)</div>
                        <div class="country-option" data-flag="🇺🇸" data-code="+1">🇺🇸 미국 (+1)</div>
                        <div class="country-option" data-flag="🇨🇳" data-code="+86">🇨🇳 중국 (+86)</div>
                        <div class="country-option" data-flag="🇹🇼" data-code="+886">🇹🇼 대만 (+886)</div>
                        <div class="country-option" data-flag="🇯🇵" data-code="+81">🇯🇵 일본 (+81)</div>
                    </div>
                </div>
                <div class="form-group verification-group" id="registerVerificationGroup" style="display:none;">
                    <label for="registerCode">인증번호</label>
                    <input type="text" id="registerCode" name="verificationCode" placeholder="인증번호 6자리" maxlength="6" pattern="\d*" inputmode="numeric">
                </div>
                <div class="form-group">
                    <label for="registerNickname">닉네임</label>
                    <input type="text" id="registerNickname" name="nickname" placeholder="닉네임을 입력하세요" required>
                </div>
                <button type="button" class="auth-button" id="registerSendCodeBtn">인증번호 받기</button>
                <button type="submit" class="auth-button" id="registerSubmitBtn" style="opacity:0;transition:none;display:none;">회원가입</button>
            </form>
        </div>
        <div class="auth-policy">
            <ul>
                <li>인증번호를 1시간 내 5회 이상 잘못 입력하실 경우, 24시간 동안 인증이 제한됩니다.</li>
            </ul>
        </div>
    </div>
</main>

<!-- reCAPTCHA 컨테이너 -->
<div id="recaptcha-container"></div>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth.js"></script>
<!-- reCAPTCHA Enterprise -->
<script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
<!-- Firebase 설정 및 인증 스크립트 -->
<script src="/assets/js/firebase-config.js"></script>
<script src="/assets/js/auth.js"></script>

<script>
// 즉시 실행
(function() {
    // 로딩 오버레이 제어
    function setLoading(isLoading) {
        console.log('setLoading 호출됨:', isLoading);
        
        const loadingOverlay = document.querySelector('.loading-overlay');
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

    // 페이지 로드 시 초기 로딩 상태 설정
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded 이벤트 발생');
        setLoading(true);
    });

    // 페이지 완전 로드 시
    window.addEventListener('load', function() {
        console.log('window.load 이벤트 발생');
        setTimeout(() => {
            console.log('타이머 완료 - 로딩 오버레이 숨기기');
            setLoading(false);
        }, 1000);
    });

    // 전역 스코프에 함수 노출
    window.setLoading = setLoading;

    // 초기 로그
    console.log('로딩 오버레이 스크립트 초기화 완료');
    
    // 즉시 로딩 오버레이 표시
    setLoading(true);
})();
</script>

<?php
// 푸터 포함
include_once __DIR__ . '/includes/footer.php';
?>
