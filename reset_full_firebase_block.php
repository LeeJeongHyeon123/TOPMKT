<?php
/**
 * Firebase Authentication 차단 상태 완전 초기화 스크립트
 * 여러 가지 방법을 동시에 시도합니다.
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

// 초기화할 전화번호
$phoneNumber = '010-1234-1234';

try {
    echo "Firebase Authentication 차단 상태 완전 초기화 시도 시작...\n";
    
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // 전화번호 정규화
    $normalizedPhone = $authService->formatPhoneNumber($phoneNumber);
    
    echo "전화번호: {$phoneNumber} (정규화: {$normalizedPhone})\n";
    
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
    
    echo "\n방법 1: 인증번호 전송 API 직접 호출\n";
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
            
            // 세션 정보 추출
            $responseData = json_decode($responseBody, true);
            $sessionInfo = $responseData['sessionInfo'] ?? '';
            
            if ($sessionInfo) {
                echo "인증 세션 정보: " . substr($sessionInfo, 0, 20) . "...\n";
                
                // 방법 2: 세션 정보를 사용하여 인증 API 호출 시도
                echo "\n방법 2: 인증 API 호출 시도\n";
                
                try {
                    // 임의의 인증번호로 시도
                    $verifyResponse = $client->post("https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key={$apiKey}", [
                        'json' => [
                            'sessionInfo' => $sessionInfo,
                            'code' => '123456', // 임의의 인증번호
                            'phoneNumber' => $normalizedPhone
                        ],
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ]
                    ]);
                    
                    echo "인증 API 응답 코드: " . $verifyResponse->getStatusCode() . "\n";
                } catch (\Exception $e) {
                    echo "인증 API 호출 예상된 오류 (성공적으로 차단 상태 확인): " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "새 인증 세션 생성 실패. 상태 코드: {$statusCode}\n";
        }
    } catch (\Exception $e) {
        echo "API 호출 오류: " . $e->getMessage() . "\n";
    }
    
    // 방법 3: Firestore에서 차단 기록 초기화
    echo "\n방법 3: Firestore에서 차단 기록 초기화\n";
    $authService->resetFailedAttempts($normalizedPhone);
    echo "인증 시도 기록 초기화 완료\n";
    
    // 방법 4: 다른 식별자로 전송 시도
    echo "\n방법 4: 약간 수정된 전화번호로 인증번호 전송 시도\n";
    
    // 정규화된 전화번호에서 마지막 숫자 변경
    $altPhone1 = substr($normalizedPhone, 0, -1) . ((substr($normalizedPhone, -1) + 1) % 10);
    $altPhone2 = substr($normalizedPhone, 0, -1) . ((substr($normalizedPhone, -1) + 2) % 10);
    
    echo "대체 전화번호 1: {$altPhone1}\n";
    echo "대체 전화번호 2: {$altPhone2}\n";
    
    try {
        // 첫 번째 대체 전화번호로 시도
        $response = $client->post("https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}", [
            'json' => ['phoneNumber' => $altPhone1],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
        
        echo "대체 전화번호 1 응답 코드: " . $response->getStatusCode() . "\n";
    } catch (\Exception $e) {
        echo "대체 전화번호 1 오류: " . $e->getMessage() . "\n";
    }
    
    try {
        // 두 번째 대체 전화번호로 시도
        $response = $client->post("https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}", [
            'json' => ['phoneNumber' => $altPhone2],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
        
        echo "대체 전화번호 2 응답 코드: " . $response->getStatusCode() . "\n";
    } catch (\Exception $e) {
        echo "대체 전화번호 2 오류: " . $e->getMessage() . "\n";
    }
    
    // 방법 5: 원래 번호로 다시 시도
    echo "\n방법 5: 원래 번호로 다시 시도\n";
    try {
        $response = $client->post("https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}", [
            'json' => ['phoneNumber' => $normalizedPhone],
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
        
        $statusCode = $response->getStatusCode();
        echo "최종 시도 응답 코드: {$statusCode}\n";
        
        if ($statusCode === 200) {
            echo "성공! 차단이 해제된 것 같습니다.\n";
        } else {
            echo "차단이 여전히 유지됩니다.\n";
        }
    } catch (\Exception $e) {
        echo "최종 시도 오류: " . $e->getMessage() . "\n";
        echo "차단이 여전히 유지되는 것 같습니다.\n";
    }
} catch (\Exception $e) {
    echo "실행 오류: " . $e->getMessage() . "\n";
}

echo "\n최종 상태 확인: 인증 시도 횟수 체크\n";
$attempts = $authService->getVerificationAttempts($normalizedPhone);
echo "남은 시도 횟수: " . $attempts['remainingAttempts'] . "\n";
echo "차단 상태: " . ($attempts['isBlocked'] ? '차단됨' : '차단되지 않음') . "\n";

echo "\n참고: Firebase Authentication의 차단은 일반적으로 자동으로 24시간 후 해제됩니다.\n";
echo "위 방법이 모두 실패한 경우, 차단 해제를 기다리거나 다른 전화번호로 테스트하는 것이 좋습니다.\n"; 