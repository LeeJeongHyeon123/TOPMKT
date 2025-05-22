<?php
require_once __DIR__ . '/../../vendor/autoload.php';
ob_start();

// 에러 출력 설정
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그 파일 설정
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 세션 설정 - 무제한 로그인 유지
ini_set('session.gc_maxlifetime', 0); // 세션 시간 무제한
ini_set('session.cookie_lifetime', 0); // 브라우저 닫기전까지 유지
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'None');

// JSON 응답 헤더 설정
header('Content-Type: application/json');
header('X-Requested-With: XMLHttpRequest');

// CORS 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// 디버깅을 위한 로그 함수
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " [Login] " . $message;
    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }
    error_log($log_message);
}

// 에러 핸들러 설정
function handleError($errno, $errstr, $errfile, $errline) {
    $error = [
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ];
    
    debug_log('PHP 에러 발생', [
        'type' => $errno,
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline
    ]);
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    // header('Content-Type: application/json; charset=utf-8'); // 위에서 이미 설정
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit();
}

// 예외 핸들러 설정
function handleException($exception) {
    $error = [
        'success' => false,
        'message' => '시스템 오류가 발생했습니다.',
        'error' => [
            'code' => 'INTERNAL_ERROR',
            'details' => '서버 내부 오류가 발생했습니다.'
        ]
    ];
    
    debug_log('예외 발생', [
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    // header('Content-Type: application/json; charset=utf-8'); // 위에서 이미 설정
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit();
}

// 에러 핸들러 등록
set_error_handler('handleError');
set_exception_handler('handleException');

// 디버깅을 위한 로그
// error_log("로그인 요청 수신: api/auth/login.php"); // 위에서 이미 테스트 로그 추가

// POST 요청이 아닌 경우 에러
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// JSON 데이터 파싱
$json = file_get_contents('php://input');
// error_log("Received JSON data: " . $json); // 위에서 이미 테스트 로그 추가

$data = json_decode($json, true);

// JSON 파싱 에러 체크
if (json_last_error() !== JSON_ERROR_NONE) {
    // error_log("JSON parsing error: " . json_last_error_msg()); // 위에서 이미 테스트 로그 추가
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '잘못된 요청 형식입니다.']);
    exit;
}

// 필수 파라미터 검증
if (!isset($data['phone']) || !isset($data['code'])) {
    // error_log("Missing required parameters: " . print_r($data, true)); // 위에서 이미 테스트 로그 추가
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '필수 파라미터가 누락되었습니다.']);
    exit;
}

// 데이터베이스 설정 로드
require_once __DIR__ . '/../../config/database.php';

// Firebase 설정 로드
require_once __DIR__ . '/../../config/firebase/config.php';

// $db_config 변수를 미리 로드된 설정으로 사용합니다.
// database.php 파일은 이미 위에서 require_once로 포함되었습니다.
// 전역 변수로 접근하거나, 아니면 이전에 로드한 시점의 변수를 사용해야 합니다.
// 여기서는 try 블록 이전에 $db_config를 명시적으로 할당하는 방식으로 수정합니다.

$db_config = require __DIR__ . '/../../config/database.php'; // Use require here instead of require_once, or ensure it's assigned before the try block

