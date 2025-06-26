<?php
/**
 * 보안 헤더 설정 미들웨어
 */

class SecurityMiddleware {
    
    /**
     * 보안 헤더 설정
     */
    public static function setSecurityHeaders() {
        // XSS 보호
        header('X-XSS-Protection: 1; mode=block');
        
        // Content Type Sniffing 방지
        header('X-Content-Type-Options: nosniff');
        
        // Clickjacking 방지
        header('X-Frame-Options: SAMEORIGIN');
        
        // HTTPS 강제 (프로덕션에서만)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Permissions Policy
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.googleapis.com https://www.google.com https://www.gstatic.com; " .
               "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; " .
               "font-src 'self' https://fonts.gstatic.com; " .
               "img-src 'self' data: https:; " .
               "connect-src 'self' https:; " .
               "media-src 'self'; " .
               "object-src 'none'; " .
               "base-uri 'self'; " .
               "form-action 'self'";
        
        header("Content-Security-Policy: $csp");
    }
    
    /**
     * 입력 데이터 검증 및 정제
     */
    public static function sanitizeInput(&$data) {
        if (is_array($data)) {
            foreach ($data as $key => &$value) {
                if (is_string($value)) {
                    // HTML 태그 제거 (특정 허용 태그 제외)
                    $value = strip_tags($value, '<p><br><strong><em><ul><ol><li>');
                    
                    // 특수 문자 인코딩
                    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    // SQL 인젝션 방지 (추가 보안)
                    $value = preg_replace('/[\'";\\\\]/', '', $value);
                } elseif (is_array($value)) {
                    self::sanitizeInput($value);
                }
            }
        } elseif (is_string($data)) {
            $data = strip_tags($data, '<p><br><strong><em><ul><ol><li>');
            $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $data = preg_replace('/[\'";\\\\]/', '', $data);
        }
    }
    
    /**
     * SQL 인젝션 패턴 감지
     */
    public static function detectSqlInjection($input) {
        $patterns = [
            '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bUPDATE\b|\bDELETE\b|\bDROP\b)/i',
            '/(\bOR\b|\bAND\b)\s+\d+\s*=\s*\d+/i',
            '/[\'"];?\s*(OR|AND)\s+[\'"]?\w/i',
            '/\b(exec|execute)\s*\(/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * XSS 패턴 감지
     */
    public static function detectXss($input) {
        $patterns = [
            '/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 요청 제한 (Rate Limiting)
     */
    public static function rateLimit($identifier, $maxRequests = 100, $timeWindow = 3600) {
        $cacheKey = "rate_limit_" . md5($identifier);
        $currentTime = time();
        
        // 간단한 파일 기반 캐시 (실제 환경에서는 Redis 사용 권장)
        $cacheFile = sys_get_temp_dir() . '/' . $cacheKey;
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            
            // 시간 창이 지났으면 리셋
            if ($currentTime - $data['start_time'] > $timeWindow) {
                $data = ['count' => 1, 'start_time' => $currentTime];
            } else {
                $data['count']++;
            }
        } else {
            $data = ['count' => 1, 'start_time' => $currentTime];
        }
        
        file_put_contents($cacheFile, json_encode($data));
        
        return $data['count'] <= $maxRequests;
    }
    
    /**
     * IP 화이트리스트 확인
     */
    public static function checkIpWhitelist($whitelist = []) {
        if (empty($whitelist)) {
            return true; // 화이트리스트가 없으면 모든 IP 허용
        }
        
        $clientIp = self::getClientIp();
        
        foreach ($whitelist as $allowedIp) {
            if (self::ipInRange($clientIp, $allowedIp)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 클라이언트 IP 주소 가져오기
     */
    public static function getClientIp() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * IP 범위 확인
     */
    private static function ipInRange($ip, $range) {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }
        
        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) === $subnet;
    }
}
?>