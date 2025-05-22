// 프로필 이미지 모달 기능
document.addEventListener('DOMContentLoaded', function() {
    // 모달 요소들
    const modal = document.getElementById('profileImageModal');
    if (!modal) return; // 모달이 없는 페이지에서는 실행하지 않음

    const modalImg = document.getElementById('modalImage');
    const modalName = document.getElementById('modalName');
    const closeBtn = document.querySelector('.close-modal');

    // 프로필 이미지 클릭 이벤트
    const leaderImages = document.querySelectorAll('.leader-image');

    leaderImages.forEach((container) => {
        const img = container.querySelector('img');
        
        // 이미지 컨테이너 클릭 이벤트
        container.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const imageUrl = img.getAttribute('data-image');
            const name = img.getAttribute('data-name');
            
            if (imageUrl && name) {
                modalImg.src = imageUrl;
                modalName.textContent = name;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                
                requestAnimationFrame(() => {
                    modal.classList.add('show');
                });
            }
        });
    });

    // 모달 닫기 함수
    function closeModal() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }

    // 닫기 버튼 클릭
    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    // 모달 외부 클릭 시 닫기
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // ESC 키로 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            closeModal();
        }
    });
});

// 언어 선택 드롭다운 토글 및 바깥 클릭 시 닫힘 (여러 개 대응)
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.language-selector').forEach(function(selector) {
        const langBtn = selector.querySelector('.language-btn');
        const langDropdown = selector.querySelector('.language-dropdown');
        if (langBtn && langDropdown) {
            langBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                langDropdown.classList.toggle('show');
            });
            document.addEventListener('click', function(e) {
                if (!langDropdown.contains(e.target) && !langBtn.contains(e.target)) {
                    langDropdown.classList.remove('show');
                }
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    langDropdown.classList.remove('show');
                }
            });
        }
    });
});

// 전역 유틸리티 함수
if (typeof TOPMKT === 'undefined') {
    window.TOPMKT = {
        // 로딩 오버레이 제어
        setLoading: function(isLoading) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                if (isLoading) {
                    loadingOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                } else {
                    loadingOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
        },

        // 에러 메시지 표시
        showError: function(message) {
            const errorElement = document.getElementById('errorMessage');
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
        },

        // 에러 메시지 숨기기
        hideError: function() {
            const errorElement = document.getElementById('errorMessage');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        },
        
        // 로그인 상태 확인
        isLoggedIn: function() {
            const authToken = localStorage.getItem('auth_token');
            const userId = localStorage.getItem('user_id');
            const keepLogin = localStorage.getItem('keep_login');
            
            return !!(authToken && userId && keepLogin === 'true');
        },
        
        // 로그인 세션 확장 (토큰 갱신)
        extendSession: function() {
            if (this.isLoggedIn()) {
                // 현재 시간 업데이트
                localStorage.setItem('login_timestamp', Date.now());
                console.log('[DEBUG] 로그인 세션 갱신 완료');
                return true;
            }
            return false;
        }
    };
}

