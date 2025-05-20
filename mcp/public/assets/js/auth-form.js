// 로그인/회원가입 폼 제출 및 유효성 검사 모듈
// - 로그인/회원가입 폼 제출, 유효성 검사, 에러 처리 등

// 에러 메시지 표시
export function showError(message) {
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    } else {
        alert(message);
    }
}

// 에러 메시지 숨기기
export function hideError() {
    const errorMessage = document.getElementById('errorMessage');
    if (errorMessage) {
        errorMessage.style.display = 'none';
    }
} 