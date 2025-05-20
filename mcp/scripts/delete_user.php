<?php
require_once __DIR__ . '/../config/firebase/config.php';

// 삭제할 회원의 전화번호
$phoneNumber = '+821012341234'; // 우리집탄이의 전화번호 (국제 형식)

try {
    // 전화번호로 사용자 검색
    $userRecord = $auth->getUserByPhoneNumber($phoneNumber);
    
    if ($userRecord) {
        // 사용자 삭제
        $auth->deleteUser($userRecord->uid);
        echo "회원이 성공적으로 삭제되었습니다.\n";
        
        // 추가 데이터가 있다면 여기서 삭제
        $database->getReference('users/' . $userRecord->uid)->remove();
        echo "회원 데이터가 성공적으로 삭제되었습니다.\n";
    } else {
        echo "해당 전화번호로 등록된 회원이 없습니다.\n";
    }
} catch (Exception $e) {
    echo "오류 발생: " . $e->getMessage() . "\n";
    
    // 오류 상세 정보 출력
    if ($e instanceof \Kreait\Firebase\Exception\AuthException) {
        echo "Firebase Auth 오류: " . $e->getMessage() . "\n";
    } elseif ($e instanceof \Kreait\Firebase\Exception\DatabaseException) {
        echo "Firebase Database 오류: " . $e->getMessage() . "\n";
    }
} 