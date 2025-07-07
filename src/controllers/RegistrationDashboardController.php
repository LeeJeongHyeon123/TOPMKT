<?php
/**
 * 기업 신청 관리 대시보드 컨트롤러
 */

require_once SRC_PATH . '/controllers/BaseController.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/services/EmailService.php';

class RegistrationDashboardController extends BaseController
{
    /**
     * 대시보드 메인 페이지
     */
    public function index()
    {
        // 로그인 및 기업 권한 확인
        if (!AuthMiddleware::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }
        
        $userRole = AuthMiddleware::getUserRole();
        // 관리자이거나 기업 회원이거나 일반 사용자(강의 생성자)라면 접근 허용
        if ($userRole !== 'ROLE_CORP' && $userRole !== 'ROLE_ADMIN' && $userRole !== 'ROLE_USER') {
            header('HTTP/1.1 403 Forbidden');
            include SRC_PATH . '/views/errors/403.php';
            exit;
        }
        
        $userId = AuthMiddleware::getCurrentUserId();
        
        try {
            // 날짜 필터 파라미터 처리
            $startDate = $_GET['start_date'] ?? null;
            $endDate = $_GET['end_date'] ?? null;
            $contentType = $_GET['type'] ?? 'lecture'; // 새로운 파라미터: lecture | event
            
            // 기본값: 최근 1개월
            if (!$startDate || !$endDate) {
                $startDate = date('Y-m-d', strtotime('-1 month'));
                $endDate = date('Y-m-d');
            }
            
            // 내 강의/행사 목록 조회 (날짜 필터 및 컨텐츠 타입 적용)
            $lecturesQuery = "
                SELECT 
                    l.id, l.title, l.start_date, l.start_time, l.end_date, l.end_time,
                    l.max_participants, l.current_participants, l.auto_approval,
                    l.registration_end_date, l.content_type, l.location_type,
                    l.sponsor_info,
                    COUNT(DISTINCT lr.id) as total_applications,
                    COUNT(DISTINCT CASE WHEN lr.status = 'pending' THEN lr.id END) as pending_count,
                    COUNT(DISTINCT CASE WHEN lr.status = 'approved' THEN lr.id END) as approved_count,
                    COUNT(DISTINCT CASE WHEN lr.status = 'rejected' THEN lr.id END) as rejected_count,
                    COUNT(DISTINCT CASE WHEN lr.status = 'waiting' THEN lr.id END) as waiting_count
                FROM lectures l
                LEFT JOIN lecture_registrations lr ON l.id = lr.lecture_id
                WHERE l.user_id = ? AND l.status = 'published' 
                AND l.content_type = ?
                AND l.start_date >= ? AND l.start_date <= ?
                GROUP BY l.id, l.title, l.start_date, l.start_time, l.end_date, l.end_time,
                         l.max_participants, l.current_participants, l.auto_approval, l.registration_end_date,
                         l.content_type, l.location_type, l.sponsor_info
                ORDER BY l.start_date DESC, l.created_at DESC
            ";
            
            $stmt = $this->db->prepare($lecturesQuery);
            $stmt->bind_param("isss", $userId, $contentType, $startDate, $endDate);
            $stmt->execute();
            $lectures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // 각 강의/행사의 신청 상태 계산
            foreach ($lectures as &$lecture) {
                $lecture['registration_status'] = $this->calculateRegistrationStatus($lecture);
            }
            
            // 대시보드 통계 계산 (컨텐츠 타입별)
            $stats = $this->getDashboardStats($userId, $contentType);
            
            // 최근 신청 목록 (최근 20개, 컨텐츠 타입별)
            $recentRegistrationsQuery = "
                SELECT 
                    r.id, r.participant_name, r.participant_email, r.status,
                    r.created_at, r.is_waiting_list, r.waiting_order,
                    l.title as lecture_title, l.id as lecture_id, l.content_type
                FROM lecture_registrations r
                JOIN lectures l ON r.lecture_id = l.id
                WHERE l.user_id = ? AND l.content_type = ?
                ORDER BY r.created_at DESC
                LIMIT 20
            ";
            
            $stmt = $this->db->prepare($recentRegistrationsQuery);
            $stmt->bind_param("is", $userId, $contentType);
            $stmt->execute();
            $recentRegistrations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // 뷰 렌더링
            $pageTitle = '신청 관리 대시보드';
            $pageDescription = ($contentType === 'event' ? '행사' : '강의') . ' 신청 현황을 한눈에 확인하고 관리하세요.';
            
            include SRC_PATH . '/views/templates/header.php';
            include SRC_PATH . '/views/registrations/dashboard.php';
            include SRC_PATH . '/views/templates/footer.php';
            
        } catch (Exception $e) {
            error_log("대시보드 오류: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            include SRC_PATH . '/views/errors/500.php';
        }
    }
    
    /**
     * 특정 강의의 신청자 관리 페이지
     */
    public function lectureRegistrations($lectureId)
    {
        // 로그인 및 권한 확인
        if (!AuthMiddleware::isLoggedIn()) {
            header('Location: /auth/login');
            exit;
        }
        
        $userId = AuthMiddleware::getCurrentUserId();
        $userRole = AuthMiddleware::getUserRole();
        
        // 강의 정보 및 권한 확인
        $lectureQuery = "
            SELECT 
                l.id, l.title, l.description, l.start_date, l.start_time, l.end_date, l.end_time,
                l.max_participants, l.current_participants, l.auto_approval,
                l.registration_start_date, l.registration_end_date, l.allow_waiting_list,
                l.user_id as organizer_id, u.nickname as organizer_name
            FROM lectures l
            JOIN users u ON l.user_id = u.id
            WHERE l.id = ? AND l.status = 'published'
        ";
        
        $stmt = $this->db->prepare($lectureQuery);
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $lecture = $stmt->get_result()->fetch_assoc();
        
        if (!$lecture) {
            header('HTTP/1.1 404 Not Found');
            include SRC_PATH . '/views/errors/404.php';
            exit;
        }
        
        // 권한 확인 (본인 강의이거나 관리자)
        if ($userRole !== 'ROLE_ADMIN' && $lecture['organizer_id'] != $userId) {
            header('HTTP/1.1 403 Forbidden');
            include SRC_PATH . '/views/errors/403.php';
            exit;
        }
        
        try {
            // 페이징 처리
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            
            // 필터링 옵션
            $statusFilter = $_GET['status'] ?? '';
            $searchQuery = trim($_GET['search'] ?? '');
            
            // 신청자 목록 조회
            $registrations = $this->getRegistrations($lectureId, $statusFilter, $searchQuery, $offset, $perPage);
            $totalCount = $this->getRegistrationsCount($lectureId, $statusFilter, $searchQuery);
            $totalPages = ceil($totalCount / $perPage);
            
            // 통계 정보
            $lectureStats = $this->getLectureStats($lectureId);
            
            // 뷰 렌더링
            $pageTitle = $lecture['title'] . ' - 신청자 관리';
            $pageDescription = '강의 신청자 목록을 확인하고 승인/거절을 관리하세요.';
            
            include SRC_PATH . '/views/templates/header.php';
            include SRC_PATH . '/views/registrations/lecture-detail.php';
            include SRC_PATH . '/views/templates/footer.php';
            
        } catch (Exception $e) {
            error_log("강의 신청자 관리 오류: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            include SRC_PATH . '/views/errors/500.php';
        }
    }
    
    /**
     * 신청 승인/거절 처리 API
     */
    public function updateRegistrationStatus($registrationId)
    {
        header('Content-Type: application/json');
        
        try {
            // HTTP 메소드 확인
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return ResponseHelper::json('error', 'POST 메소드만 허용됩니다.', null, 405);
            }
            
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json('error', '로그인이 필요합니다.', null, 401);
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            $userRole = AuthMiddleware::getUserRole();
            
            // JSON 데이터 파싱
            $input = json_decode(file_get_contents('php://input'), true);
            
            // CSRF 토큰 검증
            if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
                return ResponseHelper::json('error', 'CSRF 토큰이 유효하지 않습니다.', null, 403);
            }
            
            $newStatus = $input['status'] ?? '';
            $adminNotes = trim($input['admin_notes'] ?? '');
            
            // 상태 검증
            $validStatuses = ['approved', 'rejected'];
            if (!in_array($newStatus, $validStatuses)) {
                return ResponseHelper::json('error', '올바른 상태를 선택해주세요.', null, 400);
            }
            
            // 신청 정보 및 권한 확인
            $registrationQuery = "
                SELECT r.*, l.user_id as lecture_organizer, l.title as lecture_title,
                       l.max_participants, l.current_participants
                FROM lecture_registrations r
                JOIN lectures l ON r.lecture_id = l.id
                WHERE r.id = ?
            ";
            
            $stmt = $this->db->prepare($registrationQuery);
            $stmt->bind_param("i", $registrationId);
            $stmt->execute();
            $registration = $stmt->get_result()->fetch_assoc();
            
            if (!$registration) {
                return ResponseHelper::json('error', '신청을 찾을 수 없습니다.', null, 404);
            }
            
            // 권한 확인
            if ($userRole !== 'ROLE_ADMIN' && $registration['lecture_organizer'] != $userId) {
                return ResponseHelper::json('error', '권한이 없습니다.', null, 403);
            }
            
            // 이미 처리된 신청인지 확인
            if (in_array($registration['status'], ['approved', 'rejected'])) {
                return ResponseHelper::json('error', '이미 처리된 신청입니다.', null, 400);
            }
            
            // 승인 시 정원 확인
            if ($newStatus === 'approved') {
                $maxParticipants = $registration['max_participants'];
                $currentParticipants = $registration['current_participants'];
                
                if ($maxParticipants && $currentParticipants >= $maxParticipants) {
                    return ResponseHelper::json('error', '정원이 초과되었습니다.', null, 400);
                }
            }
            
            // 상태 업데이트
            $updateQuery = "
                UPDATE lecture_registrations 
                SET status = ?, admin_notes = ?, processed_by = ?, processed_at = NOW()
                WHERE id = ?
            ";
            
            $stmt = $this->db->prepare($updateQuery);
            $stmt->bind_param("ssii", $newStatus, $adminNotes, $userId, $registrationId);
            
            if ($stmt->execute()) {
                // SMS 알림 발송 (이메일 대신)
                try {
                    require_once SRC_PATH . '/helpers/SmsHelper.php';
                    
                    // SMS 발송
                    if ($newStatus === 'approved') {
                        $smsResult = sendLectureApprovalSms($registration['participant_phone']);
                        $logMessage = "강의 신청 승인 SMS 발송";
                    } else {
                        $smsResult = sendLectureRejectionSms($registration['participant_phone']);
                        $logMessage = "강의 신청 거절 SMS 발송";
                    }
                    
                    if ($smsResult['success']) {
                        error_log($logMessage . " 성공: " . $registration['participant_phone']);
                    } else {
                        error_log($logMessage . " 실패: " . $smsResult['message']);
                    }
                    
                } catch (Exception $e) {
                    error_log("상태 변경 SMS 발송 실패: " . $e->getMessage());
                    // SMS 실패는 전체 프로세스를 중단하지 않음
                }
                
                $message = $newStatus === 'approved' ? '신청이 승인되었습니다.' : '신청이 거절되었습니다.';
                
                return ResponseHelper::json('success', $message, [
                    'registration_id' => $registrationId,
                    'new_status' => $newStatus
                ]);
            } else {
                return ResponseHelper::json('error', '상태 업데이트에 실패했습니다.', null, 500);
            }
            
        } catch (Exception $e) {
            error_log("신청 상태 변경 오류: " . $e->getMessage());
            return ResponseHelper::json('error', '처리 중 오류가 발생했습니다.', null, 500);
        }
    }
    
