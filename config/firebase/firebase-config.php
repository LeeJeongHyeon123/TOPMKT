<?php
/**
 * Firebase 중앙 설정 파일
 * 
 * 이 파일은 탑마케팅 프로젝트의 Firebase 관련 모든 설정을 통합 관리합니다.
 * 기본정책에 따라 Firebase Authentication, Firestore, Storage 서비스를 사용합니다.
 * 
 * @version 1.0.0
 * @author TOPMKT Development Team
 */

// 직접 접근 방지
if (!defined('TOPMKT')) {
    define('TOPMKT', true);
}

// Firebase 프로젝트 기본 정보
$firebase_config = [
    'apiKey' => 'AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ',
    'authDomain' => 'topmkt-832f2.firebaseapp.com',
    'projectId' => 'topmkt-832f2',
    'storageBucket' => 'topmkt-832f2.appspot.com',
    'messagingSenderId' => '856114239779',
    'appId' => '1:856114239779:web:af81f6b2b097a31e20b971',
    'measurementId' => 'G-4SJNZ4X3JY'
];

// Firebase 상수 정의 (하위 호환성)
if (!defined('FIREBASE_DATABASE_URL')) {
    define('FIREBASE_DATABASE_URL', 'https://topmkt-832f2-default-rtdb.firebaseio.com');
}
if (!defined('FIREBASE_PROJECT_ID')) {
    define('FIREBASE_PROJECT_ID', $firebase_config['projectId']);
}
if (!defined('FIREBASE_STORAGE_BUCKET')) {
    define('FIREBASE_STORAGE_BUCKET', $firebase_config['storageBucket']);
}
if (!defined('FIREBASE_MESSAGING_SENDER_ID')) {
    define('FIREBASE_MESSAGING_SENDER_ID', $firebase_config['messagingSenderId']);
}
if (!defined('FIREBASE_APP_ID')) {
    define('FIREBASE_APP_ID', $firebase_config['appId']);
}
if (!defined('FIREBASE_AUTH_DOMAIN')) {
    define('FIREBASE_AUTH_DOMAIN', $firebase_config['authDomain']);
}
if (!defined('FIREBASE_API_KEY')) {
    define('FIREBASE_API_KEY', $firebase_config['apiKey']);
}

// 자격 증명 파일 경로
$firebase_credentials_path = __DIR__ . '/../google/service-account.json';

// Firebase 서비스 사용 설정
$firebase_services = [
    'auth' => true,        // Firebase Authentication (휴대폰 인증)
    'firestore' => true,   // Firestore (실시간 데이터)
    'storage' => true,     // Firebase Storage (파일 저장)
    'realtime' => false    // Realtime Database (사용 안 함)
];

/**
 * Firebase 데이터 저장소 선택 가이드
 * 
 * 1. MariaDB: 회원정보, 게시글, 댓글, 좋아요 등 정형적 관계형 데이터
 * 2. Firestore: 채팅, 알림, 상태변경 등 실시간 동기화 데이터
 * 3. Firebase Storage: 이미지, 영상, 첨부파일 등 대용량 파일
 */

// Firebase 인증 관련 설정
$firebase_auth_config = [
    'phone_verification' => [
        'test_phone' => '+82 10-1234-1234',
        'test_code' => '123456',
        'max_failed_attempts' => 5,
        'block_duration' => 86400, // 24시간 (초)
    ]
];

// reCAPTCHA 설정
$recaptcha_config = [
    'site_key' => '6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4',
    'service_account_path' => __DIR__ . '/../google/service-account.json',
    'actions' => [
        'login' => 'LOGIN',
        'register' => 'REGISTER',
        'send_code' => 'SEND_CODE'
    ]
];

// Firebase 설정 내보내기
return [
    'config' => $firebase_config,
    'credentials' => [
        'file' => $firebase_credentials_path
    ],
    'storage' => [
        'bucket' => 'gs://' . $firebase_config['storageBucket']
    ],
    'database' => [
        'url' => 'https://' . $firebase_config['projectId'] . '.firebaseio.com'
    ],
    'auth' => [
        'apiKey' => $firebase_config['apiKey'],
        'authDomain' => $firebase_config['authDomain'],
        'projectId' => $firebase_config['projectId'],
        'storageBucket' => $firebase_config['storageBucket'],
        'appId' => $firebase_config['appId']
    ],
    'services' => $firebase_services,
    'auth_config' => $firebase_auth_config,
    'recaptcha' => $recaptcha_config
];
