<?php
/**
 * Firebase/Firestore 연결 설정 샘플 파일
 * 
 * 실제 사용 시 firebase.php로 복사하고 실제 값으로 수정하세요.
 * firebase.php는 Git에 포함되지 않습니다.
 */

return [
    // Firebase 프로젝트 설정
    'project_id' => 'your-project-id',
    'api_key' => 'your-api-key',
    'auth_domain' => 'your-project-id.firebaseapp.com',
    'database_url' => 'https://your-project-id.firebaseio.com',
    'storage_bucket' => 'your-project-id.appspot.com',
    'messaging_sender_id' => 'your-messaging-sender-id',
    'app_id' => 'your-app-id',
    'measurement_id' => 'your-measurement-id',
    
    // 서비스 계정 인증 정보 파일 경로
    'credentials_file' => __DIR__ . '/firebase-credentials.json',
    
    // 스토리지 설정
    'storage' => [
        'temp_path' => __DIR__ . '/../public/assets/images/temp',
        'cache_path' => __DIR__ . '/../public/assets/images/cache',
        'cache_lifetime' => 86400, // 24시간
    ],
    
    // Firestore 설정
    'firestore' => [
        'collections' => [
            'chats' => 'chats',
            'notifications' => 'notifications',
            'activities' => 'activities',
        ]
    ]
]; 