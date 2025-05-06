<?php
/**
 * Firebase 연결 테스트 스크립트
 * 
 * Firebase Storage 연동이 제대로 작동하는지 확인합니다.
 */

// 부트스트랩 로드
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/app/bootstrap.php';

use App\Services\Firebase\FirebaseService;
use App\Services\Firebase\StorageService;

echo "Firebase 연결 테스트를 시작합니다...\n\n";

// 테스트 결과 상태
$success = true;

// 1. 인증 파일 확인
try {
    echo "1. Firebase 인증 파일 확인 중...\n";
    $credentialsFile = APP_ROOT . '/config/firebase/firebase-credentials.json';
    
    if (file_exists($credentialsFile)) {
        echo "   - 인증 파일 존재 확인: " . $credentialsFile . "\n";
        echo "   - 파일 크기: " . filesize($credentialsFile) . " 바이트\n";
    } else {
        echo "   - 인증 파일이 존재하지 않습니다: " . $credentialsFile . "\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - 인증 파일 확인 중 오류 발생: " . $e->getMessage() . "\n";
    $success = false;
}

// 2. config/firebase.php 로드 확인
try {
    echo "\n2. Firebase 설정 파일 로드 중...\n";
    $config = require APP_ROOT . '/config/firebase.php';
    
    echo "   - 설정 파일 로드 성공\n";
    echo "   - 프로젝트 ID: " . $config['project_id'] . "\n";
    echo "   - 인증 파일 경로: " . $config['credentials']['file'] . "\n";
    echo "   - Storage 버킷: " . $config['storage']['bucket'] . "\n";
    
    // 설정 파일의 경로가 실제 존재하는지 확인
    if (file_exists($config['credentials']['file'])) {
        echo "   - 설정에 지정된 인증 파일 존재 확인 완료\n";
    } else {
        echo "   - 설정에 지정된 인증 파일이 존재하지 않습니다: " . $config['credentials']['file'] . "\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - 설정 파일 로드 중 오류 발생: " . $e->getMessage() . "\n";
    $success = false;
}

// 3. Firebase 서비스 초기화 테스트
if ($success) {
    try {
        echo "\n3. Firebase 서비스 초기화 테스트 중...\n";
        // 글로벌 변수로 저장
        $GLOBALS['firebaseService'] = FirebaseService::getInstance();
        echo "   - Firebase 서비스 초기화 성공\n";
        
        // 설정 정보 획득
        $config = $GLOBALS['firebaseService']->getConfig();
        echo "   - 프로젝트 ID: {$config['project_id']}\n";
        echo "   - Storage 버킷: {$config['storage']['bucket']}\n";
    } catch (\Exception $e) {
        echo "   - Firebase 서비스 초기화 실패: " . $e->getMessage() . "\n";
        $success = false;
    }
}

// 4. Firebase Storage 연결 테스트
if ($success) {
    try {
        echo "\n4. Firebase Storage 연결 테스트 중...\n";
        $storageService = new StorageService();
        
        // 테스트 파일 생성
        $testFilePath = APP_ROOT . '/public/assets/images/temp/test_file.txt';
        $testFileContent = 'Firebase Storage 테스트 파일 ' . time();
        
        // 임시 디렉토리 생성
        if (!file_exists(dirname($testFilePath))) {
            mkdir(dirname($testFilePath), 0755, true);
        }
        
        // 테스트 파일 생성
        file_put_contents($testFilePath, $testFileContent);
        echo "   - 테스트 파일 생성 완료: {$testFilePath}\n";
        
        // Storage에 업로드
        $destination = 'test/test_file_' . time() . '.txt';
        $url = $storageService->uploadFile($testFilePath, $destination);
        
        if ($url) {
            echo "   - Storage 파일 업로드 성공\n";
            echo "   - 파일 URL: {$url}\n";
            
            // 파일 정보 조회
            $fileInfo = $storageService->getFileInfo($destination);
            if ($fileInfo) {
                echo "   - Storage 파일 정보 조회 성공\n";
                
                // 파일 다운로드
                $downloadPath = APP_ROOT . '/public/assets/images/temp/downloaded_test_file.txt';
                $result = $storageService->downloadFile($destination, $downloadPath);
                
                if ($result) {
                    echo "   - Storage 파일 다운로드 성공: {$downloadPath}\n";
                    
                    // 다운로드한 파일 내용 확인
                    $downloadedContent = file_get_contents($downloadPath);
                    if ($downloadedContent === $testFileContent) {
                        echo "   - 다운로드한 파일 내용 일치 확인\n";
                    } else {
                        echo "   - 다운로드한 파일 내용 불일치\n";
                        $success = false;
                    }
                    
                    // 로컬 다운로드 파일 삭제
                    unlink($downloadPath);
                } else {
                    echo "   - Storage 파일 다운로드 실패\n";
                    $success = false;
                }
                
                // 업로드한 파일 삭제
                if ($storageService->deleteFile($destination)) {
                    echo "   - Storage 파일 삭제 성공\n";
                } else {
                    echo "   - Storage 파일 삭제 실패\n";
                    $success = false;
                }
            } else {
                echo "   - Storage 파일 정보 조회 실패\n";
                $success = false;
            }
        } else {
            echo "   - Storage 파일 업로드 실패\n";
            $success = false;
        }
        
        // 테스트 로컬 파일 삭제
        unlink($testFilePath);
    } catch (\Exception $e) {
        echo "   - Firebase Storage 연결 테스트 실패: " . $e->getMessage() . "\n";
        $success = false;
    }
}

// 테스트 결과 출력
echo "\n==================================================\n";
if ($success) {
    echo "✅ 모든 Firebase 연결 테스트가 성공적으로 완료되었습니다!\n";
    echo "   Firebase Storage가 정상적으로 연동되었습니다.\n";
} else {
    echo "❌ Firebase 연결 테스트 중 일부 실패가 발생했습니다.\n";
    echo "   위 오류 메시지를 확인하고 문제를 해결해 주세요.\n";
}
echo "==================================================\n"; 