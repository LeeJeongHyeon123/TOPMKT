<?php
/**
 * 탑마케팅 로그인 페이지
 */
$page_title = '로그인';
$page_description = '탑마케팅에 로그인하여 글로벌 네트워크 마케팅 커뮤니티에 참여하세요';
$current_page = 'login';

require_once SRC_PATH . '/views/templates/header.php';
?>

<!-- 로그인 페이지 -->
<section class="auth-section">
    <div class="auth-background">
        <div class="auth-gradient-overlay"></div>
        <div class="auth-shapes">
            <div class="auth-shape auth-shape-1"></div>
            <div class="auth-shape auth-shape-2"></div>
            <div class="auth-shape auth-shape-3"></div>
        </div>
    </div>

    <div class="container">
        <div class="auth-content">
            <!-- 로그인 폼 컨테이너 -->
            <div class="auth-form-container">
                <!-- 로고 및 제목 -->
                <div class="auth-header">
                    <div class="auth-logo">
                        <div class="logo-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <span class="logo-text"><?= APP_NAME ?? '탑마케팅' ?></span>
                    </div>
                    <h1 class="auth-title">다시 만나서 반갑습니다</h1>
                    <p class="auth-subtitle">휴대폰 번호로 로그인하여 탑마케팅 커뮤니티에 참여하세요</p>
                </div>

                <!-- 로그인 폼 -->
                <form class="auth-form" method="POST" action="/auth/login">
                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="fas fa-mobile-alt"></i>
                            휴대폰 번호
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            class="form-input" 
                            placeholder="010-1234-5678"
                            value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                            required 
                            autocomplete="tel"
                            pattern="[0-9]{3}-[0-9]{3,4}-[0-9]{4}"
                        >
                        <small class="form-help">하이픈(-)을 포함하여 입력해주세요</small>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            비밀번호
                        </label>
                        <div class="password-input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                placeholder="비밀번호를 입력하세요"
                                required 
                                autocomplete="current-password"
                            >
                            <button type="button" class="password-toggle" id="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">로그인 상태 유지</span>
                        </label>
                        <a href="/auth/forgot-password" class="forgot-password">비밀번호 찾기</a>
                    </div>

                    <button type="submit" class="btn btn-primary-gradient btn-large btn-full">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>로그인</span>
                    </button>
                </form>

                <!-- 회원가입 링크 -->
                <div class="auth-footer">
                    <p class="auth-switch">
                        아직 계정이 없으신가요? 
                        <a href="/auth/signup" class="auth-link">
                            회원가입하기
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- 사이드 정보 -->
            <div class="auth-side-info">
                <div class="side-info-content">
                    <div class="side-info-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <h2>네트워킹의 힘</h2>
                    <p>전 세계 마케팅 전문가들과 연결되어 함께 성장하고 새로운 기회를 만들어가세요</p>
                    
                    <div class="info-stats">
                        <div class="info-stat">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">활성 멤버</div>
                        </div>
                        <div class="info-stat">
                            <div class="stat-number">50+</div>
                            <div class="stat-label">국가별 네트워크</div>
                        </div>
                        <div class="info-stat">
                            <div class="stat-number">98%</div>
                            <div class="stat-label">만족도</div>
                        </div>
                    </div>

                    <div class="testimonial">
                        <blockquote>
                            "탑마케팅을 통해 전 세계 파트너들과 연결되어 비즈니스를 확장할 수 있었습니다."
                        </blockquote>
                        <cite>- 김성공, 다이아몬드 리더</cite>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // 비밀번호 표시/숨김 토글
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');

        if (passwordToggle && passwordInput) {
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                const icon = passwordToggle.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        // 휴대폰 번호 자동 포맷팅
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^\d]/g, '');
                
                if (value.length >= 3) {
                    if (value.length >= 7) {
                        value = value.replace(/(\d{3})(\d{3,4})(\d{4})/, '$1-$2-$3');
                    } else {
                        value = value.replace(/(\d{3})(\d{1,4})/, '$1-$2');
                    }
                }
                
                e.target.value = value;
            });
        }

        // 폼 유효성 검사
        const form = document.querySelector('.auth-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const phone = document.getElementById('phone').value;
                const password = document.getElementById('password').value;

                if (!phone || !password) {
                    e.preventDefault();
                    alert('휴대폰 번호와 비밀번호를 모두 입력해주세요.');
                    return false;
                }

                // 휴대폰 번호 유효성 검사
                if (!isValidPhone(phone)) {
                    e.preventDefault();
                    alert('올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)');
                    return false;
                }
            });
        }

        function isValidPhone(phone) {
            const phoneRegex = /^01[0-9]-[0-9]{3,4}-[0-9]{4}$/;
            return phoneRegex.test(phone);
        }
    });
</script>

<style>
    .form-help {
        display: block;
        font-size: 0.8rem;
        color: #94a3b8;
        margin-top: 4px;
    }

    .testimonial {
        margin-top: 40px;
        padding: 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        border-left: 4px solid rgba(255, 255, 255, 0.3);
    }

    .testimonial blockquote {
        font-style: italic;
        margin: 0 0 12px 0;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .testimonial cite {
        font-size: 0.85rem;
        opacity: 0.8;
        font-style: normal;
    }

    .forgot-password {
        color: #667eea;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .forgot-password:hover {
        text-decoration: underline;
    }
</style>

<?php require_once SRC_PATH . '/views/templates/footer.php'; ?> 