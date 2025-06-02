<?php
/**
 * 탑마케팅 데이터베이스 설치 스크립트
 * URL: http://your-domain.com/install.php
 */

// 상수 정의
define('SRC_PATH', __DIR__ . '/src');

// 설정 파일 포함
require_once SRC_PATH . '/config/database.php';

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>탑마케팅 데이터베이스 설치</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px; 
            background: #f5f5f5;
        }
        .container { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            color: #27ae60; 
            background: #d5f4e6; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        .error { 
            color: #e74c3c; 
            background: #fdf2f2; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        .warning { 
            color: #f39c12; 
            background: #fef9e7; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 10px 0; 
        }
        .btn {
            background: #667eea;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .btn-danger {
            background: #e74c3c;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        h1 { color: #2c3e50; }
        h2 { color: #34495e; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 탑마케팅 데이터베이스 설치</h1>
        
        <?php if (isset($_POST['action'])): ?>
            
            <?php if ($_POST['action'] === 'install'): ?>
                <h2>📋 설치 진행 중...</h2>
                <?php
                try {
                    $db = Database::getInstance();
                    
                    // 테이블 생성 SQL 실행
                    $sqlStatements = [
                        // 1. users 테이블
                        "CREATE TABLE IF NOT EXISTS users (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            phone VARCHAR(13) NOT NULL UNIQUE COMMENT '휴대폰 번호 (010-1234-5678)',
                            nickname VARCHAR(20) NOT NULL UNIQUE COMMENT '닉네임',
                            email VARCHAR(100) NOT NULL UNIQUE COMMENT '이메일',
                            password VARCHAR(255) NOT NULL COMMENT '암호화된 비밀번호',
                            
                            phone_verified BOOLEAN DEFAULT FALSE COMMENT '휴대폰 인증 여부',
                            phone_verified_at TIMESTAMP NULL COMMENT '휴대폰 인증 완료 시간',
                            email_verified BOOLEAN DEFAULT FALSE COMMENT '이메일 인증 여부',
                            email_verified_at TIMESTAMP NULL COMMENT '이메일 인증 완료 시간',
                            
                            terms_agreed BOOLEAN DEFAULT FALSE COMMENT '이용약관 동의',
                            privacy_agreed BOOLEAN DEFAULT FALSE COMMENT '개인정보처리방침 동의',
                            marketing_agreed BOOLEAN DEFAULT FALSE COMMENT '마케팅 정보 수신 동의',
                            
                            status ENUM('ACTIVE', 'INACTIVE', 'SUSPENDED', 'DELETED') DEFAULT 'ACTIVE' COMMENT '계정 상태',
                            role ENUM('GENERAL', 'PREMIUM', 'ADMIN', 'SUPER_ADMIN') DEFAULT 'GENERAL' COMMENT '사용자 역할',
                            
                            profile_image VARCHAR(255) NULL COMMENT '프로필 이미지 경로',
                            bio TEXT NULL COMMENT '자기소개',
                            birth_date DATE NULL COMMENT '생년월일',
                            gender ENUM('M', 'F', 'OTHER') NULL COMMENT '성별',
                            
                            last_login_at TIMESTAMP NULL COMMENT '마지막 로그인 시간',
                            last_login_ip VARCHAR(45) NULL COMMENT '마지막 로그인 IP',
                            login_count INT DEFAULT 0 COMMENT '총 로그인 횟수',
                            
                            password_changed_at TIMESTAMP NULL COMMENT '비밀번호 변경 시간',
                            failed_login_attempts INT DEFAULT 0 COMMENT '로그인 실패 횟수',
                            locked_until TIMESTAMP NULL COMMENT '계정 잠금 해제 시간',
                            
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '가입일',
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '정보 수정일',
                            deleted_at TIMESTAMP NULL COMMENT '탈퇴일'
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='회원 정보'",
                        
                        // 2. user_sessions 테이블
                        "CREATE TABLE IF NOT EXISTS user_sessions (
                            id VARCHAR(128) PRIMARY KEY COMMENT '세션 ID',
                            user_id INT NOT NULL COMMENT '사용자 ID',
                            ip_address VARCHAR(45) NOT NULL COMMENT 'IP 주소',
                            user_agent TEXT NULL COMMENT 'User Agent',
                            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '마지막 활동 시간',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '세션 생성 시간',
                            
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                            INDEX idx_user_id (user_id),
                            INDEX idx_last_activity (last_activity)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 세션 관리'",
                        
                        // 3. user_logs 테이블
                        "CREATE TABLE IF NOT EXISTS user_logs (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            user_id INT NULL COMMENT '사용자 ID (비회원 활동도 기록)',
                            action VARCHAR(50) NOT NULL COMMENT '활동 유형 (LOGIN, LOGOUT, SIGNUP, etc.)',
                            description TEXT NULL COMMENT '상세 설명',
                            ip_address VARCHAR(45) NOT NULL COMMENT 'IP 주소',
                            user_agent TEXT NULL COMMENT 'User Agent',
                            extra_data JSON NULL COMMENT '추가 데이터',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '기록 시간',
                            
                            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
                            INDEX idx_user_id (user_id),
                            INDEX idx_action (action),
                            INDEX idx_created_at (created_at)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='사용자 활동 로그'",
                        
                        // 4. verification_codes 테이블
                        "CREATE TABLE IF NOT EXISTS verification_codes (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            phone VARCHAR(13) NOT NULL COMMENT '휴대폰 번호',
                            code VARCHAR(6) NOT NULL COMMENT '인증번호',
                            type ENUM('SIGNUP', 'LOGIN', 'PASSWORD_RESET') NOT NULL COMMENT '인증 유형',
                            attempts INT DEFAULT 0 COMMENT '시도 횟수',
                            is_used BOOLEAN DEFAULT FALSE COMMENT '사용 여부',
                            expires_at TIMESTAMP NOT NULL COMMENT '만료 시간',
                            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '생성 시간',
                            
                            INDEX idx_phone (phone),
                            INDEX idx_code (code),
                            INDEX idx_expires_at (expires_at)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='인증번호 임시 저장'",
                        
                        // 5. settings 테이블
                        "CREATE TABLE IF NOT EXISTS settings (
                            key_name VARCHAR(100) PRIMARY KEY COMMENT '설정 키',
                            value TEXT NULL COMMENT '설정 값',
                            description VARCHAR(255) NULL COMMENT '설정 설명',
                            type ENUM('STRING', 'INTEGER', 'BOOLEAN', 'JSON') DEFAULT 'STRING' COMMENT '값 타입',
                            is_public BOOLEAN DEFAULT FALSE COMMENT '공개 설정 여부',
                            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '수정일'
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='시스템 설정'"
                    ];
                    
                    foreach ($sqlStatements as $sql) {
                        $db->execute($sql);
                        echo "<div class='success'>✅ 테이블 생성 완료</div>";
                    }
                    
                    // 기본 설정 값 삽입
                    $settings = [
                        ['site_name', '탑마케팅', '사이트 이름', 'STRING', 1],
                        ['site_description', '글로벌 네트워크 마케팅 커뮤니티', '사이트 설명', 'STRING', 1],
                        ['max_login_attempts', '5', '최대 로그인 시도 횟수', 'INTEGER', 0],
                        ['session_timeout', '7200', '세션 타임아웃 (초)', 'INTEGER', 0],
                        ['signup_enabled', 'true', '회원가입 활성화 여부', 'BOOLEAN', 1],
                        ['maintenance_mode', 'false', '점검 모드 여부', 'BOOLEAN', 1]
                    ];
                    
                    foreach ($settings as $setting) {
                        $sql = "INSERT INTO settings (key_name, value, description, type, is_public) 
                                VALUES (?, ?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP";
                        $db->execute($sql, $setting);
                    }
                    
                    echo "<div class='success'>✅ 기본 설정 완료</div>";
                    
                    // 관리자 계정 생성
                    $adminExists = $db->fetch("SELECT id FROM users WHERE phone = '010-0000-0000'");
                    if (!$adminExists) {
                        $adminData = [
                            '010-0000-0000', 
                            '관리자', 
                            'admin@topmktx.com', 
                            password_hash('admin123!', PASSWORD_DEFAULT),
                            1, // phone_verified
                            date('Y-m-d H:i:s'), // phone_verified_at
                            1, // terms_agreed
                            1, // privacy_agreed
                            'SUPER_ADMIN',
                            'ACTIVE'
                        ];
                        
                        $sql = "INSERT INTO users (
                            phone, nickname, email, password, 
                            phone_verified, phone_verified_at,
                            terms_agreed, privacy_agreed,
                            role, status
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        
                        $db->execute($sql, $adminData);
                        echo "<div class='success'>✅ 관리자 계정 생성 완료 (010-0000-0000 / admin123!)</div>";
                    } else {
                        echo "<div class='warning'>⚠️ 관리자 계정이 이미 존재합니다.</div>";
                    }
                    
                    echo "<div class='success'><h3>🎉 설치가 완료되었습니다!</h3></div>";
                    echo "<p><strong>다음 단계:</strong></p>";
                    echo "<ol>";
                    echo "<li>보안을 위해 이 install.php 파일을 삭제하세요.</li>";
                    echo "<li><a href='/auth/login'>로그인 페이지</a>에서 관리자 계정으로 로그인하세요.</li>";
                    echo "<li><a href='/auth/signup'>회원가입 페이지</a>에서 새 계정을 만들어 테스트하세요.</li>";
                    echo "</ol>";
                    
                } catch (Exception $e) {
                    echo "<div class='error'>❌ 오류: " . $e->getMessage() . "</div>";
                }
                ?>
                
            <?php elseif ($_POST['action'] === 'delete'): ?>
                <h2>🗑️ 파일 삭제</h2>
                <?php
                if (unlink(__FILE__)) {
                    echo "<div class='success'>✅ install.php 파일이 삭제되었습니다.</div>";
                    echo "<script>setTimeout(() => window.location.href = '/', 2000);</script>";
                } else {
                    echo "<div class='error'>❌ 파일 삭제에 실패했습니다. 수동으로 삭제해주세요.</div>";
                }
                ?>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- 설치 시작 화면 -->
            <p>탑마케팅 플랫폼의 데이터베이스 테이블을 생성합니다.</p>
            
            <div class="warning">
                <strong>⚠️ 주의사항:</strong>
                <ul>
                    <li>설치 전에 데이터베이스 백업을 권장합니다.</li>
                    <li>MySQL/MariaDB 서버가 실행 중이어야 합니다.</li>
                    <li>설치 완료 후 이 파일을 삭제하세요.</li>
                </ul>
            </div>
            
            <h2>📊 생성될 테이블</h2>
            <ul>
                <li><strong>users</strong> - 회원 정보</li>
                <li><strong>user_sessions</strong> - 사용자 세션 관리</li>
                <li><strong>user_logs</strong> - 사용자 활동 로그</li>
                <li><strong>verification_codes</strong> - 인증번호 임시 저장</li>
                <li><strong>settings</strong> - 시스템 설정</li>
            </ul>
            
            <h2>👤 관리자 계정</h2>
            <pre>휴대폰: 010-0000-0000
비밀번호: admin123!
역할: 슈퍼 관리자</pre>
            
            <form method="post">
                <input type="hidden" name="action" value="install">
                <button type="submit" class="btn" onclick="return confirm('데이터베이스 테이블을 생성하시겠습니까?')">
                    🚀 설치 시작
                </button>
            </form>
            
        <?php endif; ?>
        
        <?php if (isset($_POST['action']) && $_POST['action'] === 'install'): ?>
            <hr style="margin: 30px 0;">
            <h2>🔒 보안</h2>
            <p>설치가 완료되었습니다. 보안을 위해 이 파일을 삭제하세요.</p>
            <form method="post" style="display: inline;">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="btn btn-danger" onclick="return confirm('install.php 파일을 삭제하시겠습니까?')">
                    🗑️ install.php 삭제
                </button>
            </form>
        <?php endif; ?>
        
    </div>
</body>
</html> 