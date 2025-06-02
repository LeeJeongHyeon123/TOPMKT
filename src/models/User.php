<?php
/**
 * 회원 모델 클래스
 */

require_once SRC_PATH . '/config/database.php';

class User {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * 회원가입
     */
    public function create($userData) {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO users (
                phone, nickname, email, password,
                phone_verified, phone_verified_at,
                terms_agreed, privacy_agreed, marketing_agreed,
                status, created_at
            ) VALUES (
                :phone, :nickname, :email, :password,
                :phone_verified, :phone_verified_at,
                :terms_agreed, :privacy_agreed, :marketing_agreed,
                'ACTIVE', NOW()
            )";
            
            $params = [
                ':phone' => $userData['phone'],
                ':nickname' => $userData['nickname'],
                ':email' => $userData['email'],
                ':password' => password_hash($userData['password'], PASSWORD_DEFAULT),
                ':phone_verified' => true,
                ':phone_verified_at' => date('Y-m-d H:i:s'),
                ':terms_agreed' => $userData['terms_agreed'],
                ':privacy_agreed' => $userData['terms_agreed'], // 이용약관과 동일
                ':marketing_agreed' => $userData['marketing_agreed']
            ];
            
            $this->db->execute($sql, $params);
            $userId = $this->db->lastInsertId();
            
            // 가입 로그 기록
            $this->logUserActivity($userId, 'SIGNUP', '회원가입 완료');
            
            $this->db->commit();
            return $userId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log('User creation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * 휴대폰 번호로 사용자 조회
     */
    public function findByPhone($phone) {
        $sql = "SELECT * FROM users WHERE phone = :phone AND status != 'DELETED'";
        return $this->db->fetch($sql, [':phone' => $phone]);
    }
    
    /**
     * 이메일로 사용자 조회
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND status != 'DELETED'";
        return $this->db->fetch($sql, [':email' => $email]);
    }
    
    /**
     * 닉네임으로 사용자 조회
     */
    public function findByNickname($nickname) {
        $sql = "SELECT * FROM users WHERE nickname = :nickname AND status != 'DELETED'";
        return $this->db->fetch($sql, [':nickname' => $nickname]);
    }
    
    /**
     * ID로 사용자 조회
     */
    public function findById($id) {
        $sql = "SELECT * FROM users WHERE id = :id AND status != 'DELETED'";
        return $this->db->fetch($sql, [':id' => $id]);
    }
    
    /**
     * 로그인 처리
     */
    public function login($phone, $password) {
        $user = $this->findByPhone($phone);
        
        if (!$user) {
            return false;
        }
        
        // 계정 잠금 확인
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            throw new Exception('계정이 잠겨있습니다. 잠시 후 다시 시도해주세요.');
        }
        
        // 비밀번호 확인
        if (!password_verify($password, $user['password'])) {
            $this->incrementFailedLoginAttempts($user['id']);
            return false;
        }
        
        // 로그인 성공 처리
        $this->updateLoginInfo($user['id']);
        $this->logUserActivity($user['id'], 'LOGIN', '로그인 성공');
        
        return $user;
    }
    
    /**
     * 로그인 정보 업데이트
     */
    private function updateLoginInfo($userId) {
        $sql = "UPDATE users SET 
                last_login_at = NOW(),
                last_login_ip = :ip,
                login_count = login_count + 1,
                failed_login_attempts = 0,
                locked_until = NULL
                WHERE id = :id";
        
        $params = [
            ':id' => $userId,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $this->db->execute($sql, $params);
    }
    
    /**
     * 로그인 실패 횟수 증가
     */
    private function incrementFailedLoginAttempts($userId) {
        $sql = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1 WHERE id = :id";
        $this->db->execute($sql, [':id' => $userId]);
        
        // 5회 실패 시 30분 계정 잠금
        $user = $this->findById($userId);
        if ($user['failed_login_attempts'] >= 4) { // 0부터 시작하므로 4가 5번째
            $lockUntil = date('Y-m-d H:i:s', time() + 1800); // 30분 후
            $sql = "UPDATE users SET locked_until = :lock_until WHERE id = :id";
            $this->db->execute($sql, [':lock_until' => $lockUntil, ':id' => $userId]);
            
            $this->logUserActivity($userId, 'ACCOUNT_LOCKED', '계정 잠금 (로그인 5회 실패)');
        }
    }
    
    /**
     * 휴대폰 번호 중복 검사
     */
    public function isPhoneExists($phone) {
        $user = $this->findByPhone($phone);
        return $user !== false;
    }
    
    /**
     * 이메일 중복 검사
     */
    public function isEmailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    /**
     * 닉네임 중복 검사
     */
    public function isNicknameExists($nickname) {
        $user = $this->findByNickname($nickname);
        return $user !== false;
    }
    
    /**
     * 비밀번호 변경
     */
    public function changePassword($userId, $newPassword) {
        $sql = "UPDATE users SET 
                password = :password,
                password_changed_at = NOW()
                WHERE id = :id";
        
        $params = [
            ':id' => $userId,
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ];
        
        $result = $this->db->execute($sql, $params);
        
        if ($result) {
            $this->logUserActivity($userId, 'PASSWORD_CHANGED', '비밀번호 변경');
        }
        
        return $result > 0;
    }
    
    /**
     * 프로필 정보 업데이트
     */
    public function updateProfile($userId, $profileData) {
        $fields = [];
        $params = [':id' => $userId];
        
        $allowedFields = ['nickname', 'email', 'bio', 'birth_date', 'gender'];
        
        foreach ($allowedFields as $field) {
            if (isset($profileData[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $profileData[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        
        $result = $this->db->execute($sql, $params);
        
        if ($result) {
            $this->logUserActivity($userId, 'PROFILE_UPDATED', '프로필 정보 수정');
        }
        
        return $result > 0;
    }
    
    /**
     * 사용자 활동 로그 기록
     */
    public function logUserActivity($userId, $action, $description = '', $extraData = null) {
        $sql = "INSERT INTO user_logs (user_id, action, description, ip_address, user_agent, extra_data) 
                VALUES (:user_id, :action, :description, :ip_address, :user_agent, :extra_data)";
        
        $params = [
            ':user_id' => $userId,
            ':action' => $action,
            ':description' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ':extra_data' => $extraData ? json_encode($extraData) : null
        ];
        
        try {
            $this->db->execute($sql, $params);
        } catch (Exception $e) {
            // 로그 기록 실패는 메인 기능에 영향을 주지 않음
            error_log('Failed to log user activity: ' . $e->getMessage());
        }
    }
    
    /**
     * 사용자 세션 생성
     */
    public function createSession($userId, $sessionId) {
        // 기존 세션 삭제 (중복 로그인 방지)
        $this->destroyUserSessions($userId);
        
        $sql = "INSERT INTO user_sessions (id, user_id, ip_address, user_agent) 
                VALUES (:session_id, :user_id, :ip_address, :user_agent)";
        
        $params = [
            ':session_id' => $sessionId,
            ':user_id' => $userId,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        return $this->db->execute($sql, $params) > 0;
    }
    
    /**
     * 사용자 세션 삭제
     */
    public function destroyUserSessions($userId) {
        $sql = "DELETE FROM user_sessions WHERE user_id = :user_id";
        return $this->db->execute($sql, [':user_id' => $userId]);
    }
    
    /**
     * 세션 ID로 사용자 조회
     */
    public function findBySessionId($sessionId) {
        $sql = "SELECT u.*, s.last_activity 
                FROM users u 
                JOIN user_sessions s ON u.id = s.user_id 
                WHERE s.id = :session_id AND u.status = 'ACTIVE'";
        
        return $this->db->fetch($sql, [':session_id' => $sessionId]);
    }
    
    /**
     * 세션 활동 시간 업데이트
     */
    public function updateSessionActivity($sessionId) {
        $sql = "UPDATE user_sessions SET last_activity = NOW() WHERE id = :session_id";
        return $this->db->execute($sql, [':session_id' => $sessionId]) > 0;
    }
    
    /**
     * 만료된 세션 정리
     */
    public function cleanExpiredSessions($timeout = 7200) { // 2시간
        $sql = "DELETE FROM user_sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL :timeout SECOND)";
        return $this->db->execute($sql, [':timeout' => $timeout]);
    }
} 