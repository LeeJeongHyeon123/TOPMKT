<?php
require_once __DIR__ . '/../config/firebase/config.php';
require_once __DIR__ . '/../app/Services/Firebase/AuthService.php';

use App\Services\Firebase\AuthService;

try {
    // Firebase 설정 파일 경로 설정
    putenv('FIREBASE_CREDENTIALS=' . __DIR__ . '/../config/firebase/firebase-credentials.json');
    
    $authService = AuthService::getInstance();
    $result = $authService->deleteUser('user_1746800689');
    
    if ($result) {
        echo "Firebase 사용자 삭제 성공\n";
    } else {
        echo "Firebase 사용자 삭제 실패\n";
    }
} catch (Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
    echo "상세 오류: " . $e->getTraceAsString() . "\n";
} 