<?php
/**
 * 행사 상세 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();
?>

<style>
/* 행사 상세 페이지 스타일 (파란색 테마) */
.event-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 15px;
    min-height: calc(100vh - 200px);
    padding-top: 60px;
}

.event-hero {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    padding: 60px 30px;
    border-radius: 16px;
    margin-bottom: 40px;
    margin-top: 0px;
    position: relative;
    overflow: hidden;
}

.event-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"/><circle cx="80" cy="80" r="3" fill="white" opacity="0.1"/><circle cx="40" cy="60" r="1" fill="white" opacity="0.1"/></svg>');
    pointer-events: none;
}

.event-hero-content {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
    text-align: center;
}

.event-category {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    margin-bottom: 20px;
    backdrop-filter: blur(10px);
}

.event-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.2;
}

.event-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 30px;
}

.event-meta-row {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.event-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.event-meta-item i {
    font-size: 1.1rem;
    opacity: 0.8;
}

.event-scale-badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    margin-left: 10px;
}

.event-scale-badge.large { background: rgba(255, 107, 107, 0.3); color: white; }
.event-scale-badge.medium { background: rgba(255, 167, 38, 0.3); color: white; }
.event-scale-badge.small { background: rgba(102, 187, 106, 0.3); color: white; }

.event-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
    margin-bottom: 40px;
}

.event-main {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.event-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.content-section {
    padding: 30px;
}

.content-section h2 {
    color: #1e293b;
    font-size: 1.5rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e2e8f0;
}

.event-description {
    color: #64748b;
    line-height: 1.7;
    font-size: 1rem;
}

.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 25px;
}

.info-card h3 {
    color: #1e293b;
    font-size: 1.2rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-card h3 i {
    color: #4A90E2;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    padding: 10px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-list li:last-child {
    border-bottom: none;
}

.info-label {
    color: #64748b;
    font-size: 0.9rem;
}

.info-value {
    color: #1e293b;
    font-weight: 500;
    text-align: right;
}

.register-card {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    text-align: center;
}

.register-card h3 {
    color: white;
    margin-bottom: 15px;
}

.event-fee {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 20px;
}

.register-btn {
    background: white;
    color: #4A90E2;
    border: none;
    padding: 15px 30px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
    font-size: 1.1rem;
}

.register-btn:hover {
    background: #f8fafc;
    transform: translateY(-2px);
}

.register-btn:disabled {
    background: #cbd5e0;
    color: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

.instructor-card {
    text-align: center;
}

.instructor-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    font-size: 2rem;
    color: white;
}

.instructor-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 10px;
}

.instructor-bio {
    color: #64748b;
    font-size: 0.9rem;
    line-height: 1.5;
}

.instructor-avatar-image {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 15px;
    overflow: hidden;
    position: relative;
}

.instructor-avatar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.instructor-avatar-fallback {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: white;
    position: absolute;
    top: 0;
    left: 0;
}

/* 이미지 갤러리 스타일 */
.event-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.gallery-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    aspect-ratio: 16/9;
    background: #f8fafc;
}

.gallery-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(74, 144, 226, 0.15);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.gallery-item:hover img {
    transform: scale(1.05);
}

.gallery-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(74, 144, 226, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    color: white;
    font-weight: 600;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

/* YouTube 동영상 스타일 */
.youtube-container {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 비율 */
    height: 0;
    overflow: hidden;
    border-radius: 12px;
    background: #000;
}

.youtube-container iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 12px;
}

/* 이미지 모달 스타일 */
.image-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
}

.modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    margin: auto;
    top: 50%;
    transform: translateY(-50%);
    text-align: center;
}

.modal-content img {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 25px;
    color: white;
    font-size: 35px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10000;
}

.modal-close:hover {
    color: #4A90E2;
}

/* 심플한 모달 네비게이션 */
.modal-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    transition: all 0.2s ease;
    z-index: 10001;
}

.modal-nav:hover {
    background: rgba(255, 255, 255, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transform: translateY(-50%) scale(1.05);
}

.modal-nav:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.95);
}

.modal-nav:active {
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(-50%) scale(0.98);
}

.modal-prev {
    left: 20px;
}

.modal-next {
    right: 20px;
}

.modal-nav svg {
    width: 20px;
    height: 20px;
    fill: #666;
    transition: fill 0.2s ease;
}

.modal-nav:hover svg {
    fill: #333;
}

