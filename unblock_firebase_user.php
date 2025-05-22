<?php
/**
 * Firebase 차단된 사용자 해제 스크립트
 */

// Composer 오토로더 로드
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Firebase Auth 서비스 로드
require_once __DIR__ . '/app/Services/Firebase/AuthService.php';

// Firestore Repository 로드
require_once __DIR__ . '/app/Repositories/Firebase/FirestoreRepository.php';

// 사용할 전화번호 (차단 해제할 번호)
$phoneNumber = '010-1234-1234'; // 여기에 확인하려는 번호 입력

try {
    // AuthService 인스턴스 가져오기
    $authService = \App\Services\Firebase\AuthService::getInstance();
    
    // 전화번호 정규화
    $normalizedPhone = $authService->formatPhoneNumber($phoneNumber);
    
    echo "전화번호: {$phoneNumber} (정규화: {$normalizedPhone})\n";
    
    // 인증 시도 횟수 확인
    $attempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "======= 차단 해제 전 상태 =======\n";
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
    
    // Firestore 직접 접근하여 차단 기록 삭제
    $firestoreRepo = new \App\Repositories\Firebase\FirestoreRepository('auth_attempts');
    
    // 1. 차단 데이터가 있는 문서 찾기
    $blockedDocs = $firestoreRepo->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
    
    $removedCount = 0;
    $updatedCount = 0;
    
    foreach ($blockedDocs as $doc) {
        if (isset($doc['blocked_until']) && $doc['blocked_until'] > time()) {
            // 차단 데이터가 있는 문서 수정
            $docId = isset($doc['id']) ? $doc['id'] : null;
            if ($docId) {
                // 차단 기록 제거
                $firestoreRepo->updateDocument($docId, [
                    'blocked_until' => null,
                    'success' => true,
                    'updated_at' => time(),
                    'note' => '관리자에 의해 차단 해제됨'
                ]);
                $updatedCount++;
            }
        }
    }
    
    // 2. 성공 기록 추가 (실패 횟수 초기화용)
    $firestoreRepo->createDocument([
        'phone' => $normalizedPhone,
        'timestamp' => time(),
        'success' => true,
        'action' => 'reset_attempts',
        'reset_timestamp' => time(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'note' => '관리자에 의해 차단 해제 및 초기화됨'
    ]);
    
    echo "\n차단 기록 {$updatedCount}개가 수정되었습니다.\n";
    echo "초기화 기록이 추가되었습니다.\n";
    
    // 3. 다시 확인
    $attempts = $authService->getVerificationAttempts($normalizedPhone);
    
    echo "\n======= 차단 해제 후 상태 =======\n";
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
    
    echo "\n주의: 이 스크립트는 Firestore의 차단 상태만 해제합니다. Firebase Authentication의 차단 상태는 그대로 유지될 수 있습니다.\n";
    echo "약 24시간 후에 Firebase Authentication의 차단은 자동으로 해제됩니다.\n";
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 