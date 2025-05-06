<?php
/**
 * 애플리케이션 부트스트랩 파일
 * 
 * 필요한 설정 및 초기화 과정을 처리합니다.
 */

// 기본 상수 정의
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// 오토로드 설정
spl_autoload_register(function ($class) {
    // 네임스페이스 경로 변환
    $prefix = 'App\\';
    
    // 네임스페이스가 App\\으로 시작하는지 확인
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // 클래스 경로 생성
    $relativeClass = substr($class, $len);
    $file = APP_ROOT . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    
    // 파일이 있으면 로드
    if (file_exists($file)) {
        require $file;
    }
});

// 헬퍼 함수 로드
if (file_exists(APP_ROOT . '/app/Helpers/functions.php')) {
    require_once APP_ROOT . '/app/Helpers/functions.php';
}

// Composer 오토로더 로드 (있는 경우)
$composerAutoload = APP_ROOT . '/vendor/autoload.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
}

// 기본 설정 로드
$config = [];

// Firebase 설정 로드
if (file_exists(APP_ROOT . '/config/firebase.php')) {
    $config['firebase'] = require_once APP_ROOT . '/config/firebase.php';
}

// 에러 핸들링 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 기본 디렉토리 확인 및 생성
$directories = [
    APP_ROOT . '/public/assets/images/temp',
    APP_ROOT . '/public/assets/images/cache',
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
} 