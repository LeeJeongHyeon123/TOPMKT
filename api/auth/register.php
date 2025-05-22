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
$required_fields = ['phone', 'country', 'recaptcha_token', 'nickname', 'idToken'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        debug_log("필수 필드 누락: {$field}", $data);
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "필수 파라미터가 누락되었습니다: {$field}"]);
        exit;
    }
}

// 전화번호 형식 검증
$phoneNumber = $data['phone'];
$countryCode = $data['country'];

// 전화번호에서 하이픈 제거 (E.164의 +는 유지됨)
// $phoneNumber = str_replace('-', '', $phoneNumber);

// E.164 형식 (+로 시작, 숫자 1~15자리) 또는 국내 형식(숫자 10~11자리) 허용
// 클라이언트에서는 E.164 형식으로 통일해서 보내므로, E.164만 체크해도 무방.
// Firebase idToken에 있는 전화번호도 E.164 형식이므로, 이와 일관성을 맞춤.
if (!preg_match('/^\\+[1-9][0-9]{7,14}$/', $phoneNumber)) { // 국가코드 최소 1자리, 번호 최소 7자리 가정
    debug_log("잘못된 전화번호 형식 (E.164 필요): " . $phoneNumber, $data);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '올바른 전화번호 형식(국제표준)이 아닙니다.']);
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

