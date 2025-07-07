<?php
/**
 * 행사 등록 페이지
 */

// 로그인 상태 확인
require_once SRC_PATH . '/middlewares/AuthMiddleware.php';
require_once SRC_PATH . '/helpers/HtmlSanitizerHelper.php';
$isLoggedIn = AuthMiddleware::isLoggedIn();
$currentUserId = AuthMiddleware::getCurrentUserId();

if (!$isLoggedIn) {
    header('Location: /auth/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// 기업회원 권한 확인
require_once SRC_PATH . '/middleware/CorporateMiddleware.php';
$permission = CorporateMiddleware::checkLectureEventPermission();

if (!$permission['hasPermission']) {
    $_SESSION['error_message'] = $permission['message'];
    header('Location: /corp/info');
    exit;
}
?>

<style>
/* 행사 등록 페이지 스타일 */
.event-create-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 80px 20px 40px;
    min-height: calc(100vh - 160px);
}

.event-create-header {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    padding: 40px 30px;
    text-align: center;
    margin-bottom: 40px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(74, 144, 226, 0.2);
}

.event-create-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.event-create-header p {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.event-form-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    padding: 40px;
    margin-bottom: 30px;
}

.form-section {
    margin-bottom: 40px;
}

.form-section:last-child {
    margin-bottom: 0;
}

.form-section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #2E86AB;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-label.required::after {
    content: ' *';
    color: #e53e3e;
}

