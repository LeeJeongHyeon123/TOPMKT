<?php
/**
 * 공통 헬퍼 함수 모음
 * 
 * 프로젝트 전역에서 사용되는 유틸리티 함수들을 정의합니다.
 */

if (!function_exists('app_path')) {
    /**
     * 애플리케이션 경로를 반환
     * 
     * @param string $path 상대 경로
     * @return string 전체 경로
     */
    function app_path($path = '')
    {
        return APP_ROOT . '/app' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('config_path')) {
    /**
     * 설정 파일 경로를 반환
     * 
     * @param string $path 상대 경로
     * @return string 전체 경로
     */
    function config_path($path = '')
    {
        return APP_ROOT . '/config' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('public_path')) {
    /**
     * 공개 디렉토리 경로를 반환
     * 
     * @param string $path 상대 경로
     * @return string 전체 경로
     */
    function public_path($path = '')
    {
        return APP_ROOT . '/public' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('storage_path')) {
    /**
     * 스토리지 디렉토리 경로를 반환
     * 
     * @param string $path 상대 경로
     * @return string 전체 경로
     */
    function storage_path($path = '')
    {
        return APP_ROOT . '/storage' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('base_url')) {
    /**
     * 베이스 URL을 반환
     * 
     * @param string $path 상대 경로
     * @return string 전체 URL
     */
    function base_url($path = '')
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
        
        return $protocol . $domain . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('asset')) {
    /**
     * 애셋 URL을 반환
     * 
     * @param string $path 애셋 상대 경로
     * @return string 전체 URL
     */
    function asset($path)
    {
        return base_url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('dd')) {
    /**
     * 디버그 덤프 후 종료
     * 
     * @param mixed $var 덤프할 변수
     * @return void
     */
    function dd($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        die();
    }
}

if (!function_exists('env')) {
    /**
     * 환경 변수 가져오기
     * 
     * @param string $key 환경 변수 키
     * @param mixed $default 기본값
     * @return mixed 환경 변수 값
     */
    function env($key, $default = null)
    {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        return $value;
    }
} 