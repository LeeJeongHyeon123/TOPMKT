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
        $this->client = new RecaptchaEnterpriseServiceClient();
        $this->projectId = 'topmkt-832f2';
        $this->siteKey = '6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4';
    }
    
    /**
     * reCAPTCHA 토큰을 검증합니다.
     * 
     * @param string $token reCAPTCHA 토큰
     * @param string $action 예상된 액션 (예: 'LOGIN', 'PHONE_VERIFICATION')
     * @return array 검증 결과
     */
    public function verifyToken(string $token, string $action): array {
        try {
            $projectName = $this->client->projectName($this->projectId);
            
            // 이벤트 설정
            $event = (new Event())
                ->setSiteKey($this->siteKey)
                ->setToken($token);
            
            // 평가 요청
            $assessment = (new Assessment())
                ->setEvent($event);
            
            // 평가 생성
            $response = $this->client->createAssessment(
                $projectName,
                $assessment
            );
            
            // 토큰 유효성 검사
            if (!$response->getTokenProperties()->getValid()) {
                return [
                    'success' => false,
                    'error' => '토큰이 유효하지 않습니다: ' . 
                        InvalidReason::name($response->getTokenProperties()->getInvalidReason())
                ];
            }
            
            // 액션 검증
            if ($response->getTokenProperties()->getAction() !== $action) {
                return [
                    'success' => false,
                    'error' => '예상된 액션과 일치하지 않습니다'
                ];
            }
            
            // 위험 점수 확인
            $score = $response->getRiskAnalysis()->getScore();
            
            // 점수가 0.5 미만이면 봇으로 간주
            if ($score < 0.5) {
                return [
                    'success' => false,
                    'error' => '봇으로 의심되는 행동이 감지되었습니다',
                    'score' => $score
                ];
            }
            
            return [
                'success' => true,
                'score' => $score
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'reCAPTCHA 검증 중 오류가 발생했습니다: ' . $e->getMessage()
            ];
        }
    }
} 