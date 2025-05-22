<?php
// 현재 언어 설정 확인
$currentLang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'ko');
?>
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section company-info">
                    <h3>탑마케팅</h3>
                    <p>전 세계 네트워크 마케팅 리더들이 모이는 No.1 커뮤니티</p>
                    <div class="footer-social">
                        <a href="#" class="social-link" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link" title="Telegram"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
                <div class="footer-section support-info">
                    <h3>고객지원</h3>
                    <ul class="footer-links">
                        <li><a href="/faq">자주 묻는 질문</a></li>
                        <li><a href="/terms">이용약관</a></li>
                        <li><a href="/privacy">개인정보처리방침</a></li>
                        <li><a href="/contact">문의하기</a></li>
                    </ul>
                </div>
                <div class="footer-section contact-info">
                    <h3>연락처</h3>
                    <ul class="footer-contact">
                        <li><i class="fas fa-phone"></i> 02-1234-5678</li>
                        <li><i class="fas fa-envelope"></i> support@topmkt.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 서울특별시 강남구 테헤란로 123</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 탑마케팅. All rights reserved.</p>
                <div class="footer-lang">
                    <button onclick="changeLanguage('ko')" class="lang-btn <?= $currentLang === 'ko' ? 'active' : '' ?>">
                        <span class="flag">🇰🇷</span> 한국어
                    </button>
                    <button onclick="changeLanguage('en')" class="lang-btn <?= $currentLang === 'en' ? 'active' : '' ?>">
                        <span class="flag">🇺🇸</span> English
                    </button>
                    <button onclick="changeLanguage('zh')" class="lang-btn <?= $currentLang === 'zh' ? 'active' : '' ?>">
                        <span class="flag">🇨🇳</span> 中文
                    </button>
                    <button onclick="changeLanguage('ja')" class="lang-btn <?= $currentLang === 'ja' ? 'active' : '' ?>">
                        <span class="flag">🇯🇵</span> 日本語
                    </button>
                    <button onclick="changeLanguage('vi')" class="lang-btn <?= $currentLang === 'vi' ? 'active' : '' ?>">
                        <span class="flag">🇻🇳</span> Tiếng Việt
                    </button>
                    <button onclick="changeLanguage('th')" class="lang-btn <?= $currentLang === 'th' ? 'active' : '' ?>">
                        <span class="flag">🇹🇭</span> ไทย
                    </button>
                </div>
            </div>
        </div>
    </footer>

    <!-- 공통 스크립트 -->
    <script>
    // 언어 변경 함수
    function changeLanguage(lang) {
        // 현재 URL의 쿼리 파라미터를 가져옴
        const urlParams = new URLSearchParams(window.location.search);
        // lang 파라미터를 업데이트
        urlParams.set('lang', lang);
        // 현재 페이지 경로와 업데이트된 쿼리 파라미터로 이동
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    }
    </script>
    <script src="/resources/js/main.js"></script>
</body>
</html> 