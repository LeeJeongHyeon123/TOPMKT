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
    max-width: 1600px;
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
    margin-top: 60px;
    margin-bottom: 20px;
}

.lecture-banner {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 120px 40px 40px 40px;
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
    color: rgba(255, 255, 255, 0.95);
    font-weight: 500;
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
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-edit:hover {
    background: rgba(255, 255, 255, 0.3);
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

/* 강사 정보 개선 */
.instructors-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.instructor-card {
    background: #f8fafc;
    padding: 25px;
    border-radius: 12px;
    border-left: 4px solid #667eea;
    display: flex;
    gap: 20px;
    align-items: flex-start;
    transition: all 0.3s ease;
}

.instructor-card:hover {
    background: #edf2f7;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.instructor-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #667eea;
    flex-shrink: 0;
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
    transition: all 0.3s ease;
    cursor: pointer;
}

.instructor-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.3);
}

.instructor-avatar.clickable-image {
    cursor: pointer;
}

.instructor-avatar.clickable-image:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.instructor-avatar.placeholder {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.5rem;
    position: relative;
}

.instructor-avatar.placeholder::after {
    content: '';
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    background: linear-gradient(45deg, #48bb78, #38a169);
    border-radius: 50%;
    border: 2px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
}

.instructor-avatar.placeholder::before {
    content: '👨‍🏫';
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* 이미지 로딩 상태 표시 */
.instructor-avatar.loading {
    position: relative;
    opacity: 0.7;
}

.instructor-avatar.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 이미지 오류 상태 표시 */
.instructor-avatar.error {
    background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
    position: relative;
}

.instructor-avatar.error::before {
    content: '⚠️';
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 20px;
    height: 20px;
    background: #fed7d7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    border: 2px solid white;
}

.instructor-content {
    flex: 1;
}

.instructor-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.instructor-name {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2d3748;
}

.instructor-badge {
    padding: 4px 8px;
    background: #667eea;
    color: white;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.instructor-title {
    font-size: 1rem;
    color: #4a5568;
    font-weight: 600;
    margin-bottom: 8px;
}

.instructor-details {
    color: #718096;
    line-height: 1.6;
    font-size: 0.95rem;
}

.instructor-experience {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e2e8f0;
    font-size: 0.9rem;
    color: #4a5568;
}

/* 레거시 지원 */
.instructor-info {
    background: #f8fafc;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

/* 일정 정보 */
.schedule-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.schedule-item {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    text-align: center;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.schedule-item:hover {
    background: #edf2f7;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
}

.schedule-label {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.schedule-value {
    font-size: 1.2rem;
    font-weight: 700;
    color: #2d3748;
    line-height: 1.3;
}

/* 위치 정보 */
.location-info {
    background: #f8fafc;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    color: #2d3748;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.location-type {
    display: inline-block;
    padding: 6px 12px;
    background: #667eea;
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 12px;
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
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
    
    /* 강사 카드 모바일 대응 */
    .instructor-card {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .instructor-avatar {
        width: 60px;
        height: 60px;
        margin: 0 auto;
    }
    
    .instructor-header {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .instructor-experience {
        text-align: left;
    }
}

/* 강의 갤러리 스타일 */
.lecture-gallery {
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
}

.gallery-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
    background: rgba(0, 0, 0, 0.6);
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

/* 이미지 모달 */
.image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(4px);
}

.modal-image-content {
    position: relative;
    margin: auto;
    display: block;
    width: 90%;
    max-width: 1000px;
    max-height: 90vh;
    object-fit: contain;
    margin-top: 5vh;
    border-radius: 8px;
}

.modal-image-close {
    position: absolute;
    top: 20px;
    right: 35px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    transition: color 0.3s ease;
}

.modal-image-close:hover {
    color: #ccc;
}

.modal-image-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.6);
    color: white;
    border: none;
    font-size: 18px;
    width: 50px;
    height: 50px;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.modal-image-nav:hover {
    background: rgba(0, 0, 0, 0.8);
    border-color: rgba(255, 255, 255, 0.4);
    transform: translateY(-50%) scale(1.1);
}

.modal-nav-prev {
    left: 20px;
}

.modal-nav-next {
    right: 20px;
}

.modal-nav-prev::before {
    content: '‹';
    font-size: 24px;
    font-weight: bold;
    line-height: 1;
}

.modal-nav-next::before {
    content: '›';
    font-size: 24px;
    font-weight: bold;
    line-height: 1;
}

.modal-image-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    background: rgba(0, 0, 0, 0.7);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
}

/* 네이버 지도 스타일 */
.naver-map-container {
    margin-top: 15px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e2e8f0;
}

/* 네이버 지도 컨테이너 호버 효과 */
.naver-map-container:hover {
    box-shadow: 0 4px 12px rgba(0, 199, 60, 0.15);
    transition: box-shadow 0.3s ease;
}

/* 프로필 이미지 모달 스타일 */
.profile-image-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.profile-image-modal .modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 16px;
    min-width: 300px;
    max-width: 90vw;
    max-height: 90vh;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.profile-image-modal .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8fafc;
}

.profile-image-modal .modal-header h3 {
    margin: 0;
    color: #2d3748;
    font-size: 1.2rem;
    font-weight: 600;
}

.profile-image-modal .modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #718096;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.profile-image-modal .modal-close:hover {
    background: #e2e8f0;
    color: #2d3748;
}

.profile-image-modal .modal-body {
    padding: 24px;
    text-align: center;
    background: white;
}

.profile-image-modal .modal-body img {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* 작성자 정보 카드 (컴팩트) */
.author-info-card {
    border-left: 3px solid #667eea;
}

.author-info-compact {
    display: flex;
    align-items: center;
    gap: 12px;
}

.author-avatar-small {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    flex-shrink: 0;
    transition: transform 0.2s ease;
    cursor: pointer;
}

.author-avatar-small:hover {
    transform: scale(1.1);
}

.author-details-compact {
    flex: 1;
    min-width: 0;
}

.author-name-compact {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 2px;
}

.author-meta-compact {
    font-size: 0.8rem;
    color: #718096;
    margin-bottom: 4px;
}

.author-bio-compact {
    font-size: 0.8rem;
    color: #4a5568;
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* 프로필 방문 버튼 */
.btn-visit-profile {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: #667eea;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
    justify-content: center;
    height: 44px;
    margin: 0;
}

.btn-visit-profile:hover {
    background: #5a67d8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    text-decoration: none;
}

.btn-visit-profile i {
    font-size: 0.875rem;
}

.btn-chat-author {
    background: #667eea;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 8px 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 44px;
    height: 44px;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 600;
    margin: 0;
    flex-shrink: 0;
}

.btn-chat-author:hover {
    background: #5a67d8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    text-decoration: none;
    color: white;
}

.btn-chat-author i {
    font-size: 0.875rem;
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
    
    .profile-image-modal .modal-content {
        background: #2d3748;
    }
    
    .profile-image-modal .modal-header {
        background: #4a5568;
        border-color: #718096;
    }
    
    .profile-image-modal .modal-header h3 {
        color: #e2e8f0;
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
                <button class="btn btn-secondary" onclick="shareContent()">
                    🔗 공유하기
                </button>
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
                    <span class="meta-icon">🟢</span>
                    <span>
                        시작 : <?= date('Y년 m월 d일 H:i', strtotime($lecture['start_date'] . ' ' . $lecture['start_time'])) ?>
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-icon">🔴</span>
                    <span>
                        종료 : <?= date('Y년 m월 d일 H:i', strtotime($lecture['end_date'] . ' ' . $lecture['end_time'])) ?>
                    </span>
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
            <!-- 강의 이미지 갤러리 -->
            <?php if (!empty($lecture['images'])): ?>
                <div class="info-section">
                    <h2 class="section-title">🖼️ 이미지 (총 <?= count($lecture['images']) ?>개)</h2>
                    
                    <div class="lecture-gallery">
                        <?php foreach ($lecture['images'] as $index => $image): ?>
                            <div class="gallery-item" onclick="openImageModal(<?= $index ?>)">
                                <img src="<?= htmlspecialchars($image['url']) ?>" 
                                     alt="강의 이미지 <?= $index + 1 ?>"
                                     loading="lazy">
                                <div class="gallery-overlay">
                                    <span>🔍 크게 보기</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 강의 설명 -->
            <div class="info-section">
                <h2 class="section-title">📋 강의 소개</h2>
                <div class="description-content">
                    <?= nl2br(htmlspecialchars($lecture['description'])) ?>
                </div>
            </div>
            
            <!-- 유튜브 동영상 -->
            <?php if (!empty($lecture['youtube_video'])): ?>
                <div class="info-section">
                    <h2 class="section-title">📹 동영상</h2>
                    <div class="video-container">
                        <?php
                        $youtubeUrl = $lecture['youtube_video'];
                        // 유튜브 URL을 embed 형태로 변환
                        $embedUrl = '';
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtubeUrl, $matches)) {
                            $videoId = $matches[1];
                            $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                        }
                        ?>
                        <?php if ($embedUrl): ?>
                            <iframe 
                                src="<?= htmlspecialchars($embedUrl) ?>" 
                                width="100%" 
                                height="400" 
                                frameborder="0" 
                                allowfullscreen
                                style="border-radius: 8px;">
                            </iframe>
                        <?php else: ?>
                            <div style="padding: 20px; background: #f8fafc; border-radius: 8px; text-align: center;">
                                <p>📹 <a href="<?= htmlspecialchars($youtubeUrl) ?>" target="_blank" rel="noopener">유튜브에서 동영상 보기</a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 강사 정보 -->
            <div class="info-section">
                <h2 class="section-title">👨‍🏫 강사 소개</h2>
                <div class="instructors-container">
                    <?php 
                    // 강사 정보 파싱 (여러 강사 대응)
                    $instructorNames = explode(',', $lecture['instructor_name']);
                    $instructorInfos = !empty($lecture['instructor_info']) ? 
                        explode('|||', $lecture['instructor_info']) : [];
                    
                    // instructors_json 필드에서 실제 강사 이미지 정보 가져오기
                    $instructorsData = [];
                    if (!empty($lecture['instructors_json'])) {
                        $instructorsData = json_decode($lecture['instructors_json'], true);
                        if (!$instructorsData) {
                            $instructorsData = [];
                        }
                    }
                    
                    // 디버깅: 강사 정보 출력 (개발 중에만 사용)
                    if (isset($_GET['debug'])) {
                        echo "<!-- 디버깅 정보:\n";
                        echo "강의 ID: " . $lecture['id'] . "\n";
                        echo "instructor_name: " . htmlspecialchars($lecture['instructor_name']) . "\n";
                        echo "instructor_info: " . htmlspecialchars($lecture['instructor_info']) . "\n";
                        echo "instructors_json: " . htmlspecialchars($lecture['instructors_json']) . "\n";
                        echo "강사 이름 배열: " . print_r($instructorNames, true) . "\n";
                        echo "강사 정보 배열: " . print_r($instructorInfos, true) . "\n";
                        echo "강사 JSON 데이터: " . print_r($instructorsData, true) . "\n";
                        
                        // 강사 이미지 파일 존재 여부 확인
                        if (!empty($instructorsData) && is_array($instructorsData)) {
                            echo "강사 이미지 파일 존재 여부:\n";
                            foreach ($instructorsData as $index => $instructor) {
                                if (!empty($instructor['image'])) {
                                    $imagePath = $_SERVER['DOCUMENT_ROOT'] . $instructor['image'];
                                    $exists = file_exists($imagePath);
                                    echo "  강사 {$index}: " . $instructor['image'] . " => " . ($exists ? 'EXISTS' : 'NOT FOUND') . "\n";
                                    if ($exists) {
                                        echo "    파일 크기: " . filesize($imagePath) . " bytes\n";
                                    } else {
                                        echo "    전체 경로: " . $imagePath . "\n";
                                    }
                                } else {
                                    echo "  강사 {$index}: 이미지 경로 없음\n";
                                }
                            }
                        }
                        echo "-->\n";
                    }
                    
                    // instructors_json 데이터가 있으면 우선 사용, 없으면 기존 필드 사용
                    $finalInstructors = [];
                    
                    if (!empty($instructorsData) && is_array($instructorsData)) {
                        // instructors_json에서 강사 정보 사용
                        foreach ($instructorsData as $index => $instructor) {
                            $finalInstructors[] = [
                                'name' => $instructor['name'] ?? '',
                                'info' => $instructor['info'] ?? '전문적인 경험과 노하우를 바탕으로 실무에 바로 적용할 수 있는 내용을 전달합니다.',
                                'title' => $instructor['title'] ?? '강사',
                                'image' => $instructor['image'] ?? null
                            ];
                        }
                    } else {
                        // 기존 필드에서 강사 정보 사용
                        foreach ($instructorNames as $index => $instructorName) {
                            $name = trim($instructorName);
                            $info = isset($instructorInfos[$index]) ? trim($instructorInfos[$index]) : '';
                            if (empty($info)) {
                                $info = '전문적인 경험과 노하우를 바탕으로 실무에 바로 적용할 수 있는 내용을 전달합니다.';
                            }
                            
                            $finalInstructors[] = [
                                'name' => $name,
                                'info' => $info,
                                'title' => '강사',
                                'image' => null
                            ];
                        }
                    }
                    
                    foreach ($finalInstructors as $index => $instructor): 
                        $name = $instructor['name'];
                        $info = $instructor['info'];
                        $title = $instructor['title'];
                        $imagePath = $instructor['image'];
                    ?>
                        <div class="instructor-card">
                            <!-- 강사 아바타 -->
                            <?php 
                            // 기본 강사 이미지 경로들
                            $defaultInstructorImages = [
                                '/assets/uploads/instructors/instructor-1.jpg',
                                '/assets/uploads/instructors/instructor-2.jpg', 
                                '/assets/uploads/instructors/instructor-3.jpg',
                                '/assets/uploads/instructors/instructor-kim.jpg',
                                '/assets/uploads/instructors/instructor-lee.jpg',
                                '/assets/uploads/instructors/instructor-park.jpg'
                            ];
                            
                            // 강사 이미지가 없거나 파일이 존재하지 않는 경우 기본 이미지 사용
                            if (!$imagePath || !file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
                                // 강사 이름 기반으로 기본 이미지 선택
                                $nameHash = crc32($name);
                                $selectedDefaultImage = $defaultInstructorImages[$nameHash % count($defaultInstructorImages)];
                                
                                // 기본 이미지 파일이 실제로 존재하는지 확인
                                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $selectedDefaultImage)) {
                                    $imagePath = $selectedDefaultImage;
                                }
                            }
                            ?>
                            
                            <?php if ($imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)): ?>
                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                     alt="<?= htmlspecialchars($name) ?> 강사님" 
                                     class="instructor-avatar clickable-image"
                                     loading="lazy"
                                     decoding="async"
                                     onerror="console.error('강사 이미지 로딩 실패:', this.src); this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                     onclick="openInstructorImageModal('<?= htmlspecialchars($imagePath) ?>', '<?= htmlspecialchars($name) ?> 강사님')"
                                     title="<?= htmlspecialchars($name) ?> 강사님 (클릭하면 크게 볼 수 있습니다)">
                                <!-- 이미지 로딩 실패 시 대체 표시 -->
                                <div class="instructor-avatar placeholder" style="display: none;" title="<?= htmlspecialchars($name) ?> 강사님">
                                    <?= mb_substr($name, 0, 1) ?>
                                </div>
                            <?php else: ?>
                                <!-- 기본 플레이스홀더 -->
                                <div class="instructor-avatar placeholder" title="<?= htmlspecialchars($name) ?> 강사님">
                                    <?= mb_substr($name, 0, 1) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- 강사 정보 -->
                            <div class="instructor-content">
                                <div class="instructor-header">
                                    <div class="instructor-name"><?= htmlspecialchars($name) ?></div>
                                    <?php if (count($finalInstructors) > 1): ?>
                                        <span class="instructor-badge"><?= htmlspecialchars($title) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="instructor-title">
                                    <?= htmlspecialchars($title ?: ([
                                        'seminar' => '세미나 전문가',
                                        'workshop' => '워크샵 진행자',
                                        'conference' => '컨퍼런스 연사',
                                        'webinar' => '웨비나 호스트',
                                        'training' => '교육 전문가'
                                    ][$lecture['category']] ?? '마케팅 전문가')) ?>
                                </div>
                                
                                <div class="instructor-details">
                                    <?= nl2br(htmlspecialchars($info)) ?>
                                </div>
                                
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- 일정 상세 -->
            <div class="info-section">
                <h2 class="section-title">📅 일정 상세</h2>
                <div class="schedule-grid">
                    <div class="schedule-item">
                        <div class="schedule-label">
                            <span>🚀</span> 시작일시
                        </div>
                        <div class="schedule-value">
                            <?= date('Y-m-d H:i', strtotime($lecture['start_date'] . ' ' . $lecture['start_time'])) ?>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">
                            <span>🏁</span> 종료일시
                        </div>
                        <div class="schedule-value">
                            <?= date('Y-m-d H:i', strtotime($lecture['end_date'] . ' ' . $lecture['end_time'])) ?>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">
                            <span>⏱️</span> 소요시간
                        </div>
                        <div class="schedule-value">
                            <?php 
                            $startDateTime = strtotime($lecture['start_date'] . ' ' . $lecture['start_time']);
                            $endDateTime = strtotime($lecture['end_date'] . ' ' . $lecture['end_time']);
                            $durationMinutes = ($endDateTime - $startDateTime) / 60; // 분 단위
                            
                            $hours = floor($durationMinutes / 60);
                            $minutes = $durationMinutes % 60;
                            
                            if ($hours > 0 && $minutes > 0) {
                                echo $hours . '시간 ' . $minutes . '분';
                            } elseif ($hours > 0) {
                                echo $hours . '시간';
                            } else {
                                echo $minutes . '분';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="schedule-item">
                        <div class="schedule-label">
                            <span>🌏</span> 시간대
                        </div>
                        <div class="schedule-value">
                            <?= $lecture['timezone'] ?? 'Asia/Seoul' ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 위치 정보 -->
            <?php if ($lecture['location_type'] !== 'online'): ?>
                <div class="info-section">
                    <h2 class="section-title">📍 위치 정보</h2>
                    <div class="location-info">
                        <div class="location-type">
                            📍 오프라인
                        </div>
                        <?php if (!empty($lecture['venue_name'])): ?>
                            <div class="location-details">
                                <strong><?= htmlspecialchars($lecture['venue_name']) ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($lecture['venue_address'])): ?>
                            <div style="margin-top: 8px; color: #4a5568; font-size: 14px; line-height: 1.5;">
                                📍 <?= htmlspecialchars($lecture['venue_address']) ?>
                            </div>
                            <!-- 네이버 지도 표시 (간단 다이나믹 맵) -->
                            <div class="naver-map-container">
                                <?php
                                $venueName = !empty($lecture['venue_name']) ? $lecture['venue_name'] : '강의 장소';
                                $mapAddress = !empty($lecture['venue_address']) ? $lecture['venue_address'] : '';
                                $naverClientId = defined('NAVER_MAPS_CLIENT_ID') ? NAVER_MAPS_CLIENT_ID : 'c5yj6m062z';
                                
                                // 실제 저장된 좌표 사용 (우선순위 1)
                                if (!empty($lecture['venue_latitude']) && !empty($lecture['venue_longitude'])) {
                                    $defaultCoords = [
                                        'lat' => floatval($lecture['venue_latitude']),
                                        'lng' => floatval($lecture['venue_longitude'])
                                    ];
                                } else {
                                    // 좌표가 없는 경우 지역 기반 근사 좌표 사용 (fallback)
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
                                    } elseif (strpos($mapAddress, '강남') !== false) {
                                        $defaultCoords['lat'] = 37.4979;
                                        $defaultCoords['lng'] = 127.0276;
                                    } elseif (strpos($mapAddress, '홍대') !== false || strpos($mapAddress, '마포') !== false) {
                                        $defaultCoords['lat'] = 37.5563;
                                        $defaultCoords['lng'] = 126.9236;
                                    } elseif (strpos($mapAddress, '송파') !== false || strpos($mapAddress, '올림픽로') !== false) {
                                        $defaultCoords['lat'] = 37.5126;
                                        $defaultCoords['lng'] = 127.1026;
                                    } elseif (strpos($mapAddress, '청주') !== false) {
                                        $defaultCoords['lat'] = 36.6424;
                                        $defaultCoords['lng'] = 127.4890;
                                    }
                                }
                                ?>
                                
                                <!-- 지도 컨테이너 -->
                                <div id="naverMap-<?= $lecture['id'] ?>" style="
                                    width: 100%; 
                                    height: 350px; 
                                    border-radius: 8px; 
                                    overflow: hidden;
                                    border: 1px solid #e2e8f0;
                                "></div>
                                
                                <!-- 네이버 지도 API (간단 버전) -->
                                <script type="text/javascript" 
                                        src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=<?= htmlspecialchars($naverClientId) ?>&callback=initSimpleNaverMap_<?= $lecture['id'] ?>"
                                        onerror="showMapFallback_<?= $lecture['id'] ?>()">
                                </script>
                                
                                <script type="text/javascript">
                                // 네이버 지도 API 사용 가능 여부 확인
                                function checkNaverMapsAPI() {
                                    return typeof naver !== 'undefined' && 
                                           typeof naver.maps !== 'undefined' && 
                                           typeof naver.maps.Map !== 'undefined';
                                }
                                
                                // 지도 대체 UI 표시 함수
                                function showMapFallback_<?= $lecture['id'] ?>() {
                                    var mapContainer = document.getElementById('naverMap-<?= $lecture['id'] ?>');
                                    if (mapContainer) {
                                        mapContainer.innerHTML = 
                                            '<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; background: #f8fafc; color: #4a5568; border-radius: 8px; border: 1px solid #e2e8f0;">' +
                                            '<div style="font-size: 32px; margin-bottom: 15px; color: #667eea;">🏢</div>' +
                                            '<div style="font-weight: bold; margin-bottom: 8px; font-size: 16px; color: #2d3748;">' + <?= json_encode($venueName) ?> + '</div>' +
                                            '<div style="font-size: 13px; margin-bottom: 20px; text-align: center; padding: 0 20px; color: #4a5568;">' + <?= json_encode($mapAddress) ?> + '</div>' +
                                            '<a href="https://map.naver.com/v5/search/<?= urlencode($mapAddress) ?>" target="_blank" ' +
                                            'style="background: #667eea; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold;">' +
                                            '📍 네이버 지도에서 보기</a>' +
                                            '</div>';
                                    }
                                }
                                
                                // 강의별 독립적인 지도 초기화 함수
                                function initSimpleNaverMap_<?= $lecture['id'] ?>() {
                                    try {
                                        // 네이버 지도 API 사용 가능 여부 확인
                                        if (!checkNaverMapsAPI()) {
                                            console.warn('🗺️ 네이버 지도 API를 사용할 수 없습니다.');
                                            showMapFallback_<?= $lecture['id'] ?>();
                                            return;
                                        }
                                        
                                        console.log('🗺️ 네이버 지도 (강의 <?= $lecture['id'] ?>) 초기화 시작');
                                        
                                        // 지도 중심 좌표
                                        var center = new naver.maps.LatLng(<?= floatval($defaultCoords['lat']) ?>, <?= floatval($defaultCoords['lng']) ?>);
                                        
                                        // 지도 옵션
                                        var mapOptions = {
                                            center: center,
                                            zoom: 19,
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
                                        var map = new naver.maps.Map('naverMap-<?= $lecture['id'] ?>', mapOptions);
                                        
                                        // 빨간색 마커 생성 (네이버 맵 기본 마커 사용)
                                        var marker = new naver.maps.Marker({
                                            position: center,
                                            map: map,
                                            title: <?= json_encode($venueName) ?>,
                                            icon: {
                                                content: '<div style="width: 20px; height: 20px; background: #ff0000; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
                                                anchor: new naver.maps.Point(10, 10)
                                            }
                                        });
                                        
                                        // 깔끔한 정보창 생성
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
                                                '🏢 ' + <?= json_encode($venueName) ?> +
                                                '</div>' +
                                                '<div style="font-size: 12px; color: #4a5568; line-height: 1.4;">' +
                                                '📍 ' + <?= json_encode($mapAddress) ?> +
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
                                            } catch (e) {
                                                console.warn('마커 클릭 이벤트 오류:', e);
                                            }
                                        });
                                        
                                        // 지도 클릭 시 정보창 닫기
                                        naver.maps.Event.addListener(map, 'click', function() {
                                            try {
                                                infoWindow.close();
                                            } catch (e) {
                                                console.warn('지도 클릭 이벤트 오류:', e);
                                            }
                                        });
                                        
                                        // 1.5초 후 정보창 자동 열기
                                        setTimeout(function() {
                                            try {
                                                infoWindow.open(map, marker);
                                            } catch (e) {
                                                console.warn('정보창 자동 열기 오류:', e);
                                            }
                                        }, 1500);
                                        
                                        console.log('✅ 네이버 지도 (강의 <?= $lecture['id'] ?>) 초기화 완료');
                                        
                                    } catch (error) {
                                        console.error('❌ 네이버 지도 초기화 실패:', error);
                                        showMapFallback_<?= $lecture['id'] ?>();
                                    }
                                }
                                
                                // DOM 로드 완료 후 지도 API 확인
                                document.addEventListener('DOMContentLoaded', function() {
                                    // 3초 후에도 네이버 지도 API가 로드되지 않으면 대체 UI 표시
                                    setTimeout(function() {
                                        if (!checkNaverMapsAPI()) {
                                            console.warn('🗺️ 네이버 지도 API 로딩 타임아웃');
                                            showMapFallback_<?= $lecture['id'] ?>();
                                        }
                                    }, 3000);
                                });
                                
                                // 전역 오류 핸들러
                                window.addEventListener('error', function(e) {
                                    if (e.filename && e.filename.includes('maps.js')) {
                                        console.error('네이버 지도 스크립트 오류:', e.message);
                                        showMapFallback_<?= $lecture['id'] ?>();
                                    }
                                });
                                </script>
                            </div>
                            
                            <!-- 지도 하단 정보 -->
                            <div style="margin-top: 15px; padding: 12px; background: #f7fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #2d3748;">
                                    <span style="color: #667eea;">🏢</span>
                                    <strong><?= htmlspecialchars($lecture['venue_name'] ?? '강의 장소') ?></strong>
                                </div>
                                <div style="font-size: 13px; color: #4a5568; margin-top: 4px;">
                                    지도를 클릭하거나 확대하여 상세 위치를 확인하세요
                                </div>
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
            
            <!-- 참가 조건 -->
            <?php if (!empty($lecture['prerequisites'])): ?>
                <div class="info-section">
                    <h2 class="section-title">📋 참가 조건</h2>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($lecture['prerequisites'])) ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 준비물 -->
            <?php if (!empty($lecture['what_to_bring'])): ?>
                <div class="info-section">
                    <h2 class="section-title">🎒 준비물</h2>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($lecture['what_to_bring'])) ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- 기타 안내사항 -->
            <?php if (!empty($lecture['additional_info'])): ?>
                <div class="info-section">
                    <h2 class="section-title">📝 기타 안내사항</h2>
                    <div class="description-content">
                        <?= nl2br(htmlspecialchars($lecture['additional_info'])) ?>
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
                        <div style="font-size: 0.9rem; color: #718096; margin-bottom: 5px; font-weight: 600;">👥 신청 인원</div>
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

            <!-- 작성자 정보 -->
            <?php if (isset($lecture['author_name']) || isset($lecture['user_id'])): ?>
                <div class="sidebar-card author-info-card">
                    <h3 class="sidebar-title">✍️ 작성자</h3>
                    <div class="author-info-compact">
                        <div class="author-avatar-small" onclick="showProfileImageModal('<?= addslashes(htmlspecialchars($lecture['profile_image_original'] ?? $lecture['profile_image_profile'] ?? '')) ?>', '<?= addslashes(htmlspecialchars($lecture['author_name'] ?? $lecture['nickname'] ?? '작성자')) ?>')" style="cursor: pointer;" title="프로필 이미지 크게 보기">
                            <?php 
                            $authorImage = $lecture['profile_image'] ?? null;
                            $authorName = $lecture['author_name'] ?? $lecture['nickname'] ?? '작성자';
                            
                            if ($authorImage): ?>
                                <img src="<?= htmlspecialchars($authorImage) ?>" 
                                     alt="<?= htmlspecialchars($authorName) ?>" 
                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                    <?= mb_substr($authorName, 0, 1) ?>
                                </div>
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.9rem;">
                                    <?= mb_substr($authorName, 0, 1) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="author-details-compact">
                            <div class="author-name-compact"><?= htmlspecialchars($authorName) ?></div>
                            <div class="author-meta-compact">
                                📅 <?= date('Y.m.d', strtotime($lecture['created_at'])) ?>
                            </div>
                            <?php if (!empty($lecture['author_bio'])): ?>
                                <div class="author-bio-compact"><?= htmlspecialchars(mb_substr(strip_tags($lecture['author_bio']), 0, 80)) ?>...</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; margin-top: 12px; align-items: center;">
                        <?php if (isset($lecture['user_id'])): ?>
                            <a href="/profile/<?= $lecture['user_id'] ?>" class="btn-visit-profile" style="flex: 1;">
                                <i class="fas fa-user"></i> 프로필 방문
                            </a>
                            <?php if ($isLoggedIn && $lecture['user_id'] != $currentUserId): ?>
                                <button onclick="startChatWithAuthor(<?= $lecture['user_id'] ?>, '<?= addslashes(htmlspecialchars($authorName)) ?>')" class="btn-chat-author" title="채팅하기">
                                    <i class="fas fa-comment"></i>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 이미지 모달 -->
