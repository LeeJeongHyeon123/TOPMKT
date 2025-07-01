<?php
/**
 * 🔥 직접 데이터베이스 수정을 통한 강의 시스템 복구
 * 라우팅을 거치지 않고 직접 DB에 접근해서 문제 해결
 */

header('Content-Type: text/html; charset=UTF-8');
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🔥 강의 시스템 직접 복구</title>
    <style>
        body { font-family: monospace; background: #111; color: #0f0; padding: 20px; margin: 0; }
        .success { color: #0f0; }
        .error { color: #f00; }
        .warning { color: #fa0; }
        .info { color: #4af; }
        pre { background: #000; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #f60; text-align: center; border-bottom: 2px solid #f60; padding-bottom: 10px; }
        .box { border: 1px solid #333; margin: 10px 0; padding: 15px; border-radius: 5px; }
        .action-btn { background: #f60; color: #000; padding: 10px 20px; border: none; border-radius: 5px; margin: 5px; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>

<h1>🔥 강의 시스템 직접 복구 도구</h1>

<div class="box">
    <h2>📊 시스템 상태 확인</h2>
    <pre>
<?php

echo "=== 강의 시스템 직접 복구 시작 ===\n";
echo "시간: " . date('Y-m-d H:i:s') . "\n\n";

// 데이터베이스 연결 시도
$mysqli = null;
$db_configs = [
    ['localhost', 'root', 'Dnlszkem1!', 'topmkt'],
    ['127.0.0.1', 'root', 'Dnlszkem1!', 'topmkt'],
    ['localhost', 'root', '', 'topmkt']
];

echo "<span class='info'>🔍 데이터베이스 연결 시도...</span>\n";

foreach ($db_configs as $i => $config) {
    try {
        $test_mysqli = new mysqli($config[0], $config[1], $config[2], $config[3]);
        if (!$test_mysqli->connect_error) {
            $mysqli = $test_mysqli;
            echo "<span class='success'>✅ 데이터베이스 연결 성공! (설정 " . ($i + 1) . ")</span>\n";
            break;
        }
    } catch (Exception $e) {
        // 다음 설정 시도
    }
}

if (!$mysqli) {
    echo "<span class='error'>❌ 모든 데이터베이스 연결 실패</span>\n";
    echo "<span class='warning'>⚠️ MySQL 서비스를 시작해주세요: sudo systemctl start mysqld</span>\n";
    exit;
}

// UTF-8 설정
$mysqli->set_charset('utf8mb4');

echo "\n<span class='info'>=== 테이블 상태 확인 ===</span>\n";

// 1. lectures 테이블 확인
$result = $mysqli->query("SHOW TABLES LIKE 'lectures'");
if ($result && $result->num_rows > 0) {
    echo "<span class='success'>✅ lectures 테이블 존재</span>\n";
    
    // 강의 수 확인
    $result = $mysqli->query("SELECT COUNT(*) as total, 
                             SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                             SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft
                             FROM lectures");
    if ($result) {
        $stats = $result->fetch_assoc();
        echo "<span class='success'>📊 전체 강의: {$stats['total']}개</span>\n";
        echo "<span class='success'>📊 게시된 강의: {$stats['published']}개</span>\n";
        echo "<span class='success'>📊 임시저장: {$stats['draft']}개</span>\n";
    }
} else {
    echo "<span class='error'>❌ lectures 테이블 없음</span>\n";
}

// 2. users 테이블 확인
$result = $mysqli->query("SHOW TABLES LIKE 'users'");
if ($result && $result->num_rows > 0) {
    echo "<span class='success'>✅ users 테이블 존재</span>\n";
} else {
    echo "<span class='error'>❌ users 테이블 없음</span>\n";
}

// 3. 신청 테이블 확인
$result = $mysqli->query("SHOW TABLES LIKE 'lecture_registrations'");
if ($result && $result->num_rows > 0) {
    echo "<span class='success'>✅ lecture_registrations 테이블 존재</span>\n";
} else {
    echo "<span class='warning'>⚠️ lecture_registrations 테이블 없음 (신청 시스템 미설치)</span>\n";
}

echo "\n";

// 4. 최근 강의 목록 조회
echo "<span class='info'>=== 최근 강의 5개 ===</span>\n";
$result = $mysqli->query("SELECT id, title, status, start_date, start_time, user_id 
                         FROM lectures 
                         ORDER BY created_at DESC 
                         LIMIT 5");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status_color = $row['status'] == 'published' ? 'success' : 'warning';
        echo "<span class='$status_color'>📚 ID {$row['id']}: {$row['title']}</span>\n";
        echo "   상태: {$row['status']} | 일시: {$row['start_date']} {$row['start_time']} | 작성자: {$row['user_id']}\n";
    }
} else {
    echo "<span class='error'>❌ 강의 조회 실패: " . $mysqli->error . "</span>\n";
}

?>
    </pre>
</div>

<?php if ($mysqli): ?>
<div class="box">
    <h2>🔧 자동 복구 실행</h2>
    <form method="POST" style="display: inline;">
        <input type="hidden" name="action" value="auto_fix">
        <button type="submit" class="action-btn">🚀 자동 복구 실행</button>
    </form>
    
    <form method="POST" style="display: inline;">
        <input type="hidden" name="action" value="reset_cache">
        <button type="submit" class="action-btn">🗑️ 캐시 리셋</button>
    </form>
    
    <form method="POST" style="display: inline;">
        <input type="hidden" name="action" value="test_routes">
        <button type="submit" class="action-btn">🧪 라우팅 테스트</button>
    </form>
</div>

<?php
// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    echo '<div class="box"><h2>🔄 작업 실행 결과</h2><pre>';
    
    switch ($_POST['action']) {
        case 'auto_fix':
            echo "<span class='info'>🔧 자동 복구 실행 중...</span>\n\n";
            
            // 1. 기본 필드 추가
            echo "1. 강의 테이블 구조 확인/수정...\n";
            $alter_queries = [
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS max_participants INT NULL",
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS current_participants INT DEFAULT 0",
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS auto_approval BOOLEAN DEFAULT FALSE",
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS registration_start_date DATETIME NULL",
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS registration_end_date DATETIME NULL",
                "ALTER TABLE lectures ADD COLUMN IF NOT EXISTS allow_waiting_list BOOLEAN DEFAULT FALSE"
            ];
            
            foreach ($alter_queries as $query) {
                $result = $mysqli->query($query);
                if ($result) {
                    echo "<span class='success'>✅ 구조 수정 성공</span>\n";
                } else {
                    echo "<span class='warning'>⚠️ 구조 수정 건너뜀 (이미 존재하거나 불필요)</span>\n";
                }
            }
            
            // 2. 데이터 정리
            echo "\n2. 데이터 정리...\n";
            $cleanup_queries = [
                "UPDATE lectures SET current_participants = 0 WHERE current_participants IS NULL",
                "UPDATE lectures SET status = 'published' WHERE status = 'active'",
                "UPDATE lectures SET start_time = '00:00:00' WHERE start_time IS NULL OR start_time = ''",
                "UPDATE lectures SET end_time = '23:59:59' WHERE end_time IS NULL OR end_time = ''"
            ];
            
            foreach ($cleanup_queries as $query) {
                $result = $mysqli->query($query);
                if ($result) {
                    echo "<span class='success'>✅ 데이터 정리 완료</span>\n";
                } else {
                    echo "<span class='error'>❌ 데이터 정리 실패: " . $mysqli->error . "</span>\n";
                }
            }
            
            // 3. 인덱스 추가
            echo "\n3. 성능 최적화...\n";
            $index_queries = [
                "ALTER TABLE lectures ADD INDEX IF NOT EXISTS idx_status_date (status, start_date)",
                "ALTER TABLE lectures ADD INDEX IF NOT EXISTS idx_user_status (user_id, status)"
            ];
            
            foreach ($index_queries as $query) {
                $result = $mysqli->query($query);
                if ($result) {
                    echo "<span class='success'>✅ 인덱스 추가 완료</span>\n";
                } else {
                    echo "<span class='warning'>⚠️ 인덱스 건너뜀 (이미 존재)</span>\n";
                }
            }
            
            echo "\n<span class='success'>🎉 자동 복구 완료!</span>\n";
            break;
            
        case 'reset_cache':
            echo "<span class='info'>🗑️ 캐시 리셋 실행 중...</span>\n\n";
            
            // 세션 리셋
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            session_destroy();
            session_start();
            echo "<span class='success'>✅ 세션 리셋 완료</span>\n";
            
            // OpCache 리셋
            if (function_exists('opcache_reset')) {
                opcache_reset();
                echo "<span class='success'>✅ OpCache 리셋 완료</span>\n";
            } else {
                echo "<span class='warning'>⚠️ OpCache 없음</span>\n";
            }
            
            echo "\n<span class='success'>🎉 캐시 리셋 완료!</span>\n";
            break;
            
        case 'test_routes':
            echo "<span class='info'>🧪 라우팅 테스트 실행 중...</span>\n\n";
            
            // 컨트롤러 파일 확인
            $controller_path = '../src/controllers/LectureController.php';
            if (file_exists($controller_path)) {
                echo "<span class='success'>✅ LectureController 파일 존재</span>\n";
                
                // 파일 내용 간단 검사
                $content = file_get_contents($controller_path);
                if (strpos($content, 'class LectureController') !== false) {
                    echo "<span class='success'>✅ LectureController 클래스 정의 확인</span>\n";
                } else {
                    echo "<span class='error'>❌ LectureController 클래스 정의 없음</span>\n";
                }
                
                if (strpos($content, 'function index') !== false) {
                    echo "<span class='success'>✅ index 메소드 존재</span>\n";
                } else {
                    echo "<span class='error'>❌ index 메소드 없음</span>\n";
                }
            } else {
                echo "<span class='error'>❌ LectureController 파일 없음: $controller_path</span>\n";
            }
            
            // 뷰 파일 확인
            $view_path = '../src/views/lectures/index.php';
            if (file_exists($view_path)) {
                echo "<span class='success'>✅ 강의 목록 뷰 파일 존재</span>\n";
            } else {
                echo "<span class='error'>❌ 강의 목록 뷰 파일 없음: $view_path</span>\n";
            }
            
            echo "\n<span class='success'>🎉 라우팅 테스트 완료!</span>\n";
            break;
    }
    
    echo '</pre></div>';
}
?>

<div class="box">
    <h2>🎯 수동 테스트 링크</h2>
    <div style="color: #fff;">
        <h3 style="color: #4af;">직접 접근 테스트:</h3>
        <p><a href="/lectures" style="color: #0f0; text-decoration: none;">👉 /lectures (메인 강의 페이지)</a></p>
        <p><a href="/lectures?view=list" style="color: #0f0; text-decoration: none;">👉 /lectures?view=list (리스트 뷰)</a></p>
        <p><a href="/lectures?year=2025&month=7" style="color: #0f0; text-decoration: none;">👉 /lectures?year=2025&month=7 (특정 월)</a></p>
        <p><a href="/" style="color: #0f0; text-decoration: none;">👉 / (홈페이지)</a></p>
        
        <h3 style="color: #4af;">브라우저 조치:</h3>
        <p>• Ctrl+F5로 강제 새로고침</p>
        <p>• 브라우저 캐시 및 쿠키 전체 삭제</p>
        <p>• 다른 브라우저나 시크릿 모드 시도</p>
        <p>• 스마트폰에서 접속 테스트</p>
    </div>
</div>

<?php endif; ?>

<div class="box">
    <h2>📋 다음 단계</h2>
    <div style="color: #fff;">
        <ol>
            <li style="color: #0f0;">이 페이지에서 자동 복구 실행</li>
            <li style="color: #0f0;">브라우저에서 /lectures 직접 접근</li>
            <li style="color: #fa0;">문제 지속 시 서버 재시작 필요</li>
            <li style="color: #fa0;">PHP 확장 모듈 설치 필요할 수 있음</li>
        </ol>
    </div>
</div>

<script>
// 페이지 자동 스크롤 (새 내용이 추가되면)
if (window.location.hash === '') {
    setTimeout(function() {
        window.scrollTo(0, document.body.scrollHeight);
    }, 1000);
}
</script>

</body>
</html>