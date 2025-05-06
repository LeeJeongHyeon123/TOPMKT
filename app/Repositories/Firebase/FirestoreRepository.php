<?php
/**
 * Firestore 데이터 접근 Repository 클래스
 * 
 * Firestore 컬렉션에 대한 데이터 접근을 추상화합니다.
 * 기본적인 CRUD 작업을 제공합니다.
 */

namespace App\Repositories\Firebase;

use App\Services\Firebase\FirebaseService;
use GuzzleHttp\Exception\GuzzleException;

class FirestoreRepository
{
    /**
     * Firestore 클라이언트
     */
    protected $firestore;
    
    /**
     * 프로젝트 ID
     */
    protected $projectId;
    
    /**
     * 컬렉션 이름
     */
    protected $collectionName;
    
    /**
     * 기본 데이터베이스 경로
     */
    protected $databasePath;
    
    /**
     * 생성자
     * 
     * @param string $collectionName 컬렉션 이름
     */
    public function __construct($collectionName)
    {
        $firebaseService = FirebaseService::getInstance();
        $this->firestore = $firebaseService->getFirestore();
        $this->projectId = $firebaseService->getProjectId();
        $this->collectionName = $collectionName;
        
        $this->databasePath = "projects/{$this->projectId}/databases/(default)/documents";
    }
    
    /**
     * 컬렉션 경로 가져오기
     * 
     * @return string
     */
    protected function getCollectionPath()
    {
        return "{$this->databasePath}/{$this->collectionName}";
    }
    
    /**
     * Firestore 값을 PHP 값으로 변환
     * 
     * @param array $fields Firestore 필드 배열
     * @return array 변환된 데이터
     */
    protected function convertFromFirestoreValue($fields)
    {
        if (empty($fields)) {
            return [];
        }

        $result = [];
        foreach ($fields as $key => $value) {
            $type = key($value);
            $typedValue = $value[$type];
            
            switch ($type) {
                case 'stringValue':
                    $result[$key] = $typedValue;
                    break;
                case 'integerValue':
                    $result[$key] = (int) $typedValue;
                    break;
                case 'doubleValue':
                    $result[$key] = (float) $typedValue;
                    break;
                case 'booleanValue':
                    $result[$key] = (bool) $typedValue;
                    break;
                case 'nullValue':
                    $result[$key] = null;
                    break;
                case 'timestampValue':
                    $result[$key] = strtotime($typedValue);
                    break;
                case 'arrayValue':
                    $arrayItems = [];
                    if (isset($typedValue['values'])) {
                        foreach ($typedValue['values'] as $item) {
                            $type = key($item);
                            $arrayItems[] = $item[$type];
                        }
                    }
                    $result[$key] = $arrayItems;
                    break;
                case 'mapValue':
                    if (isset($typedValue['fields'])) {
                        $result[$key] = $this->convertFromFirestoreValue($typedValue['fields']);
                    } else {
                        $result[$key] = [];
                    }
                    break;
                default:
                    $result[$key] = $typedValue;
            }
        }
        
        return $result;
    }
    