<div id="imageModal" class="image-modal">
    <span class="modal-image-close" onclick="closeImageModal()">&times;</span>
    <img class="modal-image-content" id="modalImage">
    <button class="modal-image-nav modal-nav-prev" onclick="changeImage(-1)"></button>
    <button class="modal-image-nav modal-nav-next" onclick="changeImage(1)"></button>
    <div class="modal-image-counter" id="imageCounter">    </div>
</div>

<!-- 프로필 이미지 확대 모달 -->
<div id="profileImageModal" class="profile-image-modal" onclick="closeProfileImageModal()">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 id="modalUserName">사용자 프로필</h3>
            <button class="modal-close" onclick="closeProfileImageModal()">&times;</button>
        </div>
        <div class="modal-body">
            <img id="modalProfileImage" src="" alt="프로필 이미지" style="min-width: 200px; min-height: 200px; max-width: 500px; max-height: 500px; width: auto; height: auto; border-radius: 8px;">
        </div>
    </div>
</div>

<script>
// 전역 오류 핸들러 추가
window.addEventListener('error', function(event) {
    console.error('JavaScript 오류 감지:', {
        message: event.message,
        filename: event.filename,
        lineno: event.lineno,
        colno: event.colno,
        error: event.error
    });
});

// 안전한 함수 실행 헬퍼
function safeExecute(fn, context) {
    try {
        return fn.call(context);
    } catch (error) {
        console.warn('함수 실행 중 오류:', error);
        return null;
    }
}

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
    
    // 강사 이미지 로딩 개선
    initializeInstructorImages();
    
    // 강사 이미지 로딩 함수
    function initializeInstructorImages() {
        const instructorImages = document.querySelectorAll('.instructor-avatar img');
        
        instructorImages.forEach((img, index) => {
            // 로딩 상태 표시
            img.parentElement.classList.add('loading');
            
            img.addEventListener('load', function() {
                console.log(`✅ 강사 이미지 ${index + 1} 로딩 성공:`, this.src);
                this.parentElement.classList.remove('loading');
                this.style.opacity = '1';
            });
            
            img.addEventListener('error', function() {
                console.warn(`❌ 강사 이미지 ${index + 1} 로딩 실패:`, this.src);
                this.parentElement.classList.remove('loading');
                this.parentElement.classList.add('error');
                
                // 이미지 숨기고 placeholder 표시
                this.style.display = 'none';
                const placeholder = this.nextElementSibling;
                if (placeholder && placeholder.classList.contains('placeholder')) {
                    placeholder.style.display = 'flex';
                    placeholder.classList.add('error');
                }
            });
            
            // 이미지가 이미 로드된 경우 (캐시된 경우)
            if (img.complete && img.naturalHeight !== 0) {
                img.parentElement.classList.remove('loading');
                img.style.opacity = '1';
                console.log(`✅ 강사 이미지 ${index + 1} 캐시에서 로드됨:`, img.src);
            }
        });
        
        // placeholder 이미지들에 호버 효과 추가
        const placeholders = document.querySelectorAll('.instructor-avatar.placeholder');
        placeholders.forEach(placeholder => {
            placeholder.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
                this.style.boxShadow = '0 6px 16px rgba(102, 126, 234, 0.3)';
            });
            
            placeholder.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '0 4px 8px rgba(102, 126, 234, 0.2)';
            });
        });
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

