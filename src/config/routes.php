<?php
/**
 * 탑마케팅 라우팅 설정 파일
 */

/**
 * 라우팅 클래스
 * 요청 URL을 컨트롤러와 액션에 매핑합니다.
 */
class Router {
    private $routes = [];
    
    /**
     * 생성자 - 라우트 정의
     */
    public function __construct() {
        // 인증 관련 라우트
        $this->routes = [
            // 인증 라우트
            'GET:/auth/login' => ['AuthController', 'showLogin'],
            'POST:/auth/login' => ['AuthController', 'login'],
            'GET:/auth/signup' => ['AuthController', 'showSignup'],
            'POST:/auth/signup' => ['AuthController', 'signup'],
            'POST:/auth/logout' => ['AuthController', 'logout'],
            
            // 사용자 라우트
            'GET:/users/me' => ['UserController', 'showProfile'],
            'GET:/users/{id}' => ['UserController', 'getUser'],
            'PUT:/users/{id}' => ['UserController', 'updateUser'],
            'DELETE:/users/{id}' => ['UserController', 'deleteUser'],
            
            // 게시글 라우트
            'GET:/posts' => ['PostController', 'index'],
            'GET:/posts/{id}' => ['PostController', 'show'],
            'POST:/posts' => ['PostController', 'create'],
            'PUT:/posts/{id}' => ['PostController', 'update'],
            'DELETE:/posts/{id}' => ['PostController', 'delete'],
            
            // 댓글 라우트
            'POST:/posts/{id}/comments' => ['CommentController', 'create'],
            'PUT:/comments/{id}' => ['CommentController', 'update'],
            'DELETE:/comments/{id}' => ['CommentController', 'delete'],
            
            // 기본 라우트
            'GET:/' => ['PostController', 'index'],
        ];
    }
    
    /**
     * 요청 디스패치
     * URL 경로와 HTTP 메서드를 기반으로 적절한 컨트롤러와 액션 호출
     */
    public function dispatch() {
        // 요청 URI 파싱
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        
        // HTTP 메서드 가져오기
        $method = $_SERVER['REQUEST_METHOD'];
        
        // 라우트 키 생성
        $routeKey = $method . ':' . $uri;
        
        // 라우트 검색
        if (isset($this->routes[$routeKey])) {
            // 컨트롤러와 액션 호출
            list($controllerName, $action) = $this->routes[$routeKey];
            $controllerClass = 'App\\Controllers\\' . $controllerName;
            $controller = new $controllerClass();
            $controller->$action();
        } else {
            // 매칭되는 라우트가 없으면 404 페이지 표시
            header('HTTP/1.1 404 Not Found');
            include SRC_PATH . '/views/templates/404.php';
        }
    }
} 