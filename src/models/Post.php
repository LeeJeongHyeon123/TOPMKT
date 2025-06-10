<?php
/**
 * 게시글 모델 클래스
 */

require_once SRC_PATH . '/helpers/CacheHelper.php';

class Post {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        require_once SRC_PATH . '/config/database.php';
        $this->db = Database::getInstance();
    }
    
    /**
     * 게시글 목록 조회
     *
     * @param int $page 페이지 번호
     * @param int $pageSize 페이지당 항목 수
     * @param string|null $search 검색어
     * @return array 게시글 목록
     */
    public function getList($page = 1, $pageSize = 20, $search = null) {
        // 큰 페이지는 최적화된 방식 사용
        if ($page > 500) {
            return $this->getListOptimized($page, $pageSize, $search);
        }
        
        // 작은 페이지는 기존 방식 사용
        return $this->getListWithOffset($page, $pageSize, $search);
    }
    
    /**
     * 커서 기반 게시글 목록 조회 (큰 페이지 최적화)
     *
     * @param int $page 페이지 번호
     * @param int $pageSize 페이지당 항목 수
     * @param string|null $search 검색어
     * @return array 게시글 목록
     */
    public function getListOptimized($page = 1, $pageSize = 20, $search = null) {
        // 캐시 키 생성
        $cacheKey = CacheHelper::getPostListCacheKey($page, $pageSize, $search) . '_optimized';
        
        return CacheHelper::remember($cacheKey, function() use ($page, $pageSize, $search) {
            $params = [];
            
            if ($page <= 500) {
                // 첫 500페이지는 기존 OFFSET 방식
                return $this->getListWithOffset($page, $pageSize, $search);
            }
            
            // 큰 페이지는 커서 방식 사용
            // 먼저 해당 페이지의 시작 시간을 찾음
            $skipCount = ($page - 1) * $pageSize;
            
            $timeStmt = $this->db->prepare("
                SELECT created_at 
                FROM posts 
                WHERE status = 'published'
                ORDER BY created_at DESC 
                LIMIT 1 OFFSET :skip_count
            ");
            $timeStmt->bindValue(':skip_count', $skipCount, \PDO::PARAM_INT);
            $timeStmt->execute();
            $startTime = $timeStmt->fetchColumn();
            
            if (!$startTime) {
                return []; // 해당 페이지에 데이터 없음
            }
            
            // 커서 기반으로 데이터 조회
            $sql = "
                SELECT 
                    p.id,
                    p.user_id,
                    p.title,
                    LEFT(p.content, 200) as content_preview,
                    p.view_count,
                    p.like_count,
                    p.comment_count,
                    p.status,
                    p.created_at,
                    u.nickname as author_name,
                    u.profile_image
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.status = 'published'
                AND p.created_at <= :start_time
            ";
            
            $params[':start_time'] = $startTime;
            
            if ($search) {
                $sql .= " AND (
                    p.title LIKE :search_title 
                    OR LEFT(p.content, 500) LIKE :search_content
                )";
                $params[':search_title'] = "%$search%";
                $params[':search_content'] = "%$search%";
            }
            
            $sql .= " ORDER BY p.created_at DESC LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $pageSize, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }, $page > 1000 ? 1800 : 300); // 큰 페이지는 30분, 작은 페이지는 5분 캐시
    }
    
    /**
     * OFFSET 방식 게시글 목록 조회
     */
    private function getListWithOffset($page, $pageSize, $search) {
        $offset = ($page - 1) * $pageSize;
        $params = [];
        
        $sql = "
            SELECT 
                p.id,
                p.user_id,
                p.title,
                LEFT(p.content, 200) as content_preview,
                p.view_count,
                p.like_count,
                p.comment_count,
                p.status,
                p.created_at,
                u.nickname as author_name,
                u.profile_image
            FROM posts p
            FORCE INDEX (idx_posts_list_performance)
            JOIN users u ON p.user_id = u.id
            WHERE p.status = 'published'
        ";
        
        if ($search) {
            $sql .= " AND (
                p.title LIKE :search_title 
                OR LEFT(p.content, 500) LIKE :search_content
            )";
            $params[':search_title'] = "%$search%";
            $params[':search_content'] = "%$search%";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':limit', $pageSize, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 게시글 총 개수 조회
     *
     * @param string|null $search 검색어
     * @return int 게시글 총 개수
     */
    public function getTotalCount($search = null) {
        // 캐시 키 생성
        $cacheKey = CacheHelper::getPostCountCacheKey($search);
        
        // 캐시에서 조회 시도
        return CacheHelper::remember($cacheKey, function() use ($search) {
            $params = [];
            
            // 성능 최적화: 인덱스 활용 및 조건 최적화
            $sql = "SELECT COUNT(*) FROM posts 
                    FORCE INDEX (idx_posts_list_performance) 
                    WHERE status = 'published'";
            
            if ($search) {
                $sql .= " AND (
                    title LIKE :search_title 
                    OR LEFT(content, 500) LIKE :search_content
                )";
                $params[':search_title'] = "%$search%";
                $params[':search_content'] = "%$search%";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
        }, 600); // 10분 캐시
    }
    
    /**
     * 게시글 상세 조회
     *
     * @param int $id 게시글 ID
     * @return array|false 게시글 정보 또는 false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.nickname as author_name, u.profile_image
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE p.id = :id
        ");
        
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     * 게시글 생성
     *
     * @param array $data 게시글 데이터
     * @return int 생성된 게시글 ID
     */
    public function create($data) {
        // image_path 컬럼 포함하여 INSERT
        $stmt = $this->db->prepare("
            INSERT INTO posts (user_id, title, content, image_path, created_at, updated_at)
            VALUES (:user_id, :title, :content, :image_path, NOW(), NOW())
        ");
        
        $stmt->execute([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null
        ]);
        
        // 새 게시글 추가 시 관련 캐시 무효화
        $this->clearListCaches();
        
        return $this->db->lastInsertId();
    }
    
    /**
     * 게시글 수정
     *
     * @param int $id 게시글 ID
     * @param array $data 수정할 데이터
     * @return bool 성공 여부
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE posts 
            SET title = :title, content = :content, image_path = :image_path, updated_at = NOW()
            WHERE id = :id
        ");
        
        $result = $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null
        ]);
        
        // 게시글 수정 시 관련 캐시 무효화
        if ($result) {
            $this->clearListCaches();
        }
        
        return $result;
    }
    
    /**
     * 게시글 삭제
     *
     * @param int $id 게시글 ID
     * @return bool 성공 여부
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);
        
        // 게시글 삭제 시 관련 캐시 무효화
        if ($result) {
            $this->clearListCaches();
        }
        
        return $result;
    }
    
    /**
     * 게시글 목록 관련 캐시 무효화
     */
    private function clearListCaches() {
        // 게시글 목록과 카운트 캐시를 모두 삭제
        // 패턴 매칭으로 관련 캐시를 모두 찾아 삭제
        $cacheDir = '/tmp/topmkt_cache';
        if (is_dir($cacheDir)) {
            $files = glob($cacheDir . '/*.cache');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                if ($content && strpos($content, 'posts_list_') !== false || strpos($content, 'posts_count_') !== false) {
                    unlink($file);
                }
            }
        }
    }
    
    /**
     * 사용자 게시글 목록 조회
     *
     * @param int $userId 사용자 ID
     * @param int $limit 조회할 개수
     * @return array 게시글 목록
     */
    public function getByUserId($userId, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT * FROM posts
            WHERE user_id = :user_id
            ORDER BY created_at DESC
            LIMIT :limit
        ");
        
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
} 