<?php
// 데이터베이스 설정 파일에서 설정 로드
$config = require __DIR__ . '/config/database.php';

// 데이터베이스 연결
try {
    $conn = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}",
        $config['db_user'],
        $config['db_pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "데이터베이스 연결 성공!\n";
    
    // 닉네임 중복 확인 테스트
    $nickname = '우리집탄이';
    $stmt = $conn->prepare('SELECT COUNT(*) FROM users WHERE nickname = ?');
    $stmt->execute([$nickname]);
    $exists = $stmt->fetchColumn() > 0;
    
    echo "닉네임 '{$nickname}' 중복 여부: " . ($exists ? '중복됨' : '중복되지 않음') . "\n";
    
} catch (PDOException $e) {
    echo "데이터베이스 연결 실패: " . $e->getMessage() . "\n";
} 