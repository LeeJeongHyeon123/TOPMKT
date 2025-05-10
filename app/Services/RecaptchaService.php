<?php
namespace App\Services;

use Google\Cloud\RecaptchaEnterprise\V1\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\TokenProperties\InvalidReason;

class RecaptchaService {
    private $client;
    private $projectId;
    private $siteKey;
    
    public function __construct() {
        $config = require __DIR__ . '/../../config/app.php';
        $this->client = new RecaptchaEnterpriseServiceClient([
            'credentials' => json_decode(file_get_contents(__DIR__ . '/../../config/recaptcha-enterprise-credentials.json'), true)
        ]);
        $this->projectId = $config['recaptcha']['project_id'];
        $this->siteKey = $config['recaptcha']['site_key'];
    }
    
    /**
     * reCAPTCHA 토큰을 검증합니다.
     * 
     * @param string $token reCAPTCHA 토큰
     * @param string $action 예상된 액션 (예: 'REGISTER', 'LOGIN')
     * @return array 검증 결과
     */
    public function verifyToken(string $token, string $action): array {
        try {
            error_log("reCAPTCHA 검증 시작 - 토큰: " . substr($token, 0, 50) . "...");
            error_log("예상된 액션: " . $action);
            
            $formattedParent = $this->client->projectName($this->projectId);
            
            // 이벤트 설정
            $event = (new Event())
                ->setSiteKey($this->siteKey)
                ->setToken($token)
                ->setExpectedAction($action);
            
            // 평가 요청
            $assessment = (new Assessment())
                ->setEvent($event);
            
            error_log("reCAPTCHA 평가 요청 생성 완료");
            
            // 평가 생성
            $response = $this->client->createAssessment(
                $formattedParent,
                $assessment
            );
            
            error_log("reCAPTCHA 평가 응답 수신");
            
            // 토큰 유효성 검사
            if (!$response->getTokenProperties()->getValid()) {
                $reason = InvalidReason::name($response->getTokenProperties()->getInvalidReason());
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