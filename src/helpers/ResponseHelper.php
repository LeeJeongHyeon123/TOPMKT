<?php
/**
 * 응답 관련 헬퍼 함수
 */

class ResponseHelper {
    /**
     * JSON 응답 반환
     * 
     * @param array $data 응답 데이터
     * @param int $status HTTP 상태 코드
     * @return void
     */
    public static function json($data, $status = 200) {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
    
    /**
     * 성공 응답 반환
     * 
     * @param array $data 응답 데이터
     * @param string $message 성공 메시지
     * @param int $status HTTP 상태 코드
     * @return void
     */
    public static function success($data = [], $message = '성공적으로 처리되었습니다.', $status = 200) {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    
    /**
     * 오류 응답 반환
     * 
     * @param string $message 오류 메시지
     * @param int $status HTTP 상태 코드
     * @param array $errors 상세 오류 목록
     * @return void
     */
    public static function error($message = '요청을 처리할 수 없습니다.', $status = 400, $errors = []) {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
    
    /**
     * 페이지 리다이렉트
     * 
     * @param string $url 리다이렉트 URL
     * @param array $flash 세션에 저장할 플래시 메시지
     * @return void
     */
    public static function redirect($url, $flash = []) {
        foreach ($flash as $key => $value) {
            $_SESSION[$key] = $value;
        }
        
        header("Location: $url");
        exit;
    }
    
    /**
     * 404 Not Found 오류 페이지 표시
     * 
     * @return void
     */
    public static function notFound() {
        header('HTTP/1.1 404 Not Found');
        include SRC_PATH . '/views/templates/404.php';
        exit;
    }
    
    /**
     * 403 Forbidden 오류 페이지 표시
     * 
     * @return void
     */
    public static function forbidden() {
        header('HTTP/1.1 403 Forbidden');
        include SRC_PATH . '/views/templates/403.php';
        exit;
    }
    
    /**
     * 500 Internal Server Error 오류 페이지 표시
     * 
     * @param string $error 오류 메시지 (디버그 모드에서만 표시)
     * @return void
     */
    public static function serverError($error = '') {
        header('HTTP/1.1 500 Internal Server Error');
        
        // 디버그 모드에서는 오류 메시지 로깅
        if (defined('APP_DEBUG') && APP_DEBUG && !empty($error)) {
            error_log($error);
        }
        
        include SRC_PATH . '/views/templates/500.php';
        exit;
    }
    
    /**
     * JSON 성공 응답 반환
     * 
     * @param string $message 성공 메시지
     * @param array $data 응답 데이터
     * @param int $status HTTP 상태 코드
     * @return void
     */
    public static function jsonSuccess($message = '성공적으로 처리되었습니다.', $data = [], $status = 200) {
        self::json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }
    
    /**
     * JSON 오류 응답 반환
     * 
     * @param string $message 오류 메시지
     * @param int $status HTTP 상태 코드
     * @param array $errors 상세 오류 목록
     * @return void
     */
    public static function jsonError($message = '요청을 처리할 수 없습니다.', $status = 400, $errors = []) {
        self::json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
} 