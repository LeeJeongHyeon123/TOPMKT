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
    $authConfig = $GLOBALS['firebase_config']['auth'];
    $isTestPhone = ($data['phone'] === $authConfig['phone_verification']['test_phone']);
    
    error_log("인증번호 검증: " . $verificationCode);
    
    // 테스트 전화번호인 경우 테스트 코드와 비교
    if ($isTestPhone) {
        $isValidCode = ($verificationCode === $authConfig['phone_verification']['test_code']);
        error_log("테스트 전화번호 인증 검증 결과: " . ($isValidCode ? '성공' : '실패'));
        
        if (!$isValidCode) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => '인증번호가 일치하지 않습니다.']);
            exit;
        }
    } else {
        // 실제 구현 시 Firebase Authentication 연동 필요
        // 현재는 테스트를 위해 항상 성공으로 처리
        error_log("일반 전화번호 인증은 현재 테스트 모드로 항상 성공 처리됨");
    }
    
    // 인증 성공 시 토큰 생성
    $verifyResult = [
        'success' => true,
        'idToken' => 'test_id_token_' . time(),
        'refreshToken' => 'test_refresh_token_' . time(),
        'expiresIn' => 3600,
        'localId' => $user['firebase_uid'],
        'phoneNumber' => $data['phone']
    ];
    
    // 로그인 시간 업데이트
    $stmt = $pdo->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = ? WHERE id = ?");
    $stmt->execute([$_SERVER['REMOTE_ADDR'], $user['id']]);
    
    // 성공 응답
    echo json_encode([
        'success' => true,
        'message' => '로그인에 성공했습니다.',
        'data' => [
            'idToken' => $verifyResult['idToken'],
            'refreshToken' => $verifyResult['refreshToken'],
            'phoneNumber' => $data['phone'],
            'nickname' => $user['nickname']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("로그인 오류: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 