try {
    // 데이터베이스 설정 가져오기
    // $db_config = require_once __DIR__ . '/../../config/database.php'; // 이 줄을 주석 처리하거나 삭제합니다.

    // $db_config 변수 디버깅 로그 추가
    debug_log('Loaded database config type: ' . gettype($db_config));
    debug_log('Loaded database config content: ', $db_config);

    if (!is_array($db_config) || empty($db_config['db_host']) || empty($db_config['db_name'])) {
        throw new Exception('데이터베이스 설정이 올바르지 않습니다.');
    }
    
    // DB 연결 - 타임아웃 설정 추가
    $dsn = "mysql:host={$db_config['db_host']};dbname={$db_config['db_name']};charset=utf8mb4;unix_socket=/var/lib/mysql/mysql.sock";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        // 타임아웃 설정 추가
        PDO::ATTR_TIMEOUT => 5, // 5초 연결 타임아웃
    ];
    
    debug_log('데이터베이스 연결 시도', ['host' => $db_config['db_host'], 'dbname' => $db_config['db_name']]);
    $pdo = new PDO($dsn, $db_config['db_user'], $db_config['db_pass'], $options);
    debug_log('데이터베이스 연결 성공');
    
    // 전화번호로 사용자 조회 - null 체크 강화
    if (empty($data['phone'])) {
        throw new Exception('전화번호가 제공되지 않았습니다.');
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, firebase_uid, nickname FROM users WHERE phone_number = ?");
        $stmt->execute([$data['phone']]);
        $user = $stmt->fetch();
        
        // 사용자 조회 결과 디버깅
        debug_log('사용자 조회 결과', [
            'phone' => $data['phone'],
            'found' => $user ? true : false,
            'user_id' => $user ? $user['id'] : null
        ]);
    } catch (PDOException $e) {
        debug_log('사용자 조회 쿼리 실행 오류', [
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ]);
        throw new Exception('사용자 정보를 조회하는 중 오류가 발생했습니다.');
    }
    
    if (!$user || !is_array($user)) {
        error_log("사용자를 찾을 수 없음: " . $data['phone']);
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => '등록되지 않은 전화번호입니다. 회원가입을 진행해주세요.',
            'errorCode' => 'USER_NOT_FOUND'
        ]);
        exit;
    }
    
    // 인증번호 검증 - 데이터 유효성 강화
    $verificationCode = $data['code'] ?? '';
    $sessionInfo = $data['sessionInfo'] ?? null;
    
    if (empty($verificationCode)) {
        error_log("인증번호 누락");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '인증번호를 입력해주세요.']);
        exit;
    }
    
    if (!$sessionInfo) {
        error_log("세션 정보 누락");
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => '인증 세션이 만료되었습니다. 다시 시도해주세요.']);
        exit;
    }
    
    try {
        // 디버깅을 위한 로그 추가
        debug_log('AuthService 인스턴스 생성 시작');
        
        // 안전한 인스턴스 생성 확인
        if (!class_exists('\\App\\Services\\Firebase\\AuthService')) {
            throw new Exception('AuthService 클래스를 찾을 수 없습니다.');
        }
        
        $authService = \App\Services\Firebase\AuthService::getInstance();
        debug_log('AuthService 인스턴스 생성 완료');
        
        if (!method_exists($authService, 'verifyCode')) {
            throw new Exception('verifyCode 메서드를 찾을 수 없습니다.');
        }
        
        // 전달할 파라미터 로깅
        debug_log('verifyCode 호출 전 파라미터', [
            'phone' => $data['phone'],
            'code' => $verificationCode,
            'sessionInfo' => substr($sessionInfo, 0, 20) . '...'
        ]);
        
        // 인증번호 검증 호출 - 타임아웃 설정
        debug_log('verifyCode 호출 시작');
        
        // 타임아웃 설정
        $prevTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 10); // 10초 타임아웃
        
        $verifyResult = $authService->verifyCode($data['phone'], $verificationCode, $sessionInfo);
        
        // 타임아웃 복구
        ini_set('default_socket_timeout', $prevTimeout);
        
        debug_log('verifyCode 호출 완료, 결과 타입: ' . gettype($verifyResult));
        
        // 인증번호 검증 결과 처리 및 표준화 - 확실한 타입 체크
        if ($verifyResult === null || $verifyResult === false) {
            debug_log('verifyCode 결과가 falsy 값임 (null 또는 false)');
            $verifyResult = ['success' => false, 'message' => '인증 결과가 없습니다. 다시 시도해주세요.'];
        } else if (is_bool($verifyResult) && $verifyResult === true) {
            debug_log('verifyCode 결과가 boolean true 값임');
            $verifyResult = ['success' => true, 'message' => '인증 성공'];
        } else if (!is_array($verifyResult)) {
            debug_log('verifyCode 결과가 예상치 못한 타입임: ' . gettype($verifyResult));
            $verifyResult = ['success' => false, 'message' => '인증 결과가 유효하지 않습니다. 다시 시도해주세요.'];
        } else {
            debug_log('verifyCode 결과(배열)', $verifyResult);
            // 배열이지만 success 키가 없는 경우를 처리
            if (!isset($verifyResult['success'])) {
                $verifyResult['success'] = false;
            }
            if (!isset($verifyResult['message'])) {
                $verifyResult['message'] = '인증 결과가 유효하지 않습니다.';
            }
        }
        
        // 추가적인 안전 장치: 항상 배열 형태로 유지
        if (!is_array($verifyResult)) {
            debug_log('추가 안전장치 발동: verifyResult가 배열이 아님', ['type' => gettype($verifyResult)]);
            $verifyResult = [
                'success' => false,
                'message' => '인증 처리 중 오류가 발생했습니다.',
                'idToken' => null,
                'remainingAttempts' => 0,
                'isBlocked' => false
            ];
        }
        
        // 결과 검증 (어떤 형태가 와도 안전하게)
        if (!is_array($verifyResult)) {
            $errorMessage = '인증 결과가 유효하지 않습니다. 다시 시도해주세요.';
            debug_log("인증번호 검증 실패: 유효하지 않은 결과 형식", $verifyResult);
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit;
        }
        
        // 배열에 'success' 키가 있는지, 그리고 그 값이 true인지 확인
        if (!array_key_exists('success', $verifyResult) || $verifyResult['success'] !== true) {
            $errorMessage = isset($verifyResult['message']) ? $verifyResult['message'] : '인증에 실패했습니다.';
            debug_log("인증번호 검증 실패: " . $errorMessage);
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => $errorMessage]);
            exit;
        }
        
        // Firebase idToken 획득 (안전하게 접근)
        $idToken = null;
        if (is_array($verifyResult) && isset($verifyResult['idToken']) && !empty($verifyResult['idToken'])) {
            $idToken = $verifyResult['idToken'];
            debug_log('idToken 획득 성공', ['length' => strlen($idToken)]);
        } else {
            debug_log('idToken이 없거나 비어있음', $verifyResult);
            
            // 직접 요청에서 idToken을 가져오는 시도
            if (isset($data['idToken']) && !empty($data['idToken'])) {
                $idToken = $data['idToken'];
                debug_log('요청에서 직접 idToken 획득 성공', ['length' => strlen($idToken)]);
            } else {
                debug_log('요청에서도 idToken을 찾을 수 없음');
            }
            // idToken이 없어도 계속 진행
        }
        
        // Firebase UID 업데이트 - idToken이 있는 경우에만 (안전한 업데이트)
        if ($idToken && !empty($user['id'])) {
            debug_log('Firebase UID 업데이트 시작', [
                'user_id' => $user['id'],
                'idToken_length' => strlen($idToken)
            ]);
            
            try {
                $stmt = $pdo->prepare("UPDATE users SET firebase_uid = ? WHERE id = ?");
                $result = $stmt->execute([$idToken, $user['id']]);
                
                if ($result) {
                    debug_log('Firebase UID 업데이트 완료');
                } else {
                    debug_log('Firebase UID 업데이트 실패 - 실행 결과가 false');
                }
            } catch (\PDOException $e) {
                debug_log('Firebase UID 업데이트 실패', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode()
                ]);
                // 업데이트 실패해도 로그인은 계속 진행
            }
        } else {
            debug_log("경고: idToken이 없거나 user_id가 없어 Firebase UID 업데이트를 건너뜁니다.");
        }
        
        // 세션 토큰 생성 - 더 안전한 랜덤 토큰
        $sessionToken = bin2hex(random_bytes(32)); // 더 강력한 랜덤 토큰
        debug_log('세션 토큰 생성', ['length' => strlen($sessionToken)]);
        
        // 응답 데이터 구성
        $responseData = [
            'success' => true,
            'message' => '로그인되었습니다.',
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'nickname' => $user['nickname'] ?? '사용자'
                ],
                'sessionToken' => $sessionToken
            ]
        ];
        
        // idToken이 있으면 응답에 포함
        if ($idToken) {
            $responseData['data']['token'] = $idToken;
            
            // 세션 쿠키에도 저장 - 도메인 설정 개선
            $cookieOptions = [
                'expires' => time() + 10 * 365 * 24 * 60 * 60, // 10년 동안 유효
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'None'
            ];
            
            // 도메인 자동 감지 (개발/프로덕션 환경 자동 구분)
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if (!empty($host) && !in_array($host, ['localhost', '127.0.0.1'])) {
                $cookieOptions['domain'] = $host;
            }
            
            setcookie('topmkt_session', $sessionToken, $cookieOptions);
            debug_log('세션 쿠키 설정 완료', ['domain' => $cookieOptions['domain'] ?? '기본값']);
            
            // 세션 테이블에 저장 시도 - 더 안전한 처리
            try {
                // 세션 테이블이 있는지 확인 - 트랜잭션 사용
                $pdo->beginTransaction();
                
                try {
                    // 테이블 존재 확인
                    $tableExists = false;
                    $tableCheck = $pdo->query("SHOW TABLES LIKE 'user_sessions'");
                    $tableExists = ($tableCheck && $tableCheck->rowCount() > 0);
                    
                    // 테이블이 없으면 생성
                    if (!$tableExists) {
                        debug_log('user_sessions 테이블이 없어 생성 시도');
                        $createTableSql = "CREATE TABLE IF NOT EXISTS `user_sessions` (
                            `id` VARCHAR(64) NOT NULL PRIMARY KEY,
                            `user_id` VARCHAR(64) NOT NULL,
                            `token` VARCHAR(128) NOT NULL,
                            `expires_at` DATETIME NOT NULL,
                            `created_at` DATETIME NOT NULL,
                            `ip_address` VARCHAR(45) NULL,
                            `user_agent` VARCHAR(255) NULL,
                            INDEX `idx_user_id` (`user_id`),
                            INDEX `idx_token` (`token`),
                            INDEX `idx_expires_at` (`expires_at`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
                        
                        $pdo->exec($createTableSql);
                        debug_log('user_sessions 테이블 생성 완료');
                        $tableExists = true;
                    }
                    
                    // 테이블이 있을 경우에만 세션 저장 시도
                    if ($tableExists) {
                        // 이전 세션 정리 (오류 무시)
                        $cleanStmt = $pdo->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                        $cleanStmt->execute([$user['id']]);
                        debug_log('이전 세션 정리 완료');
                        
                        // 세션 저장 - 추가 정보 포함
                        $sessionId = bin2hex(random_bytes(16));
                        $expiresAt = date('Y-m-d H:i:s', time() + 10 * 365 * 24 * 60 * 60); // 10년 후
                        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
                        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
                        
                        $sessionStmt = $pdo->prepare("INSERT INTO user_sessions 
                            (id, user_id, token, expires_at, created_at, ip_address, user_agent) 
                            VALUES (?, ?, ?, ?, NOW(), ?, ?)");
                        $sessionStmt->execute([
                            $sessionId, 
                            $user['id'], 
                            $sessionToken, 
                            $expiresAt,
                            $ipAddress,
                            $userAgent
                        ]);
                        debug_log('세션 저장 완료', ['session_id' => $sessionId]);
                    }
                    
                    // 모든 작업이 성공적으로 완료되면 커밋
                    $pdo->commit();
                    
                } catch (\PDOException $e) {
                    // 오류 발생 시 롤백
                    $pdo->rollBack();
                    debug_log('세션 저장 과정 실패 (트랜잭션 롤백)', ['error' => $e->getMessage()]);
                    // 실패해도 로그인은 계속 진행
                }
            } catch (\PDOException $e) {
                debug_log('트랜잭션 시작 실패', ['error' => $e->getMessage()]);
                // 실패해도 로그인은 계속 진행
            }
        }
        
        debug_log('로그인 성공 응답 준비 완료');
        
        // 로그인 성공 응답
        echo json_encode($responseData, JSON_UNESCAPED_UNICODE);
        debug_log('로그인 성공 응답 전송 완료');
        
    } catch (\Exception $e) {
        debug_log('Firebase 인증 처리 오류', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]);
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'message' => '인증 처리 중 오류가 발생했습니다.',
            'error' => [
                'code' => 'AUTH_ERROR',
                'details' => '인증 서비스 오류가 발생했습니다. 잠시 후 다시 시도해주세요.'
            ]
        ]);
        exit;
    }
    
} catch (PDOException $e) {
    debug_log('데이터베이스 연결 또는 쿼리 오류', [
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '데이터베이스 연결 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',
        'error' => [
            'code' => 'DB_ERROR',
            'details' => '데이터베이스 처리 중 오류가 발생했습니다.'
        ]
    ]);
    exit;
} catch (Exception $e) {
    debug_log('로그인 처리 중 일반 오류', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '로그인 처리 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',
        'error' => [
            'code' => 'GENERAL_ERROR',
            'details' => $e->getMessage()
        ]
    ]);
    exit;
}

// 출력 버퍼 플러시
ob_end_flush();