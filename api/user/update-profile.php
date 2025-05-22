<?php
/**
 * 회원 프로필 업데이트 API
 * 
 * 회사명, 자기소개, 프로필 이미지 URL 등의 정보를 업데이트합니다.
 * 
 * 요청 방식: POST
 * 
 * 요청 파라미터:
 * - company: 회사명 (선택 사항)
 * - introduction: 자기소개 (선택 사항)
 * - profile_image: 프로필 이미지 URL (선택 사항)
 * - email: 이메일 (선택 사항)
 * - position: 포지션 (선택 사항, 'leader' 또는 'sales')
 * 
 * 응답:
 * - success: 성공 여부 (boolean)
 * - message: 결과 메시지
 * - data: 업데이트된 사용자 정보 (성공 시)
 */

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
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
    
    $log_file = $log_dir . '/profile_update.log';
    $log_message = date('[Y-m-d H:i:s]') . ' ' . $message;
    
    if ($data !== null) {
        $log_message .= ' - ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    file_put_contents($log_file, $log_message . PHP_EOL, FILE_APPEND);
}

// POST 요청 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 방식입니다. POST 요청이 필요합니다.'
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

// 요청 데이터 파싱
$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

// JSON 파싱 실패 시
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 JSON 형식입니다.'
    ]);
    exit();
}

debug_log('프로필 업데이트 요청 시작', [
    'user_id' => $_SESSION['user_id'],
    'data' => $data
]);

// 필수 파라미터 확인 (하나 이상의 업데이트 필드가 있어야 함)
if (empty($data) || (
    !isset($data['company']) && 
    !isset($data['introduction']) && 
    !isset($data['profile_image']) && 
    !isset($data['email']) && 
    !isset($data['position'])
)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '업데이트할 정보가 없습니다.'
    ]);
    exit();
}

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
    
    // 업데이트할 필드와 값 구성
    $updates = [];
    $params = [];
    
    // 회사명
    if (isset($data['company'])) {
        $company = trim($data['company']);
        if (mb_strlen($company) > 100) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '회사명은 100자를 초과할 수 없습니다.'
            ]);
            exit();
        }
        $updates[] = 'company = :company';
        $params[':company'] = $company;
    }
    
    // 자기소개
    if (isset($data['introduction'])) {
        $introduction = trim($data['introduction']);
        if (mb_strlen($introduction) > 5000) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '자기소개는 5000자를 초과할 수 없습니다.'
            ]);
            exit();
        }
        $updates[] = 'introduction = :introduction';
        $params[':introduction'] = $introduction;
    }
    
    // 프로필 이미지
    if (isset($data['profile_image'])) {
        $profile_image = trim($data['profile_image']);
        if (!filter_var($profile_image, FILTER_VALIDATE_URL) && !empty($profile_image)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '유효한 프로필 이미지 URL이 아닙니다.'
            ]);
            exit();
        }
        $updates[] = 'profile_image = :profile_image';
        $params[':profile_image'] = $profile_image;
    }
    
    // 이메일
    if (isset($data['email'])) {
        $email = trim($data['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '유효한 이메일 주소가 아닙니다.'
            ]);
            exit();
        }
        $updates[] = 'email = :email';
        $params[':email'] = $email;
    }
    
    // 포지션
    if (isset($data['position'])) {
        $position = trim($data['position']);
        if (!in_array($position, ['leader', 'sales'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => '포지션은 leader 또는 sales만 가능합니다.'
            ]);
            exit();
        }
        $updates[] = 'position = :position';
        $params[':position'] = $position;
    }
    
    // 업데이트 쿼리 구성
    $update_query = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = :user_id';
    $params[':user_id'] = $_SESSION['user_id'];
    
    debug_log('프로필 업데이트 쿼리', [
        'query' => $update_query,
        'params' => $params
    ]);
    
    // 쿼리 실행
    $stmt = $pdo->prepare($update_query);
    $result = $stmt->execute($params);
    
    if ($result && $stmt->rowCount() > 0) {
        // 업데이트된 사용자 정보 조회
        $select_stmt = $pdo->prepare('SELECT id, nickname, profile_image, company, introduction, position, email, country, language FROM users WHERE id = :user_id');
        $select_stmt->execute([':user_id' => $_SESSION['user_id']]);
        $user_data = $select_stmt->fetch();
        
        debug_log('프로필 업데이트 성공', $user_data);
        
        echo json_encode([
            'success' => true,
            'message' => '프로필이 성공적으로 업데이트되었습니다.',
            'data' => $user_data
        ]);
    } else {
        debug_log('프로필 업데이트 실패 - 변경사항 없음');
        
        echo json_encode([
            'success' => true,
            'message' => '변경된 정보가 없습니다.'
        ]);
    }
} catch (PDOException $e) {
    debug_log('프로필 업데이트 오류', [
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.'
    ]);
} 