<?php
/**
 * Firebase 설정 로더
 * 
 * 이 파일은 중앙 Firebase 설정 파일을 로드하여 필요한 상수 및 변수를 정의합니다.
 * 
 * @version 1.0.0
 * @author TOPMKT Development Team
 */

// 직접 접근 방지
defined('TOPMKT') or define('TOPMKT', true);

// 통합 Firebase 설정 로드
$firebase_settings = require_once __DIR__ . '/../firebase-config.php';

// 하위 호환성을 위한 상수 정의
define('FIREBASE_API_KEY', $firebase_settings['config']['apiKey']);
define('FIREBASE_AUTH_DOMAIN', $firebase_settings['config']['authDomain']);
define('FIREBASE_PROJECT_ID', $firebase_settings['config']['projectId']);
define('FIREBASE_STORAGE_BUCKET', $firebase_settings['config']['storageBucket']);
define('FIREBASE_MESSAGING_SENDER_ID', $firebase_settings['config']['messagingSenderId']);
define('FIREBASE_APP_ID', $firebase_settings['config']['appId']);
define('FIREBASE_MEASUREMENT_ID', $firebase_settings['config']['measurementId']);

// 글로벌 Firebase 설정 변수 추가
$GLOBALS['firebase_config'] = $firebase_settings;

// Firebase SDK 로드 (필요한 경우)
// composer를 통해 설치된 Firebase SDK를 사용할 경우 활성화
// require_once __DIR__ . '/../../vendor/autoload.php';

return $firebase_settings; 