function updateHeaderUI() {
    console.log('[DEBUG] updateHeaderUI() 호출됨');

    // localStorage에서 로그인 관련 모든 값 가져오기 및 로깅
    const authToken = localStorage.getItem('auth_token');
    const userId = localStorage.getItem('user_id');
    const userNickname = localStorage.getItem('user_nickname');
    const keepLogin = localStorage.getItem('keep_login');
    const firebaseIdToken = localStorage.getItem('firebase_id_token');
    const loginTimestamp = localStorage.getItem('login_timestamp');

    console.log('[DEBUG] updateHeaderUI - localStorage 값:', {
        authToken,
        userId,
        userNickname,
        keepLogin,
        firebaseIdToken: firebaseIdToken ? firebaseIdToken.substring(0, 10) + '...' : '없음',
        loginTimestamp
    });

    const isLoggedInStatus = TOPMKT.isLoggedIn();
    console.log('[DEBUG] updateHeaderUI - TOPMKT.isLoggedIn() 결과:', isLoggedInStatus);

    const authButtonsContainer = document.querySelector('.auth-buttons');
    if (!authButtonsContainer) {
        console.log('[DEBUG] auth-buttons container not found');
        return;
    }

    const loginButton = authButtonsContainer.querySelector('.btn-login');
    const registerButton = authButtonsContainer.querySelector('.btn-register');

    if (isLoggedInStatus) {
        console.log('[DEBUG] User is logged in, updating header.');
        if (loginButton) {
            loginButton.textContent = '로그아웃';
            loginButton.href = '#'; 
            loginButton.removeEventListener('click', handleLogoutClick); // 기존 리스너 중복 방지
            loginButton.addEventListener('click', handleLogoutClick);
        }
        if (registerButton) {
            registerButton.style.display = 'none';
        }
        /*
        const nickname = localStorage.getItem('user_nickname');
        if (nickname) {
            const nicknameElement = document.createElement('span');
            nicknameElement.className = 'user-nickname';
            nicknameElement.textContent = nickname + '님 환영합니다!'; 
            nicknameElement.style.color = '#333';
            nicknameElement.style.marginRight = '10px';
            authButtonsContainer.insertBefore(nicknameElement, loginButton);
        }
        */

    } else {
        console.log('[DEBUG] User is not logged in, ensuring default header.');
        if (loginButton) {
            loginButton.textContent = '로그인';
            loginButton.href = '/auth.php';
            loginButton.removeEventListener('click', handleLogoutClick); // 로그아웃 상태에서는 이 리스너 제거
        }
        if (registerButton) {
            registerButton.style.display = ''; 
        }
        const existingNickname = authButtonsContainer.querySelector('.user-nickname');
        if (existingNickname) {
            existingNickname.remove();
        }
    }
}

async function handleLogoutClick(event) {
    event.preventDefault(); // 기본 링크 동작 방지
    console.log('[DEBUG] Logout button clicked');

    try {
        const response = await fetch('/api/auth/logout.php', {
            method: 'POST', // 또는 GET, logout.php 구현에 따라
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.success) {
            console.log('[DEBUG] Logout successful:', result.message);
            // localStorage 항목 삭제
            localStorage.removeItem('auth_token');
            localStorage.removeItem('user_id');
            localStorage.removeItem('user_nickname');
            localStorage.removeItem('keep_login');
            localStorage.removeItem('login_timestamp');
            localStorage.removeItem('firebase_id_token');
            localStorage.removeItem('verification_session_info');
            // 필요에 따라 더 많은 항목 삭제 가능

            // UI 업데이트
            updateHeaderUI();

            // (선택) 홈페이지로 리다이렉트 또는 알림 표시
            // window.location.href = '/'; 
            // alert('로그아웃되었습니다.');
        } else {
            console.error('[ERROR] Logout failed:', result.message);
            alert('로그아웃에 실패했습니다: ' + result.message);
        }
    } catch (error) {
        console.error('[ERROR] Logout request failed:', error);
        alert('로그아웃 요청 중 오류가 발생했습니다.');
    }
}

// 세션 관리를 위한 정기 점검 (5분마다 실행)
setInterval(function() {
    if (TOPMKT.isLoggedIn()) {
        TOPMKT.extendSession();
    }
}, 5 * 60 * 1000);

// 페이지 로드 완료 시 실행
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded 이벤트 발생');
    TOPMKT.setLoading(true);
    
    // 로그인 상태 확인 및 세션 연장
    if (TOPMKT.isLoggedIn()) {
        TOPMKT.extendSession();
    }
    updateHeaderUI(); // <-- Call the function to update header
});

// 모든 리소스 로드 완료 시 실행
window.addEventListener('load', function() {
    console.log('window.load 이벤트 발생');
    setTimeout(function() {
        console.log('타이머 완료 - 로딩 오버레이 숨기기');
        TOPMKT.setLoading(false);
    }, 500);
}); 