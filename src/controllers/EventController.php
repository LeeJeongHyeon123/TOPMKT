<?php
/**
 * 행사 일정 컨트롤러
 * 행사 일정 관리 기능 (강의 시스템 확장)
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/controllers/LectureController.php';

class EventController extends LectureController {
    private $db;
    private $userModel;
    
    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->userModel = new User();
        } catch (Exception $e) {
            error_log("EventController 초기화 오류: " . $e->getMessage());
            header('Location: /?error=db_connection');
            exit;
        }
    }
    
    /**
     * 행사 일정 메인 페이지 (캘린더 뷰)
     */
    public function index() {
        try {
            // 데이터베이스 테이블 존재 확인
            if (!$this->checkTablesExist()) {
                $this->showSetupPage();
                return;
            }
            
            // 현재 월/년도 파라미터 처리
            $year = $_GET['year'] ?? date('Y');
            $month = $_GET['month'] ?? date('m');
            $view = $_GET['view'] ?? 'calendar'; // calendar, list
            
            // 유효성 검사
            $year = intval($year);
            $month = intval($month);
            
            if ($year < 2020 || $year > 2030) $year = date('Y');
            if ($month < 1 || $month > 12) $month = date('m');
            
            // 해당 월의 행사 일정 조회 (content_type = 'event')
            $events = $this->getEventsByMonth($year, $month);
            
            // 캘린더 데이터 생성
            $calendarData = $this->generateCalendarData($year, $month, $events);
            
            // 뷰에 전달할 데이터
            $data = [
                'page_title' => '행사 일정',
                'page_description' => '다양한 마케팅 행사와 네트워킹 행사 일정을 확인하세요.',
                'year' => $year,
                'month' => $month,
                'view' => $view,
                'events' => $events,
                'calendar_data' => $calendarData,
                'prev_month' => $this->getPrevMonth($year, $month),
                'next_month' => $this->getNextMonth($year, $month),
                'current_user' => $this->getCurrentUser()
            ];
            
            // 뷰 렌더링
            echo "<!-- 디버그: \$view = {$view} -->";
            if ($view === 'list') {
                echo "<!-- 디버그: 리스트 뷰 렌더링 -->";
                $this->render('events/list', $data);
            } else {
                echo "<!-- 디버그: 캘린더 뷰 렌더링 (events/index) -->";
                $this->render('events/index', $data);
            }
            
        } catch (Exception $e) {
            error_log("EventController::index 오류: " . $e->getMessage());
            $this->showErrorPage("행사 일정을 불러오는 중 오류가 발생했습니다.");
        }
    }
    
    /**
     * 특정 월의 행사 일정 조회
     */
    private function getEventsByMonth($year, $month) {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate)); // 해당 월의 마지막 날
        
        $sql = "SELECT 
                    id, title, description, instructor_name, instructor_info,
                    start_date, end_date, start_time, end_time,
                    location_type, venue_name, venue_address, online_link,
                    max_participants, registration_fee, category, status,
                    content_type, sponsor_info,
                    dress_code, parking_info, created_at
                FROM lectures 
                WHERE content_type = 'event'
                    AND status = 'published'
                    AND start_date BETWEEN ? AND ?
                ORDER BY start_date ASC, start_time ASC";
        
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }
    
    /**
     * 행사 상세 페이지
     */
    public function detail() {
        $eventId = $_GET['id'] ?? null;
        
        if (!$eventId || !is_numeric($eventId)) {
            $this->showErrorPage("올바르지 않은 행사 ID입니다.", 400);
            return;
        }
        
        try {
            // 행사 정보 조회
            $event = $this->getEventById($eventId);
            
            if (!$event) {
                $this->showErrorPage("존재하지 않는 행사입니다.", 404);
                return;
            }
            
            // OG 메타 태그용 깨끗한 설명 생성
            $cleanDescription = $this->generateCleanDescription($event['description']);
            
            // OG 이미지 설정 (행사 이미지가 있으면 첫 번째 이미지 사용)
            $ogImage = 'https://' . $_SERVER['HTTP_HOST'] . '/assets/images/topmkt-og-image.png?v=' . date('Ymd');
            if (!empty($event['images']) && isset($event['images'][0]['url'])) {
                $ogImage = 'https://' . $_SERVER['HTTP_HOST'] . $event['images'][0]['url'];
            }
            
            // 뷰에 전달할 데이터
            $data = [
                'page_title' => $event['title'],
                'page_description' => $cleanDescription,
                'event' => $event,
                'current_user' => $this->getCurrentUser(),
                'og_title' => $event['title'] . ' - 탑마케팅 행사',
                'og_description' => $cleanDescription,
                'og_image' => $ogImage,
                'og_type' => 'article'
            ];
            
            // 뷰 렌더링
            $this->render('events/detail', $data);
            
        } catch (Exception $e) {
            error_log("EventController::detail 오류: " . $e->getMessage());
            $this->showErrorPage("행사 정보를 불러오는 중 오류가 발생했습니다.");
        }
    }
    
    /**
     * 행사 ID로 단일 행사 조회
     */
    private function getEventById($eventId) {
        $sql = "SELECT 
                    l.id, l.title, l.description, l.instructor_name, l.instructor_info,
                    l.start_date, l.end_date, l.start_time, l.end_time,
                    l.location_type, l.venue_name, l.venue_address, l.online_link,
                    l.max_participants, l.registration_fee, l.category, l.status,
                    l.content_type, l.sponsor_info,
                    l.dress_code, l.parking_info, l.created_at, l.user_id, l.instructor_image, l.youtube_video,
                    u.nickname as author_name, u.bio as author_bio, 
                    u.profile_image, u.profile_image_original
                FROM lectures l
                LEFT JOIN users u ON l.user_id = u.id
                WHERE l.id = ? AND l.content_type = 'event' AND l.status = 'published'";
        
        $event = $this->db->fetch($sql, [$eventId]);
        
        if ($event) {
            // 행사 이미지 추가
            $event['images'] = $this->getEventImages($eventId);
        }
        
        return $event;
    }
    
    /**
     * 행사 이미지 조회
     */
    private function getEventImages($eventId) {
        try {
            $sql = "
                SELECT * FROM event_images 
                WHERE event_id = :event_id 
                ORDER BY sort_order ASC, id ASC
            ";
            
            $images = $this->db->fetchAll($sql, [':event_id' => $eventId]);
            
            // 샘플 이미지 fallback (행사 122번용)
            if (empty($images) && $eventId == 122) {
                return [
                    [
                        'id' => 1,
                        'url' => '<?= EVENTS_WEB_PATH ?>/marketing-workshop-main.jpg',
                        'alt_text' => '여름 마케팅 전략 워크샵 메인 이미지'
                    ],
                    [
                        'id' => 2,
                        'url' => '<?= EVENTS_WEB_PATH ?>/marketing-workshop-audience.jpg',
                        'alt_text' => '워크샵 참가자들 모습'
                    ],
                    [
                        'id' => 3,
                        'url' => '<?= EVENTS_WEB_PATH ?>/marketing-workshop-presentation.jpg',
                        'alt_text' => '강의 진행 모습'
                    ],
                    [
                        'id' => 4,
                        'url' => '<?= EVENTS_WEB_PATH ?>/marketing-workshop-networking.jpg',
                        'alt_text' => '네트워킹 세션 모습'
                    ]
                ];
            }
            
            // 데이터베이스 결과를 URL 형식으로 변환
            return array_map(function($image) {
                return [
                    'id' => $image['id'],
                    'url' => $image['image_path'],
                    'alt_text' => $image['alt_text'] ?? ''
                ];
            }, $images);
            
        } catch (Exception $e) {
            error_log("행사 이미지 조회 오류: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 행사 생성 페이지
     */
    public function create() {
        // 로그인 확인
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            header('Location: /auth/login?redirect=' . urlencode('/events/create'));
            exit;
        }
        
        // 기업회원 권한 확인
        require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
        $permission = CorporateMiddleware::checkLectureEventPermission();
        
        if (!$permission['hasPermission']) {
            $_SESSION['error_message'] = $permission['message'];
            header('Location: /corp/info');
            exit;
        }
        
        $data = [
            'page_title' => '새 행사 등록',
            'page_description' => '새로운 마케팅 행사를 등록하세요.',
            'current_user' => $currentUser,
            'action' => 'create'
        ];
        
        $this->render('events/create', $data);
    }
    
    /**
     * 행사 생성 처리
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showErrorPage("잘못된 요청입니다.", 405);
            return;
        }
        
        // 로그인 및 기업회원 권한 확인
        $currentUser = $this->getCurrentUser();
        if (!$currentUser) {
            ResponseHelper::json(['success' => false, 'message' => '로그인이 필요합니다.'], 401);
            return;
        }
        
        require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
        $permission = CorporateMiddleware::checkLectureEventPermission();
        
        if (!$permission['hasPermission']) {
            ResponseHelper::json(['success' => false, 'message' => $permission['message']], 403);
            return;
        }
        
        try {
            // 입력 데이터 검증
            $data = $this->validateEventData($_POST);
            
            // 행사 생성
            $eventId = $this->createEvent($data, $currentUser['id']);
            
            ResponseHelper::json([
                'success' => true,
                'message' => '행사가 성공적으로 등록되었습니다.',
                'event_id' => $eventId,
                'redirect' => '/events/detail?id=' . $eventId
            ]);
            
        } catch (Exception $e) {
            error_log("EventController::store 오류: " . $e->getMessage());
            ResponseHelper::json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * 행사 데이터 검증
     */
    private function validateEventData($data) {
        $validated = [];
        
        // 필수 필드 검증
        $required = ['title', 'description', 'start_date', 'start_time', 'location_type', 'category'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("{$field} 필드는 필수입니다.");
            }
            $validated[$field] = trim($data[$field]);
        }
        
        // 행사 전용 필드 추가
        $validated['content_type'] = 'event';
        
        // 선택적 필드
        $optional = [
            'instructor_name', 'instructor_info', 'end_date', 'end_time',
            'venue_name', 'venue_address', 'online_link', 'max_participants',
            'registration_fee', 'sponsor_info',
            'dress_code', 'parking_info'
        ];
        
        foreach ($optional as $field) {
            $validated[$field] = $data[$field] ?? null;
        }
        
        // 데이터 타입 변환
        if ($validated['max_participants']) {
            $validated['max_participants'] = intval($validated['max_participants']);
        }
        if ($validated['registration_fee']) {
            $validated['registration_fee'] = intval($validated['registration_fee']);
        }
        
        return $validated;
    }
    
    /**
     * 행사 생성
     */
    private function createEvent($data, $userId) {
        $sql = "INSERT INTO lectures (
                    user_id, title, description, instructor_name, instructor_info,
                    start_date, end_date, start_time, end_time,
                    location_type, venue_name, venue_address, online_link,
                    max_participants, registration_fee, category, content_type,
                    sponsor_info, dress_code, parking_info,
                    status, created_at
                ) VALUES (
                    :user_id, :title, :description, :instructor_name, :instructor_info,
                    :start_date, :end_date, :start_time, :end_time,
                    :location_type, :venue_name, :venue_address, :online_link,
                    :max_participants, :registration_fee, :category, :content_type,
                    :sponsor_info, :dress_code, :parking_info,
                    'published', NOW()
                )";
        
        $params = array_merge($data, ['user_id' => $userId]);
        $this->db->execute($sql, $params);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * 뷰 렌더링 헬퍼
     */
    private function render($view, $data = []) {
        // 데이터 추출 (헤더에서 사용할 수 있도록 먼저 실행)
        extract($data);
        
        // 헤더 포함
        require_once SRC_PATH . '/views/templates/header.php';
        
        // 메인 뷰 파일 포함
        $viewPath = SRC_PATH . "/views/{$view}.php";
        echo "<!-- 디버그: 뷰 = {$view}, 경로 = {$viewPath} -->";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            echo "<h1>뷰 파일을 찾을 수 없습니다: {$view}</h1>";
            echo "<p>시도한 경로: {$viewPath}</p>";
            echo "<p>events/index.php 파일 존재 여부: " . (file_exists(SRC_PATH . "/views/events/index.php") ? "있음" : "없음") . "</p>";
        }
        
        // 푸터 포함
        require_once SRC_PATH . '/views/templates/footer.php';
    }
    
    /**
     * 에러 페이지 표시
     */
    private function showErrorPage($message, $code = 500) {
        http_response_code($code);
        $data = [
            'page_title' => 'Error',
            'error_message' => $message,
            'error_code' => $code
        ];
        $this->render('lectures/error', $data);
    }
    
    /**
     * 테이블 존재 확인
     */
    private function checkTablesExist() {
        try {
            $this->db->getConnection()->query("SELECT 1 FROM lectures LIMIT 1");
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * 설정 페이지 표시
     */
    private function showSetupPage() {
        $data = [
            'page_title' => '행사 일정 설정',
            'page_description' => '행사 일정 시스템을 설정합니다.'
        ];
        $this->render('lectures/setup', $data);
    }
    
    /**
     * 캘린더 데이터 생성
     */
    private function generateCalendarData($year, $month, $events) {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $lastDay = mktime(0, 0, 0, $month + 1, 0, $year);
        $firstWeekday = date('w', $firstDay);
        $daysInMonth = date('t', $firstDay);
        
        $calendar = [];
        $week = [];
        
        // 이전 달의 마지막 날들
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear = $month == 1 ? $year - 1 : $year;
        $daysInPrevMonth = date('t', mktime(0, 0, 0, $prevMonth, 1, $prevYear));
        
        for ($i = $firstWeekday - 1; $i >= 0; $i--) {
            $day = $daysInPrevMonth - $i;
            $week[] = [
                'day' => $day,
                'date' => sprintf('%04d-%02d-%02d', $prevYear, $prevMonth, $day),
                'class' => 'other-month'
            ];
        }
        
        // 현재 달의 날들
        $today = date('Y-m-d');
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $class = '';
            if ($date === $today) {
                $class = 'today';
            }
            
            $week[] = [
                'day' => $day,
                'date' => $date,
                'class' => $class
            ];
            
            if (count($week) == 7) {
                $calendar[] = $week;
                $week = [];
            }
        }
        
        // 다음 달의 첫날들
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear = $month == 12 ? $year + 1 : $year;
        $day = 1;
        while (count($week) < 7) {
            $week[] = [
                'day' => $day,
                'date' => sprintf('%04d-%02d-%02d', $nextYear, $nextMonth, $day),
                'class' => 'other-month'
            ];
            $day++;
        }
        
        if (count($week) > 0) {
            $calendar[] = $week;
        }
        
        return $calendar;
    }
    
    /**
     * 이전 달 정보
     */
    private function getPrevMonth($year, $month) {
        if ($month == 1) {
            return ['year' => $year - 1, 'month' => 12];
        }
        return ['year' => $year, 'month' => $month - 1];
    }
    
    /**
     * 다음 달 정보
     */
    private function getNextMonth($year, $month) {
        if ($month == 12) {
            return ['year' => $year + 1, 'month' => 1];
        }
        return ['year' => $year, 'month' => $month + 1];
    }
    
    /**
     * OG 메타 태그용 깨끗한 설명 생성
     */
    private function generateCleanDescription($description) {
        // 1. Markdown 문법 제거
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $description); // **볼드** 제거
        $text = preg_replace('/\*(.*?)\*/', '$1', $text); // *이탤릭* 제거
        $text = preg_replace('/#{1,6}\s/', '', $text); // # 헤더 제거
        $text = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $text); // [링크](url) 제거
        $text = preg_replace('/```.*?```/s', '', $text); // 코드 블록 제거
        $text = preg_replace('/`(.*?)`/', '$1', $text); // 인라인 코드 제거
        
        // 2. 이모지와 특수 문자 정리
        $text = preg_replace('/[🎯💼🎁🤝📍⭐🔥💡📊🚀]+/', '', $text); // 이모지 제거
        $text = preg_replace('/•\s*/', '- ', $text); // 불릿 포인트 정리
        
        // 3. HTML 태그 제거
        $text = strip_tags($text);
        
        // 4. 연속된 공백과 줄바꿈 정리
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);
        
        // 5. 첫 번째 문장만 추출하여 깔끔하게
        $sentences = preg_split('/[.!?]\s+/', $text);
        $firstSentence = trim($sentences[0]);
        
        // 6. 길이 제한 (160자)
        if (mb_strlen($firstSentence) > 160) {
            $firstSentence = mb_substr($firstSentence, 0, 157) . '...';
        }
        
        return $firstSentence;
    }
    
    /**
     * 행사 신청 API
     */
    public function register($eventId) {
        header('Content-Type: application/json');
        
        try {
            // HTTP 메소드 확인
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return ResponseHelper::json(null, 405, 'POST 메소드만 허용됩니다.');
            }
            
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json(null, 401, '로그인이 필요합니다.');
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // 데이터 파싱 (JSON 또는 폼 데이터 지원)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            $input = [];
            
            if (strpos($contentType, 'application/json') !== false) {
                $rawInput = file_get_contents('php://input');
                if (!empty($rawInput)) {
                    $decoded = json_decode($rawInput, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $input = $decoded;
                    } else {
                        $input = $_POST;
                    }
                } else {
                    $input = $_POST;
                }
            } else {
                $input = $_POST;
            }
            
            if (empty($input)) {
                return ResponseHelper::json(null, 400, '입력 데이터가 없습니다.');
            }
            
            // CSRF 토큰 검증
            if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
                return ResponseHelper::json(null, 403, 'CSRF 토큰이 유효하지 않습니다.');
            }
            
            // 행사 정보 조회
            $eventQuery = "
                SELECT 
                    id, title, start_date, start_time, max_participants, 
                    auto_approval, registration_start_date, 
                    registration_end_date, allow_waiting_list, status, user_id as organizer_id
                FROM lectures 
                WHERE id = ? AND content_type = 'event' AND status = 'published'
            ";
            
            $stmt = $this->db->prepare($eventQuery);
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $event = $stmt->get_result()->fetch_assoc();
            
            if (!$event) {
                return ResponseHelper::json(null, 404, '행사를 찾을 수 없습니다.');
            }
            
            // 본인 행사 신청 방지
            if ($event['organizer_id'] == $userId) {
                return ResponseHelper::json(null, 400, '본인이 등록한 행사에는 신청할 수 없습니다.');
            }
            
            // 기존 신청 확인
            $existingQuery = "SELECT id, status FROM lecture_registrations WHERE lecture_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($existingQuery);
            $stmt->bind_param("ii", $eventId, $userId);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            
            if ($existing && in_array($existing['status'], ['pending', 'approved', 'waiting'])) {
                return ResponseHelper::json(null, 400, '이미 신청하셨습니다.');
            }
            
            // 취소된 신청이 있으면 삭제 (재신청을 위해)
            if ($existing && $existing['status'] === 'cancelled') {
                $deleteQuery = "DELETE FROM lecture_registrations WHERE id = ?";
                $stmt = $this->db->prepare($deleteQuery);
                $stmt->bind_param("i", $existing['id']);
                $stmt->execute();
            }
            
            // 신청 기간 확인
            $now = new DateTime();
            
            if ($event['registration_start_date']) {
                $startDate = new DateTime($event['registration_start_date']);
                if ($now < $startDate) {
                    return ResponseHelper::json(null, 400, '아직 신청 기간이 아닙니다.');
                }
            }
            
            if ($event['registration_end_date']) {
                $endDate = new DateTime($event['registration_end_date']);
                if ($now > $endDate) {
                    return ResponseHelper::json(null, 400, '신청 기간이 마감되었습니다.');
                }
            }
            
            // 행사 시작 시간 확인
            $eventStart = new DateTime($event['start_date'] . ' ' . $event['start_time']);
            if ($now >= $eventStart) {
                return ResponseHelper::json(null, 400, '행사가 이미 시작되었습니다.');
            }
            
            // 사용자 정보 조회
            $userQuery = "SELECT nickname, phone, email FROM users WHERE id = ?";
            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            if (!$user) {
                return ResponseHelper::json(null, 404, '사용자 정보를 찾을 수 없습니다.');
            }
            
            // 입력 데이터 검증
            $validationErrors = $this->validateRegistrationData($input, $user);
            if (!empty($validationErrors)) {
                return ResponseHelper::json(['errors' => $validationErrors], 400, '입력 데이터에 오류가 있습니다.');
            }
            
            // 정원 확인 (승인된 신청 수 조회)
            $approvedCountQuery = "SELECT COUNT(*) as count FROM lecture_registrations WHERE lecture_id = ? AND status = 'approved'";
            $stmt = $this->db->prepare($approvedCountQuery);
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $currentParticipants = $stmt->get_result()->fetch_assoc()['count'];
            
            $isWaitingList = false;
            $waitingOrder = null;
            $status = 'pending';
            
            if ($event['max_participants'] && $currentParticipants >= $event['max_participants']) {
                if (!$event['allow_waiting_list']) {
                    return ResponseHelper::json(null, 400, '정원이 마감되었습니다.');
                }
                
                // 대기자로 등록
                $isWaitingList = true;
                $status = 'waiting';
                
                // 대기 순번 계산
                $waitingQuery = "SELECT MAX(waiting_order) as max_order FROM lecture_registrations WHERE lecture_id = ? AND is_waiting_list = 1";
                $stmt = $this->db->prepare($waitingQuery);
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $waitingOrder = ($result['max_order'] ?? 0) + 1;
            }
            
            // 자동 승인 확인
            if (!$isWaitingList && $event['auto_approval']) {
                $status = 'approved';
            }
            
            // 신청 데이터 구성
            $registrationData = [
                'lecture_id' => $eventId,
                'user_id' => $userId,
                'participant_name' => trim($input['participant_name'] ?? $user['nickname']),
                'participant_email' => trim($input['participant_email'] ?? $user['email']),
                'participant_phone' => trim($input['participant_phone'] ?? $user['phone']),
                'company_name' => trim($input['company_name'] ?? ''),
                'position' => trim($input['position'] ?? ''),
                'motivation' => trim($input['motivation'] ?? ''),
                'special_requests' => trim($input['special_requests'] ?? ''),
                'how_did_you_know' => trim($input['how_did_you_know'] ?? ''),
                'status' => $status,
                'is_waiting_list' => $isWaitingList,
                'waiting_order' => $waitingOrder,
                'processed_by' => null,
                'processed_at' => null
            ];
            
            // 자동 승인인 경우 처리자 정보 설정
            if ($status === 'approved') {
                $registrationData['processed_by'] = $userId;
                $registrationData['processed_at'] = date('Y-m-d H:i:s');
            }
            
            // 트랜잭션 시작
            $this->db->beginTransaction();
            
            try {
                // 신청 등록
                $insertQuery = "
                    INSERT INTO lecture_registrations 
                    (lecture_id, user_id, registration_date, participant_name, participant_email, participant_phone,
                     company_name, position, motivation, special_requests, how_did_you_know,
                     status, is_waiting_list, waiting_order, processed_by, processed_at)
                    VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";
                
                $stmt = $this->db->prepare($insertQuery);
                $stmt->bind_param(
                    "iisssssssssiiis",
                    $registrationData['lecture_id'],
                    $registrationData['user_id'],
                    $registrationData['participant_name'],
                    $registrationData['participant_email'],
                    $registrationData['participant_phone'],
                    $registrationData['company_name'],
                    $registrationData['position'],
                    $registrationData['motivation'],
                    $registrationData['special_requests'],
                    $registrationData['how_did_you_know'],
                    $registrationData['status'],
                    $registrationData['is_waiting_list'],
                    $registrationData['waiting_order'],
                    $registrationData['processed_by'],
                    $registrationData['processed_at']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception('행사 신청 등록에 실패했습니다: ' . $stmt->error);
                }
                
                $registrationId = $this->db->lastInsertId();
                
                // 커밋
                $this->db->commit();
                
                // 행사 신청 확인 SMS 발송
                try {
                    require_once SRC_PATH . '/helpers/SmsHelper.php';
                    $smsResult = sendEventApplicationSms($registrationData['participant_phone']);
                    if ($smsResult['success']) {
                        error_log("행사 신청 확인 SMS 발송 성공: " . $registrationData['participant_phone']);
                    } else {
                        error_log("행사 신청 확인 SMS 발송 실패: " . $smsResult['message']);
                    }
                } catch (Exception $e) {
                    error_log("SMS 발송 오류: " . $e->getMessage());
                    // SMS 실패는 전체 프로세스를 중단하지 않음
                }
                
                $message = $isWaitingList ? 
                    "대기자로 신청이 완료되었습니다. (대기순번: {$waitingOrder}번)" :
                    ($status === 'approved' ? '신청이 승인되었습니다.' : '신청이 완료되었습니다. 승인을 기다려주세요.');
                
                return ResponseHelper::json([
                    'registration_id' => $registrationId,
                    'status' => $status,
                    'is_waiting_list' => $isWaitingList,
                    'waiting_order' => $waitingOrder
                ], 200, $message);
                
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("행사 신청 등록 오류: " . $e->getMessage());
            return ResponseHelper::json(null, 500, '신청 처리 중 오류가 발생했습니다: ' . $e->getMessage());
        }
    }
    
    /**
     * 행사 신청 상태 확인 API
     */
    public function getRegistrationStatus($eventId) {
        header('Content-Type: application/json');
        
        try {
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json(null, 401, '로그인이 필요합니다.');
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // 행사 정보 조회
            $eventQuery = "
                SELECT 
                    l.id, l.title, l.start_date, l.start_time, l.end_date, l.end_time,
                    l.max_participants, l.auto_approval,
                    l.registration_start_date, l.registration_end_date, l.allow_waiting_list,
                    l.status as event_status,
                    COUNT(DISTINCT CASE WHEN lr.status = 'approved' THEN lr.id END) as current_participants
                FROM lectures l
                LEFT JOIN lecture_registrations lr ON l.id = lr.lecture_id
                WHERE l.id = ? AND l.content_type = 'event' AND l.status = 'published'
                GROUP BY l.id, l.title, l.start_date, l.start_time, l.end_date, l.end_time,
                         l.max_participants, l.auto_approval, l.registration_start_date, 
                         l.registration_end_date, l.allow_waiting_list, l.status
            ";
            
            $stmt = $this->db->prepare($eventQuery);
            $stmt->bind_param("i", $eventId);
            $stmt->execute();
            $event = $stmt->get_result()->fetch_assoc();
            
            if (!$event) {
                return ResponseHelper::json(null, 404, '행사를 찾을 수 없습니다.');
            }
            
            // 사용자의 신청 정보 조회
            $registrationQuery = "
                SELECT 
                    id, status, is_waiting_list, waiting_order,
                    created_at, processed_at, admin_notes
                FROM lecture_registrations 
                WHERE lecture_id = ? AND user_id = ?
                ORDER BY created_at DESC 
                LIMIT 1
            ";
            
            $stmt = $this->db->prepare($registrationQuery);
            $stmt->bind_param("ii", $eventId, $userId);
            $stmt->execute();
            $registration = $stmt->get_result()->fetch_assoc();
            
            // 응답 데이터 구성
            $responseData = [
                'event_info' => $event,
                'registration' => $registration,
                'user_id' => $userId
            ];
            
            return ResponseHelper::json($responseData, 200, '신청 상태 조회 완료');
            
        } catch (Exception $e) {
            error_log("행사 신청 상태 조회 오류: " . $e->getMessage());
            return ResponseHelper::json(null, 500, '신청 상태 조회 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 행사 신청 취소 API
     */
    public function cancelRegistration($eventId) {
        header('Content-Type: application/json');
        
        try {
            // HTTP 메소드 확인
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                return ResponseHelper::json(null, 405, 'DELETE 메소드만 허용됩니다.');
            }
            
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json(null, 401, '로그인이 필요합니다.');
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // 데이터 파싱 (JSON 또는 쿼리 파라미터 지원)
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            
            if (strpos($contentType, 'application/json') !== false) {
                $input = json_decode(file_get_contents('php://input'), true);
            } else {
                $input = array_merge($_GET, $_POST);
            }
            
            // CSRF 토큰 검증
            $csrfToken = $input['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            
            if (!$this->validateCsrfToken($csrfToken)) {
                return ResponseHelper::json(null, 403, 'CSRF 토큰이 유효하지 않습니다.');
            }
            
            // 신청 정보 조회
            $registrationQuery = "
                SELECT r.*, l.start_date, l.start_time 
                FROM lecture_registrations r
                JOIN lectures l ON r.lecture_id = l.id
                WHERE r.lecture_id = ? AND r.user_id = ? 
                AND r.status IN ('pending', 'approved', 'waiting')
                ORDER BY r.created_at DESC LIMIT 1
            ";
            
            $stmt = $this->db->prepare($registrationQuery);
            $stmt->bind_param("ii", $eventId, $userId);
            $stmt->execute();
            $registration = $stmt->get_result()->fetch_assoc();
            
            if (!$registration) {
                return ResponseHelper::json(null, 404, '취소할 신청을 찾을 수 없습니다.');
            }
            
            // 행사 시작 시간 확인
            $now = new DateTime();
            $eventStart = new DateTime($registration['start_date'] . ' ' . $registration['start_time']);
            
            if ($now >= $eventStart) {
                return ResponseHelper::json(null, 400, '행사가 이미 시작되어 취소할 수 없습니다.');
            }
            
            // 신청 취소 처리
            $updateQuery = "
                UPDATE lecture_registrations 
                SET status = 'cancelled', processed_at = NOW() 
                WHERE id = ?
            ";
            
            $stmt = $this->db->prepare($updateQuery);
            $stmt->bind_param("i", $registration['id']);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                return ResponseHelper::json([
                    'registration_id' => $registration['id']
                ], 200, '신청이 취소되었습니다.');
            } else {
                return ResponseHelper::json(null, 500, '신청 취소에 실패했습니다.');
            }
            
        } catch (Exception $e) {
            error_log("행사 신청 취소 오류: " . $e->getMessage());
            return ResponseHelper::json(null, 500, '신청 취소 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 이전 행사 신청 데이터 조회 API
     */
    public function getPreviousRegistration($eventId) {
        header('Content-Type: application/json');
        
        try {
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json(null, 401, '로그인이 필요합니다.');
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // 가장 최근 취소된 신청 정보 조회
            $query = "
                SELECT 
                    participant_name, participant_email, participant_phone,
                    company_name, position, motivation, special_requests, how_did_you_know
                FROM lecture_registrations 
                WHERE lecture_id = ? AND user_id = ? AND status = 'cancelled'
                ORDER BY created_at DESC 
                LIMIT 1
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("ii", $eventId, $userId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if ($result) {
                return ResponseHelper::json($result, 200, '이전 신청 데이터 조회 완료');
            } else {
                return ResponseHelper::json(null, 404, '이전 신청 데이터가 없습니다.');
            }
            
        } catch (Exception $e) {
            error_log("이전 행사 신청 데이터 조회 오류: " . $e->getMessage());
            return ResponseHelper::json(null, 500, '이전 신청 데이터 조회 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 행사 신청 데이터 검증
     */
    private function validateRegistrationData($input, $user) {
        $errors = [];
        
        // 필수 필드 검증
        $participantName = trim($input['participant_name'] ?? '');
        $participantEmail = trim($input['participant_email'] ?? '');
        $participantPhone = trim($input['participant_phone'] ?? '');
        
        // 이름 검증
        if (empty($participantName)) {
            $errors['participant_name'] = '이름을 입력해주세요.';
        } elseif (strlen($participantName) < 2) {
            $errors['participant_name'] = '이름은 2글자 이상 입력해주세요.';
        } elseif (strlen($participantName) > 100) {
            $errors['participant_name'] = '이름이 너무 깁니다.';
        }
        
        // 이메일 검증
        if (empty($participantEmail)) {
            $errors['participant_email'] = '이메일을 입력해주세요.';
        } elseif (!filter_var($participantEmail, FILTER_VALIDATE_EMAIL)) {
            $errors['participant_email'] = '올바른 이메일 형식을 입력해주세요.';
        } elseif (strlen($participantEmail) > 255) {
            $errors['participant_email'] = '이메일이 너무 깁니다.';
        }
        
        // 전화번호 검증
        if (empty($participantPhone)) {
            $errors['participant_phone'] = '연락처를 입력해주세요.';
        } elseif (!$this->isValidPhone($participantPhone)) {
            $errors['participant_phone'] = '올바른 연락처 형식을 입력해주세요. (예: 010-1234-5678)';
        }
        
        // 선택적 필드 길이 검증
        if (!empty($input['company_name']) && strlen(trim($input['company_name'])) > 255) {
            $errors['company_name'] = '회사명이 너무 깁니다.';
        }
        
        if (!empty($input['position']) && strlen(trim($input['position'])) > 100) {
            $errors['position'] = '직책명이 너무 깁니다.';
        }
        
        if (!empty($input['motivation']) && strlen(trim($input['motivation'])) > 1000) {
            $errors['motivation'] = '참가 동기가 너무 깁니다. (최대 1000자)';
        }
        
        if (!empty($input['special_requests']) && strlen(trim($input['special_requests'])) > 1000) {
            $errors['special_requests'] = '특별 요청사항이 너무 깁니다. (최대 1000자)';
        }
        
        // how_did_you_know 값 검증
        $validSources = ['website', 'social_media', 'friend_referral', 'company_notice', 'email', 'search_engine', 'advertisement', 'other'];
        if (!empty($input['how_did_you_know']) && !in_array($input['how_did_you_know'], $validSources)) {
            $errors['how_did_you_know'] = '올바른 항목을 선택해주세요.';
        }
        
        return $errors;
    }
    
    /**
     * 전화번호 형식 검증
     */
    private function isValidPhone($phone) {
        // 공백 제거
        $phone = preg_replace('/\s/', '', $phone);
        
        // 한국 휴대폰 번호 형식 검증
        return preg_match('/^(010|011|016|017|018|019)[-]?\d{3,4}[-]?\d{4}$/', $phone);
    }
    
    /**
     * CSRF 토큰 검증
     */
    private function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * 현재 사용자 정보 가져오기
     */
    private function getCurrentUser() {
        if (!AuthMiddleware::isLoggedIn()) {
            return null;
        }
        
        $userId = AuthMiddleware::getCurrentUserId();
        if (!$userId) {
            return null;
        }
        
        try {
            $sql = "SELECT id, nickname, email, role FROM users WHERE id = ? AND status = 'ACTIVE'";
            return $this->db->fetch($sql, [$userId]);
        } catch (Exception $e) {
            error_log("getCurrentUser 오류: " . $e->getMessage());
            return null;
        }
    }
}