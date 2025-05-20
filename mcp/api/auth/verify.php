<?php
// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// Firebase 설정 파일 포함
require_once __DIR__ . '/../../config/firebase/config.php';

// JSON 요청 처리
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 필수 파라미터 확인
if (!isset($data['uid']) || !isset($data['phoneNumber'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

try {
    // Firebase에서 사용자 정보 확인
    $user = $auth->getUser($data['uid']);
    
    // 전화번호 일치 여부 확인
    if ($user->phoneNumber !== $data['phoneNumber']) {
        throw new Exception('전화번호가 일치하지 않습니다.');
    }
    
    // 세션 시작
    session_start();
    
    // 세션에 사용자 정보 저장
    $_SESSION['user'] = [
        'uid' => $user->uid,
        'phoneNumber' => $user->phoneNumber,
        'lastLogin' => date('Y-m-d H:i:s')
    ];
    
    // 로그인 이력 기록
    $stmt = $database->getReference('users/' . $user->uid . '/loginHistory')
        ->push([
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'userAgent' => $_SERVER['HTTP_USER_AGENT']
        ]);
    
    // 성공 응답
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    // 에러 로그 기록
    error_log('Authentication Error: ' . $e->getMessage());
    
    // 에러 응답
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => '인증 처리 중 오류가 발생했습니다: ' . $e->getMessage()
    ]);
} 