<?php
/**
 * Firebase/Firestore 연결 설정 파일
 * 
 * Firebase 프로젝트와의 연동을 위한 설정을 정의합니다.
 */

return [
    // 서비스 계정 인증 정보
    'credentials' => [
        'file' => __DIR__ . '/firebase/firebase-credentials.json',
    ],
    
    // Firebase 프로젝트 정보 (Firebase 콘솔에서 확인)
    'project_id' => 'topmkt-832f2', // 실제 Firebase 프로젝트 ID
    
    // Firebase Storage 설정
    'storage' => [
        'bucket' => 'gs://topmkt-832f2.firebasestorage.app', // 업데이트된 Firebase 버킷 이름
        'temp_path' => __DIR__ . '/../public/assets/images/temp',
        'cache_path' => __DIR__ . '/../public/assets/images/cache',
        'cache_lifetime' => 86400, // 24시간 (초 단위)
    ],
    
    // Firestore 컬렉션 설정
    'firestore' => [
        'collections' => [
            'chats' => 'chats',
            'notifications' => 'notifications',
            'activities' => 'activities',
        ]
    ]
]; 