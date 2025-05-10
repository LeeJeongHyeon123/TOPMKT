// 로딩 오버레이 컴포넌트
console.log('[Loading Overlay] 초기화 시작');

// 기존 오버레이 제거
const existingOverlay = document.getElementById('loading-overlay');
if (existingOverlay) {
    console.log('[Loading Overlay] 기존 오버레이 제거');
    existingOverlay.remove();
}

// 새로운 오버레이 생성
const loadingOverlay = document.createElement('div');
loadingOverlay.id = 'loading-overlay';
loadingOverlay.classList.add('loading-overlay', 'hidden');
loadingOverlay.innerHTML = `
    <div class="loading-spinner">
        <div class="spinner"></div>
        <p>로딩 중...</p>
    </div>
`;
document.body.appendChild(loadingOverlay);

console.log('[Loading Overlay] DOM 요소 생성 완료:', loadingOverlay);

// 로딩 상태 설정 함수
window.setLoading = function(isLoading) {
    console.log('[Loading Overlay] setLoading 호출됨:', isLoading);
    
    const overlay = document.getElementById('loading-overlay');
    console.log('[Loading Overlay] 현재 overlay 요소:', overlay);
    
    if (!overlay) {
        console.error('[Loading Overlay] overlay 요소를 찾을 수 없음');
        return;
    }
    
    if (isLoading) {
        console.log('[Loading Overlay] 로딩 시작 - hidden 클래스 제거');
        overlay.classList.remove('hidden');
    } else {
        console.log('[Loading Overlay] 로딩 종료 - hidden 클래스 추가');
        overlay.classList.add('hidden');
    }
    
    console.log('[Loading Overlay] 현재 클래스 목록:', overlay.classList.toString());
};

// 페이지 로드 시 로딩 오버레이 숨기기
window.addEventListener('load', () => {
    console.log('[Loading Overlay] 페이지 로드 이벤트 발생');
    window.setLoading(false);
});

// DOMContentLoaded 이벤트에서도 로그 추가
document.addEventListener('DOMContentLoaded', () => {
    console.log('[Loading Overlay] DOMContentLoaded 이벤트 발생');
    console.log('[Loading Overlay] 현재 body의 자식 요소들:', document.body.children);
});

// 초기 상태 로그
console.log('[Loading Overlay] 초기화 완료');
console.log('[Loading Overlay] 현재 overlay 상태:', document.getElementById('loading-overlay')?.classList.toString()); 