.modal-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    background: rgba(0, 0, 0, 0.5);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
}

.networking-notice {
    background: #e0f2fe;
    border: 1px solid #81d4fa;
    border-radius: 8px;
    padding: 15px;
    margin-top: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.networking-notice i {
    color: #0277bd;
    font-size: 1.2rem;
}

.networking-notice span {
    color: #01579b;
    font-weight: 500;
}

/* 행사 액션 버튼 */
.event-actions {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
}


/* 반응형 */
@media (max-width: 768px) {
    .event-detail-container {
        padding: 20px 10px;
        padding-top: 40px;
    }
    
    .event-hero {
        margin-top: 0px;
        padding: 40px 20px;
    }
    
    .event-title {
        font-size: 2rem;
    }
    
    .event-meta-row {
        gap: 15px;
    }
    
    .event-content {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .content-section {
        padding: 20px;
    }
    
    /* 모바일 모달 네비게이션 */
    .modal-nav {
        width: 44px;
        height: 44px;
    }
    
    .modal-prev {
        left: 15px;
    }
    
    .modal-next {
        right: 15px;
    }
}
</style>

<div class="event-detail-container">
    <!-- 행사 히어로 섹션 -->
    <div class="event-hero">
        <div class="event-hero-content">
            <div class="event-category">
                <?php
                $categoryNames = [
                    'seminar' => '세미나',
                    'workshop' => '워크샵', 
                    'conference' => '컨퍼런스',
                    'webinar' => '웨비나',
                    'training' => '교육'
                ];
                echo $categoryNames[$event['category']] ?? '행사';
                ?>
                <?php if ($event['event_scale']): ?>
                    <?php
                    $scaleNames = ['small' => '소규모', 'medium' => '중규모', 'large' => '대규모'];
                    ?>
                    <span class="event-scale-badge <?= $event['event_scale'] ?>">
                        <?= $scaleNames[$event['event_scale']] ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <h1 class="event-title">
                <?= htmlspecialchars($event['title']) ?>
                <?php if ($event['has_networking']): ?>
                    <i class="fas fa-users" title="네트워킹 포함" style="font-size: 0.8em; opacity: 0.8;"></i>
                <?php endif; ?>
            </h1>
            
            <p class="event-subtitle">
                <?= htmlspecialchars(mb_substr($event['description'], 0, 100)) ?>...
            </p>
            
            <div class="event-meta-row">
                <div class="event-meta-item">
                    <i class="fas fa-calendar"></i>
                    <span><?= date('Y년 n월 j일', strtotime($event['start_date'])) ?></span>
                </div>
                <div class="event-meta-item">
                    <i class="fas fa-clock"></i>
                    <span><?= date('H:i', strtotime($event['start_time'])) ?> - <?= date('H:i', strtotime($event['end_time'])) ?></span>
                </div>
                <div class="event-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>
                        <?php if ($event['location_type'] === 'online'): ?>
                            온라인
                        <?php elseif ($event['location_type'] === 'hybrid'): ?>
                            하이브리드
                        <?php else: ?>
                            <?= htmlspecialchars($event['venue_name'] ?? '오프라인') ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
            
            <div class="event-actions">
                <button class="btn btn-secondary btn-share" onclick="shareEventContent()">
                    🔗 공유하기
                </button>
            </div>
        </div>
    </div>

    <!-- 메인 콘텐츠 -->
    <div class="event-content">
        <!-- 메인 영역 -->
        <div class="event-main">
            <?php if (!empty($event['youtube_video'])): ?>
            <div class="content-section">
                <h2>🎬 관련 영상</h2>
                <div class="youtube-container">
                    <iframe 
                        src="<?= htmlspecialchars($event['youtube_video']) ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="content-section">
                <h2>행사 소개</h2>
                <div class="event-description">
                    <?= nl2br(htmlspecialchars($event['description'])) ?>
                </div>
                
                <?php if ($event['has_networking']): ?>
                <div class="networking-notice">
                    <i class="fas fa-users"></i>
                    <span>이 행사에는 네트워킹 시간이 포함되어 있습니다.</span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($event['images'])): ?>
            <div class="content-section">
                <h2>🖼️ 이미지</h2>
                <div class="event-gallery">
                    <?php foreach ($event['images'] as $index => $image): ?>
                        <div class="gallery-item" onclick="openImageModal(<?= $index ?>)">
                            <img src="<?= htmlspecialchars($image['url']) ?>" 
                                 alt="<?= htmlspecialchars($image['alt_text']) ?>"
                                 loading="lazy">
                            <div class="gallery-overlay">
                                <span>🔍 크게 보기</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($event['sponsor_info']): ?>
            <div class="content-section">
                <h2>후원 및 협력사</h2>
                <p class="event-description"><?= htmlspecialchars($event['sponsor_info']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- 사이드바 -->
        <div class="event-sidebar">
            <!-- 등록 정보 -->
            <div class="info-card register-card">
                <h3><i class="fas fa-ticket-alt"></i> 참가 신청</h3>
                <div class="event-fee">
                    <?php if ($event['registration_fee']): ?>
                        <?= number_format($event['registration_fee']) ?>원
                    <?php else: ?>
                        무료
                    <?php endif; ?>
                </div>
                <button class="register-btn" onclick="registerEvent()">
                    참가 신청하기
                </button>
            </div>

            <!-- 행사 정보 -->
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> 행사 정보</h3>
                <ul class="info-list">
                    <li>
                        <span class="info-label">일시</span>
                        <span class="info-value">
                            <?= date('n월 j일', strtotime($event['start_date'])) ?>
                            <?php if ($event['end_date'] && $event['end_date'] !== $event['start_date']): ?>
                                - <?= date('n월 j일', strtotime($event['end_date'])) ?>
                            <?php endif; ?>
                        </span>
                    </li>
                    <li>
                        <span class="info-label">시간</span>
                        <span class="info-value"><?= date('H:i', strtotime($event['start_time'])) ?> - <?= date('H:i', strtotime($event['end_time'])) ?></span>
                    </li>
                    <li>
                        <span class="info-label">장소</span>
                        <span class="info-value">
                            <?php if ($event['location_type'] === 'online'): ?>
                                온라인
                            <?php elseif ($event['location_type'] === 'hybrid'): ?>
                                하이브리드
                            <?php else: ?>
                                오프라인
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php if ($event['max_participants']): ?>
                    <li>
                        <span class="info-label">정원</span>
                        <span class="info-value"><?= number_format($event['max_participants']) ?>명</span>
                    </li>
                    <?php endif; ?>
                    <?php if ($event['dress_code']): ?>
                    <li>
                        <span class="info-label">드레스코드</span>
                        <span class="info-value">
                            <?php
                            $dressCodes = [
                                'casual' => '캐주얼',
                                'business_casual' => '비즈니스 캐주얼',
                                'business' => '비즈니스',
                                'formal' => '정장'
                            ];
                            echo $dressCodes[$event['dress_code']] ?? $event['dress_code'];
                            ?>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- 강사 정보 -->
            <div class="info-card instructor-card">
                <h3><i class="fas fa-user"></i> 진행자</h3>
                <?php if (!empty($event['instructor_image'])): ?>
                <div class="instructor-avatar-image">
                    <img src="<?= htmlspecialchars($event['instructor_image']) ?>" 
                         alt="<?= htmlspecialchars($event['instructor_name']) ?> 프로필" 
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="instructor-avatar-fallback" style="display: none;">
                        <?= mb_substr($event['instructor_name'], 0, 1) ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="instructor-avatar">
                    <?= mb_substr($event['instructor_name'], 0, 1) ?>
                </div>
                <?php endif; ?>
                <div class="instructor-name"><?= htmlspecialchars($event['instructor_name']) ?></div>
                <div class="instructor-bio"><?= htmlspecialchars($event['instructor_info']) ?></div>
            </div>

            <?php if ($event['venue_address'] || $event['parking_info']): ?>
            <!-- 장소 정보 -->
            <div class="info-card">
                <h3><i class="fas fa-map-marker-alt"></i> 장소 안내</h3>
                <ul class="info-list">
                    <?php if ($event['venue_name']): ?>
                    <li>
                        <span class="info-label">장소명</span>
                        <span class="info-value"><?= htmlspecialchars($event['venue_name']) ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($event['venue_address']): ?>
                    <li>
                        <span class="info-label">주소</span>
                        <span class="info-value"><?= htmlspecialchars($event['venue_address']) ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($event['parking_info']): ?>
                    <li>
                        <span class="info-label">주차</span>
                        <span class="info-value"><?= htmlspecialchars($event['parking_info']) ?></span>
                    </li>
                    <?php endif; ?>
                    <?php if ($event['online_link'] && in_array($event['location_type'], ['online', 'hybrid'])): ?>
                    <li>
                        <span class="info-label">온라인 링크</span>
                        <span class="info-value">
                            <a href="<?= htmlspecialchars($event['online_link']) ?>" target="_blank" style="color: #4A90E2;">
                                참가 링크
                            </a>
                        </span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- 행사장 지도 (오프라인/하이브리드 행사만 표시) -->
            <?php if (in_array($event['location_type'], ['offline', 'hybrid']) && $event['venue_address']): ?>
            <div class="info-card">
                <h3><i class="fas fa-map"></i> 오시는 길</h3>
                <div id="eventVenueMap" style="width: 100%; height: 300px; border-radius: 8px; margin-top: 15px;"></div>
                <div style="text-align: center; margin-top: 10px; color: #64748b; font-size: 0.9rem;">
                    지도를 드래그하여 위치를 확인하세요
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 이미지 모달 -->
<?php if (!empty($event['images'])): ?>
<div id="imageModal" class="image-modal">
    <span class="modal-close" onclick="closeImageModal()">&times;</span>
    <div class="modal-content">
        <img id="modalImage" src="" alt="">
        <button class="modal-nav modal-prev" onclick="prevImage()">
            <svg viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>
        <button class="modal-nav modal-next" onclick="nextImage()">
            <svg viewBox="0 0 24 24">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </button>
        <div class="modal-counter">
            <span id="imageCounter">1 / <?= count($event['images']) ?></span>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- 네이버 지도 API (행사장 위치) -->
<?php if (in_array($event['location_type'], ['offline', 'hybrid']) && $event['venue_address']): ?>
<?php
// 행사장 정보 설정
$venueName = !empty($event['venue_name']) ? $event['venue_name'] : '행사장';
$mapAddress = !empty($event['venue_address']) ? $event['venue_address'] : '';
$naverClientId = defined('NAVER_MAPS_CLIENT_ID') ? NAVER_MAPS_CLIENT_ID : 'c5yj6m062z';

// 장소별 기본 좌표 (강의 시스템과 동일한 방식)
$defaultCoords = [
    'lat' => 37.5665,  // 서울시청 기본
    'lng' => 126.9780
];

// 반도 아이비밸리 정확 좌표 사용 (실제 측정 좌표)
if (strpos($mapAddress, '반도 아이비밸리') !== false || strpos($mapAddress, '가산디지털1로 204') !== false) {
    $defaultCoords['lat'] = 37.4835033620443;
    $defaultCoords['lng'] = 126.881038151818;
} elseif (strpos($mapAddress, '가산') !== false || strpos($mapAddress, '금천구') !== false) {
    $defaultCoords['lat'] = 37.4816;
    $defaultCoords['lng'] = 126.8819;
} elseif (strpos($mapAddress, '강남') !== false || strpos($mapAddress, '테헤란로') !== false) {
    $defaultCoords['lat'] = 37.4979;
    $defaultCoords['lng'] = 127.0276;
} elseif (strpos($mapAddress, '홍대') !== false || strpos($mapAddress, '마포') !== false) {
    $defaultCoords['lat'] = 37.5563;
    $defaultCoords['lng'] = 126.9236;
}
?>

<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=<?= htmlspecialchars($naverClientId) ?>&callback=initEventVenueMap"></script>
<script>
// 네이버 지도 API 사용 가능 여부 확인
function checkNaverMapsAPI() {
    return typeof naver !== 'undefined' && 
           typeof naver.maps !== 'undefined' && 
           typeof naver.maps.Map !== 'undefined';
}

// 지도 대체 UI 표시 함수
function showEventMapFallback() {
    var mapContainer = document.getElementById('eventVenueMap');
    if (mapContainer) {
        mapContainer.innerHTML = 
            '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background: #f8fafc; color: #4a5568; border-radius: 8px; border: 1px solid #e2e8f0;">' +
            '<div style="font-size: 32px; margin-bottom: 15px; color: #4A90E2;">🏢</div>' +
            '<div style="font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #2d3748;"><?= addslashes($venueName) ?></div>' +
            '<div style="font-size: 13px; margin-bottom: 20px; text-align: center; padding: 0 20px; color: #4a5568;"><?= addslashes($mapAddress) ?></div>' +
            '<a href="https://map.naver.com/v5/search/<?= urlencode($mapAddress) ?>" target="_blank" ' +
            'style="background: #4A90E2; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">' +
            '📍 네이버 지도에서 보기</a>' +
            '</div>';
    }
}

// 행사장 지도 초기화 함수 (글로벌 함수로 정의)
window.initEventVenueMap = function() {
    try {
        // 네이버 지도 API 사용 가능 여부 확인
        if (!checkNaverMapsAPI()) {
            console.warn('🗺️ 네이버 지도 API를 사용할 수 없습니다.');
            showEventMapFallback();
            return;
        }
        
        console.log('🗺️ 행사장 지도 초기화 시작');
        
        // 지도 중심 좌표
        var center = new naver.maps.LatLng(<?= floatval($defaultCoords['lat']) ?>, <?= floatval($defaultCoords['lng']) ?>);
        
        // 지도 옵션
        var mapOptions = {
            center: center,
            zoom: 16,
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: naver.maps.MapTypeControlStyle.BUTTON,
                position: naver.maps.Position.TOP_RIGHT
            },
            zoomControl: true,
            zoomControlOptions: {
                style: naver.maps.ZoomControlStyle.SMALL,
                position: naver.maps.Position.RIGHT_CENTER
            }
        };
        
        // 지도 생성
        var map = new naver.maps.Map('eventVenueMap', mapOptions);
        
        // 행사장 마커 생성 (파란색 테마)
        var marker = new naver.maps.Marker({
            position: center,
            map: map,
            title: '<?= addslashes($venueName) ?>',
            icon: {
                content: '<div style="width: 20px; height: 20px; background: #4A90E2; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
                anchor: new naver.maps.Point(10, 10)
            }
        });
        
        // 정보창 생성
        var infoWindow = new naver.maps.InfoWindow({
            content: '<div style="' +
                'padding: 16px 20px; ' +
                'text-align: center; ' +
                'min-width: 220px; ' +
                'background: white; ' +
                'color: #2d3748; ' +
                'border-radius: 8px; ' +
                'box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); ' +
                'border: 1px solid #e2e8f0;' +
            '">' +
                '<div style="font-weight: bold; margin-bottom: 6px; font-size: 15px; color: #1a202c;">' +
                '🎉 <?= addslashes($venueName) ?>' +
                '</div>' +
                '<div style="font-size: 12px; color: #4a5568; line-height: 1.4;">' +
                '📍 <?= addslashes($mapAddress) ?>' +
                '</div>' +
            '</div>',
            maxWidth: 260,
            backgroundColor: "white",
            borderColor: "#e2e8f0",
            borderWidth: 1,
            anchorSize: new naver.maps.Size(10, 10),
            anchorSkew: true,
            anchorColor: "white"
        });
        
        // 마커 클릭 이벤트
        naver.maps.Event.addListener(marker, 'click', function() {
            try {
                if (infoWindow.getMap()) {
                    infoWindow.close();
                } else {
                    infoWindow.open(map, marker);
                }
            } catch (error) {
                console.error('🗺️ 정보창 토글 오류:', error);
            }
        });
        
        // 초기에 정보창 표시
        setTimeout(function() {
            try {
                infoWindow.open(map, marker);
            } catch (error) {
                console.error('🗺️ 초기 정보창 표시 오류:', error);
            }
        }, 500);
        
        console.log('🗺️ 행사장 지도 초기화 완료');
        
    } catch (error) {
        console.error('🗺️ 행사장 지도 초기화 오류:', error);
        showEventMapFallback();
    }
};

// API 로드 실패시 fallback
window.addEventListener('error', function(e) {
    if (e.filename && e.filename.includes('maps.js')) {
        console.warn('🗺️ 네이버 지도 API 로드 실패:', e.message);
        showEventMapFallback();
    }
});

// DOM 로드 후 지도 초기화 (callback 방식이므로 자동 호출됨)
document.addEventListener('DOMContentLoaded', function() {
    // API가 callback으로 자동 호출되므로 별도 초기화 불필요
    console.log('🗺️ DOM 로드 완료 - API callback 대기 중');
});
</script>
<?php endif; ?>

<script>
function registerEvent() {
    <?php if ($isLoggedIn): ?>
        alert('참가 신청 기능은 준비 중입니다.');
        // TODO: 실제 참가 신청 로직 구현
    <?php else: ?>
        if (confirm('로그인이 필요합니다. 로그인 페이지로 이동하시겠습니까?')) {
            window.location.href = '/auth/login?redirect=' + encodeURIComponent(window.location.pathname);
        }
    <?php endif; ?>
}

<?php if (!empty($event['images'])): ?>
// 이미지 갤러리 관련 변수
const eventImages = <?= json_encode($event['images']) ?>;
let currentImageIndex = 0;

// 이미지 모달 열기
function openImageModal(index) {
    currentImageIndex = index;
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    modalImage.src = eventImages[currentImageIndex].url;
    modalImage.alt = eventImages[currentImageIndex].alt_text;
    counter.textContent = `${currentImageIndex + 1} / ${eventImages.length}`;
    
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // 배경 스크롤 방지
}

// 이미지 모달 닫기
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto'; // 스크롤 복원
}

