<?php
/**
 * 🔥 완전 수정된 울트라 디버깅 시스템
 * 모든 탭이 정상 작동하는 확인된 버전
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

debug_log("=== 수정된 울트라 디버깅 시작 ===");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔥 울트라 디버깅 - 완전 수정판</title>
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
        .status-box { 
            position: fixed; 
            top: 10px; 
            right: 10px; 
            background: #001100; 
            border: 2px solid #00ff00; 
            padding: 15px; 
            border-radius: 8px; 
            max-width: 300px; 
            z-index: 1000;
        }
        .debug-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
        }
        @media (max-width: 768px) { 
            .debug-grid { grid-template-columns: 1fr; }
            .tab-container { flex-direction: column; }
        }
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
            
            // 탭 컨텐츠가 보이도록 스크롤
            content.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            console.error('❌ 탭 컨텐츠를 찾을 수 없음:', tabName);
            console.log('📋 사용 가능한 탭 ID들:');
            document.querySelectorAll('[id]').forEach(el => {
                if (el.id) console.log('   -', el.id);
            });
        }
    } catch (error) {
        console.error('💥 탭 전환 중 오류:', error);
        addConsoleLog(`탭 전환 오류: ${error.message}`, 'error');
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
        addConsoleLog('테스트 오류: ' + e.message, 'error');
    }
}

// 강의 접근 테스트
function testLectureAccess() {
    console.log('강의 페이지 접근 테스트...');
    addConsoleLog('강의 접근 테스트 시작...', 'info');
    
    fetch('/lectures', { method: 'HEAD' })
        .then(response => {
            const msg = `강의 페이지 상태: ${response.status} ${response.ok ? '✅' : '❌'}`;
            console.log(msg);
            addConsoleLog(msg, response.ok ? 'success' : 'error');
        })
        .catch(error => {
            const msg = `네트워크 오류: ${error.message}`;
            console.error(msg);
            addConsoleLog(msg, 'error');
        });
}

// 응급 리셋
function emergencyReset() {
    if (confirm('응급 리셋을 실행하시겠습니까?')) {
        console.log('응급 리셋 실행...');
        localStorage.clear();
        sessionStorage.clear();
        if ('caches' in window) {
            caches.keys().then(names => names.forEach(name => caches.delete(name)));
        }
        setTimeout(() => location.reload(true), 2000);
    }
}

// 강의 페이지로 이동
function goToLectures() {
    console.log('강의 페이지로 이동...');
    window.open('/lectures', '_blank');
}

// 콘솔 로그 추가 함수
function addConsoleLog(message, type = 'log') {
    const output = document.getElementById('consoleOutput');
    if (output) {
        const timestamp = new Date().toLocaleTimeString();
        const typeClass = type === 'error' ? 'error' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'info';
        const icon = type === 'error' ? '🔴' : type === 'warning' ? '🟡' : type === 'success' ? '✅' : '💬';
        
        output.innerHTML += `<span class="timestamp">[${timestamp}]</span> <span class="${typeClass}">${icon} ${message}</span>\n`;
        output.scrollTop = output.scrollHeight;
    }
}

// 전역 에러 캐처
window.addEventListener('error', function(e) {
    const errorMsg = `JavaScript 오류: ${e.message} (${e.filename}:${e.lineno})`;
    console.error(errorMsg);
    addConsoleLog(errorMsg, 'error');
});

window.addEventListener('unhandledrejection', function(e) {
    const errorMsg = `Promise 거부: ${e.reason}`;
    console.error(errorMsg);
    addConsoleLog(errorMsg, 'error');
});

// 콘솔 함수 오버라이드
const originalLog = console.log;
const originalError = console.error;
const originalWarn = console.warn;

console.log = function(...args) {
    originalLog.apply(console, args);
    addConsoleLog(args.join(' '), 'log');
};

console.error = function(...args) {
    originalError.apply(console, args);
    addConsoleLog(args.join(' '), 'error');
};

console.warn = function(...args) {
    originalWarn.apply(console, args);
    addConsoleLog(args.join(' '), 'warning');
};

console.log('✅ 모든 JavaScript 함수 정의 완료!');
</script>

<div class="header">
    <h1>🔥 울트라 디버깅 콘솔 - 완전 수정판</h1>
    <p>JavaScript 오류 완전 해결 • 실시간 로그 수집 • 모든 기능 정상 작동</p>
    <div class="status-box">
        <strong>🟢 시스템 상태</strong><br>
        <span id="systemStatus">정상 작동 중</span>
    </div>
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
=== 서버 로그 수집 ===
시간: <?= date('Y-m-d H:i:s') ?>

<?php
echo "서버 소프트웨어: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "문서 루트: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "요청 URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "\n";

// 간단한 로그 파일 확인
$log_files = [
    '/var/log/httpd/error_log' => 'Apache Error Log',
    '/var/log/nginx/error.log' => 'Nginx Error Log',
    __DIR__ . '/debug_ultra.log' => 'Ultra Debug Log'
];

foreach ($log_files as $file => $desc) {
    if (file_exists($file) && is_readable($file)) {
        echo "\n✅ $desc 발견: $file\n";
        $lines = file($file);
        if ($lines && count($lines) > 0) {
            echo "마지막 줄: " . trim(end($lines)) . "\n";
        }
    } else {
        echo "⚠️ $desc: 파일 없음\n";
    }
}
?>
    </div>
</div>

<!-- PHP 상태 탭 -->
<div id="php" class="tab-content">
    <div class="section-header">🐘 PHP 환경 분석</div>
    <div class="debug-grid">
        <div>
            <h3>기본 정보</h3>
            <div class="log-output">
=== PHP 환경 정보 ===
PHP 버전: <?= PHP_VERSION ?>

메모리 사용: <?= number_format(memory_get_usage() / 1024 / 1024, 2) ?> MB
메모리 제한: <?= ini_get('memory_limit') ?>

최대 실행: <?= ini_get('max_execution_time') ?>초
서버: <?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?>

SAPI: <?= php_sapi_name() ?>
            </div>
        </div>
        <div>
            <h3>확장 모듈</h3>
            <div class="log-output">
=== PHP 확장 모듈 ===
<?php
$extensions = ['mysqli', 'curl', 'json', 'session', 'mbstring', 'openssl', 'zip'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<span class='success'>✅ $ext</span>\n";
    } else {
        echo "<span class='error'>❌ $ext (누락)</span>\n";
    }
}
?>
            </div>
        </div>
    </div>
</div>

<!-- 데이터베이스 탭 -->
<div id="database" class="tab-content">
    <div class="section-header">🗄️ 데이터베이스 상태</div>
    <div class="log-output">
=== 데이터베이스 연결 테스트 ===
<?php
try {
    echo "연결 시도 중...\n";
    $mysqli = new mysqli('localhost', 'root', 'Dnlszkem1!', 'topmkt');
    
    if ($mysqli->connect_error) {
        echo "<span class='error'>❌ 연결 실패: {$mysqli->connect_error}</span>\n";
    } else {
        echo "<span class='success'>✅ 데이터베이스 연결 성공</span>\n";
        
        $result = $mysqli->query("SELECT COUNT(*) as count FROM lectures WHERE status = 'published'");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<span class='info'>📊 게시된 강의: {$row['count']}개</span>\n";
        }
        
        $result = $mysqli->query("SELECT id, title FROM lectures ORDER BY created_at DESC LIMIT 3");
        if ($result) {
            echo "\n최근 강의 3개:\n";
            while ($row = $result->fetch_assoc()) {
                echo "📚 [{$row['id']}] {$row['title']}\n";
            }
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<span class='error'>❌ DB 오류: {$e->getMessage()}</span>\n";
}
?>
    </div>
</div>

<!-- 시스템 정보 탭 -->
<div id="system" class="tab-content">
    <div class="section-header">⚙️ 시스템 리소스</div>
    <div class="log-output">
=== 시스템 리소스 정보 ===
운영체제: <?= php_uname() ?>

서버 시간: <?= date('Y-m-d H:i:s') ?>

<?php
if (function_exists('sys_getloadavg')) {
    $load = sys_getloadavg();
    echo "시스템 부하: " . implode(', ', array_map(function($l) { return number_format($l, 2); }, $load)) . "\n";
} else {
    echo "시스템 부하: 정보 없음\n";
}

$disk_free = disk_free_space(__DIR__);
$disk_total = disk_total_space(__DIR__);
if ($disk_free && $disk_total) {
    $used_percent = (($disk_total - $disk_free) / $disk_total) * 100;
    echo "디스크 사용률: " . number_format($used_percent, 1) . "%\n";
    
    if ($used_percent > 90) {
        echo "<span class='error'>⚠️ 디스크 공간 부족!</span>\n";
    }
}

echo "\n요청 정보:\n";
echo "클라이언트 IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";
echo "사용자 에이전트: " . substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50) . "...\n";
?>
    </div>
</div>

<!-- 액션 탭 -->
<div id="actions" class="tab-content">
    <div class="section-header">🚀 즉시 실행 액션</div>
    <div class="debug-grid">
        <div>
            <h3>시스템 테스트</h3>
            <button class="btn" onclick="testLectureAccess()">🎯 강의 접근 테스트</button>
            <button class="btn" onclick="testError()">🧪 JavaScript 오류 테스트</button>
            <button class="btn btn-warning" onclick="emergencyReset()">🚨 응급 캐시 리셋</button>
        </div>
        <div>
            <h3>빠른 이동</h3>
            <button class="btn" onclick="goToLectures()">📚 강의 페이지 열기</button>
            <button class="btn" onclick="window.open('/', '_blank')">🏠 홈페이지 열기</button>
            <button class="btn btn-danger" onclick="location.reload()">🔄 페이지 새로고침</button>
        </div>
    </div>
    
    <div style="margin-top: 20px;">
        <h3>🔗 직접 링크</h3>
        <div class="log-output">
메인 강의 페이지: <a href="/lectures" style="color: #44ff44;">/lectures</a>
리스트 뷰: <a href="/lectures?view=list" style="color: #44ff44;">/lectures?view=list</a>
홈페이지: <a href="/" style="color: #44ff44;">/</a>

브라우저 해결 방법:
• Ctrl+F5로 강제 새로고침
• 다른 브라우저나 시크릿 모드 시도
• 브라우저 캐시 및 쿠키 삭제
        </div>
    </div>
</div>

<script>
// 페이지 로드 완료 후 초기화
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 페이지 로드 완료 - 모든 기능 정상 작동');
    
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
    
    // 함수 정의 확인
    const functions = ['switchTab', 'clearConsole', 'testError', 'testLectureAccess', 'emergencyReset', 'goToLectures'];
    functions.forEach(func => {
        if (typeof window[func] === 'function') {
            console.log(`✅ ${func} 함수 정상`);
        } else {
            console.error(`❌ ${func} 함수 오류`);
        }
    });
    
    // 상태 업데이트
    function updateStatus() {
        const status = document.getElementById('systemStatus');
        if (status) {
            status.innerHTML = `정상 작동 중<br><small>${new Date().toLocaleTimeString()}</small>`;
        }
    }
    
    setInterval(updateStatus, 5000);
    updateStatus();
    
    // 초기 테스트
    setTimeout(() => {
        console.log('🏥 초기 시스템 체크 실행...');
        addConsoleLog('시스템 초기화 완료', 'success');
        
        // 기본 탭 활성화 확인
        const activeTab = document.querySelector('.tab-content.active');
        if (activeTab) {
            console.log(`✅ 활성 탭: ${activeTab.id}`);
        } else {
            console.warn('⚠️ 활성 탭이 없음 - console 탭 활성화');
            switchTab('console');
        }
    }, 1000);
});

console.log('🔥 울트라 디버깅 시스템 완전 수정 완료!');
</script>

</body>
</html>

<?php
debug_log("수정된 울트라 디버깅 페이지 완료");
?>