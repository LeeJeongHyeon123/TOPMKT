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
            $this->config = require APP_ROOT . '/config/firebase.php';
            
            // 인증서 파일 경로
            $credentialsFile = $this->config['credentials']['file'];
            
            // 파일 존재 확인
            if (!file_exists($credentialsFile)) {
                throw new \Exception("Firebase 인증 파일이 존재하지 않습니다: {$credentialsFile}");
            }
            
            // Factory 인스턴스 생성
            $factory = (new Factory())->withServiceAccount($credentialsFile);
            
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
     * 테스트를 위한 Firebase Auth 인스턴스 반환
     * 실제 프로덕션에서는 이 메서드 사용 지양
     * 
     * @return \Kreait\Firebase\Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }
} 