// 이전 이미지
function prevImage() {
    currentImageIndex = (currentImageIndex - 1 + eventImages.length) % eventImages.length;
    updateModalImage();
}

// 다음 이미지
function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % eventImages.length;
    updateModalImage();
}

// 모달 이미지 업데이트
function updateModalImage() {
    const modalImage = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    modalImage.src = eventImages[currentImageIndex].url;
    modalImage.alt = eventImages[currentImageIndex].alt_text;
    counter.textContent = `${currentImageIndex + 1} / ${eventImages.length}`;
}

// 키보드 이벤트
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal.style.display === 'block') {
        switch(e.key) {
            case 'Escape':
                closeImageModal();
                break;
            case 'ArrowLeft':
                prevImage();
                break;
            case 'ArrowRight':
                nextImage();
                break;
        }
    }
});

// 모달 배경 클릭시 닫기
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
<?php endif; ?>

/**
 * 행사 공유하기 기능
 */
function shareEventContent() {
    try {
        const eventTitle = "<?= addslashes(htmlspecialchars($event['title'])) ?>";
        const eventUrl = window.location.href;
        const eventDescription = "<?= addslashes(htmlspecialchars(substr(strip_tags($event['description'] ?? ''), 0, 100))) ?>...";
        
        // Web Share API 지원 확인
        if (navigator.share) {
            navigator.share({
                title: eventTitle,
                text: eventDescription,
                url: eventUrl
            }).then(() => {
                console.log('공유 성공');
            }).catch((error) => {
                console.log('공유 실패:', error);
                fallbackShare(eventTitle, eventUrl);
            });
        } else {
            // 폴백: 클립보드 복사 또는 공유 옵션 표시
            fallbackShare(eventTitle, eventUrl);
        }
    } catch (error) {
        console.error('공유 기능 오류:', error);
        alert('공유 기능에 오류가 발생했습니다.');
    }
}

