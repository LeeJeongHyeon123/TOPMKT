<?php
/**
 * 강의 일정 컨트롤러
 * 강의/행사 일정 관리 기능
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/models/User.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';

class LectureController {
    private $db;
    private $userModel;
    
    public function __construct() {
        try {
            $this->db = Database::getInstance();
            $this->userModel = new User();
        } catch (Exception $e) {
            error_log("LectureController 초기화 오류: " . $e->getMessage());
            // 오류 발생 시 기본 페이지로 리다이렉트
            header('Location: /?error=db_connection');
            exit;
        }
    }
    
    /**
     * 강의 일정 메인 페이지 (캘린더 뷰)
     */
    public function index() {
        try {
            // 데이터베이스 테이블 존재 확인
            if (!$this->checkLectureTablesExist()) {
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
            
            // 해당 월의 강의 목록 조회
            $lectures = $this->getLecturesByMonth($year, $month);
            
            // 카테고리 목록 조회  
            $categories = $this->getCategories();
            
            // 뷰 데이터 준비
            $viewData = [
                'lectures' => $lectures,
                'categories' => $categories,
                'currentYear' => $year,
                'currentMonth' => $month,
                'view' => $view,
                'calendarData' => $this->generateCalendarData($year, $month, $lectures),
                'todayLectures' => $this->getTodayLectures(),
                'upcomingLectures' => $this->getUpcomingLectures(5)
            ];
            
            // 헤더 데이터
            $headerData = [
                'title' => '강의 일정 - 탑마케팅',
                'description' => '다양한 마케팅 강의와 세미나 일정을 확인하고 신청하세요',
                'pageSection' => 'lectures'
            ];
            
            $this->renderView('lectures/index', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 목록 조회 오류: " . $e->getMessage());
            $this->showErrorPage('강의 목록을 불러오는 중 오류가 발생했습니다.', $e->getMessage());
        }
    }
    
    /**
     * 강의 상세 페이지
     */
    public function show($id) {
        try {
            $lectureId = intval($id);
            
            // 강의 정보 조회 및 조회수 증가
            $lecture = $this->getLectureById($lectureId, true);
            
            if (!$lecture) {
                header("HTTP/1.0 404 Not Found");
                $this->renderView('templates/404');
                return;
            }
            
            // 현재 사용자의 신청 상태 확인
            $userRegistration = null;
            if (isset($_SESSION['user_id'])) {
                $userRegistration = $this->getUserRegistration($lectureId, $_SESSION['user_id']);
            }
            
            // 신청자 목록 (일부만)
            $registrations = $this->getLectureRegistrations($lectureId, 5);
            
            // 관련 강의 추천
            $relatedLectures = $this->getRelatedLectures($lecture['category'], $lectureId, 3);
            
            $viewData = [
                'lecture' => $lecture,
                'userRegistration' => $userRegistration,
                'registrations' => $registrations,
                'relatedLectures' => $relatedLectures,
                'canEdit' => $this->canEditLecture($lecture),
                'canRegister' => $this->canRegisterLecture($lecture),
                'iCalUrl' => $this->generateICalUrl($lectureId)
            ];
            
            $headerData = [
                'title' => htmlspecialchars($lecture['title']) . ' - 강의 일정',
                'description' => htmlspecialchars(substr($lecture['description'], 0, 150)),
                'pageSection' => 'lectures'
            ];
            
            $this->renderView('lectures/detail', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 상세 조회 오류: " . $e->getMessage());
            ResponseHelper::sendError('강의 정보를 불러오는 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 강의 작성 폼
     */
    public function create() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login?redirect=' . urlencode('/lectures/create'));
            exit;
        }
        
        // 권한 확인 (기업회원, 관리자만 가능)
        if (!$this->canCreateLecture()) {
            header("HTTP/1.0 403 Forbidden");
            $this->renderView('templates/403', [
                'title' => '접근 권한 없음',
                'message' => '강의 등록은 기업회원만 가능합니다. 기업회원 인증을 진행해주세요.'
            ]);
            return;
        }
        
        try {
            $categories = $this->getCategories();
            
            $viewData = [
                'categories' => $categories,
                'defaultData' => [
                    'location_type' => 'offline',
                    'category' => 'seminar',
                    'difficulty_level' => 'all',
                    'timezone' => 'Asia/Seoul'
                ]
            ];
            
            $headerData = [
                'title' => '강의 등록 - 탑마케팅',
                'description' => '새로운 강의나 세미나를 등록하세요',
                'pageSection' => 'lectures'
            ];
            
            $this->renderView('lectures/create', $viewData, $headerData);
            
        } catch (Exception $e) {
            error_log("강의 작성 폼 오류: " . $e->getMessage());
            ResponseHelper::sendError('페이지를 불러오는 중 오류가 발생했습니다.');
        }
    }
    
    /**
     * 강의 등록 처리
     */
    public function store() {
        try {
            // 로그인 확인
            if (!isset($_SESSION['user_id'])) {
                ResponseHelper::sendError('로그인이 필요합니다.', 401);
                return;
            }
            
            // 권한 확인 (기업회원, 관리자만 가능)
            if (!$this->canCreateLecture()) {
                ResponseHelper::sendError('강의 등록은 기업회원만 가능합니다.', 403);
                return;
            }
            
            // CSRF 토큰 검증
            if (!$this->validateCsrfToken()) {
                ResponseHelper::sendError('보안 토큰이 유효하지 않습니다.', 403);
                return;
            }
            
            // 입력 데이터 검증
            $validationResult = $this->validateLectureData($_POST);
            if (!$validationResult['valid']) {
                ResponseHelper::sendError($validationResult['message'], 400);
                return;
            }
            
            // 강의 데이터 저장
            $lectureId = $this->createLecture($_POST, $_SESSION['user_id']);
            
            if ($lectureId) {
                ResponseHelper::sendSuccess([
                    'message' => '강의가 성공적으로 등록되었습니다.',
                    'redirectUrl' => '/lectures/' . $lectureId
                ]);
            } else {
                ResponseHelper::sendError('강의 등록 중 오류가 발생했습니다.', 500);
            }
            
        } catch (Exception $e) {
            error_log("강의 등록 오류: " . $e->getMessage());
            ResponseHelper::sendError('강의 등록 중 오류가 발생했습니다.', 500);
        }
    }
    
    // === Private Methods ===
    
    /**
     * 월별 강의 목록 조회
     */
    private function getLecturesByMonth($year, $month) {
        try {
            $sql = "
                SELECT 
                    l.*,
                    u.nickname as organizer_name,
                    CASE WHEN l.max_participants IS NULL THEN '무제한' 
                         ELSE CONCAT(l.registration_count, '/', l.max_participants) 
                    END as capacity_info,
                    CASE WHEN l.registration_deadline IS NULL OR l.registration_deadline > NOW() THEN 1 ELSE 0 END as can_register
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND YEAR(l.start_date) = :year
                AND MONTH(l.start_date) = :month
                ORDER BY l.start_date ASC, l.start_time ASC
            ";
            
            return $this->db->fetchAll($sql, [
                ':year' => $year,
                ':month' => $month
            ]);
        } catch (Exception $e) {
            // 데이터베이스 오류 시 임시 데이터 반환
            return $this->getDemoLectureData($year, $month);
        }
    }
    
    /**
     * 데모 강의 데이터 생성 (데이터베이스 연결 실패 시)
     */
    private function getDemoLectureData($year, $month) {
        // 현재 월에 해당하는 샘플 강의 데이터
        $currentDate = sprintf('%04d-%02d', $year, $month);
        
        return [
            [
                'id' => 1,
                'title' => '디지털 마케팅 전략 세미나',
                'description' => '2025년 최신 디지털 마케팅 트렌드와 실전 전략을 배우는 세미나입니다.',
                'instructor_name' => '김마케팅',
                'start_date' => $currentDate . '-15',
                'end_date' => $currentDate . '-15',
                'start_time' => '14:00:00',
                'end_time' => '17:00:00',
                'location_type' => 'offline',
                'venue_name' => '서울 강남구 세미나실',
                'category' => 'seminar',
                'status' => 'published',
                'organizer_name' => '김마케팅',
                'capacity_info' => '15/30',
                'can_register' => 1,
                'registration_count' => 15,
                'max_participants' => 30,
                'view_count' => 127
            ],
            [
                'id' => 2,
                'title' => '온라인 SNS 마케팅 워크샵',
                'description' => '인스타그램, 페이스북 등 SNS를 활용한 마케팅 실무 워크샵입니다.',
                'instructor_name' => '박소셜',
                'start_date' => $currentDate . '-22',
                'end_date' => $currentDate . '-22',
                'start_time' => '19:00:00',
                'end_time' => '21:00:00',
                'location_type' => 'online',
                'venue_name' => null,
                'online_link' => 'https://zoom.us/j/123456789',
                'category' => 'workshop',
                'status' => 'published',
                'organizer_name' => '박소셜',
                'capacity_info' => '무제한',
                'can_register' => 1,
                'registration_count' => 42,
                'max_participants' => null,
                'view_count' => 89
            ]
        ];
    }
    
    /**
     * 강의 ID로 상세 정보 조회
     */
    private function getLectureById($id, $incrementView = false) {
        if ($incrementView) {
            // 조회수 증가
            $this->db->execute("UPDATE lectures SET view_count = view_count + 1 WHERE id = :id", [':id' => $id]);
        }
        
        $sql = "
            SELECT 
                l.*,
                u.nickname as organizer_name,
                u.email as organizer_email,
                CASE WHEN l.max_participants IS NULL THEN '무제한' 
                     ELSE CONCAT(l.registration_count, '/', l.max_participants) 
                END as capacity_info,
                CASE WHEN l.registration_deadline IS NULL OR l.registration_deadline > NOW() THEN 1 ELSE 0 END as can_register
            FROM lectures l
            JOIN users u ON l.user_id = u.id
            WHERE l.id = :id
        ";
        
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    /**
     * 카테고리 목록 조회
     */
    private function getCategories() {
        try {
            return $this->db->fetchAll("
                SELECT * FROM lecture_categories 
                WHERE is_active = 1 
                ORDER BY sort_order ASC
            ");
        } catch (Exception $e) {
            // 데이터베이스 오류 시 기본 카테고리 반환
            return [
                ['id' => 1, 'name' => '세미나', 'color_code' => '#007bff', 'icon' => 'fas fa-microphone'],
                ['id' => 2, 'name' => '워크샵', 'color_code' => '#28a745', 'icon' => 'fas fa-tools'],
                ['id' => 3, 'name' => '컨퍼런스', 'color_code' => '#dc3545', 'icon' => 'fas fa-users'],
                ['id' => 4, 'name' => '웨비나', 'color_code' => '#6f42c1', 'icon' => 'fas fa-video'],
                ['id' => 5, 'name' => '교육과정', 'color_code' => '#fd7e14', 'icon' => 'fas fa-graduation-cap']
            ];
        }
    }
    
    /**
     * 캘린더 데이터 생성
     */
    private function generateCalendarData($year, $month, $lectures) {
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $dayOfWeek = date('w', $firstDay);
        
        $calendar = [];
        $lecturesByDate = [];
        
        // 날짜별 강의 분류
        foreach ($lectures as $lecture) {
            $date = date('j', strtotime($lecture['start_date']));
            if (!isset($lecturesByDate[$date])) {
                $lecturesByDate[$date] = [];
            }
            $lecturesByDate[$date][] = $lecture;
        }
        
        // 캘린더 구조 생성
        $currentWeek = [];
        
        // 이전 달 빈 칸
        for ($i = 0; $i < $dayOfWeek; $i++) {
            $currentWeek[] = null;
        }
        
        // 현재 달 날짜
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentWeek[] = [
                'day' => $day,
                'lectures' => $lecturesByDate[$day] ?? [],
                'isToday' => (date('Y-m-d') === sprintf('%04d-%02d-%02d', $year, $month, $day))
            ];
            
            if (count($currentWeek) === 7) {
                $calendar[] = $currentWeek;
                $currentWeek = [];
            }
        }
        
        // 마지막 주 빈 칸 채우기
        while (count($currentWeek) < 7) {
            $currentWeek[] = null;
        }
        if (count($currentWeek) > 0) {
            $calendar[] = $currentWeek;
        }
        
        return $calendar;
    }
    
    /**
     * 오늘의 강의
     */
    private function getTodayLectures() {
        try {
            $sql = "
                SELECT l.*, u.nickname as organizer_name
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND DATE(l.start_date) = CURDATE()
                ORDER BY l.start_time ASC
                LIMIT 5
            ";
            
            return $this->db->fetchAll($sql);
        } catch (Exception $e) {
            // 오늘 날짜와 일치하는 데모 데이터 반환
            $today = date('Y-m-d');
            $demoData = $this->getDemoLectureData(date('Y'), date('n'));
            
            return array_filter($demoData, function($lecture) use ($today) {
                return $lecture['start_date'] === $today;
            });
        }
    }
    
    /**
     * 다가오는 강의
     */
    private function getUpcomingLectures($limit = 5) {
        try {
            $sql = "
                SELECT l.*, u.nickname as organizer_name
                FROM lectures l
                JOIN users u ON l.user_id = u.id
                WHERE l.status = 'published'
                AND l.start_date > CURDATE()
                ORDER BY l.start_date ASC, l.start_time ASC
                LIMIT :limit
            ";
            
            return $this->db->fetchAll($sql, [':limit' => $limit]);
        } catch (Exception $e) {
            // 미래 날짜의 데모 데이터 반환
            $today = date('Y-m-d');
            $demoData = $this->getDemoLectureData(date('Y'), date('n'));
            
            $upcoming = array_filter($demoData, function($lecture) use ($today) {
                return $lecture['start_date'] > $today;
            });
            
            return array_slice($upcoming, 0, $limit);
        }
    }
    
    /**
     * 강의 신청 정보 조회
     */
    private function getUserRegistration($lectureId, $userId) {
        return $this->db->fetch("
            SELECT * FROM lecture_registrations
            WHERE lecture_id = :lecture_id AND user_id = :user_id
        ", [
            ':lecture_id' => $lectureId,
            ':user_id' => $userId
        ]);
    }
    
    /**
     * 강의 신청자 목록
     */
    private function getLectureRegistrations($lectureId, $limit = null) {
        $sql = "
            SELECT lr.*, u.nickname
            FROM lecture_registrations lr
            JOIN users u ON lr.user_id = u.id
            WHERE lr.lecture_id = :lecture_id
            AND lr.status IN ('confirmed', 'pending')
            ORDER BY lr.registration_date ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
            return $this->db->fetchAll($sql, [':lecture_id' => $lectureId, ':limit' => $limit]);
        }
        
        return $this->db->fetchAll($sql, [':lecture_id' => $lectureId]);
    }
    
    /**
     * 관련 강의 추천
     */
    private function getRelatedLectures($category, $excludeId, $limit = 3) {
        return $this->db->fetchAll("
            SELECT l.*, u.nickname as organizer_name
            FROM lectures l
            JOIN users u ON l.user_id = u.id
            WHERE l.status = 'published'
            AND l.category = :category
            AND l.id != :exclude_id
            AND l.start_date >= CURDATE()
            ORDER BY l.start_date ASC
            LIMIT :limit
        ", [
            ':category' => $category,
            ':exclude_id' => $excludeId,
            ':limit' => $limit
        ]);
    }
    
    /**
     * 강의 수정 권한 확인
     */
    private function canEditLecture($lecture) {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // 작성자 본인 또는 관리자
        return $_SESSION['user_id'] == $lecture['user_id'] || 
               in_array($_SESSION['user_role'] ?? '', ['ADMIN', 'SUPER_ADMIN']);
    }
    
    /**
     * 강의 신청 가능 여부 확인
     */
    private function canRegisterLecture($lecture) {
        // 발행된 상태이고 등록 마감일이 지나지 않았으며 정원이 남아있는 경우
        if ($lecture['status'] !== 'published') return false;
        if ($lecture['registration_deadline'] && strtotime($lecture['registration_deadline']) < time()) return false;
        if ($lecture['max_participants'] && $lecture['registration_count'] >= $lecture['max_participants']) return false;
        
        return true;
    }
    
    /**
     * 강의 생성 권한 확인
     */
    private function canCreateLecture() {
        if (!isset($_SESSION['user_role'])) {
            return false;
        }
        
        $userRole = $_SESSION['user_role'];
        
        // 기업회원(PREMIUM), 관리자, 최고관리자만 가능
        return in_array($userRole, ['PREMIUM', 'ADMIN', 'SUPER_ADMIN']);
    }
    
    /**
     * 강의 데이터 검증
     */
    private function validateLectureData($data) {
        $errors = [];
        
        // 필수 필드 검증
        if (empty($data['title'])) $errors[] = '강의 제목을 입력해주세요.';
        if (empty($data['description'])) $errors[] = '강의 설명을 입력해주세요.';
        if (empty($data['instructor_name'])) $errors[] = '강사명을 입력해주세요.';
        if (empty($data['start_date'])) $errors[] = '시작 날짜를 입력해주세요.';
        if (empty($data['end_date'])) $errors[] = '종료 날짜를 입력해주세요.';
        if (empty($data['start_time'])) $errors[] = '시작 시간을 입력해주세요.';
        if (empty($data['end_time'])) $errors[] = '종료 시간을 입력해주세요.';
        
        // 날짜 유효성 검증
        if (!empty($data['start_date']) && !empty($data['end_date'])) {
            if (strtotime($data['start_date']) > strtotime($data['end_date'])) {
                $errors[] = '종료 날짜는 시작 날짜보다 늦어야 합니다.';
            }
        }
        
        // 시간 유효성 검증
        if (!empty($data['start_time']) && !empty($data['end_time'])) {
            if (strtotime($data['start_time']) >= strtotime($data['end_time'])) {
                $errors[] = '종료 시간은 시작 시간보다 늦어야 합니다.';
            }
        }
        
        // 정원 검증
        if (!empty($data['max_participants']) && intval($data['max_participants']) < 1) {
            $errors[] = '최대 참가자 수는 1명 이상이어야 합니다.';
        }
        
        return [
            'valid' => empty($errors),
            'message' => empty($errors) ? '' : implode(' ', $errors)
        ];
    }
    
    /**
     * 강의 생성
     */
    private function createLecture($data, $userId) {
        $sql = "
            INSERT INTO lectures (
                user_id, title, description, instructor_name, instructor_info,
                start_date, end_date, start_time, end_time, timezone,
                location_type, venue_name, venue_address, online_link,
                max_participants, registration_fee, registration_deadline,
                category, difficulty_level, requirements, benefits,
                status, created_at
            ) VALUES (
                :user_id, :title, :description, :instructor_name, :instructor_info,
                :start_date, :end_date, :start_time, :end_time, :timezone,
                :location_type, :venue_name, :venue_address, :online_link,
                :max_participants, :registration_fee, :registration_deadline,
                :category, :difficulty_level, :requirements, :benefits,
                :status, NOW()
            )
        ";
        
        $params = [
            ':user_id' => $userId,
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':instructor_name' => $data['instructor_name'],
            ':instructor_info' => $data['instructor_info'] ?? null,
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':timezone' => $data['timezone'] ?? 'Asia/Seoul',
            ':location_type' => $data['location_type'] ?? 'offline',
            ':venue_name' => $data['venue_name'] ?? null,
            ':venue_address' => $data['venue_address'] ?? null,
            ':online_link' => $data['online_link'] ?? null,
            ':max_participants' => empty($data['max_participants']) ? null : intval($data['max_participants']),
            ':registration_fee' => intval($data['registration_fee'] ?? 0),
            ':registration_deadline' => empty($data['registration_deadline']) ? null : $data['registration_deadline'],
            ':category' => $data['category'] ?? 'seminar',
            ':difficulty_level' => $data['difficulty_level'] ?? 'all',
            ':requirements' => $data['requirements'] ?? null,
            ':benefits' => $data['benefits'] ?? null,
            ':status' => $data['status'] ?? 'draft'
        ];
        
        $this->db->execute($sql, $params);
        return $this->db->lastInsertId();
    }
    
    /**
     * CSRF 토큰 검증
     */
    private function validateCsrfToken() {
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
    
    /**
     * 강의 신청 처리
     */
    public function register($id) {
        try {
            // 로그인 확인
            if (!isset($_SESSION['user_id'])) {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    ResponseHelper::sendError('로그인이 필요합니다.', 401);
                    return;
                } else {
                    header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
                    exit;
                }
            }
            
            $lectureId = intval($id);
            $userId = $_SESSION['user_id'];
            
            // 강의 정보 확인
            $lecture = $this->getLectureById($lectureId);
            if (!$lecture) {
                ResponseHelper::sendError('존재하지 않는 강의입니다.', 404);
                return;
            }
            
            // 신청 가능 여부 확인
            if (!$this->canRegisterLecture($lecture)) {
                ResponseHelper::sendError('신청할 수 없는 강의입니다.', 400);
                return;
            }
            
            // 이미 신청한 사용자인지 확인
            $existingRegistration = $this->getUserRegistration($lectureId, $userId);
            if ($existingRegistration) {
                ResponseHelper::sendError('이미 신청한 강의입니다.', 400);
                return;
            }
            
            // 사용자 정보 가져오기
            $user = $this->userModel->findById($userId);
            if (!$user) {
                ResponseHelper::sendError('사용자 정보를 찾을 수 없습니다.', 404);
                return;
            }
            
            // 신청 데이터 저장
            $registrationData = [
                'lecture_id' => $lectureId,
                'user_id' => $userId,
                'participant_name' => $user['nickname'],
                'participant_email' => $user['email'],
                'participant_phone' => $user['phone'],
                'status' => 'confirmed'
            ];
            
            $sql = "
                INSERT INTO lecture_registrations (
                    lecture_id, user_id, participant_name, participant_email, 
                    participant_phone, status, registration_date
                ) VALUES (
                    :lecture_id, :user_id, :participant_name, :participant_email,
                    :participant_phone, :status, NOW()
                )
            ";
            
            $this->db->beginTransaction();
            
            try {
                // 신청 정보 저장
                $this->db->execute($sql, $registrationData);
                
                // 강의 신청자 수 업데이트
                $this->db->execute(
                    "UPDATE lectures SET registration_count = registration_count + 1 WHERE id = :id",
                    [':id' => $lectureId]
                );
                
                $this->db->commit();
                
                ResponseHelper::sendSuccess([
                    'message' => '강의 신청이 완료되었습니다.',
                    'redirectUrl' => '/lectures/' . $lectureId
                ]);
                
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("강의 신청 오류: " . $e->getMessage());
            ResponseHelper::sendError('강의 신청 중 오류가 발생했습니다.', 500);
        }
    }
    
    /**
     * iCal 파일 생성
     */
    public function generateICal($id) {
        try {
            $lectureId = intval($id);
            $lecture = $this->getLectureById($lectureId);
            
            if (!$lecture) {
                header("HTTP/1.0 404 Not Found");
                return;
            }
            
            // iCal 내용 생성
            $ical = $this->createICalContent($lecture);
            
            // 헤더 설정
            header('Content-Type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename="lecture_' . $lectureId . '.ics"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            
            echo $ical;
            
        } catch (Exception $e) {
            error_log("iCal 생성 오류: " . $e->getMessage());
            header("HTTP/1.0 500 Internal Server Error");
            echo "iCal 파일 생성 중 오류가 발생했습니다.";
        }
    }
    
    /**
     * iCal 콘텐츠 생성
     */
    private function createICalContent($lecture) {
        $startDateTime = $lecture['start_date'] . 'T' . str_replace(':', '', $lecture['start_time']) . '00';
        $endDateTime = $lecture['end_date'] . 'T' . str_replace(':', '', $lecture['end_time']) . '00';
        $now = date('Ymd\THis\Z');
        
        $location = '';
        if ($lecture['location_type'] === 'online') {
            $location = '온라인';
            if (!empty($lecture['online_link'])) {
                $location .= ' - ' . $lecture['online_link'];
            }
        } elseif (!empty($lecture['venue_name'])) {
            $location = $lecture['venue_name'];
            if (!empty($lecture['venue_address'])) {
                $location .= ', ' . $lecture['venue_address'];
            }
        }
        
        $description = $lecture['description'];
        if (!empty($lecture['instructor_name'])) {
            $description .= "\\n\\n강사: " . $lecture['instructor_name'];
        }
        if (!empty($lecture['registration_fee']) && $lecture['registration_fee'] > 0) {
            $description .= "\\n참가비: " . number_format($lecture['registration_fee']) . "원";
        }
        
        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//TopMarketing//Lecture Calendar//KO\r\n";
        $ical .= "CALSCALE:GREGORIAN\r\n";
        $ical .= "METHOD:PUBLISH\r\n";
        $ical .= "BEGIN:VEVENT\r\n";
        $ical .= "UID:lecture-" . $lecture['id'] . "-" . time() . "@topmarketing.kr\r\n";
        $ical .= "DTSTAMP:" . $now . "\r\n";
        $ical .= "DTSTART:" . $startDateTime . "\r\n";
        $ical .= "DTEND:" . $endDateTime . "\r\n";
        $ical .= "SUMMARY:" . $this->escapeICalText($lecture['title']) . "\r\n";
        $ical .= "DESCRIPTION:" . $this->escapeICalText($description) . "\r\n";
        if (!empty($location)) {
            $ical .= "LOCATION:" . $this->escapeICalText($location) . "\r\n";
        }
        $ical .= "STATUS:CONFIRMED\r\n";
        $ical .= "SEQUENCE:0\r\n";
        $ical .= "END:VEVENT\r\n";
        $ical .= "END:VCALENDAR\r\n";
        
        return $ical;
    }
    
    /**
     * iCal 텍스트 이스케이프
     */
    private function escapeICalText($text) {
        $text = str_replace(['\\', ';', ',', "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', ''], $text);
        return $text;
    }
    
    /**
     * iCal URL 생성
     */
    private function generateICalUrl($lectureId) {
        return '/lectures/' . $lectureId . '/ical';
    }
    
    /**
     * 강의 테이블 존재 확인
     */
    private function checkLectureTablesExist() {
        try {
            $tables = $this->db->fetchAll("SHOW TABLES LIKE 'lectures'");
            return !empty($tables);
        } catch (Exception $e) {
            // 데이터베이스 연결 실패 시 데모 모드로 동작
            // 실제로는 테이블이 없지만 데모 데이터를 사용할 수 있도록 true 반환
            return true;
        }
    }
    
    /**
     * 설정 페이지 표시
     */
    private function showSetupPage() {
        $headerData = [
            'title' => '강의 시스템 설정 - 탑마케팅',
            'description' => '강의 시스템을 초기화합니다',
            'pageSection' => 'lectures'
        ];
        
        $this->renderView('lectures/setup', [], $headerData);
    }
    
    /**
     * 오류 페이지 표시
     */
    private function showErrorPage($message, $details = '') {
        $headerData = [
            'title' => '오류 발생 - 탑마케팅',
            'description' => '강의 시스템에서 오류가 발생했습니다',
            'pageSection' => 'lectures'
        ];
        
        $viewData = [
            'errorMessage' => $message,
            'errorDetails' => $details
        ];
        
        $this->renderView('lectures/error', $viewData, $headerData);
    }
    
    /**
     * 뷰 렌더링
     */
    private function renderView($view, $data = [], $headerData = []) {
        try {
            // 뷰 파일 경로를 먼저 저장 (extract 전에)
            $viewPath = SRC_PATH . '/views/' . $view . '.php';
            
            // 데이터 추출 (PHP extract 사용)
            extract($data);
            extract($headerData);
            
            // 헤더 렌더링
            require_once SRC_PATH . '/views/templates/header.php';
            
            // 메인 뷰 렌더링
            if (file_exists($viewPath)) {
                require_once $viewPath;
            } else {
                echo "<div class='error-message'>뷰 파일을 찾을 수 없습니다: {$view}</div>";
            }
            
            // 푸터 렌더링
            require_once SRC_PATH . '/views/templates/footer.php';
            
        } catch (Exception $e) {
            error_log("뷰 렌더링 오류: " . $e->getMessage());
            echo "<div class='error-message'>페이지 렌더링 중 오류가 발생했습니다.</div>";
        }
    }
}
?>