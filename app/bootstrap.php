<?php
/**
 * 애플리케이션 부트스트랩 파일
 * 
 * 애플리케이션 설정 및 초기화를 처리합니다.
 */

// 오류 보고 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 애플리케이션 기본 설정 로드
$config = [];
if (file_exists(APP_ROOT . '/config/app.php')) {
    $config = require_once APP_ROOT . '/config/app.php';
}

// 데이터베이스 연결 설정 로드
$dbConfig = [];
if (file_exists(APP_ROOT . '/config/database.php')) {
    $dbConfig = require_once APP_ROOT . '/config/database.php';
}

// 유틸리티 함수 로드
require_once APP_ROOT . '/app/Helpers/functions.php';

// 기본 클래스 오토로딩 로직 (임시, Composer를 사용하는 것이 권장됨)
spl_autoload_register(function ($className) {
    // 네임스페이스를 디렉토리 경로로 변환
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $classFile = APP_ROOT . '/' . $className . '.php';
    
    if (file_exists($classFile)) {
        require_once $classFile;
        return true;
    }
    return false;
}); 