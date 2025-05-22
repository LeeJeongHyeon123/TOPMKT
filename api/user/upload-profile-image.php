<?php
/**
 * 프로필 이미지 업로드 API
 * 
 * 사용자의 프로필 이미지를 Firebase Storage에 업로드하고 URL을 반환합니다.
 * 
 * 요청 방식: POST
 * 
 * 요청 파라미터:
 * - image: Base64로 인코딩된 이미지 데이터
 * 
 * 응답:
 * - success: 성공 여부 (boolean)
 * - message: 결과 메시지
 * - url: 업로드된 이미지 URL (성공 시)
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
    
    $log_file = $log_dir . '/profile_image_upload.log';
    $log_message = date('[Y-m-d H:i:s]') . ' ' . $message;
    
    if ($data !== null) {
        if (is_array($data) && isset($data['image'])) {
            // 이미지 데이터는 로그에 기록하지 않음
            $data_copy = $data;
            $data_copy['image'] = '[이미지 데이터 생략]';
            $log_message .= ' - ' . json_encode($data_copy, JSON_UNESCAPED_UNICODE);
        } else {
            $log_message .= ' - ' . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
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

// 이미지 데이터 검증
if (!isset($data['image']) || empty($data['image'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '이미지 데이터가 없습니다.'
    ]);
    exit();
}

debug_log('프로필 이미지 업로드 요청 시작', [
    'user_id' => $_SESSION['user_id']
]);

// Base64 이미지 데이터 처리
$image_data = $data['image'];

// Base64 형식 검증 (data:image/png;base64,로 시작하는지 확인)
if (!preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $image_data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '유효한 이미지 형식이 아닙니다.'
    ]);
    exit();
}

// Base64 데이터에서 이미지 타입 추출
preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $image_data, $matches);
$image_type = $matches[1];

// Base64 인코딩 부분만 추출
$base64_data = preg_replace('/^data:image\/(jpeg|png|gif|webp);base64,/', '', $image_data);
$image_binary = base64_decode($base64_data);

if ($image_binary === false) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '이미지 디코딩에 실패했습니다.'
    ]);
    exit();
}

// 이미지 크기 제한 (5MB)
if (strlen($image_binary) > 5 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => '이미지 크기는 5MB를 초과할 수 없습니다.'
    ]);
    exit();
}

// 임시 파일로 저장
$temp_dir = __DIR__ . '/../../temp';
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0755, true);
}

$temp_file = $temp_dir . '/' . uniqid() . '.' . $image_type;
if (!file_put_contents($temp_file, $image_binary)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '임시 파일 저장에 실패했습니다.'
    ]);
    exit();
}

// Firebase Storage 설정 로드
require_once __DIR__ . '/../../config/firebase/config.php';

try {
    // Firebase Admin SDK 초기화
    $serviceAccountPath = __DIR__ . '/../../config/google/service-account.json';
    
    if (!file_exists($serviceAccountPath)) {
        throw new Exception('Firebase 서비스 계정 파일을 찾을 수 없습니다.');
    }
    
    // Google Cloud Storage 클라이언트 생성
    $storage = new Google\Cloud\Storage\StorageClient([
        'keyFilePath' => $serviceAccountPath
    ]);
    
    // 버킷 이름 (Firebase 스토리지의 기본 버킷)
    $bucketName = FIREBASE_PROJECT_ID . '.appspot.com';
    $bucket = $storage->bucket($bucketName);
    
    // 업로드할 경로 설정 (사용자 ID 기반)
    $user_id = $_SESSION['user_id'];
    $object_name = 'profile_images/' . $user_id . '_' . time() . '.' . $image_type;
    
    // 파일 업로드
    $file = fopen($temp_file, 'r');
    $object = $bucket->upload($file, [
        'name' => $object_name,
        'predefinedAcl' => 'publicRead',
        'metadata' => [
            'contentType' => 'image/' . $image_type
        ]
    ]);
    
    // 임시 파일 삭제
    unlink($temp_file);
    
    // 업로드된 이미지 URL 생성
    $image_url = 'https://storage.googleapis.com/' . $bucketName . '/' . $object_name;
    
    debug_log('프로필 이미지 업로드 성공', [
        'user_id' => $user_id,
        'image_url' => $image_url
    ]);
    
    // 응답 반환
    echo json_encode([
        'success' => true,
        'message' => '프로필 이미지가 성공적으로 업로드되었습니다.',
        'url' => $image_url
    ]);
} catch (Exception $e) {
    // 임시 파일이 있으면 삭제
    if (file_exists($temp_file)) {
        unlink($temp_file);
    }
    
    debug_log('프로필 이미지 업로드 오류', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '이미지 업로드 중 오류가 발생했습니다: ' . $e->getMessage()
    ]);
} 