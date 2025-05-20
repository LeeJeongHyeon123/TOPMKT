<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Services/RecaptchaService.php';

use App\Services\RecaptchaService;

header('Content-Type: application/json; charset=utf-8');

try {
    // reCAPTCHA 토큰 검증
    if (!isset($_POST['recaptcha_token'])) {
        throw new Exception('보안 검증이 필요합니다.');
    }

    $recaptchaService = new RecaptchaService();
    $recaptchaResult = $recaptchaService->verifyToken($_POST['recaptcha_token'], 'PHONE_VERIFICATION');

    if (!$recaptchaResult['success']) {
        throw new Exception($recaptchaResult['error'] ?? '보안 검증에 실패했습니다.');
    }

    // 위험 점수 확인 (0.7 미만이면 추가 검증 필요)
    if ($recaptchaResult['score'] < 0.7) {
        throw new Exception('보안 검증에 실패했습니다. 잠시 후 다시 시도해주세요.');
    }

    // 전화번호 검증
    $phone = $_POST['phone'] ?? '';
    if (empty($phone)) {
        throw new Exception('전화번호를 입력해주세요.');
    }

    // 인증번호 생성 및 저장 로직
    $verificationCode = sprintf("%06d", mt_rand(0, 999999));
    
    // DB에 인증번호 저장
    $stmt = $pdo->prepare("INSERT INTO phone_verifications (phone, code, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$phone, $verificationCode]);

    // SMS 발송 로직 (실제 구현 필요)
    // sendSMS($phone, "인증번호: " . $verificationCode);

    echo json_encode([
        'success' => true,
        'message' => '인증번호가 전송되었습니다.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 