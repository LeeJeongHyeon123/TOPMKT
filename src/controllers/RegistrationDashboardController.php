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
        if ($userRole !== 'ROLE_CORP' && $userRole !== 'ROLE_ADMIN') {
            header('HTTP/1.1 403 Forbidden');
            include SRC_PATH . '/views/errors/403.php';
            exit;
        }
        
        $userId = AuthMiddleware::getCurrentUserId();
        
        try {
            // 내 강의 목록 조회 (최근 10개)
            $lecturesQuery = "
                SELECT 
                    l.id, l.title, l.start_date, l.start_time, l.end_date, l.end_time,
                    l.max_participants, l.current_participants, l.auto_approval,
                    l.registration_end_date,
                    stats.total_applications, stats.pending_count, stats.approved_count,
                    stats.rejected_count, stats.waiting_count
                FROM lectures l
                LEFT JOIN registration_statistics stats ON l.id = stats.lecture_id
                WHERE l.user_id = ? AND l.status = 'published'
                ORDER BY l.start_date DESC, l.created_at DESC
                LIMIT 10
            ";
            
            $stmt = $this->db->prepare($lecturesQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $lectures = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // 대시보드 통계 계산
            $stats = $this->getDashboardStats($userId);
            
            // 최근 신청 목록 (최근 20개)
            $recentRegistrationsQuery = "
                SELECT 
                    r.id, r.participant_name, r.participant_email, r.status,
                    r.created_at, r.is_waiting_list, r.waiting_order,
                    l.title as lecture_title, l.id as lecture_id
                FROM lecture_registrations r
                JOIN lectures l ON r.lecture_id = l.id
                WHERE l.user_id = ?
                ORDER BY r.created_at DESC
                LIMIT 20
            ";
            
            $stmt = $this->db->prepare($recentRegistrationsQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $recentRegistrations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            // 뷰 렌더링
            $pageTitle = '신청 관리 대시보드';
            $pageDescription = '강의 신청 현황을 한눈에 확인하고 관리하세요.';
            
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
                // 이메일 알림 발송
                try {
                    $emailService = new EmailService();
                    
                    // 강의 정보 조회
                    $lectureQuery = "SELECT id, title, start_date, start_time, end_date, end_time, location FROM lectures WHERE id = ?";
                    $lectureStmt = $this->db->prepare($lectureQuery);
                    $lectureStmt->bind_param("i", $registration['lecture_id']);
                    $lectureStmt->execute();
                    $lectureInfo = $lectureStmt->get_result()->fetch_assoc();
                    
                    // 업데이트된 신청 정보 구성
                    $updatedRegistration = $registration;
                    $updatedRegistration['status'] = $newStatus;
                    $updatedRegistration['admin_notes'] = $adminNotes;
                    
                    if ($newStatus === 'approved') {
                        $emailService->sendApprovalNotification($updatedRegistration, $lectureInfo);
                    } else {
                        $emailService->sendRejectionNotification($updatedRegistration, $lectureInfo);
                    }
                } catch (Exception $e) {
                    error_log("상태 변경 이메일 발송 실패: " . $e->getMessage());
                    // 이메일 실패는 전체 프로세스를 중단하지 않음
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
     * 대시보드 통계 정보
     */
    private function getDashboardStats($userId)
    {
        $statsQuery = "
            SELECT 
                COUNT(DISTINCT l.id) as total_lectures,
                COALESCE(SUM(stats.total_applications), 0) as total_applications,
                COALESCE(SUM(stats.pending_count), 0) as pending_applications,
                COALESCE(SUM(stats.approved_count), 0) as approved_applications,
                COALESCE(SUM(stats.rejected_count), 0) as rejected_applications
            FROM lectures l
            LEFT JOIN registration_statistics stats ON l.id = stats.lecture_id
            WHERE l.user_id = ? AND l.status = 'published'
        ";
        
        $stmt = $this->db->prepare($statsQuery);
        $stmt->bind_param("i", $userId);
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