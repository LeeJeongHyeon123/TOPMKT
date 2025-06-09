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
                    content_type, event_scale, has_networking, sponsor_info,
                    dress_code, parking_info, created_at
                FROM lectures 
                WHERE content_type = 'event'
                    AND status = 'published'
                    AND start_date BETWEEN ? AND ?
                ORDER BY start_date ASC, start_time ASC";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            
            // 뷰에 전달할 데이터
            $data = [
                'page_title' => $event['title'],
                'page_description' => mb_substr(strip_tags($event['description']), 0, 160),
                'event' => $event,
                'current_user' => $this->getCurrentUser(),
                'og_title' => $event['title'] . ' - 탑마케팅 행사',
                'og_description' => mb_substr(strip_tags($event['description']), 0, 160),
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
                    id, title, description, instructor_name, instructor_info,
                    start_date, end_date, start_time, end_time,
                    location_type, venue_name, venue_address, online_link,
                    max_participants, registration_fee, category, status,
                    content_type, event_scale, has_networking, sponsor_info,
                    dress_code, parking_info, created_at, user_id, instructor_image, youtube_video
                FROM lectures 
                WHERE id = ? AND content_type = 'event' AND status = 'published'";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->execute([$eventId]);
        
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
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
                        'url' => '/assets/uploads/events/marketing-workshop-main.jpg',
                        'alt_text' => '여름 마케팅 전략 워크샵 메인 이미지'
                    ],
                    [
                        'id' => 2,
                        'url' => '/assets/uploads/events/marketing-workshop-audience.jpg',
                        'alt_text' => '워크샵 참가자들 모습'
                    ],
                    [
                        'id' => 3,
                        'url' => '/assets/uploads/events/marketing-workshop-presentation.jpg',
                        'alt_text' => '강의 진행 모습'
                    ],
                    [
                        'id' => 4,
                        'url' => '/assets/uploads/events/marketing-workshop-networking.jpg',
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
        
        // 권한 확인 (PREMIUM 이상)
        if (!in_array($currentUser['role'], ['PREMIUM', 'ADMIN', 'SUPER_ADMIN'])) {
            $this->showErrorPage("행사 등록 권한이 없습니다. 프리미엄 멤버십이 필요합니다.", 403);
            return;
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
        
        // 로그인 및 권한 확인
        $currentUser = $this->getCurrentUser();
        if (!$currentUser || !in_array($currentUser['role'], ['PREMIUM', 'ADMIN', 'SUPER_ADMIN'])) {
            ResponseHelper::json(['success' => false, 'message' => '권한이 없습니다.'], 403);
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
            'registration_fee', 'event_scale', 'has_networking', 'sponsor_info',
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
        $validated['has_networking'] = !empty($data['has_networking']);
        
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
                    event_scale, has_networking, sponsor_info, dress_code, parking_info,
                    status, created_at
                ) VALUES (
                    :user_id, :title, :description, :instructor_name, :instructor_info,
                    :start_date, :end_date, :start_time, :end_time,
                    :location_type, :venue_name, :venue_address, :online_link,
                    :max_participants, :registration_fee, :category, :content_type,
                    :event_scale, :has_networking, :sponsor_info, :dress_code, :parking_info,
                    'published', NOW()
                )";
        
        $stmt = $this->db->getConnection()->prepare($sql);
        
        $params = array_merge($data, ['user_id' => $userId]);
        $stmt->execute($params);
        
        return $this->db->getConnection()->lastInsertId();
    }
    
    /**
     * 뷰 렌더링 헬퍼
     */
    private function render($view, $data = []) {
        // 헤더 포함
        require_once SRC_PATH . '/views/templates/header.php';
        
        // 데이터 추출
        extract($data);
        
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
            $stmt = $this->db->getConnection()->prepare($sql);
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("getCurrentUser 오류: " . $e->getMessage());
            return null;
        }
    }
}