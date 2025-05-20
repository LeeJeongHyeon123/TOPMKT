<?php
// 오류 표시 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\RecaptchaService;

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

try {
    // POST 데이터 파싱
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['recaptcha_token'])) {
        throw new Exception('토큰이 제공되지 않았습니다.');
    }

    $recaptchaService = new RecaptchaService();
    $result = $recaptchaService->verifyToken($data['recaptcha_token'], 'PHONE_VERIFICATION');
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} 