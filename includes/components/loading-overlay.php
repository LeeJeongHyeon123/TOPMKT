<?php
/**
 * 로딩 오버레이 컴포넌트
 * 
 * 사용 방법:
 * <?php include_once __DIR__ . '/../includes/components/loading-overlay.php'; ?>
 */
?>
<div id="loadingOverlay" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 20px;
">
    <div style="
        width: 60px;
        height: 60px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    "></div>
    <div style="
        color: #3498db;
        font-size: 16px;
        font-weight: 500;
    ">로딩 중...</div>
</div>

<style>
/* 로딩 오버레이 스타일 */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    z-index: 999999;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 20px;
    opacity: 1;
    visibility: visible;
    transition: opacity 0.3s ease-out, visibility 0.3s ease-out;
    pointer-events: auto;
}

.loading-overlay.hidden {
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
}

.loading-overlay .spinner {
    width: 60px;
    height: 60px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-overlay .loading-text {
    color: #3498db;
    font-size: 16px;
    font-weight: 500;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// 로딩 오버레이 요소
const loadingOverlay = document.getElementById('loadingOverlay');

// 로딩 상태 관리 함수
function setLoading(isLoading) {
    if (!loadingOverlay) return;
    
    if (isLoading) {
        loadingOverlay.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    } else {
        loadingOverlay.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// 페이지 로드 시 초기 로딩 상태 설정
document.addEventListener('DOMContentLoaded', function() {
    setLoading(true);
});

// 페이지 완전 로드 시
window.addEventListener('load', function() {
    setLoading(false);
});

// 전역 스코프에 함수 노출
window.setLoading = setLoading;
</script> 