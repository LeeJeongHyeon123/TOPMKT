    </main>

    <!-- 푸터 -->
    <footer class="main-footer modern-footer">
        <div class="container">
            <div class="footer-content">
                <!-- 상단 영역 -->
                <div class="footer-top">
                    <div class="footer-section">
                        <div class="footer-logo">
                            <div class="logo-icon">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <span class="logo-text"><?= APP_NAME ?? '탑마케팅' ?></span>
                        </div>
                        <p class="footer-description">
                            글로벌 네트워크 마케팅 리더들의 커뮤니티<br>
                            함께 성장하고 성공을 만들어가는 플랫폼
                        </p>
                        <div class="footer-social">
                            <a href="#" class="social-link" aria-label="Facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="Instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="LinkedIn">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <a href="#" class="social-link" aria-label="YouTube">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <div class="footer-section">
                        <h3 class="footer-title">서비스</h3>
                        <ul class="footer-links">
                            <li><a href="/posts">커뮤니티</a></li>
                            <li><a href="/events">행사 일정</a></li>
                            <li><a href="/lectures">강의 일정</a></li>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <li><a href="/auth/login">로그인</a></li>
                                <li><a href="/auth/signup">회원가입</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3 class="footer-title">고객지원</h3>
                        <ul class="footer-links">
                            <li><a href="/help">도움말</a></li>
                            <li><a href="/contact">문의하기</a></li>
                            <li><a href="/faq">자주묻는질문</a></li>
                            <li><a href="/support">고객센터</a></li>
                        </ul>
                    </div>

                    <div class="footer-section">
                        <h3 class="footer-title">정책</h3>
                        <ul class="footer-links">
                            <li><a href="/terms">이용약관</a></li>
                            <li><a href="/privacy">개인정보처리방침</a></li>
                            <li><a href="/community-rules">커뮤니티 가이드</a></li>
                        </ul>
                    </div>
                </div>

                <!-- 하단 영역 -->
                <div class="footer-bottom">
                    <div class="footer-copyright">
                        <p>&copy; <?= date('Y') ?> <?= APP_NAME ?? '탑마케팅' ?>. All rights reserved.</p>
                        <p class="company-info">
                            사업자등록번호: 000-00-00000 | 대표자: 홍길동 | 
                            주소: 서울특별시 강남구 테헤란로 000길 00, 0층
                        </p>
                    </div>
                    <div class="footer-language">
                        <button class="language-toggle" id="language-toggle">
                            <i class="fas fa-globe"></i>
                            <span>한국어</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- 스크립트 -->
    <script>
        // 모바일 메뉴 토글
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            const mobileNav = document.getElementById('main-nav');
            const overlay = document.getElementById('mobile-menu-overlay');

            if (mobileToggle && mobileNav && overlay) {
                mobileToggle.addEventListener('click', function() {
                    mobileNav.classList.toggle('active');
                    overlay.classList.toggle('active');
                    document.body.classList.toggle('mobile-menu-open');
                });

                overlay.addEventListener('click', function() {
                    mobileNav.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.classList.remove('mobile-menu-open');
                });
            }

            // 드롭다운 메뉴
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                if (toggle && menu) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        dropdown.classList.toggle('active');
                    });

                    // 외부 클릭시 드롭다운 닫기
                    document.addEventListener('click', function(e) {
                        if (!dropdown.contains(e.target)) {
                            dropdown.classList.remove('active');
                        }
                    });
                }
            });

            // 알림 자동 사라짐
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => {
                        alert.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>
</html> 