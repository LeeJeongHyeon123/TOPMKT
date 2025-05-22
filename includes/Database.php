<?php
/**
 * 데이터베이스 연결 및 쿼리 처리 클래스
 */
class Database {
    private static $instance = null;
    private $conn;
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_charset;
    
    private function __construct() {
        // config/database.php에서 DB 연결 정보 불러오기
        $config = require __DIR__ . '/../config/database.php';
        $this->db_host = $config['db_host'];
        $this->db_name = $config['db_name'];
        $this->db_user = $config['db_user'];
        $this->db_pass = $config['db_pass'];
        $this->db_charset = $config['db_charset'] ?? 'utf8mb4';
        try {
            $this->conn = new PDO(
                "mysql:host={$this->db_host};dbname={$this->db_name};charset={$this->db_charset}",
                $this->db_user,
                $this->db_pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            // 연결 성공 로그
            error_log("[Database] 연결 성공: {$this->db_host}/{$this->db_name}");
        } catch (PDOException $e) {
            error_log("[Database] 연결 실패: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            error_log("[Database] 연결 정보: {$this->db_host}/{$this->db_name}/{$this->db_user}");
            throw new Exception("데이터베이스 연결에 실패했습니다. 관리자에게 문의하세요.");
        }
    }
    
    /**
     * 싱글톤 인스턴스 반환
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO 연결 객체 반환
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * 쿼리 실행
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("[Database] 쿼리 실행 실패: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            error_log("[Database] SQL: " . $sql);
            error_log("[Database] Params: " . print_r($params, true));
            throw new Exception("데이터베이스 쿼리 실행에 실패했습니다. 관리자에게 문의하세요.");
        }
    }
    
    /**
     * 단일 행 조회
     */
    public function fetch($sql, $params = []) {
        try {
            return $this->query($sql, $params)->fetch();
        } catch (Exception $e) {
            error_log("[Database] fetch 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 여러 행 조회
     */
    public function fetchAll($sql, $params = []) {
        try {
            return $this->query($sql, $params)->fetchAll();
        } catch (Exception $e) {
            error_log("[Database] fetchAll 실패: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * 데이터 삽입
     */
    public function insert($table, $data) {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));
            
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
            $this->query($sql, array_values($data));
            
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            error_log("[Database] insert 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 데이터 수정
     */
    public function update($table, $data, $where, $whereParams = []) {
        try {
            $set = implode(' = ?, ', array_keys($data)) . ' = ?';
            $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
            
            $params = array_merge(array_values($data), $whereParams);
            return $this->query($sql, $params)->rowCount();
        } catch (Exception $e) {
            error_log("[Database] update 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 데이터 삭제
     */
    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM {$table} WHERE {$where}";
            return $this->query($sql, $params)->rowCount();
        } catch (Exception $e) {
            error_log("[Database] delete 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 트랜잭션 시작
     */
    public function beginTransaction() {
        try {
            return $this->conn->beginTransaction();
        } catch (Exception $e) {
            error_log("[Database] 트랜잭션 시작 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 트랜잭션 커밋
     */
    public function commit() {
        try {
            return $this->conn->commit();
        } catch (Exception $e) {
            error_log("[Database] 커밋 실패: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * 트랜잭션 롤백
     */
    public function rollBack() {
        try {
            return $this->conn->rollBack();
        } catch (Exception $e) {
            error_log("[Database] 롤백 실패: " . $e->getMessage());
            return false;
        }
    }
} 