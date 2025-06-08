<?php
/**
 * 탑마케팅 데이터베이스 설정 파일
 */

/**
 * 데이터베이스 연결 설정
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // 데이터베이스 설정
    private const HOST = 'localhost';
    private const DB_NAME = 'topmkt';
    private const USERNAME = 'root';
    private const PASSWORD = 'Dnlszkem1!';
    private const CHARSET = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=' . self::CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
            
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new Exception('데이터베이스 연결에 실패했습니다.');
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
        return $this->connection;
    }
    
    /**
     * 쿼리 실행 (SELECT)
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Query failed: ' . $e->getMessage() . ' | SQL: ' . $sql);
            throw new Exception('데이터베이스 쿼리 실행에 실패했습니다.');
        }
    }
    
    /**
     * 단일 행 조회
     */
    public function fetch($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * 다중 행 조회
     */
    public function fetchAll($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * INSERT/UPDATE/DELETE 실행
     */
    public function execute($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * 마지막 삽입 ID 반환
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * 트랜잭션 시작
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * 트랜잭션 커밋
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * 트랜잭션 롤백
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * 연결 해제 방지
     */
    private function __clone() {}
    
    /**
     * prepare 메서드 추가 (PDO 메서드 위임)
     */
    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }
    
    /**
     * 직렬화 방지
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
} 