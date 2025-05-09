<?php
// CORS 설정
header('Access-Control-Allow-Origin: https://www.topmktx.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// CSP 설정
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*.google.com https://*.gstatic.com https://www.recaptcha.net; frame-src 'self' https://*.google.com https://recaptcha.google.com https://www.recaptcha.net; style-src 'self' 'unsafe-inline' https://*.google.com https://*.gstatic.com; connect-src 'self' https://*.google.com https://recaptcha.google.com https://www.recaptcha.net https://identitytoolkit.googleapis.com; img-src 'self' data: https://*.google.com https://*.gstatic.com;");

// 세션 시작
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_domain', '.topmktx.com');
session_start();

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 0); // 에러 출력 비활성화
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log'); // Apache 에러 로그 파일로 변경

// 디버깅을 위한 로그 함수
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " [REGISTER] " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message);
}

debug_log("=== 회원가입 요청 시작 ===");
debug_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
debug_log("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
debug_log("HTTP_USER_AGENT: " . $_SERVER['HTTP_USER_AGENT']);
debug_log("REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR']);
debug_log("세션 ID: " . session_id());
debug_log("세션 정보: " . print_r($_SESSION, true));
debug_log("쿠키 정보: " . print_r($_COOKIE, true));

require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/Firebase/AuthService.php';

use App\Services\Firebase\AuthService;

// 데이터베이스 연결
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}",
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    error_log('데이터베이스 연결 오류: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '서버 오류가 발생했습니다.']);
    exit;
}

try {
    // JSON 요청 데이터 파싱
    $json = file_get_contents('php://input');
    debug_log("수신된 JSON 데이터", $json);

    $data = json_decode($json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('잘못된 JSON 형식입니다. 오류: ' . json_last_error_msg());
    }
    debug_log("파싱된 데이터", $data);

    // 세션 정보 로깅
    debug_log("=== 세션 검증 시작 ===");
    debug_log("세션 ID: " . session_id());
    debug_log("세션 데이터: " . json_encode($_SESSION));
    debug_log("요청 데이터: " . json_encode($data));

    // 세션 검증
    if (!isset($_SESSION['verification_id']) || !isset($_SESSION['phone'])) {
        debug_log("세션 검증 실패: verification_id 또는 phone 없음");
        debug_log("verification_id 존재: " . (isset($_SESSION['verification_id']) ? 'true' : 'false'));
        debug_log("phone 존재: " . (isset($_SESSION['phone']) ? 'true' : 'false'));
        throw new Exception('인증 세션이 만료되었습니다. 다시 시도해주세요.');
    }

    // AuthService의 formatPhoneNumber 메서드 사용
    $authService = AuthService::getInstance();
    $sessionPhone = $authService->formatPhoneNumber($_SESSION['phone']);
    $requestPhone = $authService->formatPhoneNumber($data['phone']);

    debug_log("전화번호 정규화 결과:", [
        'session_phone_original' => $_SESSION['phone'],
        'session_phone_normalized' => $sessionPhone,
        'request_phone_original' => $data['phone'],
        'request_phone_normalized' => $requestPhone
    ]);

    if ($sessionPhone !== $requestPhone) {
        debug_log("세션 검증 실패: 전화번호 불일치", [
            'session_phone' => $sessionPhone,
            'request_phone' => $requestPhone
        ]);
        throw new Exception('인증 정보가 일치하지 않습니다. 다시 시도해주세요.');
    }
    debug_log("=== 세션 검증 완료 ===");

    // 필수 필드 검증
    $requiredFields = ['phone', 'code', 'nickname'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            debug_log("필수 필드 누락: {$field}");
            throw new Exception("필수 필드가 누락되었습니다: {$field}");
        }
    }
    debug_log("필수 필드 검증 통과");

    // 닉네임 길이 검증
    if (mb_strlen($data['nickname']) < 2 || mb_strlen($data['nickname']) > 20) {
        debug_log("닉네임 길이 오류: " . mb_strlen($data['nickname']));
        throw new Exception('닉네임은 2~20자 사이여야 합니다.');
    }
    debug_log("닉네임 길이 검증 통과");

    // 닉네임 중복 검사
    $stmt = $pdo->prepare("SELECT id FROM users WHERE nickname = ?");
    $stmt->execute([$data['nickname']]);
    if ($stmt->fetch()) {
        debug_log("닉네임 중복: " . $data['nickname']);
        throw new Exception('이미 사용 중인 닉네임입니다.');
    }
    debug_log("닉네임 중복 검사 통과");

    // 인증번호 확인
    debug_log("=== 인증번호 확인 시작 ===");
    debug_log("인증 요청 데이터:", [
        'phone' => $data['phone'],
        'code' => $data['code'],
        'session_verification_id' => $_SESSION['verification_id']
    ]);
    
    $verifyResult = $authService->verifyCode($data['phone'], $data['code']);
    debug_log("인증번호 확인 결과: " . json_encode($verifyResult));
    
    if (!$verifyResult['success']) {
        debug_log("인증번호 확인 실패: " . ($verifyResult['message'] ?? '알 수 없는 오류'));
        throw new Exception($verifyResult['message'] ?? '인증에 실패했습니다. 인증번호를 확인해주세요.');
    }
    debug_log("=== 인증번호 확인 완료 ===");

    // 사용자 생성
    debug_log("=== Firebase 사용자 생성 시작 ===");
    debug_log("사용자 생성 데이터:", [
        'phone' => $data['phone'],
        'nickname' => $data['nickname']
    ]);
    
    $registerResult = $authService->createUser($data['phone'], $data['nickname']);
    debug_log("Firebase 사용자 생성 결과: " . json_encode($registerResult));
    
    if (!$registerResult['success']) {
        debug_log("Firebase 사용자 생성 실패: " . ($registerResult['message'] ?? '알 수 없는 오류'));
        throw new Exception($registerResult['message'] ?? '사용자 생성에 실패했습니다.');
    }
    debug_log("=== Firebase 사용자 생성 완료 ===");

    // DB에 사용자 정보 저장
    debug_log("=== DB 사용자 정보 저장 시작 ===");
    debug_log("저장할 사용자 정보:", [
        'phone_number' => $data['phone'],
        'nickname' => $data['nickname'],
        'firebase_uid' => $registerResult['uid']
    ]);
    
    // 사용자 ID 생성 (UUIDv4)
    $userId = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    debug_log("생성된 사용자 ID: " . $userId);
    
    $stmt = $pdo->prepare("INSERT INTO users (id, firebase_uid, phone_number, nickname, position, country, created_at) VALUES (?, ?, ?, ?, 'leader', 'KR', NOW())");
    $stmt->execute([
        $userId,
        $registerResult['uid'],
        $data['phone'],
        $data['nickname']
    ]);
    debug_log("=== DB 사용자 정보 저장 완료 ===");

    // 세션 정리
    debug_log("=== 세션 정리 시작 ===");
    debug_log("정리 전 세션 데이터: " . json_encode($_SESSION));
    unset($_SESSION['verification_id']);
    unset($_SESSION['phone']);
    unset($_SESSION['verification_time']);
    debug_log("정리 후 세션 데이터: " . json_encode($_SESSION));
    debug_log("=== 세션 정리 완료 ===");

    // 성공 응답
    $response = [
        'success' => true,
        'message' => '회원가입이 완료되었습니다.',
        'data' => [
            'idToken' => $verifyResult['idToken'],
            'refreshToken' => $verifyResult['refreshToken'],
            'phoneNumber' => $data['phone'],
            'nickname' => $data['nickname']
        ]
    ];
    debug_log("=== 성공 응답 전송 ===");
    debug_log("응답 데이터: " . json_encode($response));
    echo json_encode($response);

} catch (Exception $e) {
    debug_log("=== 일반 오류 발생 ===");
    debug_log("오류 메시지: " . $e->getMessage());
    debug_log("오류 파일: " . $e->getFile());
    debug_log("오류 라인: " . $e->getLine());
    debug_log("스택 트레이스: " . $e->getTraceAsString());
    debug_log("현재 세션 데이터: " . json_encode($_SESSION));
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} catch (Error $e) {
    debug_log("=== 치명적 오류 발생 ===");
    debug_log("오류 메시지: " . $e->getMessage());
    debug_log("오류 파일: " . $e->getFile());
    debug_log("오류 라인: " . $e->getLine());
    debug_log("스택 트레이스: " . $e->getTraceAsString());
    debug_log("현재 세션 데이터: " . json_encode($_SESSION));
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => '서버 오류가 발생했습니다.'
    ]);
} finally {
    debug_log("=== 회원가입 요청 종료 ===");
} 