    /**
     * PHP 값을 Firestore 값으로 변환
     * 
     * @param mixed $value 변환할 값
     * @return array Firestore 형식 값
     */
    protected function convertToFirestoreValue($value)
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string)$value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif (is_null($value)) {
            return ['nullValue' => null];
        } elseif (is_array($value)) {
            // 연관 배열 확인 (map)
            if (!empty($value) && !isset($value[0]) && array_keys($value) !== range(0, count($value) - 1)) {
                $fields = [];
                foreach ($value as $k => $v) {
                    $fields[$k] = $this->convertToFirestoreValue($v);
                }
                return ['mapValue' => ['fields' => $fields]];
            }
            
            // 인덱스 배열 (array)
            $values = [];
            foreach ($value as $item) {
                $values[] = $this->convertToFirestoreValue($item);
            }
            return ['arrayValue' => ['values' => $values]];
        }
        
        // 기본값은 문자열로 처리
        return ['stringValue' => (string)$value];
    }
    
    /**
     * 문서 데이터를 Firestore 형식으로 변환
     * 
     * @param array $data 원본 데이터
     * @return array 변환된 데이터
     */
    protected function convertToFirestoreData($data)
    {
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[$key] = $this->convertToFirestoreValue($value);
        }
        
        return ['fields' => $fields];
    }
    
    /**
     * 문서 가져오기
     * 
     * @param string $id 문서 ID
     * @return array|null 문서 데이터
     */
    public function getDocument($id)
    {
        try {
            if (!$this->firestore) {
                return null;
            }
            
            $response = $this->firestore->get("{$this->getCollectionPath()}/{$id}");
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['fields'])) {
                $convertedData = $this->convertFromFirestoreValue($data['fields']);
                $convertedData['id'] = $id;
                return $convertedData;
            }
            
            return null;
        } catch (GuzzleException $e) {
            error_log('Firestore 문서 조회 오류: ' . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            error_log('Firestore 문서 조회 처리 오류: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * 컬렉션의 모든 문서 가져오기
     * 
     * @param int $limit 최대 문서 수
     * @return array 문서 배열
     */
    public function getAllDocuments($limit = 50)
    {
        try {
            if (!$this->firestore) {
                return [];
            }
            
            $response = $this->firestore->get("{$this->getCollectionPath()}?pageSize={$limit}");
            $data = json_decode($response->getBody()->getContents(), true);
            
            $documents = [];
            
            if (isset($data['documents']) && is_array($data['documents'])) {
                foreach ($data['documents'] as $document) {
                    if (isset($document['fields'])) {
                        $id = basename($document['name']);
                        $documentData = $this->convertFromFirestoreValue($document['fields']);
                        $documentData['id'] = $id;
                        $documents[] = $documentData;
                    }
                }
            }
            
            return $documents;
        } catch (GuzzleException $e) {
            error_log('Firestore 컬렉션 조회 오류: ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            error_log('Firestore 컬렉션 조회 처리 오류: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 필터링된 문서 가져오기
     * 참고: Firestore REST API에서는 복잡한 쿼리를 StructuredQuery로 구현해야 함
     * 현재 버전에서는 모든 문서를 가져와서 PHP 단에서 필터링함
     * 
     * @param string $field 필드명
     * @param string $operator 연산자 (==, >, <, >=, <=)
     * @param mixed $value 값
     * @param int $limit 최대 문서 수
     * @return array 문서 배열
     */
    public function getDocumentsWhere($field, $operator, $value, $limit = 50)
    {
        try {
            $allDocuments = $this->getAllDocuments(100); // 최대 100개 문서 조회
            $filteredDocuments = [];
            $count = 0;
            
            foreach ($allDocuments as $document) {
                if ($count >= $limit) {
                    break;
                }
                
                if (!isset($document[$field])) {
                    continue;
                }
                
                $matches = false;
                
                switch ($operator) {
                    case '==':
                        $matches = $document[$field] == $value;
                        break;
                    case '===':
                        $matches = $document[$field] === $value;
                        break;
                    case '>':
                        $matches = $document[$field] > $value;
                        break;
                    case '>=':
                        $matches = $document[$field] >= $value;
                        break;
                    case '<':
                        $matches = $document[$field] < $value;
                        break;
                    case '<=':
                        $matches = $document[$field] <= $value;
                        break;
                    case '!=':
                        $matches = $document[$field] != $value;
                        break;
                    case '!==':
                        $matches = $document[$field] !== $value;
                        break;
                    case 'array-contains':
                        $matches = is_array($document[$field]) && in_array($value, $document[$field]);
                        break;
                }
                
                if ($matches) {
                    $filteredDocuments[] = $document;
                    $count++;
                }
            }
            
            return $filteredDocuments;
        } catch (\Exception $e) {
            error_log('Firestore 필터링 조회 오류: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 문서 생성
     * 
     * @param array $data 문서 데이터
     * @param string|null $id 문서 ID (선택사항)
     * @return string|false 성공 시 문서 ID, 실패 시 false
     */
    public function createDocument($data, $id = null)
    {
        try {
            if (!$this->firestore) {
                return false;
            }
            
            $firestoreData = $this->convertToFirestoreData($data);
            
            if ($id) {
                $documentPath = "{$this->getCollectionPath()}/{$id}";
                $this->firestore->patch($documentPath, [
                    'json' => $firestoreData,
                    'query' => ['updateMask.fieldPaths' => '*']
                ]);
                return $id;
            } else {
                $response = $this->firestore->post("{$this->getCollectionPath()}", [
                    'json' => $firestoreData
                ]);
                
                $responseData = json_decode($response->getBody()->getContents(), true);
                if (isset($responseData['name'])) {
                    return basename($responseData['name']);
                }
                return false;
            }
        } catch (GuzzleException $e) {
            error_log('Firestore 문서 생성 오류: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('Firestore 문서 생성 처리 오류: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 문서 업데이트
     * 
     * @param string $id 문서 ID
     * @param array $data 업데이트할 데이터
     * @return bool 성공 여부
     */
    public function updateDocument($id, $data)
    {
        try {
            if (!$this->firestore) {
                return false;
            }
            
            // 현재 문서 조회 후 데이터 병합
            $currentDocument = $this->getDocument($id);
            
            if (!$currentDocument) {
                return false;
            }
            
            // ID 필드 제거 (문서 ID는 별도로 관리)
            unset($currentDocument['id']);
            
            // 새 데이터 병합
            $mergedData = array_merge($currentDocument, $data);
            
            // 전체 문서 업데이트
            $firestoreData = $this->convertToFirestoreData($mergedData);
            $documentPath = "{$this->getCollectionPath()}/{$id}";
            
            // 문서 교체
            $this->firestore->patch($documentPath, [
                'json' => $firestoreData
            ]);
            
            return true;
        } catch (GuzzleException $e) {
            error_log('Firestore 문서 업데이트 오류: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('Firestore 문서 업데이트 처리 오류: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 문서 삭제
     * 
     * @param string $id 문서 ID
     * @return bool 성공 여부
     */
    public function deleteDocument($id)
    {
        try {
            if (!$this->firestore) {
                return false;
            }
            
            $documentPath = "{$this->getCollectionPath()}/{$id}";
            $this->firestore->delete($documentPath);
            
            return true;
        } catch (GuzzleException $e) {
            error_log('Firestore 문서 삭제 오류: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('Firestore 문서 삭제 처리 오류: ' . $e->getMessage());
            return false;
        }
    }
} 