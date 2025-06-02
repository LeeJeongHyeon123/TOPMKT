<?php
/**
 * 탑마케팅 회원가입 페이지
 */
$page_title = '회원가입';
$page_description = '탑마케팅에 가입하여 글로벌 네트워크 마케팅 커뮤니티에 참여하세요';
$current_page = 'signup';

require_once SRC_PATH . '/views/templates/header.php';
?>

<!-- 회원가입 페이지 -->
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
            <!-- 회원가입 폼 컨테이너 -->
            <div class="auth-form-container">
                <!-- 로고 및 제목 -->
                <div class="auth-header">
                    <div class="auth-logo">
                        <div class="logo-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <span class="logo-text"><?= APP_NAME ?? '탑마케팅' ?></span>
                    </div>
                    <h1 class="auth-title">새로운 여정을 시작하세요</h1>
                    <p class="auth-subtitle">글로벌 네트워크 마케팅 커뮤니티에 가입하여 성공을 함께 만들어가세요</p>
                </div>

                <!-- 회원가입 폼 -->
                <form class="auth-form" method="POST" action="/auth/signup">
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i>
                            사용자명
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            placeholder="사용자명을 입력하세요"
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            required 
                            autocomplete="username"
                        >
                    </div>

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
                        <small class="form-help">로그인 시 사용할 휴대폰 번호입니다</small>
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
                                placeholder="비밀번호를 입력하세요 (8자 이상)"
                                required 
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle" id="password-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm" class="form-label">
                            <i class="fas fa-lock"></i>
                            비밀번호 확인
                        </label>
                        <div class="password-input-wrapper">
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                class="form-input" 
                                placeholder="비밀번호를 다시 입력하세요"
                                required 
                                autocomplete="new-password"
                            >
                            <button type="button" class="password-toggle" id="password-confirm-toggle">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="terms" value="1" required>
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">
                                <a href="/terms" target="_blank">이용약관</a> 및 
                                <a href="/privacy" target="_blank">개인정보처리방침</a>에 동의합니다
                            </span>
                        </label>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="marketing" value="1">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">마케팅 정보 수신에 동의합니다 (선택)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary-gradient btn-large btn-full">
                        <i class="fas fa-user-plus"></i>
                        <span>회원가입</span>
                    </button>
                </form>

                <!-- 로그인 링크 -->
                <div class="auth-footer">
                    <p class="auth-switch">
                        이미 계정이 있으신가요? 
                        <a href="/auth/login" class="auth-link">
                            로그인하기
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </p>
                </div>
            </div>

            <!-- 사이드 정보 -->
            <div class="auth-side-info">
                <div class="side-info-content">
                    <div class="side-info-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h2>성공의 시작</h2>
                    <p>전 세계 네트워크 마케팅 전문가들과 함께 새로운 기회를 발견하고 성장하세요</p>
                    
                    <div class="info-stats">
                        <div class="info-stat">
                            <div class="stat-number">10,000+</div>
                            <div class="stat-label">글로벌 멤버</div>
                        </div>
                        <div class="info-stat">
                            <div class="stat-number">24/7</div>
                            <div class="stat-label">언제든지 소통</div>
                        </div>
                        <div class="info-stat">
                            <div class="stat-number">100+</div>
                            <div class="stat-label">전문 콘텐츠</div>
                        </div>
                    </div>

                    <div class="signup-benefits">
                        <h3>가입 혜택</h3>
                        <ul>
                            <li><i class="fas fa-check"></i> 무료 커뮤니티 액세스</li>
                            <li><i class="fas fa-check"></i> 전문가 네트워킹 기회</li>
                            <li><i class="fas fa-check"></i> 독점 행사 및 강의 참여</li>
                            <li><i class="fas fa-check"></i> 실시간 마케팅 인사이트</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // 비밀번호 표시/숨김 토글
    document.addEventListener('DOMContentLoaded', function() {
        // 비밀번호 토글
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('password-toggle');
        
        // 비밀번호 확인 토글
        const passwordConfirmInput = document.getElementById('password_confirm');
        const passwordConfirmToggle = document.getElementById('password-confirm-toggle');

        function setupPasswordToggle(input, toggle) {
            if (toggle && input) {
                toggle.addEventListener('click', function() {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    
                    const icon = toggle.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        }

        setupPasswordToggle(passwordInput, passwordToggle);
        setupPasswordToggle(passwordConfirmInput, passwordConfirmToggle);

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
                const username = document.getElementById('username').value;
                const phone = document.getElementById('phone').value;
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirm').value;
                const termsAccepted = document.querySelector('input[name="terms"]').checked;

                // 필수 입력 확인
                if (!username || !phone || !password || !passwordConfirm) {
                    e.preventDefault();
                    alert('모든 필수 항목을 입력해주세요.');
                    return false;
                }

                // 휴대폰 번호 유효성 검사
                if (!isValidPhone(phone)) {
                    e.preventDefault();
                    alert('올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)');
                    return false;
                }

                // 비밀번호 길이 확인
                if (password.length < 8) {
                    e.preventDefault();
                    alert('비밀번호는 8자 이상이어야 합니다.');
                    return false;
                }

                // 비밀번호 일치 확인
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('비밀번호가 일치하지 않습니다.');
                    return false;
                }

                // 이용약관 동의 확인
                if (!termsAccepted) {
                    e.preventDefault();
                    alert('이용약관에 동의해주세요.');
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

    .signup-benefits {
        margin-top: 40px;
    }

    .signup-benefits h3 {
        color: white;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 16px;
    }

    .signup-benefits ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .signup-benefits li {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .signup-benefits i {
        width: 16px;
        height: 16px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }

    .checkbox-text a {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }

    .checkbox-text a:hover {
        text-decoration: underline;
    }
</style>

<?php require_once SRC_PATH . '/views/templates/footer.php'; ?> 