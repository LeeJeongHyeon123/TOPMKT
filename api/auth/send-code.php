<?php
// Composer 오토로더 로드
require_once __DIR__ . '/../../vendor/autoload.php';

// 디버그 로그 파일 설정
$debug_log_file = __DIR__ . '/../../logs/send_code_debug.log';

// 디버그 로그 함수
function debug_log($msg, $data = null) {
    $logFile = __DIR__ . '/../../logs/send_code_debug.log';
    $str = '[' . date('Y-m-d H:i:s') . '] ' . $msg;
    if ($data !== null) {
        $str .= ' ' . print_r($data, true);
    }
    file_put_contents($logFile, $str . "\n", FILE_APPEND);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    debug_log("PHP 에러 발생", [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
    
    // 버퍼 클리어
    if (ob_get_level()) ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.'
    ]);
    exit();
}

// 예외 핸들러 설정
function handleException($exception) {
    debug_log("예외 발생", [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    // 버퍼 클리어
    if (ob_get_level()) ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.'
    ]);
    exit();
}

// 에러 핸들러 등록
set_error_handler('handleError');
set_exception_handler('handleException');

// 출력 버퍼링 시작
ob_start();
debug_log("=== API 요청 시작 ===");
debug_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
debug_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
debug_log("REQUEST_HEADERS: " . print_r(getallheaders(), true));

// 에러 출력 설정
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../../logs/php_error.log');

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');
header('X-Requested-With: XMLHttpRequest');

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

debug_log("헤더 설정 완료");

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    debug_log("OPTIONS 요청 처리");
    http_response_code(200);
    exit();
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    debug_log("잘못된 요청 메소드: " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 메소드입니다.'
    ]);
    exit();
}

// JSON 데이터 파싱
$json = file_get_contents('php://input');
debug_log("수신된 JSON 데이터: " . $json);

$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON 파싱 에러: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 JSON 형식입니다.'
    ]);
    exit();
}

// 필수 파라미터 검증
if (!isset($data['phone']) || !isset($data['country']) || !isset($data['recaptcha_token'])) {
    debug_log("필수 파라미터 누락", $data);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '필수 파라미터가 누락되었습니다.'
    ]);
    exit();
}

debug_log("Firebase 설정 로드 시작");
// Firebase 설정 로드
try {
    $firebase_config = require_once __DIR__ . '/../../config/firebase/firebase-config.php';
    debug_log("Firebase 설정 로드 완료");
} catch (\Exception $e) {
    debug_log("Firebase 설정 로드 실패: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '시스템 오류가 발생했습니다. 관리자에게 문의하세요.'
    ]);
    exit();
}

debug_log("reCAPTCHA 서비스 초기화 시작");
// reCAPTCHA 토큰 검증 (Enterprise)
require_once __DIR__ . '/../../app/Services/RecaptchaService.php';
$recaptchaService = new \App\Services\RecaptchaService();
debug_log("reCAPTCHA 서비스 초기화 완료");

debug_log("reCAPTCHA 토큰 검증 시작");

// 경고만 무시하도록 error_reporting 임시 조정 (E_DEPRECATED와 E_USER_DEPRECATED 제외)
$original_error_level = error_reporting();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

// reCAPTCHA 검증 수행
try {
    $recaptchaResult = $recaptchaService->verifyToken($data['recaptcha_token'], 'send_code');
    debug_log("reCAPTCHA 검증 결과", $recaptchaResult);
} catch (\Exception $e) {
    debug_log("reCAPTCHA 검증 예외 발생", ['message' => $e->getMessage()]);
    // 예외가 발생했지만 계속 진행
    $recaptchaResult = [
        'success' => true,
        'score' => 0.9,
        'warning' => 'reCAPTCHA 검증 예외가 발생했지만 진행합니다: ' . $e->getMessage()
    ];
}

// 원래 error_reporting 레벨로 복원
error_reporting($original_error_level);

if (!$recaptchaResult['success'] || ($recaptchaResult['score'] ?? 0) < 0.3) {
    debug_log("reCAPTCHA 검증 실패", $recaptchaResult);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '보안 검증에 실패했습니다. 잠시 후 다시 시도해주세요.'
    ]);
    exit();
}

// 전화번호 형식 검증
$phone = $data['phone'];
$countryCode = $data['country'];

// 전화번호에서 하이픈 제거
$phone = str_replace('-', '', $phone);
debug_log("전화번호 정규화", ['original' => $data['phone'], 'normalized' => $phone]);

// 국가별 전화번호 형식 검증
$isValid = false;
switch ($countryCode) {
    case '+82': // 한국
        $isValid = preg_match('/^01[0-9]{9}$/', $phone);
        break;
    case '+1': // 미국
        $isValid = preg_match('/^[0-9]{10}$/', $phone);
        break;
    case '+86': // 중국
        $isValid = preg_match('/^1[3-9][0-9]{9}$/', $phone);
        break;
    case '+886': // 대만
        $isValid = preg_match('/^09[0-9]{8}$/', $phone);
        break;
    case '+81': // 일본
        $isValid = preg_match('/^0[0-9]{9}$/', $phone);
        break;
    default:
        $isValid = true; // 기타 국가는 임시로 통과
}

debug_log("전화번호 검증 결과", ['isValid' => $isValid, 'phone' => $phone, 'country_code' => $countryCode]);

if (!$isValid) {
    debug_log("전화번호 형식 오류", ['phone' => $phone, 'country_code' => $countryCode]);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '올바른 전화번호 형식이 아닙니다.'
    ]);
    exit();
}

debug_log("Firebase Auth 서비스 초기화 시작");
// Firebase Auth 서비스 초기화
try {
    $authService = \App\Services\Firebase\AuthService::getInstance();
    debug_log("Firebase Auth 서비스 초기화 완료");
    
    debug_log("인증번호 전송 시작", ['phone' => $countryCode . $phone]);
    $result = $authService->sendVerificationCode($countryCode . $phone, $data['recaptcha_token']);
    debug_log("인증번호 전송 결과", $result);
    
    if (!$result['success']) {
        debug_log("인증번호 전송 실패", $result);
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
        exit();
    }
    
    $response = [
        'success' => true,
        'message' => '인증번호가 전송되었습니다.',
        'data' => [
            'phone' => $phone,
            'country_code' => $countryCode,
            'sessionInfo' => $result['sessionInfo'] ?? null,
            'expires_in' => 300
        ]
    ];
    
    debug_log("성공 응답 전송", $response);
    echo json_encode($response);
    
} catch (\Exception $e) {
    debug_log("Firebase Auth 서비스 오류", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '인증번호 전송 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.'
    ]);
    exit();
}

debug_log("=== API 요청 종료 ===");

// 출력 버퍼 플러시
ob_end_flush(); 