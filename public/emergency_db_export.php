<?php
/**
 * 긴급 데이터베이스 백업 스크립트
 * 웹을 통해 데이터베이스를 SQL 파일로 내보냅니다.
 * 
 * 사용법: 브라우저에서 http://your-domain.com/emergency_db_export.php 접속
 * 
 * ⚠️ 주의: 백업 완료 후 반드시 이 파일을 삭제하세요!
 */

// 보안 검사 - 실제 운영 시에는 더 강력한 인증 필요
$allowed_ips = ['127.0.0.1', '::1']; // 로컬에서만 접근 허용
if (!in_array($_SERVER['REMOTE_ADDR'], $allowed_ips) && !isset($_GET['secret_key'])) {
    die('Access denied. Add ?secret_key=your_secret_key to URL');
}

// 경로 설정
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');

// 시간 제한 해제 (대용량 DB 백업 시 필요)
set_time_limit(0);
ini_set('memory_limit', '512M');

// 헤더 설정
$filename = 'topmkt_backup_' . date('Ymd_His') . '.sql';
header('Content-Type: text/plain; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

try {
    // 데이터베이스 연결
    require_once SRC_PATH . '/config/database.php';
    $db = Database::getInstance();
    
    echo "-- 탑마케팅 데이터베이스 백업\n";
    echo "-- 생성일: " . date('Y-m-d H:i:s') . "\n";
    echo "-- PHP 버전: " . PHP_VERSION . "\n";
    echo "-- ================================================\n\n";
    
    echo "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    echo "SET time_zone = \"+00:00\";\n";
    echo "SET NAMES utf8mb4;\n\n";
    
    // 데이터베이스 정보
    $dbInfo = $db->query("SELECT DATABASE() as db")->fetch();
    echo "-- 데이터베이스: " . $dbInfo['db'] . "\n\n";
    
    // 모든 테이블 가져오기
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "-- ========================================\n";
        echo "-- 테이블: $table\n";
        echo "-- ========================================\n\n";
        
        // 테이블 삭제
        echo "DROP TABLE IF EXISTS `$table`;\n\n";
        
        // 테이블 생성 구문
        $createTable = $db->query("SHOW CREATE TABLE `$table`")->fetch();
        echo $createTable['Create Table'] . ";\n\n";
        
        // 데이터 개수 확인
        $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
        echo "-- 데이터 개수: $count\n\n";
        
        if ($count > 0) {
            // 컬럼 정보 가져오기
            $columns = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_COLUMN);
            
            // 데이터 내보내기 (배치 처리)
            $batchSize = 1000;
            $offset = 0;
            
            while ($offset < $count) {
                $stmt = $db->query("SELECT * FROM `$table` LIMIT $offset, $batchSize");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if (!empty($rows)) {
                    echo "INSERT INTO `$table` (`" . implode('`, `', $columns) . "`) VALUES\n";
                    
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } elseif (is_numeric($value)) {
                                $rowValues[] = $value;
                            } else {
                                // 문자열 이스케이프
                                $escaped = str_replace(
                                    ['\\', "'", '"', "\n", "\r", "\t"],
                                    ['\\\\', "''", '\"', '\\n', '\\r', '\\t'],
                                    $value
                                );
                                $rowValues[] = "'" . $escaped . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    
                    echo implode(",\n", $values) . ";\n\n";
                }
                
                $offset += $batchSize;
                
                // 메모리 정리
                unset($rows, $values);
            }
        }
        
        echo "\n";
    }
    
    echo "-- ========================================\n";
    echo "-- 백업 완료\n";
    echo "-- ========================================\n";
    
} catch (Exception $e) {
    echo "-- 오류 발생: " . $e->getMessage() . "\n";
    error_log("DB Export Error: " . $e->getMessage());
}

// 백업 로그 기록
error_log("Database backup created: $filename from IP: " . $_SERVER['REMOTE_ADDR']);