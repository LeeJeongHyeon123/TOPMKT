<?php
/**
 * 전역 유틸리티 함수 모음
 */

/**
 * 디버그 정보를 출력합니다.
 *
 * @param mixed $data 출력할 데이터
 * @param bool $die 출력 후 스크립트 종료 여부
 * @return void
 */
function debug($data, $die = false) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * 현재 사용 언어에 맞는 번역 문자열을 반환합니다.
 *
 * @param string $key 번역 키
 * @param array $params 치환할 매개변수
 * @param string $locale 사용할 언어 (기본값: 현재 언어)
 * @return string 번역된 문자열
 */
function trans($key, $params = [], $locale = null) {
    // 현재 언어 또는 지정된 언어 사용
    $locale = $locale ?: $_SESSION['locale'] ?? 'ko';
    
    // 키에서 파일명과 항목 분리
    list($file, $item) = explode('.', $key, 2);
    
    // 번역 파일 경로
    $filePath = APP_ROOT . '/resources/lang/' . $locale . '/' . $file . '.php';
    
    // 번역 파일이 존재하는지 확인
    if (!file_exists($filePath)) {
        return $key;
    }
    
    // 번역 배열 로드
    $translations = require $filePath;
    
    // 번역 항목이 존재하는지 확인
    if (!isset($translations[$item])) {
        return $key;
    }
    
    $text = $translations[$item];
    
    // 매개변수 치환
    if (!empty($params)) {
        foreach ($params as $param => $value) {
            $text = str_replace(':' . $param, $value, $text);
        }
    }
    
    return $text;
}

/**
 * 설정 값을 가져옵니다.
 *
 * @param string $key 설정 키
 * @param mixed $default 기본값
 * @return mixed 설정 값
 */
function config($key, $default = null) {
    // 키에서 파일명과 항목 분리
    list($file, $item) = explode('.', $key, 2);
    
    // 설정 파일 경로
    $filePath = APP_ROOT . '/config/' . $file . '.php';
    
    // 설정 파일이 존재하는지 확인
    if (!file_exists($filePath)) {
        return $default;
    }
    
    // 설정 배열 로드
    $config = require $filePath;
    
    // 중첩된 키 처리
    $keys = explode('.', $item);
    $value = $config;
    
    foreach ($keys as $key) {
        if (!isset($value[$key])) {
            return $default;
        }
        $value = $value[$key];
    }
    
    return $value;
}

/**
 * 현재 요청 URL의 기본 경로를 반환합니다.
 *
 * @return string 기본 URL
 */
function base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'];
    return $protocol . $domainName;
}

/**
 * 자산 파일의 URL을 생성합니다.
 *
 * @param string $path 자산 파일 경로
 * @return string 자산 URL
 */
function asset($path) {
    return base_url() . '/assets/' . ltrim($path, '/');
}

/**
 * 페이지를 다른 URL로 리다이렉트합니다.
 *
 * @param string $url 리다이렉트할 URL
 * @param int $statusCode HTTP 상태 코드
 * @return void
 */
function redirect($url, $statusCode = 302) {
    header('Location: ' . $url, true, $statusCode);
    exit;
} 