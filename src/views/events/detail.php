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
}

.event-hero {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    padding: 60px 30px;
    border-radius: 16px;
    margin-bottom: 40px;
    margin-top: 60px;
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

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #4A90E2;
    text-decoration: none;
    padding: 10px 0;
    margin-bottom: 20px;
    transition: color 0.3s;
}

.back-btn:hover {
    color: #357ABD;
    text-decoration: none;
}

/* 반응형 */
@media (max-width: 768px) {
    .event-detail-container {
        padding: 20px 10px;
    }
    
    .event-hero {
        margin-top: 20px;
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
}
</style>

<div class="event-detail-container">
    <!-- 뒤로가기 버튼 -->
    <a href="/events" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        행사 일정으로 돌아가기
    </a>

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
        </div>
    </div>

    <!-- 메인 콘텐츠 -->
    <div class="event-content">
        <!-- 메인 영역 -->
        <div class="event-main">
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
                <div class="instructor-avatar">
                    <?= mb_substr($event['instructor_name'], 0, 1) ?>
                </div>
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
            <?php endif; ?>
        </div>
    </div>
</div>

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
</script>