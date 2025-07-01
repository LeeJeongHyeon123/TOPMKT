<?php
/**
 * 🚨 PHP 오류 즉시 진단 도구
 * debug_fixed.php에서 탭이 렌더링되지 않는 문제 해결
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

// 출력 버퍼링 완전 비활성화
while (ob_get_level()) {
    ob_end_clean();
}
ob_implicit_flush(true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🚨 PHP 오류 진단</title>
    <style>
        body { font-family: monospace; background: #000; color: #0f0; padding: 20px; margin: 0; }
        .error { color: #f00; background: #330; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { color: #0f0; }
        .warning { color: #fa0; }
        .info { color: #4af; }
        pre { background: #111; padding: 15px; border-radius: 5px; overflow-x: auto; white-space: pre-wrap; }
        h1 { color: #f60; text-align: center; }
        .section { border: 1px solid #333; margin: 20px 0; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>

<h1>🚨 PHP 오류 진단 도구</h1>

<div class="section">
    <h2>1️⃣ debug_fixed.php 파일 문법 검사</h2>
    <pre>
<?php
echo "=== PHP 파일 문법 검사 ===\n";
flush();

$file_path = __DIR__ . '/debug_fixed.php';
if (!file_exists($file_path)) {
    echo "<span class='error'>❌ debug_fixed.php 파일을 찾을 수 없습니다</span>\n";
    exit;
}

echo "<span class='success'>✅ debug_fixed.php 파일 존재</span>\n";
echo "파일 크기: " . number_format(filesize($file_path)) . " 바이트\n";
flush();

// PHP 문법 검사
echo "\n=== 문법 검사 실행 ===\n";
$output = [];
$return_code = 0;
exec("php -l " . escapeshellarg($file_path) . " 2>&1", $output, $return_code);

if ($return_code === 0) {
    echo "<span class='success'>✅ PHP 문법 검사 통과</span>\n";
} else {
    echo "<span class='error'>❌ PHP 문법 오류 발견:</span>\n";
    foreach ($output as $line) {
        echo "<span class='error'>🔴 $line</span>\n";
    }
}
flush();
?>
    </pre>
</div>

<div class="section">
    <h2>2️⃣ 실제 실행 시 오류 캐치</h2>
    <pre>
<?php
echo "=== 실제 실행 테스트 ===\n";
flush();

// 오류 핸들러 설정
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    echo "<span class='error'>🔴 PHP 오류 캐치:</span>\n";
    echo "   타입: $errno\n";
    echo "   메시지: $errstr\n";
    echo "   파일: $errfile:$errline\n";
    flush();
    return false; // 기본 오류 처리도 실행
}

set_error_handler('customErrorHandler');

try {
    echo "debug_fixed.php 내용 일부 실행 테스트...\n";
    flush();
    
    // 데이터베이스 연결 테스트 (debug_fixed.php에서 사용하는 부분)
    echo "\n=== 데이터베이스 연결 테스트 ===\n";
    $mysqli = new mysqli('localhost', 'root', 'Dnlszkem1!', 'topmkt');
    
    if ($mysqli->connect_error) {
        echo "<span class='warning'>⚠️ DB 연결 실패: {$mysqli->connect_error}</span>\n";
        echo "이것이 탭 렌더링을 중단시킬 수 있습니다.\n";
    } else {
        echo "<span class='success'>✅ 데이터베이스 연결 성공</span>\n";
        $mysqli->close();
    }
    flush();
    
    // 시스템 함수 테스트
    echo "\n=== 시스템 함수 테스트 ===\n";
    
    // sys_getloadavg 함수 테스트
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        echo "<span class='success'>✅ sys_getloadavg() 작동</span>\n";
    } else {
        echo "<span class='warning'>⚠️ sys_getloadavg() 함수 없음</span>\n";
    }
    
    // 디스크 함수 테스트
    $disk_free = disk_free_space(__DIR__);
    $disk_total = disk_total_space(__DIR__);
    if ($disk_free && $disk_total) {
        echo "<span class='success'>✅ 디스크 함수 작동</span>\n";
    } else {
        echo "<span class='warning'>⚠️ 디스크 함수 문제</span>\n";
    }
    
    // PHP 확장 테스트
    echo "\n=== PHP 확장 테스트 ===\n";
    $extensions = ['mysqli', 'curl', 'json', 'session', 'mbstring', 'openssl', 'zip'];
    foreach ($extensions as $ext) {
        if (extension_loaded($ext)) {
            echo "<span class='success'>✅ $ext</span>\n";
        } else {
            echo "<span class='error'>❌ $ext (누락)</span>\n";
        }
    }
    flush();
    
} catch (Exception $e) {
    echo "<span class='error'>💥 예외 발생: {$e->getMessage()}</span>\n";
    echo "파일: {$e->getFile()}:{$e->getLine()}\n";
    echo "스택 트레이스:\n{$e->getTraceAsString()}\n";
    flush();
}

restore_error_handler();
?>
    </pre>
</div>

<div class="section">
    <h2>3️⃣ 메모리 및 실행 시간 검사</h2>
    <pre>
<?php
echo "=== 리소스 상태 ===\n";
echo "현재 메모리 사용: " . number_format(memory_get_usage() / 1024 / 1024, 2) . " MB\n";
echo "최대 메모리 사용: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . " MB\n";
echo "메모리 제한: " . ini_get('memory_limit') . "\n";
echo "최대 실행 시간: " . ini_get('max_execution_time') . "초\n";

// 실행 시간 체크
$start_time = microtime(true);
sleep(1); // 1초 대기
$end_time = microtime(true);
$execution_time = $end_time - $start_time;

echo "실제 실행 시간 측정: " . number_format($execution_time, 3) . "초\n";

if ($execution_time > 1.5) {
    echo "<span class='warning'>⚠️ 실행 시간이 예상보다 길어 타임아웃 가능성 있음</span>\n";
} else {
    echo "<span class='success'>✅ 실행 시간 정상</span>\n";
}
flush();
?>
    </pre>
</div>

<div class="section">
    <h2>4️⃣ 해결 방안</h2>
    <div style="color: #fff; padding: 15px;">
        <h3 style="color: #f60;">🔧 즉시 해결 방법:</h3>
        <ol>
            <li><strong>문법 오류</strong>가 발견되면 → 해당 라인 수정</li>
            <li><strong>데이터베이스 연결 실패</strong> → 비필수 기능으로 변경</li>
            <li><strong>누락된 PHP 확장</strong> → 조건부 실행으로 수정</li>
            <li><strong>메모리/시간 초과</strong> → 코드 최적화</li>
        </ol>
        
        <h3 style="color: #f60;">🚀 다음 단계:</h3>
        <p>이 진단 결과를 바탕으로 debug_fixed.php의 문제점을 즉시 수정하겠습니다.</p>
    </div>
</div>

<script>
// 자동 새로고침으로 지속적인 모니터링
setTimeout(function() {
    console.log('🔄 30초 후 자동 새로고침하여 상태 재확인');
}, 30000);
</script>

</body>
</html>