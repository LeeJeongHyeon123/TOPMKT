<?php
namespace App\Services;

use Exception;

class RecaptchaService {
    private $client;
    private $projectId;
    private $siteKey;
    
    public function __construct() {
        try {
            // Composer 오토로더 로드 확인
            if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
                require_once __DIR__ . '/../../vendor/autoload.php';
            }
            
            $config = require __DIR__ . '/../../config/app.php';
            $this->siteKey = $config['recaptcha']['site_key'];
            
            // Google 클래스 존재 확인 및 로드
            if (!class_exists('Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient')) {
                // 추가 디버깅 정보 기록
                error_log("Google RecaptchaEnterpriseServiceClient 클래스를 찾을 수 없습니다.");
                error_log("PHP 포함 경로: " . get_include_path());
                error_log("Composer 자동 로드 파일 존재 여부: " . (file_exists(__DIR__ . '/../../vendor/autoload.php') ? "존재함" : "존재하지 않음"));
                
                throw new Exception('Google reCAPTCHA Enterprise 라이브러리가 설치되지 않았습니다.');
            }
            
            // Google 서비스 계정 키 파일 확인
            $serviceAccountPath = __DIR__ . '/../../config/google/service-account.json';
            if (!file_exists($serviceAccountPath)) {
                error_log("서비스 계정 키 파일을 찾을 수 없습니다: {$serviceAccountPath}");
                throw new Exception('Google 서비스 계정 키 파일이 존재하지 않습니다.');
            }
            
            // 서비스 계정 키 파일 읽기 가능 여부 확인
            if (!is_readable($serviceAccountPath)) {
                error_log("서비스 계정 키 파일을 읽을 수 없습니다: {$serviceAccountPath}");
                throw new Exception('Google 서비스 계정 키 파일을 읽을 수 없습니다. 권한을 확인하세요.');
            }
            
            // 서비스 계정 키 파일 로드 및 확인
            $serviceAccountJson = file_get_contents($serviceAccountPath);
            if (!$serviceAccountJson) {
                error_log("서비스 계정 키 파일 내용을 로드할 수 없습니다.");
                throw new Exception('Google 서비스 계정 키 파일 내용을 로드할 수 없습니다.');
            }
            
            // 서비스 계정 JSON 유효성 확인
            $serviceAccount = json_decode($serviceAccountJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("서비스 계정 키 파일이 유효한 JSON 형식이 아닙니다: " . json_last_error_msg());
                throw new Exception('Google 서비스 계정 키 파일이 유효한 JSON 형식이 아닙니다.');
            }
            
            // RecaptchaEnterpriseServiceClient 인스턴스 생성
            $this->client = new \Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient([
                'credentials' => $serviceAccount
            ]);
            
            $this->projectId = $config['recaptcha']['project_id'];
            
            error_log("RecaptchaService 초기화 성공: 프로젝트 ID - {$this->projectId}");
        } catch (Exception $e) {
            error_log("reCAPTCHA 서비스 초기화 오류: " . $e->getMessage());
            error_log("스택 트레이스: " . $e->getTraceAsString());
            throw $e; // 오류 발생 시 예외를 다시 던져서 상위에서 처리하도록 함
        }
    }
    
    /**
     * reCAPTCHA 토큰을 검증합니다.
     * 
     * @param string $token reCAPTCHA 토큰
     * @param string $action 예상된 액션 (예: 'REGISTER', 'LOGIN', 'PHONE_VERIFICATION')
     * @return array 검증 결과
     */
    public function verifyToken(string $token, string $action): array {
        try {
            error_log("reCAPTCHA 검증 시작 - 토큰: " . substr($token, 0, 50) . "...");
            error_log("예상된 액션: " . $action);
            
            // Google 클래스 이름 접두사로 올바르게 사용
            $formattedParent = $this->client->projectName($this->projectId);
            
            // 이벤트 설정
            $event = new \Google\Cloud\RecaptchaEnterprise\V1\Event();
            $event->setSiteKey($this->siteKey);
            $event->setToken($token);
            $event->setExpectedAction($action);
            $event->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '');
            $event->setUserIpAddress($_SERVER['REMOTE_ADDR'] ?? '');
            
            // hashed_account_id deprecated 경고 회피를 위해 error_reporting 임시 변경
            $original_error_level = error_reporting();
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
            
            // 평가 요청
            $assessment = new \Google\Cloud\RecaptchaEnterprise\V1\Assessment();
            $assessment->setEvent($event);
            
            error_log("reCAPTCHA 평가 요청 생성 완료");
            
            // 평가 생성
            $response = $this->client->createAssessment(
                $formattedParent,
                $assessment
            );
            
            // 원래 error_reporting 레벨로 복원
            error_reporting($original_error_level);
            
            error_log("reCAPTCHA 평가 응답 수신");
            
            // 토큰 유효성 검사
            if (!$response->getTokenProperties()->getValid()) {
                $invalidReason = $response->getTokenProperties()->getInvalidReason();
                $reason = \Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason::name($invalidReason);
                error_log("토큰 유효성 검사 실패 - 이유: " . $reason);
                return [
                    'success' => false,
                    'error' => '토큰이 유효하지 않습니다: ' . $reason
                ];
            }
            
            // 액션 검증
            $actualAction = $response->getTokenProperties()->getAction();
            error_log("액션 검증 - 예상: {$action}, 실제: {$actualAction}");
            
            if ($actualAction !== $action) {
                error_log("액션 불일치");
                return [
                    'success' => false,
                    'error' => '예상된 액션과 일치하지 않습니다'
                ];
            }
            
            // 위험 점수 확인
            $score = $response->getRiskAnalysis()->getScore();
            error_log("reCAPTCHA 위험 점수: " . $score);
            
            // 점수가 0.3 미만이면 봇으로 간주
            if ($score < 0.3) {
                error_log("낮은 위험 점수로 인한 검증 실패: " . $score);
                return [
                    'success' => false,
                    'error' => '보안 검증에 실패했습니다. 잠시 후 다시 시도해주세요.',
                    'score' => $score
                ];
            }
            
            error_log("reCAPTCHA 검증 성공 - 점수: " . $score);
            return [
                'success' => true,
                'score' => $score
            ];
            
        } catch (\Exception $e) {
            error_log("reCAPTCHA 검증 중 오류 발생: " . $e->getMessage());
            error_log("스택 트레이스: " . $e->getTraceAsString());
            return [
                'success' => false,
                'error' => 'reCAPTCHA 검증 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
} 