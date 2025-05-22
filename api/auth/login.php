<?php
// 출력 버퍼링 시작
ob_start();

// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// JSON 응답 헤더 설정
header('Content-Type: application/json');
header('X-Requested-With: XMLHttpRequest');

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// 디버깅을 위한 로그 함수
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " [Login] " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ];
    
    debug_log('PHP 에러 발생', [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit();
}

// 예외 핸들러 설정
function handleException($exception) {
    $error = [
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ];
    
    debug_log('예외 발생', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit();
}

// 에러 핸들러 등록
set_error_handler('handleError');
set_exception_handler('handleException');

// 디버깅을 위한 로그
error_log("로그인 요청 수신: api/auth/login.php");

// POST 요청이 아닌 경우 에러
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// JSON 데이터 파싱
$json = file_get_contents('php://input');
error_log("Received JSON data: " . $json);

$data = json_decode($json, true);

// JSON 파싱 에러 체크
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON parsing error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '잘못된 요청 형식입니다.']);
    exit;
}

// 필수 파라미터 검증
if (!isset($data['phone']) || !isset($data['code'])) {
    error_log("Missing required parameters: " . print_r($data, true));
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

// 데이터베이스 설정 로드
require_once __DIR__ . '/../../config/database.php';

// Firebase 설정 로드
require_once __DIR__ . '/../../config/firebase/config.php';

try {
    // 데이터베이스 설정 가져오기
    $db_config = require_once __DIR__ . '/../../config/database.php';
    
    // DB 연결
    $dsn = "mysql:host={$db_config['db_host']};dbname={$db_config['db_name']};charset=utf8mb4;unix_socket=/var/lib/mysql/mysql.sock";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_config['db_user'], $db_config['db_pass'], $options);
    
    // 전화번호로 사용자 조회
    $stmt = $pdo->prepare("SELECT id, firebase_uid, nickname FROM users WHERE phone_number = ?");
    $stmt->execute([$data['phone']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        error_log("사용자를 찾을 수 없음: " . $data['phone']);
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => '등록되지 않은 전화번호입니다. 회원가입을 진행해주세요.']);
        exit;
    }
    
    // 인증번호 검증
    $verificationCode = $data['code'];
    $sessionInfo = $data['sessionInfo'] ?? null;
    
    if (!$sessionInfo) {
        error_log("세션 정보 누락");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '인증 세션이 만료되었습니다. 다시 시도해주세요.']);
        exit;
    }
    
    try {
        $authService = \App\Services\Firebase\AuthService::getInstance();
        $verifyResult = $authService->verifyCode($data['phone'], $verificationCode, $sessionInfo);
        
        if (!$verifyResult['success']) {
            error_log("인증번호 검증 실패: " . $verifyResult['message']);
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => $verifyResult['message']]);
            exit;
        }
        
        // Firebase UID 업데이트
        $stmt = $pdo->prepare("UPDATE users SET firebase_uid = ? WHERE id = ?");
        $stmt->execute([$verifyResult['idToken'], $user['id']]);
        
        // 로그인 성공
        echo json_encode([
            'success' => true,
            'message' => '로그인되었습니다.',
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'nickname' => $user['nickname']
                ],
                'token' => $verifyResult['idToken']
            ]
        ]);
        
    } catch (\Exception $e) {
        error_log("Firebase 인증 오류: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '인증 처리 중 오류가 발생했습니다.']);
        exit;
    }
    
} catch (Exception $e) {
    error_log("로그인 오류: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// 출력 버퍼 플러시
ob_end_flush(); 