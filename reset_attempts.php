<?php
/**
 * 특정 전화번호의 인증 실패 횟수 초기화 스크립트
 */

// Composer 오토로더 로드
require_once __DIR__ . '/vendor/autoload.php';

// Firebase 인증 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// 전화번호 설정
$phoneNumber = '+82-010-1234-1234'; // 국가 코드 포함
$normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

try {
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // 실패 횟수 확인 (초기화 전)
    $beforeAttempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "초기화 전 상태:\n";
    echo "실패 횟수: " . $beforeAttempts['failedCount'] . "\n";
    echo "남은 시도 횟수: " . $beforeAttempts['remainingAttempts'] . "\n";
    echo "차단 여부: " . ($beforeAttempts['isBlocked'] ? '차단됨' : '차단되지 않음') . "\n";
    
    // 초기화 수행
    $authService->resetFailedAttempts($normalizedPhone);
    
    // 성공 표시 추가 저장 (성공 기록으로 초기화)
    $authService->logAuthAttempt($normalizedPhone, true, 'reset_attempts');
    
    // 실패 횟수 다시 확인 (초기화 후)
    $afterAttempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "\n초기화 후 상태:\n";
    echo "실패 횟수: " . $afterAttempts['failedCount'] . "\n";
    echo "남은 시도 횟수: " . $afterAttempts['remainingAttempts'] . "\n";
    echo "차단 여부: " . ($afterAttempts['isBlocked'] ? '차단됨' : '차단되지 않음') . "\n";
    
    echo "\n전화번호 " . $normalizedPhone . "의 인증 실패 횟수가 초기화되었습니다.\n";
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 