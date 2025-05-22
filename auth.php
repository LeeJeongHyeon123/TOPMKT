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

// 함수 포함 (다국어 함수 등)
include_once __DIR__ . '/includes/functions.php';

// 다국어 메시지 로드
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
$messages = require __DIR__ . "/resources/lang/{$currentLang}/messages.php";

// 헤더 포함 (헤더에서 session_start()가 호출됨)
include_once __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인 - 탑마케팅</title>
    
    <!-- 파비콘 -->
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
    
    <!-- 웹 폰트 로드 -->
    <link rel="stylesheet" href="/public/assets/fonts/noto-sans-kr.css">
    
    <!-- CSS 파일 링크 -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    <link rel="stylesheet" href="/public/assets/css/loading-overlay.css">
    
    <!-- reCAPTCHA Enterprise -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-storage-compat.js"></script>
    
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
        color: #dc3545;
        font-size: 14px;
        font-weight: 600;
        z-index: 10;
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
    .nickname-input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 5px;
    }

    .nickname-input-group input {
        flex: 1;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
    }

    .check-button {
        padding: 12px 20px;
        background-color: #f8f9fa;
        border: 1px solid #ddd;
        border-radius: 8px;
        color: #495057;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .check-button:hover {
        background-color: #e9ecef;
    }

    .feedback-message {
        font-size: 13px;
        margin-top: 5px;
        min-height: 20px;
    }

    .feedback-message.success {
        color: #28a745;
    }

    .feedback-message.error {
        color: #dc3545;
    }
    
    /* 닉네임 입력 필드 스타일 */
    .input-error {
        border-color: #dc3545 !important;
    }
    
    .input-success {
        border-color: #28a745 !important;
    }
    
    /* 읽기 전용 필드 스타일 */
    input[readonly] {
        background-color: #f8f9fa;
        cursor: not-allowed;
    }

    /* 버튼 스타일 */
    .auth-button.primary {
        background-color: #1976d2;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.2s;
    }
    
    .auth-button.primary:hover {
        background-color: #1565c0;
    }
    
    .auth-button.secondary {
        background-color: #f8f9fa;
        color: #495057;
        border: 1px solid #ddd;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .auth-button.secondary:hover {
        background-color: #e9ecef;
    }
    
    .auth-button:disabled {
        background-color: #e9ecef;
        color: #adb5bd;
        cursor: not-allowed;
    }
    
    /* 인증 성공 상태 */
    .verified-success {
        border-color: #28a745 !important;
        background-color: #f8f9fa;
    }
    
    .verification-success-icon {
        color: #28a745;
        margin-left: 5px;
    }
    </style>
</head>
<body>
<!-- reCAPTCHA 컨테이너 -->
<div id="recaptcha-container"></div>

<!-- 로딩 오버레이 -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">로딩 중...</div>
</div>

<main class="auth-container">
    <div class="auth-form-container">
        <div class="auth-logo">
            <img src="/public/assets/images/logo.svg" alt="탑마케팅 로고" />
        </div>
        <div class="auth-tabs">
            <button id="loginTab" class="auth-tab active">로그인</button>
            <button id="registerTab" class="auth-tab">회원가입</button>
        </div>
        <div class="auth-tab-content">
            <!-- 에러 메시지 표시 영역 -->
            <div id="errorMessage" class="error-message"></div>
            
            <!-- 탭 제목 -->
            <h2 id="authSubtitle" class="auth-subtitle">휴대폰 번호로 간편하게 로그인하세요</h2>
            
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
                    <div class="verification-input-group">
                        <input type="text" id="loginCode" name="verificationCode" placeholder="인증번호 6자리" maxlength="6" pattern="\d*" inputmode="numeric">
                    </div>
                </div>
                <button type="button" class="auth-button" id="loginSendCodeBtn">인증번호 받기</button>
                <button type="submit" class="auth-button" id="loginSubmitBtn" style="opacity:0;transition:none;display:none;">로그인</button>
            </form>
            <!-- 회원가입 폼 -->
            <form class="auth-form" id="registerForm" style="display:none;">
                <input type="hidden" id="idToken" name="idToken">
                <div class="form-group">
                    <label for="nickname">닉네임</label>
                    <div class="nickname-input-group">
                        <input type="text" id="nickname" name="nickname" placeholder="닉네임을 입력하세요" required>
                        <button type="button" class="check-button" id="checkNicknameBtn">중복확인</button>
                    </div>
                    <div class="feedback-message" id="nicknameFeedback"></div>
                </div>
                <div class="form-group phone-section" id="phoneSection" style="display:none; position: relative; z-index: 50;">
                    <label for="registerPhone">휴대폰 번호</label>
                    <div class="phone-input-group">
                        <div class="country-select" id="registerCountrySelect">
                            <span id="registerCountryFlag">🇰🇷</span>
                            <span id="registerCountryCode">+82</span>
                        </div>
                        <input type="tel" id="registerPhone" name="phone" placeholder="010-1234-1234" required>
                    </div>
                    <div class="country-dropdown" id="registerCountryDropdown" style="display:none; z-index: 100;">
                        <div class="country-option" data-flag="🇰🇷" data-code="+82">🇰🇷 한국 (+82)</div>
                        <div class="country-option" data-flag="🇺🇸" data-code="+1">🇺🇸 미국 (+1)</div>
                        <div class="country-option" data-flag="🇨🇳" data-code="+86">🇨🇳 중국 (+86)</div>
                        <div class="country-option" data-flag="🇹🇼" data-code="+886">🇹🇼 대만 (+886)</div>
                        <div class="country-option" data-flag="🇯🇵" data-code="+81">🇯🇵 일본 (+81)</div>
                    </div>
                </div>
                <div class="form-group verification-group" id="registerVerificationGroup" style="display:none;">
                    <label for="registerCode">인증번호</label>
                    <div class="verification-input-group">
                        <input type="text" id="registerCode" name="verificationCode" placeholder="인증번호 6자리" maxlength="6" pattern="\d*" inputmode="numeric">
                        <span class="verification-timer" id="verificationTimer"></span>
                    </div>
                    <div class="verification-button-group">
                        <button type="button" class="auth-button secondary" id="verifyCodeBtn">인증번호 확인</button>
                    </div>
                </div>
                <div class="form-group code-button-section" id="codeButtonSection" style="display:none;">
                    <button type="button" class="auth-button" id="sendCodeBtn" disabled>
                        인증번호 받기
                    </button>
                </div>
                <div class="register-complete-group" id="registerCompleteGroup" style="display:none;">
                    <button type="submit" class="auth-button primary" id="registerSubmitBtn">회원가입 완료</button>
                </div>
            </form>
        </div>
        <div class="auth-policy" id="authPolicy" style="display:none;">
            <ul>
                <li>인증번호를 1시간 내 5회 이상 잘못 입력하실 경우, 24시간 동안 인증이 제한됩니다.</li>
            </ul>
        </div>
    </div>
</main>

<!-- Firebase 설정 및 인증 스크립트 -->
<script src="/public/assets/js/firebase-config.js"></script>
<script src="/public/assets/js/auth.js"></script>
<script src="/public/assets/js/main.js"></script>

<?php
// 푸터 포함
include_once __DIR__ . '/includes/footer.php';
?>
</body>
</html>
