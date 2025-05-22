<?php
/**
 * 모든 인증 시도 기록 삭제 스크립트
 */

// Composer 오토로더 로드
require_once __DIR__ . '/vendor/autoload.php';

// Firebase Firestore 레포지토리 로드
require_once __DIR__ . '/app/Repositories/Firebase/FirestoreRepository.php';

try {
    // Firestore 레포지토리 인스턴스 생성
    $authAttemptsRepository = new \App\Repositories\Firebase\FirestoreRepository('auth_attempts');
    
    // 모든 인증 시도 기록 조회
    $allAttempts = $authAttemptsRepository->getAllDocuments(100);
    
    echo "총 " . count($allAttempts) . "개의 인증 시도 기록을 찾았습니다.\n\n";
    
    // 각 기록 삭제
    $deleted = 0;
    foreach ($allAttempts as $attempt) {
        if (isset($attempt['id'])) {
            $authAttemptsRepository->deleteDocument($attempt['id']);
            $deleted++;
            
            // 삭제한 기록 정보 출력
            echo "ID: " . $attempt['id'] . " 삭제됨";
            if (isset($attempt['phone'])) {
                echo " (전화번호: " . $attempt['phone'] . ")";
            }
            echo "\n";
        }
    }
    
    echo "\n총 " . $deleted . "개의 인증 시도 기록을 삭제했습니다.\n";
    
} catch (\Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
} 