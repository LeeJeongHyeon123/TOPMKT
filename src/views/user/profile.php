<?php
/**
 * 프로필 페이지 뷰
 */

// 변수 초기값 설정
$isOwnProfile = $isOwnProfile ?? false;
$user = $user ?? [];
$stats = $stats ?? [];
$recentPosts = $recentPosts ?? [];
$recentComments = $recentComments ?? [];

// HTML 새니타이저 포함
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';

// 프로필 이미지 경로 설정
$profileImageUrl = '/assets/images/default-avatar.png';
if (!empty($user['profile_image_profile'])) {
    $profileImageUrl = $user['profile_image_profile'];
} elseif (!empty($user['profile_image_thumb'])) {
    $profileImageUrl = $user['profile_image_thumb'];
}

// 소셜 링크 파싱
$socialLinks = [];
if (!empty($user['social_links'])) {
    if (is_string($user['social_links'])) {
        $decoded = json_decode($user['social_links'], true);
        $socialLinks = $decoded && is_array($decoded) ? $decoded : [];
    } elseif (is_array($user['social_links'])) {
        $socialLinks = $user['social_links'];
    }
}

// 나이 계산
$age = null;
if (!empty($user['birth_date'])) {
    $birthDate = new DateTime($user['birth_date']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y;
}

// 가입일 포맷
$joinDate = '알 수 없음';
if (!empty($user['created_at'])) {
    $joinDate = date('Y년 m월 d일', strtotime($user['created_at']));
}

// 최근 접속일 포맷
$lastLogin = '정보 없음';
if (!empty($user['last_login'])) {
    $lastLoginTime = strtotime($user['last_login']);
    $timeDiff = time() - $lastLoginTime;
    
    if ($timeDiff < 60) {
        $lastLogin = '방금 전';
    } elseif ($timeDiff < 3600) {
        $lastLogin = floor($timeDiff / 60) . '분 전';
    } elseif ($timeDiff < 86400) {
        $lastLogin = floor($timeDiff / 3600) . '시간 전';
    } elseif ($timeDiff < 2592000) {
        $lastLogin = floor($timeDiff / 86400) . '일 전';
    } else {
        $lastLogin = date('Y-m-d', $lastLoginTime);
    }
}
?>

<style>
/* 프로필 페이지 전용 스타일 */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.profile-header-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 40px;
    margin-top: 60px;
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;
}

.profile-header-section::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: url('/assets/images/favicon.svg') no-repeat center;
    background-size: 100px;
    opacity: 0.1;
    transform: rotate(15deg);
}

.profile-main-info {
    display: flex;
    align-items: center;
    gap: 30px;
    position: relative;
    z-index: 2;
}

.profile-image-container {
    position: relative;
    flex-shrink: 0;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid rgba(255, 255, 255, 0.3);
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-image:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.profile-image-fallback {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 4px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    color: white;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-image-fallback:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.profile-details {
    flex: 1;
}

.profile-name {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.profile-role {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 15px;
    padding: 5px 15px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    display: inline-block;
}

.profile-meta {
    display: flex;
    gap: 20px;
    font-size: 0.9rem;
    opacity: 0.8;
}

.profile-actions {
    text-align: right;
    position: relative;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-edit:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}



/* 콘텐츠 그리드 */
.profile-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 30px;
}

.profile-main {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

/* 카드 공통 스타일 */
.profile-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.card-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* 자기소개 카드 */
.bio-content {
    color: #4a5568;
    line-height: 1.6;
    white-space: pre-wrap;
}

.bio-empty {
    color: #a0aec0;
    font-style: italic;
    text-align: center;
    padding: 20px;
}

/* 통계 카드 */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.stat-item {
    text-align: center;
    padding: 12px 8px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    min-width: 0; /* flexbox 내에서 축소 허용 */
}

.stat-value {
    display: block;
    font-size: 1.2rem;
    font-weight: 700;
    color: #667eea;
    margin-bottom: 4px;
    word-break: break-all; /* 긴 숫자 줄바꿈 */
}

.stat-label {
    font-size: 0.8rem;
    color: #718096;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* 기본 정보 카드 */
.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-icon {
    width: 20px;
    text-align: center;
    color: #667eea;
    margin-right: 12px;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.85rem;
    color: #718096;
    margin-bottom: 2px;
}

.info-value {
    font-size: 0.95rem;
    color: #2d3748;
    font-weight: 500;
}

/* 소셜 연결 카드 */
.social-connections-card {
    position: relative;
    overflow: visible;
}

/* 웹사이트 섹션 */
.website-section {
    margin-bottom: 20px;
}

.connection-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.connection-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: #667eea;
}

.connection-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.website-icon {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
}

.connection-content {
    flex: 1;
}

.connection-label {
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 4px;
    font-weight: 500;
}

.connection-link {
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.3s ease;
}

.connection-link:hover {
    color: #667eea;
}

.connection-link i {
    font-size: 0.8rem;
    opacity: 0.7;
}

/* 구분선 */
.section-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #e2e8f0, transparent);
    margin: 20px 0;
}

/* 소셜 그리드 */
.social-grid {
    display: grid;
    gap: 12px;
}

.social-connection-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px;
    background: white;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    text-decoration: none !important;
    color: #374151;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.social-connection-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--social-color);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: 0;
}

.social-connection-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    border-color: var(--social-color);
    text-decoration: none !important;
}

