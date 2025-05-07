<?php
// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/topmkt/logs/topmkt_error.log');

// 디버깅을 위한 로그 함수
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message, 3, '/var/www/html/topmkt/logs/topmkt_error.log');
}

debug_log("=== 회원가입 요청 시작 ===");

// 설정 파일 로드
require_once '../config/config.php';
require_once '../includes/Database.php';

// JSON 요청 데이터 파싱
$raw_data = file_get_contents('php://input');
debug_log("Raw request data", $raw_data);

$data = json_decode($raw_data, true);
debug_log("Parsed request data", $data);

// 필수 필드 검증
if (!isset($data['phone']) || !isset($data['nickname']) || !isset($data['uid'])) {
    debug_log("Missing required fields", $data);
    http_response_code(400);
    echo json_encode(['error' => '필수 정보가 누락되었습니다.']);
    exit;
}

// 소개글 필드 처리 (선택사항)
$introduction = isset($data['introduction']) ? $data['introduction'] : '';

try {
    // 데이터베이스 연결
    debug_log("Attempting database connection");
    $db = Database::getInstance();
    $conn = $db->getConnection();
    debug_log("Database connection successful");
    
    // 닉네임 중복 체크
    debug_log("Checking nickname: " . $data['nickname']);
    $stmt = $conn->prepare("SELECT id FROM users WHERE nickname = ?");
    $stmt->execute([$data['nickname']]);
    if ($stmt->rowCount() > 0) {
        debug_log("Duplicate nickname found: " . $data['nickname']);
        http_response_code(400);
        echo json_encode(['error' => '이미 사용 중인 닉네임입니다.']);
        exit;
    }

    // 휴대폰 번호 중복 체크
    debug_log("Checking phone number: " . $data['phone']);
    $phone = preg_replace('/[^0-9+]/', '', $data['phone']); // 숫자와 + 기호만 남기고 모두 제거
    $stmt = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
    $stmt->execute([$phone]);
    if ($stmt->rowCount() > 0) {
        debug_log("Duplicate phone number found: " . $phone);
        http_response_code(400);
        echo json_encode(['error' => '이미 가입된 휴대폰 번호입니다.']);
        exit;
    }

    // 국가 코드 추출
    $countryCode = 'KR'; // 기본값
    if (strpos($phone, '+82') === 0) {
        $countryCode = 'KR';
    } elseif (strpos($phone, '+1') === 0) {
        $countryCode = 'US';
    } elseif (strpos($phone, '+86') === 0) {
        $countryCode = 'CN';
    } elseif (strpos($phone, '+886') === 0) {
        $countryCode = 'TW';
    } elseif (strpos($phone, '+81') === 0) {
        $countryCode = 'JP';
    }
    debug_log("Country code determined", $countryCode);

    // 사용자 정보 저장
    debug_log("Preparing to insert user data");
    $stmt = $conn->prepare("
        INSERT INTO users (
            id,
            phone_number,
            nickname,
            country,
            position,
            introduction,
            last_login_at,
            last_login_ip,
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, NOW(), NOW())
    ");

    $params = [
        $data['uid'],
        $phone,
        $data['nickname'],
        $countryCode,
        'sales',
        $introduction,
        $_SERVER['REMOTE_ADDR']
    ];
    
    debug_log("Executing insert with params", $params);
    
    $stmt->execute($params);
    $userId = $conn->lastInsertId();
    debug_log("User inserted successfully", ['user_id' => $userId]);

    // 로그인 이력 저장
    debug_log("Recording login history");
    $stmt = $conn->prepare("
        INSERT INTO login_history (
            user_id,
            login_at,
            ip_address,
            user_agent
        ) VALUES (?, NOW(), ?, ?)
    ");
    $stmt->execute([
        $data['uid'], 
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    debug_log("Login history recorded");

    // 세션에 사용자 정보 저장
    debug_log("Starting session");
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['phone'] = $phone;
    $_SESSION['nickname'] = $data['nickname'];
    $_SESSION['country_code'] = $countryCode;
    debug_log("Session data set", $_SESSION);

    debug_log("=== 회원가입 성공 ===");
    echo json_encode(['success' => true, 'message' => '회원가입이 완료되었습니다.']);

} catch (PDOException $e) {
    debug_log("Database Error", [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode(['error' => '서버 오류가 발생했습니다.']);
} catch (Exception $e) {
    debug_log("General Error", [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'trace' => $e->getTraceAsString()
    ]);
    http_response_code(500);
    echo json_encode(['error' => '서버 오류가 발생했습니다.']);
} 