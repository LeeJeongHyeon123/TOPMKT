<?php
/**
 * 프로필 편집 페이지 뷰
 */

// 변수 초기값 설정
$user = $user ?? [];

// 소셜 링크 파싱
$socialLinks = [];
if (!empty($user['social_links']) && is_array($user['social_links'])) {
    $socialLinks = $user['social_links'];
}

// 프로필 이미지 경로 설정
$profileImageUrl = '/assets/images/default-avatar.png';
if (!empty($user['profile_image_profile'])) {
    $profileImageUrl = $user['profile_image_profile'];
} elseif (!empty($user['profile_image_thumb'])) {
    $profileImageUrl = $user['profile_image_thumb'];
}

// CSRF 토큰 생성
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Additional CSS for rich text editor and image cropping -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<style>
/* 프로필 편집 페이지 전용 스타일 */
.edit-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.edit-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px;
    padding: 30px;
    margin-top: 60px;
    margin-bottom: 30px;
    text-align: center;
}

.edit-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.edit-subtitle {
    opacity: 0.9;
    font-size: 1rem;
}

.edit-form {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: #fafafa;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    background: #fafafa;
    cursor: pointer;
}

.form-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    background: white;
}

.form-help {
    font-size: 12px;
    color: #6b7280;
    margin-top: 5px;
}

.char-counter {
    font-size: 12px;
    color: #9ca3af;
    text-align: right;
    margin-top: 5px;
}

.char-counter.warning {
    color: #f59e0b;
}

.char-counter.danger {
    color: #ef4444;
}

/* 프로필 이미지 업로드 */
.image-upload-section {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.current-image {
    flex-shrink: 0;
}

.profile-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 4px solid #e2e8f0;
    object-fit: cover;
    transition: border-color 0.3s ease;
}

.preview-fallback {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: 4px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
}

.upload-controls {
    flex: 1;
}