// reCAPTCHA 토큰 검증
try {
    debug_log('reCAPTCHA 검증 시작', ['token' => substr($data['recaptcha_token'], 0, 10) . '...']);
    require_once __DIR__ . '/../../app/Services/RecaptchaService.php';
    
    $recaptchaService = new \App\Services\RecaptchaService();
    $recaptchaResult = $recaptchaService->verifyToken($data['recaptcha_token'], 'register');
    
    debug_log('reCAPTCHA 검증 결과', $recaptchaResult);
    
    if (!$recaptchaResult['success']) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '보안 인증에 실패했습니다.'
        ]);
        exit();
    }
    
    if ($recaptchaResult['score'] < 0.3) {
        debug_log('reCAPTCHA 점수 낮음', ['score' => $recaptchaResult['score']]);
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '보안 검증 점수가 낮습니다. 잠시 후 다시 시도해주세요.'
        ]);
        exit();
    }
} catch (\Exception $e) {
    debug_log("reCAPTCHA 검증 중 오류 발생: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '보안 검증 중 오류가 발생했습니다.']);
    exit;
}

// 설정 파일 로드
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/firebase.php';

// Firebase Auth 서비스 로드
require_once __DIR__ . '/../../app/Services/Firebase/AuthService.php';

require_once __DIR__ . '/../../includes/functions.php';
$messages = require __DIR__ . '/../../resources/lang/ko/messages.php';

try {
    // 데이터베이스 설정 가져오기
    $db_config = require __DIR__ . '/../../config/database.php';
    
    // DB 연결
    $dsn = "mysql:host=localhost;dbname={$db_config['db_name']};charset={$db_config['db_charset']}";
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
    
    // Firebase Auth 서비스 초기화
    try {
        $authService = \App\Services\Firebase\AuthService::getInstance();
        debug_log("Firebase Auth 서비스 초기화 성공");
        
        // idToken 검증
        debug_log("idToken 검증 시작");
        $verifyResult = $authService->verifyIdToken($data['idToken']);
        debug_log("idToken 검증 결과", $verifyResult);
        
        if (!$verifyResult['success']) {
            debug_log("idToken 검증 실패", $verifyResult);
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => '인증 토큰이 유효하지 않습니다.']);
            exit;
        }
        
        // 전화번호 일치 여부 확인
        $tokenPhoneNumber = $verifyResult['phone_number']; // Firebase에서 온 E.164
        // $normalizedPhoneNumber = $countryCode . preg_replace('/[^0-9]/', '', $phoneNumber);
        $requestPhoneNumber = $phoneNumber; // 클라이언트가 보낸 E.164 형식 전화번호를 직접 사용
        
        debug_log("전화번호 비교", ['token' => $tokenPhoneNumber, 'request' => $requestPhoneNumber]);
        
        if ($tokenPhoneNumber !== $requestPhoneNumber) {
            debug_log("전화번호 불일치", ['token' => $tokenPhoneNumber, 'request' => $requestPhoneNumber]);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => '인증된 전화번호와 요청 전화번호가 일치하지 않습니다.']);
            exit;
        }
        
        debug_log("idToken 검증 성공", ['phone' => $tokenPhoneNumber]);
        
        // 사용자 정보 저장
        debug_log("사용자 정보 DB 저장 시작");
        
        // 현재 시간
        $now = date('Y-m-d H:i:s');
        
        // 테이블 존재 여부 확인
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        $usersTableExists = $stmt->rowCount() > 0;
        
        if (!$usersTableExists) {
            debug_log("users 테이블이 존재하지 않음");
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '데이터베이스 테이블이 준비되지 않았습니다.']);
            exit;
        }
        
        // 트랜잭션 시작
        $pdo->beginTransaction();
        
        // 사용자 정보 저장
        $stmt = $pdo->prepare("
            INSERT INTO users 
            (id, firebase_uid, nickname, phone_number, country, language, created_at, updated_at, last_login_at) 
            VALUES (UUID(), ?, ?, ?, SUBSTRING(?, 2, 2), 'ko', ?, ?, NOW())
        ");
        
        debug_log("사용자 정보 저장 쿼리 준비", [
            'uid' => $verifyResult['uid'],
            'nickname' => $data['nickname'],
            'phone' => $phoneNumber,
            'country' => $countryCode
        ]);
        
        $stmt->execute([
            $verifyResult['uid'],
            $data['nickname'],
            $phoneNumber,
            $countryCode,
            $now,
            $now
        ]);
        
        // $userId = $pdo->lastInsertId(); // UUID에는 lastInsertId() 사용 불가
        // firebase_uid로 방금 삽입된 사용자의 id (UUID)를 가져옴
        $stmt_get_id = $pdo->prepare("SELECT id FROM users WHERE firebase_uid = ?");
        $stmt_get_id->execute([$verifyResult['uid']]);
        $insertedUser = $stmt_get_id->fetch();
        $userId = $insertedUser ? $insertedUser['id'] : null;

        if (!$userId) {
            $pdo->rollBack();
            debug_log("사용자 ID 조회 실패 후 롤백", ['firebase_uid' => $verifyResult['uid']]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => '사용자 정보 저장 후 ID 조회에 실패했습니다.']);
            exit;
        }

        debug_log("사용자 정보 저장 성공", ['userId' => $userId]);
        
        // user_profiles 테이블 존재 여부 확인
        $stmt = $pdo->query("SHOW TABLES LIKE 'user_profiles'");
        $profilesTableExists = $stmt->rowCount() > 0;
        
        // user_profiles 테이블이 있는 경우에만 프로필 생성
        if ($profilesTableExists) {
            // 사용자 프로필 생성
            $stmt = $pdo->prepare("
                INSERT INTO user_profiles 
                (user_id, introduction, created_at, updated_at) 
                VALUES (?, '', ?, ?)
            ");
            
            debug_log("사용자 프로필 저장 쿼리 준비", ['userId' => $userId]);
            
            $stmt->execute([
                $userId,
                $now,
                $now
            ]);
            
            debug_log("사용자 프로필 저장 성공");
        } else {
            debug_log("user_profiles 테이블이 존재하지 않아 프로필 생성 건너뜀");
        }
        
        // 트랜잭션 커밋
        $pdo->commit();
        
        debug_log("회원가입 성공", ['user_id' => $userId, 'nickname' => $data['nickname']]);
        
        // 성공 응답
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => '회원가입이 완료되었습니다.',
            'data' => [
                'user_id' => $userId, // UUID string
                'nickname' => $data['nickname'],
                'idToken' => $data['idToken'] // Firebase ID 토큰. 이를 auth_token으로도 사용 가능
            ]
        ]);
        
    } catch (\Exception $e) {
        // 트랜잭션 롤백
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
            debug_log("트랜잭션 롤백");
        }
        
        debug_log("Firebase Auth 서비스 오류", [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => '인증 서비스 오류가 발생했습니다: ' . $e->getMessage()]);
        exit;
    }
    
} catch (\Exception $e) {
    // 트랜잭션 롤백
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
        debug_log("트랜잭션 롤백");
    }
    
    debug_log("회원가입 처리 중 오류 발생", [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $messages['auth']['register']['server_error']]);
    exit;
}

debug_log("=== 회원가입 요청 종료 ===");

// 출력 버퍼 플러시
ob_end_flush(); 