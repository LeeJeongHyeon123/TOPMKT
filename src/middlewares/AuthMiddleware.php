<?php
/**
 * 인증 미들웨어 클래스
 */

class AuthMiddleware {
    /**
     * 인증 확인
     * 인증되지 않은 사용자는 로그인 페이지로 리다이렉트
     *
     * @return bool 인증 여부
     */
    public static function isAuthenticated() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }
        
        return true;
    }
    
    /**
     * 특정 역할 확인
     * 인증되지 않았거나 권한이 없는 사용자는 적절한 페이지로 리다이렉트
     *
     * @param string|array $role 필요한 역할 (단일 문자열 또는 배열)
     * @return bool 권한 여부
     */
    public static function hasRole($role) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('Location: /auth/login');
            exit;
        }
        
        $userRole = $_SESSION['user_role'];
        
        // 관리자는 모든 권한 허용
        if ($userRole === 'ADMIN') {
            return true;
        }
        
        // 단일 역할 또는 여러 역할 확인
        if (is_array($role)) {
            if (!in_array($userRole, $role)) {
                header('HTTP/1.1 403 Forbidden');
                include SRC_PATH . '/views/templates/403.php';
                exit;
            }
        } else {
            if ($userRole !== $role) {
                header('HTTP/1.1 403 Forbidden');
                include SRC_PATH . '/views/templates/403.php';
                exit;
            }
        }
        
        return true;
    }
    
    /**
     * API 인증 확인
     * API 요청에 대한 인증 확인 (세션 또는 토큰)
     *
     * @return bool 인증 여부
     */
    public static function apiAuthenticate() {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => '인증이 필요합니다.']);
            exit;
        }
        
        return true;
    }
    
    /**
     * API 권한 확인
     * 
     * @param string|array $role 필요한 역할
     * @return bool 권한 여부
     */
    public static function apiHasRole($role) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('HTTP/1.1 401 Unauthorized');
            echo json_encode(['error' => '인증이 필요합니다.']);
            exit;
        }
        
        $userRole = $_SESSION['user_role'];
        
        // 관리자는 모든 권한 허용
        if ($userRole === 'ADMIN') {
            return true;
        }
        
        // 단일 역할 또는 여러 역할 확인
        if (is_array($role)) {
            if (!in_array($userRole, $role)) {
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['error' => '권한이 없습니다.']);
                exit;
            }
        } else {
            if ($userRole !== $role) {
                header('HTTP/1.1 403 Forbidden');
                echo json_encode(['error' => '권한이 없습니다.']);
                exit;
            }
        }
        
        return true;
    }
    
    /**
     * 로그인 상태 확인 (리다이렉트 없이)
     * 
     * @return bool 로그인 여부
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * 현재 사용자 ID 반환
     * 
     * @return int|null 사용자 ID 또는 null
     */
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * 현재 사용자 역할 반환
     * 
     * @return string|null 사용자 역할 또는 null
     */
    public static function getCurrentUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    /**
     * 현재 사용자 프로필 이미지 경로 반환
     * 
     * @return string|null 프로필 이미지 경로 또는 null
     */
    public static function getCurrentUserProfileImage() {
        // 세션에 저장된 값 우선 사용
        if (isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image'])) {
            return $_SESSION['profile_image'];
        }
        
        // 세션에 없으면 데이터베이스에서 조회
        $currentUserId = self::getCurrentUserId();
        if (!$currentUserId) {
            return null;
        }
        
        try {
            require_once SRC_PATH . '/config/database.php';
            $db = Database::getInstance();
            
            $stmt = $db->prepare("
                SELECT profile_image_thumb 
                FROM users 
                WHERE id = :user_id AND status = 'active'
            ");
            $stmt->execute([':user_id' => $currentUserId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $profileImage = $result ? $result['profile_image_thumb'] : null;
            
            // 세션에 저장하여 다음 요청에서 재사용
            if ($profileImage) {
                $_SESSION['profile_image'] = $profileImage;
            }
            
            return $profileImage;
        } catch (Exception $e) {
            error_log('프로필 이미지 조회 오류: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 관리자 권한 확인
     * 
     * @return bool 관리자 여부
     */
    public static function isAdmin() {
        $role = self::getCurrentUserRole();
        return $role === 'ADMIN' || $role === 'SUPER_ADMIN';
    }
    
    /**
     * 소유자 또는 관리자 권한 확인
     * 
     * @param int $ownerId 소유자 ID
     * @return bool 권한 여부
     */
    public static function isOwnerOrAdmin($ownerId) {
        $currentUserId = self::getCurrentUserId();
        return ($currentUserId && $currentUserId == $ownerId) || self::isAdmin();
    }
    
} 