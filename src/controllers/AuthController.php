<?php
/**
 * 인증 관련 컨트롤러
 */

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
        
        $phone = $this->sanitizePhone($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (!$phone || empty($password)) {
            $_SESSION['error'] = '휴대폰 번호와 비밀번호를 모두 입력해주세요.';
            header('Location: /auth/login');
            return;
        }
        
        // 휴대폰 번호 유효성 검사
        if (!$this->isValidPhone($phone)) {
            $_SESSION['error'] = '올바른 휴대폰 번호 형식이 아닙니다. (예: 010-1234-5678)';
            header('Location: /auth/login');
            return;
        }
        
        // 사용자 인증 로직 (실제 구현에서는 DB 조회)
        // TODO: 데이터베이스에서 휴대폰 번호로 사용자 조회 및 비밀번호 검증
        
        // 임시 인증 성공 처리
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = '홍길동';
        $_SESSION['phone'] = $phone;
        $_SESSION['user_role'] = 'GENERAL';
        
        // 세션 ID 재생성 (세션 고정 공격 방지)
        session_regenerate_id(true);
        
        $_SESSION['success'] = '로그인이 완료되었습니다.';
        
        // 메인 페이지로 리다이렉트
        header('Location: /');
        exit;
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
        
        $phone = $this->sanitizePhone($_POST['phone'] ?? '');
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $termsAccepted = isset($_POST['terms']) && $_POST['terms'] === '1';
        $marketingAccepted = isset($_POST['marketing']) && $_POST['marketing'] === '1';
        
        // 입력 검증
        $errors = [];
        
        if (!$this->isValidPhone($phone)) {
            $errors[] = '올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)';
        }
        
        if (empty($username) || strlen($username) < 2) {
            $errors[] = '사용자명은 2자 이상 입력해주세요.';
        }
        
        if (strlen($password) < 8) {
            $errors[] = '비밀번호는 최소 8자 이상이어야 합니다.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = '비밀번호와 비밀번호 확인이 일치하지 않습니다.';
        }
        
        if (!$termsAccepted) {
            $errors[] = '이용약관에 동의해주세요.';
        }
        
        // TODO: 휴대폰 번호 중복 검사
        // if ($this->isPhoneExists($phone)) {
        //     $errors[] = '이미 가입된 휴대폰 번호입니다.';
        // }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: /auth/signup');
            return;
        }
        
        // 사용자 등록 로직 (실제 구현에서는 DB에 저장)
        // TODO: 데이터베이스에 사용자 정보 저장
        // $userData = [
        //     'phone' => $phone,
        //     'username' => $username,
        //     'password' => password_hash($password, PASSWORD_DEFAULT),
        //     'marketing_consent' => $marketingAccepted,
        //     'created_at' => date('Y-m-d H:i:s')
        // ];
        
        // 성공 메시지 설정
        $_SESSION['success'] = '회원가입이 완료되었습니다. 로그인해주세요.';
        
        // 로그인 페이지로 리다이렉트
        header('Location: /auth/login');
        exit;
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
        exit;
    }
    
    /**
     * 휴대폰 번호 유효성 검사
     */
    private function isValidPhone($phone) {
        // 010, 011, 016, 017, 018, 019로 시작하는 한국 휴대폰 번호
        $pattern = '/^01[0-9]-[0-9]{3,4}-[0-9]{4}$/';
        return preg_match($pattern, $phone);
    }
    
    /**
     * 휴대폰 번호 정제 (숫자만 추출 후 하이픈 추가)
     */
    private function sanitizePhone($phone) {
        // 숫자만 추출
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // 11자리인 경우 하이픈 추가
        if (strlen($phone) === 11 && substr($phone, 0, 2) === '01') {
            $phone = substr($phone, 0, 3) . '-' . substr($phone, 3, 4) . '-' . substr($phone, 7, 4);
        }
        
        return $phone;
    }
} 