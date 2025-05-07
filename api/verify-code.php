<?php
// 모든 출력을 버퍼링
ob_start();

// CORS 설정 추가
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With');

// OPTIONS 요청에 대한 즉시 응답 (CORS 프리플라이트 요청 처리)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 요청 메소드 검증
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => '허용되지 않는 요청 방식입니다.'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 에러 핸들러 설정
function errorHandler($errno, $errstr, $errfile, $errline) {
    $error = [
        'error' => '시스템 오류가 발생했습니다.',
        'debug' => [
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline
        ]
    ];
    
    // 버퍼 클리어
    ob_clean();
    
    // JSON 응답 헤더 설정
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode($error, JSON_UNESCAPED_UNICODE);
    exit;
}

// 에러 핸들러 등록
set_error_handler('errorHandler');

// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/topmkt/logs/topmkt_error.log');

// JSON 응답 헤더 설정
header('Content-Type: application/json; charset=utf-8');

try {
    // JSON 요청 데이터 파싱
    $raw_data = file_get_contents('php://input');
    
    // 전체 요청 로깅
    error_log("[verify-code] 수신된 요청 데이터: " . $raw_data);
    
    $data = json_decode($raw_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('잘못된 JSON 형식입니다. 오류: ' . json_last_error_msg());
    }

    // 필수 필드 검증
    if (empty($data['sessionInfo'])) {
        throw new Exception('세션 정보(sessionInfo)가 누락되었습니다.');
    }
    
    if (empty($data['code'])) {
        throw new Exception('인증 코드(code)가 누락되었습니다.');
    }

    // sessionInfo 형식 검증 (Firebase 세션 정보는 다양한 문자를 포함할 수 있음)
    if (strlen($data['sessionInfo']) < 10) { // 최소 길이 검증
        throw new Exception('세션 정보(sessionInfo)가 유효하지 않습니다.');
    }

    // 인증번호 형식 검증
    if (!preg_match('/^\d{6}$/', $data['code'])) {
        throw new Exception('인증번호는 6자리 숫자여야 합니다.');
    }

    // 닉네임 검증 (회원가입인 경우)
    if (isset($data['nickname'])) {
        if (empty(trim($data['nickname']))) {
            throw new Exception('닉네임을 입력해주세요.');
        }
        
        if (mb_strlen(trim($data['nickname']), 'UTF-8') > 20) {
            throw new Exception('닉네임은 20자 이내로 입력해주세요.');
        }
    }

    // Firebase 설정 파일 확인
    $firebase_config_path = __DIR__ . '/../config/firebase-config.php';

    if (!file_exists($firebase_config_path)) {
        throw new Exception('Firebase 설정 파일을 찾을 수 없습니다.');
    }

    require_once $firebase_config_path;
    
    // 테스트 계정인 경우 즉시 인증 완료 처리
    if ($data['code'] === '123456' && strpos($data['sessionInfo'], 'test') !== false) {
        $testPhoneNumber = '+821012341234';
        error_log("[verify-code] 테스트 계정 인증 완료: " . $testPhoneNumber);
        
        $result = [
            'idToken' => 'test_id_token_' . time(),
            'refreshToken' => 'test_refresh_token_' . time(),
            'expiresIn' => '3600',
            'localId' => 'test_local_id_' . time(),
            'phoneNumber' => $testPhoneNumber
        ];
        
        // 회원가입인 경우 DB에 사용자 정보 저장
        if (isset($data['nickname'])) {
            require_once __DIR__ . '/../includes/Database.php';
            $db = new Database();
            
            try {
                // 사용자 존재 여부 확인
                $checkSql = "SELECT id FROM users WHERE phone_number = ?";
                $existingUser = $db->query($checkSql, [$testPhoneNumber])->fetch();
                
                if ($existingUser) {
                    // 기존 사용자 정보 업데이트
                    $updateSql = "UPDATE users SET nickname = ?, firebase_uid = ? WHERE phone_number = ?";
                    $db->query($updateSql, [$data['nickname'], $result['localId'], $testPhoneNumber]);
                } else {
                    // 새 사용자 등록
                    $insertSql = "INSERT INTO users (phone_number, nickname, firebase_uid) VALUES (?, ?, ?)";
                    $db->query($insertSql, [$testPhoneNumber, $data['nickname'], $result['localId']]);
                }
            } catch (Exception $e) {
                error_log("[verify-code] 테스트 계정 DB 저장 오류: " . $e->getMessage());
            }
        }
        
        // 성공 응답
        echo json_encode([
            'success' => true,
            'message' => '인증이 완료되었습니다.',
            'idToken' => $result['idToken'],
            'refreshToken' => $result['refreshToken'],
            'expiresIn' => $result['expiresIn'],
            'localId' => $result['localId'],
            'phoneNumber' => $result['phoneNumber']
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Firebase API 키 검증
    if (empty(FIREBASE_API_KEY)) {
        throw new Exception('Firebase API 키가 설정되지 않았습니다.');
    }

    // Firebase Authentication REST API를 사용하여 인증번호 확인
    $apiKey = FIREBASE_API_KEY;
    
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:verifyPhoneNumber?key=" . $apiKey;
    
    $postData = [
        'sessionInfo' => $data['sessionInfo'],
        'code' => $data['code']
    ];

    // 회원가입인 경우 닉네임 추가
    if (isset($data['nickname'])) {
        $postData['displayName'] = trim($data['nickname']);
    }

    // Firebase API 요청 로깅
    error_log("[verify-code] Firebase API 요청: URL=" . $url);
    error_log("[verify-code] Firebase API 요청 데이터: " . json_encode($postData, JSON_UNESCAPED_UNICODE));
    
    // CURL 초기화 및 설정
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_VERBOSE => true
    ]);
    
    // CURL 디버그 로깅을 위한 임시 파일 핸들
    $verbose = fopen('php://temp', 'w+');
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    
    // CURL 실행
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    $curlErrno = curl_errno($ch);
    
    // CURL 디버그 로그 읽기
    rewind($verbose);
    $verboseLog = stream_get_contents($verbose);
    error_log("[verify-code] CURL 상세 로그: " . $verboseLog);
    fclose($verbose);
    
    // CURL 세션 종료
    curl_close($ch);
    
    // API 응답 로깅
    error_log("[verify-code] Firebase API 응답 코드: " . $httpCode);
    error_log("[verify-code] Firebase API 응답 내용: " . $response);
    
    // CURL 오류 확인
    if ($curlErrno) {
        error_log("[verify-code] CURL 오류 #" . $curlErrno . ": " . $curlError);
        // 버퍼 클리어
        ob_clean();
        
        http_response_code(500);
        echo json_encode([
            'error' => 'Firebase 서버 연결에 실패했습니다. 잠시 후 다시 시도해주세요.',
            'code' => 'CURL_ERROR_' . $curlErrno,
            'details' => $curlError
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // HTTP 응답 코드 확인
    if ($httpCode !== 200) {
        error_log("[verify-code] HTTP 오류 응답: " . $httpCode);
        
        // 응답이 없는 경우
        if (empty($response)) {
            // 버퍼 클리어
            ob_clean();
            
            http_response_code(400);
            echo json_encode([
                'error' => 'Firebase 서버로부터 응답을 받지 못했습니다. 잠시 후 다시 시도해주세요.',
                'code' => 'EMPTY_RESPONSE',
                'details' => ['http_code' => $httpCode]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // JSON 디코딩 시도
        $errorResult = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            // 버퍼 클리어
            ob_clean();
            
            http_response_code(400);
            echo json_encode([
                'error' => 'Firebase 응답을 처리하는 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',
                'code' => 'INVALID_JSON_RESPONSE',
                'details' => ['http_code' => $httpCode, 'response' => $response]
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Firebase 에러 메시지 추출
        $errorMessage = '인증 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.';
        $errorCode = '';
        
        if (isset($errorResult['error']) && isset($errorResult['error']['message'])) {
            $errorCode = $errorResult['error']['message'];
            
            // Firebase 에러 메시지 한글화
            $errorMessages = [
                'INVALID_CODE' => '잘못된 인증번호입니다.',
                'EXPIRED_CODE' => '인증번호가 만료되었습니다. 다시 인증해주세요.',
                'TOO_MANY_ATTEMPTS_TRY_LATER' => '너무 많은 시도가 있었습니다. 잠시 후 다시 시도해주세요.',
                'SESSION_EXPIRED' => '인증 세션이 만료되었습니다. 다시 인증해주세요.',
                'INVALID_SESSION_INFO' => '인증 세션이 유효하지 않습니다. 다시 인증해주세요.',
                'MISSING_CODE' => '인증번호를 입력해주세요.',
                'MISSING_SESSION_INFO' => '인증 세션 정보가 누락되었습니다. 다시 시도해주세요.',
                'INVALID_PHONE_NUMBER' => '유효하지 않은 전화번호입니다.',
                'PHONE_NUMBER_NOT_FOUND' => '등록되지 않은 전화번호입니다.',
                'INVALID_ID_TOKEN' => '유효하지 않은 인증 토큰입니다.',
                'USER_DISABLED' => '비활성화된 계정입니다.',
                'USER_NOT_FOUND' => '존재하지 않는 계정입니다.',
                'INVALID_API_KEY' => '유효하지 않은 API 키입니다.',
                'API_KEY_NOT_FOUND' => 'API 키를 찾을 수 없습니다.'
            ];
            
            if (isset($errorMessages[$errorCode])) {
                $errorMessage = $errorMessages[$errorCode];
            }
        }
        
        // 버퍼 클리어
        ob_clean();
        
        // 에러 응답
        http_response_code(400);
        echo json_encode([
            'error' => $errorMessage,
            'code' => $errorCode,
            'details' => isset($errorResult['error']) ? $errorResult['error'] : null,
            'original_response' => $response // 디버깅용 원본 응답 추가
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // 응답이 비어있는지 확인
    if (empty($response)) {
        // 버퍼 클리어
        ob_clean();
        
        http_response_code(500);
        echo json_encode([
            'error' => 'Firebase 서버로부터 응답을 받지 못했습니다. 잠시 후 다시 시도해주세요.',
            'code' => 'EMPTY_RESPONSE'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // JSON 디코딩 시도
    $result = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("[verify-code] JSON 디코딩 오류: " . json_last_error_msg());
        error_log("[verify-code] 원본 응답: " . $response);
        
        // 버퍼 클리어
        ob_clean();
        
        http_response_code(500);
        echo json_encode([
            'error' => 'Firebase 응답을 처리하는 중 오류가 발생했습니다. 잠시 후 다시 시도해주세요.',
            'code' => 'JSON_DECODE_ERROR',
            'details' => json_last_error_msg()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // 응답 필드 확인
    $requiredFields = ['idToken', 'refreshToken', 'expiresIn', 'localId', 'phoneNumber'];
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($result[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        error_log("[verify-code] 필수 필드 누락: " . implode(', ', $missingFields));
        throw new Exception('Firebase 응답에 필수 정보가 누락되었습니다. 잠시 후 다시 시도해주세요.');
    }
    
    // 회원가입인 경우 DB에 사용자 정보 저장
    if (isset($data['nickname'])) {
        require_once __DIR__ . '/../includes/Database.php';
        $db = new Database();
        
        try {
            // 사용자 존재 여부 확인
            $checkSql = "SELECT id FROM users WHERE phone_number = ?";
            $existingUser = $db->query($checkSql, [$result['phoneNumber']])->fetch();
            
            if ($existingUser) {
                // 기존 사용자 정보 업데이트
                $updateSql = "UPDATE users SET nickname = ?, firebase_uid = ? WHERE phone_number = ?";
                $db->query($updateSql, [$data['nickname'], $result['localId'], $result['phoneNumber']]);
            } else {
                // 새 사용자 등록
                $insertSql = "INSERT INTO users (phone_number, nickname, firebase_uid) VALUES (?, ?, ?)";
                $db->query($insertSql, [$result['phoneNumber'], $data['nickname'], $result['localId']]);
            }
        } catch (Exception $e) {
            error_log("Error saving user data: " . $e->getMessage());
            // 사용자 저장 실패는 전체 인증을 실패시키지 않음
        }
    }
    
    // 버퍼 클리어
    ob_clean();
    
    // 성공 응답
    echo json_encode([
        'success' => true,
        'message' => '인증이 완료되었습니다.',
        'idToken' => $result['idToken'],
        'refreshToken' => $result['refreshToken'],
        'expiresIn' => $result['expiresIn'],
        'localId' => $result['localId'],
        'phoneNumber' => $result['phoneNumber']
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // 버퍼 클리어
    ob_clean();
    
    error_log("Error in verify-code.php: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Firebase API 에러인 경우 400 상태 코드 사용
    $isFirebaseError = strpos($e->getMessage(), 'Firebase') !== false;
    http_response_code($isFirebaseError ? 400 : 500);
    
    echo json_encode([
        'error' => $e->getMessage(),
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_UNESCAPED_UNICODE);
} finally {
    // 출력 버퍼 정리
    ob_end_flush();
}
