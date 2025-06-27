<?php
/**
 * 탑마케팅 로그 뷰어
 */

class LogViewer {
    private static $logPaths = [
        'main' => '/var/www/html/topmkt/logs/topmkt_errors.log',
        'standard' => '/var/log/topmkt/',
        'php' => null // PHP 기본 로그
    ];
    
    public static function showAllLogs($lines = 50) {
        echo "=== 탑마케팅 로그 현황 ===\n\n";
        
        // 1. 메인 프로젝트 로그
        echo "📋 메인 프로젝트 로그 (/var/www/html/topmkt/logs/topmkt_errors.log):\n";
        self::showLog(self::$logPaths['main'], $lines);
        
        // 2. 표준 로그 디렉토리
        echo "\n📂 표준 로그 디렉토리 (/var/log/topmkt/):\n";
        self::showStandardLogs($lines);
        
        // 3. PHP 시스템 로그
        echo "\n🐘 PHP 시스템 로그:\n";
        self::showPhpLogs($lines);
        
        // 4. Apache 로그 (SMS 관련)
        echo "\n🌐 Apache 에러 로그 (SMS 관련):\n";
        self::showApacheLogs($lines);
    }
    
    private static function showLog($logFile, $lines) {
        if (!file_exists($logFile)) {
            echo "   ❌ 로그 파일이 없습니다: $logFile\n";
            return;
        }
        
        $size = filesize($logFile);
        echo "   📊 파일 크기: " . self::formatBytes($size) . "\n";
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($logs)) {
            echo "   📝 로그가 비어있습니다.\n";
            return;
        }
        
        $totalLines = count($logs);
        echo "   📝 총 로그 라인 수: $totalLines\n";
        
        $recentLogs = array_slice($logs, -$lines);
        echo "   📄 최근 $lines 라인:\n";
        
