<?php
/**
 * 강의 등록/수정 페이지 (통합)
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

// 기업회원 권한 확인
require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
$permission = CorporateMiddleware::checkLectureEventPermission();

if (!$permission['hasPermission']) {
    $_SESSION['error_message'] = $permission['message'];
    header('Location: /corp/info');
    exit;
}

// 수정 모드 확인 (URL에서 ID 파라미터가 있으면 수정 모드)
$isEditMode = false;
$lecture = null;
$lectureId = null;

if (isset($data['lecture']) && !empty($data['lecture'])) {
    $isEditMode = true;
    $lecture = $data['lecture'];
    $lectureId = $lecture['id'];
}

// CSRF 토큰 생성
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<style>
/* 강의 등록 페이지 스타일 */
.lecture-create-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px 20px 40px;
    min-height: calc(100vh - 160px);
}

.create-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    text-align: center;
    margin-top: 60px;
    margin-bottom: 30px;
    border-radius: 12px;
}

.create-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.create-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0;
}

.create-form {
    background: white;
    border-radius: 12px;
    padding: 40px;
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
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: #e53e3e;
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background: white;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
}

.form-help {
    font-size: 0.85rem;
    color: #718096;
    margin-top: 5px;
}

.form-error {
    font-size: 0.85rem;
    color: #e53e3e;
    margin-top: 5px;
    display: none;
}

/* 라디오/체크박스 그룹 */
.radio-group, .checkbox-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.radio-item, .checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.radio-item input, .checkbox-item input {
    width: auto;
    margin: 0;
}

/* 위치 타입별 필드 */
.location-fields {
    display: none;
    margin-top: 15px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.location-fields.active {
    display: block;
}

/* 버튼 스타일 */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 40px;
    flex-wrap: wrap;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(72, 187, 120, 0.4);
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.btn-draft {
    background: #ed8936;
    color: white;
}

.btn-draft:hover {
    background: #dd6b20;
}

/* 로딩 상태 */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.loading {
    display: none;
    color: #667eea;
    font-size: 0.9rem;
}

/* 모바일 반응형 */
@media (max-width: 768px) {
    .lecture-create-container {
        padding: 15px;
    }
    
    .create-header {
        padding: 30px 20px;
    }
    
    .create-header h1 {
        font-size: 2rem;
    }
    
    .create-form {
        padding: 30px 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .radio-group, .checkbox-group {
        flex-direction: column;
        gap: 10px;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}

/* 강사 관리 스타일 */
.instructor-item {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    background: #f8fafc;
}

.instructor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}

.instructor-header h3 {
    margin: 0;
    color: #2d3748;
    font-size: 1.1rem;
}

.remove-instructor-btn {
    background: #ef4444;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background-color 0.3s ease;
}

.remove-instructor-btn:hover {
    background: #dc2626;
}

.instructor-actions {
    text-align: center;
    margin-top: 20px;
}

.btn-outline {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

/* 소요시간 표시 스타일 */
.duration-info {
    padding: 12px 15px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 6px;
    font-weight: 500;
    color: #0369a1;
}

/* 이미지 업로드 스타일 */
.image-upload-area {
    border: 2px dashed #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    transition: all 0.3s ease;
    cursor: pointer;
}

.image-upload-area:hover {
    border-color: #667eea;
    background: #f1f5f9;
}

.image-upload-container {
    margin-bottom: 15px;
}

.image-preview-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.lecture-image-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.lecture-image-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
}

.remove-lecture-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
}

.remove-lecture-image:hover {
    background: rgba(220, 38, 38, 0.9);
}

.upload-placeholder {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}

.upload-icon {
    font-size: 2rem;
    color: #94a3b8;
    margin-bottom: 10px;
}

.upload-placeholder p {
    margin: 10px 0 5px;
    font-weight: 500;
}

.upload-help {
    font-size: 0.8rem;
    color: #94a3b8;
}

/* 강사 이미지 업로드 스타일 */
.instructor-image-upload {
    margin-bottom: 15px;
}

.instructor-image-container {
    position: relative;
    width: 120px;
    height: 120px;
    border: 2px dashed #d1d5db;
    border-radius: 50%;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #f9fafb;
}

.instructor-image-container:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.instructor-image-container.has-image {
    border: 2px solid #e5e7eb;
    background: white;
}

.instructor-image-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.instructor-image-placeholder {
    text-align: center;
    color: #6b7280;
}

.instructor-image-placeholder i {
    font-size: 2rem;
    margin-bottom: 5px;
    display: block;
}

.instructor-image-placeholder span {
    font-size: 0.75rem;
    display: block;
}

.remove-instructor-image {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 12px;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.instructor-image-container.has-image .remove-instructor-image {
    display: flex;
}

.remove-instructor-image:hover {
    background: #dc2626;
}

/* 기존 이미지 스타일 */
.existing-image {
    margin: 10px;
    display: inline-block;
    vertical-align: top;
}

.existing-image .image-container {
    position: relative;
    width: 150px;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.existing-image .image-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.existing-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.existing-image .remove-existing-image {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 14px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 10;
}

.existing-image .remove-existing-image:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.1);
}

.existing-image .image-info {
    margin-top: 8px;
    text-align: center;
    width: 150px;
}

.image-preview {
    position: relative;
    text-align: center;
    padding: 20px;
}

.image-preview img {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.remove-image {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    transition: background-color 0.3s ease;
}

.remove-image:hover {
    background: #dc2626;
}

/* 다크모드 대응 */
@media (prefers-color-scheme: dark) {
    .create-form {
        background: #2d3748;
        border-color: #4a5568;
    }
    
    .form-input, .form-select, .form-textarea {
        background: #4a5568;
        border-color: #718096;
        color: white;
    }
    
    .location-fields {
        background: #4a5568;
        border-color: #718096;
    }
    
    .image-upload-area {
        background: #4a5568;
        border-color: #718096;
    }
}

/* 날짜/시간 입력 필드 개선 */
input[type="date"], 
input[type="time"], 
input[type="datetime-local"] {
    cursor: pointer;
    position: relative;
}

/* 날짜/시간 입력 필드 전체 클릭 가능하게 만들기 */
input[type="date"]::-webkit-calendar-picker-indicator,
input[type="time"]::-webkit-calendar-picker-indicator,
input[type="datetime-local"]::-webkit-calendar-picker-indicator {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    width: auto;
    height: auto;
    color: transparent;
    background: transparent;
    cursor: pointer;
}

/* 드래그&드롭 순서 변경 스타일 */
.sortable-container {
    min-height: 100px;
    position: relative;
}

.sortable-container.has-images {
    padding: 20px;
    border: 2px dashed #e2e8f0;
    border-radius: 8px;
    background: #f8fafc;
    margin-top: 15px;
}

.drag-instructions {
    text-align: center;
    color: #667eea;
    font-size: 14px;
    font-weight: 500;
    padding: 10px;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 6px;
    margin-bottom: 15px;
}

.drag-instructions i {
    margin-right: 8px;
    font-size: 16px;
}

.lecture-image-item {
    position: relative;
    display: inline-block;
    margin: 10px;
    cursor: move;
    user-select: none;
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.lecture-image-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.lecture-image-item.dragging {
    opacity: 0.5;
    transform: rotate(5deg) scale(1.05);
    z-index: 1000;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
}

.lecture-image-item.drag-over {
    transform: scale(1.05);
    border: 2px solid #667eea;
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}

.lecture-image-item .image-container {
    position: relative;
    width: 150px;
    height: 150px;
    overflow: hidden;
}

.lecture-image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.lecture-image-item .drag-handle {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 4px;
    padding: 4px 6px;
    font-size: 10px;
    cursor: move;
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 5;
}

.lecture-image-item:hover .drag-handle {
    opacity: 1;
}

.lecture-image-item .image-order {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    color: white;
    text-align: center;
    padding: 8px 4px 4px;
    font-size: 11px;
    font-weight: 600;
}

.lecture-image-item .remove-lecture-image {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s ease;
    z-index: 10;
}

.lecture-image-item .remove-lecture-image:hover {
    background: #dc2626;
}

.lecture-image-item .image-info {
    padding: 8px;
    background: white;
    border-top: 1px solid #e2e8f0;
}

.lecture-image-item .image-info div:first-child {
    font-size: 12px;
    color: #2d3748;
    font-weight: 500;
    margin-bottom: 2px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.lecture-image-item .image-info div:last-child {
    font-size: 10px;
    color: #718096;
}

/* 드래그 중 상태 표시 */
.sortable-container.drag-active {
    background: rgba(102, 126, 234, 0.05);
    border-color: #667eea;
}

.drop-zone {
    position: relative;
}

.drop-zone::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #667eea;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.drop-zone.active::before {
    opacity: 1;
}

/* 참가비 입력 필드 스타일 */
#registration_fee {
    text-align: right;
    font-weight: 600;
}

#registration_fee_display {
    font-size: 1.1em;
    font-weight: 600;
    color: #2563eb;
    margin-top: 5px;
}
</style>

<div class="lecture-create-container">
    <!-- 헤더 섹션 -->
    <div class="create-header">
        <h1><?= $isEditMode ? '✏️ 강의 수정' : '➕ 강의 등록' ?></h1>
        <p><?= $isEditMode ? '강의 정보를 수정하여 더 나은 내용을 제공하세요' : '새로운 강의나 세미나를 등록하여 많은 분들과 지식을 공유하세요' ?></p>
    </div>
    
    <!-- 등록/수정 폼 -->
    <form id="lectureForm" class="create-form" method="POST" action="<?= $isEditMode ? "/lectures/{$lectureId}/update" : '/lectures/store' ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <?php if ($isEditMode): ?>
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="lecture_id" value="<?= $lectureId ?>">
        <?php endif; ?>
        <input type="hidden" id="existing_lecture_images_hidden" name="existing_lecture_images_hidden" value="">
        
        <!-- 기본 정보 -->
        <div class="form-section">
            <h2 class="section-title">📋 기본 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title" class="form-label required">강의 제목</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['title'] ?? '') : '' ?>"
                           placeholder="예: 디지털 마케팅 전략 완벽 가이드" required>
                    <div class="form-help">참가자들이 쉽게 이해할 수 있는 명확한 제목을 입력하세요</div>
                    <div class="form-error" id="title-error"></div>
                </div>
                
                <input type="hidden" name="content_type" value="lecture">
                
                <input type="hidden" name="category" value="seminar">
                
                <input type="hidden" name="difficulty_level" value="all">
                
                <div class="form-group full-width">
                    <label for="description" class="form-label required">강의 설명</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="강의 내용, 목표, 대상자 등을 자세히 설명해주세요" required><?= $isEditMode ? htmlspecialchars($lecture['description'] ?? '') : '' ?></textarea>
                    <div class="form-help">참가자들이 강의 내용을 충분히 이해할 수 있도록 상세히 작성해주세요</div>
                    <div class="form-error" id="description-error"></div>
                </div>
            </div>
        </div>
        
        <!-- 강사 정보 -->
        <div class="form-section">
            <h2 class="section-title">👨‍🏫 강사 정보</h2>
            <div id="instructors-container">
                <div class="instructor-item" data-instructor-index="0">
                    <div class="instructor-header">
                        <h3>강사 1</h3>
                        <button type="button" class="remove-instructor-btn" style="display: none;">
                            <i class="fas fa-times"></i> 제거
                        </button>
                    </div>
                    <!-- 강사 이미지 업로드 -->
                    <div class="instructor-image-upload">
                        <label class="form-label">강사 프로필 이미지</label>
                        <div class="instructor-image-container" onclick="document.getElementById('instructor_image_0').click()">
                            <div class="instructor-image-placeholder">
                                <i class="fas fa-user-circle"></i>
                                <span>클릭하여 이미지 선택</span>
                            </div>
                            <button type="button" class="remove-instructor-image" onclick="removeInstructorImage(0)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <input type="file" id="instructor_image_0" name="instructors[0][image]" 
                               style="display: none" accept="image/*" onchange="handleInstructorImage(0, this)">
                        <div class="form-help">JPG, PNG, GIF, WebP 파일을 업로드하세요 (최대 2MB)</div>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="instructor_name_0" class="form-label required">강사명</label>
                            <input type="text" id="instructor_name_0" name="instructors[0][name]" class="form-input" 
                                   value="<?= $isEditMode ? htmlspecialchars($lecture['instructors'][0]['name'] ?? '') : '' ?>"
                                   placeholder="예: 김마케팅" required>
                            <div class="form-error" id="instructor_name_0-error"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="instructor_title_0" class="form-label">직책/전문분야</label>
                            <input type="text" id="instructor_title_0" name="instructors[0][title]" class="form-input" 
                                   value="<?= $isEditMode ? htmlspecialchars($lecture['instructors'][0]['title'] ?? '') : '' ?>"
                                   placeholder="예: 디지털 마케팅 전문가">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="instructor_info_0" class="form-label">강사 소개</label>
                            <textarea id="instructor_info_0" name="instructors[0][info]" class="form-textarea" 
                                      placeholder="강사의 경력, 전문분야, 주요 실적 등을 소개해주세요"><?= $isEditMode ? htmlspecialchars($lecture['instructors'][0]['info'] ?? '') : '' ?></textarea>
                            <div class="form-help">강사의 전문성을 어필할 수 있는 내용을 작성해주세요</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="instructor-actions">
                <button type="button" id="add-instructor-btn" class="btn btn-outline">
                    <i class="fas fa-plus"></i> 강사 추가
                </button>
                <div class="form-help">최대 5명까지 강사를 추가할 수 있습니다</div>
            </div>
        </div>
        
        <!-- 일정 정보 -->
        <div class="form-section">
            <h2 class="section-title">📅 일정 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="timezone" class="form-label">시간대</label>
                    <select id="timezone" name="timezone" class="form-select">
                        <?php $selectedTimezone = $isEditMode ? ($lecture['timezone'] ?? 'Asia/Seoul') : 'Asia/Seoul'; ?>
                        <option value="Asia/Seoul" <?= $selectedTimezone === 'Asia/Seoul' ? 'selected' : '' ?>>한국 표준시 (KST)</option>
                        <option value="Asia/Tokyo" <?= $selectedTimezone === 'Asia/Tokyo' ? 'selected' : '' ?>>일본 표준시 (JST)</option>
                        <option value="Asia/Shanghai" <?= $selectedTimezone === 'Asia/Shanghai' ? 'selected' : '' ?>>중국 표준시 (CST)</option>
                        <option value="America/New_York" <?= $selectedTimezone === 'America/New_York' ? 'selected' : '' ?>>동부 표준시 (EST)</option>
                        <option value="America/Los_Angeles" <?= $selectedTimezone === 'America/Los_Angeles' ? 'selected' : '' ?>>태평양 표준시 (PST)</option>
                        <option value="Europe/London" <?= $selectedTimezone === 'Europe/London' ? 'selected' : '' ?>>그리니치 표준시 (GMT)</option>
                        <option value="UTC" <?= $selectedTimezone === 'UTC' ? 'selected' : '' ?>>협정 세계시 (UTC)</option>
                    </select>
                    <div class="form-help">강의가 진행되는 시간대를 선택하세요</div>
                </div>
                
                <div class="form-group">
                    <label for="start_date" class="form-label required">시작 날짜</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['start_date'] ?? '') : '' ?>" required>
                    <div class="form-error" id="start_date-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="end_date" class="form-label required">종료 날짜</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['end_date'] ?? '') : '' ?>" required>
                    <div class="form-error" id="end_date-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="start_time" class="form-label required">시작 시간</label>
                    <input type="time" id="start_time" name="start_time" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['start_time'] ?? '') : '' ?>" required>
                    <div class="form-error" id="start_time-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="end_time" class="form-label required">종료 시간</label>
                    <input type="time" id="end_time" name="end_time" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['end_time'] ?? '') : '' ?>" required>
                    <div class="form-error" id="end_time-error"></div>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">소요시간</label>
                    <div id="duration-display" class="duration-info">
                        <span id="duration-text">시작 시간과 종료 시간을 입력하면 자동으로 계산됩니다</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 위치 정보 -->
        <div class="form-section">
            <h2 class="section-title">📍 위치 정보</h2>
            <div class="form-group">
                <label class="form-label required">진행 방식</label>
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="location_type" value="offline" 
                               <?= ($isEditMode ? ($lecture['location_type'] ?? '') : ($defaultData['location_type'] ?? '')) === 'offline' ? 'checked' : '' ?> required>
                        <span>📍 오프라인</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="location_type" value="online" 
                               <?= ($isEditMode ? ($lecture['location_type'] ?? '') : ($defaultData['location_type'] ?? '')) === 'online' ? 'checked' : '' ?> required>
                        <span>💻 온라인</span>
                    </label>
                </div>
            </div>
            
            <!-- 오프라인 필드 -->
            <div id="offline-fields" class="location-fields">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="venue_name" class="form-label">장소명</label>
                        <input type="text" id="venue_name" name="venue_name" class="form-input" 
                               value="<?= $isEditMode ? htmlspecialchars($lecture['venue_name'] ?? '') : '' ?>"
                               placeholder="예: 강남구 세미나실">
                    </div>
                    <div class="form-group full-width">
                        <label for="venue_address" class="form-label">장소 주소</label>
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <input type="text" id="venue_address" name="venue_address" class="form-input" 
                                   value="<?= $isEditMode ? htmlspecialchars($lecture['venue_address'] ?? '') : '' ?>"
                                   placeholder="주소 검색 버튼을 클릭하여 정확한 주소를 입력해주세요" readonly
                                   style="flex: 1;">
                            <button type="button" id="address_search_btn" class="btn btn-secondary"
                                    style="padding: 10px 16px; white-space: nowrap;">
                                🔍 주소 검색
                            </button>
                        </div>
                        <div class="form-help">주소 검색을 통해 정확한 주소를 입력하면 지도에 정확한 위치가 표시됩니다</div>
                        <!-- 위도, 경도 저장을 위한 숨김 필드 -->
                        <input type="hidden" id="venue_latitude" name="venue_latitude" 
                               value="<?= $isEditMode ? htmlspecialchars($lecture['venue_latitude'] ?? '') : '' ?>">
                        <input type="hidden" id="venue_longitude" name="venue_longitude" 
                               value="<?= $isEditMode ? htmlspecialchars($lecture['venue_longitude'] ?? '') : '' ?>">
                    </div>
                </div>
            </div>
            
            <!-- 온라인 필드 -->
            <div id="online-fields" class="location-fields">
                <div class="form-group">
                    <label for="online_link" class="form-label">온라인 링크</label>
                    <input type="url" id="online_link" name="online_link" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['online_link'] ?? '') : '' ?>"
                           placeholder="Zoom, 유튜브 등의 링크를 입력해주세요">
                    <div class="form-help">참가자들이 접속할 수 있는 링크를 입력하세요</div>
                </div>
            </div>
        </div>
        
        <!-- 참가 정보 -->
        <div class="form-section">
            <h2 class="section-title">👥 참가 정보</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="max_participants" class="form-label">최대 참가자 수</label>
                    <input type="number" id="max_participants" name="max_participants" 
                           class="form-input" min="1" value="<?= $isEditMode ? htmlspecialchars($lecture['max_participants'] ?? '') : '' ?>"
                           placeholder="무제한인 경우 비워두세요">
                    <div class="form-help">참가자 수 제한이 없으면 비워두세요</div>
                </div>
                
                <div class="form-group">
                    <label for="registration_fee" class="form-label">참가비 (원)</label>
                    <input type="text" id="registration_fee_display" 
                           class="form-input" value="<?= $isEditMode ? number_format($lecture['registration_fee'] ?? 0) : '0' ?>"
                           placeholder="0" style="text-align: right;">
                    <input type="hidden" id="registration_fee" name="registration_fee" 
                           value="<?= $isEditMode ? ($lecture['registration_fee'] ?? 0) : 0 ?>">
                    <div class="form-help">무료인 경우 0을 입력하세요 (천 단위 콤마 자동 추가)</div>
                </div>
                
                <div class="form-group">
                    <label for="registration_deadline" class="form-label">등록 마감일시</label>
                    <input type="datetime-local" id="registration_deadline" name="registration_deadline" 
                           class="form-input" value="<?= $isEditMode ? htmlspecialchars($lecture['registration_deadline'] ?? '') : '' ?>">
                    <div class="form-help">마감일이 없으면 비워두세요 (과거 날짜는 선택할 수 없습니다)</div>
                </div>
            </div>
        </div>
        
        
        <!-- 미디어 정보 -->
        <div class="form-section">
            <h2 class="section-title">📹 미디어 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="lecture_images" class="form-label">강의 이미지</label>
                    <div class="image-upload-container">
                        <div class="image-upload-area" id="lectureImageUploadArea">
                            <input type="file" id="lecture_images" name="lecture_images[]" 
                                   accept="image/*" multiple style="display: none;">
                            <div class="upload-placeholder" id="lectureImagePlaceholder">
                                <i class="fas fa-images upload-icon"></i>
                                <p>클릭하여 강의 이미지 업로드</p>
                                <span class="upload-help">JPG, PNG, GIF, WebP 파일 (최대 5MB, 최대 8장)</span>
                            </div>
                        </div>
                        <div class="image-preview-container sortable-container" id="lectureImagePreview">
                            <div class="drag-instructions" style="display: none;">
                                <i class="fas fa-arrows-alt"></i>
                                <span>드래그하여 순서를 변경하세요</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-help">강의 관련 이미지들을 업로드하면 참가자들에게 더 생생한 정보를 제공할 수 있습니다</div>
                </div>
                
                <div class="form-group full-width">
                    <label for="youtube_video" class="form-label">YouTube 동영상 URL</label>
                    <input type="url" id="youtube_video" name="youtube_video" class="form-input" 
                           value="<?= $isEditMode ? htmlspecialchars($lecture['youtube_video'] ?? '') : '' ?>"
                           placeholder="https://www.youtube.com/watch?v=...">
                    <div class="form-help">강의 소개 영상이나 관련 동영상 링크가 있으면 입력해주세요</div>
                    <div class="form-error" id="youtube_video-error"></div>
                </div>
            </div>
        </div>
        
        <!-- 추가 정보 -->
        <div class="form-section">
            <h2 class="section-title">📝 추가 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="prerequisites" class="form-label">참가 조건</label>
                    <textarea id="prerequisites" name="prerequisites" class="form-textarea" 
                              placeholder="참가자가 사전에 알아야 할 내용이나 준비사항"><?= $isEditMode ? htmlspecialchars($lecture['prerequisites'] ?? '') : '' ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label for="what_to_bring" class="form-label">준비물</label>
                    <textarea id="what_to_bring" name="what_to_bring" class="form-textarea" 
                              placeholder="참가자가 지참해야 할 물품"><?= $isEditMode ? htmlspecialchars($lecture['what_to_bring'] ?? '') : '' ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label for="additional_info" class="form-label">기타 안내사항</label>
                    <textarea id="additional_info" name="additional_info" class="form-textarea" 
                              placeholder="기타 중요한 안내사항"><?= $isEditMode ? htmlspecialchars($lecture['additional_info'] ?? '') : '' ?></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label for="benefits" class="form-label">참가자 혜택</label>
                    <textarea id="benefits" name="benefits" class="form-textarea" 
                              placeholder="참가자가 얻을 수 있는 혜택이나 성과를 설명해주세요"><?= $isEditMode ? htmlspecialchars($lecture['benefits'] ?? '') : '' ?></textarea>
                </div>
            </div>
        </div>
        
        <!-- 폼 액션 -->
        <div class="form-actions">
            <a href="<?= $isEditMode ? "/lectures/{$lectureId}" : '/lectures' ?>" class="btn btn-secondary">
                <?= $isEditMode ? '← 강의로 돌아가기' : '← 목록으로' ?>
            </a>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" name="status" value="draft" class="btn btn-draft">
                    💾 임시저장
                </button>
                <button type="submit" name="status" value="published" class="btn btn-primary">
                    <?= $isEditMode ? '✏️ 수정완료' : '🚀 등록하기' ?>
                </button>
            </div>
        </div>
        
        <div class="loading" id="loading">
            ⏳ 강의를 등록하고 있습니다...
        </div>
    </form>
