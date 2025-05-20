<?php
/**
 * Firebase Storage 서비스 클래스
 * 
 * Firebase Storage를 사용하여 파일 업로드, 다운로드, 삭제 등의 기능을 제공합니다.
 * 기본정책에 따라 이미지, 영상, 첨부파일 등 대용량 파일을 관리합니다.
 */

namespace App\Services\Firebase;

class StorageService
{
    /**
     * Firebase Storage 인스턴스
     */
    protected $storage;
    
    /**
     * Storage 버킷
     */
    protected $bucket;
    
    /**
     * Firebase 설정
     */
    protected $config;
    
    /**
     * 버킷 이름
     */
    protected $bucketName;
    
    /**
     * 생성자
     */
    public function __construct()
    {
        // Firebase 서비스 인스턴스 획득
        $firebaseService = FirebaseService::getInstance();
        $this->storage = $firebaseService->getStorage();
        $this->config = $firebaseService->getConfig();
        
        // 버킷 이름 추출
        $this->bucketName = $this->config['storage']['bucket'];
        
        // 버킷 이름 형식 수정 (gs:// 접두사 처리)
        if (strpos($this->bucketName, 'gs://') === 0) {
            $this->bucketName = substr($this->bucketName, 5);
        }
        
        error_log("Storage 버킷 이름: " . $this->bucketName);
        
        // 임시 디렉토리와 캐시 디렉토리 생성
        $this->ensureDirectoriesExist();
        
        // 버킷 획득 시도는 첫 번째 업로드 요청 시로 지연
    }
    
    /**
     * 버킷 인스턴스 가져오기 (지연 초기화)
     * 
     * @return \Google\Cloud\Storage\Bucket
     */
    protected function getBucket()
    {
        if ($this->bucket === null) {
            try {
                $this->bucket = $this->storage->getBucket($this->bucketName);
                error_log("Firebase Storage 버킷 접속 성공: {$this->bucketName}");
            } catch (\Exception $e) {
                error_log("Firebase Storage 버킷 접속 실패: " . $e->getMessage());
                throw $e;
            }
        }
        
        return $this->bucket;
    }
    
