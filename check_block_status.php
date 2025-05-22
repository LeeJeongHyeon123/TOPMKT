<?php
/**
 * 특정 전화번호의 차단 상태 확인 스크립트
 */

// Composer 오토로더 로드
require_once __DIR__ . '/vendor/autoload.php';

// Firebase 인증 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// Firebase Firestore 레포지토리 로드
require_once __DIR__ . '/app/Repositories/Firebase/FirestoreRepository.php';

// 전화번호 설정
$phoneNumber = '+82-010-1234-1234'; // 국가 코드 포함
$normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

try {
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // Firestore 레포지토리 인스턴스 생성
    $authAttemptsRepository = new \App\Repositories\Firebase\FirestoreRepository('auth_attempts');
    
    // 인증 시도 기록 조회
    $attempts = $authAttemptsRepository->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
    
    // 차단 상태 확인
    $attempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "전화번호 $normalizedPhone 상태 확인:\n";
    echo "실패 횟수: " . $attempts['failedCount'] . "\n";
    echo "남은 시도 횟수: " . $attempts['remainingAttempts'] . "\n";
    echo "차단 여부: " . ($attempts['isBlocked'] ? '차단됨' : '차단되지 않음') . "\n";
    
    if ($attempts['isBlocked']) {
        $blockedUntil = $attempts['blockedUntil'];
        $now = time();
        $remainingTime = $blockedUntil - $now;
        $hours = floor($remainingTime / 3600);
        $minutes = floor(($remainingTime % 3600) / 60);
        
        echo "차단 해제까지 남은 시간: {$hours}시간 {$minutes}분\n";
    }
    
    // 모든 인증 시도 기록 출력
    echo "\n모든 인증 시도 기록:\n";
    echo "--------------------------------\n";
    
    $attempts = $authAttemptsRepository->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
    
    if (empty($attempts)) {
        echo "인증 시도 기록이 없습니다.\n";
    } else {
        foreach ($attempts as $index => $attempt) {
            echo "기록 #" . ($index + 1) . ":\n";
            echo "  타임스탬프: " . date('Y-m-d H:i:s', $attempt['timestamp']) . "\n";
            echo "  성공 여부: " . ($attempt['success'] ? '성공' : '실패') . "\n";
            echo "  액션 타입: " . ($attempt['action'] ?? '알 수 없음') . "\n";
            
            if (isset($attempt['blocked_until'])) {
                echo "  차단 기한: " . date('Y-m-d H:i:s', $attempt['blocked_until']) . "\n";
            }
            
            if (isset($attempt['reset_timestamp'])) {
                echo "  초기화 시간: " . date('Y-m-d H:i:s', $attempt['reset_timestamp']) . "\n";
            }
            
            echo "--------------------------------\n";
        }
    }
    
    // 차단 레코드 삭제 로직 추가
    if ($attempts['isBlocked']) {
        echo "\n차단 기록 삭제 중...\n";
        
        $blockRecords = $authAttemptsRepository->getDocumentsWhere([
            ['phone', '==', $normalizedPhone],
            ['blocked_until', '>', time()]
        ], 10);
        
        if (!empty($blockRecords)) {
            foreach ($blockRecords as $record) {
                if (isset($record['id'])) {
                    $authAttemptsRepository->deleteDocument($record['id']);
                    echo "차단 기록 ID: " . $record['id'] . " 삭제됨\n";
                }
            }
            echo "차단 기록이 삭제되었습니다.\n";
        } else {
            echo "삭제할 차단 기록을 찾을 수 없습니다.\n";
        }
        
        // 실패 횟수 초기화
        $authService->resetFailedAttempts($normalizedPhone);
        echo "실패 횟수가 초기화되었습니다.\n";
        
        // 성공 표시 추가 저장 (성공 기록으로 초기화)
        $authService->logAuthAttempt($normalizedPhone, true, 'reset_attempts');
        echo "성공 기록이 추가되었습니다.\n";
        
        // 초기화 후 상태 확인
        $afterAttempts = $authService->getVerificationAttempts($normalizedPhone);
        echo "\n초기화 후 상태:\n";
        echo "실패 횟수: " . $afterAttempts['failedCount'] . "\n";
        echo "남은 시도 횟수: " . $afterAttempts['remainingAttempts'] . "\n";
        echo "차단 여부: " . ($afterAttempts['isBlocked'] ? '차단됨' : '차단되지 않음') . "\n";
    }
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 