.social-connection-item:hover::before {
    opacity: 0.05;
}

.social-connection-item:hover .social-connection-icon {
    background: var(--social-color);
    color: var(--social-text-color);
    transform: scale(1.1);
}

.social-connection-item:hover .social-connection-name {
    color: var(--social-color);
}

.social-connection-item:hover .social-connection-arrow {
    transform: translateX(4px);
    color: var(--social-color);
}

.social-connection-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #6b7280;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.social-connection-content {
    flex: 1;
    position: relative;
    z-index: 1;
}

.social-connection-name {
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 2px;
    transition: color 0.3s ease;
}

.social-connection-action {
    font-size: 0.85rem;
    color: #9ca3af;
    font-weight: 500;
}

.social-connection-arrow {
    color: #d1d5db;
    font-size: 14px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

/* 빈 소셜 상태 */
.empty-social .empty-social-content {
    text-align: center;
    padding: 40px 20px;
}

.empty-social-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    color: #9ca3af;
    margin: 0 auto 20px;
}

.empty-social-text h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 12px;
}

.empty-social-text p {
    color: #6b7280;
    line-height: 1.6;
    margin-bottom: 24px;
}

.btn-add-social {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-add-social:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

/* 반응형 */
@media (max-width: 768px) {
    .connection-item {
        padding: 12px;
        gap: 12px;
    }
    
    .connection-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .social-connection-item {
        padding: 12px;
        gap: 12px;
    }
    
    .social-connection-icon {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .empty-social-content {
        padding: 30px 15px;
    }
    
    .empty-social-icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
    }
}

/* 최근 활동 */
.activity-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #e2e8f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-title {
    font-size: 0.95rem;
    font-weight: 500;
    color: #2d3748;
    margin-bottom: 5px;
    line-height: 1.4;
}

.activity-title a {
    color: #667eea;
    text-decoration: none;
}

.activity-title a:hover {
    text-decoration: underline;
}

.activity-meta {
    font-size: 0.8rem;
    color: #a0aec0;
    display: flex;
    gap: 15px;
}

.activity-empty {
    text-align: center;
    color: #a0aec0;
    font-style: italic;
    padding: 20px;
}

/* 이미지 모달 */
.image-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    backdrop-filter: blur(5px);
}

.image-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 90%;
    max-height: 90%;
}

.image-modal img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 30px;
    color: white;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
    z-index: 10001;
}

