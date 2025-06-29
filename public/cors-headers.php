<?php
/**
 * CORS 헤더 설정 유틸리티
 */

function setCorsHeaders() {
    // 개발 환경에서는 모든 오리진 허용, 프로덕션에서는 특정 도메인만 허용
    $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:5173',
        'http://localhost',
        'https://topmkt.kr',
        'https://www.topmkt.kr',
        'https://topmktx.com',
        'https://www.topmktx.com'
    ];
    
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, $allowedOrigins) || empty($origin)) {
        header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // 24시간
}

// OPTIONS 요청 처리 (CORS preflight)
function handleCorsPreflightRequest() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        setCorsHeaders();
        http_response_code(200);
        exit;
    }
}

// 자동으로 CORS 헤더 설정
setCorsHeaders();
handleCorsPreflightRequest();
?>