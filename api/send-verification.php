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

// 세션 시작
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'None');
ini_set('session.cookie_domain', '.topmktx.com');
session_start();

// CORS 설정
header('Access-Control-Allow-Origin: https://www.topmktx.com');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json; charset=utf-8');

// CSP 설정
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://*.google.com https://*.gstatic.com https://www.recaptcha.net; frame-src 'self' https://*.google.com https://recaptcha.google.com https://www.recaptcha.net; style-src 'self' 'unsafe-inline' https://*.google.com https://*.gstatic.com; connect-src 'self' https://*.google.com https://recaptcha.google.com https://www.recaptcha.net https://identitytoolkit.googleapis.com; img-src 'self' data: https://*.google.com https://*.gstatic.com;");

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// POST 요청이 아닌 경우 에러 반환
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Firebase 설정 파일 포함
$firebaseConfig = require __DIR__ . '/../config/firebase/config.php';

// RecaptchaService 클래스 로드
require_once __DIR__ . '/../app/Services/RecaptchaService.php';

// AuthService 클래스 로드
require_once __DIR__ . '/../app/Services/Firebase/AuthService.php';

// 네임스페이스 사용
use App\Services\RecaptchaService;
use App\Services\Firebase\AuthService;

// 디버그 로깅 함수
function debug_log($message) {
    error_log(print_r($message, true));
}

// 전화번호를 E.164 형식으로 변환
function formatPhoneNumber($phoneNumber) {
    // 이미 E.164 형식인 경우 그대로 반환
    if (preg_match('/^\+[0-9]+$/', $phoneNumber)) {
        return $phoneNumber;
    }
    
    // 특수문자 제거
    $number = preg_replace('/[^0-9]/', '', $phoneNumber);
    
    // 010으로 시작하는 경우 +82로 변환
    if (substr($number, 0, 3) === '010') {
        $number = '82' . substr($number, 1);
    }
    
    // + 기호 추가
    return '+' . $number;
}

try {
    // JSON 데이터 파싱
    $json = file_get_contents('php://input');
    debug_log('Received JSON: ' . $json);
    
    $data = json_decode($json, true);

    if (!$data) {
        throw new Exception('Invalid JSON data');
    }

    // 필수 필드 확인
    if (empty($data['phone']) || empty($data['recaptcha_token'])) {
        throw new Exception('Missing required fields');
    }

    // 전화번호를 E.164 형식으로 변환
    $normalizedPhone = formatPhoneNumber($data['phone']);
    debug_log('Formatted phone number: ' . $normalizedPhone);

    // reCAPTCHA 토큰 검증
    if (!isset($data['recaptcha_token'])) {
        throw new Exception('보안 검증이 필요합니다.');
    }

    $recaptchaService = new RecaptchaService();
    $recaptchaResult = $recaptchaService->verifyToken($data['recaptcha_token'], 'PHONE_VERIFICATION', $normalizedPhone);

    if (!$recaptchaResult['success']) {
        throw new Exception($recaptchaResult['error'] ?? '보안 검증에 실패했습니다.');
    }

    // 위험 점수 확인 (0.5 미만이면 추가 검증 필요)
    if (isset($recaptchaResult['score']) && $recaptchaResult['score'] < 0.5 && !isset($recaptchaResult['test_account']) && !isset($recaptchaResult['dev_bypass'])) {
        throw new Exception('보안 검증에 실패했습니다. 잠시 후 다시 시도해주세요.');
    }

    // Firebase Authentication REST API를 사용하여 인증번호 전송
    $apiKey = $firebaseConfig['auth']['apiKey'];
    debug_log('Using API Key: ' . $apiKey);
    
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}";
    debug_log('Request URL: ' . $url);
    
    $client = new \GuzzleHttp\Client();
    $response = $client->post($url, [
        'json' => [
            'phoneNumber' => $normalizedPhone,
            'recaptchaToken' => $data['recaptcha_token']
        ],
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ],
        'verify' => false // SSL 인증서 검증 비활성화
    ]);
    
    $result = json_decode($response->getBody()->getContents(), true);
    debug_log('Firebase API response: ' . print_r($result, true));
    
    if (isset($result['error'])) {
        throw new Exception($result['error']['message']);
    }
    
    // 세션에 인증 정보 저장
    $_SESSION['verification_id'] = $result['sessionInfo'];
    $_SESSION['phone'] = $normalizedPhone;
    $_SESSION['verification_time'] = time();
    
    error_log('=== 인증번호 전송 성공 ===');
    error_log('세션 ID: ' . session_id());
    error_log('세션에 저장된 데이터: ' . json_encode($_SESSION));
    error_log('Firebase 응답: ' . json_encode($result));
    
    $response = [
        'success' => true,
        'message' => 'Verification code sent successfully',
        'sessionInfo' => [
            'phone' => $normalizedPhone,
            'expires' => time() + 300 // 5분
        ]
    ];
    
    error_log('클라이언트 응답: ' . json_encode($response));
    echo json_encode($response);
    exit();

} catch (Exception $e) {
    error_log('=== 인증번호 전송 실패 ===');
    error_log('오류 메시지: ' . $e->getMessage());
    error_log('오류 발생 위치: ' . $e->getFile() . ':' . $e->getLine());
    error_log('스택 트레이스: ' . $e->getTraceAsString());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit();
}

// 출력 버퍼 플러시
ob_end_flush();