.modal-close:hover {
    opacity: 0.7;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .profile-container {
        padding: 15px;
    }
    
    .profile-header-section {
        padding: 25px 20px;
        margin-bottom: 20px;
    }
    
    .profile-main-info {
        flex-direction: column;
        text-align: center;
        gap: 20px;
    }
    
    .profile-name {
        font-size: 2rem;
    }
    
    .profile-meta {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .profile-content {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .profile-sidebar {
        order: -1;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .stat-item {
        padding: 8px 4px;
    }
    
    .stat-value {
        font-size: 1.2rem;
    }
    
    .social-links {
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .profile-image,
    .profile-image-fallback {
        width: 100px;
        height: 100px;
    }
    
    .profile-image-fallback {
        font-size: 2.5rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .profile-card {
        padding: 20px 15px;
    }
}
</style>

<div class="profile-container">
    <!-- 프로필 헤더 -->
    <div class="profile-header-section">
        <div class="profile-main-info">
            <div class="profile-image-container">
                <?php if (!empty($user['profile_image_profile'])): ?>
                    <img src="<?= htmlspecialchars($profileImageUrl) ?>" 
                         alt="<?= htmlspecialchars($user['nickname']) ?>님의 프로필 이미지" 
                         class="profile-image"
                         onclick="showImageModal('<?= htmlspecialchars($user['profile_image_original'] ?? $profileImageUrl) ?>')">
                <?php else: ?>
                    <div class="profile-image-fallback">
                        <?= mb_substr($user['nickname'] ?? '?', 0, 1) ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="profile-details">
                <h1 class="profile-name"><?= htmlspecialchars($user['nickname'] ?? '사용자') ?></h1>
                <div class="profile-meta">
                    <span>🗓️ 가입일: <?= $joinDate ?></span>
                    <?php if ($stats['join_days'] ?? 0 > 0): ?>
                        <span>⏰ 활동 <?= $stats['join_days'] ?>일째</span>
                    <?php endif; ?>
                    <?php if (!$isOwnProfile): ?>
                        <span>👀 최근 접속: <?= $lastLogin ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="profile-actions">
                <?php if ($isOwnProfile): ?>
                    <a href="/profile/edit" class="btn btn-secondary">
                        ✏️ 프로필 편집
                    </a>
                <?php endif; ?>
                
                <!-- 프로필 공유 버튼 -->
                <button class="btn btn-secondary" onclick="shareContent()">
                    🔗 공유하기
                </button>
            </div>
        </div>
    </div>
    
    <!-- 프로필 콘텐츠 -->
    <div class="profile-content">
        <!-- 메인 콘텐츠 -->
        <div class="profile-main">
            <!-- 자기소개 -->
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-user"></i> 자기소개
                </h2>
                <?php if (!empty($user['bio'])): ?>
                    <div class="bio-content"><?= $user['bio'] ?></div>
                <?php else: ?>
                    <div class="bio-empty">
                        <?= $isOwnProfile ? '자기소개를 작성해보세요!' : '아직 자기소개가 없습니다.' ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 최근 게시글 -->
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-newspaper"></i> 최근 게시글
                </h2>
                <?php if (!empty($recentPosts)): ?>
                    <ul class="activity-list">
                        <?php foreach ($recentPosts as $post): ?>
                            <li class="activity-item">
                                <div class="activity-title">
                                    <a href="/community/posts/<?= $post['id'] ?>">
                                        <?= htmlspecialchars($post['title']) ?>
                                    </a>
                                </div>
                                <div class="activity-meta">
                                    <span>📅 <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></span>
                                    <span>👁️ <?= number_format($post['view_count'] ?? 0) ?></span>
                                    <span>💬 <?= number_format($post['comment_count'] ?? 0) ?></span>
                                    <span>❤️ <?= number_format($post['like_count'] ?? 0) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="activity-empty">
                        <?= $isOwnProfile ? '아직 작성한 게시글이 없습니다. 첫 번째 글을 작성해보세요!' : '아직 작성한 게시글이 없습니다.' ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- 최근 댓글 -->
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-comments"></i> 최근 댓글
                </h2>
                <?php if (!empty($recentComments)): ?>
                    <ul class="activity-list">
                        <?php foreach ($recentComments as $comment): ?>
                            <li class="activity-item">
                                <div class="activity-title">
                                    <a href="/community/posts/<?= $comment['post_id'] ?>#comment-<?= $comment['id'] ?>">
                                        <?= htmlspecialchars($comment['post_title']) ?>
                                    </a>에 댓글
                                </div>
                                <div class="activity-meta">
                                    <span>📅 <?= date('Y-m-d H:i', strtotime($comment['created_at'])) ?></span>
                                    <span>💬 <?= htmlspecialchars(mb_substr(strip_tags($comment['content']), 0, 50)) ?>...</span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="activity-empty">
                        <?= $isOwnProfile ? '아직 작성한 댓글이 없습니다.' : '아직 작성한 댓글이 없습니다.' ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 사이드바 -->
        <div class="profile-sidebar">
            <!-- 활동 통계 -->
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-chart-bar"></i> 활동 통계
                </h2>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-value"><?= number_format($stats['post_count'] ?? 0) ?></span>
                        <span class="stat-label">게시글</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= number_format($stats['comment_count'] ?? 0) ?></span>
                        <span class="stat-label">댓글</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= number_format($stats['like_count'] ?? 0) ?></span>
                        <span class="stat-label">좋아요</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= number_format($stats['join_days'] ?? 0) ?></span>
                        <span class="stat-label">활동일</span>
                    </div>
                </div>
            </div>
            
            <!-- 기본 정보 -->
            <div class="profile-card">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i> 기본 정보
                </h2>
                <ul class="info-list">
                    <?php if (!empty($user['email'])): ?>
                        <li class="info-item">
                            <i class="info-icon fas fa-envelope"></i>
                            <div class="info-content">
                                <div class="info-label">이메일</div>
                                <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                            </div>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['birth_date']) && $age !== null): ?>
                        <li class="info-item">
                            <i class="info-icon fas fa-birthday-cake"></i>
                            <div class="info-content">
                                <div class="info-label">나이</div>
                                <div class="info-value"><?= $age ?>세</div>
                            </div>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (!empty($user['gender'])): ?>
                        <li class="info-item">
                            <i class="info-icon fas fa-venus-mars"></i>
                            <div class="info-content">
                                <div class="info-label">성별</div>
                                <div class="info-value">
                                    <?php
                                    $genderNames = ['M' => '남성', 'F' => '여성', 'OTHER' => '기타'];
                                    echo $genderNames[$user['gender']] ?? '알 수 없음';
                                    ?>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                    
                    <!-- website_url 필드 제거됨 (social_website 사용) -->
                </ul>
            </div>
            
            <!-- 소셜 링크 & 웹사이트 -->
            <?php 
            $hasSocialLinks = !empty($socialLinks);
            if ($hasSocialLinks): 
            ?>
                <div class="profile-card social-connections-card">
                    <h2 class="card-title">
                        <i class="fas fa-globe-americas"></i> 소셜 & 웹사이트
                    </h2>
                    
                    <!-- 소셜 링크 섹션 -->
                    <div class="social-section">
                        <div class="social-grid">
                                <?php
                                $socialConfigs = [
                                    'website' => [
                                        'icon' => 'fas fa-globe',
                                        'name' => '웹사이트',
                                        'color' => '#6366f1',
                                        'textColor' => '#fff'
                                    ],
                                    'kakao' => [
                                        'icon' => 'fas fa-comment',
                                        'name' => '카카오톡',
                                        'color' => '#FEE500',
                                        'textColor' => '#000'
                                    ],
                                    'instagram' => [
                                        'icon' => 'fab fa-instagram',
                                        'name' => '인스타그램',
                                        'color' => '#E4405F',
                                        'textColor' => '#fff'
                                    ],
                                    'facebook' => [
                                        'icon' => 'fab fa-facebook',
                                        'name' => '페이스북',
                                        'color' => '#1877F2',
                                        'textColor' => '#fff'
                                    ],
                                    'youtube' => [
                                        'icon' => 'fab fa-youtube',
                                        'name' => '유튜브',
                                        'color' => '#FF0000',
                                        'textColor' => '#fff'
                                    ],
                                    'tiktok' => [
                                        'icon' => 'fab fa-tiktok',
                                        'name' => '틱톡',
                                        'color' => '#000000',
                                        'textColor' => '#fff'
                                    ]
                                ];
                                
                                // 웹사이트를 가장 위에 표시하기 위한 순서 정의
                                $displayOrder = ['website', 'kakao', 'instagram', 'facebook', 'youtube', 'tiktok'];
                                
                                // 순서대로 소셜 링크 표시
                                foreach ($displayOrder as $platform):
                                    $url = $socialLinks[$platform] ?? '';
                                    if (!empty($url) && isset($socialConfigs[$platform])):
                                        $config = $socialConfigs[$platform];
                                ?>
                                    <a href="<?= htmlspecialchars($url) ?>" 
                                       target="_blank" 
                                       rel="noopener noreferrer" 
                                       class="social-connection-item"
                                       style="--social-color: <?= $config['color'] ?>; --social-text-color: <?= $config['textColor'] ?>;"
                                       title="<?= $config['name'] ?>에서 만나요">
                                        <div class="social-connection-icon">
                                            <i class="<?= $config['icon'] ?>"></i>
                                        </div>
                                        <div class="social-connection-content">
                                            <div class="social-connection-name"><?= $config['name'] ?></div>
                                            <div class="social-connection-action">방문하기</div>
                                        </div>
                                        <div class="social-connection-arrow">
                                            <i class="fas fa-chevron-right"></i>
                                        </div>
                                    </a>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- 소셜 링크가 없을 때 -->
                <?php if ($isOwnProfile): ?>
                    <div class="profile-card social-connections-card empty-social">
                        <h2 class="card-title">
                            <i class="fas fa-globe-americas"></i> 소셜 & 웹사이트
                        </h2>
                        <div class="empty-social-content">
                            <div class="empty-social-icon">
                                <i class="fas fa-share-alt"></i>
                            </div>
                            <div class="empty-social-text">
                                <h3>소셜 프로필을 연결해보세요</h3>
                                <p>인스타그램, 유튜브, 개인 웹사이트 등을<br>프로필에 추가하여 더 많은 사람들과 소통하세요.</p>
                            </div>
                            <a href="/profile/edit" class="btn-add-social">
                                <i class="fas fa-plus"></i> 소셜 링크 추가하기
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 이미지 확대 모달 -->
<div id="imageModal" class="image-modal" onclick="hideImageModal()">
    <span class="modal-close" onclick="hideImageModal()">&times;</span>
    <div class="image-modal-content">
        <img id="modalImage" src="" alt="프로필 이미지">
    </div>
</div>

<script>
// 이미지 모달 관련 함수
function showImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    modal.style.display = 'block';
    modalImg.src = imageSrc;
    document.body.style.overflow = 'hidden';
}

function hideImageModal() {
    const modal = document.getElementById('imageModal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
}

// 공유하기 기능 (강의 페이지와 동일)
function shareContent() {
    try {
        const profileTitle = <?= json_encode(($user['nickname'] ?? '사용자') . '님의 프로필 - 탑마케팅', JSON_UNESCAPED_UNICODE) ?>;
        const profileUrl = <?= json_encode('https://' . $_SERVER['HTTP_HOST'] . '/profile/' . urlencode($user['nickname'])) ?>;
        const profileDescription = <?= json_encode('탑마케팅에서 ' . ($user['nickname'] ?? '사용자') . '님의 프로필을 확인해보세요!', JSON_UNESCAPED_UNICODE) ?>;
        
        // Web Share API 지원 확인
        if (navigator.share) {
            navigator.share({
                title: profileTitle,
                text: profileDescription,
                url: profileUrl
            }).then(() => {
                console.log('공유 성공');
            }).catch((error) => {
                console.log('공유 실패:', error);
                fallbackShare(profileTitle, profileUrl);
            });
        } else {
            // 폴백: 클립보드 복사 또는 공유 옵션 표시
            fallbackShare(profileTitle, profileUrl);
        }
    } catch (error) {
        console.error('공유 기능 오류:', error);
        alert('공유 기능에 오류가 발생했습니다.');
    }
}

// 폴백 공유 기능 (클립보드 복사)
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

// 공유 모달 표시
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
        <h3 style="margin-bottom: 20px; color: #2d3748;">🔗 프로필 공유하기</h3>
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

// 클립보드 복사
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

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        hideImageModal();
    }
});

// 프로필 페이지 로드 완료 로그
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 프로필 페이지 로드 완료');
    console.log('👤 사용자:', '<?= htmlspecialchars($user['nickname'] ?? '') ?>');
    console.log('📊 통계:', <?= json_encode($stats) ?>);
});
</script>