</div>

<script>
// 전역 변수 정의
let currentImageData = [];
let lectureImages = [];
const maxLectureImages = 8;
const isEditMode = <?= $isEditMode ? 'true' : 'false' ?>; // PHP에서 전달된 편집 모드 상태

// 기존 이미지 삭제 함수 (전역 함수로 먼저 정의)
function removeExistingImage(imageIndex, imageElement) {
    // 시각적으로 요소 제거
    imageElement.remove();
    
    // 현재 이미지 데이터에서 해당 이미지 제거
    if (Array.isArray(currentImageData)) {
        // 해당 인덱스의 이미지 제거
        currentImageData.splice(imageIndex, 1);
        // console.log('이미지 삭제 후 currentImageData:', currentImageData);
        
        // 서버에 업데이트된 이미지 목록 전송
        updateImageListOnServer(currentImageData);
        
        // 다른 이미지들의 인덱스 업데이트
        updateImageIndexes();
    } else {
        console.error('currentImageData가 배열이 아닙니다:', currentImageData);
        showAlert('이미지 삭제 중 오류가 발생했습니다.', 'error');
    }
}

// 이미지 인덱스 업데이트 함수 (전역 함수로 먼저 정의)
function updateImageIndexes() {
    const existingImages = document.querySelectorAll('.existing-image');
    existingImages.forEach((item, newIndex) => {
        item.setAttribute('data-image-index', newIndex);
        const removeBtn = item.querySelector('.remove-existing-image');
        if (removeBtn) {
            // 기존 이벤트 제거 후 새로운 이벤트 추가
            const newBtn = removeBtn.cloneNode(true);
            removeBtn.parentNode.replaceChild(newBtn, removeBtn);
            
            newBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('이 이미지를 삭제하시겠습니까?')) {
                    removeExistingImage(newIndex, item);
                }
            });
        }
    });
}

// 알림 표시 함수 (전역 함수로 먼저 정의)
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    
    // 타입별 스타일 설정
    const styles = {
        'info': 'background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460;',
        'success': 'background: #d4edda; border: 1px solid #c3e6cb; color: #155724;',
        'error': 'background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24;',
        'warning': 'background: #fff3cd; border: 1px solid #ffeaa7; color: #856404;'
    };
    
    alertDiv.style.cssText = `position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-weight: 500; min-width: 250px; max-width: 400px; ${styles[type] || styles.info}`;
    alertDiv.textContent = message;
    
    document.body.appendChild(alertDiv);
    
    // 3초 후 자동 제거
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}

