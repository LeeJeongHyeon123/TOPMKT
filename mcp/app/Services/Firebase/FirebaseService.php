<?php
/**
 * Firebase 서비스 초기화 클래스
 * 
 * Firebase의 Firestore 및 Storage 서비스에 대한 연결을 관리합니다.
 * 싱글톤 패턴을 사용하여 하나의 인스턴스만 생성합니다.
 */

namespace App\Services\Firebase;

use Kreait\Firebase\Factory;
use GuzzleHttp\Client;

class FirebaseService
{
    /**
     * 싱글톤 인스턴스
     */
    private static $instance = null;
    
    /**
     * Firebase 인스턴스
     */
    private $firebase;
    
    /**
     * Firestore REST API 클라이언트
     */
    private $firestoreClient;
    
    /**
     * Firebase Storage 인스턴스
     */
    private $storage;
    
    /**
     * 설정 배열
     */
    private $config;
    
    /**
     * Firebase 액세스 토큰
     */
    private $accessToken;
    
    /**
     * 생성자 - 외부에서 인스턴스 생성 불가
     * Firebase 서비스 초기화
     */
    private function __construct()
    {
        try {
            // Firebase 설정 로드
            $this->config = require __DIR__ . '/../../../config/firebase/config.php';
            
            // 인증서 파일 경로
            $credentialsFile = $this->config['credentials']['file'];
            
            // 파일 존재 확인
            if (!file_exists($credentialsFile)) {
                throw new \Exception("Firebase 인증 파일이 존재하지 않습니다: {$credentialsFile}");
            }
            
            // Factory 인스턴스 생성 - Storage용
            $factory = (new Factory())->withServiceAccount($credentialsFile);
            $this->firebase = $factory;
            
            // Storage 초기화
            $this->storage = $factory->createStorage();
            
            // 버킷 정보 디버깅
            $bucketName = $this->config['storage']['bucket'];
            error_log('Firebase Storage 버킷 접근 시도: ' . $bucketName);
            
            // 버킷 접근 시도
            try {
                $bucket = $this->storage->getBucket(str_replace('gs://', '', $bucketName));
                error_log('Firebase Storage 버킷 접근 성공: ' . $bucketName);
            } catch (\Exception $e) {
                error_log('Firebase Storage 버킷 접근 실패: ' . $e->getMessage());
                // 오류를 상위로 전파하지 않고 일단 기록만 하고 진행
            }
            
            // 기본 정책에 따라 스토리지 초기화 확인
            error_log('Firebase Storage 초기화 성공 - 기본정책: 이미지, 영상, 첨부파일 등 대용량 파일은 Firebase Storage에 저장');
            
            // Firestore REST API 클라이언트 초기화
            try {
                // 서비스 계정에서 OAuth 2.0 액세스 토큰 생성
                $this->createAccessToken($credentialsFile);
                
                $this->firestoreClient = new Client([
                    'base_uri' => 'https://firestore.googleapis.com/v1/',
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->accessToken,
                        'Content-Type' => 'application/json',
                    ],
                ]);
                
                error_log('Firebase Firestore REST API 초기화 성공 - 기본정책: 채팅 메시지, 알림, 상태 변경 등 실시간 동기화가 필요한 데이터는 Firestore에 저장');
            } catch (\Exception $e) {
                error_log('Firebase Firestore REST API 초기화 실패: ' . $e->getMessage());
                $this->firestoreClient = null;
            }
        } catch (\Exception $e) {
            error_log('Firebase 초기화 오류: ' . $e->getMessage());
            throw $e; // 상위로 오류 전파
        }
    }
    
    /**
     * 서비스 계정에서 OAuth 2.0 액세스 토큰 생성
     * 
     * @param string $credentialsFile 인증 파일 경로
     * @return void
     */
    private function createAccessToken($credentialsFile)
    {
        // 서비스 계정 키 파일 읽기
        $serviceAccount = json_decode(file_get_contents($credentialsFile), true);
        
        $now = time();
        
        // JWT 클레임 설정
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'sub' => $serviceAccount['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/cloud-platform'
        ];
        
        // Header
        $header = [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => $serviceAccount['private_key_id']
        ];
        
        // Base64Url 인코딩
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($header)));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));
        
        // 서명 생성
        $privateKey = $serviceAccount['private_key'];
        $signature = '';
        openssl_sign($base64UrlHeader . '.' . $base64UrlPayload, $signature, $privateKey, 'SHA256');
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        // JWT 생성
        $jwt = $base64UrlHeader . '.' . $base64UrlPayload . '.' . $base64UrlSignature;
        
        // Google OAuth 2.0 토큰 엔드포인트에 요청
        $client = new Client();
        $response = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]
        ]);
        
        $result = json_decode($response->getBody()->getContents(), true);
        $this->accessToken = $result['access_token'];
    }
    
    /**
     * 싱글톤 패턴 구현 - 인스턴스 반환
     * 
     * @return FirebaseService
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Firestore REST API 클라이언트 인스턴스 반환
     * 
     * @return \GuzzleHttp\Client|null
     */
    public function getFirestore()
    {
        return $this->firestoreClient;
    }
    
    /**
     * Firebase Storage 인스턴스 반환
     * 
     * @return \Kreait\Firebase\Contract\Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
    
    /**
     * Firebase 설정 정보 반환
     * 
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * Firebase 프로젝트 ID 반환
     * 
     * @return string
     */
    public function getProjectId()
    {
        return $this->config['database']['url'] ? explode('.', parse_url($this->config['database']['url'], PHP_URL_HOST))[0] : null;
    }
} 