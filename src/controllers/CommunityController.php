<?php
/**
 * 커뮤니티 게시판 컨트롤러
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/models/Post.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';

class CommunityController {
    private $db;
    private $postModel;
    private $userModel;
    
    public function __construct() {
        // 데이터베이스 연결 초기화
        $this->db = Database::getInstance();
        $this->postModel = new Post();
        $this->userModel = new User();
    }
    
    /**
     * 커뮤니티 게시판 메인 페이지 (게시글 목록)
     */
    public function index() {
        error_log('🏠 CommunityController::index() 호출');
        
        try {
            // 데이터베이스 연결 확인
            if (!$this->db) {
                throw new Exception('데이터베이스 연결 실패');
            }
            error_log('✅ 데이터베이스 연결 확인');
            
            // 테이블 존재 확인
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'posts'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                throw new Exception('posts 테이블이 존재하지 않습니다');
            }
            
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'users'");
            $stmt->execute();
            if (!$stmt->fetch()) {
                throw new Exception('users 테이블이 존재하지 않습니다');
            }
            error_log('✅ 필수 테이블 존재 확인');
            
            // 페이지 번호 가져오기
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $pageSize = 20; // 한 페이지당 게시글 수
            
            // 검색어 가져오기
            $search = isset($_GET['search']) ? trim($_GET['search']) : null;
            
            error_log('📄 요청 파라미터: page=' . $page . ', search=' . ($search ?? 'null'));
            
            // 게시글 목록 조회
            error_log('📊 게시글 목록 조회 시작');
            $posts = $this->postModel->getList($page, $pageSize, $search);
            error_log('📊 게시글 목록 조회 완료: ' . count($posts) . '개');
            
            $totalCount = $this->postModel->getTotalCount($search);
            error_log('📊 총 게시글 수: ' . $totalCount);
            
            $totalPages = ceil($totalCount / $pageSize);
            
            // 뷰에 전달할 데이터 준비
            $data = [
                'posts' => $posts,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'search' => $search,
                'pageSize' => $pageSize,
                'hasNextPage' => $page < $totalPages,
                'hasPrevPage' => $page > 1
            ];
            
            error_log('📊 게시글 조회 완료: ' . count($posts) . '개, 페이지: ' . $page . '/' . $totalPages);
            error_log('📄 뷰 렌더링 시작');
            
            // 뷰 렌더링
            $this->renderView('community/index', $data);
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::index() 오류: ' . $e->getMessage());
            error_log('❌ 오류 스택: ' . $e->getTraceAsString());
            
            // 개발 환경에서는 상세 오류 표시
            if (defined('APP_DEBUG') && APP_DEBUG) {
                // HTML 응답으로 상세 오류 표시
                header('Content-Type: text/html; charset=UTF-8');
                echo '<h1>500 Internal Server Error</h1>';
                echo '<p><strong>오류:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<p><strong>파일:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
                echo '<h3>스택 트레이스:</h3>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                exit;
            } else {
                ResponseHelper::error('게시판을 불러오는 중 오류가 발생했습니다.');
            }
        }
    }
    
    /**
     * 게시글 상세보기
     */
    public function show() {
        error_log('📄 CommunityController::show() 호출');
        
        try {
            // URL에서 게시글 ID 추출
            $postId = $this->getPostIdFromUrl();
            
            if (!$postId) {
                ResponseHelper::error('잘못된 게시글 번호입니다.', 400);
                return;
            }
            
            // 게시글 조회
            $post = $this->postModel->getById($postId);
            
            if (!$post) {
                ResponseHelper::error('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 조회수 증가 (나중에 구현)
            // $this->postModel->incrementViewCount($postId);
            
            // 현재 사용자가 작성자인지 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId && $currentUserId == $post['user_id'];
            
            $data = [
                'post' => $post,
                'isOwner' => $isOwner,
                'currentUserId' => $currentUserId
            ];
            
            error_log('📖 게시글 조회 완료: ID=' . $postId . ', 제목=' . $post['title']);
            
            // 뷰 렌더링
            $this->renderView('community/detail', $data);
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::show() 오류: ' . $e->getMessage());
            ResponseHelper::error('게시글을 불러오는 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 게시글 작성 폼 보기
     */
    public function showWrite() {
        error_log('✍️ CommunityController::showWrite() 호출');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::redirect('/auth/login');
            return;
        }
        
        $data = [
            'action' => 'create',
            'post' => null
        ];
        
        // 뷰 렌더링
        $this->renderView('community/write', $data);
    }
    
    /**
     * 게시글 작성 처리
     */
    public function create() {
        error_log('📝 CommunityController::create() 호출');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // POST 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ResponseHelper::jsonError('잘못된 요청 방식입니다.', 405);
            return;
        }
        
        try {
            // 입력 데이터 검증
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            // 유효성 검사
            $errors = [];
            
            if (empty($title)) {
                $errors[] = '제목을 입력해주세요.';
            } elseif (strlen($title) > 200) {
                $errors[] = '제목은 200자 이내로 입력해주세요.';
            }
            
            if (empty($content)) {
                $errors[] = '내용을 입력해주세요.';
            } elseif (strlen($content) > 10000) {
                $errors[] = '내용은 10,000자 이내로 입력해주세요.';
            }
            
            if (!empty($errors)) {
                ResponseHelper::jsonError(implode(' ', $errors), 400);
                return;
            }
            
            // 게시글 생성
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $postData = [
                'user_id' => $currentUserId,
                'title' => $title,
                'content' => $content
            ];
            
            $postId = $this->postModel->create($postData);
            
            if ($postId) {
                error_log('✅ 게시글 작성 성공: ID=' . $postId . ', 작성자=' . $currentUserId);
                ResponseHelper::jsonSuccess('게시글이 작성되었습니다.', [
                    'postId' => $postId,
                    'redirectUrl' => '/community/posts/' . $postId
                ]);
            } else {
                error_log('❌ 게시글 작성 실패');
                ResponseHelper::jsonError('게시글 작성에 실패했습니다.');
            }
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::create() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 작성 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 게시글 수정 폼 보기
     */
    public function showEdit() {
        error_log('📝 CommunityController::showEdit() 호출');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::redirect('/auth/login');
            return;
        }
        
        try {
            // URL에서 게시글 ID 추출
            $postId = $this->getPostIdFromUrl();
            
            if (!$postId) {
                ResponseHelper::error('잘못된 게시글 번호입니다.', 400);
                return;
            }
            
            // 게시글 조회
            $post = $this->postModel->getById($postId);
            
            if (!$post) {
                ResponseHelper::error('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 권한 확인 (작성자 또는 관리자만 수정 가능)
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId == $post['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            if (!$isOwner && !$isAdmin) {
                ResponseHelper::error('수정 권한이 없습니다.', 403);
                return;
            }
            
            $data = [
                'action' => 'edit',
                'post' => $post
            ];
            
            // 뷰 렌더링
            $this->renderView('community/write', $data);
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::showEdit() 오류: ' . $e->getMessage());
            ResponseHelper::error('게시글을 불러오는 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 게시글 수정 처리
     */
    public function update() {
        error_log('📝 CommunityController::update() 호출');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // POST 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ResponseHelper::jsonError('잘못된 요청 방식입니다.', 405);
            return;
        }
        
        try {
            // URL에서 게시글 ID 추출
            $postId = $this->getPostIdFromUrl();
            
            if (!$postId) {
                ResponseHelper::jsonError('잘못된 게시글 번호입니다.', 400);
                return;
            }
            
            // 게시글 조회
            $post = $this->postModel->getById($postId);
            
            if (!$post) {
                ResponseHelper::jsonError('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 권한 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId == $post['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            if (!$isOwner && !$isAdmin) {
                ResponseHelper::jsonError('수정 권한이 없습니다.', 403);
                return;
            }
            
            // 입력 데이터 검증
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            
            // 유효성 검사
            $errors = [];
            
            if (empty($title)) {
                $errors[] = '제목을 입력해주세요.';
            } elseif (strlen($title) > 200) {
                $errors[] = '제목은 200자 이내로 입력해주세요.';
            }
            
            if (empty($content)) {
                $errors[] = '내용을 입력해주세요.';
            } elseif (strlen($content) > 10000) {
                $errors[] = '내용은 10,000자 이내로 입력해주세요.';
            }
            
            if (!empty($errors)) {
                ResponseHelper::jsonError(implode(' ', $errors), 400);
                return;
            }
            
            // 게시글 수정
            $updateData = [
                'title' => $title,
                'content' => $content
            ];
            
            $success = $this->postModel->update($postId, $updateData);
            
            if ($success) {
                error_log('✅ 게시글 수정 성공: ID=' . $postId);
                ResponseHelper::jsonSuccess('게시글이 수정되었습니다.', [
                    'postId' => $postId,
                    'redirectUrl' => '/community/posts/' . $postId
                ]);
            } else {
                error_log('❌ 게시글 수정 실패: ID=' . $postId);
                ResponseHelper::jsonError('게시글 수정에 실패했습니다.');
            }
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::update() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 수정 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 게시글 삭제
     */
    public function delete() {
        error_log('🗑️ CommunityController::delete() 호출');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // POST 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ResponseHelper::jsonError('잘못된 요청 방식입니다.', 405);
            return;
        }
        
        try {
            // URL에서 게시글 ID 추출
            $postId = $this->getPostIdFromUrl();
            
            if (!$postId) {
                ResponseHelper::jsonError('잘못된 게시글 번호입니다.', 400);
                return;
            }
            
            // 게시글 조회
            $post = $this->postModel->getById($postId);
            
            if (!$post) {
                ResponseHelper::jsonError('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 권한 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId == $post['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            if (!$isOwner && !$isAdmin) {
                ResponseHelper::jsonError('삭제 권한이 없습니다.', 403);
                return;
            }
            
            // 게시글 삭제
            $success = $this->postModel->delete($postId);
            
            if ($success) {
                error_log('✅ 게시글 삭제 성공: ID=' . $postId);
                ResponseHelper::jsonSuccess('게시글이 삭제되었습니다.', [
                    'redirectUrl' => '/community'
                ]);
            } else {
                error_log('❌ 게시글 삭제 실패: ID=' . $postId);
                ResponseHelper::jsonError('게시글 삭제에 실패했습니다.');
            }
            
        } catch (Exception $e) {
            error_log('❌ CommunityController::delete() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 삭제 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * URL에서 게시글 ID 추출
     */
    private function getPostIdFromUrl() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // /community/posts/{id} 패턴에서 ID 추출
        if (preg_match('/\/community\/posts\/(\d+)/', $uri, $matches)) {
            return intval($matches[1]);
        }
        
        return null;
    }
    
    /**
     * 뷰 렌더링
     */
    private function renderView($viewName, $data = []) {
        // 뷰 데이터를 변수로 추출
        extract($data);
        
        // 공통 헤더 포함
        $headerData = [
            'title' => '커뮤니티 게시판 - 탑마케팅',
            'description' => '탑마케팅 커뮤니티에서 정보를 공유하고 소통하세요',
            'pageSection' => 'community'  // currentPage와 겹치지 않도록 변수명 변경
        ];
        
        extract($headerData);
        
        // 헤더 include
        include SRC_PATH . '/views/templates/header.php';
        
        // 메인 뷰 include
        $viewPath = SRC_PATH . '/views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            error_log('❌ 뷰 파일을 찾을 수 없습니다: ' . $viewPath);
            echo '<h1>페이지를 찾을 수 없습니다.</h1>';
        }
        
        // 푸터 include
        include SRC_PATH . '/views/templates/footer.php';
    }
}