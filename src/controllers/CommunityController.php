<?php
/**
 * 커뮤니티 게시판 컨트롤러
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/models/Post.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';
require_once SRC_PATH . '/helpers/SearchHelper.php';
require_once SRC_PATH . '/helpers/PerformanceDebugger.php';
require_once SRC_PATH . '/helpers/WebLogger.php';
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
            $postsTable = $this->db->fetch("SHOW TABLES LIKE 'posts'");
            if (!$postsTable) {
                throw new Exception('posts 테이블이 존재하지 않습니다');
            }
            
            $usersTable = $this->db->fetch("SHOW TABLES LIKE 'users'");
            if (!$usersTable) {
                throw new Exception('users 테이블이 존재하지 않습니다');
            }
            error_log('✅ 필수 테이블 존재 확인');
            
            // 페이지 번호 가져오기
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $pageSize = 20; // 한 페이지당 게시글 수
            
            // 검색어 및 필터 가져오기
            $searchRaw = isset($_GET['search']) ? trim($_GET['search']) : null;
            $filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
            $search = null;
            $searchValidation = null;
            
            // 필터 유효성 검증
            $allowedFilters = ['all', 'title', 'content', 'author'];
            if (!in_array($filter, $allowedFilters)) {
                $filter = 'all';
            }
            
            if ($searchRaw) {
                $searchValidation = SearchHelper::validateSearchTerm($searchRaw);
                if ($searchValidation['valid']) {
                    $search = $searchValidation['cleaned'];
                } else {
                    error_log('❌ 검색어 유효성 검증 실패: ' . $searchValidation['message']);
                    // 유효하지 않은 검색어는 무시하고 전체 목록 표시
                    $search = null;
                }
            }
            
            error_log('📄 요청 파라미터: page=' . $page . ', search=' . ($search ? '[검색어있음]' : 'null'));
            
            // 성능 디버깅 시작
            WebLogger::init();
            $requestStartTime = microtime(true);
            WebLogger::log("🚀 [CONTROLLER] 커뮤니티 인덱스 시작: " . date('H:i:s.u'));
            
            if ($search) {
                WebLogger::log("🔍 [CONTROLLER] 검색 요청: '$search' (필터: $filter)");
            }
            
            PerformanceDebugger::reset();
            PerformanceDebugger::startTimer('community_index_total');
            
            // 성능 최적화: 총 개수를 먼저 조회해서 페이지 범위 검증
            WebLogger::log("📊 [CONTROLLER] 총 개수 조회 시작");
            $countStartTime = microtime(true);
            $start_time = microtime(true);
            PerformanceDebugger::startTimer('total_count_query');
            $totalCount = $this->postModel->getTotalCount($search, $filter);
            PerformanceDebugger::endTimer('total_count_query');
            $countTime = (microtime(true) - $countStartTime) * 1000;
            WebLogger::log("📊 [CONTROLLER] 총 개수 조회 완료: {$totalCount}개, " . round($countTime, 2) . "ms");
            
            $totalPages = ceil($totalCount / $pageSize);
            
            // 검색 로그 기록 (SearchHelper 사용)
            if ($search) {
                $searchTime = microtime(true) - $start_time;
                SearchHelper::logSearch($search, $totalCount, $searchTime);
            }
            
            // 페이지 범위 검증 - 존재하지 않는 페이지는 첫 페이지로 리다이렉트
            if ($page > $totalPages && $totalPages > 0) {
                error_log("⚠️ 잘못된 페이지 요청: {$page} (최대: {$totalPages})");
                $redirectUrl = '/community' . ($search ? '?search=' . urlencode($search) : '');
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $redirectUrl);
                exit;
            }
            
            
            // 게시글 목록 조회 (성능 모니터링 포함)
            WebLogger::log("📝 [CONTROLLER] 게시글 목록 조회 시작");
            $list_start = microtime(true);
            PerformanceDebugger::startTimer('post_list_total');
            $posts = $this->postModel->getList($page, $pageSize, $search, $filter);
            PerformanceDebugger::endTimer('post_list_total');
            $list_time = round((microtime(true) - $list_start) * 1000, 2);
            WebLogger::log("📝 [CONTROLLER] 게시글 목록 조회 완료: " . count($posts) . "개, " . $list_time . "ms");
            
            $query_time = round((microtime(true) - $start_time) * 1000, 2);
            
            // 전체 성능 디버깅 종료
            $totalTimer = PerformanceDebugger::endTimer('community_index_total');
            $totalRequestTime = (microtime(true) - $requestStartTime) * 1000;
            
            WebLogger::log("🏁 [CONTROLLER] 전체 요청 완료: " . round($totalRequestTime, 2) . "ms");
            
            // 성능 리포트 생성
            $performanceReport = PerformanceDebugger::logReport('커뮤니티 인덱스' . ($search ? " 검색: '$search'" : ''));
            
            // 성능 상세 로깅
            if ($page > 1000) {
                error_log("🐌 큰 페이지 성능 분석: 페이지 {$page}, 총 시간: {$query_time}ms, 목록 조회: {$list_time}ms");
            }
            
            // 캐시 히트 여부 로깅
            $listCacheKey = CacheHelper::getPostListCacheKey($page, $pageSize, $search);
            $countCacheKey = CacheHelper::getPostCountCacheKey($search);
            $isCached = CacheHelper::has($listCacheKey) && CacheHelper::has($countCacheKey);
            
            // 검색 성능 로깅 강화
            if ($search) {
                SearchHelper::logSearch($search, $totalCount, $query_time / 1000);
                error_log("🔍 검색 완료 ['{$search}']: {$query_time}ms, {$totalCount}개 결과, " . count($posts) . "개 표시" . ($isCached ? ' (캐시)' : ' (DB)'));
            } else {
                error_log("📊 데이터 조회 완료: {$query_time}ms, 게시글 {$totalCount}개 중 " . count($posts) . "개 조회" . ($isCached ? ' (캐시 히트)' : ' (DB 쿼리)'));
            }
            
            // 뷰에 전달할 데이터 준비
            $data = [
                'posts' => $posts,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalCount' => $totalCount,
                'search' => $search,
                'filter' => $filter,
                'pageSize' => $pageSize,
                'hasNextPage' => $page < $totalPages,
                'hasPrevPage' => $page > 1,
                'performanceLogs' => WebLogger::getLogs(), // 성능 로그 추가
                'showDebugInfo' => isset($_GET['debug']) || $search // 검색 시 또는 debug 파라미터 시 표시
            ];
            
            error_log('📊 게시글 조회 완료: ' . count($posts) . '개, 페이지: ' . $page . '/' . $totalPages);
            
            // 메모리 사용량 모니터링
            $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
            $peakMemory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
            error_log("💾 메모리 사용량: {$memoryUsage}MB (최대: {$peakMemory}MB)");
            
            // 캐시 효율성 로깅
            if ($isCached) {
                error_log('⚡ 캐시 활용으로 빠른 응답 완료');
            } else {
                error_log('🔄 데이터베이스 쿼리 실행 (캐시 미스)');
            }
            
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
            
            // 좋아요 정보 조회 (테이블이 없으면 기본값 사용)
            $isLiked = false;
            if ($currentUserId) {
                try {
                    $likeResult = $this->db->fetch("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?", [$postId, $currentUserId]);
                    $isLiked = (bool)$likeResult;
                } catch (Exception $e) {
                    // post_likes 테이블이 없는 경우 기본값 사용
                    error_log('post_likes 테이블 접근 실패: ' . $e->getMessage());
                    $isLiked = false;
                }
            }
            
            $data = [
                'post' => $post,
                'isOwner' => $isOwner,
                'currentUserId' => $currentUserId,
                'isLiked' => $isLiked
            ];
            
            error_log('📖 게시글 조회 완료: ID=' . $postId . ', 제목=' . $post['title']);
            
            // OG 태그 데이터 추가
            $headerData = [
                'page_title' => htmlspecialchars($post['title']),
                'page_description' => htmlspecialchars(substr(strip_tags($post['content']), 0, 150)),
                'pageSection' => 'community',
                'og_type' => 'article',
                'og_title' => htmlspecialchars($post['title']) . ' - 탑마케팅 커뮤니티',
                'og_description' => htmlspecialchars(substr(strip_tags($post['content']), 0, 200)),
                'keywords' => '마케팅 커뮤니티, ' . htmlspecialchars($post['author_name']) . ', 게시글, ' . htmlspecialchars($post['title'])
            ];

            // 뷰 렌더링
            $this->renderView('community/detail', $data, $headerData);
            
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
            
            // 게시글 생성 (이미지 경로 추출)
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $imagePath = $this->extractImagePathFromContent($content);
            
            $postData = [
                'user_id' => $currentUserId,
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath
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
            
            // 게시글 수정 (이미지 경로 추출)
            $imagePath = $this->extractImagePathFromContent($content);
            
            $updateData = [
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath
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
        
        // /community/posts/{id} 패턴에서 ID 추출 (기존 PHP 라우트)
        if (preg_match('/\/community\/posts\/(\d+)/', $uri, $matches)) {
            return intval($matches[1]);
        }
        
        // /api/community/posts/{id} 패턴에서 ID 추출 (React API 라우트)
        if (preg_match('/\/api\/community\/posts\/(\d+)/', $uri, $matches)) {
            return intval($matches[1]);
        }
        
        return null;
    }
    
    /**
     * 컨텐츠에서 첫 번째 이미지 경로 추출
     */
    private function extractImagePathFromContent($content) {
        // HTML에서 img 태그의 src 속성 추출
        preg_match('/<img[^>]+src=[\'"](\/assets\/uploads\/[^"\']+)[\'"][^>]*>/i', $content, $matches);
        
        if (!empty($matches[1])) {
            return $matches[1];
        }
        
        // Markdown 형식의 이미지도 확인 ![alt](path)
        preg_match('/!\[[^\]]*\]\(([^)]+)\)/', $content, $matches);
        
        if (!empty($matches[1]) && strpos($matches[1], '/assets/uploads/') === 0) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * 뷰 렌더링
     */
    private function renderView($viewName, $data = [], $headerData = []) {
        // 뷰 데이터를 변수로 추출
        extract($data);
        
        // 공통 헤더 포함
        $defaultHeaderData = [
            'page_title' => '커뮤니티 게시판',
            'page_description' => '탑마케팅 커뮤니티에서 정보를 공유하고 소통하세요',
            'pageSection' => 'community'  // currentPage와 겹치지 않도록 변수명 변경
        ];
        
        // 전달받은 헤더 데이터와 기본값 병합
        $headerData = array_merge($defaultHeaderData, $headerData);
        
        extract($headerData);
        
        // 헤더 include
        require_once SRC_PATH . '/views/templates/header.php';
        
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
    
    /**
     * 캐시 상태 확인 (관리자용)
     */
    public function cacheStatus() {
        // 관리자 권한 확인
        if (!AuthMiddleware::isAdmin()) {
            ResponseHelper::jsonError('권한이 없습니다.', 403);
            return;
        }
        
        $cacheDir = '/tmp/topmkt_cache';
        $status = [
            'cache_enabled' => is_dir($cacheDir),
            'cache_files' => 0,
            'total_size' => 0,
            'oldest_cache' => null,
            'newest_cache' => null
        ];
        
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*.cache');
            $status['cache_files'] = count($files);
            
            foreach ($files as $file) {
                $status['total_size'] += filesize($file);
                $mtime = filemtime($file);
                
                if (!$status['oldest_cache'] || $mtime < $status['oldest_cache']) {
                    $status['oldest_cache'] = $mtime;
                }
                
                if (!$status['newest_cache'] || $mtime > $status['newest_cache']) {
                    $status['newest_cache'] = $mtime;
                }
            }
        }
        
        $status['total_size_mb'] = round($status['total_size'] / 1024 / 1024, 2);
        $status['oldest_cache_formatted'] = $status['oldest_cache'] ? date('Y-m-d H:i:s', $status['oldest_cache']) : null;
        $status['newest_cache_formatted'] = $status['newest_cache'] ? date('Y-m-d H:i:s', $status['newest_cache']) : null;
        
        ResponseHelper::json(['success' => true, 'cache_status' => $status]);
    }
    
    /**
     * 캐시 비우기 (관리자용)
     */
    public function clearCache() {
        // 관리자 권한 확인
        if (!AuthMiddleware::isAdmin()) {
            ResponseHelper::jsonError('권한이 없습니다.', 403);
            return;
        }
        
        try {
            $result = CacheHelper::clear();
            
            if ($result) {
                error_log('🗑️ 관리자가 캐시를 모두 삭제했습니다');
                ResponseHelper::jsonSuccess('캐시가 성공적으로 삭제되었습니다.');
            } else {
                ResponseHelper::jsonError('캐시 삭제에 실패했습니다.');
            }
        } catch (Exception $e) {
            error_log('❌ 캐시 삭제 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('캐시 삭제 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * React API: 커뮤니티 게시글 목록 조회
     */
    public function apiIndex() {
        error_log('🚀 [API] CommunityController::apiIndex() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // 데이터베이스 연결 확인
            if (!$this->db) {
                throw new Exception('데이터베이스 연결 실패');
            }
            
            // 페이지 번호 가져오기
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $pageSize = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 20;
            
            // 검색어 및 필터 가져오기
            $searchRaw = isset($_GET['search']) ? trim($_GET['search']) : null;
            $filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
            $search = null;
            
            // 필터 유효성 검증
            $allowedFilters = ['all', 'title', 'content', 'author'];
            if (!in_array($filter, $allowedFilters)) {
                $filter = 'all';
            }
            
            if ($searchRaw) {
                $searchValidation = SearchHelper::validateSearchTerm($searchRaw);
                if ($searchValidation['valid']) {
                    $search = $searchValidation['cleaned'];
                } else {
                    error_log('❌ API 검색어 유효성 검증 실패: ' . $searchValidation['message']);
                    ResponseHelper::jsonError($searchValidation['message'], 400);
                    return;
                }
            }
            
            error_log("📊 [API] 요청 파라미터: page={$page}, limit={$pageSize}, search=" . ($search ? '[검색어있음]' : 'null') . ", filter={$filter}");
            
            // 성능 모니터링 시작
            $requestStartTime = microtime(true);
            WebLogger::init();
            WebLogger::log("🚀 [API] 커뮤니티 목록 API 시작: " . date('H:i:s.u'));
            
            // 총 개수 조회
            $totalCount = $this->postModel->getTotalCount($search, $filter);
            $totalPages = ceil($totalCount / $pageSize);
            
            // 페이지 범위 검증
            if ($page > $totalPages && $totalPages > 0) {
                error_log("⚠️ [API] 잘못된 페이지 요청: {$page} (최대: {$totalPages})");
                ResponseHelper::jsonError('요청한 페이지가 존재하지 않습니다.', 404);
                return;
            }
            
            // 게시글 목록 조회
            $posts = $this->postModel->getList($page, $pageSize, $search, $filter);
            
            // 성능 로깅
            $totalRequestTime = (microtime(true) - $requestStartTime) * 1000;
            WebLogger::log("🏁 [API] 전체 요청 완료: " . round($totalRequestTime, 2) . "ms");
            
            if ($search) {
                error_log("🔍 [API] 검색 완료 ['{$search}']: " . round($totalRequestTime, 2) . "ms, {$totalCount}개 결과");
            } else {
                error_log("📊 [API] 목록 조회 완료: " . round($totalRequestTime, 2) . "ms, {$totalCount}개 중 " . count($posts) . "개");
            }
            
            // 응답 데이터 구성
            $response = [
                'success' => true,
                'data' => [
                    'posts' => $posts,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_count' => $totalCount,
                        'page_size' => $pageSize,
                        'has_next' => $page < $totalPages,
                        'has_prev' => $page > 1
                    ],
                    'filters' => [
                        'search' => $search,
                        'filter' => $filter
                    ]
                ]
            ];
            
            ResponseHelper::json($response);
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiIndex() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 목록을 불러오는 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 커뮤니티 게시글 상세 조회
     */
    public function apiShow() {
        error_log('📄 [API] CommunityController::apiShow() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
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
            
            // 현재 사용자 정보
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId && $currentUserId == $post['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            // 좋아요 정보 조회
            $isLiked = false;
            if ($currentUserId) {
                try {
                    $likeResult = $this->db->fetch("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?", [$postId, $currentUserId]);
                    $isLiked = (bool)$likeResult;
                } catch (Exception $e) {
                    error_log('post_likes 테이블 접근 실패: ' . $e->getMessage());
                    $isLiked = false;
                }
            }
            
            // 조회수 증가 (세션으로 중복 방지)
            if (!isset($_SESSION['viewed_posts'])) {
                $_SESSION['viewed_posts'] = [];
            }
            
            if (!in_array($postId, $_SESSION['viewed_posts'])) {
                try {
                    $this->db->execute("UPDATE posts SET view_count = view_count + 1 WHERE id = ?", [$postId]);
                    $_SESSION['viewed_posts'][] = $postId;
                    $post['view_count']++;
                } catch (Exception $e) {
                    error_log('조회수 증가 실패: ' . $e->getMessage());
                }
            }
            
            $response = [
                'success' => true,
                'data' => [
                    'post' => $post,
                    'user_permissions' => [
                        'is_owner' => $isOwner,
                        'is_admin' => $isAdmin,
                        'can_edit' => $isOwner || $isAdmin,
                        'can_delete' => $isOwner || $isAdmin
                    ],
                    'user_actions' => [
                        'is_liked' => $isLiked,
                        'is_logged_in' => (bool)$currentUserId
                    ]
                ]
            ];
            
            error_log("📖 [API] 게시글 조회 완료: ID={$postId}, 제목={$post['title']}");
            ResponseHelper::json($response);
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiShow() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글을 불러오는 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 커뮤니티 게시글 작성
     */
    public function apiCreate() {
        error_log('📝 [API] CommunityController::apiCreate() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
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
            // JSON 데이터 파싱
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            
            if (!$inputData) {
                // JSON이 아닌 경우 POST 데이터 사용
                $inputData = $_POST;
            }
            
            // 입력 데이터 검증
            $title = trim($inputData['title'] ?? '');
            $content = trim($inputData['content'] ?? '');
            
            // 유효성 검사 (기존과 동일)
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
            $imagePath = $this->extractImagePathFromContent($content);
            
            $postData = [
                'user_id' => $currentUserId,
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath
            ];
            
            $postId = $this->postModel->create($postData);
            
            if ($postId) {
                error_log("✅ [API] 게시글 작성 성공: ID={$postId}, 작성자={$currentUserId}");
                
                // 생성된 게시글 정보 조회
                $newPost = $this->postModel->getById($postId);
                
                ResponseHelper::jsonSuccess('게시글이 작성되었습니다.', [
                    'post_id' => $postId,
                    'post' => $newPost
                ]);
            } else {
                error_log('❌ [API] 게시글 작성 실패');
                ResponseHelper::jsonError('게시글 작성에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiCreate() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 작성 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 커뮤니티 게시글 수정
     */
    public function apiUpdate() {
        error_log('📝 [API] CommunityController::apiUpdate() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // PUT 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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
            
            // JSON 데이터 파싱
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            
            if (!$inputData) {
                ResponseHelper::jsonError('잘못된 요청 데이터입니다.', 400);
                return;
            }
            
            // 입력 데이터 검증
            $title = trim($inputData['title'] ?? '');
            $content = trim($inputData['content'] ?? '');
            
            // 유효성 검사 (기존과 동일)
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
            $imagePath = $this->extractImagePathFromContent($content);
            
            $updateData = [
                'title' => $title,
                'content' => $content,
                'image_path' => $imagePath
            ];
            
            $success = $this->postModel->update($postId, $updateData);
            
            if ($success) {
                error_log("✅ [API] 게시글 수정 성공: ID={$postId}");
                
                // 수정된 게시글 정보 조회
                $updatedPost = $this->postModel->getById($postId);
                
                ResponseHelper::jsonSuccess('게시글이 수정되었습니다.', [
                    'post_id' => $postId,
                    'post' => $updatedPost
                ]);
            } else {
                error_log("❌ [API] 게시글 수정 실패: ID={$postId}");
                ResponseHelper::jsonError('게시글 수정에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiUpdate() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 수정 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 커뮤니티 게시글 삭제
     */
    public function apiDelete() {
        error_log('🗑️ [API] CommunityController::apiDelete() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // DELETE 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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
                error_log("✅ [API] 게시글 삭제 성공: ID={$postId}");
                ResponseHelper::jsonSuccess('게시글이 삭제되었습니다.', [
                    'deleted_post_id' => $postId
                ]);
            } else {
                error_log("❌ [API] 게시글 삭제 실패: ID={$postId}");
                ResponseHelper::jsonError('게시글 삭제에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiDelete() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('게시글 삭제 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 게시글 댓글 목록 조회
     */
    public function apiGetComments() {
        error_log('💬 [API] CommunityController::apiGetComments() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            // URL에서 게시글 ID 추출
            $postId = $this->getPostIdFromUrl();
            
            if (!$postId) {
                ResponseHelper::jsonError('잘못된 게시글 번호입니다.', 400);
                return;
            }
            
            // 게시글 존재 확인
            $post = $this->postModel->getById($postId);
            if (!$post) {
                ResponseHelper::jsonError('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 댓글 모델 초기화
            require_once SRC_PATH . '/models/Comment.php';
            $commentModel = new Comment();
            
            // 페이지네이션 파라미터
            $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
            $limit = isset($_GET['limit']) ? min(50, max(1, intval($_GET['limit']))) : 20;
            
            // 댓글 목록 조회
            $comments = $commentModel->getByPostId($postId, $page, $limit);
            $totalCount = $commentModel->getCountByPostId($postId);
            $totalPages = ceil($totalCount / $limit);
            
            // 현재 사용자 권한 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            
            // 각 댓글에 권한 정보 추가
            foreach ($comments as &$comment) {
                $comment['user_permissions'] = [
                    'is_owner' => $currentUserId && $currentUserId == $comment['user_id'],
                    'is_admin' => AuthMiddleware::isAdmin(),
                    'can_edit' => $currentUserId && ($currentUserId == $comment['user_id'] || AuthMiddleware::isAdmin()),
                    'can_delete' => $currentUserId && ($currentUserId == $comment['user_id'] || AuthMiddleware::isAdmin())
                ];
            }
            
            $response = [
                'success' => true,
                'data' => [
                    'comments' => $comments,
                    'pagination' => [
                        'current_page' => $page,
                        'total_pages' => $totalPages,
                        'total_count' => $totalCount,
                        'page_size' => $limit,
                        'has_next' => $page < $totalPages,
                        'has_prev' => $page > 1
                    ]
                ]
            ];
            
            error_log("💬 [API] 댓글 조회 완료: 게시글 ID={$postId}, 댓글 수={$totalCount}");
            ResponseHelper::json($response);
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiGetComments() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('댓글을 불러오는 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 댓글 작성
     */
    public function apiCreateComment() {
        error_log('💬 [API] CommunityController::apiCreateComment() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
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
            
            // 게시글 존재 확인
            $post = $this->postModel->getById($postId);
            if (!$post) {
                ResponseHelper::jsonError('게시글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // JSON 데이터 파싱
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            
            if (!$inputData) {
                // JSON이 아닌 경우 POST 데이터 사용
                $inputData = $_POST;
            }
            
            // 입력 데이터 검증
            $content = trim($inputData['content'] ?? '');
            $parentId = isset($inputData['parent_id']) ? intval($inputData['parent_id']) : null;
            
            // 유효성 검사
            if (empty($content)) {
                ResponseHelper::jsonError('댓글 내용을 입력해주세요.', 400);
                return;
            }
            
            if (strlen($content) > 1000) {
                ResponseHelper::jsonError('댓글은 1,000자 이내로 입력해주세요.', 400);
                return;
            }
            
            // 부모 댓글 존재 확인 (대댓글인 경우)
            if ($parentId) {
                require_once SRC_PATH . '/models/Comment.php';
                $commentModel = new Comment();
                $parentComment = $commentModel->getById($parentId);
                
                if (!$parentComment || $parentComment['post_id'] != $postId) {
                    ResponseHelper::jsonError('부모 댓글을 찾을 수 없습니다.', 404);
                    return;
                }
            }
            
            // 댓글 생성
            $currentUserId = AuthMiddleware::getCurrentUserId();
            
            $commentData = [
                'post_id' => $postId,
                'user_id' => $currentUserId,
                'parent_id' => $parentId,
                'content' => $content
            ];
            
            require_once SRC_PATH . '/models/Comment.php';
            $commentModel = new Comment();
            $commentId = $commentModel->create($commentData);
            
            if ($commentId) {
                error_log("✅ [API] 댓글 작성 성공: ID={$commentId}, 게시글 ID={$postId}");
                
                // 생성된 댓글 정보 조회
                $newComment = $commentModel->getById($commentId);
                
                ResponseHelper::jsonSuccess('댓글이 작성되었습니다.', [
                    'comment_id' => $commentId,
                    'comment' => $newComment
                ]);
            } else {
                error_log('❌ [API] 댓글 작성 실패');
                ResponseHelper::jsonError('댓글 작성에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiCreateComment() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('댓글 작성 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 댓글 수정
     */
    public function apiUpdateComment() {
        error_log('💬 [API] CommunityController::apiUpdateComment() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // PUT 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            ResponseHelper::jsonError('잘못된 요청 방식입니다.', 405);
            return;
        }
        
        try {
            // URL에서 게시글 ID와 댓글 ID 추출
            $postId = $this->getPostIdFromUrl();
            $commentId = $this->getCommentIdFromUrl();
            
            if (!$postId || !$commentId) {
                ResponseHelper::jsonError('잘못된 요청입니다.', 400);
                return;
            }
            
            // 댓글 조회 및 권한 확인
            require_once SRC_PATH . '/models/Comment.php';
            $commentModel = new Comment();
            $comment = $commentModel->getById($commentId);
            
            if (!$comment || $comment['post_id'] != $postId) {
                ResponseHelper::jsonError('댓글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 권한 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId == $comment['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            if (!$isOwner && !$isAdmin) {
                ResponseHelper::jsonError('수정 권한이 없습니다.', 403);
                return;
            }
            
            // JSON 데이터 파싱
            $rawInput = file_get_contents('php://input');
            $inputData = json_decode($rawInput, true);
            
            if (!$inputData) {
                ResponseHelper::jsonError('잘못된 요청 데이터입니다.', 400);
                return;
            }
            
            // 입력 데이터 검증
            $content = trim($inputData['content'] ?? '');
            
            if (empty($content)) {
                ResponseHelper::jsonError('댓글 내용을 입력해주세요.', 400);
                return;
            }
            
            if (strlen($content) > 1000) {
                ResponseHelper::jsonError('댓글은 1,000자 이내로 입력해주세요.', 400);
                return;
            }
            
            // 댓글 수정
            $updateData = ['content' => $content];
            $success = $commentModel->update($commentId, $updateData);
            
            if ($success) {
                error_log("✅ [API] 댓글 수정 성공: ID={$commentId}");
                
                // 수정된 댓글 정보 조회
                $updatedComment = $commentModel->getById($commentId);
                
                ResponseHelper::jsonSuccess('댓글이 수정되었습니다.', [
                    'comment_id' => $commentId,
                    'comment' => $updatedComment
                ]);
            } else {
                error_log("❌ [API] 댓글 수정 실패: ID={$commentId}");
                ResponseHelper::jsonError('댓글 수정에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiUpdateComment() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('댓글 수정 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * React API: 댓글 삭제
     */
    public function apiDeleteComment() {
        error_log('💬 [API] CommunityController::apiDeleteComment() 호출');
        
        // CORS 헤더 설정
        header('Content-Type: application/json; charset=utf-8');
        
        // 로그인 확인
        if (!AuthMiddleware::isLoggedIn()) {
            ResponseHelper::jsonError('로그인이 필요합니다.', 401);
            return;
        }
        
        // DELETE 요청만 허용
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            ResponseHelper::jsonError('잘못된 요청 방식입니다.', 405);
            return;
        }
        
        try {
            // URL에서 게시글 ID와 댓글 ID 추출
            $postId = $this->getPostIdFromUrl();
            $commentId = $this->getCommentIdFromUrl();
            
            if (!$postId || !$commentId) {
                ResponseHelper::jsonError('잘못된 요청입니다.', 400);
                return;
            }
            
            // 댓글 조회 및 권한 확인
            require_once SRC_PATH . '/models/Comment.php';
            $commentModel = new Comment();
            $comment = $commentModel->getById($commentId);
            
            if (!$comment || $comment['post_id'] != $postId) {
                ResponseHelper::jsonError('댓글을 찾을 수 없습니다.', 404);
                return;
            }
            
            // 권한 확인
            $currentUserId = AuthMiddleware::getCurrentUserId();
            $isOwner = $currentUserId == $comment['user_id'];
            $isAdmin = AuthMiddleware::isAdmin();
            
            if (!$isOwner && !$isAdmin) {
                ResponseHelper::jsonError('삭제 권한이 없습니다.', 403);
                return;
            }
            
            // 댓글 삭제
            $success = $commentModel->delete($commentId);
            
            if ($success) {
                error_log("✅ [API] 댓글 삭제 성공: ID={$commentId}");
                ResponseHelper::jsonSuccess('댓글이 삭제되었습니다.', [
                    'deleted_comment_id' => $commentId
                ]);
            } else {
                error_log("❌ [API] 댓글 삭제 실패: ID={$commentId}");
                ResponseHelper::jsonError('댓글 삭제에 실패했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log('❌ [API] CommunityController::apiDeleteComment() 오류: ' . $e->getMessage());
            ResponseHelper::jsonError('댓글 삭제 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * URL에서 댓글 ID 추출
     */
    private function getCommentIdFromUrl() {
        $uri = $_SERVER['REQUEST_URI'];
        
        // /api/community/posts/{id}/comments/{commentId} 패턴에서 댓글 ID 추출
        if (preg_match('/\/api\/community\/posts\/\d+\/comments\/(\d+)/', $uri, $matches)) {
            return intval($matches[1]);
        }
        
        return null;
    }
}