// 이미지 갤러리 관련 변수
let currentImageIndex = 0;
let lectureImages = [];
let instructorImages = [];
let currentGalleryType = 'lecture'; // 'lecture' 또는 'instructor'

// 강의 이미지 데이터 초기화
lectureImages = [];

<?php if (!empty($lecture['images']) && is_array($lecture['images'])): ?>
    <?php foreach ($lecture['images'] as $index => $image): ?>
        lectureImages.push({
            url: "<?= addslashes($image['url'] ?? '') ?>",
            alt: "<?= addslashes($image['alt_text'] ?? '강의 이미지') ?>"
        });
    <?php endforeach; ?>
<?php endif; ?>

// 강사 이미지 데이터 초기화 (instructors_json에서 추출)
instructorImages = [];
<?php 
// instructors_json에서 강사 이미지 추출
if (!empty($lecture['instructors_json'])) {
    $instructorsData = json_decode($lecture['instructors_json'], true);
    if (is_array($instructorsData)) {
        foreach ($instructorsData as $index => $instructor) {
            if (!empty($instructor['image'])) {
?>
                instructorImages.push({
                    url: "<?= addslashes($instructor['image']) ?>",
                    alt: "<?= addslashes(($instructor['name'] ?? '강사') . ' 이미지') ?>"
                });
<?php 
            }
        }
    }
}
?>

