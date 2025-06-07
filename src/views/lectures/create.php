<?php
/**
 * 강의 등록 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

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
    padding: 20px;
    min-height: calc(100vh - 200px);
}

.create-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    text-align: center;
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
}
</style>

<div class="lecture-create-container">
    <!-- 헤더 섹션 -->
    <div class="create-header">
        <h1>➕ 강의 등록</h1>
        <p>새로운 강의나 세미나를 등록하여 많은 분들과 지식을 공유하세요</p>
    </div>
    
    <!-- 등록 폼 -->
    <form id="lectureForm" class="create-form" method="POST" action="/lectures/store">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <!-- 기본 정보 -->
        <div class="form-section">
            <h2 class="section-title">📋 기본 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="title" class="form-label required">강의 제목</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           placeholder="예: 디지털 마케팅 전략 완벽 가이드" required>
                    <div class="form-help">참가자들이 쉽게 이해할 수 있는 명확한 제목을 입력하세요</div>
                    <div class="form-error" id="title-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label required">강의 유형</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">선택해주세요</option>
                        <option value="seminar" <?= $defaultData['category'] === 'seminar' ? 'selected' : '' ?>>세미나</option>
                        <option value="workshop">워크샵</option>
                        <option value="conference">컨퍼런스</option>
                        <option value="webinar">웨비나</option>
                        <option value="training">교육과정</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="difficulty_level" class="form-label">난이도</label>
                    <select id="difficulty_level" name="difficulty_level" class="form-select">
                        <option value="all" <?= $defaultData['difficulty_level'] === 'all' ? 'selected' : '' ?>>전체 대상</option>
                        <option value="beginner">초급</option>
                        <option value="intermediate">중급</option>
                        <option value="advanced">고급</option>
                    </select>
                </div>
                
                <div class="form-group full-width">
                    <label for="description" class="form-label required">강의 설명</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="강의 내용, 목표, 대상자 등을 자세히 설명해주세요" required></textarea>
                    <div class="form-help">참가자들이 강의 내용을 충분히 이해할 수 있도록 상세히 작성해주세요</div>
                    <div class="form-error" id="description-error"></div>
                </div>
            </div>
        </div>
        
        <!-- 강사 정보 -->
        <div class="form-section">
            <h2 class="section-title">👨‍🏫 강사 정보</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="instructor_name" class="form-label required">강사명</label>
                    <input type="text" id="instructor_name" name="instructor_name" class="form-input" 
                           placeholder="예: 김마케팅" required>
                    <div class="form-error" id="instructor_name-error"></div>
                </div>
                
                <div class="form-group full-width">
                    <label for="instructor_info" class="form-label">강사 소개</label>
                    <textarea id="instructor_info" name="instructor_info" class="form-textarea" 
                              placeholder="강사의 경력, 전문분야, 주요 실적 등을 소개해주세요"></textarea>
                    <div class="form-help">강사의 전문성을 어필할 수 있는 내용을 작성해주세요</div>
                </div>
            </div>
        </div>
        
        <!-- 일정 정보 -->
        <div class="form-section">
            <h2 class="section-title">📅 일정 정보</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label for="start_date" class="form-label required">시작 날짜</label>
                    <input type="date" id="start_date" name="start_date" class="form-input" required>
                    <div class="form-error" id="start_date-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="end_date" class="form-label required">종료 날짜</label>
                    <input type="date" id="end_date" name="end_date" class="form-input" required>
                    <div class="form-error" id="end_date-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="start_time" class="form-label required">시작 시간</label>
                    <input type="time" id="start_time" name="start_time" class="form-input" required>
                    <div class="form-error" id="start_time-error"></div>
                </div>
                
                <div class="form-group">
                    <label for="end_time" class="form-label required">종료 시간</label>
                    <input type="time" id="end_time" name="end_time" class="form-input" required>
                    <div class="form-error" id="end_time-error"></div>
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
                               <?= $defaultData['location_type'] === 'offline' ? 'checked' : '' ?> required>
                        <span>📍 오프라인</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="location_type" value="online" required>
                        <span>💻 온라인</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="location_type" value="hybrid" required>
                        <span>🔄 하이브리드</span>
                    </label>
                </div>
            </div>
            
            <!-- 오프라인/하이브리드 필드 -->
            <div id="offline-fields" class="location-fields">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="venue_name" class="form-label">장소명</label>
                        <input type="text" id="venue_name" name="venue_name" class="form-input" 
                               placeholder="예: 강남구 세미나실">
                    </div>
                    <div class="form-group full-width">
                        <label for="venue_address" class="form-label">장소 주소</label>
                        <input type="text" id="venue_address" name="venue_address" class="form-input" 
                               placeholder="상세 주소를 입력해주세요">
                    </div>
                </div>
            </div>
            
            <!-- 온라인/하이브리드 필드 -->
            <div id="online-fields" class="location-fields">
                <div class="form-group">
                    <label for="online_link" class="form-label">온라인 링크</label>
                    <input type="url" id="online_link" name="online_link" class="form-input" 
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
                           class="form-input" min="1" placeholder="무제한인 경우 비워두세요">
                    <div class="form-help">참가자 수 제한이 없으면 비워두세요</div>
                </div>
                
                <div class="form-group">
                    <label for="registration_fee" class="form-label">참가비 (원)</label>
                    <input type="number" id="registration_fee" name="registration_fee" 
                           class="form-input" min="0" value="0" placeholder="0">
                    <div class="form-help">무료인 경우 0을 입력하세요</div>
                </div>
                
                <div class="form-group">
                    <label for="registration_deadline" class="form-label">등록 마감일시</label>
                    <input type="datetime-local" id="registration_deadline" name="registration_deadline" 
                           class="form-input">
                    <div class="form-help">마감일이 없으면 비워두세요</div>
                </div>
            </div>
        </div>
        
        <!-- 추가 정보 -->
        <div class="form-section">
            <h2 class="section-title">📝 추가 정보</h2>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="requirements" class="form-label">참가 요구사항</label>
                    <textarea id="requirements" name="requirements" class="form-textarea" 
                              placeholder="필요한 사전 지식, 준비물 등을 안내해주세요"></textarea>
                </div>
                
                <div class="form-group full-width">
                    <label for="benefits" class="form-label">혜택</label>
                    <textarea id="benefits" name="benefits" class="form-textarea" 
                              placeholder="수료증, 자료 제공, 네트워킹 기회 등의 혜택을 안내해주세요"></textarea>
                </div>
            </div>
        </div>
        
        <!-- 폼 액션 -->
        <div class="form-actions">
            <a href="/lectures" class="btn btn-secondary">
                ← 목록으로
            </a>
            
            <div style="display: flex; gap: 15px;">
                <button type="submit" name="status" value="draft" class="btn btn-draft">
                    💾 임시저장
                </button>
                <button type="submit" name="status" value="published" class="btn btn-primary">
                    🚀 등록하기
                </button>
            </div>
        </div>
        
        <div class="loading" id="loading">
            ⏳ 강의를 등록하고 있습니다...
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('➕ 강의 등록 페이지 로드 완료');
    
    const form = document.getElementById('lectureForm');
    const locationTypeInputs = document.querySelectorAll('input[name="location_type"]');
    const offlineFields = document.getElementById('offline-fields');
    const onlineFields = document.getElementById('online-fields');
    
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
            case 'hybrid':
                offlineFields.classList.add('active');
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
        
        // AJAX 제출
        fetch('/lectures/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            
            if (data.success) {
                alert('강의가 성공적으로 등록되었습니다!');
                window.location.href = data.redirectUrl || '/lectures';
            } else {
                alert(data.message || '강의 등록 중 오류가 발생했습니다.');
            }
        })
        .catch(error => {
            console.error('폼 제출 오류:', error);
            showLoading(false);
            alert('강의 등록 중 오류가 발생했습니다. 다시 시도해주세요.');
        });
    });
    
    // 유효성 검사 함수
    function validateForm() {
        let isValid = true;
        
        // 필수 필드 검사
        const requiredFields = [
            'title', 'description', 'instructor_name', 
            'start_date', 'end_date', 'start_time', 'end_time'
        ];
        
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field.value.trim()) {
                showError(fieldName, '이 필드는 필수입니다.');
                isValid = false;
            } else {
                clearError(fieldName);
            }
        });
        
        // 날짜/시간 검사
        if (!validateDates() || !validateTimes()) {
            isValid = false;
        }
        
        // 위치 타입별 필수 필드 검사
        const locationType = document.querySelector('input[name="location_type"]:checked');
        if (locationType) {
            if (locationType.value === 'offline' || locationType.value === 'hybrid') {
                const venueField = document.getElementById('venue_name');
                if (!venueField.value.trim()) {
                    alert('오프라인 진행 시 장소명은 필수입니다.');
                    venueField.focus();
                    isValid = false;
                }
            }
            
            if (locationType.value === 'online' || locationType.value === 'hybrid') {
                const linkField = document.getElementById('online_link');
                if (!linkField.value.trim()) {
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
    
    // 자동 저장 (임시저장) 기능
    let autoSaveTimeout;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                // 여기에 자동 저장 로직 추가 가능
                console.log('자동 저장 가능한 상태');
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
});
</script>