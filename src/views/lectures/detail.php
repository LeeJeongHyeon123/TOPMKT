<?php
/**
 * 강의 상세 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();
?>

<style>
/* 강의 상세 페이지 스타일 */
.lecture-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.lecture-header {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 20px;
}

.lecture-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    position: relative;
}

.lecture-category {
    display: inline-block;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.lecture-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    line-height: 1.2;
}

.lecture-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 20px;
}

.lecture-meta-basic {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1rem;
}

.meta-icon {
    font-size: 1.2rem;
}

.lecture-actions {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #48bb78;
    color: white;
}

.btn-primary:hover {
    background: #38a169;
    transform: translateY(-1px);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}

.btn-edit {
    background: #ed8936;
    color: white;
}

.btn-edit:hover {
    background: #dd6b20;
}

/* 콘텐츠 레이아웃 */
.lecture-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
}

.lecture-main {
    background: white;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.lecture-sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.sidebar-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #667eea;
}

/* 강의 정보 섹션 */
.info-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.description-content {
    color: #4a5568;
    line-height: 1.7;
    font-size: 1rem;
}

.instructor-info {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.instructor-name {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 10px;
}

.instructor-details {
    color: #718096;
    line-height: 1.6;
}

/* 일정 정보 */
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.schedule-item {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
}

.schedule-label {
    font-size: 0.85rem;
    color: #718096;
    font-weight: 600;
    margin-bottom: 5px;
}

.schedule-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
}

/* 위치 정보 */
.location-info {
    background: #f0fff4;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #9ae6b4;
}

.location-type {
    display: inline-block;
    padding: 4px 8px;
    background: #48bb78;
    color: white;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-bottom: 10px;
}

.location-details {
    color: #2d3748;
    font-weight: 600;
}

/* 신청 정보 */
.registration-info {
    text-align: center;
}

.registration-status {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.registration-count {
    color: #667eea;
}

.registration-deadline {
    color: #e53e3e;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.registration-fee {
    font-size: 1.5rem;
    font-weight: 700;
    color: #48bb78;
    margin-bottom: 20px;
}

.btn-register {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    font-size: 1.1rem;
    padding: 15px 30px;
    border-radius: 8px;
    font-weight: 700;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
}

.btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
}

.btn-register:disabled {
    background: #a0aec0;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* 참가자 목록 */
.participants-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.participant-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f8fafc;
    border-radius: 6px;
}

.participant-avatar {
    width: 32px;
    height: 32px;
    background: #667eea;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.8rem;
}

.participant-info {
    flex: 1;
}

.participant-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 0.9rem;
}

.participant-date {
    font-size: 0.8rem;
    color: #718096;
}

