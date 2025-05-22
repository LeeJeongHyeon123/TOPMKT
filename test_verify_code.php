<?php
/**
 * 인증번호 확인 API 테스트 스크립트
 */

// Composer 오토로더 로드
require_once __DIR__ . '/vendor/autoload.php';

// Firebase 인증 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// 테스트 데이터 설정
$phoneNumber = '+8201012341234'; // 테스트 전화번호
$code = '123456'; // 임의의 인증번호 (실제로는 틀린 번호)
$sessionInfo = 'test_session_info'; // 임의의 세션 정보

try {
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    echo "인증번호 확인 테스트 시작\n";
    echo "전화번호: $phoneNumber\n";
    echo "인증번호: $code\n";
    echo "세션 정보: $sessionInfo\n\n";
    
    // 인증번호 확인 시도
    $result = $authService->verifyCode($phoneNumber, $code, $sessionInfo);
    
    echo "인증번호 확인 결과:\n";
    echo "성공 여부: " . ($result['success'] ? '성공' : '실패') . "\n";
    echo "메시지: " . $result['message'] . "\n";
    
    if (isset($result['remainingAttempts'])) {
        echo "남은 시도 횟수: " . $result['remainingAttempts'] . "\n";
    }
    
    if (isset($result['isBlocked']) && $result['isBlocked']) {
        echo "차단 여부: 차단됨\n";
        if (isset($result['blockedUntil'])) {
            echo "차단 해제 시간: " . date('Y-m-d H:i:s', $result['blockedUntil']) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 