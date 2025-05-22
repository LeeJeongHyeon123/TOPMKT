<?php
/**
 * 회원가입 데이터 초기화 스크립트
 * 
 * 테스트로 가입한 회원 데이터를 삭제하는 스크립트입니다.
 * 1. MariaDB의 users 테이블에서 사용자 정보 삭제
 * 2. Firebase Authentication에서 사용자 계정 삭제
 * 
 * 사용법:
 * php cleanup_test_users.php [--all] [--phone=전화번호] [--confirm]
 * 
 * 옵션:
 *   --all: 모든 테스트 사용자 삭제 (주의: 실제 사용자도 삭제될 수 있음)
 *   --phone=전화번호: 특정 전화번호로 등록된 사용자만 삭제
 *   --confirm: 확인 없이 바로 실행 (기본적으로는 삭제 전 확인 요청)
 */

// 오류 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 실행 시작
echo "\n";
echo "🧹 TOPMKT 회원가입 데이터 초기화 스크립트\n";
echo "==========================================\n\n";

// 루트 디렉토리 설정
define('ROOT_DIR', realpath(__DIR__ . '/..'));

// 데이터베이스 설정 로드
require_once ROOT_DIR . '/config/database.php';

// 명령행 인자 파싱
$options = getopt('', ['all', 'phone:', 'confirm']);

if (!isset($options['all']) && !isset($options['phone'])) {
    echo "❌ 오류: --all 또는 --phone 옵션을 지정해야 합니다.\n";
    echo "사용법: php cleanup_test_users.php [--all] [--phone=전화번호] [--confirm]\n";
    exit(1);
}

// 데이터베이스 연결
try {
    echo "ℹ️ 데이터베이스 연결 중...\n";
    $dsn = "mysql:host=localhost;dbname={$db_config['db_name']};charset={$db_config['db_charset']}";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    $pdo = new PDO($dsn, $db_config['db_user'], $db_config['db_pass'], $options);
    echo "✅ 데이터베이스 연결 성공\n";
} catch (PDOException $e) {
    echo "❌ 데이터베이스 연결 오류: " . $e->getMessage() . "\n";
    exit(1);
}

// Firebase Admin SDK 초기화 (PHP SDK가 설치되어 있을 경우)
$useFirebase = false;
if (file_exists(ROOT_DIR . '/vendor/autoload.php')) {
    try {
        echo "ℹ️ Firebase 초기화 시도 중...\n";
        require_once ROOT_DIR . '/vendor/autoload.php';
        
        // Firebase SDK 클래스가 존재하는지 확인
        if (class_exists('Kreait\Firebase\Factory')) {
            $factory = new Kreait\Firebase\Factory();
            $serviceAccount = ROOT_DIR . '/config/google/service-account.json';
            
            if (file_exists($serviceAccount)) {
                $firebase = $factory->withServiceAccount($serviceAccount)
                    ->create();
                $auth = $firebase->getAuth();
                $useFirebase = true;
                echo "✅ Firebase 초기화 성공\n";
            } else {
                echo "⚠️ Firebase 서비스 계정 키 파일을 찾을 수 없습니다. Firebase 삭제 기능은 비활성화됩니다.\n";
            }
        } else {
            echo "⚠️ Firebase SDK를 찾을 수 없습니다. Firebase 삭제 기능은 비활성화됩니다.\n";
        }
    } catch (Exception $e) {
        echo "⚠️ Firebase 초기화 오류: " . $e->getMessage() . " Firebase 삭제 기능은 비활성화됩니다.\n";
    }
} else {
    echo "⚠️ Firebase SDK를 찾을 수 없습니다. Firebase 삭제 기능은 비활성화됩니다.\n";
}

// 사용자 목록 조회
try {
    if (isset($options['phone'])) {
        $phone = $options['phone'];
        $stmt = $pdo->prepare("SELECT id, firebase_uid, phone_number, nickname FROM users WHERE phone_number = :phone");
        $stmt->execute(['phone' => $phone]);
    } else {
        $stmt = $pdo->prepare("SELECT id, firebase_uid, phone_number, nickname FROM users");
        $stmt->execute();
    }
    
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "⚠️ 삭제할 사용자가 없습니다.\n";
        exit(0);
    }
    
    echo "ℹ️ " . count($users) . "명의 사용자를 찾았습니다.\n\n";
    
    // 사용자 목록 출력
    echo "삭제 대상 사용자 목록:\n";
    echo str_pad("ID", 36) . " | " . str_pad("Firebase UID", 36) . " | " . str_pad("전화번호", 15) . " | " . "닉네임\n";
    echo str_repeat("-", 100) . "\n";
    
    foreach ($users as $user) {
        echo str_pad($user['id'], 36) . " | " . str_pad($user['firebase_uid'] ? $user['firebase_uid'] : 'N/A', 36) . " | " . str_pad($user['phone_number'], 15) . " | " . $user['nickname'] . "\n";
    }
} catch (PDOException $e) {
    echo "❌ 사용자 목록 조회 오류: " . $e->getMessage() . "\n";
    exit(1);
}

// 확인 메시지
if (!isset($options['confirm'])) {
    echo "\n위 사용자들의 데이터를 삭제하시겠습니까? (y/N): ";
    $confirmation = trim(fgets(STDIN));
    
    if (strtolower($confirmation) !== 'y') {
        echo "ℹ️ 작업이 취소되었습니다.\n";
        exit(0);
    }
}

// 사용자 삭제
echo "\n";
echo "🚮 사용자 데이터 삭제 중...\n";
echo "==========================\n\n";

$successCount = 0;
$failCount = 0;

foreach ($users as $user) {
    echo "사용자 '{$user['nickname']}' ({$user['phone_number']}) 삭제 중...\n";
    
    // Firebase Authentication에서 사용자 삭제
    if ($useFirebase && !empty($user['firebase_uid'])) {
        try {
            echo "ℹ️ Firebase Authentication에서 사용자 삭제 중... (UID: {$user['firebase_uid']})\n";
            $auth->deleteUser($user['firebase_uid']);
            echo "✅ Firebase Authentication에서 사용자 삭제 완료\n";
        } catch (Exception $e) {
            echo "⚠️ Firebase Authentication에서 사용자 삭제 실패: " . $e->getMessage() . "\n";
        }
    }
    
    // 데이터베이스에서 사용자 삭제
    try {
        echo "ℹ️ 데이터베이스에서 사용자 삭제 중...\n";
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);
        
        if ($stmt->rowCount() > 0) {
            echo "✅ 데이터베이스에서 사용자 삭제 완료\n";
            $successCount++;
        } else {
            echo "❌ 데이터베이스에서 사용자 삭제 실패\n";
            $failCount++;
        }
    } catch (PDOException $e) {
        echo "❌ 데이터베이스에서 사용자 삭제 오류: " . $e->getMessage() . "\n";
        $failCount++;
    }
    
    echo "\n";
}

// 결과 출력
echo "🏁 삭제 작업 완료\n";
echo "=================\n\n";
echo "✅ 성공: " . $successCount . "명\n";

if ($failCount > 0) {
    echo "❌ 실패: " . $failCount . "명\n";
}

echo "\n회원가입 데이터 초기화 작업이 완료되었습니다.\n"; 