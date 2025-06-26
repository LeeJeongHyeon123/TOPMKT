<?php
/**
 * API 응답 표준화 헬퍼
 */

class ApiResponseHelper {
    
    /**
     * 성공 응답
     */
    public static function success($data = null, $message = '', $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 에러 응답
     */
    public static function error($message = '오류가 발생했습니다.', $code = 400, $errors = null) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        // 개발 환경에서만 스택 트레이스 포함
        if (defined('APP_ENV') && APP_ENV === 'development') {
            $response['debug'] = [
                'timestamp' => date('c'),
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'method' => $_SERVER['REQUEST_METHOD'] ?? ''
            ];
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * 유효성 검사 에러 응답
     */
    public static function validationError($errors, $message = '입력 데이터가 올바르지 않습니다.') {
        self::error($message, 422, $errors);
    }
    
    /**
     * 인증 에러 응답
     */
    public static function unauthorized($message = '인증이 필요합니다.') {
        self::error($message, 401);
    }
    
    /**
     * 권한 에러 응답
     */
    public static function forbidden($message = '접근 권한이 없습니다.') {
        self::error($message, 403);
    }
    
    /**
     * 리소스 없음 에러 응답
     */
    public static function notFound($message = '요청한 리소스를 찾을 수 없습니다.') {
        self::error($message, 404);
    }
    
    /**
     * 서버 에러 응답
     */
    public static function serverError($message = '서버 내부 오류가 발생했습니다.') {
        self::error($message, 500);
    }
    
    /**
     * 페이지네이션 응답
     */
    public static function paginated($data, $pagination, $message = '') {
        self::success([
            'items' => $data,
            'pagination' => $pagination
        ], $message);
    }
    
    /**
     * 메서드 허용 안됨 에러
     */
    public static function methodNotAllowed($allowedMethods = []) {
        $message = '허용되지 않는 HTTP 메서드입니다.';
        if (!empty($allowedMethods)) {
            header('Allow: ' . implode(', ', $allowedMethods));
            $message .= ' 허용된 메서드: ' . implode(', ', $allowedMethods);
        }
        self::error($message, 405);
    }
    
    /**
     * 요청 데이터 파싱
     */
    public static function getRequestData() {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        
        if (strpos($contentType, 'application/json') === 0) {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                self::error('잘못된 JSON 형식입니다.', 400);
            }
            
            return $data ?? [];
        }
        
        return $_POST;
    }
    
    /**
     * 요청 헤더에서 Bearer 토큰 추출
     */
    public static function getBearerToken() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }
        
        return $matches[1];
    }
    
    /**
     * 유효성 검사 헬퍼
     */
    public static function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $errors[$field] = "{$field} 필드는 필수입니다.";
            }
        }
        
        if (!empty($errors)) {
            self::validationError($errors);
        }
    }
    
    /**
     * 이메일 유효성 검사
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * 휴대폰 번호 유효성 검사 (한국)
     */
    public static function validatePhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return preg_match('/^01[0-9]{8,9}$/', $phone);
    }
}
?>