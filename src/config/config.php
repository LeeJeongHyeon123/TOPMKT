<?php
/**
 * 탑마케팅 기본 설정 파일
 */

// 기본 설정
define('APP_NAME', '탑마케팅');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'https://www.topmktx.com');
define('APP_DEBUG', true);

// 에러 로깅 설정
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . '/logs/php_errors.log');

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

// 네이버 Maps API 설정
define('NAVER_MAPS_CLIENT_ID', 'c5yj6m062z'); // 네이버 Maps API 클라이언트 ID
define('NAVER_MAPS_CLIENT_SECRET', 'ifjGgFsON2vMO2DiIFW1QLRBnEQ7l1j4w5CciajG'); // 네이버 Maps API 클라이언트 시크릿


// 알리고 SMS API 설정
define('ALIGO_API_KEY', 'ukqd7brex9cf9o3ggvy3bxr37brxxkm1'); // 알리고에서 발급받은 실제 API 키
define('ALIGO_USER_ID', 'neungsoft');                          // 알리고 실제 사용자 ID  
define('ALIGO_SENDER', '070-4136-8899');                       // 실제 등록된 발신번호
define('ALIGO_API_URL', 'https://apis.aligo.in/send/');        // 알리고 API URL
define('ALIGO_REMAIN_URL', 'https://apis.aligo.in/remain/');   // 잔여 건수 조회 URL

// SMS 실제 모드 설정 (Mock 모드 완전 제거)
define('SMS_MOCK_MODE', false);                        // 항상 실제 발송
define('SMS_TEST_PHONE', '010-2659-1346');            // 테스트용 전화번호 