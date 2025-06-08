<?php
/**
 * 커뮤니티 게시판 메인 페이지 (게시글 목록)
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();
?>

<style>
/* 커뮤니티 게시판 스타일 */
.community-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.community-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    text-align: center;
    margin-top: 60px;
    margin-bottom: 30px;
    border-radius: 12px;
}

.community-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.community-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.board-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 15px;
}

.search-form {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    padding: 10px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    width: 250px;
    transition: border-color 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.btn-write {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    font-weight: 700;
}

.btn-write:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
}

.board-stats {
    background: #f8fafc;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid #667eea;
}

.stats-text {
    color: #4a5568;
    font-size: 14px;
    margin: 0;
}

.post-list {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.post-item {
    padding: 20px;
    border-bottom: 1px solid #e2e8f0;
    transition: background-color 0.2s ease;
    cursor: pointer;
}

.post-item:hover {
    background-color: #f8fafc;
}

.post-item:last-child {
    border-bottom: none;
}

.post-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    line-height: 1.4;
}

.post-title:hover {
    color: #667eea;
}

.post-meta {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 0.9rem;
    color: #718096;
    margin-bottom: 10px;
}

.post-author {
    font-weight: 600;
    color: #4a5568;
}

.post-date {
    color: #a0aec0;
}

.post-content-preview {
    color: #718096;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-top: 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.post-stats {
    display: flex;
    gap: 15px;
    font-size: 0.85rem;
    color: #a0aec0;
    margin-top: 10px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 30px;
}

.page-link {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    background: white;
    color: #4a5568;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.page-link:hover {
    background: #f8fafc;
    border-color: #667eea;
    color: #667eea;
}

.page-link.active {
    background: #667eea;
    border-color: #667eea;
    color: white;
}

.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #cbd5e0;
}

.empty-state h3 {
    font-size: 1.2rem;
    margin-bottom: 10px;
    color: #4a5568;
}

.empty-state p {
    font-size: 0.9rem;
    margin-bottom: 20px;
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .community-container {
        padding: 15px;
    }
    
    .community-header {
        padding: 30px 20px;
    }
    
    .community-header h1 {
        font-size: 2rem;
    }
    
    .board-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-form {
        justify-content: center;
    }
    
    .search-input {
        width: 100%;
        max-width: 300px;
    }
    
    .post-item {
        padding: 15px;
    }
    
    .post-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .pagination {
        flex-wrap: wrap;
    }
}

/* 다크모드 대응 */
@media (prefers-color-scheme: dark) {
    .post-list {
        background: #2d3748;
        border-color: #4a5568;
    }
    
    .post-item:hover {
        background-color: #4a5568;
    }
    
    .post-title {
        color: #e2e8f0;
    }
}
</style>

