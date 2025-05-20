<?php
/**
 * 애플리케이션 기본 설정
 * 
 * @package TOPMKT
 */

return [
    // 기본 설정
    'name' => '탑마케팅',
    'env' => 'production',
    'debug' => false,
    'url' => 'https://www.topmkt.co.kr',
    'timezone' => 'Asia/Seoul',
    'locale' => 'ko',
    'fallback_locale' => 'en',
    
    // 세션 설정
    'session' => [
        'driver' => 'file',
        'lifetime' => 0, // 무제한
        'path' => '/var/www/html/topmkt/storage/sessions',
    ],
    
    // 로그 설정
    'log' => [
        'channel' => 'file',
        'level' => 'error',
        'path' => '/var/log/httpd/topmkt_error.log',
    ],
    
    // 캐시 설정
    'cache' => [
        'driver' => 'file',
        'prefix' => 'topmkt_',
        'path' => '/var/www/html/topmkt/storage/cache',
    ],
    
    // 파일 업로드 설정
    'upload' => [
        'max_size' => 10485760, // 10MB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ],
    
    // reCAPTCHA 설정
    'recaptcha' => [
        'site_key' => '6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4',
        'secret_key' => '6LfCdjErAAAAAPYQYwXxXxXxXxXxXxXxXxXxXxXx',
        'project_id' => 'topmkt-832f2',
        'threshold' => 0.3,
        'action' => [
            'phone_verification' => 'PHONE_VERIFICATION',
            'login' => 'LOGIN',
            'register' => 'REGISTER',
            'comment' => 'COMMENT',
            'post' => 'POST'
        ]
    ],
]; 