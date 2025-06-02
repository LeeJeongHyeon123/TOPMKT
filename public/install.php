<?php
/**
 * 탑마케팅 프로젝트 데이터베이스 설치 스크립트
 * 
 * 이 스크립트는 다음 작업을 수행합니다:
 * 1. 데이터베이스 연결 테스트
 * 2. 필요한 테이블 생성
 * 3. 초기 데이터 삽입
 * 4. 관리자 계정 생성
 */

// 경로 설정
define('ROOT_PATH', dirname(__DIR__));
define('SRC_PATH', ROOT_PATH . '/src');

// 설정 로드
require_once SRC_PATH . '/config/config.php';

// 설치 상태 확인
$isInstalled = false;
$installError = '';
$installSuccess = '';

/**
 * 데이터베이스 연결 함수
 */
function createDbConnection() {
    try {
        $dsn = 'mysql:host=localhost;charset=utf8mb4';
        $pdo = new PDO($dsn, 'root', 'Dnlszkem1!');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception('MySQL 서버 연결 실패: ' . $e->getMessage());
    }
}

/**
 * 데이터베이스 생성
 */
function createDatabase($pdo) {
    $sql = "CREATE DATABASE IF NOT EXISTS topmkt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    $pdo->exec("USE topmkt");
}

/**
 * 테이블 생성
 */
function createTables($pdo) {
    $tables = [
        // users 테이블
        "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nickname VARCHAR(50) NOT NULL UNIQUE COMMENT '닉네임',
            phone VARCHAR(20) NOT NULL UNIQUE COMMENT '휴대폰 번호',
            email VARCHAR(100) NOT NULL UNIQUE COMMENT '이메일',
            password_hash VARCHAR(255) NOT NULL COMMENT '암호화된 비밀번호',
            marketing_agreed BOOLEAN DEFAULT FALSE COMMENT '마케팅 수신 동의',
            phone_verified BOOLEAN DEFAULT FALSE COMMENT '휴대폰 인증 여부',
            email_verified BOOLEAN DEFAULT FALSE COMMENT '이메일 인증 여부',
            login_attempts INT DEFAULT 0 COMMENT '로그인 시도 횟수',
            locked_until TIMESTAMP NULL COMMENT '계정 잠금 해제 시간',
            last_login TIMESTAMP NULL COMMENT '마지막 로그인',
            status ENUM('active', 'inactive', 'suspended', 'deleted') DEFAULT 'active' COMMENT '계정 상태',
            extra_data JSON COMMENT '추가 데이터 (JSON)',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
            
            INDEX idx_phone (phone),
            INDEX idx_email (email),
            INDEX idx_nickname (nickname),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 테이블'",

        // user_sessions 테이블
        "CREATE TABLE IF NOT EXISTS user_sessions (
            id VARCHAR(128) PRIMARY KEY COMMENT '세션 ID',
            user_id INT NOT NULL COMMENT '사용자 ID',
            data TEXT COMMENT '세션 데이터',
            ip_address VARCHAR(45) COMMENT 'IP 주소',
            user_agent TEXT COMMENT '사용자 에이전트',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
            expires_at TIMESTAMP NOT NULL COMMENT '만료일',
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 세션 테이블'",

        // user_logs 테이블
        "CREATE TABLE IF NOT EXISTS user_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL COMMENT '사용자 ID (NULL인 경우 비로그인 사용자)',
            action VARCHAR(100) NOT NULL COMMENT '액션 유형',
            description TEXT COMMENT '상세 설명',
            ip_address VARCHAR(45) COMMENT 'IP 주소',
            user_agent TEXT COMMENT '사용자 에이전트',
            extra_data JSON COMMENT '추가 데이터 (JSON)',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_action (action),
            INDEX idx_created_at (created_at),
            INDEX idx_ip_address (ip_address)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 활동 로그 테이블'",

        // verification_codes 테이블
        "CREATE TABLE IF NOT EXISTS verification_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20) NOT NULL COMMENT '휴대폰 번호',
            code VARCHAR(10) NOT NULL COMMENT '인증번호',
            purpose ENUM('signup', 'login', 'password_reset') NOT NULL COMMENT '용도',
            attempts INT DEFAULT 0 COMMENT '시도 횟수',
            verified BOOLEAN DEFAULT FALSE COMMENT '인증 완료 여부',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            expires_at TIMESTAMP NOT NULL COMMENT '만료일',
            
            INDEX idx_phone_purpose (phone, purpose),
            INDEX idx_code (code),
            INDEX idx_expires_at (expires_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='인증번호 테이블'",

        // posts 테이블
        "CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL COMMENT '작성자 ID',
            title VARCHAR(200) NOT NULL COMMENT '제목',
            content TEXT NOT NULL COMMENT '내용',
            category VARCHAR(50) COMMENT '카테고리',
            tags JSON COMMENT '태그 목록 (JSON)',
            view_count INT DEFAULT 0 COMMENT '조회수',
            like_count INT DEFAULT 0 COMMENT '좋아요 수',
            comment_count INT DEFAULT 0 COMMENT '댓글 수',
            status ENUM('draft', 'published', 'private', 'deleted') DEFAULT 'published' COMMENT '상태',
            featured BOOLEAN DEFAULT FALSE COMMENT '추천 게시글 여부',
            extra_data JSON COMMENT '추가 데이터 (JSON)',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
            
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_category (category),
            INDEX idx_status (status),
            INDEX idx_featured (featured),
            INDEX idx_created_at (created_at),
            FULLTEXT idx_title_content (title, content)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='게시글 테이블'",

        // comments 테이블
        "CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL COMMENT '게시글 ID',
            user_id INT NOT NULL COMMENT '작성자 ID',
            parent_id INT NULL COMMENT '부모 댓글 ID (대댓글인 경우)',
            content TEXT NOT NULL COMMENT '댓글 내용',
            like_count INT DEFAULT 0 COMMENT '좋아요 수',
            status ENUM('published', 'hidden', 'deleted') DEFAULT 'published' COMMENT '상태',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
            
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
            INDEX idx_post_id (post_id),
            INDEX idx_user_id (user_id),
            INDEX idx_parent_id (parent_id),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='댓글 테이블'",

        // settings 테이블
        "CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            key_name VARCHAR(100) NOT NULL UNIQUE COMMENT '설정 키',
            value TEXT COMMENT '설정 값',
            description TEXT COMMENT '설정 설명',
            type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string' COMMENT '데이터 타입',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성일',
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일',
            
            INDEX idx_key_name (key_name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시스템 설정 테이블'"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
}

/**
 * 초기 데이터 삽입
 */
function insertInitialData($pdo) {
    // 관리자 계정 생성
    $adminPhone = '010-0000-0000';
    $adminPassword = 'admin123!';
    $adminPasswordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    $sql = "INSERT IGNORE INTO users (nickname, phone, email, password_hash, phone_verified, email_verified, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $pdo->prepare($sql)->execute([
        'admin',
        $adminPhone,
        'admin@topmktx.com',
        $adminPasswordHash,
        true,
        true,
        'active'
    ]);

    // 시스템 설정 기본값
    $settings = [
        ['site_name', '탑마케팅', '사이트 이름', 'string'],
        ['site_description', '글로벌 네트워크 마케팅 커뮤니티', '사이트 설명', 'string'],
        ['admin_email', 'admin@topmktx.com', '관리자 이메일', 'string'],
        ['maintenance_mode', 'false', '점검 모드', 'boolean'],
        ['signup_enabled', 'true', '회원가입 허용', 'boolean'],
        ['email_verification_required', 'false', '이메일 인증 필수', 'boolean'],
        ['phone_verification_required', 'true', '휴대폰 인증 필수', 'boolean'],
        ['max_login_attempts', '5', '최대 로그인 시도 횟수', 'number'],
        ['account_lock_duration', '30', '계정 잠금 시간(분)', 'number']
    ];

    $sql = "INSERT IGNORE INTO settings (key_name, value, description, type) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }
}

// POST 요청 처리 (설치 실행)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        $pdo = createDbConnection();
        createDatabase($pdo);
        createTables($pdo);
        insertInitialData($pdo);
        
        $installSuccess = '✅ 설치가 성공적으로 완료되었습니다!';
        $isInstalled = true;
    } catch (Exception $e) {
        $installError = $e->getMessage();
    }
}

