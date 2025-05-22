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
        }
    };
}

// 페이지 로드 완료 시 실행
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded 이벤트 발생');
    TOPMKT.setLoading(true);
});

// 모든 리소스 로드 완료 시 실행
window.addEventListener('load', function() {
    console.log('window.load 이벤트 발생');
    setTimeout(function() {
        console.log('타이머 완료 - 로딩 오버레이 숨기기');
        TOPMKT.setLoading(false);
    }, 500);
}); 