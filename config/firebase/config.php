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

// Firebase 설정
$firebaseConfig = [
    'apiKey' => "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
    'authDomain' => "topmkt-832f2.firebaseapp.com",
    'projectId' => "topmkt-832f2",
    'storageBucket' => "topmkt-832f2.appspot.com",
    'messagingSenderId' => "856114239779",
    'appId' => "1:856114239779:web:d8dd9049a9723ac8835496"
];

// Firebase Admin SDK 설정
$serviceAccountPath = __DIR__ . '/../google/service-account.json';
if (!file_exists($serviceAccountPath)) {
    error_log("Firebase service account file not found at: " . $serviceAccountPath);
    throw new Exception('Firebase configuration error');
}

$serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
if (!$serviceAccount) {
    error_log("Failed to parse Firebase service account JSON");
    throw new Exception('Firebase configuration error');
}

// Firebase Admin SDK 초기화
require_once __DIR__ . '/../../vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://topmkt-832f2-default-rtdb.firebaseio.com');

$auth = $factory->createAuth();
$database = $factory->createDatabase();
$storage = $factory->createStorage();

// 하위 호환성을 위한 상수 정의
define('FIREBASE_API_KEY', $firebaseConfig['apiKey']);
define('FIREBASE_AUTH_DOMAIN', $firebaseConfig['authDomain']);
define('FIREBASE_PROJECT_ID', $firebaseConfig['projectId']);
define('FIREBASE_STORAGE_BUCKET', $firebaseConfig['storageBucket']);
define('FIREBASE_MESSAGING_SENDER_ID', $firebaseConfig['messagingSenderId']);
define('FIREBASE_APP_ID', $firebaseConfig['appId']);

// 글로벌 Firebase 설정 변수 추가
$GLOBALS['firebase_config'] = [
    'credentials' => [
        'file' => __DIR__ . '/../google/service-account.json'
    ],
    'database' => [
        'url' => 'https://topmkt-832f2-default-rtdb.firebaseio.com'
    ],
    'storage' => [
        'bucket' => 'topmkt-832f2.firebasestorage.app'
    ],
    'auth' => [
        'domain' => $firebaseConfig['authDomain'],
        'apiKey' => $firebaseConfig['apiKey'],
        'projectId' => $firebaseConfig['projectId'],
        'messagingSenderId' => $firebaseConfig['messagingSenderId'],
        'appId' => $firebaseConfig['appId'],
        'phone_verification' => [
            'test_phone' => '01012341234',
            'test_code' => '123456'
        ]
    ],
    'recaptcha' => [
        'site_key' => '6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb',
        'secret_key' => '6LfViDErAAAAAJYZ6zqP3I6q124NuaUlAGcUWeB5',
        'service_account_path' => __DIR__ . '/google/service-account.json',
        'actions' => [
            'login' => 'LOGIN',
            'register' => 'REGISTER',
            'send_code' => 'SEND_CODE'
        ]
    ]
];

return $GLOBALS['firebase_config'];