/**
 * 이미지 모달 열기 (강의 이미지용)
 */
function openImageModal(index) {
    if (lectureImages.length === 0) return;
    
    currentImageIndex = index;
    currentGalleryType = 'lecture';
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    modal.style.display = 'block';
    modalImg.src = lectureImages[currentImageIndex].url;
    counter.textContent = `${currentImageIndex + 1} / ${lectureImages.length}`;
    
    document.body.style.overflow = 'hidden';
}

/**
 * 강사 이미지 모달 열기 (강사 이미지 전용)
 */
function openInstructorImageModal(index) {
    if (instructorImages.length === 0) return;
    
    currentImageIndex = index;
    currentGalleryType = 'instructor';
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    modal.style.display = 'block';
    modalImg.src = instructorImages[currentImageIndex].url;
    counter.textContent = `강사 이미지 ${currentImageIndex + 1} / ${instructorImages.length}`;
    
    document.body.style.overflow = 'hidden';
}

/**
 * 이미지 모달 닫기
 */
function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    
    // 네비게이션 버튼 다시 보이기 (다음에 강의 이미지 갤러리에서 사용할 수 있도록)
    const prevBtn = document.querySelector('.modal-nav-prev');
    const nextBtn = document.querySelector('.modal-nav-next');
    const counter = document.getElementById('imageCounter');
    if (prevBtn) prevBtn.style.display = 'block';
    if (nextBtn) nextBtn.style.display = 'block';
    if (counter) counter.style.display = 'block';
    
    currentGalleryType = 'lecture'; // 기본값으로 리셋
}