<div class="community-container">
    <!-- 헤더 섹션 -->
    <div class="community-header">
        <h1>💬 커뮤니티 게시판</h1>
        <p>탑마케팅 커뮤니티에서 정보를 공유하고 함께 성장하세요</p>
    </div>
    
    <!-- 게시판 컨트롤 영역 -->
    <div class="board-controls">
        <!-- 검색 폼 -->
        <form method="GET" action="/community" class="search-form">
            <input type="text" 
                   name="search" 
                   value="<?= htmlspecialchars($search ?? '') ?>" 
                   placeholder="제목, 내용으로 검색..."
                   class="search-input">
            <button type="submit" class="btn btn-secondary">
                🔍 검색
            </button>
            <?php if (!empty($search)): ?>
                <a href="/community" class="btn btn-secondary">
                    ✖️ 검색 해제
                </a>
            <?php endif; ?>
        </form>
        
        <!-- 글쓰기 버튼 -->
        <?php if ($isLoggedIn): ?>
            <a href="/community/write" class="btn btn-write">
                ✍️ 글쓰기
            </a>
        <?php else: ?>
            <a href="/auth/login?redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn btn-primary">
                🔑 로그인 후 글쓰기
            </a>
        <?php endif; ?>
    </div>
    
    <!-- 게시판 통계 -->
    <div class="board-stats">
        <p class="stats-text">
            📊 총 <strong><?= number_format($totalCount) ?></strong>개의 게시글이 있습니다
            <?php if (!empty($search)): ?>
                (검색어: <strong><?= htmlspecialchars($search) ?></strong>)
            <?php endif; ?>
        </p>
    </div>
    
    <!-- 게시글 목록 -->
    <?php if (!empty($posts)): ?>
        <div class="post-list">
            <?php foreach ($posts as $post): ?>
                <div class="post-item" onclick="location.href='/community/posts/<?= $post['id'] ?>'">
                    <div class="post-title">
                        <?= htmlspecialchars($post['title']) ?>
                        <?php if ($post['comment_count'] > 0): ?>
                            <span style="color: #e53e3e; font-size: 0.9rem;">[<?= $post['comment_count'] ?>]</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="post-meta">
                        <span class="post-author">👤 <?= htmlspecialchars($post['author_name'] ?? $post['nickname'] ?? '익명') ?></span>
                        <span class="post-date">📅 <?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></span>
                    </div>
                    
                    <div class="post-content-preview">
                        <?= htmlspecialchars(HtmlSanitizerHelper::htmlToPlainText($post['content'], 150)) ?>
                    </div>
                    
                    <div class="post-stats">
                        <span class="stat-item">
                            👁️ <?= number_format($post['view_count'] ?? 0) ?>
                        </span>
                        <span class="stat-item">
                            💬 <?= number_format($post['comment_count'] ?? 0) ?>
                        </span>
                        <span class="stat-item">
                            ❤️ <?= number_format($post['like_count'] ?? 0) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- 페이지네이션 -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <!-- 이전 페이지 -->
                <?php if ($hasPrevPage): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="page-link">
                        ← 이전
                    </a>
                <?php else: ?>
                    <span class="page-link disabled">← 이전</span>
                <?php endif; ?>
                
                <!-- 페이지 번호들 -->
                <?php
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                if ($startPage > 1): ?>
                    <a href="?page=1<?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="page-link">1</a>
                    <?php if ($startPage > 2): ?>
                        <span class="page-link disabled">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="page-link <?= $i === $currentPage ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="page-link disabled">...</span>
                    <?php endif; ?>
                    <a href="?page=<?= $totalPages ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="page-link"><?= $totalPages ?></a>
                <?php endif; ?>
                
                <!-- 다음 페이지 -->
                <?php if ($hasNextPage): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                       class="page-link">
                        다음 →
                    </a>
                <?php else: ?>
                    <span class="page-link disabled">다음 →</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <!-- 빈 상태 -->
        <div class="post-list">
            <div class="empty-state">
                <i>📝</i>
                <h3>
                    <?php if (!empty($search)): ?>
                        검색 결과가 없습니다
                    <?php else: ?>
                        첫 번째 게시글을 작성해보세요!
                    <?php endif; ?>
                </h3>
                <p>
                    <?php if (!empty($search)): ?>
                        다른 키워드로 검색해보거나 새로운 글을 작성해보세요.
                    <?php else: ?>
                        탑마케팅 커뮤니티의 첫 번째 이야기를 시작해보세요.
                    <?php endif; ?>
                </p>
                <?php if ($isLoggedIn): ?>
                    <a href="/community/write" class="btn btn-primary">
                        ✍️ 글쓰기
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// 게시글 클릭 이벤트 (이미 onclick으로 처리되어 있지만 추가 기능용)
document.addEventListener('DOMContentLoaded', function() {
    console.log('📋 커뮤니티 게시판 로드 완료');
    console.log('📊 게시글 수:', <?= count($posts ?? []) ?>);
    console.log('📄 현재 페이지:', <?= isset($currentPage) ? $currentPage : 1 ?>);
    console.log('📄 총 페이지:', <?= isset($totalPages) ? $totalPages : 1 ?>);
    
    // 검색 폼 엔터키 처리
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.target.closest('form').submit();
            }
        });
    }
    
    // 게시글 항목 호버 효과 개선
    const postItems = document.querySelectorAll('.post-item');
    postItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(4px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // 커뮤니티 관련 전역 객체 정의 (오류 방지용)
    if (typeof window.community === 'undefined') {
        window.community = {
            initialized: true,
            currentPage: <?= isset($currentPage) ? $currentPage : 1 ?>,
            totalPages: <?= isset($totalPages) ? $totalPages : 1 ?>,
            postCount: <?= count($posts ?? []) ?>,
            searchQuery: '<?= addslashes($search ?? '') ?>',
            hasNextPage: <?= isset($hasNextPage) ? ($hasNextPage ? 'true' : 'false') : 'false' ?>,
            hasPrevPage: <?= isset($hasPrevPage) ? ($hasPrevPage ? 'true' : 'false') : 'false' ?>
        };
    }
    
    // 추가 안전장치: 필수 DOM 요소 존재 확인
    const requiredElements = [
        '.search-input',
        '.post-item',
        '.pagination'
    ];
    
    requiredElements.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        if (elements.length === 0) {
            console.log(`🚀 요소 없음: ${selector} (정상 - 빈 게시판일 수 있음)`);
        }
    });
    
    // 전역 오류 핸들러 (커뮤니티 페이지 전용)
    window.addEventListener('error', function(event) {
        if (event.filename && event.filename.includes('community')) {
            console.warn('🚀 커뮤니티 페이지 JavaScript 오류 감지:', {
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno
            });
            
            // 사용자에게는 오류를 표시하지 않고 조용히 처리
            event.preventDefault();
        }
    });
});
</script>