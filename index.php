<?php
// 에러 리포팅 설정
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/httpd/topmkt_error.log');

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 설정 파일 로드
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/functions.php';

// 현재 로그인 상태 확인
$isLoggedIn = isset($_SESSION['user_id']);

// 다국어 처리
$currentLang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko';
require_once __DIR__ . "/resources/lang/{$currentLang}/messages.php";

// 데이터 가져오기
$recommendedLeaders = getDummyRecommendedLeaders();
$latestVisionPosts = getDummyLatestVisionPosts();
$popularCommunityPosts = getDummyPopularCommunityPosts();
$upcomingEvents = getDummyUpcomingEvents();
$upcomingLectures = getDummyUpcomingLectures();
$knowhowPosts = getDummyKnowhowPosts();
$recruitingPosts = getDummyRecruitingPosts();
$noticePosts = getDummyNoticePosts();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('index.title') ?> - 탑마케팅</title>
    
    <!-- 파비콘 -->
    <link rel="icon" type="image/x-icon" href="/public/assets/images/favicon.ico">
    
    <!-- 웹 폰트 로드 (서버에 저장된 폰트 사용) -->
    <!-- <link rel="stylesheet" href="/public/assets/fonts/noto-sans-kr.css"> -->
    
    <!-- CSS 파일 로드 -->
    <link rel="stylesheet" href="/public/assets/css/main.css">
    <link rel="stylesheet" href="/public/assets/css/loading-overlay.css">
    
    <!-- reCAPTCHA Enterprise -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LfCdjErAAAAAL6YKLyHV_bt9of-8FNLCoOhW9C4"></script>
    
    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-firestore-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.6.0/firebase-storage-compat.js"></script>
    
    <!-- 공통 스크립트 -->
    <script src="/public/assets/js/loading-overlay.js" defer></script>
    <script src="/public/assets/js/main.js" defer></script>
</head>
<body>
    <!-- 로딩 오버레이 -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
        <div class="loading-text">로딩 중...</div>
    </div>

<?php
// 헤더 포함
include_once __DIR__ . '/includes/header.php';
?>

