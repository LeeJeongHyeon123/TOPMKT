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
            // 기본 라우트
            'GET:/' => ['HomeController', 'index'],
            
            // 인증 라우트
            'GET:/auth/login' => ['AuthController', 'showLogin'],
            'POST:/auth/login' => ['AuthController', 'login'],
            'GET:/auth/signup' => ['AuthController', 'showSignup'],
            'POST:/auth/signup' => ['AuthController', 'signup'],
            'POST:/auth/logout' => ['AuthController', 'logout'],
            
            // 휴대폰 인증 라우트
            'POST:/auth/send-verification' => ['AuthController', 'sendVerification'],
            'POST:/auth/verify-code' => ['AuthController', 'verifyCode'],
            
            // 법적 문서 라우트
            'GET:/terms' => ['LegalController', 'showTerms'],
            'GET:/privacy' => ['LegalController', 'showPrivacy'],
            
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
        ];
    }
    
    /**
     * 요청 디스패치
     * URL 경로와 HTTP 메서드를 기반으로 적절한 컨트롤러와 액션 호출
     */
    public function dispatch() {
        // 웹 서버가 아닌 환경에서 실행되는 경우 기본값 설정
        if (!isset($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = '/';
        }
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $_SERVER['REQUEST_METHOD'] = 'GET';
        }
        
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
            $controllerPath = SRC_PATH . '/controllers/' . $controllerName . '.php';
            
            if (file_exists($controllerPath)) {
                require_once $controllerPath;
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $action)) {
                        $controller->$action();
                        return;
                    } else {
                        error_log("Method $action not found in $controllerName");
                    }
                } else {
                    error_log("Controller class $controllerName not found");
                }
            } else {
                error_log("Controller file not found: $controllerPath");
            }
        }
        
        // 매칭되는 라우트가 없거나 오류가 발생하면 404 페이지 표시
        header('HTTP/1.1 404 Not Found');
        $this->show404();
    }
    
    /**
     * 404 페이지 표시
     */
    private function show404() {
        $templatePath = SRC_PATH . '/views/templates/404.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        } else {
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The requested page could not be found.</p>';
        }
    }
} 