<?php
/**
 * 특정 사용자 프로필 정보 API
 * 
 * 특정 사용자의 프로필 정보를 반환합니다.
 * 
 * 요청 방식: GET
 * 
 * 요청 파라미터:
 * - user_id: 조회할 사용자 ID
 * 
 * 응답:
 * - success: 성공 여부 (boolean)
 * - message: 결과 메시지
 * - data: 사용자 정보 (성공 시)
 */

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 로그 함수
function debug_log($message, $data = null) {
    $log_dir = __DIR__ . '/../../logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    
    $log_file = $log_dir . '/user_profile.log';
    $log_message = date('[Y-m-d H:i:s]') . ' ' . $message;
    
    if ($data !== null) {
        $log_message .= ' - ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
}

// GET 요청 검증
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 방식입니다. GET 요청이 필요합니다.'
    ]);
    exit();
}

// 세션 시작 및 로그인 체크
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => '로그인이 필요합니다.'
    ]);
    exit();
}

// 요청 파라미터 검증
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '사용자 ID가 필요합니다.'
    ]);
    exit();
}

$target_user_id = $_GET['user_id'];
debug_log('특정 사용자 프로필 정보 요청', [
    'requester_id' => $_SESSION['user_id'],
    'target_user_id' => $target_user_id
]);

// 데이터베이스 연결
require_once __DIR__ . '/../../config/database.php';
$db_config = require __DIR__ . '/../../config/database.php';

try {
    // PDO 연결
    $dsn = "mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname={$db_config['db_name']};charset={$db_config['db_charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    $pdo = new PDO($dsn, $db_config['db_user'], $db_config['db_pass'], $options);
    
    // 사용자 정보 조회 (공개 정보만)
    $stmt = $pdo->prepare('SELECT 
                            id, 
                            nickname, 
                            profile_image, 
                            company, 
                            introduction, 
                            position, 
                            country
                          FROM users 
                          WHERE id = :user_id AND is_blocked = 0');
    $stmt->execute([':user_id' => $target_user_id]);
    $user_data = $stmt->fetch();
    
    if (!$user_data) {
        debug_log('특정 사용자 프로필 정보 조회 실패 - 사용자 없음 또는 차단됨', [
            'target_user_id' => $target_user_id
        ]);
        
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => '사용자 정보를 찾을 수 없습니다.'
        ]);
        exit();
    }
    
    debug_log('특정 사용자 프로필 정보 조회 성공', [
        'target_user_id' => $target_user_id
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => '사용자 프로필 정보를 성공적으로 조회했습니다.',
        'data' => $user_data
    ]);
} catch (PDOException $e) {
    debug_log('특정 사용자 프로필 정보 조회 오류', [
        'target_user_id' => $target_user_id,
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.'
    ]);
} 