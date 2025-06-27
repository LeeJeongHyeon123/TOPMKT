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
            'GET:/auth/logout' => ['AuthController', 'logout'],
            'POST:/auth/logout' => ['AuthController', 'logout'],
            
            // 휴대폰 인증 라우트
            'POST:/auth/send-verification' => ['AuthController', 'sendVerification'],
            'POST:/auth/verify-code' => ['AuthController', 'verifyCode'],
            
            // JWT API 라우트
            'POST:/auth/refresh' => ['AuthController', 'refreshToken'],
            'GET:/auth/me' => ['AuthController', 'me'],
            
            // React API 라우트
            'POST:/api/auth/login' => ['AuthController', 'apiLogin'],
            'POST:/api/auth/signup' => ['AuthController', 'apiSignup'],
            'POST:/api/auth/logout' => ['AuthController', 'apiLogout'],
            'POST:/api/auth/refresh' => ['AuthController', 'apiRefreshToken'],
            'GET:/api/auth/me' => ['AuthController', 'apiMe'],
            'GET:/api/csrf-token' => ['AuthController', 'getCsrfToken'],
            
            // 법적 문서 라우트
            'GET:/terms' => ['LegalController', 'showTerms'],
            'GET:/privacy' => ['LegalController', 'showPrivacy'],
            
            // 사용자 라우트 (기존)
            'GET:/users/me' => ['UserController', 'showProfile'],
            'GET:/users/{id}' => ['UserController', 'getUser'],
            'PUT:/users/{id}' => ['UserController', 'updateUser'],
            'DELETE:/users/{id}' => ['UserController', 'deleteUser'],
            
            // React 사용자 API
            'GET:/api/users/profile' => ['UserController', 'apiGetProfile'],
            'PUT:/api/users/profile' => ['UserController', 'apiUpdateProfile'],
            'POST:/api/users/profile/image' => ['UserController', 'apiUploadProfileImage'],
            
            // 프로필 라우트 (새로 추가)
            'GET:/profile' => ['UserController', 'showMyProfile'],
            'GET:/profile/edit' => ['UserController', 'showEditProfile'],
            'POST:/profile/update' => ['UserController', 'updateProfile'],
            'POST:/profile/upload-image' => ['UserController', 'uploadProfileImage'],
            'GET:/profile/{nickname}' => ['UserController', 'showPublicProfile'],
            
            // 커뮤니티 게시판 라우트
            'GET:/community' => ['CommunityController', 'index'],
            'GET:/community/posts/{id}' => ['CommunityController', 'show'],
            'GET:/community/write' => ['CommunityController', 'showWrite'],
            'POST:/community/posts' => ['CommunityController', 'create'],
            'GET:/community/posts/{id}/edit' => ['CommunityController', 'showEdit'],
            'PUT:/community/posts/{id}' => ['CommunityController', 'update'],
            'DELETE:/community/posts/{id}' => ['CommunityController', 'delete'],
            
            // React 커뮤니티 API
            'GET:/api/community/posts' => ['CommunityController', 'apiIndex'],
            'GET:/api/community/posts/{id}' => ['CommunityController', 'apiShow'],
            'POST:/api/community/posts' => ['CommunityController', 'apiCreate'],
            'PUT:/api/community/posts/{id}' => ['CommunityController', 'apiUpdate'],
            'DELETE:/api/community/posts/{id}' => ['CommunityController', 'apiDelete'],
            
            // React 커뮤니티 댓글 API
            'GET:/api/community/posts/{id}/comments' => ['CommunityController', 'apiGetComments'],
            'POST:/api/community/posts/{id}/comments' => ['CommunityController', 'apiCreateComment'],
            'PUT:/api/community/posts/{id}/comments/{commentId}' => ['CommunityController', 'apiUpdateComment'],
            'DELETE:/api/community/posts/{id}/comments/{commentId}' => ['CommunityController', 'apiDeleteComment'],
            
            // 기존 게시글 라우트 (호환성 유지)
            'GET:/posts' => ['PostController', 'index'],
            'GET:/posts/{id}' => ['PostController', 'show'],
            'POST:/posts' => ['PostController', 'create'],
            'PUT:/posts/{id}' => ['PostController', 'update'],
            'DELETE:/posts/{id}' => ['PostController', 'delete'],
            
            // 댓글 라우트
            'POST:/api/comments' => ['CommentController', 'store'],
            'PUT:/api/comments/{id}' => ['CommentController', 'update'],
            'DELETE:/api/comments/{id}' => ['CommentController', 'delete'],
            'GET:/api/comments' => ['CommentController', 'list'],
            
            // 좋아요 라우트
            'POST:/api/posts/{id}/like' => ['LikeController', 'togglePostLike'],
            'GET:/api/posts/{id}/like' => ['LikeController', 'getPostLikeStatus'],
            
            // 미디어 업로드 라우트
            'POST:/api/media/upload-image' => ['MediaController', 'uploadImage'],
            
            // 사용자 프로필 이미지 API
            'GET:/api/users/{id}/profile-image' => ['UserController', 'getProfileImage'],
            
            // Geocoding API (제거됨 - 클라이언트 사이드로 변경)
            
            // 강의 일정 라우트
            'GET:/lectures' => ['LectureController', 'index'],
            'GET:/lectures/{id}' => ['LectureController', 'show'],
            'GET:/lectures/create' => ['LectureController', 'create'],
            'POST:/lectures/store' => ['LectureController', 'store'],
            'GET:/lectures/{id}/edit' => ['LectureController', 'edit'],
            'POST:/lectures/{id}/update' => ['LectureController', 'update'],
            'PUT:/lectures/{id}/update' => ['LectureController', 'update'],
            'POST:/lectures/{id}/delete' => ['LectureController', 'delete'],
            'POST:/lectures/{id}/register' => ['LectureController', 'register'],
            'POST:/lectures/update-images' => ['LectureController', 'updateImages'],
            'GET:/lectures/{id}/ical' => ['LectureController', 'generateICal'],
            
            // React 강의 API
            'GET:/api/lectures' => ['LectureController', 'apiIndex'],
            'GET:/api/lectures/{id}' => ['LectureController', 'apiShow'],
            'POST:/api/lectures' => ['LectureController', 'apiStore'],
            'PUT:/api/lectures/{id}' => ['LectureController', 'apiUpdate'],
            'DELETE:/api/lectures/{id}' => ['LectureController', 'apiDelete'],
            
            // 행사 일정 라우트
            'GET:/events' => ['EventController', 'index'],
            'GET:/events/detail' => ['EventController', 'detail'],
            'GET:/events/create' => ['EventController', 'create'],
            'POST:/events/store' => ['EventController', 'store'],
            'GET:/events/{id}/edit' => ['EventController', 'edit'],
            'POST:/events/{id}/update' => ['EventController', 'update'],
            'POST:/events/{id}/delete' => ['EventController', 'delete'],
            'POST:/events/{id}/register' => ['EventController', 'register'],
            'GET:/events/{id}/ical' => ['EventController', 'generateICal'],
            
            // 채팅 라우트
            'GET:/chat' => ['ChatController', 'index'],
            'GET:/chat/rooms' => ['ChatController', 'getRooms'],
            'POST:/chat/rooms' => ['ChatController', 'createRoom'],
            'GET:/chat/search-users' => ['ChatController', 'searchUsers'],
            'GET:/chat/firebase-token' => ['ChatController', 'getFirebaseToken'],
            
            // 기업회원 라우트
            'GET:/corp/info' => ['CorporateController', 'info'],
            'GET:/corp/apply' => ['CorporateController', 'apply'],
            'POST:/corp/apply' => ['CorporateController', 'apply'],
            'GET:/corp/status' => ['CorporateController', 'status'],
            'GET:/corp/edit' => ['CorporateController', 'edit'],
            'POST:/corp/edit' => ['CorporateController', 'edit'],
            
            // 관리자 라우트
            'GET:/admin' => ['AdminController', 'dashboard'],
            'GET:/admin/corporate/pending' => ['AdminController', 'corporatePending'],
            'GET:/admin/corporate/list' => ['AdminController', 'corporateList'],
            'POST:/admin/corporate/process' => ['AdminController', 'corporateProcess'],
            'POST:/admin/corporate/detail' => ['AdminController', 'corporateApplicationDetail'],
            'GET:/admin/document/view' => ['AdminController', 'viewDocument'],
            'POST:/admin/corporate/manage' => ['AdminController', 'manageCorporateMember'],
            
            // 요가 랜딩페이지 테스트 라우트
            'GET:/yoga-landing' => ['YogaController', 'landing'],
            
            // Tvelia 여행사 웹사이트 라우트
            'GET:/tvelia-travel' => ['TveliaController', 'landing'],
            
            // 테스트 라우트
            'GET:/test1' => ['TestController', 'test1'],
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
        
        // 요청 URI 파싱 및 URL 디코딩
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = urldecode($uri); // URL 디코딩 추가
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        
        // React Frontend 라우트 처리
        if (strpos($uri, '/frontend') === 0) {
            $this->serveReactApp();
            return;
        }
        
        // HTTP 메서드 가져오기
        $method = $_SERVER['REQUEST_METHOD'];
        
        // 라우트 키 생성
        $routeKey = $method . ':' . $uri;
        
        // 정적 라우트 먼저 검색
        if (isset($this->routes[$routeKey])) {
            $this->executeRoute($this->routes[$routeKey]);
            return;
        }
        
        // 동적 라우트 검색
        foreach ($this->routes as $pattern => $route) {
            if ($this->matchDynamicRoute($pattern, $routeKey)) {
                $this->executeRoute($route);
                return;
            }
        }
        
        // 매칭되는 라우트가 없으면 404 페이지 표시
        header('HTTP/1.1 404 Not Found');
        $this->show404();
    }
    
    /**
     * 동적 라우트 매칭
     */
    private function matchDynamicRoute($pattern, $requestRoute) {
        // 닉네임 라우트 특별 처리
        if (strpos($pattern, '{nickname}') !== false) {
            $regexPattern = preg_replace('/\{nickname\}/', '([^\/]+)', $pattern);
        } else {
            // {id} 패턴을 정규식으로 변환 (숫자만)
            $regexPattern = preg_replace('/\{[^}]+\}/', '(\d+)', $pattern);
        }
        
        $regexPattern = '#^' . str_replace('/', '\/', $regexPattern) . '$#u';
        
        return preg_match($regexPattern, $requestRoute);
    }
    
    /**
     * 라우트 실행
     */
    private function executeRoute($route) {
        list($controllerName, $action) = $route;
        $controllerPath = SRC_PATH . '/controllers/' . $controllerName . '.php';
        
        // 라우트 실행 로깅
        file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "=== ROUTE EXECUTION ===\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Controller: $controllerName\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Action: $action\n", FILE_APPEND);
        file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Path: $controllerPath\n", FILE_APPEND);
        
        if (file_exists($controllerPath)) {
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Controller file exists\n", FILE_APPEND);
            require_once $controllerPath;
            if (class_exists($controllerName)) {
                file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Controller class exists\n", FILE_APPEND);
                $controller = new $controllerName();
                if (method_exists($controller, $action)) {
                    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Method exists, calling $action\n", FILE_APPEND);
                    // 동적 라우트에서 파라미터 추출
                    $params = $this->extractRouteParams();
                    if (!empty($params)) {
                        $controller->$action(...$params);
                    } else {
                        $controller->$action();
                    }
                    return;
                } else {
                    file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Method $action not found in $controllerName\n", FILE_APPEND);
                    error_log("Method $action not found in $controllerName");
                }
            } else {
                file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Controller class $controllerName not found\n", FILE_APPEND);
                error_log("Controller class $controllerName not found");
            }
        } else {
            file_put_contents('/var/www/html/topmkt/logs/topmkt_errors.log', "Controller file not found: $controllerPath\n", FILE_APPEND);
            error_log("Controller file not found: $controllerPath");
        }
        
        // 컨트롤러 실행 실패 시 500 에러
        header('HTTP/1.1 500 Internal Server Error');
        echo '<h1>500 Internal Server Error</h1>';
        echo '<p>An error occurred while processing your request.</p>';
    }
    
    /**
     * 라우트 파라미터 추출
     */
    private function extractRouteParams() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        $routeKey = $method . ':' . $uri;
        
        foreach ($this->routes as $pattern => $route) {
            // 닉네임 라우트 특별 처리
            if (strpos($pattern, '{nickname}') !== false) {
                $regexPattern = preg_replace('/\{nickname\}/', '([^\/]+)', $pattern);
            } else {
                $regexPattern = preg_replace('/\{[^}]+\}/', '(\d+)', $pattern);
            }
            
            $regexPattern = '#^' . str_replace('/', '\/', $regexPattern) . '$#';
            
            if (preg_match($regexPattern, $routeKey, $matches)) {
                // 첫 번째 매치는 전체 문자열이므로 제거
                array_shift($matches);
                
                // URL 디코딩 처리 (한국어 닉네임 지원)
                $decodedParams = [];
                foreach ($matches as $param) {
                    $decodedParams[] = urldecode($param);
                }
                
                return $decodedParams;
            }
        }
        
        return [];
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
    
    /**
     * React 앱 서빙
     */
    private function serveReactApp() {
        $frontendPath = ROOT_PATH . '/public/frontend';
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // /frontend 프리픽스 제거
        $filePath = str_replace('/frontend', '', $requestPath);
        $fullPath = $frontendPath . $filePath;
        
        // 파일이 존재하고 실제 파일인 경우
        if ($filePath !== '/' && file_exists($fullPath) && is_file($fullPath)) {
            $mimeTypes = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'woff' => 'font/woff',
                'woff2' => 'font/woff2',
                'ttf' => 'font/ttf'
            ];
            
            $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
            if (isset($mimeTypes[$ext])) {
                header('Content-Type: ' . $mimeTypes[$ext]);
                header('Cache-Control: public, max-age=31536000'); // 1년 캐시
            }
            
            readfile($fullPath);
            return;
        }
        
        // React 앱의 index.html 서빙
        $indexPath = $frontendPath . '/index.html';
        if (file_exists($indexPath)) {
            header('Content-Type: text/html; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            readfile($indexPath);
        } else {
            header('HTTP/1.1 404 Not Found');
            echo 'React app not built. Please run: npm run build';
        }
    }
} 