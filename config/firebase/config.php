<?php
// Firebase 설정
$firebaseConfig = [
    'apiKey' => "AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ",
    'authDomain' => "topmkt-832f2.firebaseapp.com",
    'projectId' => "topmkt-832f2",
    'storageBucket' => "topmkt-832f2.appspot.com",
    'messagingSenderId' => "856114239779",
    'appId' => "1:856114239779:web:d8dd9049a9723ac8835496"
];

// Firebase Admin SDK 설정
$serviceAccountPath = __DIR__ . '/../google/service-account.json';
if (!file_exists($serviceAccountPath)) {
    error_log("Firebase service account file not found at: " . $serviceAccountPath);
    throw new Exception('Firebase configuration error');
}

$serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
if (!$serviceAccount) {
    error_log("Failed to parse Firebase service account JSON");
    throw new Exception('Firebase configuration error');
}

// Firebase Admin SDK 초기화
require_once __DIR__ . '/../../vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://topmkt-832f2-default-rtdb.firebaseio.com');

$auth = $factory->createAuth();
$database = $factory->createDatabase();
$storage = $factory->createStorage();

return [
    'credentials' => [
        'file' => __DIR__ . '/../google/service-account.json'
    ],
    'database' => [
        'url' => 'https://topmkt-832f2-default-rtdb.firebaseio.com'
    ],
    'storage' => [
        'bucket' => 'topmkt-832f2.firebasestorage.app'
    ],
    'auth' => [
        'domain' => 'topmkt-832f2.firebaseapp.com',
        'apiKey' => 'AIzaSyAlFQNcYxi29uhu5fW1MYy7iESy3GvmnUQ',
        'projectId' => 'topmkt-832f2',
        'messagingSenderId' => '856114239779',
        'appId' => '1:856114239779:web:d8dd9049a9723ac8835496'
    ]
]; 