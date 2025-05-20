<?php
// 출력 버퍼링 시작
ob_start();

// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 세션 시작
session_start();

// Firebase 설정 파일 포함
require_once __DIR__ . '/../config/firebase/config.php';

// 함수 포함 (다국어 함수 등)
include_once __DIR__ . '/../includes/functions.php';

// 다국어 메시지 로드
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
$messages = require __DIR__ . "/../resources/lang/{$currentLang}/messages.php";

// 헤더 포함
include_once __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('auth.login.title') ?> - 탑마케팅</title>
    
    <!-- CSS 파일 링크 -->
    <link rel="stylesheet" href="/public/assets/css/auth.css">
    
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

    /* 로딩 오버레이 스타일 수정 */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.95);
        z-index: 999999;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 20px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
        pointer-events: none;
    }

    .loading-overlay.visible {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
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
    </style>

    <script>
    // 로딩 오버레이 제어
    function setLoading(isLoading) {
        const loadingOverlay = document.querySelector('.loading-overlay');
        if (!loadingOverlay) {
            console.error('로딩 오버레이를 찾을 수 없습니다.');
            return;
        }
        
        if (isLoading) {
            loadingOverlay.classList.add('visible');
            document.body.style.overflow = 'hidden';
        } else {
            loadingOverlay.classList.remove('visible');
            document.body.style.overflow = '';
        }
    }

    // 페이지 로드 시 초기 로딩 상태 설정
    document.addEventListener('DOMContentLoaded', function() {
        setLoading(true);
    });

    // 페이지 완전 로드 시
    window.addEventListener('load', function() {
        setTimeout(() => {
            setLoading(false);
        }, 500);
    });

    // 전역 스코프에 함수 노출
    window.setLoading = setLoading;
    </script>
</head>
<body>
<!-- 로딩 오버레이 -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
    <div class="loading-text">로딩 중...</div>
</div>

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
                </div>
                <div class="form-group verification-group" id="loginVerificationGroup" style="display:none;">
                    <label for="loginVerificationCode">인증번호</label>
                    <div class="verification-input-group">
                        <input type="text" id="loginVerificationCode" name="verification_code" placeholder="인증번호 6자리" required>
                        <div class="verification-timer"></div>
                    </div>
                </div>
                <button type="button" class="auth-button" id="loginSendCodeBtn">인증번호 받기</button>
                <button type="submit" class="auth-button" id="loginSubmitBtn" style="display:none;">로그인</button>
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
                </div>
                <div class="form-group">
                    <label for="registerNickname">닉네임</label>
                    <input type="text" id="registerNickname" name="nickname" placeholder="닉네임을 입력하세요 (2~20자)" required>
                    <small class="form-text text-muted">한글, 영문, 숫자만 사용 가능합니다.</small>
                </div>
                <div class="form-group verification-group" id="registerVerificationGroup" style="display:none;">
                    <label for="registerVerificationCode">인증번호</label>
                    <div class="verification-input-group">
                        <input type="text" id="registerVerificationCode" name="verification_code" placeholder="인증번호 6자리" required>
                        <div class="verification-timer"></div>
                    </div>
                </div>
                <button type="button" class="auth-button" id="registerSendCodeBtn">인증번호 받기</button>
                <button type="submit" class="auth-button" id="registerSubmitBtn" style="display:none;">회원가입</button>
            </form>
        </div>
    </div>
</main>

<!-- reCAPTCHA 컨테이너 -->
<div id="recaptcha-container"></div>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>

<!-- 인증 관련 스크립트 -->
<script src="/public/assets/js/firebase-config.js"></script>
<script src="/public/assets/js/auth.js"></script>

<?php
// 푸터 포함
include_once __DIR__ . '/../includes/footer.php';
?>
