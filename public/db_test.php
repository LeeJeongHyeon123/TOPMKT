<?php
/**
 * 데이터베이스 연결 테스트 스크립트
 */

try {
    $dsn = 'mysql:host=localhost;charset=utf8mb4';
    $username = 'root';
    $password = 'Dnlszkem1!';
    
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ MySQL 서버 연결 성공!<br>";
    
    // topmkt 데이터베이스 존재 확인
    $stmt = $pdo->query("SHOW DATABASES LIKE 'topmkt'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "✅ topmkt 데이터베이스 존재함<br>";
        
        // topmkt 데이터베이스에 연결
        $pdo->exec("USE topmkt");
        
        // 테이블 확인
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            echo "⚠️ topmkt 데이터베이스에 테이블이 없습니다.<br>";
            echo "📋 install.php를 실행해서 테이블을 생성하세요.<br>";
        } else {
            echo "✅ 테이블 목록:<br>";
            foreach ($tables as $table) {
                echo "  - " . htmlspecialchars($table) . "<br>";
            }
        }
    } else {
        echo "❌ topmkt 데이터베이스가 존재하지 않습니다.<br>";
        echo "📋 데이터베이스를 생성합니다...<br>";
        
        $pdo->exec("CREATE DATABASE topmkt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "✅ topmkt 데이터베이스가 생성되었습니다!<br>";
        echo "📋 이제 install.php를 실행해서 테이블을 생성하세요.<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ 데이터베이스 연결 실패: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "🔧 확인사항:<br>";
    echo "  - MySQL/MariaDB 서버가 실행 중인지 확인<br>";
    echo "  - 사용자명과 비밀번호가 정확한지 확인<br>";
    echo "  - 포트 3306이 열려있는지 확인<br>";
}

echo "<br><a href='/install.php'>install.php 실행하기</a>";
echo "<br><a href='/auth/signup'>회원가입 페이지 가기</a>";
?> 