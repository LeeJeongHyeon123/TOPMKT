<?php
/**
 * 인증번호 확인 API
 * 
 * 요청 파라미터:
 * - phone: 전화번호
 * - country: 국가 코드 (예: +82)
 * - code: 인증번호
 * - sessionInfo: 인증번호 발송 시 받은 세션 정보
 * - recaptcha_token: reCAPTCHA 토큰
 * 
 * 반환값:
 * - success: 성공 여부
 * - message: 메시지
 * - idToken: 인증 성공 시 발급되는 토큰 (로그인/회원가입에 사용)
 */

// Composer 오토로더 로드
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// POST 요청이 아닌 경우 오류 반환
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 메서드입니다.'
    ]);
    exit();
}

// 로그 파일 설정
function debug_log($message, $data = null) {
    $logDir = __DIR__ . '/../../logs';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $log = date('[Y-m-d H:i:s]') . ' ' . $message;
    if ($data !== null) {
        $log .= ': ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    $log .= PHP_EOL;
    
    file_put_contents($logDir . '/verify_code_debug.log', $log, FILE_APPEND);
}

// 요청 데이터 파싱
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

debug_log('인증번호 확인 요청 받음', $data);

// 필수 파라미터 검증
if (!isset($data['phone']) || !isset($data['country']) || !isset($data['code']) || !isset($data['sessionInfo']) || !isset($data['recaptcha_token'])) {
    debug_log('필수 파라미터 누락', $data);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '필수 파라미터가 누락되었습니다.'
    ]);
    exit();
}

// 인증번호 검증
$phone = $data['phone'];
$country = $data['country'];
$code = $data['code'];
$sessionInfo = $data['sessionInfo'];
$recaptchaToken = $data['recaptcha_token'];

try {
    // reCAPTCHA 검증
    debug_log('reCAPTCHA 검증 시작', ['token' => substr($recaptchaToken, 0, 10) . '...']);
    
    // 클래스 존재 여부 확인
    if (!class_exists('App\Services\RecaptchaService')) {
        debug_log('RecaptchaService 클래스를 찾을 수 없음', [
            'autoload_exists' => file_exists(__DIR__ . '/../../vendor/autoload.php'),
            'file_exists' => file_exists(__DIR__ . '/../../app/Services/RecaptchaService.php')
        ]);
        throw new \Exception('RecaptchaService 클래스를 찾을 수 없습니다.');
    }
    
    require_once __DIR__ . '/../../app/Services/RecaptchaService.php';
    
    try {
        $recaptchaService = new \App\Services\RecaptchaService();
        debug_log('RecaptchaService 인스턴스 생성 성공');
    } catch (\Throwable $e) {
        debug_log('RecaptchaService 인스턴스 생성 실패', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        throw $e;
    }
    
    try {
        $recaptchaResult = $recaptchaService->verifyToken($recaptchaToken, 'verify_code');
        debug_log('reCAPTCHA 검증 결과', $recaptchaResult);
    } catch (\Throwable $e) {
        debug_log('reCAPTCHA 토큰 검증 중 오류', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        throw $e;
    }
    
    if (!$recaptchaResult['success']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '보안 인증에 실패했습니다.'
        ]);
        exit();
    }
    
    if (isset($recaptchaResult['score']) && $recaptchaResult['score'] < 0.3) {
        debug_log('reCAPTCHA 점수 낮음', ['score' => $recaptchaResult['score']]);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '보안 검증 점수가 낮습니다. 잠시 후 다시 시도해주세요.'
        ]);
        exit();
    }
    
    // Firebase 설정 로드
    debug_log('Firebase 설정 로드 시작');
    try {
        $firebase_config = require_once __DIR__ . '/../../config/firebase/firebase-config.php';
        debug_log('Firebase 설정 로드 완료');
    } catch (\Throwable $e) {
        debug_log('Firebase 설정 로드 실패', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        throw $e;
    }
    
    // Firebase Auth 서비스 로드
    debug_log('Firebase Auth 서비스 로드 시작');
    require_once __DIR__ . '/../../app/Services/Firebase/AuthService.php';
    
    // 디버깅: 파일 존재 확인
    if (!file_exists(__DIR__ . '/../../app/Services/Firebase/AuthService.php')) {
        debug_log('AuthService.php 파일을 찾을 수 없음', ['path' => __DIR__ . '/../../app/Services/Firebase/AuthService.php']);
        throw new \Exception('AuthService.php 파일을 찾을 수 없습니다.');
    }
    
    try {
        $authService = \App\Services\Firebase\AuthService::getInstance();
        debug_log('Firebase Auth 서비스 로드 완료');
    } catch (\Throwable $e) {
        debug_log('Firebase Auth 서비스 인스턴스 생성 오류', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
    
    // 전화번호 정규화
    $normalizedPhone = preg_replace('/[^0-9+]/', '', $phone);
    if ($country && strpos($normalizedPhone, $country) !== 0) {
        $normalizedPhone = $country . ltrim($normalizedPhone, '0');
    }
    debug_log('정규화된 전화번호', ['original' => $phone, 'normalized' => $normalizedPhone]);
    
    // 인증번호 확인
    debug_log('인증번호 확인 시작', ['phone' => $normalizedPhone, 'code' => '******', 'sessionInfo' => substr($sessionInfo, 0, 10) . '...']);
    
    try {
        $result = $authService->verifyCode($normalizedPhone, $code, $sessionInfo);
        debug_log('인증번호 확인 결과', $result);
    } catch (\Throwable $e) {
        debug_log('인증번호 확인 메서드 실행 오류', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
    
    if (!$result['success']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $result['message'],
            'remainingAttempts' => $result['remainingAttempts'] ?? null,
            'isBlocked' => $result['isBlocked'] ?? false,
            'blockedUntil' => $result['blockedUntil'] ?? null
        ]);
        exit();
    }
    
    // 성공 응답
    debug_log('인증 성공 응답 전송', ['idToken' => isset($result['idToken']) ? substr($result['idToken'], 0, 10) . '...' : 'null']);
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => '인증이 완료되었습니다.',
        'idToken' => $result['idToken'],
        'remainingAttempts' => $result['remainingAttempts'] ?? 5
    ]);
    
} catch (\Throwable $e) {
    debug_log('인증번호 확인 오류', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다: ' . $e->getMessage(),
        'remainingAttempts' => 5 // 오류의 경우 기본값 설정
    ]);
} 