<?php include SRC_PATH . '/views/templates/header.php'; ?>

<div class="profile-container">
    <h2>사용자 프로필</h2>
    
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <!-- 실제 구현에서는 사용자 프로필 이미지 표시 -->
                <div class="avatar-placeholder">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>
            </div>
            <div class="profile-info">
                <h3><?= $user['name'] ?></h3>
                <p class="profile-role"><?= $user['role'] ?></p>
            </div>
        </div>
        
        <div class="profile-body">
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-value">0</span>
                    <span class="stat-label">게시글</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">0</span>
                    <span class="stat-label">댓글</span>
                </div>
                <div class="stat-item">
                    <span class="stat-value">0</span>
                    <span class="stat-label">좋아요</span>
                </div>
            </div>
            
            <div class="profile-actions">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                    <a href="/users/<?= $user['id'] ?>/edit" class="btn btn-secondary">프로필 수정</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 사용자 게시글 목록 (실제 구현에서는 DB에서 가져옴) -->
    <div class="profile-posts">
        <h3>최근 게시글</h3>
        <div class="posts-empty">
            <p>아직 작성한 게시글이 없습니다.</p>
        </div>
    </div>
</div>

<?php include SRC_PATH . '/views/templates/footer.php'; ?> 