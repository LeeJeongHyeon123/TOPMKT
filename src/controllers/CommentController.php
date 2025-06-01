<?php
/**
 * 댓글 관련 컨트롤러
 */
namespace App\Controllers;

class CommentController {
    /**
     * 댓글 작성 처리
     */
    public function create() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 게시글 ID 추출
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        preg_match('/\/posts\/(\d+)\/comments/', $uri, $matches);
        $post_id = $matches[1] ?? null;
        
        if (!$post_id) {
            header('HTTP/1.1 404 Not Found');
            return;
        }
        
        // POST 요청 확인
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            return;
        }
        
        // 입력 데이터 검증
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING);
        
        if (empty($content)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => '댓글 내용을 입력해주세요.']);
            return;
        }
        
        // 댓글 저장 (실제 구현에서는 DB에 저장)
        // ...
        
        // 응답 처리
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'message' => '댓글이 작성되었습니다.',
                'comment_id' => 3 // 실제 구현에서는 DB에서 생성된 ID 반환
            ]);
        } else {
            header('Location: /posts/' . $post_id);
        }
    }
    
    /**
     * 댓글 수정 처리
     */
    public function update() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 댓글 ID 추출
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        preg_match('/\/comments\/(\d+)/', $uri, $matches);
        $comment_id = $matches[1] ?? null;
        
        if (!$comment_id) {
            header('HTTP/1.1 404 Not Found');
            return;
        }
        
        // 댓글 작성자 확인 (실제 구현에서는 DB에서 확인)
        // 임시로 항상 본인 댓글로 가정
        
        // PUT 요청 데이터 파싱
        parse_str(file_get_contents("php://input"), $putData);
        
        // 입력 데이터 검증
        $content = filter_var($putData['content'] ?? '', FILTER_SANITIZE_STRING);
        
        if (empty($content)) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => '댓글 내용을 입력해주세요.']);
            return;
        }
        
        // 댓글 업데이트 (실제 구현에서는 DB 업데이트)
        // ...
        
        // 응답 처리
        header('Content-Type: application/json');
        echo json_encode(['message' => '댓글이 수정되었습니다.']);
    }
    
    /**
     * 댓글 삭제 처리
     */
    public function delete() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            return;
        }
        
        // URL에서 댓글 ID 추출
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        preg_match('/\/comments\/(\d+)/', $uri, $matches);
        $comment_id = $matches[1] ?? null;
        
        if (!$comment_id) {
            header('HTTP/1.1 404 Not Found');
            return;
        }
        
        // 댓글 작성자 확인 (실제 구현에서는 DB에서 확인)
        // 임시로 항상 본인 댓글로 가정
        
        // 댓글 삭제 (실제 구현에서는 DB에서 삭제)
        // ...
        
        // 응답 처리
        header('HTTP/1.1 204 No Content');
    }
} 