    /**
     * 필요한 디렉토리가 존재하는지 확인하고 없으면 생성
     */
    private function ensureDirectoriesExist()
    {
        $tempPath = $this->config['storage']['temp_path'];
        $cachePath = $this->config['storage']['cache_path'];
        
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0755, true);
        }
        
        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
    }
    
    /**
     * 파일 업로드
     * 
     * @param string $localFilePath 로컬 파일 경로
     * @param string $destination Storage에 저장될 경로
     * @param array $options 업로드 옵션
     * @return string|bool 성공 시 파일 URL, 실패 시 false
     */
    public function uploadFile($localFilePath, $destination, $options = [])
    {
        try {
            // 파일이 존재하는지 확인
            if (!file_exists($localFilePath)) {
                error_log("업로드할 파일이 존재하지 않습니다: {$localFilePath}");
                return false;
            }
            
            // 기본 옵션 설정
            $defaultOptions = [
                'name' => $destination,
                'predefinedAcl' => 'publicRead', // 공개 접근 가능하도록 설정
            ];
            
            // 사용자 옵션과 병합
            $uploadOptions = array_merge($defaultOptions, $options);
            
            // 파일 업로드 준비
            $bucket = $this->getBucket();
            
            // 파일 읽기
            $file = fopen($localFilePath, 'r');
            if (!$file) {
                error_log("파일을 읽을 수 없습니다: {$localFilePath}");
                return false;
            }
            
            // 콘텐츠 타입 추론
            $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($localFilePath);
            
            // 메타데이터 추가
            $uploadOptions['metadata'] = [
                'contentType' => $mimeType,
                'cacheControl' => 'public, max-age=86400'
            ];
            
            error_log("Firebase Storage 파일 업로드 시작: {$destination}");
            error_log("업로드 옵션: " . json_encode($uploadOptions));
            
            // 파일 업로드
            $object = $bucket->upload($file, $uploadOptions);
            
            // 파일 닫기
            fclose($file);
            
            // 캐시 디렉토리에 복사
            $this->saveToCache($localFilePath, $destination);
            
            // 공개 URL 반환
            error_log("Firebase Storage 파일 업로드 성공: {$destination}");
            return $object->info()['mediaLink'];
        } catch (\Exception $e) {
            error_log("Firebase Storage 파일 업로드 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 업로드된 파일 URL 가져오기
     * 
     * @param string $path Storage 경로
     * @param int $expiration URL 만료 시간(초, 기본 1시간)
     * @return string|bool 성공 시 URL, 실패 시 false
     */
    public function getFileUrl($path, $expiration = 3600)
    {
        try {
            $object = $this->getBucket()->object($path);
            
            if (!$object->exists()) {
                return false;
            }
            
            // 만료 시간 설정
            $expiration = new \DateTime('now + ' . $expiration . ' seconds');
            
            // 서명된 URL 생성
            return $object->signedUrl($expiration);
        } catch (\Exception $e) {
            error_log("Firebase Storage URL 생성 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 파일 다운로드
     * 
     * @param string $path Storage 경로
     * @param string|null $localPath 저장할 로컬 경로 (null인 경우 자동 생성)
     * @return string|bool 성공 시 로컬 파일 경로, 실패 시 false
     */
    public function downloadFile($path, $localPath = null)
    {
        try {
            $object = $this->getBucket()->object($path);
            
            if (!$object->exists()) {
                error_log("다운로드할 파일이 존재하지 않습니다: {$path}");
                return false;
            }
            
            // 저장 경로 설정
            if ($localPath === null) {
                $localPath = $this->config['storage']['cache_path'] . '/' . basename($path);
            }
            
            // 디렉토리 생성
            $dir = dirname($localPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            
            // 파일 다운로드
            $object->downloadToFile($localPath);
            
            return $localPath;
        } catch (\Exception $e) {
            error_log("Firebase Storage 파일 다운로드 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 파일 삭제
     * 
     * @param string $path Storage 경로
     * @return bool 성공 여부
     */
    public function deleteFile($path)
    {
        try {
            $object = $this->getBucket()->object($path);
            
            if (!$object->exists()) {
                error_log("삭제할 파일이 존재하지 않습니다: {$path}");
                return false;
            }
            
            // 파일 삭제
            $object->delete();
            
            // 캐시에서도 삭제
            $cachePath = $this->config['storage']['cache_path'] . '/' . basename($path);
            if (file_exists($cachePath)) {
                unlink($cachePath);
            }
            
            return true;
        } catch (\Exception $e) {
            error_log("Firebase Storage 파일 삭제 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 업로드된 파일 정보 가져오기
     * 
     * @param string $path Storage 경로
     * @return array|bool 성공 시 파일 정보 배열, 실패 시 false
     */
    public function getFileInfo($path)
    {
        try {
            $object = $this->getBucket()->object($path);
            
            if (!$object->exists()) {
                return false;
            }
            
            return $object->info();
        } catch (\Exception $e) {
            error_log("Firebase Storage 파일 정보 조회 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 로컬 파일을 캐시 디렉토리에 저장
     * 
     * @param string $localFilePath 로컬 파일 경로
     * @param string $destination Storage 경로
     * @return string|bool 성공 시 캐시 파일 경로, 실패 시 false
     */
    private function saveToCache($localFilePath, $destination)
    {
        try {
            $cachePath = $this->config['storage']['cache_path'] . '/' . basename($destination);
            
            // 캐시 디렉토리 없으면 생성
            if (!file_exists(dirname($cachePath))) {
                mkdir(dirname($cachePath), 0755, true);
            }
            
            // 파일 복사
            copy($localFilePath, $cachePath);
            
            return $cachePath;
        } catch (\Exception $e) {
            error_log("캐시 저장 오류: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 업로드된 파일 목록 가져오기
     * 
     * @param string $prefix 경로 접두사
     * @param int $maxResults 최대 결과 수
     * @return array 파일 목록 배열
     */
    public function listFiles($prefix = '', $maxResults = 50)
    {
        try {
            $options = [
                'prefix' => $prefix,
                'maxResults' => $maxResults,
            ];
            
            $objects = $this->getBucket()->objects($options);
            
            $files = [];
            foreach ($objects as $object) {
                $files[] = [
                    'name' => $object->name(),
                    'size' => $object->info()['size'],
                    'updated' => $object->info()['updated'],
                    'contentType' => $object->info()['contentType'],
                    'mediaLink' => $object->info()['mediaLink'],
                ];
            }
            
            return $files;
        } catch (\Exception $e) {
            error_log("Firebase Storage 파일 목록 조회 오류: " . $e->getMessage());
            return [];
        }
    }
} 