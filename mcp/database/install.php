<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Database.php';

try {
    // 데이터베이스 연결
    $db = Database::getInstance();
    
    // SQL 파일 읽기
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    
    // SQL 문을 개별적으로 실행
    $queries = array_filter(
        array_map(
            'trim',
            explode(';', $sql)
        )
    );
    
    // 트랜잭션 시작
    $db->beginTransaction();
    
    // 각 쿼리 실행
    foreach ($queries as $query) {
        if (!empty($query)) {
            $db->query($query);
        }
    }
    
    // 트랜잭션 커밋
    $db->commit();
    
    echo "데이터베이스 테이블이 성공적으로 생성되었습니다.\n";
} catch (Exception $e) {
    // 오류 발생 시 롤백
    if (isset($db)) {
        $db->rollBack();
    }
    echo "오류 발생: " . $e->getMessage() . "\n";
} 