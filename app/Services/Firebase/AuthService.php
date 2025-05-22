<?php
/**
 * Firebase 인증 서비스 클래스
 * 
 * Firebase Authentication을 사용하여 휴대폰 번호 인증 및 사용자 관리를 담당합니다.
 * 기본정책에 따라 비밀번호 없이 휴대폰 번호만으로 인증합니다.
 */

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use GuzzleHttp\Client;

class AuthService
{
    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;
    
    /**
     * Firebase Authentication 인스턴스
     */
    private $auth;
    
    /**
     * Firestore 레포지토리 - 인증 시도 기록용
     */
    private $authAttemptsRepository;
    
    /**
     * 설정 배열
     */
    private $config;
    
    /**
     * 생성자 - 외부에서 인스턴스 생성 불가
     * Firebase Auth 초기화
     */
    private function __construct()
    {
        try {
            // Firebase 설정 로드
            $this->config = require __DIR__ . '/../../../config/firebase/firebase-config.php';
            
            // 인증서 파일 경로
            $credentialsFile = __DIR__ . '/../../../config/google/service-account.json';
            
            // 파일 존재 확인
            if (!file_exists($credentialsFile)) {
                throw new \Exception("Firebase 인증 파일이 존재하지 않습니다: {$credentialsFile}");
            }
            
            // Factory 인스턴스 생성
            $factory = (new Factory())
                ->withServiceAccount($credentialsFile)
                ->withDatabaseUri('https://topmkt-832f2.firebaseio.com');
            
            // Auth 인스턴스 생성
            $this->auth = $factory->createAuth();
            
            // 인증 시도 기록을 위한 Firestore 레포지토리 초기화
            try {
                $this->authAttemptsRepository = new \App\Repositories\Firebase\FirestoreRepository('auth_attempts');
            } catch (\Exception $e) {
                error_log('Firestore 레포지토리 초기화 오류: ' . $e->getMessage());
                // 오류 발생시 null로 설정하고 계속 진행
                $this->authAttemptsRepository = null;
            }
            
            error_log('Firebase Auth 초기화 성공');
        } catch (\Exception $e) {
            error_log('Firebase Auth 초기화 오류: ' . $e->getMessage());
            throw $e; // 상위로 오류 전파
        }
    }
    
