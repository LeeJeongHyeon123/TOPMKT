<?php
/**
 * Firestore 연결 테스트 스크립트
 * 
 * Firestore 연동이 제대로 작동하는지 확인합니다.
 */

// 부트스트랩 로드
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/app/bootstrap.php';

use App\Services\Firebase\FirebaseService;
use App\Repositories\Firebase\FirestoreRepository;

echo "Firestore 연결 테스트를 시작합니다...\n\n";

// 테스트 결과 상태
$success = true;

// 1. Firebase 서비스 초기화
try {
    echo "1. Firebase 서비스 초기화 중...\n";
    $firebaseService = FirebaseService::getInstance();
    echo "   - Firebase 서비스 초기화 성공\n";
    
    // Firestore 인스턴스 가져오기
    $firestore = $firebaseService->getFirestore();
    
    if ($firestore) {
        echo "   - Firestore 인스턴스 획득 성공\n";
    } else {
        echo "   - Firestore 인스턴스 획득 실패\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - Firebase 서비스 초기화 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 테스트를 계속할 수 있는지 확인
if (!$success) {
    echo "\n❌ Firebase 서비스 초기화에 실패했습니다. 테스트를 중단합니다.\n";
    exit(1);
}

// 2. Firestore Repository 테스트
try {
    echo "\n2. Firestore Repository 테스트 중...\n";
    
    // 테스트용 컬렉션 이름
    $collectionName = 'test_collection';
    
    // Repository 생성
    $repository = new FirestoreRepository($collectionName);
    echo "   - FirestoreRepository 생성 성공: {$collectionName}\n";
    
    // 테스트 문서 데이터
    $testDocument = [
        'title' => 'Firestore 테스트',
        'content' => '이것은 Firestore 연결 테스트 데이터입니다.',
        'timestamp' => time(),
        'isTest' => true
    ];
    
    // 문서 생성
    $documentId = $repository->createDocument($testDocument);
    
    if ($documentId) {
        echo "   - 테스트 문서 생성 성공 (ID: {$documentId})\n";
        
        // 문서 조회
        $document = $repository->getDocument($documentId);
        
        if ($document) {
            echo "   - 문서 조회 성공\n";
            echo "   - 문서 제목: {$document['title']}\n";
            echo "   - 타임스탬프: {$document['timestamp']}\n";
            
            // 문서 업데이트
            $updateData = [
                'content' => '이것은 업데이트된 Firestore 테스트 데이터입니다.',
                'updated_at' => time()
            ];
            
            if ($repository->updateDocument($documentId, $updateData)) {
                echo "   - 문서 업데이트 성공\n";
                
                // 업데이트된 문서 조회
                $updatedDocument = $repository->getDocument($documentId);
                
                if ($updatedDocument && $updatedDocument['content'] === $updateData['content']) {
                    echo "   - 업데이트된 문서 조회 성공\n";
                    echo "   - 업데이트된 내용: {$updatedDocument['content']}\n";
                } else {
                    echo "   - 업데이트된 문서 조회 실패\n";
                    $success = false;
                }
            } else {
                echo "   - 문서 업데이트 실패\n";
                $success = false;
            }
            
            // 컬렉션 조회
            $documents = $repository->getAllDocuments();
            echo "   - 컬렉션 내 문서 수: " . count($documents) . "\n";
            
            // 필터링 조회
            $filteredDocuments = $repository->getDocumentsWhere('isTest', '==', true);
            echo "   - 테스트 문서 필터링 결과 수: " . count($filteredDocuments) . "\n";
            
            // 문서 삭제
            if ($repository->deleteDocument($documentId)) {
                echo "   - 테스트 문서 삭제 성공\n";
            } else {
                echo "   - 테스트 문서 삭제 실패\n";
                $success = false;
            }
        } else {
            echo "   - 문서 조회 실패\n";
            $success = false;
        }
    } else {
        echo "   - 테스트 문서 생성 실패\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - Firestore Repository 테스트 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 테스트 결과 출력
echo "\n==================================================\n";
if ($success) {
    echo "✅ 모든 Firestore 연결 테스트가 성공적으로 완료되었습니다!\n";
    echo "   Firestore가 정상적으로 연동되었습니다.\n";
    echo "   기본정책에 따라, 채팅 메시지, 알림, 상태 변경 등 실시간 동기화가 필요한 데이터는 Firestore에 저장하세요.\n";
} else {
    echo "❌ Firestore 연결 테스트 중 일부 실패가 발생했습니다.\n";
    echo "   위 오류 메시지를 확인하고 문제를 해결해 주세요.\n";
}
echo "==================================================\n"; 