<?php
namespace App\Controllers;

/**
 * 홈 컨트롤러
 * 
 * 메인 페이지와 관련된 컨트롤러
 */
class HomeController {
    /**
     * 메인 페이지를 표시합니다.
     *
     * @return void
     */
    public function index() {
        // 여기에서 필요한 데이터를 가져와 뷰에 전달
        $title = '탑마케팅(TOPMKT) - 홈';
        $data = [
            'title' => $title,
            'content' => '탑마케팅에 오신 것을 환영합니다.'
        ];
        
        // 뷰 불러오기
        require_once APP_ROOT . '/app/Views/layouts/header.php';
        require_once APP_ROOT . '/app/Views/pages/home.php';
        require_once APP_ROOT . '/app/Views/layouts/footer.php';
    }
} 