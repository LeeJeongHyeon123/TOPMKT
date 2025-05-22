<?php
/**
 * Firebase 설정 파일
 */

// Firebase 프로젝트 ID
if (!defined('FIREBASE_PROJECT_ID')) {
    define('FIREBASE_PROJECT_ID', 'topmkt-project');
}

// Firebase API 키
if (!defined('FIREBASE_API_KEY')) {
    define('FIREBASE_API_KEY', 'YOUR_FIREBASE_API_KEY');
}

// Firebase 인증 도메인
if (!defined('FIREBASE_AUTH_DOMAIN')) {
    define('FIREBASE_AUTH_DOMAIN', 'topmkt-project.firebaseapp.com');
}

// Firebase 스토리지 버킷
if (!defined('FIREBASE_STORAGE_BUCKET')) {
    define('FIREBASE_STORAGE_BUCKET', 'topmkt-project.appspot.com');
}

// Firebase 앱 ID
if (!defined('FIREBASE_APP_ID')) {
    define('FIREBASE_APP_ID', 'YOUR_FIREBASE_APP_ID');
}

// Firebase 측정 ID
if (!defined('FIREBASE_MEASUREMENT_ID')) {
    define('FIREBASE_MEASUREMENT_ID', 'YOUR_FIREBASE_MEASUREMENT_ID');
}

// Firebase Admin SDK 설정 경로
if (!defined('FIREBASE_ADMIN_SDK_PATH')) {
    define('FIREBASE_ADMIN_SDK_PATH', __DIR__ . '/../google/service-account.json');
}

// API 키 등을 포함하는 배열 반환
return [
    'auth' => [
        'apiKey' => FIREBASE_API_KEY,
        'authDomain' => FIREBASE_AUTH_DOMAIN,
        'projectId' => FIREBASE_PROJECT_ID,
        'storageBucket' => FIREBASE_STORAGE_BUCKET,
        'appId' => FIREBASE_APP_ID
    ],
    'credentials' => [
        'file' => __DIR__ . '/../google/service-account.json'
    ],
    'storage' => [
        'bucket' => 'gs://' . FIREBASE_STORAGE_BUCKET
    ],
    'database' => [
        'url' => 'https://' . FIREBASE_PROJECT_ID . '.firebaseio.com'
    ]
];
