import React, { useState, useEffect, useCallback } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { usePageMeta } from '../../hooks/usePageMeta';
import SEOHead from '../../components/common/SEOHead';

// reCAPTCHA 타입 정의
declare global {
  interface Window {
    grecaptcha: {
      execute: (siteKey: string, options: { action: string }) => Promise<string>;
      render: (element: string, options: any) => void;
    };
  }
}

const SignupPage: React.FC = () => {
  const [formData, setFormData] = useState({
    nickname: '',
    phone: '',
    email: '',
    password: '',
    password_confirm: '',
    verification_code: '',
    terms: false,
    marketing: false,
    phone_verified: '0',
    csrf_token: '',
    recaptcha_token: ''
  });

  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isLoading, setIsLoading] = useState(false);
  const [verificationSent, setVerificationSent] = useState(false);
  const [timer, setTimer] = useState(180); // 3분 = 180초
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirm, setShowPasswordConfirm] = useState(false);
  const [isFormValid, setIsFormValid] = useState(false);
  const [phoneVerified, setPhoneVerified] = useState(false);

  const { isAuthenticated } = useAuth();
  const { success, error, info } = useToast();
  const navigate = useNavigate();

  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '회원가입',
    description: '탑마케팅에 가입하여 글로벌 네트워크 마케팅 커뮤니티에 참여하세요',
    ogType: 'website'
  });

  // 이미 로그인된 경우 리다이렉트
  useEffect(() => {
    if (isAuthenticated) {
      navigate('/', { replace: true });
    }
  }, [isAuthenticated, navigate]);

  // 인증번호 타이머 (3분 = 180초)
  useEffect(() => {
    let interval: NodeJS.Timeout;
    if (verificationSent && timer > 0) {
      interval = setInterval(() => {
        setTimer(prev => prev - 1);
      }, 1000);
    } else if (timer === 0) {
      setVerificationSent(false);
    }
    return () => clearInterval(interval);
  }, [verificationSent, timer]);

  // 실시간 폼 검증
  const validateForm = useCallback(() => {
    const isNicknameValid = formData.nickname.length >= 2 && formData.nickname.length <= 20;
    const isPhoneValid = /^010-[0-9]{3,4}-[0-9]{4}$/.test(formData.phone);
    const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email);
    const isPasswordValid = formData.password.length >= 8;
    const isPasswordMatch = formData.password === formData.password_confirm;
    const isTermsAgreed = formData.terms;
    
    const allValid = isNicknameValid && isPhoneValid && isEmailValid && 
                    isPasswordValid && isPasswordMatch && isTermsAgreed && phoneVerified;
    
    setIsFormValid(allValid);
    return allValid;
  }, [formData, phoneVerified]);

  // 폼 데이터 변경 시 실시간 검증
  useEffect(() => {
    validateForm();
  }, [validateForm]);

  // 닉네임 검증 (2-20자, 한글/영문/숫자)
  const validateNickname = (nickname: string) => {
    if (!nickname.trim()) {
      return '닉네임을 입력해주세요.';
    }
    if (nickname.length < 2) {
      return '닉네임은 2자 이상 입력해주세요.';
    }
    if (nickname.length > 20) {
      return '닉네임은 20자 이하로 입력해주세요.';
    }
    return '';
  };

  // 휴대폰 번호 검증 및 포맷팅
  const formatPhoneNumber = (value: string) => {
    let numbers = value.replace(/[^0-9]/g, '');
    
    // 010으로 시작하지 않으면 에러
    if (numbers.length > 0 && !numbers.startsWith('010')) {
      setErrors(prev => ({ ...prev, phone: '010으로 시작하는 휴대폰 번호만 입력할 수 있습니다.' }));
      return formData.phone;
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

  const validatePhone = (phone: string) => {
    if (!phone.trim()) {
      return '휴대폰 번호를 입력해주세요.';
    }
    if (!/^010-[0-9]{3,4}-[0-9]{4}$/.test(phone)) {
      return '010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.';
    }
    return '';
  };

  // 이메일 검증
  const validateEmail = (email: string) => {
    if (!email.trim()) {
      return '이메일을 입력해주세요.';
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      return '올바른 이메일 형식을 입력해주세요.';
    }
    return '';
  };

  // 비밀번호 검증 (8자 이상, 영문/숫자/특수문자)
  const validatePassword = (password: string) => {
    if (!password.trim()) {
      return '비밀번호를 입력해주세요.';
    }
    if (password.length < 8) {
      return '비밀번호는 8자 이상 입력해주세요.';
    }
    // 영문, 숫자, 특수문자 포함 검증
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    if (!hasLetter || !hasNumber || !hasSpecial) {
      return '영문, 숫자, 특수문자를 포함하여 8자 이상 입력해주세요.';
    }
    return '';
  };

  // 비밀번호 확인 검증
  const validatePasswordConfirm = (passwordConfirm: string) => {
    if (!passwordConfirm.trim()) {
      return '비밀번호 확인을 입력해주세요.';
    }
    if (formData.password !== passwordConfirm) {
      return '비밀번호가 일치하지 않습니다.';
    }
    return '';
  };

  // reCAPTCHA 토큰 생성
  const generateRecaptchaToken = async (action: string): Promise<string> => {
    try {
      if (window.grecaptcha) {
        return await window.grecaptcha.execute('6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb', { action });
      }
    } catch (err) {
      console.error('reCAPTCHA error:', err);
    }
    return '';
  };

  // 입력값 변경 핸들러
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    let newValue = type === 'checkbox' ? checked : value;
    
    // 휴대폰 번호 포맷팅
    if (name === 'phone') {
      newValue = formatPhoneNumber(value);
      // 휴대폰 번호 변경 시 인증 상태 초기화
      if (phoneVerified) {
        setPhoneVerified(false);
        setFormData(prev => ({ ...prev, phone_verified: '0' }));
        setErrors(prev => ({ ...prev, phone: '' }));
      }
    }
    
    setFormData(prev => ({
      ...prev,
      [name]: newValue
    }));

    // 실시간 검증
    let errorMessage = '';
    switch (name) {
      case 'nickname':
        errorMessage = validateNickname(value);
        break;
      case 'phone':
        errorMessage = validatePhone(newValue as string);
        break;
      case 'email':
        errorMessage = validateEmail(value);
        break;
      case 'password':
        errorMessage = validatePassword(value);
        // 비밀번호 확인도 재검증
        if (formData.password_confirm) {
          const confirmError = validatePasswordConfirm(formData.password_confirm);
          setErrors(prev => ({ ...prev, password_confirm: confirmError }));
        }
        break;
      case 'password_confirm':
        errorMessage = validatePasswordConfirm(value);
        break;
    }
    
    setErrors(prev => ({
      ...prev,
      [name]: errorMessage
    }));
  };

  // SMS 인증번호 발송
  const sendVerificationCode = async () => {
    const phoneError = validatePhone(formData.phone);
    if (phoneError) {
      setErrors(prev => ({ ...prev, phone: phoneError }));
      return;
    }

    setIsLoading(true);
    try {
      const recaptchaToken = await generateRecaptchaToken('send_verification');
      
      // API 호출 (실제 구현 시)
      // await AuthService.sendVerificationCode(formData.phone, recaptchaToken);
      console.log('reCAPTCHA token:', recaptchaToken);
      
      setVerificationSent(true);
      setTimer(180); // 3분 타이머 시작
      success('인증번호가 발송되었습니다.', '확인해주세요');
      info('인증번호는 3분간 유효합니다.');
    } catch (err) {
      error('인증번호 발송에 실패했습니다.', '다시 시도해주세요');
    } finally {
      setIsLoading(false);
    }
  };

  // 인증번호 확인
  const verifyCode = async () => {
    if (!formData.verification_code || formData.verification_code.length !== 4) {
      setErrors(prev => ({ ...prev, verification_code: '4자리 인증번호를 입력해주세요.' }));
      return;
    }

    setIsLoading(true);
    try {
      // API 호출 (실제 구현 시)
      // await AuthService.verifyCode(formData.phone, formData.verification_code);
      
      setPhoneVerified(true);
      setFormData(prev => ({ ...prev, phone_verified: '1' }));
      setVerificationSent(false);
      success('휴대폰 인증이 완료되었습니다.', '');
    } catch (err) {
      error('인증번호가 올바르지 않습니다.', '다시 확인해주세요');
    } finally {
      setIsLoading(false);
    }
  };

  // 폼 제출
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isFormValid) {
      error('모든 필수 항목을 올바르게 입력해주세요.', '');
      return;
    }

    setIsLoading(true);
    try {
      const recaptchaToken = await generateRecaptchaToken('signup');
      
      const submitData = {
        ...formData,
        recaptcha_token: recaptchaToken
      };

      // API 호출 (실제 구현 시)
      // await AuthService.signup(submitData);
      console.log('Submit data:', submitData);
      
      success('회원가입이 완료되었습니다!', '환영합니다');
      navigate('/login');
    } catch (err) {
      error('회원가입에 실패했습니다.', '다시 시도해주세요');
    } finally {
      setIsLoading(false);
    }
  };

  // 타이머 표시 형식 (MM:SS)
  const formatTimer = (seconds: number) => {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
  };

  return (
    <>
      <SEOHead {...metaData} />
      
      {/* reCAPTCHA v3 스크립트 */}
      <script src="https://www.google.com/recaptcha/api.js?render=6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb"></script>
      
      {/* 회원가입 페이지 - 기존 PHP 디자인과 동일 */}
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
            {/* 회원가입 폼 컨테이너 */}
            <div className="auth-form-container">
              {/* 로고 및 제목 */}
              <div className="auth-header">
                <div className="auth-logo">
                  <div className="logo-icon">
                    <i className="fas fa-rocket"></i>
                  </div>
                  <span className="logo-text">탑마케팅</span>
                </div>
                <h1 className="auth-title">새로운 여정을 시작하세요</h1>
                <p className="auth-subtitle">글로벌 네트워크 마케팅 커뮤니티에 가입하여 성공을 함께 만들어가세요</p>
              </div>

              {/* 회원가입 폼 */}
              <form className="auth-form" onSubmit={handleSubmit} id="signup-form">
                {/* 닉네임 */}
                <div className="form-group">
                  <label htmlFor="nickname" className="form-label">
                    <i className="fas fa-user"></i>
                    닉네임 <span className="required">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="nickname" 
                    name="nickname" 
                    className={`form-input ${errors.nickname ? 'error' : ''}`}
                    placeholder="닉네임을 입력하세요 (2-20자)"
                    value={formData.nickname}
                    onChange={handleInputChange}
                    required 
                    autoComplete="username"
                    maxLength={20}
                    minLength={2}
                  />
                  {errors.nickname && <div className="error-message">{errors.nickname}</div>}
                  <small className="form-help">한글, 영문, 숫자를 사용하여 2-20자로 입력하세요</small>
                </div>

                {/* 휴대폰 번호 */}
                <div className="form-group">
                  <label htmlFor="phone" className="form-label">
                    <i className="fas fa-mobile-alt"></i>
                    휴대폰 번호 <span className="required">*</span>
                  </label>
                  <div className="phone-verification-group">
                    <input 
                      type="tel" 
                      id="phone" 
                      name="phone" 
                      className={`form-input phone-input ${errors.phone ? 'error' : ''}`}
                      placeholder="010-1234-5678"
                      value={formData.phone}
                      onChange={handleInputChange}
                      required 
                      autoComplete="tel"
                      pattern="010-[0-9]{3,4}-[0-9]{4}"
                      maxLength={13}
                    />
                    <button 
                      type="button" 
                      id="send-verification-btn" 
                      className="btn btn-outline-primary"
                      onClick={sendVerificationCode}
                      disabled={!formData.phone || !/^010-[0-9]{3,4}-[0-9]{4}$/.test(formData.phone) || verificationSent}
                    >
                      {verificationSent ? '발송됨' : '인증번호 발송'}
                    </button>
                  </div>
                  {errors.phone && <div className="error-message">{errors.phone}</div>}
                  <small className="form-help">010으로 시작하는 휴대폰 번호를 입력하세요 (로그인 시 사용됩니다)</small>
                </div>

                {/* 인증번호 입력 필드 */}
                {verificationSent && (
                  <div className="form-group" id="verification-group">
                    <label htmlFor="verification_code" className="form-label">
                      <i className="fas fa-shield-alt"></i>
                      인증번호 <span className="required">*</span>
                    </label>
                    <div className="verification-input-group">
                      <input 
                        type="text" 
                        id="verification_code" 
                        name="verification_code" 
                        className={`form-input verification-input ${errors.verification_code ? 'error' : ''}`}
                        placeholder="4자리 인증번호 입력"
                        value={formData.verification_code}
                        onChange={handleInputChange}
                        required 
                        maxLength={4}
                      />
                      <button 
                        type="button" 
                        className="btn btn-primary"
                        onClick={verifyCode}
                        disabled={!formData.verification_code || formData.verification_code.length !== 4}
                      >
                        확인
                      </button>
                      <div className={`timer ${timer <= 30 ? 'text-red-500' : 'text-gray-600'}`}>
                        {formatTimer(timer)}
                      </div>
                    </div>
                    {errors.verification_code && <div className="error-message">{errors.verification_code}</div>}
                    <small className="form-help">휴대폰으로 전송된 4자리 인증번호를 입력하세요</small>
                  </div>
                )}

                {phoneVerified && (
                  <div className="text-green-600 text-sm flex items-center">
                    <i className="fas fa-check mr-2"></i>
                    휴대폰 인증이 완료되었습니다.
                  </div>
                )}

                {/* 이메일 */}
                <div className="form-group">
                  <label htmlFor="email" className="form-label">
                    <i className="fas fa-envelope"></i>
                    이메일 <span className="required">*</span>
                  </label>
                  <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    className={`form-input ${errors.email ? 'error' : ''}`}
                    placeholder="example@email.com"
                    value={formData.email}
                    onChange={handleInputChange}
                    required 
                    autoComplete="email"
                  />
                  {errors.email && <div className="error-message">{errors.email}</div>}
                  <small className="form-help">계정 복구 및 중요한 알림을 받기 위해 사용됩니다 (필수)</small>
                </div>

                {/* 비밀번호 */}
                <div className="form-group">
                  <label htmlFor="password" className="form-label">
                    <i className="fas fa-lock"></i>
                    비밀번호 <span className="required">*</span>
                  </label>
                  <div className="password-input-wrapper relative">
                    <input 
                      type={showPassword ? "text" : "password"}
                      id="password" 
                      name="password" 
                      className={`form-input ${errors.password ? 'error' : ''}`}
                      placeholder="비밀번호를 입력하세요 (8자 이상)"
                      value={formData.password}
                      onChange={handleInputChange}
                      required 
                      autoComplete="new-password"
                      minLength={8}
                    />
                    <button 
                      type="button" 
                      className="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      onClick={() => setShowPassword(!showPassword)}
                    >
                      <i className={`fas fa-${showPassword ? 'eye-slash' : 'eye'}`}></i>
                    </button>
                  </div>
                  {errors.password && <div className="error-message">{errors.password}</div>}
                  <small className="form-help">영문, 숫자, 특수문자를 포함하여 8자 이상 입력하세요</small>
                </div>

                {/* 비밀번호 확인 */}
                <div className="form-group">
                  <label htmlFor="password_confirm" className="form-label">
                    <i className="fas fa-lock"></i>
                    비밀번호 확인 <span className="required">*</span>
                  </label>
                  <div className="password-input-wrapper relative">
                    <input 
                      type={showPasswordConfirm ? "text" : "password"}
                      id="password_confirm" 
                      name="password_confirm" 
                      className={`form-input ${errors.password_confirm ? 'error' : ''}`}
                      placeholder="비밀번호를 다시 입력하세요"
                      value={formData.password_confirm}
                      onChange={handleInputChange}
                      required 
                      autoComplete="new-password"
                      minLength={8}
                    />
                    <button 
                      type="button" 
                      className="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                      onClick={() => setShowPasswordConfirm(!showPasswordConfirm)}
                    >
                      <i className={`fas fa-${showPasswordConfirm ? 'eye-slash' : 'eye'}`}></i>
                    </button>
                  </div>
                  {errors.password_confirm && <div className="error-message">{errors.password_confirm}</div>}
                </div>

                {/* 약관 동의 */}
                <div className="terms-group">
                  <div className="checkbox-item">
                    <input
                      id="terms"
                      name="terms"
                      type="checkbox"
                      checked={formData.terms}
                      onChange={handleInputChange}
                      required
                    />
                    <label htmlFor="terms" className="ml-2 text-sm text-gray-700">
                      <Link to="/terms" target="_blank" className="text-blue-600 hover:text-blue-500">
                        이용약관
                      </Link>
                      {' '}및{' '}
                      <Link to="/privacy" target="_blank" className="text-blue-600 hover:text-blue-500">
                        개인정보처리방침
                      </Link>
                      에 동의합니다 <span className="text-red-500">*</span>
                    </label>
                  </div>

                  <div className="checkbox-item">
                    <input
                      id="marketing"
                      name="marketing"
                      type="checkbox"
                      checked={formData.marketing}
                      onChange={handleInputChange}
                    />
                    <label htmlFor="marketing" className="ml-2 text-sm text-gray-700">
                      마케팅 정보 수신에 동의합니다 (선택)
                    </label>
                  </div>
                </div>

                {/* 숨겨진 필드들 */}
                <input type="hidden" name="phone_verified" value={phoneVerified ? '1' : '0'} />
                <input type="hidden" name="csrf_token" value={formData.csrf_token} />
                <input type="hidden" name="recaptcha_token" value={formData.recaptcha_token} />

                <button
                  type="submit"
                  className="btn btn-primary-gradient btn-large btn-full"
                  disabled={!isFormValid || isLoading}
                >
                  <i className="fas fa-user-plus"></i>
                  <span>{isLoading ? '가입 중...' : '회원가입'}</span>
                </button>

                <div className="text-xs text-gray-500 text-center bg-blue-50 p-3 rounded-lg">
                  <i className="fas fa-shield-alt mr-2"></i>
                  이 사이트는 reCAPTCHA로 보호되며, Google의{' '}
                  <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:text-blue-500">
                    개인정보처리방침
                  </a>
                  과{' '}
                  <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer" className="text-blue-600 hover:text-blue-500">
                    서비스 약관
                  </a>
                  이 적용됩니다.
                </div>
              </form>

              <div className="auth-footer">
                <p className="auth-switch">
                  이미 계정이 있으신가요?{' '}
                  <Link
                    to="/auth/login"
                    className="auth-link"
                  >
                    로그인하기
                  </Link>
                </p>
              </div>
            </div>

            {/* 사이드 정보 */}
            <div className="auth-side-info">
              <div className="side-info-content">
                <div className="side-info-icon">
                  <i className="fas fa-rocket"></i>
                </div>
                <h2>커뮤니티 가입의 혜택</h2>
                <p>글로벌 네트워크 마케팅 전문가들과 함께 성장하세요</p>
                
                <div className="security-features">
                  <div className="security-feature">
                    <i className="fas fa-users"></i>
                    <span>10,000+ 글로벌 멤버</span>
                  </div>
                  <div className="security-feature">
                    <i className="fas fa-clock"></i>
                    <span>24/7 언제든지 소통</span>
                  </div>
                  <div className="security-feature">
                    <i className="fas fa-graduation-cap"></i>
                    <span>100+ 전문 콘텐츠</span>
                  </div>
                </div>

                <div className="login-benefits">
                  <h3>가입 후 이용 가능한 서비스</h3>
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
    </>
  );
};

export default SignupPage;