    /**
     * 대시보드 통계 정보 (컨텐츠 타입별)
     */
    private function getDashboardStats($userId, $contentType = 'lecture')
    {
        // registration_statistics 테이블 없이 직접 집계
        $statsQuery = "
            SELECT 
                COUNT(DISTINCT l.id) as total_lectures,
                COUNT(DISTINCT lr.id) as total_applications,
                COUNT(DISTINCT CASE WHEN lr.status = 'pending' THEN lr.id END) as pending_applications,
                COUNT(DISTINCT CASE WHEN lr.status = 'approved' THEN lr.id END) as approved_applications,
                COUNT(DISTINCT CASE WHEN lr.status = 'rejected' THEN lr.id END) as rejected_applications
            FROM lectures l
            LEFT JOIN lecture_registrations lr ON l.id = lr.lecture_id
            WHERE l.user_id = ? AND l.status = 'published' AND l.content_type = ?
        ";
        
        $stmt = $this->db->prepare($statsQuery);
        $stmt->bind_param("is", $userId, $contentType);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * 특정 강의의 신청자 목록 조회
     */
    private function getRegistrations($lectureId, $statusFilter, $searchQuery, $offset, $perPage)
    {
        $whereConditions = ["r.lecture_id = ?"];
        $params = [$lectureId];
        $types = "i";
        
        // 상태 필터
        if (!empty($statusFilter)) {
            $whereConditions[] = "r.status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }
        
        // 검색 쿼리
        if (!empty($searchQuery)) {
            $whereConditions[] = "(r.participant_name LIKE ? OR r.participant_email LIKE ? OR r.company_name LIKE ?)";
            $searchTerm = "%$searchQuery%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }
        
        $whereClause = implode(" AND ", $whereConditions);
        
        $query = "
            SELECT 
                r.id, r.participant_name, r.participant_email, r.participant_phone,
                r.company_name, r.position, r.motivation, r.special_requests,
                r.status, r.is_waiting_list, r.waiting_order, r.created_at,
                r.processed_at, r.admin_notes,
                processor.nickname as processed_by_name
            FROM lecture_registrations r
            LEFT JOIN users processor ON r.processed_by = processor.id
            WHERE $whereClause
            ORDER BY 
                CASE r.status 
                    WHEN 'pending' THEN 1 
                    WHEN 'waiting' THEN 2 
                    WHEN 'approved' THEN 3 
                    WHEN 'rejected' THEN 4 
                    ELSE 5 
                END,
                r.created_at DESC
            LIMIT ?, ?
        ";
        
        $params[] = $offset;
        $params[] = $perPage;
        $types .= "ii";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * 신청자 총 개수 조회
     */
    private function getRegistrationsCount($lectureId, $statusFilter, $searchQuery)
    {
        $whereConditions = ["r.lecture_id = ?"];
        $params = [$lectureId];
        $types = "i";
        
        if (!empty($statusFilter)) {
            $whereConditions[] = "r.status = ?";
            $params[] = $statusFilter;
            $types .= "s";
        }
        
        if (!empty($searchQuery)) {
            $whereConditions[] = "(r.participant_name LIKE ? OR r.participant_email LIKE ? OR r.company_name LIKE ?)";
            $searchTerm = "%$searchQuery%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "sss";
        }
        
        $whereClause = implode(" AND ", $whereConditions);
        
        $query = "SELECT COUNT(*) as count FROM lecture_registrations r WHERE $whereClause";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc()['count'];
    }
    
    /**
     * 특정 강의의 통계 정보
     */
    private function getLectureStats($lectureId)
    {
        $query = "SELECT * FROM registration_statistics WHERE lecture_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    /**
     * 강의/행사의 신청 상태 계산
     */
    private function calculateRegistrationStatus($lecture)
    {
        $now = new DateTime();
        $startDate = new DateTime($lecture['start_date'] . ' ' . $lecture['start_time']);
        $registrationEndDate = $lecture['registration_end_date'] ? new DateTime($lecture['registration_end_date']) : null;
        
        // 행사/강의가 이미 시작됨
        if ($startDate <= $now) {
            return [
                'status' => 'completed',
                'label' => '완료됨',
                'color' => 'gray',
                'icon' => '✅'
            ];
        }
        
        // 신청 마감일이 설정되어 있고 지났음
        if ($registrationEndDate && $registrationEndDate <= $now) {
            return [
                'status' => 'closed',
                'label' => '신청 마감',
                'color' => 'red',
                'icon' => '🔒'
            ];
        }
        
        // 최대 참가자 수가 설정되어 있고 가득참
        if ($lecture['max_participants'] && $lecture['current_participants'] >= $lecture['max_participants']) {
            return [
                'status' => 'full',
                'label' => '정원 마감',
                'color' => 'orange',
                'icon' => '👥'
            ];
        }
        
        // 신청 가능
        return [
            'status' => 'open',
            'label' => '신청 중',
            'color' => 'green',
            'icon' => '📝'
        ];
    }
    
    /**
     * CSRF 토큰 검증
     */
    private function validateCsrfToken($token)
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>