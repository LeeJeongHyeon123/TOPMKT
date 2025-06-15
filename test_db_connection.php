<?php
/**
 * 데이터베이스 연결 테스트 및 백업 가능 여부 확인
 */

// 경로 설정
define('ROOT_PATH', '/workspace');
define('SRC_PATH', ROOT_PATH . '/src');

// 데이터베이스 설정 파일 로드
require_once SRC_PATH . '/config/database.php';

echo "=== 데이터베이스 연결 테스트 ===\n";

try {
    // Database 클래스 인스턴스 가져오기
    $db = Database::getInstance();
    echo "✅ 데이터베이스 연결 성공!\n\n";
    
    // 데이터베이스 정보 확인
    echo "=== 데이터베이스 정보 ===\n";
    
    // 현재 데이터베이스 확인
    $stmt = $db->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "현재 데이터베이스: " . $result['current_db'] . "\n";
    
    // 테이블 목록 확인
    echo "\n=== 테이블 목록 ===\n";
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        // 각 테이블의 행 수 확인
        $countStmt = $db->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countStmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "- $table: $count 행\n";
    }
    
    // 데이터베이스 크기 확인
    echo "\n=== 데이터베이스 크기 ===\n";
    $stmt = $db->query("
        SELECT 
            table_schema AS 'Database',
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
        FROM information_schema.tables 
        WHERE table_schema = DATABASE()
        GROUP BY table_schema
    ");
    $size = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "데이터베이스 크기: " . $size['Size (MB)'] . " MB\n";
    
    // 사용자 정보 확인
    echo "\n=== 데이터베이스 사용자 정보 ===\n";
    $stmt = $db->query("SELECT USER() as user, CURRENT_USER() as current_user");
    $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "접속 사용자: " . $userInfo['user'] . "\n";
    echo "현재 사용자: " . $userInfo['current_user'] . "\n";
    
    // 백업 권한 확인
    echo "\n=== 백업 권한 확인 ===\n";
    try {
        $stmt = $db->query("SHOW GRANTS FOR CURRENT_USER()");
        $grants = $stmt->fetchAll(PDO::FETCH_COLUMN);
        foreach ($grants as $grant) {
            echo $grant . "\n";
        }
    } catch (Exception $e) {
        echo "권한 확인 실패: " . $e->getMessage() . "\n";
    }
    
    echo "\n✅ 데이터베이스 접근 가능합니다!\n";
    echo "💡 백업을 진행하려면 mysqldump 명령어나 PHP를 통한 백업이 필요합니다.\n";
    
} catch (Exception $e) {
    echo "❌ 데이터베이스 연결 실패: " . $e->getMessage() . "\n";
    echo "\n디버그 정보:\n";
    echo "- 호스트: " . (defined('DB_HOST') ? DB_HOST : 'localhost') . "\n";
    echo "- 데이터베이스: " . (defined('DB_NAME') ? DB_NAME : 'topmkt') . "\n";
    echo "- 사용자: " . (defined('DB_USERNAME') ? DB_USERNAME : 'root') . "\n";
}