<?php
/**
 * 사용자 모델 클래스
 */
namespace App\Models;

class User {
    private $db;
    
    /**
     * 생성자
     */
    public function __construct() {
        $this->db = getDbConnection();
    }
    
    /**
     * 이메일로 사용자 찾기
     *
     * @param string $email 이메일
     * @return array|false 사용자 정보 또는 false
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * ID로 사용자 찾기
     *
     * @param int $id 사용자 ID
     * @return array|false 사용자 정보 또는 false
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * 새 사용자 생성
     *
     * @param array $data 사용자 데이터
     * @return int 생성된 사용자 ID
     */
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, password_hash, name, role, created_at)
            VALUES (:email, :password_hash, :name, :role, NOW())
        ");
        
        $stmt->execute([
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
            'name' => $data['name'],
            'role' => $data['role'] ?? 'GENERAL'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * 사용자 정보 업데이트
     *
     * @param int $id 사용자 ID
     * @param array $data 업데이트할 데이터
     * @return bool 성공 여부
     */
    public function update($id, $data) {
        $fields = [];
        $params = ['id' => $id];
        
        // 업데이트할 필드 동적 생성
        foreach ($data as $key => $value) {
            if (in_array($key, ['name', 'password_hash'])) {
                $fields[] = "$key = :$key";
                $params[$key] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            UPDATE users 
            SET " . implode(', ', $fields) . ", updated_at = NOW()
            WHERE user_id = :id
        ");
        
        return $stmt->execute($params);
    }
    
    /**
     * 사용자 삭제
     *
     * @param int $id 사용자 ID
     * @return bool 성공 여부
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * 로그인 시간 업데이트
     *
     * @param int $id 사용자 ID
     * @return bool 성공 여부
     */
    public function updateLoginTime($id) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET last_login = NOW()
            WHERE user_id = :id
        ");
        
        return $stmt->execute(['id' => $id]);
    }
} 