    /**
     * 싱글톤 패턴 구현 - 인스턴스 반환
     * 
     * @return AuthService
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            try {
                self::$instance = new self();
            } catch (\Exception $e) {
                error_log('Firebase Auth 인스턴스 생성 오류: ' . $e->getMessage());
                // 오류가 발생해도 기본 인스턴스 생성
                self::$instance = new class extends AuthService {
                    public function __construct() {
                        // 빈 생성자로 대체
                        error_log('Firebase Auth 대체 인스턴스 생성됨');
                    }
                    
                    // 필수 메서드 오버라이드
                    public function sendVerificationCode($phoneNumber) {
                        return [
                            'success' => false,
                            'message' => '서비스 초기화에 실패했습니다. 관리자에게 문의하세요.'
                        ];
                    }
                };
            }
        }
        return self::$instance;
    }
    
    /**
     * 사용자가 존재하는지 확인
     * 
     * @param string $phoneNumber 전화번호 (E.164 형식: +[국가코드][전화번호])
     * @return bool 사용자 존재 여부
     */
    public function userExists($phoneNumber)
    {
        try {
            $user = $this->auth->getUserByPhoneNumber($phoneNumber);
            return true;
        } catch (UserNotFound $e) {
            return false;
        } catch (\Exception $e) {
            error_log('사용자 존재 확인 오류: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 인증번호 전송 가능 여부 확인
     * 기본정책: 인증번호 1시간 이내에 5번 틀리면 24시간 동안 인증 불가
     * 
     * @param string $phoneNumber 전화번호 (E.164 형식)
     * @return array ['allowed' => bool, 'message' => string, 'remainingTime' => int]
     */
    public function canSendVerificationCode($phoneNumber)
    {
        try {
            // Firestore 레포지토리가 초기화되지 않은 경우
            if ($this->authAttemptsRepository === null) {
                error_log('Firestore 레포지토리가 초기화되지 않아 인증 시도 제한을 확인할 수 없습니다.');
                return ['allowed' => true, 'message' => '인증번호를 전송할 수 있습니다.', 'remainingTime' => 0];
            }
            
            // 전화번호 정규화 (특수문자 제거)
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            // 인증 시도 기록 조회
            $attempts = $this->authAttemptsRepository->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
            
            if (empty($attempts)) {
                return ['allowed' => true, 'message' => '인증번호를 전송할 수 있습니다.', 'remainingTime' => 0];
            }
            
            // 최근 24시간 내 실패한 시도 횟수 계산
            $now = time();
            $oneDayAgo = $now - 86400; // 24시간
            $oneHourAgo = $now - 3600; // 1시간
            
            $failedCount = 0;
            $blockedUntil = 0;
            
            foreach ($attempts as $attempt) {
                // 차단된 경우 확인
                if (isset($attempt['blocked_until']) && $attempt['blocked_until'] > $now) {
                    $blockedUntil = $attempt['blocked_until'];
                    $remainingTime = $blockedUntil - $now;
                    
                    // 남은 시간 계산
                    $hours = floor($remainingTime / 3600);
                    $minutes = floor(($remainingTime % 3600) / 60);
                    
                    return [
                        'allowed' => false,
                        'message' => "인증이 차단되었습니다. {$hours}시간 {$minutes}분 후에 다시 시도해주세요.",
                        'remainingTime' => $remainingTime
                    ];
                }
                
                // 1시간 내 실패한 횟수 계산
                if (isset($attempt['timestamp']) && $attempt['timestamp'] > $oneHourAgo && isset($attempt['success']) && $attempt['success'] === false) {
                    $failedCount++;
                }
            }
            
            // 1시간 내 5번 이상 실패한 경우 차단
            if ($failedCount >= 5) {
                // 24시간 차단 설정
                $blockedUntil = $now + 86400;
                
                // 차단 기록 저장
                $this->authAttemptsRepository->createDocument([
                    'phone' => $normalizedPhone,
                    'timestamp' => $now,
                    'blocked_until' => $blockedUntil,
                    'success' => false,
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
                
                return [
                    'allowed' => false,
                    'message' => '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.',
                    'remainingTime' => 86400
                ];
            }
            
            return ['allowed' => true, 'message' => '인증번호를 전송할 수 있습니다.', 'remainingTime' => 0];
        } catch (\Exception $e) {
            error_log('인증번호 전송 가능 여부 확인 오류: ' . $e->getMessage());
            return ['allowed' => false, 'message' => '시스템 오류가 발생했습니다. 잠시 후 다시 시도해주세요.', 'remainingTime' => 0];
        }
    }
    
    /**
     * 인증 시도 기록 저장
     * 
     * @param string $phoneNumber 전화번호
     * @param bool $success 성공 여부
     * @param string $action 액션 타입 (send, verify)
     * @return void
     */
    public function logAuthAttempt($phoneNumber, $success, $action = 'verify')
    {
        try {
            // Firestore 레포지토리가 초기화되지 않은 경우
            if ($this->authAttemptsRepository === null) {
                error_log('Firestore 레포지토리가 초기화되지 않아 인증 시도를 기록할 수 없습니다.');
                return;
            }
            
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            $this->authAttemptsRepository->createDocument([
                'phone' => $normalizedPhone,
                'timestamp' => time(),
                'success' => $success,
                'action' => $action,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            error_log('인증 시도 기록 저장 오류: ' . $e->getMessage());
        }
    }
    
    /**
     * Firebase Auth 인스턴스 반환
     * 
     * @return Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }
    
    /**
     * 전화번호를 E.164 형식으로 변환
     * 
     * @param string $phoneNumber 전화번호
     * @return string E.164 형식의 전화번호
     */
    public function formatPhoneNumber($phoneNumber)
    {
        // 이미 E.164 형식인 경우 그대로 반환
        if (preg_match('/^\+[0-9]+$/', $phoneNumber)) {
            return $phoneNumber;
        }
        
        // 특수문자 제거
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // 010으로 시작하는 경우 +82로 변환
        if (substr($number, 0, 3) === '010') {
            $number = '82' . substr($number, 1);
        }
        
        // + 기호 추가
        return '+' . $number;
    }
    
    /**
     * 인증번호 전송
     * 
     * @param string $phoneNumber 전화번호 (E.164 형식)
     * @param string $recaptchaToken reCAPTCHA 토큰 (옵션)
     * @return array ['success' => bool, 'message' => string, 'sessionInfo' => string]
     */
    public function sendVerificationCode($phoneNumber, $recaptchaToken = null)
    {
        try {
            // 전화번호 정규화
            $normalizedPhone = $this->formatPhoneNumber($phoneNumber);
            error_log('정규화된 전화번호: ' . $normalizedPhone);
            
            // 인증번호 전송 가능 여부 확인
            $canSend = $this->canSendVerificationCode($normalizedPhone);
            if (!$canSend['allowed']) {
                return [
                    'success' => false,
                    'message' => $canSend['message']
                ];
            }
            
            // Firebase Authentication REST API를 사용하여 인증번호 전송
            $apiKey = $this->config['auth']['apiKey'];
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:sendVerificationCode?key={$apiKey}";
            
            $requestData = [
                'phoneNumber' => $normalizedPhone
            ];
            
            // reCAPTCHA 토큰이 제공된 경우 추가
            if ($recaptchaToken) {
                $requestData['recaptchaToken'] = $recaptchaToken;
            }
            
            error_log('인증번호 전송 API 요청: ' . json_encode(array_merge(
                $requestData, 
                ['recaptchaToken' => $recaptchaToken ? substr($recaptchaToken, 0, 20) . '...' : null]
            )));
            
            try {
                $client = new Client([
                    'timeout' => 30.0,
                    'connect_timeout' => 10.0,
                    'http_errors' => false,
                    'verify' => true // SSL 인증서 검증 활성화
                ]);
                
                $response = $client->post($url, [
                    'json' => $requestData,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ]);
                
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();
                
                error_log('인증번호 전송 API 응답 상태 코드: ' . $statusCode);
                error_log('인증번호 전송 API 응답 내용: ' . $responseBody);
                
                if ($statusCode >= 400) {
                    $errorData = json_decode($responseBody, true);
                    $errorMsg = isset($errorData['error']['message']) ? $errorData['error']['message'] : '상태 코드 ' . $statusCode;
                    throw new \Exception('API 오류 응답: ' . $errorMsg);
                }
                
                $result = json_decode($responseBody, true);
                
                if (empty($result)) {
                    throw new \Exception('API 응답이 비어있거나 유효하지 않은 JSON 형식입니다.');
                }
                
                if (isset($result['error'])) {
                    $errorMsg = $result['error']['message'] ?? '알 수 없는 오류';
                    error_log('Firebase 인증번호 전송 오류: ' . json_encode($result['error']));
                    throw new \Exception($errorMsg);
                }
                
                // 인증 시도 기록
                $this->logAuthAttempt($normalizedPhone, true, 'send');
                
                return [
                    'success' => true,
                    'message' => '인증번호가 전송되었습니다.',
                    'sessionInfo' => $result['sessionInfo']
                ];
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                error_log('인증번호 전송 API 요청 오류: ' . $e->getMessage());
                
                if ($e->hasResponse()) {
                    $responseBody = $e->getResponse()->getBody()->getContents();
                    error_log('API 오류 응답 내용: ' . $responseBody);
                    
                    $errorData = json_decode($responseBody, true);
                    if (isset($errorData['error']['message'])) {
                        $errorMsg = $errorData['error']['message'];
                        error_log('Firebase 오류 메시지: ' . $errorMsg);
                        
                        // 오류 메시지에 따른 사용자 친화적인 메시지
                        $errorMapping = [
                            'INVALID_PHONE_NUMBER' => '유효하지 않은 전화번호 형식입니다.',
                            'TOO_MANY_ATTEMPTS' => '너무 많은 시도로 인해 차단되었습니다. 잠시 후 다시 시도해주세요.',
                            'QUOTA_EXCEEDED' => '일일 인증 한도를 초과했습니다. 내일 다시 시도해주세요.',
                            'MISSING_RECAPTCHA_TOKEN' => 'reCAPTCHA 토큰이 필요합니다. 페이지를 새로고침 후 다시 시도해주세요.',
                            'INVALID_RECAPTCHA_TOKEN' => 'reCAPTCHA 인증에 실패했습니다. 페이지를 새로고침 후 다시 시도해주세요.'
                        ];
                        
                        $message = $errorMapping[$errorMsg] ?? '인증번호 전송 중 오류가 발생했습니다: ' . $errorMsg;
                        
                        // 인증 시도 기록
                        $this->logAuthAttempt($normalizedPhone, false, 'send');
                        
                        return [
                            'success' => false,
                            'message' => $message
                        ];
                    }
                }
                
                throw new \Exception('API 요청 오류: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            error_log('인증번호 전송 오류: ' . $e->getMessage());
            
            // 인증 시도 기록
            $this->logAuthAttempt($phoneNumber, false, 'send');
            
            return [
                'success' => false,
                'message' => '인증번호 전송에 실패했습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    /**
     * 인증 실패 횟수 초기화
     * 
     * @param string $phoneNumber 전화번호 (E.164 형식)
     * @return void
     */
    public function resetFailedAttempts($phoneNumber)
    {
        try {
            // Firestore 레포지토리가 초기화되지 않은 경우
            if ($this->authAttemptsRepository === null) {
                error_log('Firestore 레포지토리가 초기화되지 않아 인증 시도 기록을 초기화할 수 없습니다.');
                return;
            }
            
            // 전화번호 정규화 (특수문자 제거)
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            // 최근 1시간 내 실패 기록에 '초기화됨' 마킹 추가
            $now = time();
            $oneHourAgo = $now - 3600; // 1시간
            
            // 인증 시도 기록 조회
            $attempts = $this->authAttemptsRepository->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
            
            if (empty($attempts)) {
                return;
            }
            
            // 성공 표시 저장
            $this->authAttemptsRepository->createDocument([
                'phone' => $normalizedPhone,
                'timestamp' => $now,
                'success' => true,
                'action' => 'reset_attempts',
                'reset_timestamp' => $now,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
            
            error_log('인증 성공으로 실패 횟수가 초기화되었습니다: ' . $normalizedPhone);
        } catch (\Exception $e) {
            error_log('인증 시도 초기화 오류: ' . $e->getMessage());
        }
    }
    
    /**
     * 인증번호 확인 시도 횟수 및 남은 시도 횟수 계산
     * 
     * @param string $phoneNumber 전화번호 (E.164 형식)
     * @return array ['failedCount' => int, 'remainingAttempts' => int, 'isBlocked' => bool, 'blockedUntil' => int]
     */
    public function getVerificationAttempts($phoneNumber)
    {
        try {
            // Firestore 레포지토리가 초기화되지 않은 경우
            if ($this->authAttemptsRepository === null) {
                error_log('Firestore 레포지토리가 초기화되지 않아 인증 시도 횟수를 확인할 수 없습니다.');
                return [
                    'failedCount' => 0, 
                    'remainingAttempts' => 5, 
                    'isBlocked' => false, 
                    'blockedUntil' => 0
                ];
            }
            
            // 전화번호 정규화 (특수문자 제거)
            $normalizedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);
            
            // 인증 시도 기록 조회
            $attempts = $this->authAttemptsRepository->getDocumentsWhere('phone', '==', $normalizedPhone, 100);
            
            if (empty($attempts)) {
                return [
                    'failedCount' => 0, 
                    'remainingAttempts' => 5, 
                    'isBlocked' => false, 
                    'blockedUntil' => 0
                ];
            }
            
            // 최근 24시간 내 실패한 시도 횟수 계산
            $now = time();
            $oneDayAgo = $now - 86400; // 24시간
            $oneHourAgo = $now - 3600; // 1시간
            
            $failedCount = 0;
            $blockedUntil = 0;
            $isBlocked = false;
            $lastResetTime = 0;
            
            // 가장 최근 초기화 시간 찾기
            foreach ($attempts as $attempt) {
                if (
                    isset($attempt['action']) && 
                    $attempt['action'] === 'reset_attempts' && 
                    isset($attempt['reset_timestamp']) && 
                    $attempt['reset_timestamp'] > $lastResetTime
                ) {
                    $lastResetTime = $attempt['reset_timestamp'];
                }
            }
            
            foreach ($attempts as $attempt) {
                // 차단된 경우 확인
                if (isset($attempt['blocked_until']) && $attempt['blocked_until'] > $now) {
                    $blockedUntil = $attempt['blocked_until'];
                    $isBlocked = true;
                    break;
                }
                
                // 1시간 내 실패한 verify 횟수만 계산 (마지막 초기화 이후의 기록만)
                if (
                    isset($attempt['timestamp']) && 
                    $attempt['timestamp'] > $oneHourAgo && 
                    $attempt['timestamp'] > $lastResetTime &&
                    isset($attempt['success']) && 
                    $attempt['success'] === false &&
                    isset($attempt['action']) &&
                    $attempt['action'] === 'verify'
                ) {
                    $failedCount++;
                }
            }
            
            // 남은 시도 횟수 계산 (최대 5회)
            $remainingAttempts = max(0, 5 - $failedCount);
            
            return [
                'failedCount' => $failedCount,
                'remainingAttempts' => $remainingAttempts,
                'isBlocked' => $isBlocked,
                'blockedUntil' => $blockedUntil,
                'lastResetTime' => $lastResetTime
            ];
        } catch (\Exception $e) {
            error_log('인증 시도 횟수 확인 오류: ' . $e->getMessage());
            return [
                'failedCount' => 0, 
                'remainingAttempts' => 5, 
                'isBlocked' => false, 
                'blockedUntil' => 0
            ];
        }
    }
    
    /**
     * 인증번호 확인
     * 
     * @param string $phoneNumber 전화번호
     * @param string $code 인증번호
     * @param string $sessionInfo 세션 정보
     * @return array ['success' => bool, 'message' => string, 'idToken' => string, 'remainingAttempts' => int]
     */
    public function verifyCode($phoneNumber, $code, $sessionInfo)
    {
        try {
            // 전화번호 정규화
            $normalizedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // 인증 시도 횟수 확인
            $attempts = $this->getVerificationAttempts($normalizedPhone);
            
            // 디버그: 인증 시도 확인 결과 로깅
            error_log('인증번호 확인 시도 - 전화번호: ' . $normalizedPhone . ', 시도 결과: ' . json_encode($attempts));
            
            // 차단된 경우
            if ($attempts['isBlocked']) {
                $remainingTime = $attempts['blockedUntil'] - time();
                $hours = floor($remainingTime / 3600);
                $minutes = floor(($remainingTime % 3600) / 60);
                
                error_log('인증 차단 감지 - 전화번호: ' . $normalizedPhone . ', 남은 시간: ' . $hours . '시간 ' . $minutes . '분');
                
                return [
                    'success' => false,
                    'message' => "인증이 차단되었습니다. {$hours}시간 {$minutes}분 후에 다시 시도해주세요.",
                    'remainingAttempts' => 0,
                    'isBlocked' => true,
                    'blockedUntil' => $attempts['blockedUntil']
                ];
            }
            
            // 시도 횟수 초과
            if ($attempts['remainingAttempts'] <= 0) {
                // 24시간 차단 설정
                $now = time();
                $blockedUntil = $now + 86400;
                
                error_log('인증 시도 횟수 초과 - 전화번호: ' . $normalizedPhone . ', 차단 설정: ' . date('Y-m-d H:i:s', $blockedUntil));
                
                // 차단 기록 저장
                $this->authAttemptsRepository->createDocument([
                    'phone' => $normalizedPhone,
                    'timestamp' => $now,
                    'blocked_until' => $blockedUntil,
                    'success' => false,
                    'action' => 'verify',
                    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ]);
                
                return [
                    'success' => false,
                    'message' => '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.',
                    'remainingAttempts' => 0,
                    'isBlocked' => true,
                    'blockedUntil' => $blockedUntil
                ];
            }
            
            // Firebase Authentication REST API를 사용하여 인증번호 확인
            $apiKey = $this->config['auth']['apiKey'];
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPhoneNumber?key={$apiKey}";
            
            $requestData = [
                'phoneNumber' => $normalizedPhone,
                'code' => $code,
                'sessionInfo' => $sessionInfo
            ];
            
            error_log('인증번호 확인 API 요청: ' . json_encode($requestData, JSON_UNESCAPED_UNICODE));
            
            try {
                $client = new Client([
                    'timeout' => 30.0,
                    'connect_timeout' => 10.0,
                    'http_errors' => false,
                    'verify' => false // SSL 인증서 검증 비활성화
                ]);
                
                $response = $client->post($url, [
                    'json' => $requestData,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]
                ]);
                
                $statusCode = $response->getStatusCode();
                $responseBody = $response->getBody()->getContents();
                
                error_log('인증번호 확인 API 응답 상태 코드: ' . $statusCode);
                error_log('인증번호 확인 API 응답 내용: ' . $responseBody);
                
                if ($statusCode >= 400) {
                    throw new \Exception('API 오류 응답: 상태 코드 ' . $statusCode . ', 응답: ' . $responseBody);
                }
                
                $result = json_decode($responseBody, true);
                
                if (empty($result)) {
                    throw new \Exception('API 응답이 비어있거나 유효하지 않은 JSON 형식입니다.');
                }
                
                if (isset($result['error'])) {
                    $errorMsg = $result['error']['message'] ?? '알 수 없는 오류';
                    
                    // Firebase 오류 코드를 한국어 메시지로 변환
                    $errorMapping = [
                        'INVALID_CODE' => '인증번호가 일치하지 않습니다.',
                        'SESSION_EXPIRED' => '인증 세션이 만료되었습니다. 인증번호를 다시 요청해주세요.',
                        'TOO_MANY_ATTEMPTS' => '너무 많은 시도로 인해 차단되었습니다. 잠시 후 다시 시도해주세요.',
                        'INVALID_SESSION_INFO' => '잘못된 세션 정보입니다. 인증번호를 다시 요청해주세요.',
                        'INVALID_PHONE_NUMBER' => '유효하지 않은 전화번호 형식입니다.',
                        'QUOTA_EXCEEDED' => '일일 인증 한도를 초과했습니다. 내일 다시 시도해주세요.'
                    ];
                    
                    $message = $errorMapping[$errorMsg] ?? '인증 처리 중 오류가 발생했습니다: ' . $errorMsg;
                    
                    error_log('Firebase 인증 오류: ' . $errorMsg);
                    
                    // 인증 시도 기록
                    $this->logAuthAttempt($normalizedPhone, false, 'verify');
                    
                    // 남은 시도 횟수 업데이트
                    $updatedAttempts = $this->getVerificationAttempts($normalizedPhone);
                    
                    // 차단 상태 무시 - Firebase에서는 차단됐지만 우리 시스템에서는 아직 시도 횟수가 남아있는 경우
                    if ($errorMsg === 'TOO_MANY_ATTEMPTS') {
                        error_log('Firebase 차단 상태 감지, Firebase에서는 차단되었지만 로컬 시스템에서는 확인 필요');
                        
                        // 로컬 시스템의 남은 시도 횟수 확인
                        if ($updatedAttempts['remainingAttempts'] > 0) {
                            error_log('로컬 시스템에서는 시도 횟수가 남아있음: ' . $updatedAttempts['remainingAttempts']);
                            
                            // 로컬 시스템 기준으로 메시지 설정
                            $message = '인증번호가 일치하지 않습니다. 남은 시도 횟수: ' . $updatedAttempts['remainingAttempts'] . '회';
                            
                            // 시도 횟수 감소
                            $this->logAuthAttempt($normalizedPhone, false, 'verify');
                            
                            // 남은 시도 횟수 다시 확인
                            $updatedAttempts = $this->getVerificationAttempts($normalizedPhone);
                            
                            return [
                                'success' => false,
                                'message' => $message,
                                'remainingAttempts' => $updatedAttempts['remainingAttempts'],
                                'isBlocked' => false,  // 차단 상태를 false로 설정
                                'blockedUntil' => 0
                            ];
                        } else {
                            // 실제로 시도 횟수가 0이면 차단 메시지 그대로 유지
                            error_log('로컬 시스템에서도 시도 횟수가 없음, 차단 상태 유지');
                            
                            // 24시간 차단 설정
                            $now = time();
                            $blockedUntil = $now + 86400;
                            
                            return [
                                'success' => false,
                                'message' => '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.',
                                'remainingAttempts' => 0,
                                'isBlocked' => true,
                                'blockedUntil' => $blockedUntil
                            ];
                        }
                    }
                    
                    // 오류 메시지에 남은 시도 횟수 추가
                    if ($errorMsg === 'INVALID_CODE') {
                        if ($updatedAttempts['remainingAttempts'] <= 0) {
                            $message = '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.';
                        } else {
                            $message .= ' 남은 시도 횟수: ' . $updatedAttempts['remainingAttempts'] . '회';
                        }
                    }
                    
                    return [
                        'success' => false,
                        'message' => $message,
                        'remainingAttempts' => $updatedAttempts['remainingAttempts'],
                        'isBlocked' => $updatedAttempts['isBlocked'],
                        'blockedUntil' => $updatedAttempts['blockedUntil'] ?? 0
                    ];
                }
                
                // 인증 시도 기록
                $this->logAuthAttempt($normalizedPhone, true, 'verify');
                
                // 인증 성공 시 실패 횟수 초기화
                $this->resetFailedAttempts($normalizedPhone);
                
                return [
                    'success' => true,
                    'message' => '인증이 완료되었습니다.',
                    'idToken' => $result['idToken'] ?? null,
                    'remainingAttempts' => 5,
                    'isBlocked' => false
                ];
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                error_log('인증번호 확인 API 요청 오류: ' . $e->getMessage());
                
                if ($e->hasResponse()) {
                    $responseBody = $e->getResponse()->getBody()->getContents();
                    error_log('API 오류 응답 내용: ' . $responseBody);
                    
                    $errorData = json_decode($responseBody, true);
                    if (isset($errorData['error']['message'])) {
                        $errorMsg = $errorData['error']['message'];
                        error_log('Firebase 오류 메시지: ' . $errorMsg);
                        
                        // 특정 오류 메시지 처리
                        if ($errorMsg === 'INVALID_CODE') {
                            $this->logAuthAttempt($normalizedPhone, false, 'verify');
                            
                            // 남은 시도 횟수 업데이트
                            $updatedAttempts = $this->getVerificationAttempts($normalizedPhone);
                            
                            // 메시지에 항상 남은 시도 횟수 포함
                            $message = '인증번호가 일치하지 않습니다.';
                            if ($updatedAttempts['remainingAttempts'] <= 0) {
                                $message = '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.';
                            } else {
                                $message .= ' 남은 시도 횟수: ' . $updatedAttempts['remainingAttempts'] . '회';
                            }
                            
                            return [
                                'success' => false,
                                'message' => $message,
                                'remainingAttempts' => $updatedAttempts['remainingAttempts'],
                                'isBlocked' => $updatedAttempts['isBlocked'],
                                'blockedUntil' => $updatedAttempts['blockedUntil'] ?? 0
                            ];
                        }
                    }
                }
                
                // 인증 시도 기록
                $this->logAuthAttempt($normalizedPhone, false, 'verify');
                
                // 남은 시도 횟수 업데이트
                $updatedAttempts = $this->getVerificationAttempts($normalizedPhone);
                
                // 메시지에 항상 남은 시도 횟수 포함
                $message = '인증번호가 일치하지 않습니다.';
                if ($updatedAttempts['remainingAttempts'] <= 0) {
                    $message = '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.';
                } else {
                    $message .= ' 남은 시도 횟수: ' . $updatedAttempts['remainingAttempts'] . '회';
                }
                
                return [
                    'success' => false,
                    'message' => $message,
                    'remainingAttempts' => $updatedAttempts['remainingAttempts'],
                    'isBlocked' => $updatedAttempts['isBlocked'],
                    'blockedUntil' => $updatedAttempts['blockedUntil'] ?? 0
                ];
            }
        } catch (\Exception $e) {
            error_log('인증번호 확인 오류: ' . $e->getMessage());
            
            // 인증 시도 기록
            $this->logAuthAttempt($phoneNumber, false, 'verify');
            
            // 남은 시도 횟수 업데이트
            $updatedAttempts = $this->getVerificationAttempts($phoneNumber);
            
            // 메시지에 항상 남은 시도 횟수 포함
            $message = '인증번호가 일치하지 않습니다.';
            if ($updatedAttempts['remainingAttempts'] <= 0) {
                $message = '1시간 내 인증번호 5회 오류로 24시간 동안 인증이 차단되었습니다.';
            } else {
                $message .= ' 남은 시도 횟수: ' . $updatedAttempts['remainingAttempts'] . '회';
            }
            
            return [
                'success' => false,
                'message' => $message,
                'remainingAttempts' => $updatedAttempts['remainingAttempts'],
                'isBlocked' => $updatedAttempts['isBlocked'],
                'blockedUntil' => $updatedAttempts['blockedUntil'] ?? 0
            ];
        }
    }
    
    /**
     * 사용자 생성
     * 
     * @param string $phoneNumber 전화번호
     * @param string $nickname 닉네임
     * @return array ['success' => bool, 'message' => string, 'uid' => string]
     */
    public function createUser($phoneNumber, $nickname)
    {
        try {
            // 전화번호 정규화
            $normalizedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // 사용자 존재 여부 확인
            if ($this->userExists($normalizedPhone)) {
                return [
                    'success' => false,
                    'message' => '이미 가입된 전화번호입니다.'
                ];
            }
            
            // Firebase Authentication REST API를 사용하여 사용자 생성
            $apiKey = $this->config['auth']['apiKey'];
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key={$apiKey}";
            
            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'phoneNumber' => $normalizedPhone,
                    'displayName' => $nickname
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'verify' => false // SSL 인증서 검증 비활성화
            ]);
            
            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['error'])) {
                throw new \Exception($result['error']['message']);
            }
            
            return [
                'success' => true,
                'message' => '회원가입이 완료되었습니다.',
                'uid' => $result['localId']
            ];
        } catch (\Exception $e) {
            error_log('사용자 생성 오류: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => '회원가입에 실패했습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    /**
     * 사용자 삭제
     * 
     * @param string $uid 사용자 ID
     * @return array ['success' => bool, 'message' => string]
     */
    public function deleteUser($uid)
    {
        try {
            $this->auth->deleteUser($uid);
            
            return [
                'success' => true,
                'message' => '회원 탈퇴가 완료되었습니다.'
            ];
        } catch (\Exception $e) {
            error_log('사용자 삭제 오류: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => '회원 탈퇴에 실패했습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    /**
     * 커스텀 토큰 생성
     * 
     * @param string $uid 사용자 ID
     * @return array ['success' => bool, 'message' => string, 'token' => string]
     */
    public function createCustomToken($uid)
    {
        try {
            $token = $this->auth->createCustomToken($uid);
            
            return [
                'success' => true,
                'message' => '토큰이 생성되었습니다.',
                'token' => $token->toString()
            ];
        } catch (\Exception $e) {
            error_log('커스텀 토큰 생성 오류: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => '토큰 생성에 실패했습니다. 잠시 후 다시 시도해주세요.'
            ];
        }
    }
    
    /**
     * 토큰 검증
     * 
     * @param string $idToken Firebase 인증 토큰
     * @return array ['success' => bool, 'message' => string, 'phone_number' => string]
     */
    public function verifyIdToken($idToken)
    {
        try {
            // Firebase Authentication REST API를 사용하여 토큰 검증
            $apiKey = $this->config['auth']['apiKey'];
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key={$apiKey}";
            
            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'idToken' => $idToken
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ],
                'verify' => false // SSL 인증서 검증 비활성화
            ]);
            
            $result = json_decode($response->getBody()->getContents(), true);
            
            if (isset($result['error'])) {
                throw new \Exception($result['error']['message']);
            }
            
            if (!isset($result['users']) || count($result['users']) === 0) {
                return [
                    'success' => false,
                    'message' => '유효하지 않은 토큰입니다.'
                ];
            }
            
            $user = $result['users'][0];
            
            if (!isset($user['phoneNumber'])) {
                return [
                    'success' => false,
                    'message' => '전화번호 정보가 없는 토큰입니다.'
                ];
            }
            
            return [
                'success' => true,
                'message' => '토큰 검증 성공',
                'phone_number' => $user['phoneNumber'],
                'uid' => $user['localId'] ?? null,
                'email' => $user['email'] ?? null,
                'display_name' => $user['displayName'] ?? null
            ];
        } catch (\Exception $e) {
            error_log('토큰 검증 오류: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => '토큰 검증에 실패했습니다: ' . $e->getMessage()
            ];
        }
    }
} 