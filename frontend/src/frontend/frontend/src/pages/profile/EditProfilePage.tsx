import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { UpdateProfileRequest, ChangePasswordRequest } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const EditProfilePage: React.FC = () => {
  const navigate = useNavigate();
  const { user, updateUser, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [loading, setLoading] = useState(false);
  const [activeTab, setActiveTab] = useState<'profile' | 'password' | 'settings'>('profile');
  
  // 프로필 편집 폼
  const [profileForm, setProfileForm] = useState({
    nickname: '',
    email: '',
    introduction: '',
    marketing_agreed: false,
  });
  const [profileErrors, setProfileErrors] = useState<Record<string, string>>({});
  const [profileSubmitting, setProfileSubmitting] = useState(false);

  // 비밀번호 변경 폼
  const [passwordForm, setPasswordForm] = useState({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  });
  const [passwordErrors, setPasswordErrors] = useState<Record<string, string>>({});
  const [passwordSubmitting, setPasswordSubmitting] = useState(false);

  // 프로필 이미지 업로드
  const [profileImage, setProfileImage] = useState<File | null>(null);
  const [profileImagePreview, setProfileImagePreview] = useState<string>('');
  const [imageUploading, setImageUploading] = useState(false);

  // 로그인 체크 및 초기 데이터 설정
  useEffect(() => {
    if (!isAuthenticated || !user) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login', { state: { from: location.pathname } });
      return;
    }

    // 기존 사용자 정보로 폼 초기화
    setProfileForm({
      nickname: user.nickname || '',
      email: user.email || '',
      introduction: user.introduction || '',
      marketing_agreed: user.marketing_agreed || false,
    });

    if (user.profile_image) {
      setProfileImagePreview(user.profile_image);
    }
  }, [isAuthenticated, user]);

  // 프로필 폼 입력 처리
  const handleProfileInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value, type } = e.target;
    const checked = (e.target as HTMLInputElement).checked;
    
    setProfileForm(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));

    // 에러 메시지 클리어
    if (profileErrors[name]) {
      setProfileErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  // 비밀번호 폼 입력 처리
  const handlePasswordInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setPasswordForm(prev => ({
      ...prev,
      [name]: value
    }));

    // 에러 메시지 클리어
    if (passwordErrors[name]) {
      setPasswordErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  // 프로필 이미지 선택 처리
  const handleImageSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // 파일 타입 검증
    if (!file.type.startsWith('image/')) {
      error('이미지 파일만 업로드할 수 있습니다.');
      return;
    }

    // 파일 크기 검증 (5MB 제한)
    if (file.size > 5 * 1024 * 1024) {
      error('파일 크기는 5MB를 초과할 수 없습니다.');
      return;
    }

    setProfileImage(file);
    
    // 미리보기 생성
    const reader = new FileReader();
    reader.onload = (e) => {
      setProfileImagePreview(e.target?.result as string);
    };
    reader.readAsDataURL(file);
  };

  // 프로필 정보 유효성 검사
  const validateProfileForm = () => {
    const newErrors: Record<string, string> = {};

    if (!profileForm.nickname.trim()) {
      newErrors.nickname = '닉네임을 입력해주세요.';
    } else if (profileForm.nickname.length < 2) {
      newErrors.nickname = '닉네임은 2자 이상 입력해주세요.';
    } else if (profileForm.nickname.length > 20) {
      newErrors.nickname = '닉네임은 20자 이하로 입력해주세요.';
    }

    if (!profileForm.email.trim()) {
      newErrors.email = '이메일을 입력해주세요.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(profileForm.email)) {
      newErrors.email = '올바른 이메일 주소를 입력해주세요.';
    }

    if (profileForm.introduction && profileForm.introduction.length > 500) {
      newErrors.introduction = '자기소개는 500자 이하로 입력해주세요.';
    }

    setProfileErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // 비밀번호 유효성 검사
  const validatePasswordForm = () => {
    const newErrors: Record<string, string> = {};

    if (!passwordForm.current_password.trim()) {
      newErrors.current_password = '현재 비밀번호를 입력해주세요.';
    }

    if (!passwordForm.new_password.trim()) {
      newErrors.new_password = '새 비밀번호를 입력해주세요.';
    } else if (passwordForm.new_password.length < 8) {
      newErrors.new_password = '새 비밀번호는 8자 이상 입력해주세요.';
    } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(passwordForm.new_password)) {
      newErrors.new_password = '새 비밀번호는 영문과 숫자를 포함해야 합니다.';
    }

    if (!passwordForm.new_password_confirmation.trim()) {
      newErrors.new_password_confirmation = '새 비밀번호 확인을 입력해주세요.';
    } else if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
      newErrors.new_password_confirmation = '새 비밀번호가 일치하지 않습니다.';
    }

    setPasswordErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // 프로필 업데이트
  const handleProfileSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateProfileForm()) return;

    setProfileSubmitting(true);
    try {
      // 프로필 이미지 업로드 (있는 경우)
      let profileImageUrl = '';
      if (profileImage) {
        setImageUploading(true);
        try {
          const imageResponse = await request({
            url: '/upload/profile-image',
            method: 'POST',
            data: { file: profileImage },
            headers: { 'Content-Type': 'multipart/form-data' },
          });

          if (imageResponse.success && imageResponse.data) {
            profileImageUrl = imageResponse.data.url;
          }
        } catch (err) {
          console.error('이미지 업로드 실패:', err);
        } finally {
          setImageUploading(false);
        }
      }

      // 프로필 정보 업데이트
      const updateData: UpdateProfileRequest = {
        nickname: profileForm.nickname.trim(),
        email: profileForm.email.trim(),
        introduction: profileForm.introduction.trim(),
        marketing_agreed: profileForm.marketing_agreed,
      };

      if (profileImageUrl) {
        updateData.profile_image = profileImageUrl;
      }

      const response = await request({
        url: '/users/profile',
        method: 'PUT',
        data: updateData,
      });

      if (response.success && response.data) {
        updateUser(response.data);
        success('프로필이 성공적으로 업데이트되었습니다.');
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '프로필 업데이트에 실패했습니다.';
      error(errorMessage);
    } finally {
      setProfileSubmitting(false);
    }
  };

  // 비밀번호 변경
  const handlePasswordSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validatePasswordForm()) return;

    setPasswordSubmitting(true);
    try {
      const changeData: ChangePasswordRequest = {
        current_password: passwordForm.current_password,
        new_password: passwordForm.new_password,
        new_password_confirmation: passwordForm.new_password_confirmation,
      };

      const response = await request({
        url: '/auth/password/change',
        method: 'POST',
        data: changeData,
      });

      if (response.success) {
        success('비밀번호가 성공적으로 변경되었습니다.');
        setPasswordForm({
          current_password: '',
          new_password: '',
          new_password_confirmation: '',
        });
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '비밀번호 변경에 실패했습니다.';
      error(errorMessage);
    } finally {
      setPasswordSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="프로필 정보를 불러오는 중..." />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 네비게이션 */}
        <div className="mb-6">
          <Link
            to="/profile"
            className="inline-flex items-center text-blue-600 hover:text-blue-700"
          >
            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            프로필로 돌아가기
          </Link>
        </div>

        {/* 헤더 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <h1 className="text-3xl font-bold text-gray-900">
            프로필 편집
          </h1>
          <p className="text-gray-600 mt-2">
            프로필 정보를 수정하고 계정 설정을 관리하세요.
          </p>
        </div>

        {/* 탭 메뉴 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8">
              {[
                { key: 'profile', label: '기본 정보', icon: 'user' },
                { key: 'password', label: '비밀번호 변경', icon: 'lock-closed' },
                { key: 'settings', label: '계정 설정', icon: 'cog' },
              ].map((tab) => (
                <button
                  key={tab.key}
                  onClick={() => setActiveTab(tab.key as any)}
                  className={`flex items-center py-2 px-1 border-b-2 font-medium text-sm ${
                    activeTab === tab.key
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    {tab.icon === 'user' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    )}
                    {tab.icon === 'lock-closed' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    )}
                    {tab.icon === 'cog' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    )}
                  </svg>
                  {tab.label}
                </button>
              ))}
            </nav>
          </div>
        </div>

        {/* 탭 콘텐츠 */}
        <div>
          {/* 기본 정보 탭 */}
          {activeTab === 'profile' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <form onSubmit={handleProfileSubmit} className="space-y-6">
                {/* 프로필 이미지 */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-4">
                    프로필 이미지
                  </label>
                  <div className="flex items-center space-x-6">
                    <div className="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden">
                      {profileImagePreview ? (
                        <img
                          src={profileImagePreview}
                          alt="프로필 미리보기"
                          className="w-24 h-24 rounded-full object-cover"
                        />
                      ) : (
                        <svg className="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                      )}
                    </div>
                    <div>
                      <input
                        type="file"
                        id="profile-image"
                        accept="image/*"
                        onChange={handleImageSelect}
                        className="hidden"
                      />
                      <label htmlFor="profile-image">
                        <Button
                          type="button"
                          variant="outline"
                          loading={imageUploading}
                          onClick={() => document.getElementById('profile-image')?.click()}
                        >
                          이미지 선택
                        </Button>
                      </label>
                      <p className="text-xs text-gray-500 mt-2">
                        JPG, PNG 파일 (최대 5MB)
                      </p>
                    </div>
                  </div>
                </div>

                {/* 닉네임 */}
                <Input
                  label="닉네임"
                  name="nickname"
                  value={profileForm.nickname}
                  onChange={handleProfileInputChange}
                  error={profileErrors.nickname}
                  placeholder="사용할 닉네임을 입력하세요"
                  required
                  fullWidth
                  maxLength={20}
                  hint={`${profileForm.nickname.length}/20`}
                />

                {/* 이메일 */}
                <Input
                  label="이메일"
                  type="email"
                  name="email"
                  value={profileForm.email}
                  onChange={handleProfileInputChange}
                  error={profileErrors.email}
                  placeholder="example@email.com"
                  required
                  fullWidth
                />

                {/* 자기소개 */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    자기소개
                  </label>
                  <textarea
                    name="introduction"
                    value={profileForm.introduction}
                    onChange={handleProfileInputChange}
                    placeholder="자신을 간단히 소개해주세요"
                    rows={4}
                    maxLength={500}
                    className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                      profileErrors.introduction ? 'border-red-300' : 'border-gray-300'
                    }`}
                  />
                  <div className="flex justify-between items-center mt-2">
                    {profileErrors.introduction && (
                      <p className="text-red-600 text-sm">{profileErrors.introduction}</p>
                    )}
                    <div className="text-sm text-gray-500 ml-auto">
                      {profileForm.introduction.length}/500
                    </div>
                  </div>
                </div>

                {/* 마케팅 수신 동의 */}
                <div className="flex items-center">
                  <input
                    id="marketing_agreed"
                    name="marketing_agreed"
                    type="checkbox"
                    checked={profileForm.marketing_agreed}
                    onChange={handleProfileInputChange}
                    className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                  />
                  <label htmlFor="marketing_agreed" className="ml-2 block text-sm text-gray-700">
                    마케팅 정보 수신에 동의합니다 (선택)
                  </label>
                </div>

                {/* 저장 버튼 */}
                <div className="pt-6 border-t border-gray-200">
                  <Button
                    type="submit"
                    loading={profileSubmitting}
                    className="bg-blue-600 hover:bg-blue-700"
                  >
                    프로필 저장
                  </Button>
                </div>
              </form>
            </div>
          )}

          {/* 비밀번호 변경 탭 */}
          {activeTab === 'password' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <form onSubmit={handlePasswordSubmit} className="space-y-6">
                <div className="mb-6">
                  <h2 className="text-lg font-semibold text-gray-900 mb-2">
                    비밀번호 변경
                  </h2>
                  <p className="text-sm text-gray-600">
                    계정 보안을 위해 정기적으로 비밀번호를 변경해주세요.
                  </p>
                </div>

                <Input
                  label="현재 비밀번호"
                  type="password"
                  name="current_password"
                  value={passwordForm.current_password}
                  onChange={handlePasswordInputChange}
                  error={passwordErrors.current_password}
                  placeholder="현재 비밀번호를 입력하세요"
                  required
                  fullWidth
                />

                <Input
                  label="새 비밀번호"
                  type="password"
                  name="new_password"
                  value={passwordForm.new_password}
                  onChange={handlePasswordInputChange}
                  error={passwordErrors.new_password}
                  placeholder="새 비밀번호를 입력하세요"
                  required
                  fullWidth
                  hint="영문, 숫자를 포함하여 8자 이상 입력해주세요."
                />

                <Input
                  label="새 비밀번호 확인"
                  type="password"
                  name="new_password_confirmation"
                  value={passwordForm.new_password_confirmation}
                  onChange={handlePasswordInputChange}
                  error={passwordErrors.new_password_confirmation}
                  placeholder="새 비밀번호를 다시 입력하세요"
                  required
                  fullWidth
                />

                <div className="pt-6 border-t border-gray-200">
                  <Button
                    type="submit"
                    loading={passwordSubmitting}
                    className="bg-red-600 hover:bg-red-700"
                  >
                    비밀번호 변경
                  </Button>
                </div>
              </form>
            </div>
          )}

          {/* 계정 설정 탭 */}
          {activeTab === 'settings' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <div className="space-y-6">
                <div>
                  <h2 className="text-lg font-semibold text-gray-900 mb-4">
                    계정 설정
                  </h2>
                </div>

                {/* 알림 설정 */}
                <div className="border-b border-gray-200 pb-6">
                  <h3 className="text-md font-medium text-gray-900 mb-3">
                    알림 설정
                  </h3>
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <div>
                        <label className="text-sm font-medium text-gray-700">
                          이메일 알림
                        </label>
                        <p className="text-xs text-gray-500">
                          새로운 댓글, 좋아요 등의 알림을 이메일로 받습니다
                        </p>
                      </div>
                      <input
                        type="checkbox"
                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        defaultChecked
                      />
                    </div>
                    <div className="flex items-center justify-between">
                      <div>
                        <label className="text-sm font-medium text-gray-700">
                          SMS 알림
                        </label>
                        <p className="text-xs text-gray-500">
                          중요한 알림을 SMS로 받습니다
                        </p>
                      </div>
                      <input
                        type="checkbox"
                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                    </div>
                  </div>
                </div>

                {/* 개인정보 설정 */}
                <div className="border-b border-gray-200 pb-6">
                  <h3 className="text-md font-medium text-gray-900 mb-3">
                    개인정보 설정
                  </h3>
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <div>
                        <label className="text-sm font-medium text-gray-700">
                          프로필 공개
                        </label>
                        <p className="text-xs text-gray-500">
                          다른 사용자가 내 프로필을 볼 수 있습니다
                        </p>
                      </div>
                      <input
                        type="checkbox"
                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        defaultChecked
                      />
                    </div>
                    <div className="flex items-center justify-between">
                      <div>
                        <label className="text-sm font-medium text-gray-700">
                          이메일 주소 공개
                        </label>
                        <p className="text-xs text-gray-500">
                          프로필에서 이메일 주소를 공개합니다
                        </p>
                      </div>
                      <input
                        type="checkbox"
                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                    </div>
                  </div>
                </div>

                {/* 위험한 작업 */}
                <div>
                  <h3 className="text-md font-medium text-red-900 mb-3">
                    위험한 작업
                  </h3>
                  <div className="space-y-3">
                    <div className="p-4 bg-red-50 rounded-lg">
                      <div className="flex items-center justify-between">
                        <div>
                          <label className="text-sm font-medium text-red-900">
                            계정 탈퇴
                          </label>
                          <p className="text-xs text-red-700 mt-1">
                            계정을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다.
                          </p>
                        </div>
                        <Button
                          variant="outline"
                          className="text-red-600 border-red-200 hover:bg-red-50"
                          onClick={() => {
                            if (window.confirm('정말로 계정을 탈퇴하시겠습니까? 이 작업은 되돌릴 수 없습니다.')) {
                              // TODO: 계정 탈퇴 API 호출
                              error('계정 탈퇴 기능은 준비 중입니다.');
                            }
                          }}
                        >
                          계정 탈퇴
                        </Button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default EditProfilePage;