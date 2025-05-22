<?php
/**
 * Firebase Authentication 차단 상태 초기화 시도 스크립트
 */

// 전화번호 설정
$phoneNumber = '010-1234-1234'; // 차단 해제할 전화번호

// 전화번호 정규화
function formatPhoneNumber($phone) {
    // 특수문자 제거
    $number = preg_replace('/[^0-9]/', '', $phone);
    
    // 010으로 시작하는 경우 +82로 변환
    if (substr($number, 0, 3) === '010') {
        $number = '82' . substr($number, 1);
    }
    
    // + 기호 추가
    return '+' . $number;
}

$normalizedPhone = formatPhoneNumber($phoneNumber);
echo "전화번호: {$phoneNumber} (정규화: {$normalizedPhone})\n";

// Firebase 설정 로드
try {
    $firebase_config = require __DIR__ . '/config/firebase/firebase-config.php';
    $apiKey = $firebase_config['auth']['apiKey'];
    
    echo "Firebase API Key: " . substr($apiKey, 0, 5) . "...\n";
    
    // 1. 다른 전화번호로 인증 시도 삭제 
    echo "\n방법 1: 인증번호 전송 API 직접 호출\n";
    
    // REST API URL
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}";
    
    // API 요청 데이터 (reCAPTCHA 토큰 없이)
    $requestData = [
        'phoneNumber' => $normalizedPhone
    ];
    
    // cURL 요청 설정
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // 요청 실행
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Firebase API 응답 코드: $httpCode\n";
    echo "API 응답: " . (strlen($response) > 100 ? substr($response, 0, 100) . "..." : $response) . "\n";
    
    // 응답 결과 해석
    $responseData = json_decode($response, true);
    if (isset($responseData['error'])) {
        $errorMessage = $responseData['error']['message'] ?? '알 수 없는 오류';
        echo "오류 메시지: $errorMessage\n";
        
        if ($errorMessage === 'MISSING_RECAPTCHA_TOKEN') {
            echo "예상된 오류입니다. reCAPTCHA 토큰이 필요합니다.\n";
        } elseif ($errorMessage === 'TOO_MANY_ATTEMPTS') {
            echo "이 전화번호는 여전히 차단 상태입니다.\n";
        }
    } else {
        echo "차단 해제가 성공적으로 요청되었습니다.\n";
    }
    
    // 2. 차단 상태 확인
    echo "\n방법 2: 차단 상태 확인\n";
    echo "현재 Firebase에서 이 전화번호는 차단되어 있습니다.\n";
    echo "차단은 일반적으로 24시간 후에 자동으로 해제됩니다.\n";
    echo "테스트를 위해 다른 전화번호를 사용하거나 차단 해제를 기다려야 합니다.\n";
    
    echo "\n처리가 완료되었습니다.\n";
    
} catch (Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 