        foreach ($recentLogs as $index => $log) {
            $lineNum = $totalLines - $lines + $index + 1;
            echo "   $lineNum: " . substr($log, 0, 100) . (strlen($log) > 100 ? '...' : '') . "\n";
        }
    }
    
    private static function showStandardLogs($lines) {
        $standardDir = self::$logPaths['standard'];
        
        if (!is_dir($standardDir)) {
            echo "   ❌ 표준 로그 디렉토리가 없습니다: $standardDir\n";
            return;
        }
        
        $files = glob($standardDir . '*.log');
        if (empty($files)) {
            echo "   📝 표준 로그 파일이 없습니다.\n";
            return;
        }
        
        foreach ($files as $file) {
            $fileName = basename($file);
            echo "   📄 $fileName:\n";
            self::showLog($file, min($lines, 10));
            echo "\n";
        }
    }
    
    private static function showPhpLogs($lines) {
        // PHP 기본 에러 로그 경로 찾기
        $phpLogPath = ini_get('error_log');
        
        if (empty($phpLogPath)) {
            // 일반적인 PHP 로그 경로들 확인
            $possiblePaths = [
                '/var/log/php_errors.log',
                '/var/log/php7.4-fpm.log',
                '/var/log/apache2/error.log'
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $phpLogPath = $path;
                    break;
                }
            }
        }
        
        if (empty($phpLogPath) || !file_exists($phpLogPath)) {
            echo "   ❌ PHP 로그 파일을 찾을 수 없습니다.\n";
            echo "   💡 현재 PHP error_log 설정: " . ($phpLogPath ?: '설정되지 않음') . "\n";
            return;
        }
        
        echo "   📍 PHP 로그 위치: $phpLogPath\n";
        
        // SMS 관련 로그만 필터링
        $command = "grep -i 'sms\\|aligo\\|topmkt' '$phpLogPath' | tail -$lines";
        $smsLogs = shell_exec($command);
        
        if (empty($smsLogs)) {
            echo "   📝 SMS 관련 PHP 로그가 없습니다.\n";
        } else {
            echo "   📄 SMS 관련 PHP 로그:\n";
            $lines = explode("\n", trim($smsLogs));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    echo "   " . substr($line, 0, 120) . (strlen($line) > 120 ? '...' : '') . "\n";
                }
            }
        }
    }
    
    private static function showApacheLogs($lines) {
        $apacheLog = '/var/log/apache2/error.log';
        
        if (!file_exists($apacheLog)) {
            echo "   ❌ Apache 로그 파일이 없습니다: $apacheLog\n";
            return;
        }
        
        // SMS 관련 Apache 로그만 필터링
        $command = "grep -i 'sms\\|aligo\\|topmkt\\|curl' '$apacheLog' | tail -$lines";
        $apacheLogs = shell_exec($command);
        
        if (empty($apacheLogs)) {
            echo "   📝 SMS 관련 Apache 로그가 없습니다.\n";
        } else {
            echo "   📄 SMS 관련 Apache 로그:\n";
            $lines = explode("\n", trim($apacheLogs));
            foreach ($lines as $line) {
                if (!empty($line)) {
                    echo "   " . substr($line, 0, 120) . (strlen($line) > 120 ? '...' : '') . "\n";
                }
            }
        }
    }
    
    private static function formatBytes($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($size >= 1024 && $i < 3) {
            $size /= 1024;
            $i++;
        }
        
        return round($size, 2) . ' ' . $units[$i];
    }
    
    public static function monitorLogs() {
        echo "=== 실시간 로그 모니터링 (Ctrl+C로 종료) ===\n";
        echo "📍 모니터링 대상: " . self::$logPaths['main'] . "\n\n";
        
        $logFile = self::$logPaths['main'];
        
        if (!file_exists($logFile)) {
            echo "❌ 로그 파일이 없습니다. 로그 생성을 기다리는 중...\n";
            
            // 파일이 생성될 때까지 대기
            while (!file_exists($logFile)) {
                sleep(1);
            }
            echo "✅ 로그 파일이 생성되었습니다!\n\n";
        }
        
        $handle = fopen($logFile, 'r');
        fseek($handle, 0, SEEK_END);
        
        while (true) {
            $line = fgets($handle);
            if ($line !== false) {
                echo date('H:i:s') . " | " . trim($line) . "\n";
            } else {
                usleep(100000); // 0.1초 대기
            }
        }
    }
    
    public static function searchLogs($keyword, $lines = 100) {
        echo "=== 로그 검색: '$keyword' ===\n\n";
        
        $logFile = self::$logPaths['main'];
        
        if (!file_exists($logFile)) {
            echo "❌ 로그 파일이 없습니다: $logFile\n";
            return;
        }
        
        $command = "grep -i '$keyword' '$logFile' | tail -$lines";
        $results = shell_exec($command);
        
        if (empty($results)) {
            echo "📝 '$keyword'와 관련된 로그를 찾을 수 없습니다.\n";
        } else {
            echo "📄 검색 결과 (최근 $lines 개):\n";
            $lines = explode("\n", trim($results));
            foreach ($lines as $index => $line) {
                if (!empty($line)) {
                    echo sprintf("%3d: %s\n", $index + 1, $line);
                }
            }
        }
    }
}

// 명령행 인수 처리
if ($argc > 1) {
    switch ($argv[1]) {
        case 'show':
            $lines = isset($argv[2]) ? intval($argv[2]) : 20;
            LogViewer::showAllLogs($lines);
            break;
        case 'monitor':
            LogViewer::monitorLogs();
            break;
        case 'search':
            $keyword = $argv[2] ?? '';
            $lines = isset($argv[3]) ? intval($argv[3]) : 50;
            if (empty($keyword)) {
                echo "사용법: php view_logs.php search [키워드] [줄수]\n";
            } else {
                LogViewer::searchLogs($keyword, $lines);
            }
            break;
        default:
            echo "사용법: php view_logs.php [show|monitor|search] [옵션]\n";
            echo "  show [줄수]           - 모든 로그 보기 (기본: 20줄)\n";
            echo "  monitor              - 실시간 로그 모니터링\n";
            echo "  search [키워드] [줄수] - 로그 검색\n";
    }
} else {
    // 기본: 로그 현황 보기
    LogViewer::showAllLogs(20);
}
?>