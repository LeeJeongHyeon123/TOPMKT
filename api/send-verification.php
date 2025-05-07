<?php
// use 구문을 상단으로 이동
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\PhoneNumberExists;

// reCAPTCHA 설정
define('RECAPTCHA_SITE_KEY', '6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb');
define('RECAPTCHA_SECRET_KEY', '6LfViDErAAAAAJYZ6zqP3I6q124NuaUlAGcUWeB5');

// 모든 출력을 버퍼링
ob_start();

// CORS 설정 추가
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');

// OPTIONS 요청에 대한 즉시 응답 (CORS 프리플라이트 요청 처리)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 요청 메소드 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '허용되지 않는 요청 방식입니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 에러 핸들러 설정
function errorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'error' => '시스템 오류가 발생했습니다.',
        'debug' => [
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit;
}

// 에러 핸들러 등록
set_error_handler('errorHandler');

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/topmkt/logs/topmkt_error.log');

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/firebase-config.php';

use App\Services\RecaptchaService;

try {
    // JSON 요청 데이터 파싱
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('잘못된 JSON 형식입니다.');
    }

    // 필수 필드 검증
    if (!isset($data['phone']) || !isset($data['recaptcha_token'])) {
        throw new Exception('필수 정보가 누락되었습니다.');
    }

    // reCAPTCHA 검증
    $recaptchaService = new RecaptchaService();
    $recaptchaResult = $recaptchaService->verifyToken($data['recaptcha_token'], 'PHONE_VERIFICATION');

    if (!$recaptchaResult['success']) {
        throw new Exception($recaptchaResult['error']);
    }

    // Firebase 설정 파일 확인
    $firebase_config_path = __DIR__ . '/../config/firebase-config.php';
    $firebase_credentials_path = __DIR__ . '/../config/firebase-credentials.json';

    if (!file_exists($firebase_config_path)) {
        throw new Exception('Firebase 설정 파일을 찾을 수 없습니다.');
    }

    if (!file_exists($firebase_credentials_path)) {
        throw new Exception('Firebase 인증 파일을 찾을 수 없습니다.');
    }

    require_once $firebase_config_path;

    // Firebase 초기화
    $factory = (new Factory)
        ->withServiceAccount($firebase_credentials_path)
        ->withDatabaseUri(FIREBASE_DATABASE_URL);

    $auth = $factory->createAuth();
    
    // 전화번호 형식 검증
    $phone = $data['phone'];
    if (!preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
        throw new Exception('올바른 전화번호 형식이 아닙니다.');
    }
    
    // Firebase Authentication REST API를 사용하여 전화번호 인증 시작
    $apiKey = FIREBASE_API_KEY;
    $projectId = FIREBASE_PROJECT_ID;
    
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key=" . $apiKey;
    
    $postData = [
        'phoneNumber' => $phone,
        'recaptchaToken' => $data['recaptcha_token']
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($postData)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === false) {
        throw new Exception('Firebase 서버 연결에 실패했습니다.');
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['error'])) {
        throw new Exception($result['error']['message']);
    }
    
    // 버퍼 클리어
    ob_clean();
    
    // 성공 응답
    echo json_encode([
        'success' => true,
        'message' => '인증번호가 전송되었습니다.',
        'sessionInfo' => $result['sessionInfo']
    ], JSON_UNESCAPED_UNICODE);

} catch (PhoneNumberExists $e) {
    // 버퍼 클리어
    ob_clean();
    
    error_log("Phone number already exists: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'error' => '이미 등록된 전화번호입니다.'
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // 버퍼 클리어
    ob_clean();
    
    error_log("Error in send-verification.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'error' => '인증 처리 중 오류가 발생했습니다: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} finally {
    // 출력 버퍼 정리
    ob_end_flush();
} 