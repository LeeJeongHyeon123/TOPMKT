<?php
/**
 * MySQL 연결 디버깅 스크립트
 * 다양한 연결 옵션을 시도하여 문제를 진단합니다
 */

echo "<h1>🔍 MySQL 연결 진단</h1>";
echo "<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; padding: 20px; }
    .test { margin: 15px 0; padding: 15px; border-radius: 8px; }
    .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
    .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
    .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
    pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

// 연결 설정 옵션들
$connections = [
    [
        'name' => 'root 계정 (비밀번호: Dnlszkem1!)',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'Dnlszkem1!',
        'port' => 3306
    ],
    [
        'name' => 'root 계정 (비밀번호 없음)',
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'port' => 3306
    ],
    [
        'name' => '127.0.0.1 호스트',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'Dnlszkem1!',
        'port' => 3306
    ],
    [
        'name' => '소켓 연결',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'Dnlszkem1!',
        'port' => null,
        'socket' => '/var/lib/mysql/mysql.sock'
    ],
    [
        'name' => '다른 소켓 경로',
        'host' => 'localhost',
        'username' => 'root',
        'password' => 'Dnlszkem1!',
        'port' => null,
        'socket' => '/tmp/mysql.sock'
    ]
];

// PHP 확장 모듈 확인
echo "<div class='test info'>";
echo "<h3>📋 PHP 확장 모듈 확인</h3>";
echo "PDO: " . (extension_loaded('pdo') ? '✅ 설치됨' : '❌ 없음') . "<br>";
echo "PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ 설치됨' : '❌ 없음') . "<br>";
echo "MySQLi: " . (extension_loaded('mysqli') ? '✅ 설치됨' : '❌ 없음') . "<br>";
echo "</div>";

// 각 연결 옵션 테스트
foreach ($connections as $config) {
    echo "<div class='test'>";
    echo "<h3>🔌 {$config['name']} 테스트</h3>";
    
    try {
        // DSN 구성
        $dsn = "mysql:host={$config['host']}";
        if (isset($config['port']) && $config['port']) {
            $dsn .= ";port={$config['port']}";
        }
        if (isset($config['socket'])) {
            $dsn .= ";unix_socket={$config['socket']}";
        }
        $dsn .= ";charset=utf8mb4";
        
        echo "DSN: <pre>$dsn</pre>";
        echo "사용자명: {$config['username']}<br>";
        echo "비밀번호: " . (empty($config['password']) ? '(없음)' : '설정됨') . "<br>";
        
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5
        ]);
        
        echo "<div class='success'>✅ 연결 성공!</div>";
        
        // 서버 정보 확인
        $version = $pdo->query("SELECT VERSION()")->fetchColumn();
        echo "MySQL 버전: $version<br>";
        
        // 데이터베이스 목록 확인
        $stmt = $pdo->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "데이터베이스 목록: " . implode(', ', $databases) . "<br>";
        
        // topmkt 데이터베이스 확인
        if (in_array('topmkt', $databases)) {
            echo "✅ topmkt 데이터베이스 존재<br>";
            
            $pdo->exec("USE topmkt");
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($tables)) {
                echo "⚠️ 테이블이 없음 - 설치 필요<br>";
            } else {
                echo "테이블 목록: " . implode(', ', $tables) . "<br>";
            }
        } else {
            echo "❌ topmkt 데이터베이스 없음<br>";
        }
        
        echo "<br><strong>🎉 이 설정으로 연결 가능합니다!</strong><br>";
        break; // 성공한 연결을 찾으면 종료
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ 연결 실패</div>";
        echo "오류 메시지: " . htmlspecialchars($e->getMessage()) . "<br>";
        
        // 일반적인 해결책 제안
        $errorCode = $e->getCode();
        switch ($errorCode) {
            case 1045:
                echo "<strong>💡 해결책:</strong> 사용자명 또는 비밀번호가 잘못되었습니다.<br>";
                break;
            case 2002:
                echo "<strong>💡 해결책:</strong> MySQL 서버가 실행되지 않거나 연결할 수 없습니다.<br>";
                break;
            case 1049:
                echo "<strong>💡 해결책:</strong> 지정된 데이터베이스가 존재하지 않습니다.<br>";
                break;
            default:
                echo "<strong>💡 해결책:</strong> MySQL 서버 설정을 확인하세요.<br>";
        }
    }
    
    echo "</div>";
}

// 추가 진단 정보
echo "<div class='test info'>";
echo "<h3>🔧 추가 진단 정보</h3>";
echo "PHP 버전: " . phpversion() . "<br>";
echo "서버 소프트웨어: " . ($_SERVER['SERVER_SOFTWARE'] ?? '알 수 없음') . "<br>";
echo "운영체제: " . php_uname() . "<br>";
echo "</div>";

// 권장 조치
echo "<div class='test info'>";
echo "<h3>📋 권장 조치</h3>";
echo "<ol>";
echo "<li>MySQL/MariaDB 서비스가 실행 중인지 확인</li>";
echo "<li>MySQL root 계정의 비밀번호 확인</li>";
echo "<li>MySQL 포트 3306이 열려있는지 확인</li>";
echo "<li>방화벽 설정 확인</li>";
echo "<li>MySQL 설정 파일(/etc/mysql/my.cnf) 확인</li>";
echo "</ol>";
echo "</div>";

echo "<p><a href='/public/install.php'>설치 페이지로 이동</a></p>";
?> 