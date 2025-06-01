<?php
/**
 * 사용자 관련 컨트롤러
 */
namespace App\Controllers;

class UserController {
    /**
     * 사용자 프로필 페이지 표시
     */
    public function showProfile() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            return;
        }
        
        // 사용자 정보 가져오기 (실제 구현에서는 DB 조회)
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role']
        ];
        
        // 뷰 표시
        include SRC_PATH . '/views/user/profile.php';
    }
    
    /**
     * 특정 사용자 정보 조회
     */
    public function getUser($id = null) {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 ID 추출
        if ($id === null) {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            preg_match('/\/users\/(\d+)/', $uri, $matches);
            $id = $matches[1] ?? null;
        }
        
        if (!$id) {
            header('HTTP/1.1 400 Bad Request');
            return;
        }
        
        // 사용자 정보 가져오기 (실제 구현에서는 DB 조회)
        $user = [
            'id' => $id,
            'name' => '사용자 ' . $id,
            'role' => 'GENERAL'
        ];
        
        // 뷰 또는 JSON 응답
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode($user);
        } else {
            include SRC_PATH . '/views/user/profile.php';
        }
    }
    
    /**
     * 사용자 정보 업데이트
     */
    public function updateUser() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 ID 추출
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        preg_match('/\/users\/(\d+)/', $uri, $matches);
        $id = $matches[1] ?? null;
        
        // 본인 확인
        if ($id != $_SESSION['user_id']) {
            header('HTTP/1.1 403 Forbidden');
            return;
        }
        
        // PUT 요청 데이터 파싱
        parse_str(file_get_contents("php://input"), $putData);
        
        // 업데이트할 데이터 검증
        $name = filter_var($putData['name'] ?? '', FILTER_SANITIZE_STRING);
        
        if (empty($name)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => '이름은 필수 입력 항목입니다.']);
            return;
        }
        
        // 사용자 정보 업데이트 (실제 구현에서는 DB 업데이트)
        // ...
        
        // 세션 정보 업데이트
        $_SESSION['user_name'] = $name;
        
        // 성공 응답
        header('Content-Type: application/json');
        echo json_encode(['message' => '회원 정보가 수정되었습니다.']);
    }
    
    /**
     * 사용자 계정 삭제
     */
    public function deleteUser() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 ID 추출
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        preg_match('/\/users\/(\d+)/', $uri, $matches);
        $id = $matches[1] ?? null;
        
        // 본인 확인
        if ($id != $_SESSION['user_id']) {
            header('HTTP/1.1 403 Forbidden');
            return;
        }
        
        // 사용자 계정 삭제 (실제 구현에서는 DB에서 삭제)
        // ...
        
        // 세션 삭제
        session_destroy();
        
        // 성공 응답
        header('HTTP/1.1 204 No Content');
    }
} 