import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import AuthService from '../../services/authService';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';

const SignupPage: React.FC = () => {
  const [currentStep, setCurrentStep] = useState(1);
  const [formData, setFormData] = useState({
    nickname: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
    verification_code: '',
    marketing_agreed: false,
    terms_agreed: false,
    privacy_agreed: false,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isLoading, setIsLoading] = useState(false);
  const [verificationSent, setVerificationSent] = useState(false);
  const [countdown, setCountdown] = useState(0);

  const { signup, isAuthenticated } = useAuth();
  const { success, error, info } = useToast();
  const navigate = useNavigate();

  // 이미 로그인된 경우 리다이렉트
  useEffect(() => {
    if (isAuthenticated) {
      navigate('/', { replace: true });
    }
  }, [isAuthenticated, navigate]);

  // 인증번호 카운트다운
  useEffect(() => {
    if (countdown > 0) {
      const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [countdown]);

  const validateStep1 = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.nickname.trim()) {
      newErrors.nickname = '닉네임을 입력해주세요.';
    } else if (formData.nickname.length < 2) {
      newErrors.nickname = '닉네임은 2자 이상 입력해주세요.';
    } else if (formData.nickname.length > 20) {
      newErrors.nickname = '닉네임은 20자 이하로 입력해주세요.';
    }

    if (!formData.phone.trim()) {
      newErrors.phone = '휴대폰 번호를 입력해주세요.';
    } else if (!/^010-\d{4}-\d{4}$/.test(formData.phone)) {
      newErrors.phone = '올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)';
    }

    if (!formData.email.trim()) {
      newErrors.email = '이메일을 입력해주세요.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
      newErrors.email = '올바른 이메일 주소를 입력해주세요.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const validateStep2 = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.verification_code.trim()) {
      newErrors.verification_code = '인증번호를 입력해주세요.';
    } else if (formData.verification_code.length !== 6) {
      newErrors.verification_code = '인증번호는 6자리입니다.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const validateStep3 = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.password.trim()) {
      newErrors.password = '비밀번호를 입력해주세요.';
    } else if (formData.password.length < 8) {
      newErrors.password = '비밀번호는 8자 이상 입력해주세요.';
    } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(formData.password)) {
      newErrors.password = '비밀번호는 영문과 숫자를 포함해야 합니다.';
    }

    if (!formData.password_confirmation.trim()) {
      newErrors.password_confirmation = '비밀번호 확인을 입력해주세요.';
    } else if (formData.password !== formData.password_confirmation) {
      newErrors.password_confirmation = '비밀번호가 일치하지 않습니다.';
    }

    if (!formData.terms_agreed) {
      newErrors.terms_agreed = '이용약관에 동의해주세요.';
    }

    if (!formData.privacy_agreed) {
      newErrors.privacy_agreed = '개인정보처리방침에 동의해주세요.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));

    // 에러 메시지 클리어
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const formatPhoneNumber = (value: string) => {
    const numbers = value.replace(/[^\d]/g, '');
    if (numbers.length <= 3) return numbers;
    if (numbers.length <= 7) return `${numbers.slice(0, 3)}-${numbers.slice(3)}`;
    return `${numbers.slice(0, 3)}-${numbers.slice(3, 7)}-${numbers.slice(7, 11)}`;
  };

  const handlePhoneChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const formatted = formatPhoneNumber(e.target.value);
    setFormData(prev => ({ ...prev, phone: formatted }));
    
    if (errors.phone) {
      setErrors(prev => ({ ...prev, phone: '' }));
    }
  };

  const sendVerificationCode = async () => {
    setIsLoading(true);
    try {
      await AuthService.sendVerificationCode(formData.phone, 'SIGNUP');
      setVerificationSent(true);
      setCountdown(180); // 3분
      success('인증번호가 발송되었습니다.', '확인 요청');
      info('3분 이내에 인증번호를 입력해주세요.');
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '인증번호 발송에 실패했습니다.';
      error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  const verifyCode = async () => {
    if (!validateStep2()) return;

    setIsLoading(true);
    try {
      await AuthService.verifyCode(formData.phone, formData.verification_code, 'SIGNUP');
      success('휴대폰 인증이 완료되었습니다.');
      setCurrentStep(3);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '인증번호가 올바르지 않습니다.';
      error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validateStep3()) return;

    setIsLoading(true);
    try {
      await signup(formData);
      success('회원가입이 완료되었습니다!', '환영합니다!');
      navigate('/', { replace: true });
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '회원가입에 실패했습니다.';
      error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  const nextStep = () => {
    if (currentStep === 1 && validateStep1()) {
      setCurrentStep(2);
    }
  };

  const prevStep = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  return (
    <div className="min-h-screen flex">
      {/* 왼쪽 브랜드 섹션 */}
      <div className="hidden lg:flex lg:flex-1 bg-gradient-to-br from-purple-900 via-blue-800 to-blue-900 relative overflow-hidden">
        <div className="absolute inset-0 bg-black opacity-20"></div>
        <div className="relative flex flex-col justify-center px-12 text-white">
          <div className="max-w-md">
            <h1 className="text-4xl font-bold mb-6">
              탑마케팅과 함께
              <br />
              성공의 여정을 시작하세요
            </h1>
            <p className="text-xl text-blue-100 mb-8 leading-relaxed">
              네트워크 마케팅 전문가들과 함께
              <br />
              새로운 기회를 발견하고 성장하세요.
            </p>
            
            {/* 진행 단계 표시 */}
            <div className="space-y-4">
              <div className={`flex items-center ${currentStep >= 1 ? 'text-white' : 'text-blue-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 1 ? 'bg-blue-500' : 'bg-blue-700'
                }`}>
                  {currentStep > 1 ? '✓' : '1'}
                </div>
                <span>기본 정보 입력</span>
              </div>
              <div className={`flex items-center ${currentStep >= 2 ? 'text-white' : 'text-blue-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 2 ? 'bg-blue-500' : 'bg-blue-700'
                }`}>
                  {currentStep > 2 ? '✓' : '2'}
                </div>
                <span>휴대폰 인증</span>
              </div>
              <div className={`flex items-center ${currentStep >= 3 ? 'text-white' : 'text-blue-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 3 ? 'bg-blue-500' : 'bg-blue-700'
                }`}>
                  3
                </div>
                <span>비밀번호 설정</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* 오른쪽 회원가입 폼 */}
      <div className="flex-1 flex flex-col justify-center px-6 sm:px-12 lg:px-16 bg-gray-50">
        <div className="mx-auto w-full max-w-md">
          <div className="text-center mb-8">
            <Link to="/" className="inline-block mb-6">
              <img 
                className="h-12 w-auto mx-auto" 
                src="/assets/images/topmkt-logo-og.svg" 
                alt="탑마케팅" 
                onError={(e) => {
                  e.currentTarget.style.display = 'none';
                  e.currentTarget.nextElementSibling?.classList.remove('hidden');
                }}
              />
              <span className="hidden text-2xl font-bold text-blue-600">
                탑마케팅
              </span>
            </Link>
            <h2 className="text-3xl font-bold text-gray-900 mb-2">
              회원가입
            </h2>
            <p className="text-gray-600">
              {currentStep === 1 && '기본 정보를 입력해주세요.'}
              {currentStep === 2 && '휴대폰 인증을 완료해주세요.'}
              {currentStep === 3 && '비밀번호를 설정해주세요.'}
            </p>
          </div>

          {/* 단계 1: 기본 정보 */}
          {currentStep === 1 && (
            <div className="space-y-6">
              <Input
                label="닉네임"
                name="nickname"
                value={formData.nickname}
                onChange={handleInputChange}
                error={errors.nickname}
                placeholder="사용할 닉네임을 입력하세요"
                required
                fullWidth
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                }
                hint="2-20자의 한글, 영문, 숫자를 사용할 수 있습니다."
              />

              <Input
                label="휴대폰 번호"
                type="tel"
                name="phone"
                value={formData.phone}
                onChange={handlePhoneChange}
                error={errors.phone}
                placeholder="010-1234-5678"
                required
                fullWidth
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                  </svg>
                }
              />

              <Input
                label="이메일"
                type="email"
                name="email"
                value={formData.email}
                onChange={handleInputChange}
                error={errors.email}
                placeholder="example@email.com"
                required
                fullWidth
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                }
              />

              <Button
                onClick={nextStep}
                fullWidth
                size="lg"
                className="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700"
              >
                다음 단계
              </Button>
            </div>
          )}

          {/* 단계 2: 휴대폰 인증 */}
          {currentStep === 2 && (
            <div className="space-y-6">
              <div className="text-center p-6 bg-blue-50 rounded-lg">
                <svg className="w-12 h-12 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <p className="text-blue-800 font-medium">
                  {formData.phone}로 인증번호를 발송했습니다.
                </p>
                <p className="text-blue-600 text-sm mt-1">
                  SMS로 받은 6자리 인증번호를 입력해주세요.
                </p>
              </div>

              {!verificationSent ? (
                <Button
                  onClick={sendVerificationCode}
                  loading={isLoading}
                  fullWidth
                  size="lg"
                >
                  인증번호 발송
                </Button>
              ) : (
                <>
                  <Input
                    label="인증번호"
                    name="verification_code"
                    value={formData.verification_code}
                    onChange={handleInputChange}
                    error={errors.verification_code}
                    placeholder="6자리 인증번호"
                    maxLength={6}
                    required
                    fullWidth
                    leftIcon={
                      <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    }
                    rightIcon={
                      countdown > 0 && (
                        <span className="text-red-500 font-medium">
                          {formatTime(countdown)}
                        </span>
                      )
                    }
                  />

                  <div className="flex space-x-3">
                    <Button
                      onClick={verifyCode}
                      loading={isLoading}
                      fullWidth
                      size="lg"
                      className="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700"
                    >
                      인증 확인
                    </Button>
                    <Button
                      onClick={sendVerificationCode}
                      disabled={countdown > 0 || isLoading}
                      variant="outline"
                      size="lg"
                    >
                      재발송
                    </Button>
                  </div>
                </>
              )}

              <Button
                onClick={prevStep}
                variant="ghost"
                fullWidth
              >
                이전 단계
              </Button>
            </div>
          )}

          {/* 단계 3: 비밀번호 설정 */}
          {currentStep === 3 && (
            <form onSubmit={handleSubmit} className="space-y-6">
              <Input
                label="비밀번호"
                type="password"
                name="password"
                value={formData.password}
                onChange={handleInputChange}
                error={errors.password}
                placeholder="8자 이상의 비밀번호"
                required
                fullWidth
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                }
                hint="영문, 숫자를 포함하여 8자 이상 입력해주세요."
              />

              <Input
                label="비밀번호 확인"
                type="password"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleInputChange}
                error={errors.password_confirmation}
                placeholder="비밀번호를 다시 입력하세요"
                required
                fullWidth
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                }
              />

              <div className="space-y-4">
                <div className="flex items-center">
                  <input
                    id="terms_agreed"
                    name="terms_agreed"
                    type="checkbox"
                    checked={formData.terms_agreed}
                    onChange={handleInputChange}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label htmlFor="terms_agreed" className="ml-2 block text-sm text-gray-700">
                    <Link to="/legal/terms" className="text-blue-600 hover:text-blue-500">
                      이용약관
                    </Link>에 동의합니다 (필수)
                  </label>
                </div>
                {errors.terms_agreed && (
                  <p className="text-red-600 text-sm">{errors.terms_agreed}</p>
                )}

                <div className="flex items-center">
                  <input
                    id="privacy_agreed"
                    name="privacy_agreed"
                    type="checkbox"
                    checked={formData.privacy_agreed}
                    onChange={handleInputChange}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label htmlFor="privacy_agreed" className="ml-2 block text-sm text-gray-700">
                    <Link to="/legal/privacy" className="text-blue-600 hover:text-blue-500">
                      개인정보처리방침
                    </Link>에 동의합니다 (필수)
                  </label>
                </div>
                {errors.privacy_agreed && (
                  <p className="text-red-600 text-sm">{errors.privacy_agreed}</p>
                )}

                <div className="flex items-center">
                  <input
                    id="marketing_agreed"
                    name="marketing_agreed"
                    type="checkbox"
                    checked={formData.marketing_agreed}
                    onChange={handleInputChange}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label htmlFor="marketing_agreed" className="ml-2 block text-sm text-gray-700">
                    마케팅 정보 수신에 동의합니다 (선택)
                  </label>
                </div>
              </div>

              <Button
                type="submit"
                loading={isLoading}
                fullWidth
                size="lg"
                className="bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700"
              >
                회원가입 완료
              </Button>

              <Button
                onClick={prevStep}
                variant="ghost"
                fullWidth
                type="button"
              >
                이전 단계
              </Button>
            </form>
          )}

          <div className="mt-8 text-center">
            <p className="text-gray-600">
              이미 계정이 있으신가요?{' '}
              <Link
                to="/auth/login"
                className="font-medium text-blue-600 hover:text-blue-500"
              >
                로그인하기
              </Link>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SignupPage;