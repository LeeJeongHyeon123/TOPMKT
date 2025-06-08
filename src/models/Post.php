<?php
/**
 * 게시글 모델 클래스
 */

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
        $offset = ($page - 1) * $pageSize;
        $params = [];
        
        $sql = "
            SELECT p.*, u.nickname as author_name
            FROM posts p
            JOIN users u ON p.user_id = u.id
            WHERE 1=1
        ";
        
        if ($search) {
            $sql .= " AND (p.title LIKE :search OR p.content LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $sql .= " ORDER BY p.created_at DESC LIMIT :offset, :limit";
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $pageSize, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * 게시글 총 개수 조회
     *
     * @param string|null $search 검색어
     * @return int 게시글 총 개수
     */
    public function getTotalCount($search = null) {
        $sql = "SELECT COUNT(*) FROM posts WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (title LIKE :search OR content LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchColumn();
    }
    
    /**
     * 게시글 상세 조회
     *
     * @param int $id 게시글 ID
     * @return array|false 게시글 정보 또는 false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, u.nickname as author_name
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
        
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'content' => $data['content'],
            'image_path' => $data['image_path'] ?? null
        ]);
    }
    
    /**
     * 게시글 삭제
     *
     * @param int $id 게시글 ID
     * @return bool 성공 여부
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
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