// 이미지 업로드 플레이스홀더 업데이트 함수 (전역 함수로 먼저 정의)
function updateImageUploadPlaceholder() {
    const placeholder = document.getElementById('lectureImagePlaceholder');
    if (!placeholder) return;
    
    // 현재 전체 이미지 수 계산 (기존 이미지 + 새 이미지)
    const existingImageCount = (Array.isArray(currentImageData) ? currentImageData.length : 0);
    const totalImageCount = existingImageCount + (Array.isArray(lectureImages) ? lectureImages.length : 0);
    
    if (totalImageCount >= maxLectureImages) {
        placeholder.style.display = 'none';
    } else {
        placeholder.style.display = 'block';
        const remainingCount = maxLectureImages - totalImageCount;
        const uploadHelp = placeholder.querySelector('.upload-help');
        if (uploadHelp) {
            uploadHelp.textContent = `JPG, PNG, GIF, WebP 파일 (최대 5MB, ${remainingCount}장 더 추가 가능)`;
        }
    }
}

// 강의 이미지 화면 업데이트 함수 (전역 함수로 먼저 정의)
function updateLectureImagesDisplay(updatedImages) {
    // console.log('updateLectureImagesDisplay 호출됨, 이미지 개수:', updatedImages.length);
    
    const imagePreviewContainer = document.getElementById('lectureImagePreview');
    if (!imagePreviewContainer) {
        console.error('lectureImagePreview 컨테이너를 찾을 수 없음');
        return;
    }
    
    // 기존 화면 내용 제거
    imagePreviewContainer.innerHTML = '';
    
    // 서버에서 받은 이미지 데이터로 화면 다시 구성
    updatedImages.forEach((image, index) => {
        const imageItem = document.createElement('div');
        imageItem.className = 'lecture-image-item existing-image';
        imageItem.setAttribute('data-image-index', index);
        imageItem.innerHTML = '<div class="image-container">' +
            '<img src="' + image.file_path + '" alt="' + (image.original_name || '강의 이미지') + '" class="lecture-image-preview">' +
            '<button type="button" class="remove-existing-image"><i class="fas fa-times"></i></button>' +
            '</div>' +
            '<div class="image-info">' +
            '<div style="font-size: 12px; color: #666; margin-bottom: 2px;">' + (image.original_name || '알 수 없는 파일') + '</div>' +
            '<div style="font-size: 10px; color: #999;">임시저장된 이미지</div>' +
            '</div>';
        
        // 삭제 버튼 이벤트 추가
        const removeBtn = imageItem.querySelector('.remove-existing-image');
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('이 이미지를 삭제하시겠습니까?')) {
                removeExistingImage(index, imageItem);
            }
        });
        
        imagePreviewContainer.appendChild(imageItem);
    });
    
    // 업로드 플레이스홀더 업데이트
    updateImageUploadPlaceholder();
    
    // console.log('화면 업데이트 완료:', updatedImages.length + '개 이미지 표시됨');
}

// 강사 이미지 처리 함수들 (전역 함수로 먼저 정의)
function handleInstructorImage(index, input) {
    // console.log(`handleInstructorImage 호출: index=${index}, input=`, input);
    
    const file = input.files[0];
    if (!file) {
        // console.log('파일이 선택되지 않음');
        return;
    }
    
    // console.log('선택된 파일:', file.name, file.type, file.size);
    
    // 파일 유효성 검사
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        alert('JPG, PNG, GIF, WebP 파일만 업로드 가능합니다.');
        input.value = '';
        return;
    }
    
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        alert('파일 크기는 2MB 이하여야 합니다.');
        input.value = '';
        return;
    }
    
    // 미리보기 표시
    const reader = new FileReader();
    reader.onload = function(e) {
        // console.log(`FileReader onload 시작: index=${index}`);
        
        const fileInput = document.querySelector(`#instructor_image_${index}`);
        // console.log(`찾은 파일 입력 요소:`, fileInput);
        if (!fileInput) {
            console.error(`강사 이미지 입력 요소를 찾을 수 없습니다: #instructor_image_${index}`);
            return;
        }
        
        // input 요소의 형제 요소인 instructor-image-container 찾기
        const uploadDiv = fileInput.closest('.instructor-image-upload');
        const container = uploadDiv ? uploadDiv.querySelector('.instructor-image-container') : null;
        // console.log(`찾은 업로드 div:`, uploadDiv);
        // console.log(`찾은 컨테이너:`, container);
        if (!container) {
            console.error('이미지 컨테이너를 찾을 수 없습니다');
            return;
        }
        
        // 기존 내용 제거
        container.innerHTML = '';
        
        // 새 이미지 요소 생성
        const img = document.createElement('img');
        img.src = e.target.result;
        img.alt = `강사 ${index + 1} 이미지`;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '8px';
        
        // 삭제 버튼 생성
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'remove-instructor-image';
        removeBtn.innerHTML = '<i class="fas fa-times"></i>';
        removeBtn.onclick = function() { removeInstructorImage(index); };
        
        // 컨테이너에 추가
        container.appendChild(img);
        container.appendChild(removeBtn);
        
        // console.log(`이미지 미리보기 설정 완료: index=${index}`);
    };
    
    reader.readAsDataURL(file);
}

