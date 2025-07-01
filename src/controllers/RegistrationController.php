<?php
/**
 * 강의/행사 신청 관리 컨트롤러
 */

require_once SRC_PATH . '/controllers/BaseController.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/ResponseHelper.php';
require_once SRC_PATH . '/helpers/ValidationHelper.php';
require_once SRC_PATH . '/services/EmailService.php';

class RegistrationController extends BaseController
{
    /**
     * 신청 상태 확인 API
     */
    public function getRegistrationStatus($lectureId)
    {
        header('Content-Type: application/json');
        
        try {
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json('error', '로그인이 필요합니다.', null, 401);
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // 강의 정보 조회
            $lectureQuery = "
                SELECT 
                    id, title, start_date, start_time, end_date, end_time,
                    max_participants, current_participants, auto_approval,
                    registration_start_date, registration_end_date, allow_waiting_list,
                    status as lecture_status
                FROM lectures 
                WHERE id = ? AND status = 'published'
            ";
            
            $stmt = $this->db->prepare($lectureQuery);
            $stmt->bind_param("i", $lectureId);
            $stmt->execute();
            $lecture = $stmt->get_result()->fetch_assoc();
            
            if (!$lecture) {
                return ResponseHelper::json('error', '강의를 찾을 수 없습니다.', null, 404);
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
            $stmt->bind_param("ii", $lectureId, $userId);
            $stmt->execute();
            $registration = $stmt->get_result()->fetch_assoc();
            
            // 응답 데이터 구성
            $responseData = [
                'lecture_info' => $lecture,
                'registration' => $registration,
                'user_id' => $userId
            ];
            
            return ResponseHelper::json('success', '신청 상태 조회 완료', $responseData);
            
        } catch (Exception $e) {
            error_log("신청 상태 조회 오류: " . $e->getMessage());
            return ResponseHelper::json('error', '신청 상태 조회 중 오류가 발생했습니다.', null, 500);
        }
    }
    
    /**
     * 신청 등록 API
     */
    public function createRegistration($lectureId)
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
            
            // JSON 데이터 파싱
            $input = json_decode(file_get_contents('php://input'), true);
            
            // CSRF 토큰 검증
            if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
                return ResponseHelper::json('error', 'CSRF 토큰이 유효하지 않습니다.', null, 403);
            }
            
            // 강의 정보 조회
            $lectureQuery = "
                SELECT 
                    id, title, start_date, start_time, max_participants, 
                    current_participants, auto_approval, registration_start_date, 
                    registration_end_date, allow_waiting_list, status, user_id as organizer_id
                FROM lectures 
                WHERE id = ? AND status = 'published'
            ";
            
            $stmt = $this->db->prepare($lectureQuery);
            $stmt->bind_param("i", $lectureId);
            $stmt->execute();
            $lecture = $stmt->get_result()->fetch_assoc();
            
            if (!$lecture) {
                return ResponseHelper::json('error', '강의를 찾을 수 없습니다.', null, 404);
            }
            
            // 본인 강의 신청 방지
            if ($lecture['organizer_id'] == $userId) {
                return ResponseHelper::json('error', '본인이 등록한 강의에는 신청할 수 없습니다.', null, 400);
            }
            
            // 기존 신청 확인
            $existingQuery = "SELECT id, status FROM lecture_registrations WHERE lecture_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($existingQuery);
            $stmt->bind_param("ii", $lectureId, $userId);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            
            if ($existing && in_array($existing['status'], ['pending', 'approved', 'waiting'])) {
                return ResponseHelper::json('error', '이미 신청하셨습니다.', null, 400);
            }
            
            // 신청 기간 확인
            $now = new DateTime();
            
            if ($lecture['registration_start_date']) {
                $startDate = new DateTime($lecture['registration_start_date']);
                if ($now < $startDate) {
                    return ResponseHelper::json('error', '아직 신청 기간이 아닙니다.', null, 400);
                }
            }
            
            if ($lecture['registration_end_date']) {
                $endDate = new DateTime($lecture['registration_end_date']);
                if ($now > $endDate) {
                    return ResponseHelper::json('error', '신청 기간이 마감되었습니다.', null, 400);
                }
            }
            
            // 강의 시작 시간 확인
            $lectureStart = new DateTime($lecture['start_date'] . ' ' . $lecture['start_time']);
            if ($now >= $lectureStart) {
                return ResponseHelper::json('error', '강의가 이미 시작되었습니다.', null, 400);
            }
            