.file-input-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 15px;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.file-input-label {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    background: #667eea;
    color: white;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.file-input-label:hover {
    background: #5a67d8;
}

.image-info {
    background: #f8fafc;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.image-info h4 {
    margin: 0 0 10px 0;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.image-info ul {
    margin: 0;
    padding-left: 20px;
    font-size: 12px;
    color: #6b7280;
}

/* 소셜 링크 그리드 */
.social-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.social-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

.social-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    flex-shrink: 0;
}

.social-icon.kakao { background: #fee500; color: #000; }
.social-icon.website { background: #6b7280; }
.social-icon.instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); }
.social-icon.facebook { background: #1877f2; }
.social-icon.youtube { background: #ff0000; }
.social-icon.tiktok { background: #000; }

.social-input {
    flex: 1;
}

/* 버튼 스타일 */
.button-group {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
    padding-top: 30px;
    margin-top: 30px;
    border-top: 1px solid #e2e8f0;
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

.btn-primary {
    background: #667eea;
    color: white;
}

.btn-primary:hover {
    background: #5a67d8;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
    background: #e2e8f0;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* 로딩 상태 */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading .btn-primary {
    background: #9ca3af;
}

/* 알림 메시지 */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #bfdbfe;
}

/* 이미지 크롭 모달 */
.crop-modal {
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

.crop-modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 16px;
    padding: 30px;
    max-width: 90vw;
    max-height: 90vh;
    overflow: auto;
}

.crop-container {
    max-width: 500px;
    max-height: 400px;
    margin: 20px 0;
}

.crop-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin: 20px auto;
    border: 3px solid #667eea;
}

.crop-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 20px;
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 20px;
    color: #6b7280;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.modal-close:hover {
    background: #f3f4f6;
    color: #374151;
}

/* Quill Editor 스타일 조정 */
.ql-editor {
    min-height: 500px;
    font-family: 'Noto Sans KR', sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.ql-toolbar {
    border-top: 2px solid #e2e8f0;
    border-left: 2px solid #e2e8f0;
    border-right: 2px solid #e2e8f0;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    background: #fafafa;
}

.ql-container {
    border-bottom: 2px solid #e2e8f0;
    border-left: 2px solid #e2e8f0;
    border-right: 2px solid #e2e8f0;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
    background: white;
}

.ql-editor:focus {
    outline: none;
}

#bio-editor.ql-container:focus-within {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

#bio-editor.ql-container:focus-within .ql-toolbar {
    border-color: #667eea;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    .edit-container {
        padding: 15px;
    }
    
    .edit-form {
        padding: 20px;
    }
    
    .image-upload-section {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .social-grid {
        grid-template-columns: 1fr;
    }
    
    .button-group {
        flex-direction: column-reverse;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .crop-modal-content {
        padding: 20px;
        margin: 20px;
        max-width: calc(100vw - 40px);
    }
    
    .crop-container {
        max-width: 100%;
        max-height: 300px;
    }
}

@media (max-width: 480px) {
    .edit-header {
        padding: 20px;
    }
    
    .edit-title {
        font-size: 1.5rem;
    }
    
    .social-item {
        flex-direction: column;
        gap: 8px;
    }
    
    .social-icon {
        width: 35px;
        height: 35px;
        font-size: 16px;
    }
}
</style>

<div class="edit-container">
    <!-- 헤더 -->
    <div class="edit-header">
        <h1 class="edit-title">
            <i class="fas fa-edit"></i> 프로필 편집
        </h1>
        <p class="edit-subtitle">개인 정보와 소셜 링크를 관리하세요</p>
    </div>
    
    <!-- 알림 메시지 영역 -->
    <div id="alert-container"></div>
    
    <!-- 편집 폼 -->
    <form class="edit-form" id="profile-form">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <!-- 프로필 이미지 섹션 -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-camera"></i> 프로필 이미지
            </h2>
            <div class="image-upload-section">
                <div class="current-image">
                    <?php if (!empty($user['profile_image_profile'])): ?>
                        <img src="<?= htmlspecialchars($profileImageUrl) ?>" 
                             alt="현재 프로필 이미지" 
                             class="profile-preview"
                             id="image-preview">
                    <?php else: ?>
                        <div class="preview-fallback" id="image-preview">
                            <?= mb_substr($user['nickname'] ?? '?', 0, 1) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="upload-controls">
                    <div class="file-input-wrapper">
                        <input type="file" 
                               id="profile-image" 
                               name="profile_image" 
                               class="file-input"
                               accept="image/jpeg,image/png,image/gif,image/webp">
                        <label for="profile-image" class="file-input-label">
                            <i class="fas fa-upload"></i> 이미지 선택
                        </label>
                    </div>
                    <div class="image-info">
                        <h4>이미지 업로드 가이드</h4>
                        <ul>
                            <li>권장 크기: 400x400px 이상의 정사각형</li>
                            <li>최대 파일 크기: 5MB</li>
                            <li>지원 형식: JPG, PNG, GIF, WebP</li>
                            <li>업로드된 이미지는 자동으로 원형으로 표시됩니다</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 기본 정보 섹션 -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-user"></i> 기본 정보
            </h2>
            
            <div class="form-group">
                <label for="nickname" class="form-label">닉네임 *</label>
                <input type="text" 
                       id="nickname" 
                       name="nickname" 
                       class="form-input"
                       value="<?= htmlspecialchars($user['nickname'] ?? '') ?>"
                       required
                       maxlength="20"
                       minlength="2"
                       placeholder="닉네임을 입력하세요 (2-20자)">
                <div class="form-help">한글, 영문, 숫자를 사용하여 2-20자로 입력하세요</div>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">이메일 *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-input"
                       value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                       required
                       maxlength="100">
                <div class="form-help">계정 복구 및 중요한 알림을 받기 위해 사용됩니다 (필수)</div>
            </div>
            
            <div class="form-group">
                <label for="bio" class="form-label">자기소개</label>
                <div id="bio-editor" style="min-height: 120px; border: 2px solid #e2e8f0; border-radius: 8px; background: #fafafa;"></div>
                <textarea id="bio" 
                          name="bio" 
                          class="form-input form-textarea"
                          maxlength="2000"
                          style="display: none;"
                          placeholder="자신을 소개해보세요..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                <div class="char-counter" id="bio-counter">
                    <span id="bio-current"><?= mb_strlen($user['bio'] ?? '') ?></span>/2000
                </div>
            </div>
        </div>
        
        <!-- 개인 정보 섹션 -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i> 개인 정보
            </h2>
            
            <div class="form-group">
                <label for="birth_date" class="form-label">생년월일</label>
                <input type="date" 
                       id="birth_date" 
                       name="birth_date" 
                       class="form-input"
                       value="<?= $user['birth_date'] ?? '' ?>">
                <div class="form-help">나이 표시에 사용됩니다 (선택사항)</div>
            </div>
            
            <div class="form-group">
                <label for="gender" class="form-label">성별</label>
                <select id="gender" name="gender" class="form-select">
                    <option value="">선택하지 않음</option>
                    <option value="M" <?= ($user['gender'] ?? '') === 'M' ? 'selected' : '' ?>>남성</option>
                    <option value="F" <?= ($user['gender'] ?? '') === 'F' ? 'selected' : '' ?>>여성</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="phone" class="form-label">연락처</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       class="form-input"
                       value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                       placeholder="010-1234-5678"
                       readonly>
                <div class="form-help">등록된 연락처입니다 (읽기 전용)</div>
            </div>
        </div>
        
        <!-- 소셜 링크 섹션 -->
        <div class="form-section">
            <h2 class="section-title">
                <i class="fas fa-share-alt"></i> 소셜 링크
            </h2>
            
            <div class="social-grid">
                <div class="social-item">
                    <div class="social-icon website">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_website" class="form-label">웹사이트</label>
                        <input type="url" 
                               id="social_website" 
                               name="social_website" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['website'] ?? '') ?>"
                               placeholder="https://mywebsite.com">
                    </div>
                </div>
                
                <div class="social-item">
                    <div class="social-icon kakao">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_kakao" class="form-label">카카오톡</label>
                        <input type="url" 
                               id="social_kakao" 
                               name="social_kakao" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['kakao'] ?? '') ?>"
                               placeholder="https://open.kakao.com/o/xxxxxxx">
                    </div>
                </div>
                
                <div class="social-item">
                    <div class="social-icon instagram">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_instagram" class="form-label">인스타그램</label>
                        <input type="url" 
                               id="social_instagram" 
                               name="social_instagram" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['instagram'] ?? '') ?>"
                               placeholder="https://instagram.com/username">
                    </div>
                </div>
                
                <div class="social-item">
                    <div class="social-icon facebook">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_facebook" class="form-label">페이스북</label>
                        <input type="url" 
                               id="social_facebook" 
                               name="social_facebook" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['facebook'] ?? '') ?>"
                               placeholder="https://facebook.com/username">
                    </div>
                </div>
                
                <div class="social-item">
                    <div class="social-icon youtube">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_youtube" class="form-label">유튜브</label>
                        <input type="url" 
                               id="social_youtube" 
                               name="social_youtube" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['youtube'] ?? '') ?>"
                               placeholder="https://youtube.com/@channelname">
                    </div>
                </div>
                
                <div class="social-item">
                    <div class="social-icon tiktok">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div class="social-input">
                        <label for="social_tiktok" class="form-label">틱톡</label>
                        <input type="url" 
                               id="social_tiktok" 
                               name="social_tiktok" 
                               class="form-input"
                               value="<?= htmlspecialchars($socialLinks['tiktok'] ?? '') ?>"
                               placeholder="https://tiktok.com/@username">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 버튼 그룹 -->
        <div class="button-group">
            <a href="/profile" class="btn btn-secondary">
                <i class="fas fa-times"></i> 취소
            </a>
            <button type="submit" class="btn btn-primary" id="save-btn">
                <i class="fas fa-save"></i> 저장하기
            </button>
        </div>
    </form>