/**
 * 이미지 변경 (이전/다음) - 갤러리 타입별 분리
 */
function changeImage(direction) {
    // 단일 강사 이미지인 경우 네비게이션 불가
    if (currentGalleryType === 'instructor-single') return;
    
    const currentImages = currentGalleryType === 'instructor' ? instructorImages : lectureImages;
    
    if (currentImages.length === 0) return;
    
    currentImageIndex += direction;
    
    if (currentImageIndex >= currentImages.length) {
        currentImageIndex = 0;
    } else if (currentImageIndex < 0) {
        currentImageIndex = currentImages.length - 1;
    }
    
    const modalImg = document.getElementById('modalImage');
    const counter = document.getElementById('imageCounter');
    
    modalImg.src = currentImages[currentImageIndex].url;
    
    if (currentGalleryType === 'instructor') {
        counter.textContent = `강사 이미지 ${currentImageIndex + 1} / ${currentImages.length}`;
    } else {
        counter.textContent = `${currentImageIndex + 1} / ${currentImages.length}`;
    }
}

// 모달 외부 클릭 시 닫기 (오류 방지)
document.addEventListener('DOMContentLoaded', function() {
    const imageModal = document.getElementById('imageModal');
    if (imageModal) {
        imageModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });
    }
});

// 키보드 이벤트 수정 (ESC는 이미지 모달 우선, 그 다음 뒤로가기)
document.addEventListener('keydown', function(e) {
    const imageModal = document.getElementById('imageModal');
    
    if (imageModal && imageModal.style.display === 'block') {
        // 이미지 모달이 열려있을 때
        if (e.key === 'Escape') {
            closeImageModal();
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            // 단일 강사 이미지가 아닌 경우에만 키보드 네비게이션 허용
            if (currentGalleryType !== 'instructor-single') {
                if (e.key === 'ArrowLeft') {
                    changeImage(-1);
                } else if (e.key === 'ArrowRight') {
                    changeImage(1);
                }
            }
        }
    } else {
        // 이미지 모달이 없거나 닫혀있을 때
        if (e.key === 'Escape') {
            window.history.back();
        }
    }
});