// 기존 설치 상태 확인
try {
    $pdo = createDbConnection();
    $pdo->exec("USE topmkt");
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) >= 7) {
        $isInstalled = true;
        $installSuccess = '이미 설치되어 있습니다.';
    }
} catch (Exception $e) {
    // 데이터베이스가 없거나 연결 실패는 정상적인 상황
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>탑마케팅 설치</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .install-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .install-header h1 {
            color: #333;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .install-header p {
            color: #666;
            font-size: 1.1em;
        }

        .status-message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .install-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .install-info h3 {
            color: #333;
            margin-bottom: 15px;
        }

        .install-info ul {
            list-style: none;
            padding: 0;
        }

        .install-info li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .install-info li:last-child {
            border-bottom: none;
        }

        .install-info li:before {
            content: "✓";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }

        .admin-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .admin-info h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }

        .admin-info p {
            margin: 5px 0;
            color: #424242;
        }

        .links {
            margin-top: 30px;
            text-align: center;
        }

        .links a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.2s;
        }

        .links a:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1>🚀 탑마케팅 설치</h1>
            <p>데이터베이스를 설정하고 초기 데이터를 생성합니다</p>
        </div>

        <?php if ($installError): ?>
            <div class="status-message status-error">
                ❌ <?= htmlspecialchars($installError) ?>
            </div>
        <?php endif; ?>

        <?php if ($installSuccess): ?>
            <div class="status-message status-success">
                <?= htmlspecialchars($installSuccess) ?>
            </div>
        <?php endif; ?>

        <div class="install-info">
            <h3>설치 내용</h3>
            <ul>
                <li>topmkt 데이터베이스 생성</li>
                <li>7개 테이블 생성 (users, sessions, logs, verification_codes, posts, comments, settings)</li>
                <li>관리자 계정 생성</li>
                <li>시스템 기본 설정 추가</li>
                <li>인덱스 및 외래키 설정</li>
            </ul>
        </div>

        <?php if (!$isInstalled): ?>
            <form method="POST">
                <button type="submit" name="install" class="btn">
                    🚀 설치 시작
                </button>
            </form>
        <?php else: ?>
            <button class="btn" disabled>
                ✅ 설치 완료
            </button>

            <div class="admin-info">
                <h4>👤 관리자 계정 정보</h4>
                <p><strong>휴대폰:</strong> 010-0000-0000</p>
                <p><strong>비밀번호:</strong> admin123!</p>
                <p><small>⚠️ 보안을 위해 로그인 후 비밀번호를 변경하세요</small></p>
            </div>
        <?php endif; ?>

        <div class="links">
            <a href="/auth/login">로그인 페이지</a>
            <a href="/auth/signup">회원가입 페이지</a>
            <a href="/">메인 페이지</a>
        </div>
    </div>
</body>
</html> 