<?php
/**
 * 커뮤니티 게시글 상세보기 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

// 게시글 정보가 없으면 404 처리
if (!isset($post) || !$post) {
    header('HTTP/1.1 404 Not Found');
    include SRC_PATH . '/views/templates/404.php';
    return;
}
?>

<style>
/* 게시글 상세보기 페이지 스타일 */
.detail-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.detail-navigation {
    margin-bottom: 20px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #718096;
}

.breadcrumb a {
    color: #4299e1;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.post-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    margin-bottom: 30px;
}

.post-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
}

.post-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 15px;
    line-height: 1.4;
    word-break: break-word;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 20px;
    font-size: 0.9rem;
    opacity: 0.9;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-content {
    padding: 30px;
}

.content-body {
    font-size: 1rem;
    line-height: 1.8;
    color: #2d3748;
    word-break: break-word;
    white-space: pre-wrap;
}

.post-footer {
    padding: 20px 30px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.post-stats {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 15px;
    font-size: 0.9rem;
    color: #718096;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.post-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #4299e1;
    color: white;
}

.btn-primary:hover {
    background: #3182ce;
    transform: translateY(-1px);
}

.btn-success {
    background: #48bb78;
    color: white;
}

.btn-success:hover {
    background: #38a169;
}

.btn-warning {
    background: #ed8936;
    color: white;
}

.btn-warning:hover {
    background: #dd6b20;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.author-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 30px;
    background: #f0fff4;
    border-top: 1px solid #e2e8f0;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}

.author-details {
    flex: 1;
}

.author-name {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 4px;
}

.author-role {
    font-size: 0.8rem;
    color: #718096;
}

.comments-section {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.comments-header {
    padding: 20px 30px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 600;
    color: #2d3748;
}

.comments-list {
    padding: 20px 30px;
}

.comment-placeholder {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.comment-placeholder i {
    font-size: 2rem;
    margin-bottom: 15px;
    color: #cbd5e0;
}

.back-to-list {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #667eea;
    color: white;
    border: none;
    border-radius: 50px;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.back-to-list:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(102, 126, 234, 0.5);
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .detail-container {
        padding: 15px;
    }
    
    .post-header {
        padding: 20px;
    }
    
    .post-title {
        font-size: 1.4rem;
    }
    
    .post-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .post-content {
        padding: 20px;
    }
    
    .post-footer {
        padding: 15px 20px;
    }
    
    .post-actions {
        justify-content: center;
    }
    
    .author-info {
        padding: 15px 20px;
    }
    
    .back-to-list {
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        font-size: 1rem;
    }
}

/* 다크모드 대응 */
@media (prefers-color-scheme: dark) {
    .post-container,
    .comments-section {
        background: #2d3748;
        border-color: #4a5568;
    }
    
    .content-body {
        color: #e2e8f0;
    }
    
    .post-footer,
    .comments-header {
        background: #4a5568;
        border-color: #718096;
    }
    
    .author-info {
        background: #2d5016;
    }
}

/* 애니메이션 */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.post-container {
    animation: fadeInUp 0.6s ease-out;
}

.comments-section {
    animation: fadeInUp 0.6s ease-out 0.2s both;
}
</style>

<div class="detail-container">
    <!-- 네비게이션 -->
    <div class="detail-navigation">
        <div class="breadcrumb">
            <a href="/community">📋 커뮤니티</a>
            <span>›</span>
            <span>게시글 보기</span>
        </div>
    </div>
    
    <!-- 게시글 컨테이너 -->
    <div class="post-container">
        <!-- 게시글 헤더 -->
        <div class="post-header">
            <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post-meta">
                <div class="meta-item">
                    👤 <strong><?= htmlspecialchars($post['author_name'] ?? $post['nickname'] ?? '익명') ?></strong>
                </div>
                <div class="meta-item">
                    📅 <?= date('Y년 m월 d일 H:i', strtotime($post['created_at'])) ?>
                </div>
                <?php if (isset($post['updated_at']) && $post['updated_at'] !== $post['created_at']): ?>
                    <div class="meta-item">
                        ✏️ <?= date('Y-m-d H:i', strtotime($post['updated_at'])) ?> 수정됨
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- 게시글 내용 -->
        <div class="post-content">
            <div class="content-body">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
        </div>
        
        <!-- 게시글 푸터 -->
        <div class="post-footer">
            <!-- 통계 -->
            <div class="post-stats">
                <div class="stat-item">
                    👁️ 조회 <?= number_format($post['view_count'] ?? 0) ?>
                </div>
                <div class="stat-item">
                    💬 댓글 <?= number_format($post['comment_count'] ?? 0) ?>
                </div>
                <div class="stat-item">
                    ❤️ 좋아요 <?= number_format($post['like_count'] ?? 0) ?>
                </div>
            </div>
            
            <!-- 액션 버튼들 -->
            <div class="post-actions">
                <?php if ($isLoggedIn): ?>
                    <button class="btn btn-primary" id="likeBtn">
                        ❤️ 좋아요
                    </button>
                    <button class="btn btn-success" id="shareBtn">
                        📤 공유
                    </button>
                <?php endif; ?>
                
                <?php if ($isOwner): ?>
                    <a href="/community/posts/<?= $post['id'] ?>/edit" class="btn btn-warning">
                        ✏️ 수정
                    </a>
                    <button class="btn btn-danger" id="deleteBtn">
                        🗑️ 삭제
                    </button>
                <?php endif; ?>
                
                <a href="/community" class="btn btn-secondary">
                    📋 목록으로
                </a>
            </div>
        </div>
        
        <!-- 작성자 정보 -->
        <div class="author-info">
            <div class="author-avatar">
                <?= mb_substr($post['author_name'] ?? $post['nickname'] ?? '익명', 0, 1) ?>
            </div>
            <div class="author-details">
                <div class="author-name"><?= htmlspecialchars($post['author_name'] ?? $post['nickname'] ?? '익명') ?></div>
                <div class="author-role">
                    <?php
                    $role = $post['role'] ?? 'GENERAL';
                    switch ($role) {
                        case 'SUPER_ADMIN':
                        case 'ADMIN':
                            echo '🛡️ 관리자';
                            break;
                        case 'PREMIUM':
                            echo '⭐ 프리미엄 회원';
                            break;
                        case 'COMPANY':
                            echo '🏢 기업 회원';
                            break;
                        default:
                            echo '👤 일반 회원';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 댓글 섹션 (향후 구현) -->
    <div class="comments-section">
        <div class="comments-header">
            💬 댓글 <?= number_format($post['comment_count'] ?? 0) ?>개
        </div>
        <div class="comments-list">
            <div class="comment-placeholder">
                <div>💭</div>
                <p>댓글 기능은 곧 추가될 예정입니다.</p>
            </div>
        </div>
    </div>
</div>

<!-- 목록으로 돌아가기 플로팅 버튼 -->
<button class="back-to-list" onclick="location.href='/community'" title="목록으로 돌아가기">
    📋
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 게시글 상세보기 페이지 로드 완료');
    console.log('📝 게시글 ID:', <?= $post['id'] ?>);
    console.log('👤 작성자:', '<?= htmlspecialchars($post['author_name'] ?? $post['nickname'] ?? '익명') ?>');
    console.log('🔑 소유자 여부:', <?= $isOwner ? 'true' : 'false' ?>);
    
    const isLoggedIn = <?= $isLoggedIn ? 'true' : 'false' ?>;
    const isOwner = <?= $isOwner ? 'true' : 'false' ?>;
    const postId = <?= $post['id'] ?>;
    
    // 좋아요 버튼 처리
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn && isLoggedIn) {
        likeBtn.addEventListener('click', function() {
            // 향후 좋아요 기능 구현
            alert('좋아요 기능은 곧 추가될 예정입니다! 💝');
        });
    }
    
    // 공유 버튼 처리
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            if (navigator.share) {
                // Web Share API 사용 (모바일 등)
                navigator.share({
                    title: '<?= htmlspecialchars($post['title']) ?>',
                    text: '탑마케팅 커뮤니티의 게시글을 확인해보세요!',
                    url: window.location.href
                }).catch(console.error);
            } else {
                // 클립보드에 URL 복사
                navigator.clipboard.writeText(window.location.href).then(function() {
                    alert('게시글 링크가 클립보드에 복사되었습니다! 📋');
                }).catch(function() {
                    // 클립보드 접근 실패 시 대체 방법
                    const textArea = document.createElement('textarea');
                    textArea.value = window.location.href;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    alert('게시글 링크가 클립보드에 복사되었습니다! 📋');
                });
            }
        });
    }
    
    // 삭제 버튼 처리
    const deleteBtn = document.getElementById('deleteBtn');
    if (deleteBtn && isOwner) {
        deleteBtn.addEventListener('click', function() {
            if (!confirm('정말로 이 게시글을 삭제하시겠습니까?\n\n삭제된 게시글은 복구할 수 없습니다.')) {
                return;
            }
            
            // 삭제 확인 재요청
            if (!confirm('정말로 삭제하시겠습니까?\n\n이 작업은 되돌릴 수 없습니다.')) {
                return;
            }
            
            // 로딩 표시
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '🔄 삭제 중...';
            
            // CSRF 토큰 가져오기
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            fetch(`/community/posts/${postId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    csrf_token: csrfToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = data.data?.redirectUrl || '/community';
                } else {
                    alert(data.message || '삭제 중 오류가 발생했습니다.');
                    deleteBtn.disabled = false;
                    deleteBtn.innerHTML = '🗑️ 삭제';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('네트워크 오류가 발생했습니다. 다시 시도해주세요.');
                deleteBtn.disabled = false;
                deleteBtn.innerHTML = '🗑️ 삭제';
            });
        });
    }
    
    // 키보드 단축키
    document.addEventListener('keydown', function(e) {
        // ESC: 목록으로 돌아가기
        if (e.key === 'Escape') {
            if (confirm('목록으로 돌아가시겠습니까?')) {
                window.location.href = '/community';
            }
        }
        
        // E: 수정 (소유자만)
        if (e.key === 'e' || e.key === 'E') {
            if (isOwner && !e.ctrlKey && !e.metaKey && !e.altKey) {
                const activeElement = document.activeElement;
                if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA') {
                    window.location.href = `/community/posts/${postId}/edit`;
                }
            }
        }
    });
    
    // 조회수 증가 (Ajax로 처리, 실제 구현 시)
    // setTimeout(function() {
    //     fetch(`/community/posts/${postId}/view`, { method: 'POST' });
    // }, 2000);
    
    // 스크롤 시 플로팅 버튼 표시/숨김
    const backToListBtn = document.querySelector('.back-to-list');
    let lastScrollTop = 0;
    
    window.addEventListener('scroll', function() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > 300) {
            backToListBtn.style.display = 'flex';
        } else {
            backToListBtn.style.display = 'none';
        }
        
        lastScrollTop = scrollTop;
    });
});
</script>