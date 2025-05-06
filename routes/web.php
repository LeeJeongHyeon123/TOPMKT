<?php
/**
 * 웹 라우트 정의
 * 
 * URL 패턴과 처리할 컨트롤러 메서드를 연결합니다.
 */

// 라우팅 로직은 향후 라우터 클래스로 구현 예정
// 현재는 간단한 라우팅 로직만 포함

// 요청 URL 가져오기
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// 스크립트 경로 제거 (DocumentRoot 설정에 따라 달라질 수 있음)
$baseDir = str_replace(basename($scriptName), '', $scriptName);
$path = str_replace($baseDir, '', $requestUri);
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// 쿼리 파라미터 분리
$query = [];
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    parse_str($_SERVER['QUERY_STRING'], $query);
}

// 라우트 처리
switch ($path) {
    case '':
    case 'index.php':
    case 'home':
        // 홈 페이지
        require_once APP_ROOT . '/app/Controllers/HomeController.php';
        $controller = new \App\Controllers\HomeController();
        $controller->index();
        break;
        
    case 'about':
        // 소개 페이지
        require_once APP_ROOT . '/app/Controllers/PageController.php';
        $controller = new \App\Controllers\PageController();
        $controller->about();
        break;
        
    case 'contact':
        // 연락처 페이지
        require_once APP_ROOT . '/app/Controllers/PageController.php';
        $controller = new \App\Controllers\PageController();
        $controller->contact();
        break;
        
    // 로그인/회원 관련
    case 'login':
        require_once APP_ROOT . '/app/Controllers/AuthController.php';
        $controller = new \App\Controllers\AuthController();
        $controller->login();
        break;
        
    case 'register':
        require_once APP_ROOT . '/app/Controllers/AuthController.php';
        $controller = new \App\Controllers\AuthController();
        $controller->register();
        break;
        
    // 기타 라우트는 추가 예정
        
    default:
        // 404 페이지
        header("HTTP/1.0 404 Not Found");
        require_once APP_ROOT . '/app/Controllers/ErrorController.php';
        $controller = new \App\Controllers\ErrorController();
        $controller->notFound();
        break;
} 