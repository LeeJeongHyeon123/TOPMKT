<?php
/**
 * 행사 일정 컨트롤러
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../helpers/ResponseHelper.php';

class EventController {
    
    /**
     * 행사 일정 목록 페이지
     */
    public function index() {
        $pageTitle = '행사 일정 - ' . (APP_NAME ?? '탑마케팅');
        $pageDescription = '탑마케팅에서 주최하는 다양한 행사 일정을 확인하세요';
        $pageSection = 'events';
        
        // 현재는 준비 중 페이지로 표시
        $this->renderView('events/index', [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageSection' => $pageSection
        ]);
    }
    
    /**
     * 행사 상세 페이지
     */
    public function show() {
        // 추후 구현
        $this->index();
    }
    
    /**
     * 행사 등록 페이지
     */
    public function showWrite() {
        // 로그인 확인
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = '로그인이 필요한 서비스입니다.';
            header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        
        // 관리자 또는 기업회원만 접근 가능
        if (!in_array($_SESSION['user_role'] ?? '', ['ADMIN', 'SUPER_ADMIN', 'CORP'])) {
            $_SESSION['error'] = '행사 등록은 관리자 또는 기업회원만 가능합니다.';
            header('Location: /events');
            exit;
        }
        
        $this->index();
    }
    
    /**
     * 행사 등록 처리
     */
    public function create() {
        // 추후 구현
        ResponseHelper::json(['message' => '행사 일정 기능은 준비 중입니다.'], 503);
    }
    
    /**
     * 행사 수정 페이지
     */
    public function showEdit() {
        // 추후 구현
        $this->index();
    }
    
    /**
     * 행사 수정 처리
     */
    public function update() {
        // 추후 구현
        ResponseHelper::json(['message' => '행사 일정 기능은 준비 중입니다.'], 503);
    }
    
    /**
     * 행사 삭제 처리
     */
    public function delete() {
        // 추후 구현
        ResponseHelper::json(['message' => '행사 일정 기능은 준비 중입니다.'], 503);
    }
    
    /**
     * 뷰 렌더링 헬퍼
     */
    private function renderView($view, $data = []) {
        extract($data);
        
        // 헤더 데이터 준비
        $headerData = [
            'title' => $pageTitle ?? '행사 일정 - 탑마케팅',
            'description' => $pageDescription ?? '탑마케팅 행사 일정',
            'pageSection' => $pageSection ?? 'events'
        ];
        
        // 뷰 파일 경로
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        // 헤더 포함
        extract($headerData);
        include __DIR__ . '/../views/templates/header.php';
        
        // 본문 내용
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            // 뷰 파일이 없으면 준비 중 페이지 표시
            $this->showComingSoon();
        }
        
        // 푸터 포함
        include __DIR__ . '/../views/templates/footer.php';
    }
    
    /**
     * 준비 중 페이지 표시
     */
    private function showComingSoon() {
        ?>
        <div class="coming-soon-container">
            <div class="coming-soon-content">
                <div class="coming-soon-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h1 class="coming-soon-title">행사 일정</h1>
                <p class="coming-soon-subtitle">곧 만나실 수 있습니다!</p>
                <p class="coming-soon-description">
                    탑마케팅의 다양한 네트워킹 행사와 이벤트를 준비하고 있습니다.<br>
                    의미 있는 만남과 성장의 기회가 될 행사들로 찾아뵙겠습니다.
                </p>
                <div class="coming-soon-features">
                    <div class="feature-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>행사 장소 안내</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-ticket-alt"></i>
                        <span>온라인 신청</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-share-alt"></i>
                        <span>행사 공유</span>
                    </div>
                </div>
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i> 홈으로 돌아가기
                </a>
            </div>
        </div>
        
        <style>
        .coming-soon-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .coming-soon-content {
            text-align: center;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.95);
            padding: 60px 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .coming-soon-icon {
            font-size: 80px;
            color: #f5576c;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .coming-soon-title {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .coming-soon-subtitle {
            font-size: 24px;
            color: #f5576c;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .coming-soon-description {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.8;
            margin-bottom: 40px;
        }
        
        .coming-soon-features {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }
        
        .feature-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        
        .feature-item i {
            font-size: 40px;
            color: #f093fb;
        }
        
        .feature-item span {
            font-size: 14px;
            color: #4b5563;
        }
        
        @media (max-width: 768px) {
            .coming-soon-content {
                padding: 40px 20px;
            }
            
            .coming-soon-title {
                font-size: 28px;
            }
            
            .coming-soon-subtitle {
                font-size: 20px;
            }
            
            .coming-soon-features {
                gap: 20px;
            }
        }
        </style>
        <?php
    }
}