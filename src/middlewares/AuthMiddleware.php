<?php
/**
 * 인증 미들웨어 클래스
 */
namespace App\Middlewares;

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
} 