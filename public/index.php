<?php
/**
 * 탑마케팅 애플리케이션 진입점
 * 
 * 모든 요청은 이 파일을 통해 처리됩니다.
 */

// 상대 경로 설정
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', SRC_PATH . '/config');

// 설정 파일 로드
require_once CONFIG_PATH . '/config.php';
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/routes.php';

// 세션 시작
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Strict',
    'gc_maxlifetime' => 1800, // 30분
]);

// 라우팅 처리
$router = new Router();
$router->dispatch(); 