function removeInstructorImage(index) {
    // console.log(`removeInstructorImage 호출: index=${index}`);
    
    const fileInput = document.querySelector(`#instructor_image_${index}`);
    const uploadDiv = fileInput ? fileInput.closest('.instructor-image-upload') : null;
    const container = uploadDiv ? uploadDiv.querySelector('.instructor-image-container') : null;
    
    if (container && fileInput) {
        // 파일 입력 초기화
        fileInput.value = '';
        
        // 플레이스홀더로 복원
        container.innerHTML = `
            <div class="instructor-image-placeholder">
                <i class="fas fa-user-circle"></i>
                <span>클릭하여 이미지 선택</span>
            </div>
            <button type="button" class="remove-instructor-image" onclick="removeInstructorImage(${index})">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // 클릭 이벤트 재설정
        container.onclick = function() {
            fileInput.click();
        };
        
        // console.log(`강사 이미지 제거 완료: index=${index}`);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('lectureForm');
    const locationTypeInputs = document.querySelectorAll('input[name="location_type"]');
    const offlineFields = document.getElementById('offline-fields');
    const onlineFields = document.getElementById('online-fields');
    
    // 참가비 콤마 처리
    const registrationFeeDisplay = document.getElementById('registration_fee_display');
    const registrationFeeHidden = document.getElementById('registration_fee');
    
    // 숫자에 콤마 추가 함수
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
    
    // 콤마 제거하고 숫자만 반환하는 함수
    function removeCommas(str) {
        return str.replace(/,/g, '');
    }
    
    // 참가비 입력 이벤트
    registrationFeeDisplay.addEventListener('input', function(e) {
        let value = e.target.value;
        
        // 숫자가 아닌 문자 제거 (콤마 제외)
        value = value.replace(/[^\d,]/g, '');
        
        // 콤마 제거
        let numericValue = removeCommas(value);
        
        // 빈 값이면 0으로 설정
        if (numericValue === '') {
            numericValue = '0';
        }
        
        // 숫자로 변환
        let num = parseInt(numericValue);
        if (isNaN(num)) {
            num = 0;
        }
        
        // 콤마 추가해서 표시
        e.target.value = numberWithCommas(num);
        
        // hidden 필드에 실제 숫자값 저장
        registrationFeeHidden.value = num;
    });
    
    // 날짜/시간 필드 클릭 개선
    const dateTimeInputs = document.querySelectorAll('input[type="date"], input[type="time"], input[type="datetime-local"]');
    dateTimeInputs.forEach(input => {
        input.addEventListener('click', function() {
            this.showPicker();
        });
    });
    
    // 복수 강사 관리
    let instructorCount = 1;
    const maxInstructors = 5;
    
    // 강사 추가 버튼 이벤트
    document.getElementById('add-instructor-btn').addEventListener('click', function() {
        if (instructorCount >= maxInstructors) {
            alert('최대 5명까지 강사를 추가할 수 있습니다.');
            return;
        }
        
        addInstructorField();
        updateInstructorButtons();
    });
    
    // 강사 추가 함수
    function addInstructorField() {
        const container = document.getElementById('instructors-container');
        const newInstructor = document.createElement('div');
        newInstructor.className = 'instructor-item';
        newInstructor.setAttribute('data-instructor-index', instructorCount);
        
        newInstructor.innerHTML = `
            <div class="instructor-header">
                <h3>강사 ${instructorCount + 1}</h3>
                <button type="button" class="remove-instructor-btn">
                    <i class="fas fa-times"></i> 제거
                </button>
            </div>
            <!-- 강사 이미지 업로드 -->
            <div class="instructor-image-upload">
                <label class="form-label">강사 프로필 이미지</label>
                <div class="instructor-image-container" onclick="document.getElementById('instructor_image_${instructorCount}').click()">
                    <div class="instructor-image-placeholder">
                        <i class="fas fa-user-circle"></i>
                        <span>클릭하여 이미지 선택</span>
                    </div>
                    <button type="button" class="remove-instructor-image" onclick="removeInstructorImage(${instructorCount})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <input type="file" id="instructor_image_${instructorCount}" name="instructors[${instructorCount}][image]" 
                       style="display: none" accept="image/*" onchange="handleInstructorImage(${instructorCount}, this)">
                <div class="form-help">JPG, PNG, GIF, WebP 파일을 업로드하세요 (최대 2MB)</div>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="instructor_name_${instructorCount}" class="form-label required">강사명</label>
                    <input type="text" id="instructor_name_${instructorCount}" name="instructors[${instructorCount}][name]" class="form-input" 
                           placeholder="예: 김마케팅" required>
                    <div class="form-error" id="instructor_name_${instructorCount}-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="instructor_title_${instructorCount}" class="form-label">직책/전문분야</label>
                    <input type="text" id="instructor_title_${instructorCount}" name="instructors[${instructorCount}][title]" class="form-input" 
                           placeholder="예: 디지털 마케팅 전문가">
                </div>
                
                <div class="form-group full-width">
                    <label for="instructor_info_${instructorCount}" class="form-label">강사 소개</label>
                    <textarea id="instructor_info_${instructorCount}" name="instructors[${instructorCount}][info]" class="form-textarea" 
                              placeholder="강사의 경력, 전문분야, 주요 실적 등을 소개해주세요"></textarea>
                    <div class="form-help">강사의 전문성을 어필할 수 있는 내용을 작성해주세요</div>
                </div>
            </div>
        `;
        
        container.appendChild(newInstructor);
        
        // 제거 버튼 이벤트 추가
        newInstructor.querySelector('.remove-instructor-btn').addEventListener('click', function() {
            removeInstructorField(newInstructor);
        });
        
        instructorCount++;
    }
    
    // 강사 제거 함수
    function removeInstructorField(instructorElement) {
        instructorElement.remove();
        instructorCount--;
        updateInstructorNumbers();
        updateInstructorButtons();
    }
    
    // 강사 번호 업데이트
    function updateInstructorNumbers() {
        const instructors = document.querySelectorAll('.instructor-item');
        instructors.forEach((instructor, index) => {
            const header = instructor.querySelector('.instructor-header h3');
            header.textContent = `강사 ${index + 1}`;
            instructor.setAttribute('data-instructor-index', index);
        });
    }
    
    // 강사 버튼 상태 업데이트
    function updateInstructorButtons() {
        const removeButtons = document.querySelectorAll('.remove-instructor-btn');
        const addButton = document.getElementById('add-instructor-btn');
        
        // 강사가 1명일 때는 제거 버튼 숨김
        if (instructorCount <= 1) {
            removeButtons.forEach(btn => btn.style.display = 'none');
        } else {
            removeButtons.forEach(btn => btn.style.display = 'inline-block');
        }
        
        // 최대 강사 수에 도달하면 추가 버튼 비활성화
        if (instructorCount >= maxInstructors) {
            addButton.disabled = true;
            addButton.textContent = '최대 강사 수에 도달했습니다';
        } else {
            addButton.disabled = false;
            addButton.innerHTML = '<i class="fas fa-plus"></i> 강사 추가';
        }
    }
    
    // 소요시간 자동 계산 (날짜와 시간 모두 고려)
    function calculateDuration() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const durationText = document.getElementById('duration-text');
        
        // console.log('calculateDuration 호출됨:', { startDate, endDate, startTime, endTime });
        
        if (!durationText) {
            console.error('duration-text 요소를 찾을 수 없습니다');
            return;
        }
        
        if (startDate && endDate && startTime && endTime) {
            // 날짜와 시간을 결합하여 Date 객체 생성
            const start = new Date(`${startDate}T${startTime}`);
            const end = new Date(`${endDate}T${endTime}`);
            
            // console.log('날짜/시간 계산:', { start, end });
            
            if (end > start) {
                const diffMs = end - start;
                const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                
                let durationStr = '';
                if (diffDays > 0) {
                    durationStr += `${diffDays}일`;
                }
                if (diffHours > 0) {
                    if (durationStr) durationStr += ' ';
                    durationStr += `${diffHours}시간`;
                }
                if (diffMinutes > 0) {
                    if (durationStr) durationStr += ' ';
                    durationStr += `${diffMinutes}분`;
                }
                
                const finalDuration = durationStr || '0분';
                durationText.textContent = finalDuration;
                durationText.style.color = '#0369a1';
                // console.log('소요시간 계산 완료:', finalDuration);
            } else if (end.getTime() === start.getTime()) {
                durationText.textContent = '시작과 종료가 같습니다';
                durationText.style.color = '#f59e0b';
                // console.log('시간 동일: 시작과 종료가 같음');
            } else {
                durationText.textContent = '종료 날짜/시간이 시작 날짜/시간보다 늦어야 합니다';
                durationText.style.color = '#dc2626';
                // console.log('시간 오류: 종료가 시작보다 빠름');
            }
        } else {
            const missingFields = [];
            if (!startDate) missingFields.push('시작 날짜');
            if (!endDate) missingFields.push('종료 날짜');
            if (!startTime) missingFields.push('시작 시간');
            if (!endTime) missingFields.push('종료 시간');
            
            durationText.textContent = `${missingFields.join(', ')}을(를) 입력하세요`;
            durationText.style.color = '#64748b';
            // console.log('입력 대기 중:', missingFields);
        }
    }
    
    // 날짜와 시간 입력 이벤트 리스너 (실시간 업데이트)
    const startDateElement = document.getElementById('start_date');
    const endDateElement = document.getElementById('end_date');
    const startTimeElement = document.getElementById('start_time');
    const endTimeElement = document.getElementById('end_time');
    
    if (startDateElement && endDateElement && startTimeElement && endTimeElement) {
        // console.log('소요시간 계산 이벤트 리스너 등록 중...');
        
        // 날짜 변경 이벤트
        startDateElement.addEventListener('change', calculateDuration);
        startDateElement.addEventListener('input', calculateDuration);
        endDateElement.addEventListener('change', calculateDuration);
        endDateElement.addEventListener('input', calculateDuration);
        
        // 시간 변경 이벤트
        startTimeElement.addEventListener('change', calculateDuration);
        startTimeElement.addEventListener('input', calculateDuration);
        endTimeElement.addEventListener('change', calculateDuration);
        endTimeElement.addEventListener('input', calculateDuration);
        
        // console.log('소요시간 계산 이벤트 리스너 등록 완료');
    } else {
        console.error('날짜/시간 입력 요소를 찾을 수 없습니다:', { 
            startDateElement, endDateElement, startTimeElement, endTimeElement 
        });
    }
    
    // 페이지 로딩 시 기존 값이 있다면 한 번 계산
    // console.log('초기 소요시간 계산 실행...');
    calculateDuration();
    
    // 강의 이미지 업로드 관리 (변수들은 이미 전역에서 선언됨)
    
    function initLectureImageUpload() {
        const uploadArea = document.getElementById('lectureImageUploadArea');
        const fileInput = document.getElementById('lecture_images');
        const previewContainer = document.getElementById('lectureImagePreview');
        
        if (!uploadArea || !fileInput || !previewContainer) {
            return;
        }
        
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            // 현재 전체 이미지 수 계산 (기존 이미지 + 새 이미지)
            const existingImageCount = (Array.isArray(currentImageData) ? currentImageData.length : 0);
            const totalImageCount = existingImageCount + lectureImages.length;
            
            if (totalImageCount + files.length > maxLectureImages) {
                alert(`최대 ${maxLectureImages}장까지 업로드할 수 있습니다. (현재: ${totalImageCount}장)`);
                return;
            }
            
            files.forEach(file => {
                if (validateLectureImageFile(file)) {
                    addLectureImagePreview(file);
                }
            });
        });
        
        // 드래그 앤 드롭 지원
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#667eea';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#e2e8f0';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#e2e8f0';
            
            const files = Array.from(e.dataTransfer.files);
            
            // 현재 전체 이미지 수 계산 (기존 이미지 + 새 이미지)
            const existingImageCount = (Array.isArray(currentImageData) ? currentImageData.length : 0);
            const totalImageCount = existingImageCount + lectureImages.length;
            
            if (totalImageCount + files.length > maxLectureImages) {
                alert(`최대 ${maxLectureImages}장까지 업로드할 수 있습니다. (현재: ${totalImageCount}장)`);
                return;
            }
            
            files.forEach(file => {
                if (validateLectureImageFile(file)) {
                    addLectureImagePreview(file);
                }
            });
        });
    }
    
    function validateLectureImageFile(file) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('JPG, PNG, GIF, WebP 파일만 업로드 가능합니다.');
            return false;
        }
        
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (file.size > maxSize) {
            alert('파일 크기는 5MB 이하여야 합니다.');
            return false;
        }
        
        return true;
    }
    
    function addLectureImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.getElementById('lectureImagePreview');
            const currentOrder = previewContainer.querySelectorAll('.lecture-image-item').length + 1;
            
            const imageItem = document.createElement('div');
            imageItem.className = 'lecture-image-item new-image';
            imageItem.draggable = true;
            imageItem.dataset.fileIndex = lectureImages.length;
            imageItem.innerHTML = `
                <div class="image-container">
                    <img src="${e.target.result}" alt="강의 이미지">
                    <div class="drag-handle">
                        <i class="fas fa-grip-lines"></i>
                    </div>
                    <div class="image-order">${currentOrder}</div>
                    <button type="button" class="remove-lecture-image">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="image-info">
                    <div style="font-size: 12px; color: #666; margin-bottom: 2px;">
                        ${file.name}
                    </div>
                    <div style="font-size: 11px; color: #999;">
                        새로 업로드된 이미지
                    </div>
                </div>
            `;
            
            // 삭제 버튼 이벤트
            const removeBtn = imageItem.querySelector('.remove-lecture-image');
            removeBtn.addEventListener('click', function() {
                const index = lectureImages.indexOf(file);
                if (index > -1) {
                    lectureImages.splice(index, 1);
                }
                imageItem.remove();
                updateImageOrderNumbers();
                updateImageUploadPlaceholder();
                updateSortableContainerState();
            });
            
            // 드래그 이벤트 추가
            setupImageDragEvents(imageItem);
            
            previewContainer.appendChild(imageItem);
            lectureImages.push(file);
            updateImageOrderNumbers();
            updateImageUploadPlaceholder();
            updateSortableContainerState();
        };
        reader.readAsDataURL(file);
    }
    
    // 드래그&드롭 관련 함수들
    function setupImageDragEvents(imageItem) {
        imageItem.addEventListener('dragstart', function(e) {
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
            e.dataTransfer.setData('text/plain', this.dataset.fileIndex || '');
            
            document.getElementById('lectureImagePreview').classList.add('drag-active');
        });
        
        imageItem.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            document.getElementById('lectureImagePreview').classList.remove('drag-active');
            
            // 모든 drop-zone 클래스 제거
            document.querySelectorAll('.lecture-image-item').forEach(item => {
                item.classList.remove('drag-over', 'drop-zone');
            });
        });
        
        imageItem.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            
            // 드래그 중인 요소가 아닌 경우에만 hover 효과 적용
            if (!this.classList.contains('dragging')) {
                this.classList.add('drag-over');
            }
        });
        
        imageItem.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        
        imageItem.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            // 자기 자신에게 드롭하는 경우 무시
            if (this.classList.contains('dragging')) {
                return;
            }
            
            const draggedElement = document.querySelector('.lecture-image-item.dragging');
            if (draggedElement && draggedElement !== this) {
                reorderImages(draggedElement, this);
            }
        });
    }
    
    function reorderImages(draggedElement, targetElement) {
        const container = document.getElementById('lectureImagePreview');
        const allImages = Array.from(container.querySelectorAll('.lecture-image-item'));
        
        // 현재 위치 계산
        const draggedIndex = allImages.indexOf(draggedElement);
        const targetIndex = allImages.indexOf(targetElement);
        
        if (draggedIndex === -1 || targetIndex === -1) return;
        
        // DOM에서 요소 순서 변경
        if (draggedIndex < targetIndex) {
            container.insertBefore(draggedElement, targetElement.nextSibling);
        } else {
            container.insertBefore(draggedElement, targetElement);
        }
        
        // 드래그&드롭 시에는 lectureImages 배열과 dataset.fileIndex를 변경하지 않음
        // DOM 순서만 변경하고, 원본 파일과의 연결은 유지
        // 나중에 form 제출 시 DOM 순서(display_order)와 원본 인덱스(temp_index)를 함께 전송
        // console.log('드래그&드롭: DOM 순서만 변경, 파일 배열과 인덱스는 원본 유지');
        
        // 순서 번호 업데이트
        updateImageOrderNumbers();
        
        // 드래그&드롭 후 DOM 순서 확인
        const finalOrder = Array.from(container.querySelectorAll('.lecture-image-item')).map((item, idx) => ({
            domIndex: idx + 1,
            fileIndex: item.dataset.fileIndex,
            className: item.className,
            orderText: item.querySelector('.image-order')?.textContent,
            imageName: item.querySelector('.image-info div')?.textContent
        }));
        
        // console.log('=== 드래그&드롭 완료 후 DOM 순서 ===');
        // console.log('이미지 순서 변경 완료:', {
        //     draggedIndex,
        //     targetIndex,
        //     finalDomOrder: finalOrder
        // });
        // console.log('현재 DOM 순서:', finalOrder);
    }
    
    function updateImageOrderNumbers() {
        const imageItems = document.querySelectorAll('.lecture-image-item');
        imageItems.forEach((item, index) => {
            const orderElement = item.querySelector('.image-order');
            if (orderElement) {
                orderElement.textContent = index + 1;
            }
        });
    }
    
    function updateFileIndexes() {
        // 드래그&드롭 후에는 dataset.fileIndex를 변경하지 않음
        // 원본 파일과의 연결을 유지하기 위해 이 함수는 드래그&드롭에서 호출되지 않음
        // console.log('updateFileIndexes: 드래그&드롭에서는 호출되지 않아야 함');
    }
    
    function updateSortableContainerState() {
        const container = document.getElementById('lectureImagePreview');
        const dragInstructions = container.querySelector('.drag-instructions');
        const imageItems = container.querySelectorAll('.lecture-image-item');
        
        if (imageItems.length > 1) {
            container.classList.add('has-images');
            if (dragInstructions) {
                dragInstructions.style.display = 'block';
            }
        } else {
            container.classList.remove('has-images');
            if (dragInstructions) {
                dragInstructions.style.display = 'none';
            }
        }
    }
    
    // 강의 이미지 업로드 초기화
    initLectureImageUpload();
    
    // 위치 타입 변경 시 필드 표시/숨김
    function toggleLocationFields() {
        const selectedType = document.querySelector('input[name="location_type"]:checked');
        if (!selectedType) return;
        
        offlineFields.classList.remove('active');
        onlineFields.classList.remove('active');
        
        switch (selectedType.value) {
            case 'offline':
                offlineFields.classList.add('active');
                break;
            case 'online':
                onlineFields.classList.add('active');
                break;
        }
    }
    
    // 초기 설정
    toggleLocationFields();
    
    // 위치 타입 변경 이벤트
    locationTypeInputs.forEach(input => {
        input.addEventListener('change', toggleLocationFields);
    });
    
    
    // 날짜 유효성 검사
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    function validateDates() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startDate && endDate && startDate > endDate) {
            showError('end_date', '종료 날짜는 시작 날짜보다 늦어야 합니다.');
            return false;
        }
        
        clearError('end_date');
        return true;
    }
    
    function validateTimes() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        if (startTime && endTime && startDate === endDate && startTime >= endTime) {
            showError('end_time', '종료 시간은 시작 시간보다 늦어야 합니다.');
            return false;
        }
        
        clearError('end_time');
        return true;
    }
    
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
    startTimeInput.addEventListener('change', validateTimes);
    endTimeInput.addEventListener('change', validateTimes);
    
    // 실시간 필드 검증
    function addRealTimeValidation() {
        // 제목 검증
        const titleInput = document.getElementById('title');
        titleInput.addEventListener('blur', function() {
            if (this.value.trim().length === 0) {
                showError('title', '강의 제목을 입력해주세요.');
            } else if (this.value.trim().length < 5) {
                showError('title', '강의 제목은 5자 이상 입력해주세요.');
            } else {
                clearError('title');
            }
        });
        
        // 제목 입력 시 실시간 에러 제거
        titleInput.addEventListener('input', function() {
            if (this.value.trim().length > 0) {
                clearError('title');
            }
        });
        
        // 설명 검증
        const descriptionInput = document.getElementById('description');
        descriptionInput.addEventListener('blur', function() {
            if (this.value.trim().length === 0) {
                showError('description', '강의 설명을 입력해주세요.');
            } else if (this.value.trim().length < 20) {
                showError('description', '강의 설명은 20자 이상 입력해주세요.');
            } else {
                clearError('description');
            }
        });
        
        // 첫 번째 강사명 검증
        const firstInstructorInput = document.getElementById('instructor_name_0');
        if (firstInstructorInput) {
            firstInstructorInput.addEventListener('blur', function() {
                if (this.value.trim().length === 0) {
                    showError('instructor_name_0', '강사명을 입력해주세요.');
                } else {
                    clearError('instructor_name_0');
                }
            });
        }
        
        // 온라인 링크 검증
        const onlineLinkInput = document.getElementById('online_link');
        if (onlineLinkInput) {
            onlineLinkInput.addEventListener('blur', function() {
                const locationType = document.querySelector('input[name="location_type"]:checked');
                if (locationType && locationType.value === 'online') {
                    if (this.value.trim().length === 0) {
                        showError('online_link', '온라인 링크를 입력해주세요.');
                    } else if (!isValidUrl(this.value)) {
                        showError('online_link', '올바른 URL 형식을 입력해주세요.');
                    } else {
                        clearError('online_link');
                    }
                }
            });
        }
        
        // 장소명 검증
        const venueInput = document.getElementById('venue_name');
        if (venueInput) {
            venueInput.addEventListener('blur', function() {
                const locationType = document.querySelector('input[name="location_type"]:checked');
                if (locationType && locationType.value === 'offline') {
                    if (this.value.trim().length === 0) {
                        showError('venue_name', '장소명을 입력해주세요.');
                    } else {
                        clearError('venue_name');
                    }
                }
            });
        }
        
        // 참가자 수 검증
        const maxParticipantsInput = document.getElementById('max_participants');
        if (maxParticipantsInput) {
            maxParticipantsInput.addEventListener('blur', function() {
                if (this.value && parseInt(this.value) < 1) {
                    showError('max_participants', '최대 참가자 수는 1명 이상이어야 합니다.');
                } else {
                    clearError('max_participants');
                }
            });
        }
        
        // YouTube URL 검증
        const youtubeInput = document.getElementById('youtube_video');
        if (youtubeInput) {
            youtubeInput.addEventListener('blur', function() {
                if (this.value && !isValidYouTubeUrl(this.value)) {
                    showError('youtube_video', '올바른 YouTube URL을 입력해주세요.');
                } else {
                    clearError('youtube_video');
                }
            });
        }
    }
    
    // URL 유효성 검사 함수
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    // YouTube URL 유효성 검사 함수
    function isValidYouTubeUrl(url) {
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/;
        return youtubeRegex.test(url);
    }
    
    // 실시간 검증 활성화
    addRealTimeValidation();
    
    // 이미지 업로드 기능
    function initImageUpload() {
        const uploadArea = document.getElementById('imageUploadArea');
        const fileInput = document.getElementById('instructor_image');
        const placeholder = document.getElementById('uploadPlaceholder');
        const preview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const removeBtn = document.getElementById('removeImage');
        
        // 강사 프로필 이미지 요소들이 존재하지 않으면 함수 종료
        if (!uploadArea || !fileInput || !placeholder || !preview || !previewImg || !removeBtn) {
            return;
        }
        
        // 업로드 영역 클릭 시 파일 선택
        uploadArea.addEventListener('click', function() {
            fileInput.click();
        });
        
        // 파일 선택 시 미리보기
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // 파일 유효성 검사
                if (!validateImageFile(file)) {
                    return;
                }
                
                // 미리보기 표시
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    placeholder.style.display = 'none';
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
                
                clearError('instructor_image');
            }
        });
        
        // 이미지 제거
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            fileInput.value = '';
            placeholder.style.display = 'block';
            preview.style.display = 'none';
            previewImg.src = '';
        });
        
        // 드래그 앤 드롭 지원
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#667eea';
            uploadArea.style.background = '#f1f5f9';
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#e2e8f0';
            uploadArea.style.background = '#f8fafc';
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.style.borderColor = '#e2e8f0';
            uploadArea.style.background = '#f8fafc';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (validateImageFile(file)) {
                    fileInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            }
        });
    }
    
    // 이미지 파일 유효성 검사
    function validateImageFile(file) {
        // 파일 형식 검사
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('JPG, PNG, GIF, WebP 파일만 업로드 가능합니다.');
            return false;
        }
        
        // 파일 크기 검사 (5MB)
        const maxSize = 5 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('파일 크기는 5MB 이하여야 합니다.');
            return false;
        }
        
        return true;
    }
    
    // 이미지 업로드 초기화
    initImageUpload();
    
    // 클릭된 버튼 추적
    let clickedButton = null;
    
    // 모든 submit 버튼에 클릭 이벤트 추가
    const submitButtons = form.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(button => {
        button.addEventListener('click', function() {
            clickedButton = this;
        });
    });
    
    // 폼 제출 처리
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // 유효성 검사
        if (!validateForm()) {
            return;
        }
        
        // 로딩 상태 표시
        showLoading(true);
        
        // 폼 데이터 수집
        const formData = new FormData(form);
        
        // 드래그&드롭으로 변경된 이미지 순서를 수집하여 FormData에 추가
        // console.log('=== 폼 제출 시 이미지 순서 수집 ===');
        
        // 현재 화면에 표시된 이미지 순서대로 데이터 수집
        const imagePreviewContainer = document.getElementById('lectureImagePreview');
        const orderedImageData = [];
        
        if (imagePreviewContainer) {
            const imageItems = imagePreviewContainer.querySelectorAll('.lecture-image-item');
            // console.log('화면에 표시된 이미지 개수:', imageItems.length);
            
            // console.log('=== 드래그&드롭 순서 디버깅 시작 ===');
            // console.log('DOM에서 발견된 이미지 순서:', Array.from(imageItems).map((item, idx) => ({
            //     domIndex: idx + 1,
            //     classList: item.className,
            //     fileIndex: item.dataset.fileIndex,
            //     orderNumber: item.querySelector('.image-order')?.textContent
            // })));
            
            imageItems.forEach((item, index) => {
                const actualOrder = index + 1; // DOM에서의 실제 순서
                // console.log(`처리 중: DOM 순서 ${actualOrder}, 클래스: ${item.className}`);
                
                if (item.classList.contains('existing-image')) {
                    // 기존 이미지 (임시저장된 이미지)
                    const img = item.querySelector('img');
                    const infoDiv = item.querySelector('.image-info div');
                    if (img && infoDiv) {
                        const imageData = {
                            file_path: img.src,
                            original_name: infoDiv.textContent.trim(),
                            file_name: img.src.split('/').pop(),
                            is_existing: true,
                            display_order: actualOrder
                        };
                        orderedImageData.push(imageData);
                        // console.log(`기존 이미지 DOM 순서 ${actualOrder}:`, imageData);
                    }
                } else if (item.classList.contains('new-image')) {
                    // 새로 업로드된 이미지 - 안전한 식별자 사용
                    const fileIndex = parseInt(item.dataset.fileIndex);
                    // console.log(`새 이미지: DOM 순서 ${actualOrder}, fileIndex ${fileIndex}`);
                    
                    if (fileIndex >= 0 && fileIndex < lectureImages.length) {
                        const file = lectureImages[fileIndex];
                        const imageData = {
                            original_name: `temp_${Date.now()}_${fileIndex}`,  // 임시 안전한 이름
                            file_name: `temp_${Date.now()}_${fileIndex}`,     // 서버에서 실제 파일명으로 매칭
                            file_size: file.size,
                            is_new: true,
                            display_order: actualOrder,  // DOM에서의 실제 순서 사용
                            temp_index: fileIndex  // 서버에서 매칭용
                        };
                        orderedImageData.push(imageData);
                        // console.log(`새 이미지 DOM 순서 ${actualOrder}:`, imageData);
                    }
                }
            });
            // console.log('=== 최종 정렬된 이미지 데이터 ===');
            // console.log('orderedImageData:', orderedImageData);
        }
        
        // 드래그&드롭으로 정렬된 이미지 순서를 서버로 전달
        if (orderedImageData.length > 0) {
            formData.append('ordered_lecture_images', JSON.stringify(orderedImageData));
            // console.log('정렬된 이미지 데이터 전송:', orderedImageData);
        }
        
        // 기존 로직도 유지 (호환성을 위해)
        // console.log('=== 기존 이미지 데이터 호환성 처리 ===');
        // console.log('currentImageData 타입:', typeof currentImageData);
        // console.log('currentImageData 값:', currentImageData);
        
        if (typeof currentImageData !== 'undefined' && Array.isArray(currentImageData) && currentImageData.length > 0) {
            formData.append('existing_lecture_images', JSON.stringify(currentImageData));
            // console.log('기존 강의 이미지 정보 추가:', currentImageData);
        } else {
            // 히든 필드에서 데이터 가져오기 (만약 currentImageData가 비어있다면)
            const hiddenField = document.querySelector('#existing_lecture_images_hidden');
            if (hiddenField && hiddenField.value) {
                try {
                    const hiddenData = JSON.parse(hiddenField.value);
                    if (hiddenData && hiddenData.length > 0) {
                        formData.append('existing_lecture_images', hiddenField.value);
                        // console.log('히든 필드에서 기존 강의 이미지 정보 추가:', hiddenData);
                    } else {
                        // console.log('기존 강의 이미지 정보가 없거나 비어있음');
                    }
                } catch (e) {
                    // console.log('히든 필드 데이터 파싱 오류:', e);
                    // console.log('기존 강의 이미지 정보가 없거나 비어있음');
                }
            } else {
                // console.log('기존 강의 이미지 정보가 없거나 비어있음');
            }
        }
        
        // 클릭된 버튼의 name과 value를 FormData에 추가
        if (clickedButton && clickedButton.name && clickedButton.value) {
            formData.append(clickedButton.name, clickedButton.value);
        }
        
        // 상세 디버깅: 폼 데이터 로깅
        // console.log('=== 폼 제출 데이터 상세 분석 ===');
        // console.log('클릭된 버튼:', clickedButton ? clickedButton.name + '=' + clickedButton.value : 'NONE');
        // console.log('핵심 필드 값 확인:');
        // console.log('- registration_deadline:', formData.get('registration_deadline') || 'EMPTY');
        // console.log('- youtube_video:', formData.get('youtube_video') || 'EMPTY');
        // console.log('- status:', formData.get('status') || 'EMPTY');
        // console.log('- title:', formData.get('title') || 'EMPTY');
        
        // 모든 폼 데이터 출력
        // console.log('전체 FormData 내용:');
        for (let [key, value] of formData.entries()) {
            // console.log(`${key}: ${value}`);
        }
        
        // AJAX 제출
        fetch('/lectures/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // 서버 응답 상태 확인
            if (!response.ok) {
                throw new Error(`서버 오류: ${response.status} ${response.statusText}`);
            }
            
            // JSON 응답 검증
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('서버에서 올바르지 않은 응답을 받았습니다.');
            }
            
            return response.json();
        })
        .then(data => {
            showLoading(false);
            
            // 응답 데이터 검증
            if (typeof data !== 'object' || data === null) {
                throw new Error('서버에서 올바르지 않은 데이터를 받았습니다.');
            }
            
            if (data.success) {
                // 성공 메시지 표시
                showSuccessMessage(data.message);
                hasUnsavedChanges = false;
                
                if (data.isDraft) {
                    // 임시저장인 경우 현재 페이지에 머물기
                    // console.log('임시저장 완료, 강의 ID:', data.lectureId);
                    
                    // 임시저장 후 최신 이미지 데이터로 업데이트
                    // console.log('=== 임시저장 응답 처리 시작 ===');
                    // console.log('data.debug 존재:', !!data.debug);
                    // console.log('data.debug.update_binding 존재:', !!(data.debug && data.debug.update_binding));
                    // console.log('data.debug.update_binding.params 존재:', !!(data.debug && data.debug.update_binding && data.debug.update_binding.params));
                    
                    if (data.debug && data.debug.update_binding && data.debug.update_binding.params) {
                        const updatedImages = data.debug.update_binding.params;
                        // console.log('서버에서 받은 업데이트된 이미지 데이터:', updatedImages);
                        // console.log('업데이트된 이미지 개수:', updatedImages.length);
                        // console.log('각 이미지 상세 정보:');
                        updatedImages.forEach((img, idx) => {
                            // console.log(`이미지 ${idx}:`, img.original_name, img.file_path);
                        });
                        
                        // 전역 변수 업데이트
                        const previousCount = currentImageData ? currentImageData.length : 0;
                        currentImageData = [...updatedImages];
                        // console.log(`currentImageData 업데이트: ${previousCount}개 -> ${currentImageData.length}개`);
                        
                        // 히든 필드도 즉시 업데이트하여 다음 제출 시 올바른 데이터가 전송되도록 함
                        const hiddenField = document.querySelector('#existing_lecture_images_hidden');
                        if (hiddenField) {
                            hiddenField.value = JSON.stringify(currentImageData);
                            // console.log('히든 필드 업데이트 완료:', currentImageData.length + '개 이미지');
                        } else {
                            console.warn('히든 필드를 찾을 수 없음');
                        }
                        
                        // 화면과 데이터 동기화 확인 후 필요시 화면 업데이트
                        let currentScreenImages = document.querySelectorAll('.lecture-image-preview');
                        // console.log('화면 업데이트 전 - 화면에 보이는 이미지 개수:', currentScreenImages.length);
                        // console.log('currentImageData 이미지 개수:', currentImageData.length);
                        
                        if (currentScreenImages.length !== updatedImages.length) {
                            // console.log('화면과 데이터가 불일치하므로 화면을 강제 업데이트합니다.');
                            // 강의 이미지 화면 업데이트
                            updateLectureImagesDisplay(updatedImages);
                            
                            // 화면 업데이트 후 다시 확인
                            currentScreenImages = document.querySelectorAll('.lecture-image-preview');
                            // console.log('화면 업데이트 후 - 화면에 보이는 이미지 개수:', currentScreenImages.length);
                        } else {
                            // console.log('화면과 데이터가 일치하므로 화면 업데이트를 건너뜁니다.');
                        }
                        
                        // console.log('이미지 데이터 업데이트 완료:', currentImageData);
                        
                        // 최종 동기화 상태 확인
                        if (currentScreenImages.length !== currentImageData.length) {
                            console.error('⚠️ 최종 확인: 화면과 데이터가 불일치! 화면:', currentScreenImages.length, 'vs 데이터:', currentImageData.length);
                        } else {
                            // console.log('✅ 최종 확인: 화면과 데이터가 일치');
                        }
                    } else {
                        // console.log('⚠️ 서버 응답에 이미지 데이터가 없음');
                        // console.log('data.debug:', data.debug);
                    }
                    
                    // 디버깅 정보 출력
                    if (data.debug) {
                        // console.log('=== 서버 디버그 정보 ===');
                        // console.log('POST registration_deadline:', data.debug.post_registration_deadline);
                        // console.log('POST youtube_video:', data.debug.post_youtube_video);
                        // console.log('검증된 registration_deadline:', data.debug.validated_registration_deadline);
                        // console.log('검증된 youtube_video:', data.debug.validated_youtube_video);
                    }
                } else {
                    // 정식 등록인 경우 리다이렉트
                    setTimeout(() => {
                        if (isEditMode && data.lectureId) {
                            // 수정 모드인 경우 강의 상세 페이지의 수정 모드로 리다이렉트
                            window.location.href = `/lectures/${data.lectureId}/edit`;
                        } else if (data.lectureId) {
                            // 새 강의 등록인 경우 강의 상세 페이지의 수정 모드로 리다이렉트
                            window.location.href = `/lectures/${data.lectureId}/edit`;
                        } else {
                            // 기본 리다이렉트
                            window.location.href = data.redirectUrl || '/lectures';
                        }
                    }, 1500);
                }
            } else {
                // 서버 검증 오류 처리
                if (data.errors && Array.isArray(data.errors)) {
                    showFieldErrors(data.errors);
                } else {
                    showErrorMessage(data.message || '강의 등록 중 오류가 발생했습니다.');
                }
            }
        })
        .catch(error => {
            console.error('폼 제출 오류:', error);
            showLoading(false);
            
            // 네트워크 오류 타입별 처리
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                showErrorMessage('네트워크 연결을 확인해주세요.');
            } else if (error.message.includes('서버 오류')) {
                showErrorMessage('서버에 일시적인 문제가 발생했습니다. 잠시 후 다시 시도해주세요.');
            } else {
                showErrorMessage(error.message || '강의 등록 중 예상치 못한 오류가 발생했습니다.');
            }
        });
    });
    
    // 유효성 검사 함수
    function validateForm() {
        let isValid = true;
        
        // 필수 필드 검사
        const requiredFields = [
            'title', 'description',
            'start_date', 'end_date', 'start_time', 'end_time'
        ];
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field && !field.value.trim()) {
                showError(fieldName, '이 필드는 필수입니다.');
                isValid = false;
            } else if (field) {
                clearError(fieldName);
            }
        });
        
        // 첫 번째 강사명 필수 검사
        const firstInstructor = document.getElementById('instructor_name_0');
        if (firstInstructor && !firstInstructor.value.trim()) {
            showError('instructor_name_0', '강사명을 입력해주세요.');
            isValid = false;
        } else if (firstInstructor) {
            clearError('instructor_name_0');
        }
        
        // 날짜/시간 검사
        if (!validateDates() || !validateTimes()) {
            isValid = false;
        }
        
        // 위치 타입별 필수 필드 검사
        const locationType = document.querySelector('input[name="location_type"]:checked');
        if (locationType) {
            if (locationType.value === 'offline') {
                const venueField = document.getElementById('venue_name');
                if (venueField && !venueField.value.trim()) {
                    alert('오프라인 진행 시 장소명은 필수입니다.');
                    venueField.focus();
                    isValid = false;
                }
            }
            
            if (locationType.value === 'online') {
                const linkField = document.getElementById('online_link');
                if (linkField && !linkField.value.trim()) {
                    alert('온라인 진행 시 온라인 링크는 필수입니다.');
                    linkField.focus();
                    isValid = false;
                }
            }
        }
        
        return isValid;
    }
    
    // 오류 표시 함수
    function showError(fieldName, message) {
        const errorElement = document.getElementById(fieldName + '-error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
        
        const field = document.getElementById(fieldName);
        if (field) {
            field.style.borderColor = '#e53e3e';
        }
    }
    
    function clearError(fieldName) {
        const errorElement = document.getElementById(fieldName + '-error');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
        
        const field = document.getElementById(fieldName);
        if (field) {
            field.style.borderColor = '#e2e8f0';
        }
    }
    
    // 로딩 상태 함수
    function showLoading(show) {
        const loading = document.getElementById('loading');
        const buttons = form.querySelectorAll('button[type="submit"]');
        
        loading.style.display = show ? 'block' : 'none';
        buttons.forEach(btn => {
            btn.disabled = show;
        });
    }
    
    // 성공 메시지 표시
    function showSuccessMessage(message) {
        // 기존 메시지 제거
        const existingMsg = document.querySelector('.success-notification');
        if (existingMsg) existingMsg.remove();
        
        const successDiv = document.createElement('div');
        successDiv.className = 'success-notification';
        successDiv.innerHTML = `
            <div style="background: #10b981; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                ${message}
            </div>
        `;
        form.insertBefore(successDiv, form.firstChild);
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // 강사 이미지 로딩 함수 (전역 함수로 정의)
    window.loadInstructorImage = function(index, imagePath) {
        // console.log(`강사 ${index} 이미지 로딩 시도:`, imagePath);
        
        const fileInput = document.querySelector(`#instructor_image_${index}`);
        if (!fileInput) {
            console.error(`강사 이미지 입력 요소를 찾을 수 없습니다: #instructor_image_${index}`);
            return;
        }
        
        const uploadDiv = fileInput.closest('.instructor-image-upload');
        const container = uploadDiv ? uploadDiv.querySelector('.instructor-image-container') : null;
        if (!container) {
            console.error('이미지 컨테이너를 찾을 수 없습니다');
            return;
        }
        
        const placeholder = container.querySelector('.instructor-image-placeholder');
        const removeBtn = container.querySelector('.remove-instructor-image');
        
        // 기존 이미지가 있으면 제거
        const existingImg = container.querySelector('.instructor-image-preview');
        if (existingImg) {
            existingImg.remove();
        }
        
        // 새 이미지 추가
        const img = document.createElement('img');
        img.className = 'instructor-image-preview';
        img.src = imagePath;
        img.style.width = '100%';
        img.style.height = '100%';
        img.style.objectFit = 'cover';
        img.style.borderRadius = '8px';
        
        // placeholder 숨기고 이미지 표시
        if (placeholder) placeholder.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'block';
        container.appendChild(img);
        container.classList.add('has-image');
        
        // console.log(`강사 ${index} 이미지 로딩 완료:`, imagePath);
    };
    
    // 에러 메시지 표시
    function showErrorMessage(message) {
        // 기존 메시지 제거
        const existingMsg = document.querySelector('.error-notification');
        if (existingMsg) existingMsg.remove();
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-notification';
        errorDiv.innerHTML = `
            <div style="background: #ef4444; color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                <i class="fas fa-exclamation-triangle" style="margin-right: 8px;"></i>
                ${message}
            </div>
        `;
        form.insertBefore(errorDiv, form.firstChild);
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // 필드별 에러 표시
    function showFieldErrors(errors) {
        // 기존 에러 초기화
        document.querySelectorAll('.form-error').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => {
            el.style.borderColor = '#e2e8f0';
        });
        
        // 에러 메시지 표시
        errors.forEach(error => {
            showErrorMessage(error);
        });
    }
    
    // 자동 저장 (임시저장) 기능
    let autoSaveTimeout;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // 여기에 자동 저장 로직 추가 가능
                // console.log('자동 저장 가능한 상태');
            }, 30000); // 30초 후 자동 저장
        });
    });
    
    // 페이지 이탈 경고
    let hasUnsavedChanges = false;
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            hasUnsavedChanges = true;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = '작성 중인 내용이 있습니다. 정말 페이지를 떠나시겠습니까?';
        }
    });
    
    form.addEventListener('submit', function() {
        hasUnsavedChanges = false;
    });
    
    // 임시저장된 데이터가 있는 경우 처리
    <?php if ($draftLecture): ?>
    if (confirm('임시저장된 내용이 있습니다. 이어서 진행하시겠습니까?')) {
        loadDraftData();
    }
    
    function loadDraftData() {
        const draftData = <?php echo json_encode($draftLecture, JSON_UNESCAPED_UNICODE); ?>;
        
        // console.log('=== 임시저장 데이터 확인 ===');
        // console.log('전체 draftData:', draftData);
        // console.log('prerequisites:', draftData ? draftData.prerequisites : 'NO DATA');
        // console.log('what_to_bring:', draftData ? draftData.what_to_bring : 'NO DATA');
        // console.log('additional_info:', draftData ? draftData.additional_info : 'NO DATA');
        // console.log('benefits:', draftData ? draftData.benefits : 'NO DATA');
        
        if (!draftData) return; // draftData가 null이면 함수 종료
        
        // 기본 정보 채우기
        if (draftData.title) {
            const titleEl = document.getElementById('title');
            if (titleEl) titleEl.value = draftData.title;
        }
        if (draftData.description) {
            const descEl = document.getElementById('description');
            if (descEl) descEl.value = draftData.description;
        }
        if (draftData.category) {
            const catEl = document.getElementById('category');
            if (catEl) catEl.value = draftData.category;
        }
        
        // 일정 정보 채우기
        if (draftData.start_date) {
            const startDateEl = document.getElementById('start_date');
            if (startDateEl) startDateEl.value = draftData.start_date;
        }
        if (draftData.end_date) {
            const endDateEl = document.getElementById('end_date');
            if (endDateEl) endDateEl.value = draftData.end_date;
        }
        if (draftData.start_time) {
            const startTimeEl = document.getElementById('start_time');
            if (startTimeEl) startTimeEl.value = draftData.start_time;
        }
        if (draftData.end_time) {
            const endTimeEl = document.getElementById('end_time');
            if (endTimeEl) endTimeEl.value = draftData.end_time;
        }
        if (draftData.timezone) {
            const timezoneEl = document.getElementById('timezone');
            if (timezoneEl) timezoneEl.value = draftData.timezone;
        }
        
        // 장소 정보 채우기
        if (draftData.location_type) {
            const locationTypeEl = document.getElementById('location_type');
            if (locationTypeEl) locationTypeEl.value = draftData.location_type;
            
            // 위치 타입 버튼 업데이트
            document.querySelectorAll('.location-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.type === draftData.location_type) {
                    btn.classList.add('active');
                }
            });
            // 해당 필드 표시
            document.querySelectorAll('.location-fields').forEach(field => field.classList.remove('active'));
            const targetFieldsEl = document.getElementById(draftData.location_type + '-fields');
            if (targetFieldsEl) targetFieldsEl.classList.add('active');
        }
        if (draftData.venue_name) {
            const venueNameEl = document.getElementById('venue_name');
            if (venueNameEl) venueNameEl.value = draftData.venue_name;
        }
        if (draftData.venue_address) {
            const venueAddressEl = document.getElementById('venue_address');
            if (venueAddressEl) venueAddressEl.value = draftData.venue_address;
        }
        if (draftData.online_link) {
            const onlineLinkEl = document.getElementById('online_link');
            if (onlineLinkEl) onlineLinkEl.value = draftData.online_link;
        }
        
        // 강의 세부사항 채우기
        if (draftData.max_participants) {
            const maxParticipantsEl = document.getElementById('max_participants');
            if (maxParticipantsEl) maxParticipantsEl.value = draftData.max_participants;
        }
        if (draftData.registration_fee) {
            const registrationFeeEl = document.getElementById('registration_fee');
            const registrationFeeDisplayEl = document.getElementById('registration_fee_display');
            if (registrationFeeEl) registrationFeeEl.value = draftData.registration_fee;
            if (registrationFeeDisplayEl) registrationFeeDisplayEl.value = numberWithCommas(draftData.registration_fee);
        }
        if (draftData.prerequisites) {
            const prerequisitesEl = document.getElementById('prerequisites');
            if (prerequisitesEl) prerequisitesEl.value = draftData.prerequisites;
        }
        if (draftData.what_to_bring) {
            const whatToBringEl = document.getElementById('what_to_bring');
            if (whatToBringEl) whatToBringEl.value = draftData.what_to_bring;
        }
        if (draftData.additional_info) {
            const additionalInfoEl = document.getElementById('additional_info');
            if (additionalInfoEl) additionalInfoEl.value = draftData.additional_info;
        }
        if (draftData.benefits) {
            const benefitsEl = document.getElementById('benefits');
            if (benefitsEl) benefitsEl.value = draftData.benefits;
        }
        if (draftData.youtube_video) {
            const youtubeVideoEl = document.getElementById('youtube_video');
            if (youtubeVideoEl) youtubeVideoEl.value = draftData.youtube_video;
        }
        
        // 등록 마감일시 채우기
        // console.log('등록 마감일시 데이터:', draftData.registration_deadline);
        if (draftData.registration_deadline) {
            const regDeadlineEl = document.getElementById('registration_deadline');
            // console.log('등록 마감일시 엘리먼트:', regDeadlineEl);
            if (regDeadlineEl) {
                // MySQL datetime을 datetime-local 형식으로 변환
                const date = new Date(draftData.registration_deadline);
                const localDateTime = date.toISOString().slice(0, 16);
                // console.log('변환된 날짜:', localDateTime);
                regDeadlineEl.value = localDateTime;
            }
        } else {
            // console.log('등록 마감일시 데이터 없음');
        }
        
        // 강사 정보 채우기
        // console.log('강사 데이터:', draftData.instructors);
        if (draftData.instructors && draftData.instructors.length > 0) {
            // 기존 강사 필드 초기화하지 않고 데이터만 채우기
            const instructorContainer = document.getElementById('instructors-container');
            // console.log('강사 컨테이너:', instructorContainer);
            if (instructorContainer) {
                // 임시저장된 강사 데이터로 필드 채우기
                draftData.instructors.forEach((instructor, index) => {
                    if (index === 0) {
                        // 첫 번째 강사는 기본 필드 사용
                        if (instructor.name) {
                            const nameInput = document.querySelector(`input[name="instructors[0][name]"]`);
                            if (nameInput) nameInput.value = instructor.name;
                        }
                        if (instructor.title) {
                            const titleInput = document.querySelector(`input[name="instructors[0][title]"]`);
                            if (titleInput) titleInput.value = instructor.title;
                        }
                        if (instructor.info) {
                            const infoTextarea = document.querySelector(`textarea[name="instructors[0][info]"]`);
                            if (infoTextarea) infoTextarea.value = instructor.info;
                        }
                        // 첫 번째 강사 이미지 로딩
                        if (instructor.image) {
                            loadInstructorImage(0, instructor.image);
                        }
                    } else {
                        // 두 번째 강사부터는 새로 추가
                        if (typeof addInstructorField === 'function') {
                            addInstructorField();
                            const currentIndex = instructorCount - 1;
                        
                            if (instructor.name) {
                                const nameInput = document.querySelector(`input[name="instructors[${currentIndex}][name]"]`);
                                if (nameInput) nameInput.value = instructor.name;
                            }
                            if (instructor.title) {
                                const titleInput = document.querySelector(`input[name="instructors[${currentIndex}][title]"]`);
                                if (titleInput) titleInput.value = instructor.title;
                            }
                            if (instructor.info) {
                                const infoTextarea = document.querySelector(`textarea[name="instructors[${currentIndex}][info]"]`);
                                if (infoTextarea) infoTextarea.value = instructor.info;
                            }
                            // 추가된 강사 이미지 로딩
                            if (instructor.image) {
                                loadInstructorImage(currentIndex, instructor.image);
                            }
                        }
                    }
                });
            }
        }
        
        // 강의 이미지 정보 표시 (기존 업로드된 이미지 정보)
        if (draftData.lecture_images) {
            try {
                const imageData = typeof draftData.lecture_images === 'string' 
                    ? JSON.parse(draftData.lecture_images) 
                    : draftData.lecture_images;
                
                if (Array.isArray(imageData) && imageData.length > 0) {
                    // 전역 변수에 현재 이미지 데이터 저장
                    currentImageData = [...imageData];
                    // console.log('현재 이미지 데이터 초기화:', currentImageData);
                    
                    const imagePreviewContainer = document.getElementById('lectureImagePreview');
                    if (imagePreviewContainer) {
                        imagePreviewContainer.innerHTML = ''; // 기존 내용 제거
                        
                        imageData.forEach((image, index) => {
                            const imageItem = document.createElement('div');
                            imageItem.className = 'lecture-image-item existing-image';
                            imageItem.setAttribute('data-image-index', index);
                            imageItem.innerHTML = `
                                <div class="image-container">
                                    <img src="${image.file_path}" alt="${image.original_name || '강의 이미지'}">
                                    <button type="button" class="remove-existing-image">
                                        ×
                                    </button>
                                </div>
                                <div class="image-info">
                                    <div style="font-size: 12px; color: #666; margin-bottom: 2px;">
                                        ${image.original_name || '이미지'}
                                    </div>
                                    <div style="font-size: 11px; color: #999;">
                                        기존 업로드된 이미지
                                    </div>
                                </div>
                            `;
                            
                            // 삭제 버튼 이벤트 추가
                            const removeBtn = imageItem.querySelector('.remove-existing-image');
                            removeBtn.addEventListener('click', function(e) {
                                e.preventDefault();
                                if (confirm('이 이미지를 삭제하시겠습니까?')) {
                                    removeExistingImage(index, imageItem);
                                }
                            });
                            
                            imagePreviewContainer.appendChild(imageItem);
                        });
                        
                        // 기존 이미지 로딩 후 placeholder 업데이트
                        updateImageUploadPlaceholder();
                    }
                }
            } catch (e) {
                // console.log('이미지 데이터 파싱 오류:', e);
            }
        }
        
        // 알림 표시
        showAlert('임시저장된 내용을 불러왔습니다.', 'info');
        
        // 임시저장 데이터 불러온 후 소요시간 재계산
        calculateDuration();
    }
    
    
    // 이미지 미리보기 화면 업데이트 함수
    function updateImagePreviewDisplay(imageData) {
        // console.log('=== updateImagePreviewDisplay 함수 시작 ===');
        // console.log('전달받은 imageData:', imageData);
        // console.log('전달받은 이미지 개수:', imageData ? imageData.length : 0);
        
        const imagePreviewContainer = document.getElementById('lectureImagePreview');
        // console.log('imagePreviewContainer 찾기:', imagePreviewContainer ? 'SUCCESS' : 'FAILED');
        if (!imagePreviewContainer) {
            console.error('lectureImagePreview 컨테이너를 찾을 수 없습니다');
            return;
        }
        
        // 기존 이미지만 제거 (새로 업로드된 이미지는 유지)
        const existingImages = imagePreviewContainer.querySelectorAll('.existing-image');
        // console.log('제거할 기존 이미지 개수:', existingImages.length);
        existingImages.forEach(img => img.remove());
        // console.log('기존 이미지 제거 완료');
        
        // 업데이트된 이미지 데이터로 다시 생성
        if (Array.isArray(imageData) && imageData.length > 0) {
            // console.log('새 이미지 생성 시작, 개수:', imageData.length);
            imageData.forEach((image, index) => {
                const imageItem = document.createElement('div');
                imageItem.className = 'lecture-image-item existing-image';
                imageItem.setAttribute('data-image-index', index);
                imageItem.innerHTML = `
                    <div class="image-container">
                        <img src="${image.file_path}" alt="${image.original_name || '강의 이미지'}">
                        <button type="button" class="remove-existing-image">
                            ×
                        </button>
                    </div>
                    <div class="image-info">
                        <div style="font-size: 12px; color: #666; margin-bottom: 2px;">
                            ${image.original_name || '이미지'}
                        </div>
                        <div style="font-size: 11px; color: #999;">
                            기존 업로드된 이미지
                        </div>
                    </div>
                `;
                
                // 삭제 버튼 이벤트 추가
                const removeBtn = imageItem.querySelector('.remove-existing-image');
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('이 이미지를 삭제하시겠습니까?')) {
                        removeExistingImage(index, imageItem);
                    }
                });
                
                imagePreviewContainer.appendChild(imageItem);
            });
        }
        
        // placeholder 업데이트
        updateImageUploadPlaceholder();
    }
    
    // 이미지 인덱스 업데이트 함수
    

    // 서버에 업데이트된 이미지 목록 전송 함수
    function updateImageListOnServer(updatedImageData) {
        const formData = new FormData();
        formData.append('action', 'update_images');
        formData.append('lecture_images', JSON.stringify(updatedImageData));
        formData.append('csrf_token', <?php echo json_encode($_SESSION['csrf_token']); ?>);
        
        fetch(window.location.origin + '/lectures/update-images', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // console.log('Response status:', response.status);
            // console.log('Response headers:', [...response.headers.entries()]);
            
            // 응답이 JSON인지 확인
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                // HTML 응답인 경우 내용 확인을 위해 텍스트로 읽기
                return response.text().then(text => {
                    console.error('Non-JSON response:', text.substring(0, 500));
                    throw new Error('서버에서 올바르지 않은 응답을 받았습니다. 로그인 상태나 권한을 확인해주세요.');
                });
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert('이미지가 삭제되었습니다.', 'success');
            } else {
                showAlert(data.message || '이미지 삭제 중 오류가 발생했습니다.', 'error');
                console.error('서버 오류:', data);
            }
        })
        .catch(error => {
            console.error('이미지 업데이트 오류:', error);
            if (error.message.includes('JSON')) {
                showAlert('서버 응답 오류입니다. 페이지를 새로고침하고 다시 시도해주세요.', 'error');
            } else {
                showAlert(error.message || '이미지 업데이트 중 오류가 발생했습니다.', 'error');
            }
        });
    }
    
    
    
    <?php endif; ?>
    
    // 카카오 주소 검색 API 구현
    function initAddressSearch() {
        const addressSearchBtn = document.getElementById('address_search_btn');
        const addressField = document.getElementById('venue_address');
        
        // 주소 검색 실행 함수
        function openAddressSearch() {
            new daum.Postcode({
                oncomplete: function(data) {
                    // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.
                    
                    // 각 주소의 노출 규칙에 따라 주소를 조합한다.
                    // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                    let addr = ''; // 주소 변수
                    let extraAddr = ''; // 참고항목 변수
                    
                    //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
                    if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
                        addr = data.roadAddress;
                    } else { // 사용자가 지번 주소를 선택했을 경우(J)
                        addr = data.jibunAddress;
                    }
                    
                    // 사용자가 선택한 주소가 도로명 타입일때 참고항목을 조합한다.
                    if(data.userSelectedType === 'R'){
                        // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                        // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                        if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                            extraAddr += data.bname;
                        }
                        // 건물명이 있고, 공동주택일 경우 추가한다.
                        if(data.buildingName !== '' && data.apartment === 'Y'){
                            extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                        }
                        // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                        if(extraAddr !== ''){
                            extraAddr = ' (' + extraAddr + ')';
                        }
                        // 조합된 참고항목을 해당 필드에 넣는다.
                        addr += extraAddr;
                    }
                    
                    // 주소 정보를 해당 필드에 넣는다.
                    document.getElementById('venue_address').value = addr;
                    
                    // 카카오 좌표 API를 사용하여 위도, 경도 가져오기
                    getCoordinates(addr);
                    
                    // console.log('주소 검색 완료:', {
                    //     address: addr,
                    //     zonecode: data.zonecode
                    // });
                },
                onresize : function(size) {
                    // 팝업 크기 변경 시 실행할 코드
                },
                onclose : function(state) {
                    // 팝업 닫기 시 실행할 코드 (state는 닫기 방법)
                    // console.log('주소 검색 팝업 닫힘:', state);
                }
            }).open();
        }
        
        // 주소 검색 버튼 클릭 이벤트
        if (addressSearchBtn) {
            // 기존 이벤트 리스너 제거 후 새로 추가
            addressSearchBtn.removeEventListener('click', openAddressSearch);
            addressSearchBtn.addEventListener('click', openAddressSearch);
        }
        
        // 주소 입력 박스 클릭 이벤트 (읽기 전용이므로 클릭 시 주소 검색 팝업 열기)
        if (addressField) {
            // 기존 이벤트 리스너 제거를 위해 함수를 변수에 저장
            if (addressField._clickHandler) {
                addressField.removeEventListener('click', addressField._clickHandler);
            }
            if (addressField._focusHandler) {
                addressField.removeEventListener('focus', addressField._focusHandler);
            }
            
            // 새로운 이벤트 핸들러 정의 (클릭만 사용)
            addressField._clickHandler = function() {
                // console.log('주소 입력 박스 클릭됨');
                openAddressSearch();
            };
            
            // 클릭 이벤트만 추가 (focus 이벤트 제거하여 중복 방지)
            addressField.addEventListener('click', addressField._clickHandler);
        }
    }
    
    // 네이버 Maps API를 통한 정확한 좌표 설정 (클라이언트 사이드)
    function getCoordinates(address) {
        // console.log('주소 저장됨:', address);
        
        if (!address) {
            document.getElementById('venue_latitude').value = '';
            document.getElementById('venue_longitude').value = '';
            return;
        }
        
        // 네이버 Maps API가 로드되어 있는지 확인
        if (typeof naver !== 'undefined' && naver.maps && naver.maps.Service) {
            // 네이버 Maps Geocoding 서비스 사용
            naver.maps.Service.geocode({
                query: address
            }, function(status, response) {
                if (status === naver.maps.Service.Status.OK) {
                    const result = response.v2.addresses[0];
                    if (result) {
                        const lat = parseFloat(result.y);
                        const lng = parseFloat(result.x);
                        
                        document.getElementById('venue_latitude').value = lat;
                        document.getElementById('venue_longitude').value = lng;
                        
                        // 성공 시각적 피드백
                        const addressField = document.getElementById('venue_address');
                        addressField.style.backgroundColor = '#f0fdf4';
                        addressField.style.borderColor = '#22c55e';
                        
                        console.log('정확한 좌표 설정 완료:', {
                            address: address,
                            latitude: lat,
                            longitude: lng
                        });
                        return;
                    }
                }
                
                // API 실패 시 지역 기반 근사 좌표 사용
                console.warn('네이버 Geocoding 실패, 지역 기반 좌표 사용');
                setRegionBasedCoordinates(address);
            });
        } else {
            // 네이버 Maps API가 로드되지 않은 경우 지역 기반 근사 좌표 사용
            console.warn('네이버 Maps API 미로드, 지역 기반 좌표 사용');
            setRegionBasedCoordinates(address);
        }
        
        // 주소 입력 완료를 시각적으로 표시
        const addressField = document.getElementById('venue_address');
        addressField.style.backgroundColor = '#f0f9ff';
        addressField.style.borderColor = '#0ea5e9';
    }
    
    // 지역 기반 근사 좌표 설정 함수
    function setRegionBasedCoordinates(address) {
        const regionCoordinates = {
            // 서울 지역
            '서울': { lat: 37.5665, lng: 126.9780 },
            '강남': { lat: 37.4979, lng: 127.0276 },
            '강북': { lat: 37.6390, lng: 127.0258 },
            '강동': { lat: 37.5301, lng: 127.1238 },
            '강서': { lat: 37.5509, lng: 126.8495 },
            '홍대': { lat: 37.5563, lng: 126.9236 },
            '가산': { lat: 37.4816, lng: 126.8819 },
            '여의도': { lat: 37.5219, lng: 126.9245 },
            '잠실': { lat: 37.5133, lng: 127.1028 },
            
            // 광역시/도청 소재지
            '부산': { lat: 35.1796, lng: 129.0756 },
            '대구': { lat: 35.8714, lng: 128.6014 },
            '인천': { lat: 37.4563, lng: 126.7052 },
            '광주': { lat: 35.1595, lng: 126.8526 },
            '대전': { lat: 36.3504, lng: 127.3845 },
            '울산': { lat: 35.5384, lng: 129.3114 },
            '세종': { lat: 36.4800, lng: 127.2890 },
            
            // 주요 도시
            '청주': { lat: 36.6424, lng: 127.4890 },
            '서원구': { lat: 36.637, lng: 127.491 },  // 청주 서원구
            '전주': { lat: 35.8242, lng: 127.1479 },
            '창원': { lat: 35.2281, lng: 128.6811 },
            '천안': { lat: 36.8151, lng: 127.1139 },
            '안양': { lat: 37.3943, lng: 126.9568 },
            '안산': { lat: 37.3236, lng: 126.8219 },
            '용인': { lat: 37.2411, lng: 127.1776 },
            '성남': { lat: 37.4449, lng: 127.1388 },
            '수원': { lat: 37.2636, lng: 127.0286 }
        };
        
        let foundCoords = null;
        for (const [region, coords] of Object.entries(regionCoordinates)) {
            if (address.includes(region)) {
                foundCoords = coords;
                break;
            }
        }
        
        if (foundCoords) {
            document.getElementById('venue_latitude').value = foundCoords.lat;
            document.getElementById('venue_longitude').value = foundCoords.lng;
            // console.log('지역 기반 근사 좌표 사용:', foundCoords, 'for address:', address);
        } else {
            // 기본 서울 좌표 사용
            document.getElementById('venue_latitude').value = '37.5665';
            document.getElementById('venue_longitude').value = '126.9780';
            // console.log('기본 서울 좌표 사용 for address:', address);
        }
    }
    
    // 등록 마감일시 검증 설정
    function initRegistrationDeadlineValidation() {
        const deadlineInput = document.getElementById('registration_deadline');
        if (!deadlineInput) return;
        
        // 현재 시간을 min 값으로 설정
        function updateMinDateTime() {
            const now = new Date();
            // 현재 시간에서 10분 후를 최소값으로 설정 (여유시간)
            now.setMinutes(now.getMinutes() + 10);
            const minDateTime = now.toISOString().slice(0, 16);
            deadlineInput.min = minDateTime;
        }
        
        // 페이지 로드 시 min 값 설정
        updateMinDateTime();
        
        // 매 분마다 min 값 업데이트 (선택사항)
        setInterval(updateMinDateTime, 60000);
        
        // 검증 함수
        function validateRegistrationDeadline() {
            const value = deadlineInput.value;
            if (!value) return true; // 비어있으면 유효 (선택사항)
            
            const selectedDate = new Date(value);
            const now = new Date();
            
            if (selectedDate <= now) {
                deadlineInput.setCustomValidity('등록 마감일시는 현재 시간 이후여야 합니다.');
                return false;
            } else {
                deadlineInput.setCustomValidity('');
                return true;
            }
        }
        
        // 이벤트 리스너 추가
        deadlineInput.addEventListener('change', validateRegistrationDeadline);
        deadlineInput.addEventListener('blur', validateRegistrationDeadline);
        
        // 폼 제출 시 추가 검증
        const form = document.getElementById('lectureForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!validateRegistrationDeadline()) {
                    e.preventDefault();
                    showAlert('등록 마감일시를 올바르게 설정해주세요.', 'error');
                    deadlineInput.focus();
                }
            });
        }
    }
    
    // 등록 마감일시 검증 초기화
    initRegistrationDeadlineValidation();
    
    // 주소 검색 초기화
    initAddressSearch();
});

