<?php
/**
 * Firebase 사용자 삭제 스크립트
 * 이 스크립트는 Firebase에서 사용자를 삭제하여 차단 상태를 초기화하려고 시도합니다.
 */

// 오류 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Composer 오토로더 로드
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Firebase Auth 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// 삭제할 전화번호
$phoneNumber = '010-1234-1234'; // 여기에 차단된 번호 입력

try {
    echo "Firebase 사용자 삭제/초기화 시도 시작...\n";
    
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // 전화번호 정규화
    $normalizedPhone = $authService->formatPhoneNumber($phoneNumber);
    
    echo "전화번호: {$phoneNumber} (정규화: {$normalizedPhone})\n";
    
    // 대안: 새로운 인증 세션 생성 시도
    echo "\n대안: 새로운 인증 세션 생성 시도...\n";
    
    // Firebase 설정 로드
    $firebase_config = require __DIR__ . '/config/firebase/firebase-config.php';
    $apiKey = $firebase_config['auth']['apiKey'];
    
    echo "Firebase API Key: " . substr($apiKey, 0, 5) . "...\n";
    
    // Guzzle 클라이언트 생성
    $client = new \GuzzleHttp\Client([
        'timeout' => 30.0,
        'connect_timeout' => 10.0,
        'verify' => false
    ]);
    
    try {
        // 인증번호 전송 API 직접 호출
        $response = $client->post("https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}", [
            'json' => ['phoneNumber' => $normalizedPhone],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
        
        $statusCode = $response->getStatusCode();
        $responseBody = $response->getBody()->getContents();
        
        echo "Firebase API 응답 코드: {$statusCode}\n";
        echo "API 응답: " . substr($responseBody, 0, 100) . "...\n";
        
        if ($statusCode === 200) {
            echo "새 인증 세션 생성에 성공했습니다. 차단이 해제되었을 가능성이 높습니다.\n";
            
            // 추가: Firestore에서 차단 기록 삭제 시도
            echo "\nFirestore에서 차단 기록 삭제 시도...\n";
            
            // 인증 시도 기록 초기화
            $authService->resetFailedAttempts($normalizedPhone);
            echo "인증 시도 기록 초기화 완료\n";
        } else {
            echo "새 인증 세션 생성 실패. 상태 코드: {$statusCode}\n";
        }
    } catch (\Exception $e) {
        echo "API 호출 오류: " . $e->getMessage() . "\n";
    }
} catch (\Exception $e) {
    echo "실행 오류: " . $e->getMessage() . "\n";
}

echo "\n참고: Firebase Authentication의 차단은 일반적으로 자동으로 24시간 후 해제됩니다.\n";
echo "위 방법이 실패한 경우, 차단 해제를 기다리거나 다른 전화번호로 테스트하는 것이 좋습니다.\n"; 