/**
 * 공유하기 기능
 */
// 작성자와 채팅 시작
function startChatWithAuthor(authorId, authorName) {
    if (!authorId) {
        alert('작성자 정보를 찾을 수 없습니다.');
        return;
    }
    
    // 채팅 페이지로 이동하면서 해당 사용자와 채팅 시작
    window.location.href = `/chat#user-${authorId}`;
}

function shareContent() {
    try {
        const lectureTitle = <?= json_encode($lecture['title']) ?>;
        const lectureUrl = window.location.href;
        const lectureDescription = <?php
            $description = '';
            if (isset($lecture['description']) && trim($lecture['description']) !== '') {
                $description = substr(strip_tags($lecture['description']), 0, 100) . '...';
            } else {
                $description = (isset($lecture['title']) ? $lecture['title'] . ' 강의에 참여해보세요!' : '탑마케팅 강의에 참여해보세요!');
            }
            
            // UTF-8 검증 및 정리
            if (!mb_check_encoding($description, 'UTF-8')) {
                $description = mb_convert_encoding($description, 'UTF-8', 'auto');
            }
            
            $jsonResult = json_encode($description, JSON_UNESCAPED_UNICODE);
            if ($jsonResult === false) {
                // JSON 인코딩 실패 시 안전한 기본값 사용
                echo '"강의에 참여해보세요!"';
            } else {
                echo $jsonResult;
            }
        ?>;
        
        // Web Share API 지원 확인
        if (navigator.share) {
            navigator.share({
                title: lectureTitle,
                text: lectureDescription,
                url: lectureUrl
            }).then(() => {
                console.log('공유 성공');
            }).catch((error) => {
                console.log('공유 실패:', error);
                fallbackShare(lectureTitle, lectureUrl);
            });
        } else {
            // 폴백: 클립보드 복사 또는 공유 옵션 표시
            fallbackShare(lectureTitle, lectureUrl);
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
        <h3 style="margin-bottom: 20px; color: #2d3748;">🔗 강의 공유하기</h3>
        <p style="margin-bottom: 20px; color: #4a5568;">${title}</p>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; word-break: break-all; font-family: monospace; font-size: 14px;">
            ${url}
        </div>
        <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <button onclick="copyToClipboard('${url}')" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">
                📋 복사하기
            </button>
            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}" target="_blank" style="padding: 10px 20px; background: #4267B2; color: white; text-decoration: none; border-radius: 6px;">
                📘 Facebook
            </a>
            <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(url)}" target="_blank" style="padding: 10px 20px; background: #1DA1F2; color: white; text-decoration: none; border-radius: 6px;">
                🐦 Twitter
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

/**
 * 강사 이미지 모달 열기 (단일 이미지)
 */
function openInstructorImageModal(imageSrc, imageAlt) {
    currentGalleryType = 'instructor-single'; // 특별한 타입으로 설정
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    if (modal && modalImg) {
        modal.style.display = 'block';
        modalImg.src = imageSrc;
        modalImg.alt = imageAlt || '강사 프로필 이미지';
        
        // 카운터 숨기기 (단일 이미지이므로)
        const counter = document.getElementById('imageCounter');
        if (counter) {
            counter.style.display = 'none';
        }
        
        // 네비게이션 버튼 숨기기
        const prevBtn = document.querySelector('.modal-nav-prev');
        const nextBtn = document.querySelector('.modal-nav-next');
        if (prevBtn) prevBtn.style.display = 'none';
        if (nextBtn) nextBtn.style.display = 'none';
        
        document.body.style.overflow = 'hidden';
    }
}

// 프로필 이미지 모달 함수
function showProfileImageModal(imageSrc, userName) {
    if (!imageSrc || imageSrc.trim() === '') {
        alert('원본 프로필 이미지를 찾을 수 없습니다.');
        return; // 이미지가 없으면 모달을 열지 않음
    }
    
    const modal = document.getElementById('profileImageModal');
    const modalImage = document.getElementById('modalProfileImage');
    const modalUserName = document.getElementById('modalUserName');
    
    if (!modal || !modalImage || !modalUserName) {
        console.error('프로필 모달 요소를 찾을 수 없습니다.');
        return;
    }
    
    // 이미지 로딩 상태 표시
    modalImage.style.display = 'none';
    modalUserName.textContent = userName + '의 프로필';
    modal.style.display = 'block';
    
    // 새 이미지 객체로 로딩 확인
    const img = new Image();
    img.onload = function() {
        modalImage.src = imageSrc;
        modalImage.style.display = 'block';
    };
    img.onerror = function() {
        modalImage.style.display = 'none';
        alert('이미지를 로딩할 수 없습니다.');
        closeProfileImageModal();
    };
    img.src = imageSrc;
    
    // ESC 키로 모달 닫기
    document.addEventListener('keydown', handleProfileModalEscKey);
}

function closeProfileImageModal() {
    const modal = document.getElementById('profileImageModal');
    modal.style.display = 'none';
    
    // ESC 키 이벤트 제거
    document.removeEventListener('keydown', handleProfileModalEscKey);
}

function handleProfileModalEscKey(event) {
    if (event.key === 'Escape') {
        closeProfileImageModal();
    }
}
</script>

<?php include SRC_PATH . '/views/templates/footer.php'; ?>