            // 사용자 정보 조회
            $userQuery = "SELECT nickname, phone, email FROM users WHERE id = ?";
            $stmt = $this->db->prepare($userQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            
            if (!$user) {
                return ResponseHelper::json('error', '사용자 정보를 찾을 수 없습니다.', null, 404);
            }
            
            // 정원 확인 및 대기자 처리
            $isWaitingList = false;
            $waitingOrder = null;
            $status = 'pending';
            
            if ($lecture['max_participants'] && $lecture['current_participants'] >= $lecture['max_participants']) {
                if (!$lecture['allow_waiting_list']) {
                    return ResponseHelper::json('error', '정원이 마감되었습니다.', null, 400);
                }
                
                // 대기자로 등록
                $isWaitingList = true;
                $status = 'waiting';
                
                // 대기 순번 계산
                $waitingQuery = "SELECT MAX(waiting_order) as max_order FROM lecture_registrations WHERE lecture_id = ? AND is_waiting_list = 1";
                $stmt = $this->db->prepare($waitingQuery);
                $stmt->bind_param("i", $lectureId);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $waitingOrder = ($result['max_order'] ?? 0) + 1;
            }
            
            // 자동 승인 확인
            if (!$isWaitingList && $lecture['auto_approval']) {
                $status = 'approved';
            }
            
            // 입력 데이터 검증
            $validationErrors = $this->validateRegistrationData($input, $user);
            if (!empty($validationErrors)) {
                return ResponseHelper::json('error', '입력 데이터에 오류가 있습니다.', ['errors' => $validationErrors], 400);
            }
            
            // 신청 데이터 구성
            $registrationData = [
                'lecture_id' => $lectureId,
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
                'waiting_order' => $waitingOrder
            ];
            
            // 자동 승인인 경우 처리자 정보 설정
            if ($status === 'approved') {
                $registrationData['processed_by'] = $userId;
                $registrationData['processed_at'] = date('Y-m-d H:i:s');
            }
            
            // 트랜잭션 시작
            $this->db->begin_transaction();
            
            try {
                // 신청 등록
                $insertQuery = "
                    INSERT INTO lecture_registrations 
                    (lecture_id, user_id, participant_name, participant_email, participant_phone,
                     company_name, position, motivation, special_requests, how_did_you_know,
                     status, is_waiting_list, waiting_order, processed_by, processed_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                    throw new Exception('신청 등록에 실패했습니다.');
                }
                
                $registrationId = $this->db->insert_id;
                
                // 커밋
                $this->db->commit();
                
                // 신청 확인 이메일 발송
                try {
                    $emailService = new EmailService();
                    $registrationData['id'] = $registrationId;
                    $registrationData['created_at'] = date('Y-m-d H:i:s');
                    $emailService->sendApplicationConfirmation($registrationData, $lecture);
                } catch (Exception $e) {
                    error_log("신청 확인 이메일 발송 실패: " . $e->getMessage());
                    // 이메일 실패는 전체 프로세스를 중단하지 않음
                }
                
                $message = $isWaitingList ? 
                    "대기자로 신청이 완료되었습니다. (대기순번: {$waitingOrder}번)" :
                    ($status === 'approved' ? '신청이 승인되었습니다.' : '신청이 완료되었습니다. 승인을 기다려주세요.');
                
                return ResponseHelper::json('success', $message, [
                    'registration_id' => $registrationId,
                    'status' => $status,
                    'is_waiting_list' => $isWaitingList,
                    'waiting_order' => $waitingOrder
                ]);
                
            } catch (Exception $e) {
                $this->db->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            error_log("신청 등록 오류: " . $e->getMessage());
            return ResponseHelper::json('error', '신청 처리 중 오류가 발생했습니다.', null, 500);
        }
    }
    
    /**
     * 신청 취소 API
     */
    public function cancelRegistration($lectureId)
    {
        header('Content-Type: application/json');
        
        try {
            // HTTP 메소드 확인
            if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
                return ResponseHelper::json('error', 'DELETE 메소드만 허용됩니다.', null, 405);
            }
            
            // 로그인 확인
            if (!AuthMiddleware::isLoggedIn()) {
                return ResponseHelper::json('error', '로그인이 필요합니다.', null, 401);
            }
            
            $userId = AuthMiddleware::getCurrentUserId();
            
            // JSON 데이터 파싱
            $input = json_decode(file_get_contents('php://input'), true);
            
            // CSRF 토큰 검증
            if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
                return ResponseHelper::json('error', 'CSRF 토큰이 유효하지 않습니다.', null, 403);
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
            $stmt->bind_param("ii", $lectureId, $userId);
            $stmt->execute();
            $registration = $stmt->get_result()->fetch_assoc();
            
            if (!$registration) {
                return ResponseHelper::json('error', '취소할 신청을 찾을 수 없습니다.', null, 404);
            }
            
            // 강의 시작 시간 확인
            $now = new DateTime();
            $lectureStart = new DateTime($registration['start_date'] . ' ' . $registration['start_time']);
            
            if ($now >= $lectureStart) {
                return ResponseHelper::json('error', '강의가 이미 시작되어 취소할 수 없습니다.', null, 400);
            }
            
            // 신청 취소 처리
            $updateQuery = "
                UPDATE lecture_registrations 
                SET status = 'cancelled', processed_at = NOW() 
                WHERE id = ?
            ";
            
            $stmt = $this->db->prepare($updateQuery);
            $stmt->bind_param("i", $registration['id']);
            
            if ($stmt->execute()) {
                return ResponseHelper::json('success', '신청이 취소되었습니다.', [
                    'registration_id' => $registration['id']
                ]);
            } else {
                return ResponseHelper::json('error', '신청 취소에 실패했습니다.', null, 500);
            }
            
        } catch (Exception $e) {
            error_log("신청 취소 오류: " . $e->getMessage());
            return ResponseHelper::json('error', '신청 취소 중 오류가 발생했습니다.', null, 500);
        }
    }
    
    /**
     * 신청 데이터 검증
     */
    private function validateRegistrationData($input, $user)
    {
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
    private function isValidPhone($phone)
    {
        // 공백 제거
        $phone = preg_replace('/\s/', '', $phone);
        
        // 한국 휴대폰 번호 형식 검증
        return preg_match('/^(010|011|016|017|018|019)[-]?\d{3,4}[-]?\d{4}$/', $phone);
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