/* 관련 강의 */
.related-lectures {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.related-lecture-item {
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    border-left: 4px solid #667eea;
}

.related-lecture-item:hover {
    background: #e2e8f0;
    transform: translateX(4px);
}

.related-lecture-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.related-lecture-meta {
    font-size: 0.8rem;
    color: #718096;
}

/* 모바일 반응형 */
@media (max-width: 1024px) {
    .lecture-content {
        grid-template-columns: 1fr;
    }
    
    .lecture-sidebar {
        order: -1;
    }
}

@media (max-width: 768px) {
    .lecture-detail-container {
        padding: 15px;
    }
    
    .lecture-banner {
        padding: 30px 20px;
    }
    
    .lecture-title {
        font-size: 2rem;
    }
    
    .lecture-actions {
        position: static;
        justify-content: center;
        margin-top: 20px;
    }
    
    .lecture-meta-basic {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .schedule-grid {
        grid-template-columns: 1fr;
    }
}

/* 다크모드 대응 */
@media (prefers-color-scheme: dark) {
    .lecture-header, .lecture-main, .sidebar-card {
        background: #2d3748;
        border-color: #4a5568;
    }
    
    .schedule-item, .instructor-info, .participant-item, .related-lecture-item {
        background: #4a5568;
    }
}
</style>

<div class="lecture-detail-container">
    <!-- 강의 헤더 -->
    <div class="lecture-header">
        <div class="lecture-banner">
            <div class="lecture-actions">
                <?php if ($canEdit): ?>
                    <a href="/lectures/<?= $lecture['id'] ?>/edit" class="btn btn-edit">
                        ✏️ 수정
                    </a>
                <?php endif; ?>
                <a href="<?= $iCalUrl ?>" class="btn btn-secondary" download>
                    📅 일정 추가
                </a>
            </div>
            
            <div class="lecture-category">
                <?= [
                    'seminar' => '📢 세미나',
                    'workshop' => '🛠️ 워크샵',
                    'conference' => '🏢 컨퍼런스',
                    'webinar' => '💻 웨비나',
                    'training' => '🎓 교육과정'
                ][$lecture['category']] ?? $lecture['category'] ?>
            </div>
            
            <h1 class="lecture-title"><?= htmlspecialchars($lecture['title']) ?></h1>
            <p class="lecture-subtitle">
                👨‍🏫 <?= htmlspecialchars($lecture['organizer_name']) ?> 강사님과 함께하는 특별한 시간
            </p>
            
            <div class="lecture-meta-basic">
                <div class="meta-item">
                    <span class="meta-icon">📅</span>
                    <span><?= date('Y년 m월 d일', strtotime($lecture['start_date'])) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">🕒</span>
                    <span><?= date('H:i', strtotime($lecture['start_time'])) ?> - <?= date('H:i', strtotime($lecture['end_time'])) ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">
                        <?php if ($lecture['location_type'] === 'online'): ?>
                            💻
                        <?php elseif ($lecture['location_type'] === 'hybrid'): ?>
                            🔄
                        <?php else: ?>
                            📍
                        <?php endif; ?>
                    </span>
                    <span>
                        <?php if ($lecture['location_type'] === 'online'): ?>
                            온라인 진행
                        <?php elseif ($lecture['location_type'] === 'hybrid'): ?>
                            하이브리드 (온라인 + 오프라인)
                        <?php else: ?>
                            <?= htmlspecialchars($lecture['venue_name'] ?? '오프라인 진행') ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">👥</span>
                    <span><?= $lecture['capacity_info'] ?></span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 메인 콘텐츠 -->
    <div class="lecture-content">
        <div class="lecture-main">
            <!-- 강의 설명 -->
            <div class="info-section">
                <h2 class="section-title">📋 강의 소개</h2>
                <div class="description-content">
                    <?= nl2br(htmlspecialchars($lecture['description'])) ?>
                </div>
            </div>
            
            <!-- 강사 정보 -->
            <div class="info-section">
                <h2 class="section-title">👨‍🏫 강사 소개</h2>
                <div class="instructor-info">
                    <div class="instructor-name"><?= htmlspecialchars($lecture['instructor_name']) ?></div>
                    <div class="instructor-details">
                        <?php if (!empty($lecture['instructor_info'])): ?>
                            <?= nl2br(htmlspecialchars($lecture['instructor_info'])) ?>
                        <?php else: ?>
                            전문적인 경험과 노하우를 바탕으로 실무에 바로 적용할 수 있는 내용을 전달합니다.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- 일정 상세 -->
            <div class="info-section">
                <h2 class="section-title">📅 일정 상세</h2>
                <div class="schedule-grid">
                    <div class="schedule-item">
                        <div class="schedule-label">시작일</div>
                        <div class="schedule-value"><?= date('Y-m-d', strtotime($lecture['start_date'])) ?></div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">종료일</div>
                        <div class="schedule-value"><?= date('Y-m-d', strtotime($lecture['end_date'])) ?></div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">시작시간</div>
                        <div class="schedule-value"><?= date('H:i', strtotime($lecture['start_time'])) ?></div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">종료시간</div>
                        <div class="schedule-value"><?= date('H:i', strtotime($lecture['end_time'])) ?></div>
                    </div>
                </div>
            </div>
            
            <!-- 위치 정보 -->
            <?php if ($lecture['location_type'] !== 'online'): ?>
                <div class="info-section">
                    <h2 class="section-title">📍 위치 정보</h2>
                    <div class="location-info">
                        <div class="location-type">
                            <?php if ($lecture['location_type'] === 'hybrid'): ?>
                                🔄 하이브리드
                            <?php else: ?>
                                📍 오프라인
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($lecture['venue_name'])): ?>
                            <div class="location-details">
                                <strong><?= htmlspecialchars($lecture['venue_name']) ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($lecture['venue_address'])): ?>
                            <div style="margin-top: 5px; color: #718096;">
                                <?= htmlspecialchars($lecture['venue_address']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 참가 요구사항 -->
            <?php if (!empty($lecture['requirements'])): ?>
                <div class="info-section">
                    <h2 class="section-title">📝 참가 요구사항</h2>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($lecture['requirements'])) ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 혜택 정보 -->
            <?php if (!empty($lecture['benefits'])): ?>
                <div class="info-section">
                    <h2 class="section-title">🎁 혜택</h2>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($lecture['benefits'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- 사이드바 -->
        <div class="lecture-sidebar">
            <!-- 신청 정보 -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">🎫 신청 정보</h3>
                <div class="registration-info">
                    <div class="registration-status">
                        <span class="registration-count"><?= $lecture['capacity_info'] ?></span>
                    </div>
                    
                    <?php if ($lecture['registration_deadline']): ?>
                        <div class="registration-deadline">
                            ⏰ 등록 마감: <?= date('Y-m-d H:i', strtotime($lecture['registration_deadline'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="registration-fee">
                        <?php if ($lecture['registration_fee'] > 0): ?>
                            💰 <?= number_format($lecture['registration_fee']) ?>원
                        <?php else: ?>
                            🆓 무료
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($isLoggedIn): ?>
                        <?php if ($userRegistration): ?>
                            <div class="btn-register" style="background: #68d391; cursor: default;">
                                ✅ 신청 완료
                            </div>
                        <?php elseif ($canRegister): ?>
                            <a href="/lectures/<?= $lecture['id'] ?>/register" class="btn-register">
                                📝 지금 신청하기
                            </a>
                        <?php else: ?>
                            <div class="btn-register" style="background: #a0aec0; cursor: not-allowed;">
                                ❌ 신청 마감
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-register">
                            🔑 로그인 후 신청
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- 참가자 목록 -->
            <?php if (!empty($registrations)): ?>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">👥 참가자 목록</h3>
                    <div class="participants-list">
                        <?php foreach ($registrations as $registration): ?>
                            <div class="participant-item">
                                <div class="participant-avatar">
                                    <?= mb_substr($registration['nickname'], 0, 1) ?>
                                </div>
                                <div class="participant-info">
                                    <div class="participant-name"><?= htmlspecialchars($registration['nickname']) ?></div>
                                    <div class="participant-date"><?= date('m/d', strtotime($registration['registration_date'])) ?> 신청</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 관련 강의 -->
            <?php if (!empty($relatedLectures)): ?>
                <div class="sidebar-card">
                    <h3 class="sidebar-title">📚 관련 강의</h3>
                    <div class="related-lectures">
                        <?php foreach ($relatedLectures as $relatedLecture): ?>
                            <a href="/lectures/<?= $relatedLecture['id'] ?>" class="related-lecture-item">
                                <div class="related-lecture-title"><?= htmlspecialchars($relatedLecture['title']) ?></div>
                                <div class="related-lecture-meta">
                                    📅 <?= date('m/d', strtotime($relatedLecture['start_date'])) ?> | 
                                    👨‍🏫 <?= htmlspecialchars($relatedLecture['organizer_name']) ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📅 강의 상세 페이지 로드 완료');
    console.log('📊 강의 ID:', <?= $lecture['id'] ?>);
    console.log('👥 신청자 수:', <?= count($registrations ?? []) ?>);
    
    // 강의 상세 관련 전역 객체 정의
    if (typeof window.lectureDetail === 'undefined') {
        window.lectureDetail = {
            initialized: true,
            lectureId: <?= $lecture['id'] ?>,
            canRegister: <?= $canRegister ? 'true' : 'false' ?>,
            canEdit: <?= $canEdit ? 'true' : 'false' ?>,
            userRegistered: <?= $userRegistration ? 'true' : 'false' ?>,
            registrationCount: <?= count($registrations ?? []) ?>
        };
    }
    
    // 신청 버튼 클릭 이벤트
    const registerBtn = document.querySelector('.btn-register[href*="register"]');
    if (registerBtn) {
        registerBtn.addEventListener('click', function(e) {
            // 신청 확인
            if (!confirm('이 강의에 신청하시겠습니까?')) {
                e.preventDefault();
            }
        });
    }
    
    // 일정 추가 버튼 이벤트
    const icalBtn = document.querySelector('a[download]');
    if (icalBtn) {
        icalBtn.addEventListener('click', function() {
            console.log('📅 iCal 파일 다운로드 시작');
        });
    }
    
    // 참가자 목록 애니메이션
    const participantItems = document.querySelectorAll('.participant-item');
    participantItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
        item.style.animation = 'fadeInUp 0.5s ease forwards';
    });
    
    // 관련 강의 호버 효과
    const relatedItems = document.querySelectorAll('.related-lecture-item');
    relatedItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(4px)';
        });
    });
    
    // 뒤로가기 단축키
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.history.back();
        }
    });
});

// 애니메이션 키프레임 추가
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);
</script>