</div>

<!-- 이미지 크롭 모달 -->
<div id="crop-modal" class="crop-modal">
    <div class="crop-modal-content">
        <button class="modal-close" onclick="closeCropModal()">&times;</button>
        <h3 style="margin: 0 0 20px 0; text-align: center; color: #374151;">프로필 이미지 크롭</h3>
        
        <div class="crop-container">
            <img id="crop-image" style="max-width: 100%; display: block;">
        </div>
        
        <div class="crop-preview" id="crop-preview"></div>
        
        <div class="crop-buttons">
            <button type="button" class="btn btn-secondary" onclick="closeCropModal()">
                <i class="fas fa-times"></i> 취소
            </button>
            <button type="button" class="btn btn-primary" onclick="applyCrop()">
                <i class="fas fa-check"></i> 적용
            </button>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
let quill;
let cropper;
let croppedImageBlob = null;

// 전역 알림 메시지 표시 함수
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 
                 type === 'error' ? 'exclamation-circle' : 
                 'info-circle';
    
    alert.innerHTML = `
        <i class="fas fa-${icon}"></i>
        <span>${message}</span>
    `;
    
    alertContainer.appendChild(alert);
    
    // 5초 후 자동 제거
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }, 5000);
    
    // 스크롤을 알림으로 이동
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    const bioCurrentSpan = document.getElementById('bio-current');
    const saveBtn = document.getElementById('save-btn');
    const alertContainer = document.getElementById('alert-container');
    const imageInput = document.getElementById('profile-image');
    const imagePreview = document.getElementById('image-preview');
    
    // DOMNodeInserted 경고 완전 억제
    const originalAddEventListener = Node.prototype.addEventListener;
    Node.prototype.addEventListener = function(type, listener, options) {
        if (type === 'DOMNodeInserted') {
            // DOMNodeInserted 이벤트 리스너 등록을 무시
            return;
        }
        return originalAddEventListener.call(this, type, listener, options);
    };
    
    // Quill 에디터 초기화
    quill = new Quill('#bio-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: '자신을 소개해보세요...'
    });
    
    // addEventListener 복원 (Quill 초기화 후)
    setTimeout(() => {
        Node.prototype.addEventListener = originalAddEventListener;
    }, 2000);
    
    // Quill 이미지 업로드 핸들러
    quill.getModule('toolbar').addHandler('image', function() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();
        
        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                // 파일 크기 체크 (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert('이미지 크기는 2MB 이하여야 합니다.', 'error');
                    return;
                }
                
                // Base64로 변환하여 에디터에 삽입
                const reader = new FileReader();
                reader.onload = function(e) {
                    const range = quill.getSelection();
                    quill.insertEmbed(range.index, 'image', e.target.result);
                };
                reader.readAsDataURL(file);
            }
        };
    });
    
    // 기존 bio 내용을 Quill에 설정
    if (bioTextarea.value) {
        quill.root.innerHTML = bioTextarea.value;
    }
    
    // Quill 내용 변경 시 hidden textarea와 글자수 카운터 업데이트
    quill.on('text-change', function() {
        const html = quill.root.innerHTML;
        const text = quill.getText();
        bioTextarea.value = html; // HTML 저장
        updateBioCounter(text);
    });
    
    // 자기소개 글자수 카운터
    function updateBioCounter(text) {
        const current = text ? text.length - 1 : quill.getText().length - 1; // Quill은 마지막에 \n을 추가하므로 -1
        const max = 2000;
        bioCurrentSpan.textContent = Math.max(0, current);
        
        bioCounter.className = 'char-counter';
        if (current > max * 0.9) {
            bioCounter.classList.add('warning');
        }
        if (current > max * 0.95) {
            bioCounter.classList.add('danger');
        }
        
        // 2000자 초과 시 경고 표시
        if (current > max) {
            bioCounter.classList.add('danger');
            bioCounter.style.color = '#ef4444';
            bioCounter.style.fontWeight = 'bold';
        }
    }
    
    updateBioCounter();
    
    // 프로필 이미지 선택 시 크롭 모달 열기
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // 파일 크기 검증 (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('파일 크기는 5MB 이하여야 합니다.', 'error');
                e.target.value = '';
                return;
            }
            
            // 파일 형식 검증
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showAlert('지원하지 않는 파일 형식입니다. JPG, PNG, GIF, WebP만 허용됩니다.', 'error');
                e.target.value = '';
                return;
            }
            
            // 이미지 크롭 모달 열기
            openCropModal(file);
        }
    });
    
    // 폼 제출 처리
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (saveBtn.disabled) return;
        
        // 필수 필드 검증
        const emailInput = document.getElementById('email');
        const nicknameInput = document.getElementById('nickname');
        
        if (!nicknameInput.value.trim()) {
            showAlert('닉네임은 필수 입력 항목입니다.', 'error');
            nicknameInput.focus();
            return;
        }
        
        if (!emailInput.value.trim()) {
            showAlert('이메일은 필수 입력 항목입니다.', 'error');
            emailInput.focus();
            return;
        }
        
        if (!emailInput.checkValidity()) {
            showAlert('유효한 이메일 주소를 입력해주세요.', 'error');
            emailInput.focus();
            return;
        }
        
        // 자기소개 글자수 검증 (순수 텍스트 기준)
        const bioText = quill.getText();
        if (bioText.length - 1 > 2000) { // Quill은 마지막에 \n을 추가하므로 -1
            showAlert('자기소개는 2000자 이하로 입력해주세요. (현재: ' + (bioText.length - 1) + '자)', 'error');
            return;
        }
        
        // Quill 내용을 textarea에 동기화 (HTML로 저장)
        bioTextarea.value = quill.root.innerHTML;
        
        // 버튼 비활성화 및 로딩 상태
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 저장 중...';
        form.classList.add('loading');
        
        // FormData 생성
        const formData = new FormData(form);
        
        // 크롭된 이미지가 있으면 FormData에 추가
        console.log('🔍 croppedImageBlob 상태 확인:', croppedImageBlob);
        console.log('🔍 window.croppedImageBlob 상태 확인:', window.croppedImageBlob);
        
        const imageBlob = croppedImageBlob || window.croppedImageBlob;
        if (imageBlob) {
            console.log('📎 크롭된 이미지를 FormData에 추가:', imageBlob);
            formData.append('profile_image', imageBlob, 'profile_image.jpg');
        } else {
            console.log('❌ 크롭된 이미지 없음');
        }
        
        // 프로필 정보 업데이트 요청
        fetch('/profile/update', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log('📋 서버 응답 데이터:', data);
            if (data.error) {
                showAlert(data.error, 'error');
            } else {
                showAlert(data.message || '프로필이 성공적으로 업데이트되었습니다.', 'success');
                
                // 이미지가 포함된 경우 바로 프로필 페이지로 이동
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('프로필 업데이트 중 오류가 발생했습니다.', 'error');
        })
        .finally(() => {
            // 버튼 상태 복원
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> 저장하기';
            form.classList.remove('loading');
        });
    });
    
    // 프로필 이미지 업로드
    function uploadProfileImage(blob) {
        console.log('🔄 이미지 업로드 함수 호출됨', blob);
        
        const imageFormData = new FormData();
        imageFormData.append('profile_image', blob, 'profile_image.jpg');
        imageFormData.append('csrf_token', form.csrf_token.value);
        
        console.log('📤 서버로 이미지 전송 시작');
        
        fetch('/profile/upload-image', {
            method: 'POST',
            body: imageFormData
        })
        .then(response => {
            console.log('📨 서버 응답 상태:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 서버 응답 데이터:', data);
            if (data.error) {
                showAlert('이미지 업로드 실패: ' + data.error, 'error');
            } else {
                showAlert('프로필 이미지가 성공적으로 업데이트되었습니다.', 'success');
                
                // 2초 후 프로필 페이지로 이동
                setTimeout(() => {
                    window.location.href = '/profile';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('❌ Image upload error:', error);
            showAlert('이미지 업로드 중 오류가 발생했습니다.', 'error');
        });
    }
    
    console.log('🔧 프로필 편집 페이지 로드 완료');
});

// 이미지 크롭 모달 관련 함수들
function openCropModal(file) {
    const modal = document.getElementById('crop-modal');
    const cropImage = document.getElementById('crop-image');
    const cropPreview = document.getElementById('crop-preview');
    
    const reader = new FileReader();
    reader.onload = function(e) {
        cropImage.src = e.target.result;
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Cropper 초기화
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            autoCropArea: 0.8,
            responsive: true,
            cropBoxMovable: true,
            cropBoxResizable: true,
            preview: cropPreview,
            crop: function(event) {
                // 미리보기 업데이트는 자동으로 됨
            }
        });
    };
    reader.readAsDataURL(file);
}

