<?php
/**
 * 🔥 debug_fixed.php 최소화 버전
 * 문제 부분을 찾기 위해 단계별로 코드 추가
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
header('Content-Type: text/html; charset=UTF-8');

// 출력 버퍼링 비활성화
if (ob_get_level()) {
    ob_end_clean();
}
ob_implicit_flush(true);

// 디버그 로그 함수
$log_file = __DIR__ . '/debug_ultra.log';
function debug_log($message, $type = 'INFO') {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$type] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

debug_log("=== 최소화 버전 디버깅 시작 ===");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔥 울트라 디버깅 - 최소화 버전</title>
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Courier New', monospace; 
            background: #000; 
            color: #00ff00; 
            margin: 0; 
            padding: 20px; 
            line-height: 1.4; 
        }
        .header { 
            text-align: center; 
            border-bottom: 2px solid #00ff00; 
            padding: 20px; 
            margin-bottom: 30px; 
            background: #001100; 
            border-radius: 8px;
        }
        .tab-container { 
            margin: 20px 0; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 5px; 
        }
        .tab { 
            padding: 12px 20px; 
            background: #333; 
            color: #fff; 
            cursor: pointer; 
            border-radius: 8px; 
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        .tab:hover { 
            background: #555; 
            border-color: #00ff00;
        }
        .tab.active { 
            background: #00ff00; 
            color: #000; 
            font-weight: bold;
        }
        .tab-content { 
            display: none; 
            background: #111; 
            border: 1px solid #333; 
            border-radius: 8px; 
            margin: 20px 0; 
            padding: 20px;
        }
        .tab-content.active { 
            display: block; 
        }
        .log-output { 
            background: #000; 
            color: #0f0; 
            padding: 15px; 
            border-radius: 5px; 
            font-family: 'Courier New', monospace; 
            font-size: 12px; 
            line-height: 1.4; 
            white-space: pre-wrap; 
            overflow-x: auto; 
            max-height: 400px; 
            overflow-y: auto;
            border: 1px solid #333;
        }
        .error { color: #ff4444; background: rgba(255, 68, 68, 0.1); padding: 2px 5px; border-radius: 3px; }
        .warning { color: #ffaa00; background: rgba(255, 170, 0, 0.1); padding: 2px 5px; border-radius: 3px; }
        .success { color: #44ff44; }
        .info { color: #4444ff; }
        .timestamp { color: #888; }
        .btn { 
            background: #00ff00; 
            color: #000; 
            border: none; 
            padding: 10px 20px; 
            border-radius: 5px; 
            cursor: pointer; 
            margin: 5px; 
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn:hover { 
            background: #44ff44; 
            transform: translateY(-2px);
        }
        .btn-danger { background: #ff4444; color: #fff; }
        .btn-warning { background: #ffaa00; color: #000; }
        .section-header {
            background: #00ff00;
            color: #000;
            padding: 10px 15px;
            font-weight: bold;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<!-- 전역 JavaScript 함수들을 맨 먼저 정의 -->
<script>
// 🔥 전역 함수들을 즉시 정의 (HTML 로드 전에)
console.log('🚀 JavaScript 함수 정의 시작...');

// 탭 전환 함수
function switchTab(tabName) {
    console.log('🔄 탭 전환 요청:', tabName);
    
    try {
        // 모든 탭 비활성화
        const allTabs = document.querySelectorAll('.tab');
        console.log('🔍 찾은 탭 수:', allTabs.length);
        allTabs.forEach(tab => {
            tab.classList.remove('active');
        });
        
        // 모든 탭 컨텐츠 숨기기
        const allContents = document.querySelectorAll('.tab-content');
        console.log('🔍 찾은 탭 컨텐츠 수:', allContents.length);
        allContents.forEach(content => {
            content.classList.remove('active');
        });
        
        // 클릭된 탭 활성화
        const clickedTab = event ? event.target : document.querySelector(`[data-tab="${tabName}"]`);
        if (clickedTab) {
            clickedTab.classList.add('active');
            console.log('✅ 탭 활성화:', tabName);
        }
        
        // 해당 탭 컨텐츠 표시
        const content = document.getElementById(tabName);
        if (content) {
            content.classList.add('active');
            console.log('✅ 탭 전환 완료:', tabName);
        } else {
            console.error('❌ 탭 컨텐츠를 찾을 수 없음:', tabName);
            console.log('📋 사용 가능한 탭 ID들:');
            document.querySelectorAll('[id]').forEach(el => {
                if (el.id) console.log('   -', el.id);
            });
        }
    } catch (error) {
        console.error('💥 탭 전환 중 오류:', error);
    }
}

// 콘솔 정리
function clearConsole() {
    const output = document.getElementById('consoleOutput');
    if (output) {
        output.textContent = '콘솔이 정리되었습니다.\n';
    }
    console.log('콘솔 정리 완료');
}

// JavaScript 오류 테스트
function testError() {
    console.log('JavaScript 오류 테스트 시작...');
    try {
        nonExistentFunction();
    } catch (e) {
        console.error('테스트 오류:', e.message);
    }
}

console.log('✅ 모든 JavaScript 함수 정의 완료!');
</script>

<div class="header">
    <h1>🔥 울트라 디버깅 콘솔 - 최소화 버전</h1>
    <p>단계별 코드 추가로 문제 부분 찾기</p>
</div>

<!-- 탭 네비게이션 -->
<div class="tab-container">
    <div class="tab active" data-tab="console" onclick="switchTab('console')">🖥️ 콘솔 로그</div>
    <div class="tab" data-tab="server" onclick="switchTab('server')">🖧 서버 로그</div>
    <div class="tab" data-tab="php" onclick="switchTab('php')">🐘 PHP 상태</div>
    <div class="tab" data-tab="database" onclick="switchTab('database')">🗄️ 데이터베이스</div>
    <div class="tab" data-tab="system" onclick="switchTab('system')">⚙️ 시스템 정보</div>
    <div class="tab" data-tab="actions" onclick="switchTab('actions')">🚀 액션</div>
</div>

<!-- 콘솔 탭 -->
<div id="console" class="tab-content active">
    <div class="section-header">🖥️ 실시간 콘솔 로그</div>
    <div id="consoleOutput" class="log-output">
콘솔 로그 캡처 시작...
JavaScript 오류와 로그가 실시간으로 여기에 표시됩니다.
    </div>
    <button class="btn" onclick="clearConsole()">🗑️ 콘솔 정리</button>
    <button class="btn" onclick="testError()">🧪 오류 테스트</button>
</div>

<!-- 서버 로그 탭 -->
<div id="server" class="tab-content">
    <div class="section-header">🖧 서버 로그 분석</div>
    <div class="log-output">
=== 서버 로그 최소 버전 ===
서버 소프트웨어: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>

시간: <?php echo date('Y-m-d H:i:s'); ?>

    </div>
</div>

<!-- PHP 상태 탭 -->
<div id="php" class="tab-content">
    <div class="section-header">🐘 PHP 환경 분석</div>
    <div class="log-output">
=== PHP 환경 정보 ===
PHP 버전: <?php echo PHP_VERSION; ?>

메모리 사용: <?php echo number_format(memory_get_usage() / 1024 / 1024, 2); ?> MB
메모리 제한: <?php echo ini_get('memory_limit'); ?>

최대 실행: <?php echo ini_get('max_execution_time'); ?>초
서버: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?>

    </div>
</div>

<!-- 데이터베이스 탭 - 단순화 -->
<div id="database" class="tab-content">
    <div class="section-header">🗄️ 데이터베이스 상태</div>
    <div class="log-output">
=== 데이터베이스 연결 테스트 ===
<?php
try {
    echo "연결 시도 중...\n";
    $mysqli = new mysqli('localhost', 'root', 'Dnlszkem1!', 'topmkt');
    
    if ($mysqli->connect_error) {
        echo "❌ 연결 실패: " . $mysqli->connect_error . "\n";
    } else {
        echo "✅ 데이터베이스 연결 성공\n";
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ DB 오류: " . $e->getMessage() . "\n";
}
?>
    </div>
</div>

<!-- 시스템 정보 탭 - 단순화 -->
<div id="system" class="tab-content">
    <div class="section-header">⚙️ 시스템 리소스</div>
    <div class="log-output">
=== 시스템 리소스 정보 ===
운영체제: <?php echo php_uname(); ?>

서버 시간: <?php echo date('Y-m-d H:i:s'); ?>

메모리 사용량: <?php echo number_format(memory_get_usage() / 1024 / 1024, 2); ?> MB
    </div>
</div>

<!-- 액션 탭 -->
<div id="actions" class="tab-content">
    <div class="section-header">🚀 즉시 실행 액션</div>
    <div>
        <h3>시스템 테스트</h3>
        <button class="btn" onclick="testError()">🧪 JavaScript 오류 테스트</button>
        <button class="btn" onclick="clearConsole()">🗑️ 콘솔 정리</button>
        
        <h3>빠른 이동</h3>
        <button class="btn" onclick="window.open('/lectures', '_blank')">📚 강의 페이지 열기</button>
        <button class="btn" onclick="window.open('/', '_blank')">🏠 홈페이지 열기</button>
        <button class="btn btn-danger" onclick="location.reload()">🔄 페이지 새로고침</button>
    </div>
</div>

<script>
// 페이지 로드 완료 후 초기화
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 페이지 로드 완료 - 최소화 버전');
    
    // DOM 요소 존재 확인
    const expectedTabs = ['console', 'server', 'php', 'database', 'system', 'actions'];
    console.log('🔍 탭 요소 존재 확인:');
    expectedTabs.forEach(tabId => {
        const element = document.getElementById(tabId);
        if (element) {
            console.log(`✅ ${tabId} 탭 요소 존재`);
        } else {
            console.error(`❌ ${tabId} 탭 요소 없음`);
        }
    });
    
    // 탭 버튼 존재 확인
    const tabButtons = document.querySelectorAll('.tab[data-tab]');
    console.log(`🔍 탭 버튼 수: ${tabButtons.length}개`);
    
    console.log('🏥 최소화 버전 초기화 완료');
});

console.log('🔥 최소화 버전 JavaScript 완료!');
</script>

</body>
</html>

<?php
debug_log("최소화 버전 디버깅 페이지 완료");
?>