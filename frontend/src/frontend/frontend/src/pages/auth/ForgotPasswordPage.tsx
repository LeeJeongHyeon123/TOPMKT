import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useToast } from '../../context/ToastContext';
import AuthService from '../../services/authService';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';

const ForgotPasswordPage: React.FC = () => {
  const [currentStep, setCurrentStep] = useState(1);
  const [formData, setFormData] = useState({
    phone: '',
    verification_code: '',
    password: '',
    password_confirmation: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [isLoading, setIsLoading] = useState(false);
  const [verificationSent, setVerificationSent] = useState(false);
  const [countdown, setCountdown] = useState(0);

  const { success, error, info } = useToast();
  const navigate = useNavigate();

  // 인증번호 카운트다운
  React.useEffect(() => {
    if (countdown > 0) {
      const timer = setTimeout(() => setCountdown(countdown - 1), 1000);
      return () => clearTimeout(timer);
    }
  }, [countdown]);

  const validateStep1 = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.phone.trim()) {
      newErrors.phone = '휴대폰 번호를 입력해주세요.';
    } else if (!/^010-\d{4}-\d{4}$/.test(formData.phone)) {
      newErrors.phone = '올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)';
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
      newErrors.password = '새 비밀번호를 입력해주세요.';
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

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
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
    if (!validateStep1()) return;

    setIsLoading(true);
    try {
      await AuthService.sendVerificationCode(formData.phone, 'PASSWORD_RESET');
      setVerificationSent(true);
      setCountdown(180); // 3분
      setCurrentStep(2);
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
      await AuthService.verifyCode(formData.phone, formData.verification_code, 'PASSWORD_RESET');
      success('휴대폰 인증이 완료되었습니다.');
      setCurrentStep(3);
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '인증번호가 올바르지 않습니다.';
      error(errorMessage);
    } finally {
      setIsLoading(false);
    }
  };

  const resetPassword = async () => {
    if (!validateStep3()) return;

    setIsLoading(true);
    try {
      await AuthService.resetPassword({
        phone: formData.phone,
        code: formData.verification_code,
        password: formData.password,
        password_confirmation: formData.password_confirmation,
      });
      success('비밀번호가 성공적으로 변경되었습니다!', '완료');
      navigate('/auth/login', { replace: true });
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '비밀번호 변경에 실패했습니다.';
      error(errorMessage);
    } finally {
      setIsLoading(false);
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
      <div className="hidden lg:flex lg:flex-1 bg-gradient-to-br from-indigo-900 via-purple-800 to-pink-800 relative overflow-hidden">
        <div className="absolute inset-0 bg-black opacity-20"></div>
        <div className="relative flex flex-col justify-center px-12 text-white">
          <div className="max-w-md">
            <h1 className="text-4xl font-bold mb-6">
              비밀번호를 잊으셨나요?
            </h1>
            <p className="text-xl text-indigo-100 mb-8 leading-relaxed">
              걱정하지 마세요! 휴대폰 인증을 통해
              <br />
              새로운 비밀번호를 설정하실 수 있습니다.
            </p>
            
            {/* 진행 단계 표시 */}
            <div className="space-y-4">
              <div className={`flex items-center ${currentStep >= 1 ? 'text-white' : 'text-indigo-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 1 ? 'bg-indigo-500' : 'bg-indigo-700'
                }`}>
                  {currentStep > 1 ? '✓' : '1'}
                </div>
                <span>휴대폰 번호 입력</span>
              </div>
              <div className={`flex items-center ${currentStep >= 2 ? 'text-white' : 'text-indigo-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 2 ? 'bg-indigo-500' : 'bg-indigo-700'
                }`}>
                  {currentStep > 2 ? '✓' : '2'}
                </div>
                <span>인증번호 확인</span>
              </div>
              <div className={`flex items-center ${currentStep >= 3 ? 'text-white' : 'text-indigo-300'}`}>
                <div className={`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${
                  currentStep >= 3 ? 'bg-indigo-500' : 'bg-indigo-700'
                }`}>
                  3
                </div>
                <span>새 비밀번호 설정</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* 오른쪽 폼 섹션 */}
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
              <span className="hidden text-2xl font-bold text-indigo-600">
                탑마케팅
              </span>
            </Link>
            <h2 className="text-3xl font-bold text-gray-900 mb-2">
              비밀번호 재설정
            </h2>
            <p className="text-gray-600">
              {currentStep === 1 && '등록된 휴대폰 번호를 입력해주세요.'}
              {currentStep === 2 && 'SMS로 받은 인증번호를 입력해주세요.'}
              {currentStep === 3 && '새로운 비밀번호를 설정해주세요.'}
            </p>
          </div>

          {/* 단계 1: 휴대폰 번호 입력 */}
          {currentStep === 1 && (
            <div className="space-y-6">
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
                hint="회원가입 시 등록한 휴대폰 번호를 입력해주세요."
              />

              <Button
                onClick={sendVerificationCode}
                loading={isLoading}
                fullWidth
                size="lg"
                className="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700"
              >
                인증번호 발송
              </Button>

              <div className="text-center">
                <Link
                  to="/auth/login"
                  className="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                >
                  로그인 페이지로 돌아가기
                </Link>
              </div>
            </div>
          )}

          {/* 단계 2: 인증번호 확인 */}
          {currentStep === 2 && (
            <div className="space-y-6">
              <div className="text-center p-6 bg-indigo-50 rounded-lg">
                <svg className="w-12 h-12 text-indigo-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <p className="text-indigo-800 font-medium">
                  {formData.phone}로 인증번호를 발송했습니다.
                </p>
                <p className="text-indigo-600 text-sm mt-1">
                  SMS로 받은 6자리 인증번호를 입력해주세요.
                </p>
              </div>

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
                  className="bg-gradient-to-r from-green-600 to-indigo-600 hover:from-green-700 hover:to-indigo-700"
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

              <Button
                onClick={prevStep}
                variant="ghost"
                fullWidth
              >
                이전 단계
              </Button>
            </div>
          )}

          {/* 단계 3: 새 비밀번호 설정 */}
          {currentStep === 3 && (
            <div className="space-y-6">
              <Input
                label="새 비밀번호"
                type="password"
                name="password"
                value={formData.password}
                onChange={handleInputChange}
                error={errors.password}
                placeholder="8자 이상의 새 비밀번호"
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
                label="새 비밀번호 확인"
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

              <Button
                onClick={resetPassword}
                loading={isLoading}
                fullWidth
                size="lg"
                className="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700"
              >
                비밀번호 변경 완료
              </Button>

              <Button
                onClick={prevStep}
                variant="ghost"
                fullWidth
                type="button"
              >
                이전 단계
              </Button>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default ForgotPasswordPage;