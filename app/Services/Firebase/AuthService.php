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
            $this->config = require __DIR__ . '/../../../config/firebase/config.php';
            
            // 인증서 파일 경로
            $credentialsFile = __DIR__ . '/../../../config/firebase/firebase-credentials.json';
            
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
            $this->authAttemptsRepository = new \App\Repositories\Firebase\FirestoreRepository('auth_attempts');
            
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
            self::$instance = new self();
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
     * @param string $phoneNumber 전화번호
     * @return array ['success' => bool, 'message' => string, 'sessionInfo' => string]
     *
     * [설명]
     * 1. 전화번호를 정규화(국제표준 E.164 형식)한다.
     * 2. 인증번호 전송 가능 여부(횟수 제한 등)를 체크한다.
     * 3. Firebase REST API(accounts:sendVerificationCode)로 인증번호 발송을 요청한다.
     * 4. 성공 시 sessionInfo(세션 정보)를 반환한다.
     * 5. 실패 시 에러 메시지를 반환한다.
     */
    public function sendVerificationCode($phoneNumber)
    {
        try {
            // 전화번호 정규화
            $normalizedPhone = $this->formatPhoneNumber($phoneNumber);
            
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
            
            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'phoneNumber' => $normalizedPhone
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
            
            // 인증 시도 기록
            $this->logAuthAttempt($normalizedPhone, true, 'send');
            
            return [
                'success' => true,
                'message' => '인증번호가 전송되었습니다.',
                'sessionInfo' => $result['sessionInfo']
            ];
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
     * 인증번호 확인
     * 
     * @param string $phoneNumber 전화번호
     * @param string $code 인증번호
     * @param string $sessionInfo 세션 정보
     * @return array ['success' => bool, 'message' => string, 'idToken' => string]
     *
     * [설명]
     * 1. 전화번호를 정규화(국제표준 E.164 형식)한다.
     * 2. Firebase REST API(accounts:verifyPhoneNumber)로 인증번호 일치 여부를 확인한다.
     * 3. 성공 시 idToken(로그인 토큰) 등 인증 결과를 반환한다.
     * 4. 실패 시 에러 메시지를 반환한다.
     */
    public function verifyCode($phoneNumber, $code, $sessionInfo)
    {
        try {
            // 전화번호 정규화
            $normalizedPhone = $this->formatPhoneNumber($phoneNumber);
            
            // Firebase Authentication REST API를 사용하여 인증번호 확인
            $apiKey = $this->config['auth']['apiKey'];
            $url = "https://identitytoolkit.googleapis.com/v1/accounts:verifyPhoneNumber?key={$apiKey}";
            
            $client = new Client();
            $response = $client->post($url, [
                'json' => [
                    'phoneNumber' => $normalizedPhone,
                    'code' => $code,
                    'sessionInfo' => $sessionInfo
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
            
            // 인증 시도 기록
            $this->logAuthAttempt($normalizedPhone, true, 'verify');
            
            return [
                'success' => true,
                'message' => '인증이 완료되었습니다.',
                'idToken' => $result['idToken']
            ];
        } catch (\Exception $e) {
            error_log('인증번호 확인 오류: ' . $e->getMessage());
            
            // 인증 시도 기록
            $this->logAuthAttempt($phoneNumber, false, 'verify');
            
            return [
                'success' => false,
                'message' => '인증번호가 일치하지 않습니다. 다시 확인해주세요.'
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
} 