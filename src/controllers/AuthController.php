<?php
/**
 * 인증 관련 컨트롤러
 */
namespace App\Controllers;

class AuthController {
    /**
     * 로그인 페이지 표시
     */
    public function showLogin() {
        include SRC_PATH . '/views/auth/login.php';
    }
    
    /**
     * 로그인 처리
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            return;
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (!$email || empty($password)) {
            $_SESSION['error'] = '이메일과 비밀번호를 모두 입력해주세요.';
            header('Location: /auth/login');
            return;
        }
        
        // 사용자 인증 로직 (실제 구현에서는 DB 조회)
        // ...
        
        // 세션에 사용자 정보 저장
        $_SESSION['user_id'] = 1; // 임시 ID
        $_SESSION['user_name'] = '홍길동'; // 임시 이름
        $_SESSION['user_role'] = 'GENERAL'; // 임시 역할
        
        // 세션 ID 재생성 (세션 고정 공격 방지)
        session_regenerate_id(true);
        
        // 메인 페이지로 리다이렉트
        header('Location: /');
    }
    
    /**
     * 회원가입 페이지 표시
     */
    public function showSignup() {
        include SRC_PATH . '/views/auth/signup.php';
    }
    
    /**
     * 회원가입 처리
     */
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            return;
        }
        
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // 입력 검증
        $errors = [];
        
        if (!$email) {
            $errors[] = '유효한 이메일 주소를 입력해주세요.';
        }
        
        if (empty($name)) {
            $errors[] = '이름을 입력해주세요.';
        }
        
        if (strlen($password) < 8) {
            $errors[] = '비밀번호는 최소 8자 이상이어야 합니다.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = '비밀번호와 비밀번호 확인이 일치하지 않습니다.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = [
                'email' => $email,
                'name' => $name
            ];
            header('Location: /auth/signup');
            return;
        }
        
        // 사용자 등록 로직 (실제 구현에서는 DB에 저장)
        // ...
        
        // 성공 메시지 설정
        $_SESSION['success'] = '회원가입이 완료되었습니다. 로그인해주세요.';
        
        // 로그인 페이지로 리다이렉트
        header('Location: /auth/login');
    }
    
    /**
     * 로그아웃 처리
     */
    public function logout() {
        // 세션 변수 초기화
        $_SESSION = [];
        
        // 세션 쿠키 삭제
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        // 세션 삭제
        session_destroy();
        
        // 로그인 페이지로 리다이렉트
        header('Location: /auth/login');
    }
} 