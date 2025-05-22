<?php
/**
 * Firebase 인증 시도 횟수 확인 스크립트
 */

// Composer 오토로더 로드
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Firebase Auth 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// 사용할 전화번호 (테스트 번호)
$phoneNumber = '010-1234-1234'; // 여기에 확인하려는 번호 입력

try {
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // 전화번호 정규화
    $normalizedPhone = $authService->formatPhoneNumber($phoneNumber);
    
    echo "전화번호: {$phoneNumber} (정규화: {$normalizedPhone})\n";
    
    // 인증 시도 횟수 확인
    $attempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "======= 인증 시도 현황 =======\n";
    echo "실패 횟수: {$attempts['failedCount']}\n";
    echo "남은 시도 횟수: {$attempts['remainingAttempts']}\n";
    echo "차단 상태: " . ($attempts['isBlocked'] ? "차단됨" : "차단되지 않음") . "\n";
    
    if ($attempts['isBlocked']) {
        $remainingTime = $attempts['blockedUntil'] - time();
        $hours = floor($remainingTime / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);
        echo "차단 해제까지 남은 시간: {$hours}시간 {$minutes}분\n";
        echo "차단 해제 시간: " . date('Y-m-d H:i:s', $attempts['blockedUntil']) . "\n";
    }
    
    echo "마지막 초기화 시간: " . ($attempts['lastResetTime'] > 0 ? date('Y-m-d H:i:s', $attempts['lastResetTime']) : '없음') . "\n";
    echo "============================\n";
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 