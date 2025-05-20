<?php
session_start();
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count 
        FROM messages 
        WHERE receiver_id = ? AND is_read = 0
    ");
    
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode(['count' => (int)$result['unread_count']]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    echo json_encode(['count' => 0]);
} 