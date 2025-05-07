<?php
// Firebase 설정
$firebaseConfig = [
    'apiKey' => "YOUR_API_KEY",
    'authDomain' => "topmkt-832f2.firebaseapp.com",
    'projectId' => "topmkt-832f2",
    'storageBucket' => "topmkt-832f2.appspot.com",
    'messagingSenderId' => "YOUR_MESSAGING_SENDER_ID",
    'appId' => "YOUR_APP_ID"
];

// Firebase Admin SDK 설정
$serviceAccount = json_decode(file_get_contents(__DIR__ . '/../../config/google/service-account.json'), true);

// Firebase Admin SDK 초기화
require_once __DIR__ . '/../../vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount($serviceAccount)
    ->withDatabaseUri('https://topmkt-832f2.firebaseio.com');

$auth = $factory->createAuth();
$database = $factory->createDatabase();
$storage = $factory->createStorage(); 