/**
 * 탑마케팅 메인 자바스크립트 파일
 */

document.addEventListener('DOMContentLoaded', function() {
    // 폼 메서드 재정의 (PUT, DELETE 요청 처리)
    setupFormMethodOverride();
    
    // 댓글 수정 기능
    setupCommentEdit();
});

/**
 * PUT, DELETE 등 HTML 폼에서 지원하지 않는 HTTP 메서드를 처리하기 위한 설정
 */
function setupFormMethodOverride() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) {
                const method = methodInput.value.toUpperCase();
                if (method === 'PUT' || method === 'DELETE') {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    const url = form.getAttribute('action');
                    
                    fetch(url, {
                        method: method,
                        body: new URLSearchParams(formData),
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.ok) {
                            if (method === 'DELETE') {
                                window.location.href = '/posts';
                            } else {
                                window.location.reload();
                            }
                        } else {
                            throw new Error('서버 응답 오류: ' + response.status);
                        }
                    })
                    .catch(error => {
                        console.error('요청 실패:', error);
                        alert('요청을 처리하는 중에 오류가 발생했습니다.');
                    });
                }
            }
        });
    });
}

/**
 * 댓글 수정 기능 설정
 */
function setupCommentEdit() {
    document.querySelectorAll('.btn-edit-comment').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-id');
            const commentItem = this.closest('.comment-item');
            const commentContent = commentItem.querySelector('.comment-content').textContent.trim();
            
            // 기존 댓글 내용 숨기기
            commentItem.querySelector('.comment-content').style.display = 'none';
            commentItem.querySelector('.comment-actions').style.display = 'none';
            
            // 수정 폼 생성
            const editForm = document.createElement('form');
            editForm.className = 'comment-edit-form';
            editForm.innerHTML = `
                <div class="form-group">
                    <textarea name="content" required>${commentContent}</textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">수정</button>
                    <button type="button" class="btn btn-secondary btn-cancel">취소</button>
                </div>
                <input type="hidden" name="_method" value="PUT">
            `;
            
            commentItem.appendChild(editForm);
            
            // 취소 버튼 이벤트
            editForm.querySelector('.btn-cancel').addEventListener('click', function() {
                editForm.remove();
                commentItem.querySelector('.comment-content').style.display = 'block';
                commentItem.querySelector('.comment-actions').style.display = 'block';
            });
            
            // 폼 제출 이벤트
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(editForm);
                
                fetch(`/comments/${commentId}`, {
                    method: 'PUT',
                    body: new URLSearchParams(formData),
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error('서버 응답 오류: ' + response.status);
                })
                .then(data => {
                    // 성공 시 댓글 내용 업데이트 및 폼 제거
                    commentItem.querySelector('.comment-content').textContent = formData.get('content');
                    editForm.remove();
                    
                    commentItem.querySelector('.comment-content').style.display = 'block';
                    commentItem.querySelector('.comment-actions').style.display = 'block';
                })
                .catch(error => {
                    console.error('요청 실패:', error);
                    alert('댓글을 수정하는 중에 오류가 발생했습니다.');
                });
            });
        });
    });
} 