// 카카오 주소 검색 API 스크립트 로드
document.head.appendChild(Object.assign(document.createElement('script'), {
    src: '//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js',
    onload: function() {
        // console.log('카카오 주소 검색 API 로드 완료');
    },
    onerror: function() {
        console.error('카카오 주소 검색 API 로드 실패');
    }
}));

// 네이버 Maps API 로드 (Geocoding 기능을 위해)
</script>

<!-- 네이버 Maps API 스크립트 추가 -->
<script type="text/javascript" src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId=<?php echo NAVER_MAPS_CLIENT_ID; ?>&submodules=geocoder"></script>

<?php if ($isEditMode && !empty($lecture)): ?>
<script>
// 수정 모드 데이터 초기화
document.addEventListener('DOMContentLoaded', function() {
    console.log('Edit mode initialization started');
    
    // 강사 데이터 초기화
    <?php if (!empty($lecture['instructors'])): ?>
        const instructors = <?= json_encode($lecture['instructors'], JSON_UNESCAPED_UNICODE) ?>;
        console.log('Instructors data:', instructors);
        
        // 추가 강사가 있는 경우 폼 필드 직접 생성
        if (instructors.length > 1) {
            const container = document.getElementById('instructors-container');
            if (container) {
                for (let i = 1; i < instructors.length; i++) {
                    createAdditionalInstructorField(container, i);
                }
            }
        }
        
        // 각 강사 데이터로 폼 필드 채우기 (약간의 지연을 주어 DOM이 준비될 때까지 기다림)
        setTimeout(() => {
            instructors.forEach((instructor, index) => {
                const nameField = document.querySelector(`input[name="instructors[${index}][name]"]`);
                const titleField = document.querySelector(`input[name="instructors[${index}][title]"]`);
                const infoField = document.querySelector(`textarea[name="instructors[${index}][info]"]`);
                
                if (nameField && instructor.name) nameField.value = instructor.name;
                if (titleField && instructor.title) titleField.value = instructor.title;
                if (infoField && instructor.info) infoField.value = instructor.info;
                
                // 강사 이미지 로드
                if (instructor.image_url) {
                    if (typeof loadInstructorImage === 'function') {
                        loadInstructorImage(index, instructor.image_url);
                    } else {
                        // 함수가 없는 경우 직접 이미지 로드
                        loadInstructorImageDirect(index, instructor.image_url);
                    }
                }
            });
        }, 200);
    <?php endif; ?>
    
    // 강의 이미지 초기화
    <?php if (!empty($lecture['images'])): ?>
        const lectureImages = <?= json_encode($lecture['images'], JSON_UNESCAPED_UNICODE) ?>;
        console.log('Lecture images data:', lectureImages);
        currentImageData = lectureImages;
        
        // 기존 이미지 UI 표시
        displayExistingImages(lectureImages);
    <?php endif; ?>
    
    // 소요시간 계산
    setTimeout(() => {
        if (typeof calculateDuration === 'function') {
            calculateDuration();
        } else {
            console.warn('calculateDuration function not available yet');
        }
    }, 500);
});

