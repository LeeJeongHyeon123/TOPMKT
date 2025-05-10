// 인증번호 발송/타이머/확인 등 인증번호 관련 모듈
// - 인증번호 발송, 타이머, 인증번호 확인, 메시지 표시 등

let verificationTimer = null;

// 타이머 시작 함수
export function startVerificationTimer() {
    const timerElement = document.getElementById('verificationTimer');
    if (!timerElement) return;
    let timeLeft = 180; // 3분 = 180초
    if (verificationTimer) clearInterval(verificationTimer);
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        if (timeLeft <= 0) {
            clearInterval(verificationTimer);
            timerElement.textContent = '시간 만료';
            timerElement.style.color = '#dc3545';
            const verificationCode = document.getElementById('verificationCode');
            if (verificationCode) verificationCode.disabled = true;
        }
        timeLeft--;
    }
    updateTimer();
    verificationTimer = setInterval(updateTimer, 1000);
}

// 타이머 중지 함수
export function stopVerificationTimer() {
    if (verificationTimer) {
        clearInterval(verificationTimer);
        verificationTimer = null;
    }
}

// 인증 메시지 표시
export function showVerificationMessage(message, type) {
    const verificationMessage = document.querySelector('.verification-message');
    verificationMessage.textContent = message;
    verificationMessage.className = `verification-message ${type}`;
    verificationMessage.style.display = 'block';
} 