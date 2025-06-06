<?php
/**
 * 커뮤니티 게시글 작성/수정 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

// 로그인하지 않은 경우 로그인 페이지로 리다이렉트
if (!$isLoggedIn) {
    header('Location: /auth/login');
    exit;
}

$isEdit = isset($action) && $action === 'edit';
$pageTitle = $isEdit ? '게시글 수정' : '새 게시글 작성';
$submitText = $isEdit ? '수정하기' : '작성하기';
?>

<style>
/* 게시글 작성/수정 페이지 스타일 */
.write-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.write-header {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    padding: 30px;
    text-align: center;
    margin-bottom: 30px;
    border-radius: 12px;
}

.write-header h1 {
    font-size: 2rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.write-header p {
    font-size: 1rem;
    opacity: 0.9;
    margin: 0;
}

.write-form {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2d3748;
    font-size: 14px;
}

.form-label.required::after {
    content: ' *';
    color: #e53e3e;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 16px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #48bb78;
    box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 300px;
    font-family: inherit;
    line-height: 1.6;
}

.char-counter {
    font-size: 12px;
    color: #718096;
    text-align: right;
    margin-top: 5px;
}

.char-counter.warning {
    color: #d69e2e;
}

.char-counter.error {
    color: #e53e3e;
}

.form-buttons {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    min-width: 120px;
    justify-content: center;
}

.btn-primary {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
}

.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

.form-tips {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.form-tips h4 {
    color: #22543d;
    margin: 0 0 10px 0;
    font-size: 14px;
}

.form-tips ul {
    margin: 0;
    padding-left: 20px;
    color: #276749;
    font-size: 13px;
}

.form-tips li {
    margin-bottom: 4px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-content {
    background: white;
    padding: 30px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #e2e8f0;
    border-top: 4px solid #48bb78;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .write-container {
        padding: 15px;
    }
    
    .write-header {
        padding: 25px 20px;
    }
    
    .write-header h1 {
        font-size: 1.5rem;
    }
    
    .write-form {
        padding: 20px;
    }
    
    .form-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn {
        width: 100%;
        max-width: 200px;
    }
}

/* 다크모드 대응 */
@media (prefers-color-scheme: dark) {
    .write-form {
        background: #2d3748;
        border-color: #4a5568;
    }
    
    .form-label {
        color: #e2e8f0;
    }
    
    .form-input {
        background: #4a5568;
        border-color: #718096;
        color: #e2e8f0;
    }
    
    .form-input:focus {
        border-color: #48bb78;
        background: #4a5568;
    }
}
</style>

<div class="write-container">
    <!-- 헤더 섹션 -->
    <div class="write-header">
        <h1><?= $isEdit ? '📝 게시글 수정' : '✍️ 새 게시글 작성' ?></h1>
        <p><?= $isEdit ? '게시글을 수정해주세요' : '커뮤니티에 새로운 이야기를 공유해주세요' ?></p>
    </div>
    
    <!-- 작성 팁 -->
    <div class="form-tips">
        <h4>💡 게시글 작성 팁</h4>
        <ul>
            <li>제목은 간결하고 명확하게 작성해주세요 (200자 이내)</li>
            <li>내용은 구체적이고 유용한 정보를 포함해주세요 (10,000자 이내)</li>
            <li>예의를 지키고 타인을 배려하는 글을 작성해주세요</li>
            <li>스팸성 내용이나 광고는 삼가해주세요</li>
        </ul>
    </div>
    
    <!-- 게시글 작성 폼 -->
    <form id="writeForm" class="write-form">
        <!-- CSRF 토큰 -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
        
        <!-- 제목 입력 -->
        <div class="form-group">
            <label for="title" class="form-label required">제목</label>
            <input type="text" 
                   id="title" 
                   name="title" 
                   class="form-input" 
                   placeholder="게시글 제목을 입력해주세요"
                   value="<?= isset($post) ? htmlspecialchars($post['title']) : '' ?>"
                   maxlength="200"
                   required>
            <div id="titleCounter" class="char-counter">0 / 200</div>
        </div>
        
        <!-- 내용 입력 -->
        <div class="form-group">
            <label for="content" class="form-label required">내용</label>
            <textarea id="content" 
                      name="content" 
                      class="form-input form-textarea" 
                      placeholder="게시글 내용을 입력해주세요&#10;&#10;• 구체적이고 유용한 정보를 공유해주세요&#10;• 예의를 지키고 타인을 배려하는 글을 작성해주세요&#10;• 개인정보나 민감한 정보는 포함하지 마세요"
                      maxlength="10000"
                      required><?= isset($post) ? htmlspecialchars($post['content']) : '' ?></textarea>
            <div id="contentCounter" class="char-counter">0 / 10,000</div>
        </div>
        
        <!-- 버튼 영역 -->
        <div class="form-buttons">
            <button type="submit" id="submitBtn" class="btn btn-primary">
                <span id="submitText"><?= $submitText ?></span>
            </button>
            <a href="/community" class="btn btn-secondary">
                ❌ 취소
            </a>
            <?php if ($isEdit): ?>
                <button type="button" id="deleteBtn" class="btn btn-danger">
                    🗑️ 삭제
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- 로딩 오버레이 -->
<div id="loadingOverlay" class="loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <p><?= $isEdit ? '게시글을 수정하고 있습니다...' : '게시글을 작성하고 있습니다...' ?></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('✍️ 게시글 작성 페이지 로드 완료');
    
    const form = document.getElementById('writeForm');
    const titleInput = document.getElementById('title');
    const contentTextarea = document.getElementById('content');
    const titleCounter = document.getElementById('titleCounter');
    const contentCounter = document.getElementById('contentCounter');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingOverlay = document.getElementById('loadingOverlay');
    const deleteBtn = document.getElementById('deleteBtn');
    
    const isEdit = <?= $isEdit ? 'true' : 'false' ?>;
    const postId = <?= isset($post) ? $post['id'] : 'null' ?>;
    
    // 문자 수 카운터 업데이트
    function updateCharCounter(input, counter, maxLength) {
        const currentLength = input.value.length;
        counter.textContent = `${currentLength.toLocaleString()} / ${maxLength.toLocaleString()}`;
        
        // 경고 및 오류 상태 표시
        counter.className = 'char-counter';
        if (currentLength > maxLength * 0.9) {
            counter.classList.add('warning');
        }
        if (currentLength >= maxLength) {
            counter.classList.add('error');
        }
    }
    
    // 초기 문자 수 카운터 설정
    updateCharCounter(titleInput, titleCounter, 200);
    updateCharCounter(contentTextarea, contentCounter, 10000);
    
    // 실시간 문자 수 업데이트
    titleInput.addEventListener('input', function() {
        updateCharCounter(this, titleCounter, 200);
    });
    
    contentTextarea.addEventListener('input', function() {
        updateCharCounter(this, contentCounter, 10000);
    });
    
    // 폼 제출 처리
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const title = titleInput.value.trim();
        const content = contentTextarea.value.trim();
        
        // 유효성 검사
        if (!title) {
            alert('제목을 입력해주세요.');
            titleInput.focus();
            return;
        }
        
        if (title.length > 200) {
            alert('제목은 200자 이내로 입력해주세요.');
            titleInput.focus();
            return;
        }
        
        if (!content) {
            alert('내용을 입력해주세요.');
            contentTextarea.focus();
            return;
        }
        
        if (content.length > 10000) {
            alert('내용은 10,000자 이내로 입력해주세요.');
            contentTextarea.focus();
            return;
        }
        
        // 로딩 표시
        showLoading();
        
        // 폼 데이터 준비
        const formData = new FormData();
        formData.append('title', title);
        formData.append('content', content);
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        
        // API 요청
        const url = isEdit ? `/community/posts/${postId}` : '/community/posts';
        const method = 'POST';
        
        fetch(url, {
            method: method,
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            
            if (data.success) {
                alert(data.message);
                if (data.data && data.data.redirectUrl) {
                    window.location.href = data.data.redirectUrl;
                } else {
                    window.location.href = '/community';
                }
            } else {
                alert(data.message || '오류가 발생했습니다.');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('네트워크 오류가 발생했습니다. 다시 시도해주세요.');
        });
    });
    
    // 삭제 버튼 처리 (수정 페이지에서만)
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (!confirm('정말로 이 게시글을 삭제하시겠습니까?\n삭제된 게시글은 복구할 수 없습니다.')) {
                return;
            }
            
            showLoading();
            
            fetch(`/community/posts/${postId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    csrf_token: document.querySelector('input[name="csrf_token"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                
                if (data.success) {
                    alert(data.message);
                    window.location.href = data.data.redirectUrl || '/community';
                } else {
                    alert(data.message || '삭제 중 오류가 발생했습니다.');
                }
            })
            .catch(error => {
                hideLoading();
                console.error('Error:', error);
                alert('네트워크 오류가 발생했습니다. 다시 시도해주세요.');
            });
        });
    }
    
    // 로딩 표시/숨김 함수
    function showLoading() {
        loadingOverlay.style.display = 'flex';
        submitBtn.disabled = true;
        if (deleteBtn) deleteBtn.disabled = true;
    }
    
    function hideLoading() {
        loadingOverlay.style.display = 'none';
        submitBtn.disabled = false;
        if (deleteBtn) deleteBtn.disabled = false;
    }
    
    // 페이지 이탈 시 경고 (내용이 입력된 경우)
    let isSubmitting = false;
    
    form.addEventListener('submit', function() {
        isSubmitting = true;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (!isSubmitting && (titleInput.value.trim() || contentTextarea.value.trim())) {
            const message = '작성 중인 내용이 있습니다. 페이지를 벗어나시겠습니까?';
            e.returnValue = message;
            return message;
        }
    });
    
    // 자동 저장 기능 (나중에 구현)
    // setInterval(function() {
    //     const title = titleInput.value.trim();
    //     const content = contentTextarea.value.trim();
    //     if (title || content) {
    //         // 임시 저장 로직
    //         console.log('자동 저장 중...');
    //     }
    // }, 30000); // 30초마다
});
</script>