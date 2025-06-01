<?php
/**
 * 댓글 모델 클래스
 */
namespace App\Models;

class Comment {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * 게시글의 댓글 목록 조회
     *
     * @param int $postId 게시글 ID
     * @return array 댓글 목록
     */
    public function getByPostId($postId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as author_name 
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.post_id = :post_id
            ORDER BY c.created_at ASC
        ");
        
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchAll();
    }
    
    /**
     * 댓글 상세 조회
     *
     * @param int $id 댓글 ID
     * @return array|false 댓글 정보 또는 false
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.name as author_name 
            FROM comments c
            JOIN users u ON c.user_id = u.user_id
            WHERE c.comment_id = :id
        ");
        
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * 댓글 생성
     *
     * @param array $data 댓글 데이터
     * @return int 생성된 댓글 ID
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (post_id, user_id, parent_comment_id, content, created_at)
            VALUES (:post_id, :user_id, :parent_comment_id, :content, NOW())
        ");
        
        $stmt->execute([
            'post_id' => $data['post_id'],
            'user_id' => $data['user_id'],
            'parent_comment_id' => $data['parent_comment_id'] ?? null,
            'content' => $data['content']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * 댓글 수정
     *
     * @param int $id 댓글 ID
     * @param array $data 수정할 데이터
     * @return bool 성공 여부
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE comments 
            SET content = :content
            WHERE comment_id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'content' => $data['content']
        ]);
    }
    
    /**
     * 댓글 삭제
     *
     * @param int $id 댓글 ID
     * @return bool 성공 여부
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE comment_id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * 게시글별 댓글 수 조회
     *
     * @param int $postId 게시글 ID
     * @return int 댓글 수
     */
    public function getCountByPostId($postId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM comments
            WHERE post_id = :post_id
        ");
        
        $stmt->execute(['post_id' => $postId]);
        return $stmt->fetchColumn();
    }
} 