.form-input,
.form-textarea,
.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus,
.form-textarea:focus,
.form-select:focus {
    outline: none;
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.form-textarea {
    min-height: 120px;
    resize: vertical;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.form-checkbox {
    width: 18px;
    height: 18px;
    border: 2px solid #e5e7eb;
    border-radius: 4px;
    cursor: pointer;
    accent-color: #4A90E2;
}

.location-toggle {
    display: flex;
    background: #f8fafc;
    border-radius: 8px;
    padding: 4px;
    margin-bottom: 20px;
}

.location-btn {
    flex: 1;
    padding: 10px 20px;
    border: none;
    background: transparent;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.location-btn.active {
    background: #4A90E2;
    color: white;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
}

.location-fields {
    display: none;
}

.location-fields.active {
    display: block;
}

.form-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 40px;
    padding-top: 30px;
    border-top: 2px solid #f1f5f9;
}

.btn {
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
}

.btn-secondary {
    background: #f8fafc;
    color: #64748b;
    border: 2px solid #e2e8f0;
}

.btn-secondary:hover {
    background: #e2e8f0;
}

.help-text {
    font-size: 0.85rem;
    color: #64748b;
    margin-top: 5px;
}

.error-message {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

.success-message {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
}

/* 로딩 스피너 */
.loading-spinner {
    display: none;
    width: 20px;
    height: 20px;
    border: 2px solid #ffffff;
    border-top: 2px solid transparent;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* 반응형 */
@media (max-width: 768px) {
    .event-create-container {
        padding: 40px 15px 20px;
    }
    
    .event-create-header {
        padding: 30px 20px;
    }
    
    .event-create-header h1 {
        font-size: 2rem;
    }
    
    .event-form-card {
        padding: 25px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .form-buttons {
        flex-direction: column;
    }
}
</style>

<div class="event-create-container">
    <!-- 헤더 -->
    <div class="event-create-header">
        <h1>🎉 새 행사 등록</h1>
        <p>마케팅 전문가들과 함께하는 의미있는 행사를 만들어보세요</p>
    </div>

    <!-- 폼 카드 -->
    <div class="event-form-card">
        <!-- 에러/성공 메시지 -->
        <div id="error-message" class="error-message"></div>
        <div id="success-message" class="success-message"></div>

        <form id="event-form" novalidate>
            <!-- 기본 정보 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    기본 정보
                </h3>
                
                <div class="form-group">
                    <label for="title" class="form-label required">행사 제목</label>
                    <input type="text" id="title" name="title" class="form-input" 
                           placeholder="예: 여름 마케팅 전략 워크샵" required>
                    <div class="help-text">참가자들이 쉽게 이해할 수 있는 명확한 제목을 입력하세요.</div>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label required">행사 설명</label>
                    <textarea id="description" name="description" class="form-textarea" 
                              placeholder="행사의 목적, 내용, 참가 대상 등을 자세히 설명해주세요..." required></textarea>
                    <div class="help-text">마크다운 문법을 사용할 수 있습니다. (**, *, ###, - 등)</div>
                </div>

                <div class="form-group">
                    <label for="category" class="form-label required">카테고리</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">카테고리를 선택하세요</option>
                        <option value="세미나">세미나</option>
                        <option value="워크샵">워크샵</option>
                        <option value="컨퍼런스">컨퍼런스</option>
                        <option value="네트워킹">네트워킹</option>
                        <option value="전시회">전시회</option>
                        <option value="기타">기타</option>
                    </select>
                </div>
            </div>

            <!-- 일정 정보 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-calendar-alt"></i>
                    일정 정보
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_date" class="form-label required">시작일</label>
                        <input type="date" id="start_date" name="start_date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="start_time" class="form-label required">시작 시간</label>
                        <input type="time" id="start_time" name="start_time" class="form-input" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="end_date" class="form-label">종료일</label>
                        <input type="date" id="end_date" name="end_date" class="form-input">
                        <div class="help-text">당일 행사인 경우 비워두셔도 됩니다.</div>
                    </div>
                    <div class="form-group">
                        <label for="end_time" class="form-label">종료 시간</label>
                        <input type="time" id="end_time" name="end_time" class="form-input">
                    </div>
                </div>
            </div>

            <!-- 장소 정보 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-map-marker-alt"></i>
                    장소 정보
                </h3>
                
                <div class="location-toggle">
                    <button type="button" class="location-btn active" data-type="offline">
                        <i class="fas fa-building"></i> 오프라인
                    </button>
                    <button type="button" class="location-btn" data-type="online">
                        <i class="fas fa-globe"></i> 온라인
                    </button>
                </div>

                <input type="hidden" id="location_type" name="location_type" value="offline">

                <div id="offline-fields" class="location-fields active">
                    <div class="form-group">
                        <label for="venue_name" class="form-label">행사장명</label>
                        <input type="text" id="venue_name" name="venue_name" class="form-input" 
                               placeholder="예: 코엑스 컨퍼런스룸 A">
                    </div>
                    <div class="form-group">
                        <label for="venue_address" class="form-label">주소</label>
                        <input type="text" id="venue_address" name="venue_address" class="form-input" 
                               placeholder="서울시 강남구 영동대로 513">
                    </div>
                </div>

                <div id="online-fields" class="location-fields">
                    <div class="form-group">
                        <label for="online_link" class="form-label">온라인 링크</label>
                        <input type="url" id="online_link" name="online_link" class="form-input" 
                               placeholder="https://zoom.us/j/1234567890">
                        <div class="help-text">Zoom, Teams, Meet 등의 온라인 회의 링크를 입력하세요.</div>
                    </div>
                </div>
            </div>

            <!-- 행사 세부사항 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-cogs"></i>
                    행사 세부사항
                </h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="max_participants" class="form-label">최대 참가자 수</label>
                        <input type="number" id="max_participants" name="max_participants" class="form-input" 
                               placeholder="50" min="1">
                        <div class="help-text">참가자 수 제한이 없으면 비워두세요.</div>
                    </div>
                    <div class="form-group">
                        <label for="registration_fee" class="form-label">참가비 (원)</label>
                        <input type="number" id="registration_fee" name="registration_fee" class="form-input" 
                               placeholder="50000" min="0">
                        <div class="help-text">무료 행사인 경우 0 또는 비워두세요.</div>
                    </div>
                </div>

            </div>

            <!-- 주최자 정보 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-user-tie"></i>
                    주최자 정보
                </h3>
                
                <div class="form-group">
                    <label for="instructor_name" class="form-label">주최자명</label>
                    <input type="text" id="instructor_name" name="instructor_name" class="form-input" 
                           placeholder="홍길동">
                </div>

                <div class="form-group">
                    <label for="instructor_info" class="form-label">주최자 소개</label>
                    <textarea id="instructor_info" name="instructor_info" class="form-textarea" 
                              placeholder="주최자의 경력, 전문 분야 등을 간단히 소개해주세요..."></textarea>
                </div>
            </div>

            <!-- 추가 정보 섹션 -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-plus-circle"></i>
                    추가 정보
                </h3>
                
                <div class="form-group">
                    <label for="sponsor_info" class="form-label">후원사 정보</label>
                    <input type="text" id="sponsor_info" name="sponsor_info" class="form-input" 
                           placeholder="후원사명을 입력하세요">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="dress_code" class="form-label">복장 규정</label>
                        <select id="dress_code" name="dress_code" class="form-select">
                            <option value="">선택하세요</option>
                            <option value="casual">캐주얼</option>
                            <option value="business_casual">비즈니스 캐주얼</option>
                            <option value="formal">정장</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parking_info" class="form-label">주차 정보</label>
                        <input type="text" id="parking_info" name="parking_info" class="form-input" 
                               placeholder="주차 가능 여부, 주차비 등">
                    </div>
                </div>
            </div>

            <!-- 버튼 -->
            <div class="form-buttons">
                <a href="/events" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    취소
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="loading-spinner"></span>
                    <i class="fas fa-plus"></i>
                    <span class="btn-text">행사 등록</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// 장소 타입 토글
document.querySelectorAll('.location-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const type = this.dataset.type;
        
        // 버튼 상태 변경
        document.querySelectorAll('.location-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // 필드 표시/숨김
        document.querySelectorAll('.location-fields').forEach(field => field.classList.remove('active'));
        document.getElementById(type + '-fields').classList.add('active');
        
        // hidden input 값 변경
        document.getElementById('location_type').value = type;
    });
});

// 날짜 유효성 검사
document.getElementById('start_date').addEventListener('change', function() {
    const startDate = this.value;
    const endDateInput = document.getElementById('end_date');
    
    if (startDate) {
        endDateInput.min = startDate;
        if (endDateInput.value && endDateInput.value < startDate) {
            endDateInput.value = startDate;
        }
    }
});

// 폼 제출 처리
document.getElementById('event-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.loading-spinner');
    const btnText = submitBtn.querySelector('.btn-text');
    const errorDiv = document.getElementById('error-message');
    const successDiv = document.getElementById('success-message');
    
    // 버튼 상태 변경
    submitBtn.disabled = true;
    spinner.style.display = 'inline-block';
    btnText.textContent = '등록 중...';
    
    // 메시지 초기화
    errorDiv.style.display = 'none';
    successDiv.style.display = 'none';
    
    try {
        const formData = new FormData(this);
        
        const response = await fetch('/events/store', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            successDiv.textContent = result.message;
            successDiv.style.display = 'block';
            
            // 성공 시 상세 페이지로 이동
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 1500);
        } else {
            errorDiv.textContent = result.message || '행사 등록 중 오류가 발생했습니다.';
            errorDiv.style.display = 'block';
            
            // 스크롤을 에러 메시지로 이동
            errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    } catch (error) {
        console.error('폼 제출 오류:', error);
        errorDiv.textContent = '네트워크 오류가 발생했습니다. 다시 시도해주세요.';
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } finally {
        // 버튼 상태 복원
        submitBtn.disabled = false;
        spinner.style.display = 'none';
        btnText.textContent = '행사 등록';
    }
});

// 오늘 날짜를 최소값으로 설정
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('start_date').min = today;
});
</script>