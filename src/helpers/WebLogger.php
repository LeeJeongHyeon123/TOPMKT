<?php
/**
 * 웹 페이지용 실시간 로그 헬퍼
 */

class WebLogger {
    private static $logs = [];
    private static $startTime = null;
    
    public static function init() {
        self::$startTime = microtime(true);
        self::$logs = [];
    }
    
    public static function log($message) {
        if (self::$startTime === null) {
            self::init();
        }
        
        $elapsed = round((microtime(true) - self::$startTime) * 1000, 2);
        self::$logs[] = "[{$elapsed}ms] {$message}";
        
        // 에러 로그도 함께 기록
        error_log("[{$elapsed}ms] {$message}");
    }
    
    public static function getLogs() {
        return self::$logs;
    }
    
    public static function getLogsHtml() {
        $html = '<div style="background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 5px; font-family: monospace; font-size: 12px;">';
        $html .= '<h4>🔍 실시간 성능 로그</h4>';
        
        foreach (self::$logs as $log) {
            $color = '#333';
            if (strpos($log, '[CONTROLLER]') !== false) $color = '#007bff';
            if (strpos($log, '[SEARCH]') !== false) $color = '#28a745';
            if (strpos($log, '[COUNT]') !== false) $color = '#ffc107';
            if (strpos($log, '[CACHE]') !== false) $color = '#6f42c1';
            
            $html .= '<div style="color: ' . $color . '; margin: 2px 0;">' . htmlspecialchars($log) . '</div>';
        }
        
        $html .= '</div>';
        return $html;
    }
    
    public static function clear() {
        self::$logs = [];
        self::$startTime = microtime(true);
    }
}