<?php
/**
 * React API 연동 테스트 스크립트
 */

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// OPTIONS 요청 처리 (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // 기본 설정 로드
    define('ROOT_PATH', dirname(__DIR__));
    define('SRC_PATH', ROOT_PATH . '/src');
    define('CONFIG_PATH', SRC_PATH . '/config');

    require_once CONFIG_PATH . '/config.php';
    require_once CONFIG_PATH . '/database.php';

    // 데이터베이스 연결 테스트
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT COUNT(*) as user_count FROM users WHERE status = 'active'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'message' => 'API 연결 성공',
        'data' => [
            'database_connected' => true,
            'active_users' => $result['user_count'],
            'server_time' => date('Y-m-d H:i:s'),
            'php_version' => PHP_VERSION
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'API 연결 실패',
        'error' => $e->getMessage()
    ]);
}
?>