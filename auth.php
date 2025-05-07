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
include_once 'includes/functions.php';

// 다국어 메시지 로드
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
$messages = require __DIR__ . "/resources/lang/{$currentLang}/messages.php";
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('auth.login.title') ?> - 탑마케팅</title>
    
    <!-- CSS 파일 링크 -->
    <link rel="stylesheet" href="/assets/css/auth.css">
    
    <style>
    /* 초기 스타일 - FOUC 방지 */
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        opacity: 1;
    }
    
    main {
        flex: 1;
        padding-top: 90px;
        padding-bottom: 40px;
    }
    
    .footer {
        background: linear-gradient(to right, #ffffff, #f8f9fa);
        padding: 40px 0 20px;
        text-align: center;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.03);
        margin-top: auto;
        opacity: 1;
    }
    
    .auth-button {
        padding: 12px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 4px;
        opacity: 1 !important;
        display: block !important;
    }

    .auth-button:hover {
        background-color: #0056b3;
    }

    #loginSubmitBtn, #registerSubmitBtn {
        opacity: 0;
        display: none;
        transition: opacity 0.2s ease-in-out;
    }

    .js-enabled #loginSubmitBtn, .js-enabled #registerSubmitBtn {
        opacity: 1;
        display: block;
    }
    </style>
</head>
<body>
<?php
// 헤더 포함
include_once 'includes/header.php';
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
document.addEventListener('DOMContentLoaded', function() {
    // 국가 선택 관련 함수
    function toggleCountryDropdown() {
        const dropdown = document.getElementById('countryDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
    }

    function selectCountry(countryCode, phoneCode, countryName) {
        const countryCodeEl = document.getElementById('countryCode');
        const countryDropdownEl = document.getElementById('countryDropdown');
        const phoneNumberEl = document.getElementById('phoneNumber');
        
        if (countryCodeEl) countryCodeEl.textContent = phoneCode;
        if (countryDropdownEl) countryDropdownEl.style.display = 'none';
        if (phoneNumberEl) phoneNumberEl.value = '';
    }

    // 전화번호 형식 지정
    const phoneNumberEl = document.getElementById('phoneNumber');
    if (phoneNumberEl) {
        phoneNumberEl.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length > 0) {
                if (value.startsWith('010')) {
                    if (value.length >= 3) {
                        value = value.slice(0, 3) + '-' + value.slice(3);
                    }
                    if (value.length >= 8) {
                        value = value.slice(0, 8) + '-' + value.slice(8);
                    }
                }
            }
            e.target.value = value;
        });
    }

    // 드롭다운 외부 클릭 시 닫기
    document.addEventListener('click', function(event) {
        const countryDropdown = document.getElementById('countryDropdown');
        if (countryDropdown && !event.target.closest('.country-select')) {
            countryDropdown.style.display = 'none';
        }
    });
});
</script>

<?php
// 푸터 포함
include_once 'includes/footer.php';
?> 