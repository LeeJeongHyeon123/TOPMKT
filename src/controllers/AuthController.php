<?php
/**
 * 인증 관련 컨트롤러
 */

require_once SRC_PATH . '/helpers/SmsHelper.php';
require_once SRC_PATH . '/models/User.php';

class AuthController {
    
    private $userModel;
    
    public function __construct() {
        // CSRF 토큰 생성
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        // User 모델 초기화
        $this->userModel = new User();
    }
    
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
        
        // CSRF 토큰 검증
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = '보안 토큰이 일치하지 않습니다. 다시 시도해주세요.';
            header('Location: /auth/login');
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
            $_SESSION['error'] = '010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.';
            header('Location: /auth/login');
            return;
        }
        
        try {
            // 사용자 인증
            $user = $this->userModel->login($phone, $password);
            
            if (!$user) {
                $_SESSION['error'] = '휴대폰 번호 또는 비밀번호가 일치하지 않습니다.';
                header('Location: /auth/login');
                return;
            }
            
            // 로그인 세션 생성
            $this->createUserSession($user);
            
            $_SESSION['success'] = $user['nickname'] . '님, 환영합니다!';
            
            // 메인 페이지로 리다이렉트
            header('Location: /');
            exit;
            
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /auth/login');
            return;
        }
    }
    
    /**
     * 회원가입 페이지 표시
     */
    public function showSignup() {
        include SRC_PATH . '/views/auth/signup.php';
    }
    
    /**
     * 인증번호 발송
     */
    public function sendVerification() {
        // AJAX 요청만 처리
        if (!$this->isAjaxRequest()) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $this->sanitizePhone($input['phone'] ?? '');
        $recaptchaToken = $input['recaptcha_token'] ?? '';
        
        // reCAPTCHA 검증
        if (!$this->verifyRecaptcha($recaptchaToken, 'send_verification')) {
            echo json_encode([
                'success' => false, 
                'message' => '보안 검증에 실패했습니다. 다시 시도해주세요.'
            ]);
            return;
        }
        
        // 입력 검증
        if (!$this->isValidPhone($phone)) {
            echo json_encode([
                'success' => false, 
                'message' => '010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.'
            ]);
            return;
        }
        
        // 010 번호 추가 검증
        if (!$this->isValidKoreanMobile($phone)) {
            echo json_encode([
                'success' => false, 
                'message' => '010으로 시작하는 한국 휴대폰 번호만 사용할 수 있습니다.'
            ]);
            return;
        }
        
        // SMS 발송 제한 확인 (스팸 방지)
        if ($this->isSmsRateLimited($phone)) {
            echo json_encode([
                'success' => false, 
                'message' => '너무 많은 요청입니다. 1분 후 다시 시도해주세요.'
            ]);
            return;
        }
        
        // TODO: 휴대폰 번호 중복 검사
        // if ($this->isPhoneExists($phone)) {
        //     echo json_encode(['success' => false, 'message' => '이미 가입된 휴대폰 번호입니다.']);
        //     return;
        // }
        
        // 인증번호 생성 (4자리)
        $verificationCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // 세션에 인증번호와 만료시간 저장
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['verification_phone'] = $phone;
        $_SESSION['verification_expires'] = time() + 180; // 3분 후 만료
        
        // SMS 발송 제한 기록
        $this->recordSmsRequest($phone);
        
        // SMS 발송
        $result = sendAuthCodeSms($phone, $verificationCode);
        
        if ($result['success']) {
            echo json_encode([
                'success' => true,
                'message' => '인증번호가 발송되었습니다.',
                'expires_in' => 180
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'SMS 발송에 실패했습니다. 잠시 후 다시 시도해주세요.'
            ]);
        }
    }
    
    /**
     * 인증번호 확인
     */
    public function verifyCode() {
        // AJAX 요청만 처리
        if (!$this->isAjaxRequest()) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $phone = $this->sanitizePhone($input['phone'] ?? '');
        $code = $input['code'] ?? '';
        
        // 입력 검증
        if (!$this->isValidPhone($phone) || !preg_match('/^\d{4}$/', $code)) {
            echo json_encode([
                'success' => false,
                'message' => '입력값이 올바르지 않습니다.'
            ]);
            return;
        }
        
        // 세션에서 인증 정보 확인
        $sessionCode = $_SESSION['verification_code'] ?? '';
        $sessionPhone = $_SESSION['verification_phone'] ?? '';
        $expiresAt = $_SESSION['verification_expires'] ?? 0;
        
        // 인증번호 만료 확인
        if (time() > $expiresAt) {
            unset($_SESSION['verification_code'], $_SESSION['verification_phone'], $_SESSION['verification_expires']);
            echo json_encode([
                'success' => false,
                'message' => '인증 시간이 만료되었습니다. 다시 인증번호를 요청해주세요.'
            ]);
            return;
        }
        
        // 휴대폰 번호와 인증번호 일치 확인
        if ($phone !== $sessionPhone || $code !== $sessionCode) {
            echo json_encode([
                'success' => false,
                'message' => '인증번호가 일치하지 않습니다.'
            ]);
            return;
        }
        
        // 인증 성공 - 세션에 인증 완료 표시
        $_SESSION['phone_verified'] = $phone;
        $_SESSION['phone_verified_at'] = time();
        
        // 사용한 인증번호 정보 삭제
        unset($_SESSION['verification_code'], $_SESSION['verification_phone'], $_SESSION['verification_expires']);
        
        echo json_encode([
            'success' => true,
            'message' => '휴대폰 인증이 완료되었습니다.'
        ]);
    }
    
    /**
     * 회원가입 처리
     */
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            return;
        }
        
        // CSRF 토큰 검증
        if (!$this->verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = '보안 토큰이 일치하지 않습니다. 다시 시도해주세요.';
            header('Location: /auth/signup');
            return;
        }
        
        // reCAPTCHA 검증
        $recaptchaToken = $_POST['recaptcha_token'] ?? '';
        if (!$this->verifyRecaptcha($recaptchaToken, 'signup')) {
            $_SESSION['error'] = '보안 검증에 실패했습니다. 다시 시도해주세요.';
            header('Location: /auth/signup');
            return;
        }
        
        $phone = $this->sanitizePhone($_POST['phone'] ?? '');
        $nickname = $this->sanitizeInput($_POST['nickname'] ?? '');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $termsAccepted = isset($_POST['terms']) && $_POST['terms'] === '1';
        $marketingAccepted = isset($_POST['marketing']) && $_POST['marketing'] === '1';
        $phoneVerified = $_POST['phone_verified'] ?? '0';
        
        // 입력 검증
        $errors = [];
        
        // 닉네임 검증
        if (empty($nickname)) {
            $errors[] = '닉네임을 입력해주세요.';
        } elseif (strlen($nickname) < 2 || strlen($nickname) > 20) {
            $errors[] = '닉네임은 2자 이상 20자 이하로 입력해주세요.';
        } elseif (!preg_match('/^[가-힣a-zA-Z0-9_]+$/', $nickname)) {
            $errors[] = '닉네임은 한글, 영문, 숫자, 언더스코어만 사용할 수 있습니다.';
        }
        
        // 휴대폰 번호 검증 (010 전용)
        if (!$this->isValidPhone($phone)) {
            $errors[] = '010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.';
        } elseif (!$this->isValidKoreanMobile($phone)) {
            $errors[] = '010으로 시작하는 한국 휴대폰 번호만 사용할 수 있습니다.';
        }
        
        // 휴대폰 인증 확인
        if ($phoneVerified !== '1' || 
            !isset($_SESSION['phone_verified']) || 
            $_SESSION['phone_verified'] !== $phone ||
            (time() - ($_SESSION['phone_verified_at'] ?? 0)) > 1800) { // 30분 이내
            $errors[] = '휴대폰 인증을 완료해주세요.';
        }
        
        // 이메일 검증 (필수)
        if (empty($email)) {
            $errors[] = '이메일을 입력해주세요. (필수)';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = '올바른 이메일 형식을 입력해주세요.';
        } elseif (strlen($email) > 100) {
            $errors[] = '이메일 주소가 너무 깁니다. (최대 100자)';
        }
        
        // 비밀번호 검증
        if (strlen($password) < 8) {
            $errors[] = '비밀번호는 최소 8자 이상이어야 합니다.';
        } elseif (strlen($password) > 100) {
            $errors[] = '비밀번호가 너무 깁니다. (최대 100자)';
        } elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
            $errors[] = '비밀번호는 영문, 숫자, 특수문자를 포함해야 합니다.';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = '비밀번호와 비밀번호 확인이 일치하지 않습니다.';
        }
        
        if (!$termsAccepted) {
            $errors[] = '이용약관에 동의해주세요.';
        }
        
        // 중복 검사
        try {
            if ($this->userModel->isNicknameExists($nickname)) {
                $errors[] = '이미 사용 중인 닉네임입니다.';
            }
            
            if ($this->userModel->isPhoneExists($phone)) {
                $errors[] = '이미 가입된 휴대폰 번호입니다.';
            }
            
            if ($this->userModel->isEmailExists($email)) {
                $errors[] = '이미 가입된 이메일입니다.';
            }
        } catch (Exception $e) {
            error_log('Database error during signup validation: ' . $e->getMessage());
            $errors[] = '회원가입 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.';
        }
        
        if (!empty($errors)) {
            $_SESSION['error'] = implode(' ', $errors);
            header('Location: /auth/signup');
            return;
        }
        
        // 회원 정보 저장
        try {
            $userData = [
                'phone' => $phone,
                'nickname' => $nickname,
                'email' => $email,
                'password' => $password,
                'terms_agreed' => $termsAccepted,
                'marketing_agreed' => $marketingAccepted
            ];
            
            $userId = $this->userModel->create($userData);
            
            if ($userId) {
                // 회원가입 성공 - 자동 로그인 처리
                $newUser = $this->userModel->findById($userId);
                $this->createUserSession($newUser);
                
                // 인증 세션 정보 정리
                unset($_SESSION['phone_verified'], $_SESSION['phone_verified_at']);
                
                // 환영 SMS 발송 (선택적)
                try {
                    sendWelcomeSms($phone, $nickname);
                } catch (Exception $e) {
                    // SMS 발송 실패는 회원가입 성공에 영향을 주지 않음
                    error_log('Welcome SMS sending failed: ' . $e->getMessage());
                }
                
                // 성공 메시지 설정
                $_SESSION['success'] = $nickname . '님, 가입을 환영합니다! 탑마케팅과 함께 성공적인 마케팅 여정을 시작하세요.';
                
                // 메인 페이지로 리다이렉트 (자동 로그인 완료)
                header('Location: /');
                exit;
            } else {
                $_SESSION['error'] = '회원가입 처리 중 오류가 발생했습니다. 다시 시도해주세요.';
                header('Location: /auth/signup');
                return;
            }
            
        } catch (Exception $e) {
            error_log('User registration failed: ' . $e->getMessage());
            $_SESSION['error'] = '회원가입 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.';
            header('Location: /auth/signup');
            return;
        }
    }
    
    /**
     * 로그아웃 처리
     */
    public function logout() {
        // 사용자 세션 로그 기록
        if (isset($_SESSION['user_id'])) {
            try {
                $this->userModel->logUserActivity($_SESSION['user_id'], 'LOGOUT', '로그아웃');
                
                // 데이터베이스 세션 정리
                $this->userModel->destroyUserSessions($_SESSION['user_id']);
            } catch (Exception $e) {
                error_log('Logout session cleanup failed: ' . $e->getMessage());
            }
        }
        
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
        
        // 성공 메시지를 위한 새 세션 시작
        session_start();
        $_SESSION['success'] = '로그아웃이 완료되었습니다.';
        
        // 메인 페이지로 리다이렉트
        header('Location: /');
        exit;
    }
    
    /**
     * 휴대폰 번호 유효성 검사
     */
    private function isValidPhone($phone) {
        // 010으로 시작하는 한국 휴대폰 번호만 허용
        $pattern = '/^010-[0-9]{3,4}-[0-9]{4}$/';
        return preg_match($pattern, $phone);
    }
    
    /**
     * 한국 휴대폰 번호 010 검증
     */
    private function isValidKoreanMobile($phone) {
        // 010으로 시작하는지 확인
        return strpos($phone, '010-') === 0;
    }
    
    /**
     * SMS 발송 제한 확인 (Rate Limiting)
     */
    private function isSmsRateLimited($phone) {
        $sessionKey = 'sms_requests_' . md5($phone);
        $currentTime = time();
        
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }
        
        // 1분 이전의 요청들은 제거
        $_SESSION[$sessionKey] = array_filter($_SESSION[$sessionKey], function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) < 60;
        });
        
        // 1분 내에 3회 이상 요청하면 제한
        return count($_SESSION[$sessionKey]) >= 3;
    }
    
    /**
     * SMS 요청 기록
     */
    private function recordSmsRequest($phone) {
        $sessionKey = 'sms_requests_' . md5($phone);
        
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }
        
        $_SESSION[$sessionKey][] = time();
    }
    
    /**
     * reCAPTCHA v3 토큰 검증
     */
    private function verifyRecaptcha($token, $action) {
        if (empty($token)) {
            return false;
        }
        
        // 실제 reCAPTCHA 비밀 키 적용
        $secretKey = '6LfViDErAAAAAJYZ6zqP3I6q124NuaUlAGcUWeB5';
        
        $data = [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        
        if ($result === false) {
            error_log('reCAPTCHA API 호출 실패');
            return false;
        }
        
        $resultArray = json_decode($result, true);
        
        if (!$resultArray) {
            error_log('reCAPTCHA 응답 파싱 실패');
            return false;
        }
        
        // 성공 여부 및 점수 확인
        $isSuccess = $resultArray['success'] ?? false;
        $score = $resultArray['score'] ?? 0;
        $receivedAction = $resultArray['action'] ?? '';
        
        // 액션이 일치하고, 성공했으며, 점수가 0.5 이상인 경우 통과
        if ($isSuccess && $receivedAction === $action && $score >= 0.5) {
            return true;
        }
        
        // 실패 로그 기록
        $errorCodes = $resultArray['error-codes'] ?? [];
        error_log('reCAPTCHA 검증 실패 - Action: ' . $action . ', Score: ' . $score . ', Errors: ' . implode(', ', $errorCodes));
        
        return false;
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
    
    /**
     * 입력값 정제
     */
    private function sanitizeInput($input) {
        return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
    }
    
    /**
     * AJAX 요청 확인
     */
    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * CSRF 토큰 검증
     */
    private function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    private function createUserSession($user) {
        // 세션에 사용자 정보 저장
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nickname'];
        $_SESSION['phone'] = $user['phone'];
        $_SESSION['user_role'] = $user['role'];
        
        // 세션 ID 재생성 (세션 고정 공격 방지)
        session_regenerate_id(true);
    }
} 