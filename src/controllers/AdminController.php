<?php
/**
 * AdminController 클래스
 * 관리자 페이지 전용 컨트롤러
 */

require_once SRC_PATH . '/config/database.php';
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';

class AdminController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
        
        // 관리자 권한 체크
        $this->checkAdminAccess();
    }
    
    /**
     * 관리자 권한 체크
     */
    private function checkAdminAccess() {
        // 로그인 체크
        if (!AuthMiddleware::isLoggedIn()) {
            header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        
        // 관리자 권한 체크
        $user = AuthMiddleware::getCurrentUser();
        if ($user['role'] !== 'ROLE_ADMIN') {
            header('HTTP/1.1 403 Forbidden');
            include SRC_PATH . '/views/templates/403.php';
            exit;
        }
        
        // 관리자 활동 로깅
        $this->logAdminActivity();
    }
    
    /**
     * 관리자 활동 로깅
     */
    private function logAdminActivity() {
        $userId = AuthMiddleware::getCurrentUserId();
        $action = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $this->db->prepare("
            INSERT INTO user_logs (user_id, action, description, ip_address, user_agent, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, 'ADMIN_ACCESS', $action, $ip, $userAgent]);
    }
    
    /**
     * 관리자 대시보드 메인 페이지
     */
    public function dashboard() {
        // 대시보드 데이터 수집
        $dashboardData = $this->getDashboardData();
        
        // 헤더 데이터
        $headerData = [
            'title' => '관리자 대시보드 - 탑마케팅',
            'description' => '탑마케팅 관리자 페이지',
            'pageSection' => 'admin'
        ];
        
        // 뷰 렌더링
        $this->renderView('admin/dashboard', $dashboardData, $headerData);
    }
    
    /**
     * 대시보드 데이터 수집
     */
    private function getDashboardData() {
        // 오늘의 통계
        $todayStats = $this->getTodayStats();
        
        // 주간 트렌드
        $weeklyTrend = $this->getWeeklyTrend();
        
        // 긴급 처리 사항
        $urgentTasks = $this->getUrgentTasks();
        
        // 최근 활동
        $recentActivities = $this->getRecentActivities();
        
        return [
            'todayStats' => $todayStats,
            'weeklyTrend' => $weeklyTrend,
            'urgentTasks' => $urgentTasks,
            'recentActivities' => $recentActivities
        ];
    }
    
    /**
     * 오늘의 통계
     */
    private function getTodayStats() {
        $today = date('Y-m-d');
        
        // 신규 가입자
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $todaySignups = $stmt->fetchColumn();
        
        // 오늘 게시글
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM posts WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $todayPosts = $stmt->fetchColumn();
        
        // 활성 사용자 (최근 30분)
        $stmt = $this->db->prepare("
            SELECT COUNT(DISTINCT user_id) FROM user_sessions 
            WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 30 MINUTE)
        ");
        $stmt->execute();
        $activeUsers = $stmt->fetchColumn();
        
        // 대기 중인 기업인증
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM company_profiles WHERE status = 'pending'");
        $stmt->execute();
        $pendingCorps = $stmt->fetchColumn();
        
        return [
            'signups' => (int)$todaySignups,
            'posts' => (int)$todayPosts,
            'activeUsers' => (int)$activeUsers,
            'pendingCorps' => (int)$pendingCorps
        ];
    }
    
    /**
     * 주간 트렌드 (최근 7일)
     */
    private function getWeeklyTrend() {
        $weeklySignups = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?");
            $stmt->execute([$date]);
            $weeklySignups[] = [
                'date' => $date,
                'count' => (int)$stmt->fetchColumn()
            ];
        }
        
        return [
            'signups' => $weeklySignups
        ];
    }
    
    /**
     * 긴급 처리 사항
     */
    private function getUrgentTasks() {
        // 대기 중인 기업인증
        $stmt = $this->db->prepare("
            SELECT cp.*, u.nickname 
            FROM company_profiles cp 
            JOIN users u ON cp.user_id = u.id 
            WHERE cp.status = 'pending' 
            ORDER BY cp.created_at ASC 
            LIMIT 5
        ");
        $stmt->execute();
        $pendingCorps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'pendingCorps' => $pendingCorps,
            'reports' => [], // TODO: 신고 시스템 구현 시 추가
            'systemAlerts' => []
        ];
    }
    
    /**
     * 최근 활동
     */
    private function getRecentActivities() {
        // 최근 게시글
        $stmt = $this->db->prepare("
            SELECT p.*, u.nickname 
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 최근 댓글
        $stmt = $this->db->prepare("
            SELECT c.*, u.nickname, p.title as post_title 
            FROM comments c 
            JOIN users u ON c.user_id = u.id 
            JOIN posts p ON c.post_id = p.id 
            ORDER BY c.created_at DESC 
            LIMIT 5
        ");
        $stmt->execute();
        $recentComments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'posts' => $recentPosts,
            'comments' => $recentComments
        ];
    }
    
    /**
     * 관리자 전용 뷰 렌더링 (헤더/푸터 없음)
     */
    private function renderView($viewPath, $data = [], $headerData = []) {
        // 데이터를 변수로 추출
        extract($data);
        extract($headerData);
        
        // 관리자 페이지 전용 레이아웃
        ?>
        <!DOCTYPE html>
        <html lang="ko">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= $title ?? '관리자 페이지 - 탑마케팅' ?></title>
            <meta name="description" content="<?= $description ?? '탑마케팅 관리자 페이지' ?>">
            <meta name="robots" content="noindex, nofollow">
            <link rel="icon" href="/assets/images/favicon.svg" type="image/svg+xml">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        </head>
        <?php
        
        // 메인 뷰 포함
        include SRC_PATH . '/views/' . $viewPath . '.php';
        
        ?>
        </html>
        <?php
    }
}