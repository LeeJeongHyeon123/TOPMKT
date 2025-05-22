<?php
require_once '../../includes/functions.php';
require_once '../../includes/Database.php';
$messages = require __DIR__ . '/../../resources/lang/ko/messages.php';

// 디버그 로그 설정
$debug_log_file = __DIR__ . '/../../logs/check_nickname_debug.log';
function debug_log($message, $data = null) {
    global $debug_log_file;
    $log = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($data !== null) {
        $log .= ' - Data: ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    file_put_contents($debug_log_file, $log . "\n", FILE_APPEND);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    debug_log("PHP Error: [$errno] $errstr in $errfile on line $errline");
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.',
        'error' => 'Internal Server Error'
    ]);
    exit;
}

// 예외 핸들러 설정
function handleException($e) {
    debug_log("Exception: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.',
        'error' => 'Internal Server Error'
    ]);
    exit;
}

set_error_handler('handleError');
set_exception_handler('handleException');

// 출력 버퍼링 시작
ob_start();

// 응답 헤더 설정
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// OPTIONS 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// GET 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '허용되지 않는 요청 메소드입니다.',
        'error' => 'Method Not Allowed'
    ]);
    exit;
}

debug_log('닉네임 중복 체크 요청 시작');

$nickname = $_GET['nickname'] ?? '';
if (!$nickname) {
    debug_log('닉네임 미입력');
    echo json_encode([
        'success' => false,
        'message' => $messages['auth']['register']['required']
    ]);
    exit;
}

try {
    $db = Database::getInstance();
    $stmt = $db->query('SELECT COUNT(*) FROM users WHERE nickname = ?', [$nickname]);
    $exists = $stmt->fetchColumn() > 0;
    
    debug_log('닉네임 중복 체크 결과', ['nickname' => $nickname, 'exists' => $exists]);
    
    echo json_encode([
        'success' => true,
        'exists' => $exists
    ]);
} catch (Exception $e) {
    debug_log('데이터베이스 오류', ['error' => $e->getMessage()]);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.',
        'error' => 'Database Error'
    ]);
}

// 출력 버퍼 플러시
ob_end_flush(); 