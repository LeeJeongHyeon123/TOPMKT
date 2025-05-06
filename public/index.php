<?php
/**
 * 탑마케팅(TOPMKT) 공개 디렉토리 진입점
 * 
 * 이 파일은 웹 요청을 처리하는 프론트 컨트롤러입니다.
 * 애플리케이션의 부트스트랩 과정을 시작합니다.
 */

// 세션 시작
session_start();

// 기본 시간대 설정
date_default_timezone_set('Asia/Seoul');

// 애플리케이션 루트 경로가 정의되지 않은 경우 정의
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// Composer 오토로더 로드
if (file_exists(APP_ROOT . '/vendor/autoload.php')) {
    require APP_ROOT . '/vendor/autoload.php';
}

// 환경 변수 로드
if (file_exists(APP_ROOT . '/.env')) {
    // 환경 변수 로드 라이브러리가 있다면 사용
    // 없으면 수동으로 구현
}

// 애플리케이션 부트스트랩
require_once APP_ROOT . '/app/bootstrap.php';

// 라우팅 처리
require_once APP_ROOT . '/routes/web.php';

// 요청 실행
// Router::dispatch(); 