// 추가 강사 필드 생성 함수 (edit mode 전용)
function createAdditionalInstructorField(container, index) {
    const newInstructor = document.createElement('div');
    newInstructor.className = 'instructor-item';
    newInstructor.setAttribute('data-instructor-index', index);
    
    newInstructor.innerHTML = `
        <div class="instructor-header">
            <h4>강사 ${index + 1}</h4>
            <button type="button" class="btn-remove-instructor" onclick="removeInstructorField(this.closest('.instructor-item'))">
                <i class="fas fa-times"></i> 삭제
            </button>
        </div>
        <div class="instructor-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="instructor_name_${index}">강사명 *</label>
                    <input type="text" id="instructor_name_${index}" name="instructors[${index}][name]" required>
                </div>
                <div class="form-group">
                    <label for="instructor_title_${index}">직책/소속</label>
                    <input type="text" id="instructor_title_${index}" name="instructors[${index}][title]">
                </div>
            </div>
            <div class="form-group">
                <label for="instructor_info_${index}">강사 소개</label>
                <textarea id="instructor_info_${index}" name="instructors[${index}][info]" rows="3" placeholder="강사의 경력, 전문분야 등을 입력해주세요"></textarea>
            </div>
            <div class="form-group">
                <label for="instructor_image_${index}">강사 이미지</label>
                <div class="instructor-image-wrapper">
                    <input type="file" id="instructor_image_${index}" name="instructors[${index}][image]" accept="image/*" onchange="handleInstructorImage(${index}, this)">
                    <div class="instructor-image-preview" id="instructor-preview-${index}" style="display: none;">
                        <img id="instructor-img-${index}" src="" alt="강사 이미지">
                        <button type="button" class="btn-remove-image" onclick="removeInstructorImage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(newInstructor);
}

// 강사 이미지 직접 로드 함수 (edit mode 전용)
function loadInstructorImageDirect(index, imageUrl) {
    const preview = document.getElementById(`instructor-preview-${index}`);
    const img = document.getElementById(`instructor-img-${index}`);
    
    if (preview && img) {
        img.src = imageUrl;
        preview.style.display = 'block';
        console.log(`Instructor ${index} image loaded:`, imageUrl);
    } else {
        console.warn(`Could not find preview elements for instructor ${index}`);
    }
}

// 기존 이미지 표시 함수
function displayExistingImages(images) {
    const container = document.getElementById('lectureImagePreview');
    if (!container) {
        console.warn('Lecture image preview container not found');
        return;
    }
    
    images.forEach((image, index) => {
        const imageItem = document.createElement('div');
        imageItem.className = 'image-item existing-image';
        imageItem.setAttribute('data-image-id', image.file_name || index);
        
        imageItem.innerHTML = `
            <div class="image-wrapper">
                <img src="${image.file_path}" alt="${image.original_name}" class="preview-image">
                <div class="image-overlay">
                    <button type="button" class="btn-remove-image" onclick="removeExistingImage(${index})">
                        <i class="fas fa-trash"></i>
                    </button>
                    <div class="drag-handle">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                </div>
                <div class="image-info">
                    <span class="image-name">${image.original_name}</span>
                    <span class="image-size">${formatFileSize(image.file_size)}</span>
                </div>
            </div>
        `;
        
        container.appendChild(imageItem);
    });
    
    // 드래그 앤 드롭 활성화
    enableImageSorting();
}

// 기존 이미지 삭제 함수
function removeExistingImage(index) {
    if (confirm('이 이미지를 삭제하시겠습니까?')) {
        // currentImageData에서 제거
        if (currentImageData && currentImageData[index]) {
            currentImageData.splice(index, 1);
        }
        
        // UI에서 제거
        const imageItems = document.querySelectorAll('.existing-image');
        if (imageItems[index]) {
            imageItems[index].remove();
        }
        
        // existing_lecture_images 필드 업데이트
        updateExistingImagesField();
    }
}

// existing_lecture_images 필드 업데이트
function updateExistingImagesField() {
    const field = document.querySelector('input[name="existing_lecture_images"]');
    if (field && currentImageData) {
        field.value = JSON.stringify(currentImageData);
    }
}

// 파일 크기 포맷팅
function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// 이미지 정렬 활성화
function enableImageSorting() {
    const container = document.getElementById('lectureImagePreview');
    if (!container) return;
    
    // 기존 Sortable이 있다면 제거
    if (container.sortable) {
        container.sortable.destroy();
    }
    
    // 새로운 Sortable 인스턴스 생성
    if (typeof Sortable !== 'undefined') {
        container.sortable = Sortable.create(container, {
            animation: 150,
            handle: '.drag-handle',
            onEnd: function(evt) {
                // 순서 변경 후 currentImageData 업데이트
                if (currentImageData) {
                    const item = currentImageData.splice(evt.oldIndex, 1)[0];
                    currentImageData.splice(evt.newIndex, 0, item);
                    updateExistingImagesField();
                }
            }
        });
    }
}
</script>
<?php endif; ?>