function closeCropModal() {
    const modal = document.getElementById('crop-modal');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    
    // 파일 입력 초기화 (크롭된 이미지는 유지)
    document.getElementById('profile-image').value = '';
    // croppedImageBlob = null; // 크롭된 이미지는 폼 저장까지 유지
}

function applyCrop() {
    if (!cropper) return;
    
    // 크롭된 이미지를 캔버스로 가져오기
    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });
    
    // 캔버스를 Blob으로 변환
    canvas.toBlob(function(blob) {
        console.log('✂️ 이미지 크롭 완료, Blob 생성됨:', blob);
        croppedImageBlob = blob;
        window.croppedImageBlob = blob; // window 객체에도 저장
        console.log('💾 전역 변수에 저장됨:', croppedImageBlob);
        console.log('💾 window 객체에도 저장됨:', window.croppedImageBlob);
        
        // 미리보기 업데이트
        const imagePreview = document.getElementById('image-preview');
        const previewUrl = URL.createObjectURL(blob);
        
        if (imagePreview.tagName === 'IMG') {
            imagePreview.src = previewUrl;
        } else {
            // fallback div를 img로 교체
            const newImg = document.createElement('img');
            newImg.src = previewUrl;
            newImg.alt = '프로필 이미지 미리보기';
            newImg.className = 'profile-preview';
            newImg.id = 'image-preview';
            imagePreview.parentNode.replaceChild(newImg, imagePreview);
        }
        
        // 모달 닫기
        closeCropModal();
        
        showAlert('이미지가 선택되었습니다. 저장하기를 클릭하여 업로드하세요.', 'info');
    }, 'image/jpeg', 0.9);
}

// ESC 키로 모달 닫기
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('crop-modal');
        if (modal.style.display === 'block') {
            closeCropModal();
        }
    }
});
</script>