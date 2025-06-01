<?php
/**
 * 탑마케팅 기본 설정 파일
 */

// 기본 설정
define('APP_NAME', '탑마케팅');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://www.topmktx.com');
define('APP_DEBUG', false);

// 타임존 설정
date_default_timezone_set('Asia/Seoul');

// 언어 설정
define('DEFAULT_LANG', 'ko');
define('SUPPORTED_LANGS', ['ko', 'en', 'zh', 'ja', 'vi', 'th']);

// 업로드 설정
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_PATH', ROOT_PATH . '/public/assets/uploads');
define('PROFILE_PATH', UPLOAD_PATH . '/profiles');

// 보안 설정
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_PREFIX', 'topmkt_');

// Firebase 설정
define('FIREBASE_API_KEY', '');
define('FIREBASE_AUTH_DOMAIN', '');
define('FIREBASE_DATABASE_URL', '');
define('FIREBASE_PROJECT_ID', ''); 