import React, { useState, useEffect } from 'react';
import { Link, useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { usePageMeta } from '../../hooks/usePageMeta';
import SEOHead from '../../components/common/SEOHead';

const LoginPage: React.FC = () => {
  const [formData, setFormData] = useState({
    phone: '',
    password: '',
    remember: false,
    csrf_token: '',
    redirect: ''
  });
  
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isLoading, setIsLoading] = useState(false);
  const [showPassword, setShowPassword] = useState(false);

  const { login, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const navigate = useNavigate();
  const location = useLocation();

  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '로그인',
    description: '탑마케팅에 로그인하여 네트워크 마케팅 커뮤니티에 참여하세요',
    ogType: 'website'
  });

  // 이미 로그인된 경우 리다이렉트
  useEffect(() => {
    if (isAuthenticated) {
      const redirectTo = new URLSearchParams(location.search).get('redirect') || '/';
      navigate(redirectTo, { replace: true });
    }
  }, [isAuthenticated, navigate, location]);

  // 휴대폰 번호 포맷팅
  const formatPhoneNumber = (value: string) => {
    let numbers = value.replace(/[^0-9]/g, '');
    
    // 최대 11자리까지만 허용 (010-xxxx-xxxx)
    if (numbers.length > 11) {
      numbers = numbers.substring(0, 11);
    }
    
    // 입력이 있지만 010으로 시작하지 않으면 에러 표시 (하지만 입력은 허용)
    if (numbers.length > 0 && numbers.length >= 3 && !numbers.startsWith('010')) {
      setErrors(prev => ({ ...prev, phone: '010으로 시작하는 휴대폰 번호만 입력할 수 있습니다.' }));
    } else {
      setErrors(prev => ({ ...prev, phone: '' }));
    }
    
    // 자동 하이픈 삽입
    if (numbers.length >= 3) {
      numbers = numbers.substring(0, 3) + '-' + numbers.substring(3);
    }
    if (numbers.length >= 8) {
      numbers = numbers.substring(0, 8) + '-' + numbers.substring(8, 12);
    }
    
    return numbers;
  };

  // 입력값 변경 핸들러
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    let newValue = type === 'checkbox' ? checked : value;
    
    // 휴대폰 번호 포맷팅
    if (name === 'phone') {
      newValue = formatPhoneNumber(value);
    }
    
    setFormData(prev => ({
      ...prev,
      [name]: newValue
    }));
  };

  // 폼 제출
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // 폼 검증
    if (!formData.phone.trim() || !formData.password.trim()) {
      error('휴대폰 번호와 비밀번호를 모두 입력해주세요.', '');
      return;
    }
    
    if (!/^010-[0-9]{3,4}-[0-9]{4}$/.test(formData.phone)) {
      error('010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.', '');
      return;
    }

    setIsLoading(true);
    try {
      await login(formData.phone, formData.password, formData.remember);
      success('로그인되었습니다!', '환영합니다');
      const redirectTo = new URLSearchParams(location.search).get('redirect') || '/';
      navigate(redirectTo, { replace: true });
    } catch (err) {
      error('로그인에 실패했습니다.', '휴대폰 번호와 비밀번호를 확인해주세요');
    } finally {
      setIsLoading(false);
    }
  };


  return (
    <>
      <SEOHead {...metaData} />
      
      {/* 로그인 페이지 - 기존 PHP 디자인과 동일 */}
      <section className="auth-section">
        <div className="auth-background">
          <div className="auth-gradient-overlay"></div>
          <div className="auth-shapes">
            <div className="auth-shape auth-shape-1"></div>
            <div className="auth-shape auth-shape-2"></div>
            <div className="auth-shape auth-shape-3"></div>
          </div>
        </div>

        <div className="container">
          <div className="auth-content">
            {/* 로그인 폼 컨테이너 */}
            <div className="auth-form-container">
              {/* 로고 및 제목 */}
              <div className="auth-header">
                <div className="auth-logo">
                  <div className="logo-icon">
                    <i className="fas fa-rocket"></i>
                  </div>
                  <span className="logo-text">탑마케팅</span>
                </div>
                <h1 className="auth-title">다시 만나서 반갑습니다</h1>
                <p className="auth-subtitle">계정에 로그인하여 커뮤니티 활동을 계속하세요</p>
              </div>

              {/* 로그인 폼 */}
              <form className="auth-form" onSubmit={handleSubmit} id="login-form">
                <div className="form-group">
                  <label htmlFor="phone" className="form-label">
                    <i className="fas fa-mobile-alt"></i>
                    휴대폰 번호
                  </label>
                  <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    className={`form-input ${errors.phone ? 'error' : ''}`}
                    placeholder="010-1234-5678"
                    value={formData.phone}
                    onChange={handleInputChange}
                    required 
                    autoComplete="tel"
                    pattern="010-[0-9]{3,4}-[0-9]{4}"
                    maxLength={13}
                  />
                  {errors.phone && <div className="error-message">{errors.phone}</div>}
                  <small className="form-help">회원가입 시 사용한 휴대폰 번호를 입력하세요</small>
                </div>

                <div className="form-group">
                  <label htmlFor="password" className="form-label">
                    <i className="fas fa-lock"></i>
                    비밀번호
                  </label>
                  <div className="password-input-wrapper relative">
                    <input 
                      type={showPassword ? "text" : "password"}
                      id="password" 
                      name="password" 
                      className="form-input" 
                      placeholder="비밀번호를 입력하세요"
                      value={formData.password}
                      onChange={handleInputChange}
                      required 
                      autoComplete="current-password"
                    />
                    <button 
                      type="button" 
                      className="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      onClick={() => setShowPassword(!showPassword)}
                    >
                      <i className={`fas fa-${showPassword ? 'eye-slash' : 'eye'}`}></i>
                    </button>
                  </div>
                </div>

                <div className="form-options">
                  <label className="checkbox-label">
                    <input 
                      type="checkbox" 
                      name="remember" 
                      checked={formData.remember}
                      onChange={handleInputChange}
                    />
                    <span className="checkbox-custom"></span>
                    <span className="checkbox-text">로그인 상태 유지</span>
                  </label>
                  <Link to="/forgot-password" className="auth-link">
                    비밀번호를 잊으셨나요?
                  </Link>
                </div>

                {/* 숨겨진 필드들 */}
                <input type="hidden" name="csrf_token" value={formData.csrf_token} />
                <input type="hidden" name="redirect" value={formData.redirect} />

                <button 
                  type="submit" 
                  className="btn btn-primary-gradient btn-large btn-full"
                  disabled={isLoading}
                >
                  <i className="fas fa-sign-in-alt"></i>
                  <span>{isLoading ? '로그인 중...' : '로그인'}</span>
                </button>
              </form>

              {/* 회원가입 링크 */}
              <div className="auth-footer">
                <p className="auth-switch">
                  아직 계정이 없으신가요? 
                  <Link to="/auth/signup" className="auth-link ml-1">
                    회원가입하기
                    <i className="fas fa-arrow-right ml-1"></i>
                  </Link>
                </p>
              </div>

            </div>

            {/* 사이드 정보 */}
            <div className="auth-side-info">
              <div className="side-info-content">
                <div className="side-info-icon">
                  <i className="fas fa-lock"></i>
                </div>
                <h2>안전한 로그인</h2>
                <p>최신 보안 기술로 여러분의 계정을 안전하게 보호합니다</p>
                
                <div className="security-features">
                  <div className="security-feature">
                    <i className="fas fa-shield-alt"></i>
                    <span>SSL 암호화</span>
                  </div>
                  <div className="security-feature">
                    <i className="fas fa-user-shield"></i>
                    <span>2단계 인증</span>
                  </div>
                  <div className="security-feature">
                    <i className="fas fa-history"></i>
                    <span>로그인 기록</span>
                  </div>
                </div>

                <div className="login-benefits">
                  <h3>로그인 후 이용 가능한 서비스</h3>
                  <ul>
                    <li><i className="fas fa-comments"></i> 커뮤니티 참여</li>
                    <li><i className="fas fa-bell"></i> 실시간 알림</li>
                    <li><i className="fas fa-chart-line"></i> 성과 분석 도구</li>
                    <li><i className="fas fa-graduation-cap"></i> 전문가 강의</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 원본 PHP와 동일한 스타일 */}
      <style>{`
        /* 인증 페이지 기본 스타일 */
        .auth-section {
          min-height: 100vh;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          position: relative;
          display: flex;
          align-items: center;
          overflow: hidden;
          padding-top: 120px;
          padding-bottom: 60px;
        }

        .auth-background {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 1;
        }

        .auth-gradient-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .auth-shapes {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          overflow: hidden;
        }

        .auth-shape {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.1);
          animation: authFloat 8s ease-in-out infinite;
        }

        .auth-shape-1 {
          width: 120px;
          height: 120px;
          top: 15%;
          left: 10%;
          animation-delay: 0s;
        }

        .auth-shape-2 {
          width: 180px;
          height: 180px;
          top: 60%;
          right: 15%;
          animation-delay: 3s;
        }

        .auth-shape-3 {
          width: 90px;
          height: 90px;
          top: 30%;
          right: 25%;
          animation-delay: 6s;
        }

        @keyframes authFloat {
          0%, 100% { 
            transform: translateY(0px) rotate(0deg) scale(1); 
            opacity: 0.6;
          }
          33% { 
            transform: translateY(-15px) rotate(120deg) scale(1.1); 
            opacity: 0.8;
          }
          66% { 
            transform: translateY(-5px) rotate(240deg) scale(0.9); 
            opacity: 0.4;
          }
        }

        .auth-content {
          position: relative;
          z-index: 2;
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 80px;
          align-items: start;
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 20px;
        }

        .auth-form-container {
          background: rgba(255, 255, 255, 0.95);
          backdrop-filter: blur(20px);
          border-radius: 20px;
          padding: 40px;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-header {
          text-align: center;
          margin-bottom: 32px;
        }

        .auth-logo {
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 24px;
          gap: 12px;
        }

        .auth-logo .logo-icon {
          width: 48px;
          height: 48px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.5rem;
        }

        .auth-logo .logo-text {
          font-size: 1.8rem;
          font-weight: 700;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          -webkit-background-clip: text;
          background-clip: text;
          -webkit-text-fill-color: transparent;
        }

        .auth-title {
          font-size: 2rem;
          font-weight: 700;
          color: #1a202c;
          margin-bottom: 12px;
          line-height: 1.2;
        }

        .auth-subtitle {
          font-size: 1rem;
          color: #64748b;
          line-height: 1.5;
        }

        .auth-form {
          margin-top: 32px;
        }

        .form-group {
          margin-bottom: 24px;
        }

        .form-label {
          display: flex;
          align-items: center;
          gap: 8px;
          font-weight: 500;
          color: #374151;
          margin-bottom: 8px;
          font-size: 0.95rem;
        }

        .form-label i {
          width: 16px;
          color: #64748b;
          font-size: 0.9rem;
        }

        .form-input {
          width: 100%;
          padding: 14px 16px;
          border: 2px solid #e2e8f0;
          border-radius: 12px;
          font-size: 1rem;
          transition: all 0.3s ease;
          background: white;
          box-sizing: border-box;
        }

        .form-input:focus {
          outline: none;
          border-color: #667eea;
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
          color: #94a3b8;
        }

        .form-input.error {
          border-color: #ef4444;
          background: #fef2f2;
        }

        .form-help {
          display: block;
          margin-top: 6px;
          font-size: 0.8rem;
          color: #9ca3af;
        }

        .error-message {
          margin-top: 6px;
          font-size: 0.8rem;
          color: #ef4444;
        }

        .password-input-wrapper {
          position: relative;
        }

        .password-toggle {
          position: absolute;
          right: 16px;
          top: 50%;
          transform: translateY(-50%);
          background: none;
          border: none;
          color: #9ca3af;
          cursor: pointer;
          padding: 4px;
          transition: color 0.3s ease;
        }

        .password-toggle:hover {
          color: #374151;
        }

        .form-options {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          flex-wrap: wrap;
          gap: 10px;
        }

        .checkbox-label {
          display: flex;
          align-items: center;
          gap: 8px;
          cursor: pointer;
          font-size: 0.9rem;
          color: #374151;
        }

        .checkbox-label input[type="checkbox"] {
          display: none;
        }

        .checkbox-custom {
          width: 18px;
          height: 18px;
          border: 2px solid #d1d5db;
          border-radius: 4px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.3s ease;
        }

        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-color: #667eea;
        }

        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
          content: '✓';
          color: white;
          font-size: 0.75rem;
          font-weight: bold;
        }

        .auth-link {
          color: #667eea;
          text-decoration: none;
          font-weight: 500;
          display: inline-flex;
          align-items: center;
          gap: 4px;
          transition: color 0.3s ease;
        }

        .auth-link:hover {
          color: #5a67d8;
          text-decoration: underline;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 16px 32px;
          border: none;
          border-radius: 12px;
          font-weight: 600;
          font-size: 1rem;
          text-decoration: none;
          transition: all 0.3s ease;
          cursor: pointer;
          min-height: 50px;
        }

        .btn-primary-gradient {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-gradient:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-gradient:disabled {
          opacity: 0.7;
          cursor: not-allowed;
          transform: none;
        }

        .btn-large {
          padding: 18px 36px;
          font-size: 1.1rem;
        }

        .btn-full {
          width: 100%;
        }


        .auth-footer {
          text-align: center;
          margin-top: 32px;
          padding-top: 24px;
          border-top: 1px solid #e2e8f0;
        }

        .auth-switch {
          color: #64748b;
          font-size: 0.95rem;
          margin: 0;
        }

        .auth-side-info {
          display: flex;
          flex-direction: column;
          justify-content: center;
          color: white;
          padding: 40px;
        }

        .side-info-content {
          max-width: 400px;
        }

        .side-info-icon {
          width: 80px;
          height: 80px;
          background: rgba(255, 255, 255, 0.2);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 30px;
          backdrop-filter: blur(10px);
        }

        .side-info-icon i {
          font-size: 2rem;
          color: white;
        }

        .auth-side-info h2 {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 20px;
          color: white;
        }

        .auth-side-info p {
          font-size: 1.1rem;
          color: rgba(255, 255, 255, 0.9);
          line-height: 1.6;
          margin-bottom: 0;
        }

        .security-features {
          display: flex;
          flex-direction: column;
          gap: 12px;
          margin: 20px 0;
        }

        .security-feature {
          display: flex;
          align-items: center;
          gap: 10px;
          color: rgba(255, 255, 255, 0.8);
          font-size: 14px;
        }

        .security-feature i {
          color: #10b981;
          width: 20px;
        }

        .login-benefits {
          margin-top: 30px;
        }

        .login-benefits h3 {
          font-size: 16px;
          margin-bottom: 15px;
          color: white;
        }

        .login-benefits ul {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .login-benefits li {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-bottom: 8px;
          color: rgba(255, 255, 255, 0.8);
          font-size: 14px;
        }

        .login-benefits li i {
          color: #fbbf24;
          width: 16px;
        }


        /* 알림 메시지 스타일 */
        .alert {
          padding: 12px 16px;
          border-radius: 6px;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          gap: 10px;
          font-size: 14px;
          line-height: 1.4;
        }

        .alert-error {
          background-color: #fee;
          border: 1px solid #fcc;
          color: #c33;
        }

        .alert-success {
          background-color: #efe;
          border: 1px solid #cfc;
          color: #363;
        }

        .alert i {
          font-size: 16px;
        }

        /* 반응형 */
        @media (max-width: 1024px) {
          .auth-section {
            padding-top: 100px;
            min-height: auto;
          }

          .auth-content {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 20px;
          }

          .auth-side-info {
            order: -1;
            text-align: center;
            padding: 20px;
          }

          .auth-side-info h2 {
            font-size: 1.8rem;
          }
        }

        @media (max-width: 768px) {
          .auth-section {
            padding-top: 80px;
          }

          .auth-form-container {
            padding: 30px 20px;
          }

          .auth-title {
            font-size: 1.5rem;
          }

          .auth-side-info h2 {
            font-size: 1.6rem;
          }

          .form-options {
            flex-direction: column;
            align-items: flex-start;
          }
        }
      `}</style>
    </>
  );
};

export default LoginPage;