/**
 * 폴백 공유 기능 (클립보드 복사)
 */
function fallbackShare(title, url) {
    // 클립보드에 URL 복사
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert('🔗 링크가 클립보드에 복사되었습니다!\n다른 곳에 붙여넣기하여 공유하세요.');
        }).catch(() => {
            showShareModal(title, url);
        });
    } else {
        showShareModal(title, url);
    }
}

/**
 * 공유 모달 표시
 */
function showShareModal(title, url) {
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    `;
    
    content.innerHTML = `
        <h3 style="margin-bottom: 20px; color: #2d3748;">🔗 행사 공유하기</h3>
        <p style="margin-bottom: 20px; color: #4a5568;">${title}</p>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; word-break: break-all; font-family: monospace; font-size: 14px;">
            ${url}
        </div>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <button onclick="copyToClipboard('${url}')" style="padding: 10px 20px; background: #4A90E2; color: white; border: none; border-radius: 6px; cursor: pointer;">
                📋 복사하기
            </button>
            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank" style="padding: 10px 20px; background: #4267B2; color: white; text-decoration: none; border-radius: 6px;">
                📘 Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}" target="_blank" style="padding: 10px 20px; background: #1DA1F2; color: white; text-decoration: none; border-radius: 6px;">
                🐦 Twitter
            </a>
            <a href="https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}" target="_blank" style="padding: 10px 20px; background: #0088CC; color: white; text-decoration: none; border-radius: 6px;">
                📤 Telegram
            </a>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" style="padding: 10px 20px; background: #a0aec0; color: white; border: none; border-radius: 6px; cursor: pointer;">
                닫기
            </button>
        </div>
    `;
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    // 모달 외부 클릭 시 닫기
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

/**
 * 클립보드 복사
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            alert('✅ 링크가 복사되었습니다!');
        });
    } else {
        // 폴백 방법
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        alert('✅ 링크가 복사되었습니다!');
    }
}
</script>