<main>
    <!-- 히어로 섹션 -->
    <section class="hero">
        <div class="hero-container">
            <h1 class="hero-title">전 세계 <span class="highlight">네트워크 마케팅</span> 리더들이 모이는 No.1 커뮤니티</h1>
            <p class="hero-subtitle">회사 비전 공유부터 팀 리크루팅까지, 이곳이 바로 시작입니다.</p>
            <div class="hero-features">
                <div class="feature">
                    <span class="feature-icon">🌍</span>
                    <span class="feature-text">글로벌 네트워크</span>
                </div>
                <div class="feature">
                    <span class="feature-icon">💡</span>
                    <span class="feature-text">비전 공유</span>
                </div>
                <div class="feature">
                    <span class="feature-icon">🤝</span>
                    <span class="feature-text">팀 리크루팅</span>
                </div>
            </div>
            <div class="hero-description">
                <p>탑마케팅은 전 세계 <span class="highlight">네트워크 마케팅</span> 리더들이 모여 정보를 공유하고 성장하는 플랫폼입니다.</p>
                <p>회사의 비전을 공유하고, 팀을 구성하며, 글로벌 네트워크를 확장하세요.</p>
            </div>
        </div>
    </section>

    <!-- 추천 리더 섹션 -->
    <section id="leaders" class="section-leaders">
        <div class="section-container">
            <h2 class="section-title">추천 리더</h2>
            <div class="leaders-grid">
                <?php foreach ($recommendedLeaders as $leader): ?>
                    <div class="leader-card">
                        <div class="leader-image">
                            <img src="<?php echo htmlspecialchars($leader['profile_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($leader['nickname']); ?>"
                                 class="profile-image-modal-trigger"
                                 data-image="<?php echo htmlspecialchars($leader['profile_image']); ?>"
                                 data-name="<?php echo htmlspecialchars($leader['nickname']); ?>">
                        </div>
                        <div class="leader-info">
                            <h3 class="leader-name"><?php echo htmlspecialchars($leader['nickname']); ?></h3>
                            <p class="leader-company"><?php echo htmlspecialchars($leader['company']); ?></p>
                            <p class="leader-intro"><?php echo isset($leader['introduction']) ? htmlspecialchars($leader['introduction']) : ''; ?></p>
                            <div class="leader-actions">
                                <a href="/chat.php/<?php echo $leader['id']; ?>" class="btn-chat">채팅하기</a>
                                <a href="/profile.php/<?php echo $leader['id']; ?>" class="btn-content">프로필 보기</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 프로필 이미지 모달 -->
    <div id="profileImageModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <img id="modalImage" src="" alt="">
            <p id="modalName"></p>
        </div>
    </div>

    <!-- 회사/비전 소개 섹션 -->
    <section id="vision" class="section-vision">
        <div class="section-container">
            <h2 class="section-title">회사/비전 소개</h2>
            <div class="posts-grid">
                <?php foreach ($latestVisionPosts as $post): ?>
                    <article class="post-card">
                        <a href="/vision/view.php?id=<?php echo $post['id']; ?>" class="post-link">
                            <div class="post-image">
                                <img src="/public/assets/images/vision/<?php echo $post['id']; ?>.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </div>
                            <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                            <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="post-meta">
                                <div class="post-meta-left">
                                    <span class="post-author"><?php echo htmlspecialchars($post['author']); ?></span>
                                </div>
                                <div class="post-meta-right">
                                    <span class="post-views"><?php echo number_format($post['views']); ?></span>
                                    <span class="post-likes"><?php echo number_format($post['likes']); ?></span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 노하우 공유 섹션 -->
    <section id="knowhow" class="section-knowhow">
        <div class="section-container">
            <h2 class="section-title">노하우 공유</h2>
            <div class="posts-grid">
                <?php foreach ($knowhowPosts as $post): ?>
                    <article class="post-card">
                        <a href="/knowhow/view.php?id=<?php echo $post['id']; ?>" class="post-link">
                            <div class="post-image">
                                <img src="/public/assets/images/knowhow/<?php echo $post['id']; ?>.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </div>
                            <h3 class="post-title">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="post-meta">
                                <div class="post-meta-left">
                                    <span class="post-author"><?php echo htmlspecialchars($post['author']); ?></span>
                                </div>
                                <div class="post-meta-right">
                                    <span class="post-views"><?php echo number_format($post['views']); ?></span>
                                    <span class="post-likes"><?php echo number_format($post['likes']); ?></span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 팀 리쿠르팅 모집 섹션 -->
    <section id="recruiting" class="section-recruiting">
        <div class="section-container">
            <h2 class="section-title">팀 리쿠르팅 모집</h2>
            <div class="posts-grid">
                <?php foreach ($recruitingPosts as $post): ?>
                    <article class="post-card">
                        <a href="/recruiting/view.php?id=<?php echo $post['id']; ?>" class="post-link">
                            <div class="post-image">
                                <img src="/public/assets/images/recruiting/<?php echo $post['id']; ?>.jpg" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </div>
                            <h3 class="post-title">
                                <?php echo htmlspecialchars($post['title']); ?>
                                <span class="position-badge"><?php echo htmlspecialchars($post['position']); ?></span>
                            </h3>
                            <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="post-meta">
                                <div class="post-meta-row">
                                    <span class="post-company"><?php echo htmlspecialchars($post['company']); ?></span>
                                </div>
                                <div class="post-meta-row">
                                    <span class="post-location"><?php echo htmlspecialchars($post['location']); ?></span>
                                </div>
                                <div class="post-meta-row">
                                    <span class="post-views"><?php echo number_format($post['views']); ?></span>
                                    <span class="post-likes"><?php echo number_format($post['likes']); ?></span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 행사 일정 섹션 -->
    <section id="events" class="section-events">
        <div class="section-container">
            <h2 class="section-title">행사 일정</h2>
            <div class="events-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <img src="/public/assets/images/events/<?php echo $event['id']; ?>.jpg" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        </div>
                        <div class="event-content">
                            <div class="event-date">
                                <span class="year"><?php echo date('Y', strtotime($event['date'])); ?>년</span>
                                <span class="month"><?php echo date('m', strtotime($event['date'])); ?>월</span>
                                <span class="day"><?php echo date('d', strtotime($event['date'])); ?>일</span>
                            </div>
                            <div class="event-info">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                <p class="event-time"><?php echo htmlspecialchars($event['time']); ?></p>
                                <p class="event-location"><?php echo htmlspecialchars($event['location']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 강의 일정 섹션 -->
    <section id="lectures" class="section-lectures">
        <div class="section-container">
            <h2 class="section-title">강의 일정</h2>
            <div class="events-grid">
                <?php foreach ($upcomingLectures as $lecture): ?>
                    <div class="event-card">
                        <div class="event-image">
                            <img src="/public/assets/images/lectures/<?php echo $lecture['id']; ?>.jpg" alt="<?php echo htmlspecialchars($lecture['title']); ?>">
                        </div>
                        <div class="event-content">
                            <div class="event-date">
                                <span class="year"><?php echo date('Y', strtotime($lecture['date'])); ?>년</span>
                                <span class="month"><?php echo date('m', strtotime($lecture['date'])); ?>월</span>
                                <span class="day"><?php echo date('d', strtotime($lecture['date'])); ?>일</span>
                            </div>
                            <div class="event-info">
                                <h3 class="event-title"><?php echo htmlspecialchars($lecture['title']); ?></h3>
                                <p class="event-time"><?php echo htmlspecialchars($lecture['time']); ?></p>
                                <p class="event-location"><?php echo htmlspecialchars($lecture['location']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 자유 커뮤니티 섹션 -->
    <section id="community" class="section-community">
        <div class="section-container">
            <h2 class="section-title">자유 커뮤니티</h2>
            <div class="posts-grid">
                <?php foreach ($popularCommunityPosts as $post): ?>
                    <article class="post-card">
                        <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        <div class="post-meta">
                            <span class="post-views">조회 <?php echo number_format($post['views']); ?></span>
                            <span class="post-likes">좋아요 <?php echo number_format($post['likes']); ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 공지사항 섹션 -->
    <section id="notice" class="section-notice">
        <div class="section-container">
            <h2 class="section-title">공지사항</h2>
            <div class="posts-grid">
                <?php foreach ($noticePosts as $post): ?>
                    <article class="post-card <?php echo $post['is_important'] ? 'important' : ''; ?>">
                        <a href="/notice/view.php?id=<?php echo $post['id']; ?>" class="post-link">
                            <h3 class="post-title">
                                <?php if ($post['is_important']): ?>
                                    <span class="notice-badge">중요</span>
                                <?php endif; ?>
                                <?php echo htmlspecialchars($post['title']); ?>
                            </h3>
                            <p class="post-excerpt"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                            <div class="post-meta">
                                <div class="post-meta-left">
                                    <span class="post-date"><?php echo formatDate($post['created_at']); ?></span>
                                </div>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php
// 푸터 포함
include_once __DIR__ . '/includes/footer.php';
?>
</body>
</html> 