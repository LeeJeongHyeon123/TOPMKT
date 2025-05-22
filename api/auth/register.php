<?php
/**
 * 회원가입 API
 * 
 * 사용자 회원가입을 처리하는 API 엔드포인트입니다.
 * 휴대폰 인증 후 사용자 정보를 데이터베이스에 저장합니다.
 * 
 * @version 1.0.0
 * @author TOPMKT Development Team
 */

// 출력 버퍼링 시작
ob_start();

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 에러 출력 설정
ini_set('display_errors', 0); // 프로덕션 환경에서는 오류 출력 비활성화
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 디버깅을 위한 로그 함수
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " [Register] " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ];
    
    debug_log('PHP 에러 발생', $error);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ]);
    exit;
}

// 예외 핸들러 설정
function handleException($exception) {
    $error = [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
    
    debug_log('예외 발생', $error);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ]);
    exit;
}

// 에러 핸들러 등록
set_error_handler('handleError');
set_exception_handler('handleException');

debug_log("=== 회원가입 요청 시작 ===");
debug_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
debug_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);

// POST 요청이 아닌 경우 에러
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// JSON 데이터 파싱
$json = file_get_contents('php://input');
debug_log("수신된 JSON 데이터", $json);

$data = json_decode($json, true);

// JSON 파싱 에러 체크
if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON 파싱 오류: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '잘못된 요청 형식입니다.']);
    exit;
}

// 필수 파라미터 검증
$required_fields = ['phone', 'country_code', 'recaptcha_token', 'action', 'nickname'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        debug_log("필수 필드 누락: {$field}", $data);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "필수 파라미터가 누락되었습니다: {$field}"]);
        exit;
    }
}

// action 값 검증
if ($data['action'] !== 'REGISTER') {
    debug_log("잘못된 action 값: " . $data['action']);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '잘못된 요청입니다.']);
    exit;
}

// reCAPTCHA 토큰 검증
try {
    $recaptcha = new \App\Services\Google\ReCaptchaService();
    $recaptchaResult = $recaptcha->verify($data['recaptcha_token'], $data['action']);
    
    if (!$recaptchaResult['success']) {
        debug_log("reCAPTCHA 검증 실패", $recaptchaResult);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '보안 검증에 실패했습니다.']);
        exit;
    }
} catch (\Exception $e) {
    debug_log("reCAPTCHA 검증 중 오류 발생: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '보안 검증 중 오류가 발생했습니다.']);
    exit;
}

// 전화번호 형식 검증
$phoneNumber = $data['phone'];
$countryCode = $data['country_code'];

// 전화번호에서 하이픈 제거
$phoneNumber = str_replace('-', '', $phoneNumber);

if (!preg_match('/^[0-9]{10,11}$/', $phoneNumber)) {
    debug_log("잘못된 전화번호 형식: " . $phoneNumber);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '올바른 전화번호 형식이 아닙니다.']);
    exit;
}

// 국가 코드 형식 검증
if (!preg_match('/^\+[0-9]{1,4}$/', $countryCode)) {
    debug_log("잘못된 국가 코드 형식: " . $countryCode);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '올바른 국가 코드 형식이 아닙니다.']);
    exit;
}

// 닉네임 형식 검증
if (!preg_match('/^[가-힣a-zA-Z0-9]{2,20}$/', $data['nickname'])) {
    debug_log("잘못된 닉네임 형식: " . $data['nickname']);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '닉네임은 2~20자의 한글, 영문, 숫자만 사용 가능합니다.']);
    exit;
}

// 설정 파일 로드
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/firebase-config.php';

// Firebase Auth 서비스 로드
require_once __DIR__ . '/../../app/Services/Firebase/AuthService.php';

require_once __DIR__ . '/../../includes/functions.php';
$messages = require __DIR__ . '/../../resources/lang/ko/messages.php';

try {
    // 데이터베이스 설정 가져오기
    $db_config = require __DIR__ . '/../../config/database.php';
    
    // DB 연결
    $dsn = "mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname={$db_config['db_name']};charset={$db_config['db_charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_PERSISTENT => true
    ];
    
    debug_log("데이터베이스 연결 시도", [
        'dsn' => $dsn,
        'user' => $db_config['db_user'],
        'charset' => $db_config['db_charset']
    ]);
    
    $pdo = new PDO($dsn, $db_config['db_user'], $db_config['db_pass'], $options);
    debug_log("데이터베이스 연결 성공");
    
    // 전화번호 중복 검사
    $stmt = $pdo->prepare("SELECT id FROM users WHERE phone_number = ?");
    $stmt->execute([$phoneNumber]);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        debug_log("전화번호 중복: " . $phoneNumber);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $messages['auth']['register']['phone_exists']]);
        exit;
    }
    
    // 닉네임 중복 검사
    $stmt = $pdo->prepare("SELECT id FROM users WHERE nickname = ?");
    $stmt->execute([$data['nickname']]);
    $existingNickname = $stmt->fetch();
    
    if ($existingNickname) {
        debug_log("닉네임 중복: " . $data['nickname']);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $messages['auth']['register']['nickname_exists']]);
        exit;
    }
    
    // 인증번호 검증
    debug_log("=== 인증번호 검증 시작 ===");
    debug_log("인증번호: " . $data['code']);
    
    // Firebase Auth 서비스 초기화
    try {
        $authService = \App\Services\Firebase\AuthService::getInstance();
        debug_log("Firebase Auth 서비스 초기화 성공");
        
        // 인증번호 전송 가능 여부 확인
        $canSend = $authService->canSendVerificationCode($countryCode . $phoneNumber);
        if (!$canSend['allowed']) {
            debug_log("인증번호 전송 제한", $canSend);
            http_response_code(429);
            echo json_encode([
                'success' => false,
                'message' => $canSend['message'],
                'remainingTime' => $canSend['remainingTime']
            ]);
            exit;
        }
        
        // 인증번호 전송
        $result = $authService->sendVerificationCode($countryCode . $phoneNumber);
        
        if (!$result['success']) {
            debug_log("인증번호 전송 실패", $result);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $messages['auth']['register']['verification_code_send_failed']]);
            exit;
        }
        
        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => $messages['auth']['register']['verification_code_sent'],
            'data' => [
                'phone' => $phoneNumber,
                'country_code' => $countryCode,
                'sessionInfo' => $result['sessionInfo'] ?? null
            ]
        ]);
        
    } catch (\Exception $e) {
        debug_log("Firebase Auth 서비스 오류: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => $messages['auth']['register']['server_error'],
            'error' => [
                'code' => 'AUTH_ERROR',
                'details' => $e->getMessage()
            ]
        ]);
        exit;
    }
    
} catch (\Exception $e) {
    debug_log("인증번호 전송 중 오류 발생: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $messages['auth']['register']['server_error']]);
    exit;
}

debug_log("=== 회원가입 요청 종료 ===");

if (!headers_sent()) {
    echo json_encode(['success' => false, 'message' => '서버 오류: 빈 응답']);
    exit;
} 