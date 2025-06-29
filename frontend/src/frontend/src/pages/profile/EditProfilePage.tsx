import React, { useState, useEffect, useRef } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../hooks/useToast';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

// Rich Text Editor
import ReactQuill from 'react-quill';
import 'react-quill/dist/quill.snow.css';

// Image Cropper
import Cropper from 'react-cropper';
import 'cropperjs/dist/cropper.css';

// 인터페이스 정의
interface ProfileUser {
  id: number;
  nickname: string;
  email: string;
  phone?: string;
  bio?: string;
  birth_date?: string;
  gender?: 'M' | 'F' | 'OTHER';
  profile_image_original?: string;
  profile_image_profile?: string;
  profile_image_thumb?: string;
  social_links?: {
    website?: string;
    kakao?: string;
    instagram?: string;
    facebook?: string;
    youtube?: string;
    tiktok?: string;
  };
  role: 'ROLE_USER' | 'ROLE_CORP' | 'ROLE_ADMIN';
  phone_verified: boolean;
  email_verified: boolean;
  marketing_agreed: boolean;
  created_at: string;
  updated_at: string;
}

interface SocialLinks {
  website?: string;
  kakao?: string;
  instagram?: string;
  facebook?: string;
  youtube?: string;
  tiktok?: string;
}

const EditProfilePage: React.FC = () => {
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();
  const { success: showSuccess, error: showError } = useToast();
  const fileInputRef = useRef<HTMLInputElement>(null);
  const cropperRef = useRef<HTMLImageElement>(null);
  
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [profileUser, setProfileUser] = useState<ProfileUser | null>(null);
  const [activeTab, setActiveTab] = useState<'basic' | 'image' | 'social' | 'password'>('basic');
  
  // 기본 정보 폼
  const [basicForm, setBasicForm] = useState({
    nickname: '',
    email: '',
    birth_date: '',
    gender: '',
    marketing_agreed: false,
  });
  const [bioContent, setBioContent] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});

  // 소셜 링크 폼
  const [socialForm, setSocialForm] = useState<SocialLinks>({
    website: '',
    kakao: '',
    instagram: '',
    facebook: '',
    youtube: '',
    tiktok: '',
  });

  // 비밀번호 변경 폼
  const [passwordForm, setPasswordForm] = useState({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
  });

  // 이미지 관리
  const [imageFile, setImageFile] = useState<File | null>(null);
  const [cropData, setCropData] = useState('');
  const [cropper, setCropper] = useState<any>();
  const [showCropModal, setShowCropModal] = useState(false);
  const [imageUploading, setImageUploading] = useState(false);

  // 프로필 데이터 로드
  useEffect(() => {
    const loadProfileData = async () => {
      if (!isAuthenticated) {
        showError('로그인이 필요합니다.');
        navigate('/auth/login');
        return;
      }

      try {
        const response = await fetch('/profile/edit', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        
        if (data.user) {
          setProfileUser(data.user);
          
          // 폼 초기화
          setBasicForm({
            nickname: data.user.nickname || '',
            email: data.user.email || '',
            birth_date: data.user.birth_date || '',
            gender: data.user.gender || '',
            marketing_agreed: data.user.marketing_agreed || false,
          });
          
          setBioContent(data.user.bio || '');
          
          if (data.user.social_links) {
            setSocialForm({
              website: data.user.social_links.website || '',
              kakao: data.user.social_links.kakao || '',
              instagram: data.user.social_links.instagram || '',
              facebook: data.user.social_links.facebook || '',
              youtube: data.user.social_links.youtube || '',
              tiktok: data.user.social_links.tiktok || '',
            });
          }
        }
      } catch (error) {
        console.error('프로필 데이터 로드 실패:', error);
        showError('프로필 정보를 불러오는데 실패했습니다.');
      } finally {
        setLoading(false);
      }
    };

    loadProfileData();
  }, [isAuthenticated, navigate]);

  // Quill 에디터 설정
  const quillModules = {
    toolbar: [
      ['bold', 'italic', 'underline'],
      [{ 'list': 'ordered'}, { 'list': 'bullet' }],
      ['link'],
      ['clean']
    ],
  };

  const quillFormats = [
    'bold', 'italic', 'underline',
    'list', 'bullet',
    'link'
  ];

  // 폼 입력 처리
  const handleBasicFormChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value, type } = e.target;
    const checked = (e.target as HTMLInputElement).checked;
    
    setBasicForm(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));

    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  const handleSocialFormChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setSocialForm(prev => ({ ...prev, [name]: value }));
  };

  const handlePasswordFormChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setPasswordForm(prev => ({ ...prev, [name]: value }));
    
    if (errors[name]) {
      setErrors(prev => ({ ...prev, [name]: '' }));
    }
  };

  // 이미지 선택 처리
  const handleImageSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // 파일 타입 검증
    if (!file.type.startsWith('image/')) {
      showError('이미지 파일만 업로드할 수 있습니다.');
      return;
    }

    // 파일 크기 검증 (5MB 제한)
    if (file.size > 5 * 1024 * 1024) {
      showError('파일 크기는 5MB를 초과할 수 없습니다.');
      return;
    }

    setImageFile(file);
    
    // 이미지 크롭 모달 표시를 위한 미리보기 생성
    const reader = new FileReader();
    reader.onload = () => {
      setShowCropModal(true);
    };
    reader.readAsDataURL(file);
  };

  // 이미지 크롭 완료
  const handleCropImage = () => {
    if (typeof cropper !== 'undefined') {
      const croppedCanvas = cropper.getCroppedCanvas({
        width: 200,
        height: 200,
      });
      
      setCropData(croppedCanvas.toDataURL());
      setShowCropModal(false);
    }
  };

  // 프로필 이미지 경로
  const getProfileImageUrl = () => {
    if (cropData) return cropData;
    if (profileUser?.profile_image_profile) return profileUser.profile_image_profile;
    if (profileUser?.profile_image_thumb) return profileUser.profile_image_thumb;
    return '/assets/images/default-avatar.png';
  };

  // 유효성 검사
  const validateBasicForm = () => {
    const newErrors: Record<string, string> = {};

    if (!basicForm.nickname.trim()) {
      newErrors.nickname = '닉네임을 입력해주세요.';
    } else if (basicForm.nickname.length < 2 || basicForm.nickname.length > 20) {
      newErrors.nickname = '닉네임은 2-20자 사이로 입력해주세요.';
    }

    if (!basicForm.email.trim()) {
      newErrors.email = '이메일을 입력해주세요.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(basicForm.email)) {
      newErrors.email = '올바른 이메일 주소를 입력해주세요.';
    }

    // 자기소개 HTML 순수 텍스트 길이 검사
    const bioTextLength = bioContent.replace(/<[^>]*>/g, '').length;
    if (bioTextLength > 2000) {
      newErrors.bio = '자기소개는 2000자 이하로 입력해주세요.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const validatePasswordForm = () => {
    const newErrors: Record<string, string> = {};

    if (!passwordForm.current_password) {
      newErrors.current_password = '현재 비밀번호를 입력해주세요.';
    }

    if (!passwordForm.new_password) {
      newErrors.new_password = '새 비밀번호를 입력해주세요.';
    } else if (passwordForm.new_password.length < 8) {
      newErrors.new_password = '비밀번호는 8자 이상이어야 합니다.';
    } else if (!/(?=.*[a-zA-Z])(?=.*\d)/.test(passwordForm.new_password)) {
      newErrors.new_password = '비밀번호는 영문과 숫자를 포함해야 합니다.';
    }

    if (passwordForm.new_password !== passwordForm.new_password_confirmation) {
      newErrors.new_password_confirmation = '비밀번호가 일치하지 않습니다.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // URL 유효성 검사
  const isValidUrl = (url: string) => {
    if (!url) return true; // 빈 문자열은 유효
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  };

  // 기본 정보 업데이트
  const handleBasicSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validateBasicForm()) return;

    setSaving(true);
    try {
      const formData = new FormData();
      formData.append('csrf_token', 'dummy'); // PHP에서 CSRF 처리
      formData.append('nickname', basicForm.nickname);
      formData.append('email', basicForm.email);
      formData.append('bio', bioContent);
      formData.append('birth_date', basicForm.birth_date);
      formData.append('gender', basicForm.gender);
      formData.append('marketing_agreed', basicForm.marketing_agreed ? '1' : '0');

      const response = await fetch('/profile/update', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: formData
      });

      const result = await response.json();
      
      if (response.ok && result.message) {
        showSuccess(result.message);
        // 최신 데이터 다시 로드
        window.location.reload();
      } else {
        throw new Error(result.error || '업데이트에 실패했습니다.');
      }
    } catch (error) {
      console.error('기본 정보 업데이트 실패:', error);
      showError(error instanceof Error ? error.message : '업데이트에 실패했습니다.', 'error');
    } finally {
      setSaving(false);
    }
  };

  // 소셜 링크 업데이트
  const handleSocialSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    // URL 유효성 검사
    const urlErrors: Record<string, string> = {};
    Object.entries(socialForm).forEach(([key, value]) => {
      if (value && !isValidUrl(value)) {
        urlErrors[key] = '올바른 URL을 입력해주세요.';
      }
    });
    
    if (Object.keys(urlErrors).length > 0) {
      setErrors(urlErrors);
      return;
    }

    setSaving(true);
    try {
      const formData = new FormData();
      formData.append('csrf_token', 'dummy');
      Object.entries(socialForm).forEach(([key, value]) => {
        formData.append(`social_${key}`, value || '');
      });

      const response = await fetch('/profile/update', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: formData
      });

      const result = await response.json();
      
      if (response.ok && result.message) {
        showSuccess(result.message);
      } else {
        throw new Error(result.error || '업데이트에 실패했습니다.');
      }
    } catch (error) {
      console.error('소셜 링크 업데이트 실패:', error);
      showError(error instanceof Error ? error.message : '업데이트에 실패했습니다.', 'error');
    } finally {
      setSaving(false);
    }
  };

  // 이미지 업로드
  const handleImageUpload = async () => {
    if (!cropData) {
      showError('이미진를 선택하고 크롭해주세요.');
      return;
    }

    setImageUploading(true);
    try {
      // Canvas에서 Blob 생성
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d');
      const img = new Image();
      
      await new Promise((resolve) => {
        img.onload = resolve;
        img.src = cropData;
      });
      
      canvas.width = img.width;
      canvas.height = img.height;
      ctx?.drawImage(img, 0, 0);
      
      canvas.toBlob(async (blob) => {
        if (!blob) return;
        
        const formData = new FormData();
        formData.append('csrf_token', 'dummy');
        formData.append('profile_image', blob, 'profile.jpg');

        const response = await fetch('/profile/upload-image', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: formData
        });

        const result = await response.json();
        
        if (response.ok && result.message) {
          showSuccess(result.message);
          setCropData(''); // 초기화
          // 최신 데이터 다시 로드
          window.location.reload();
        } else {
          throw new Error(result.error || '이미지 업로드에 실패했습니다.');
        }
      }, 'image/jpeg', 0.85);
    } catch (error) {
      console.error('이미지 업로드 실패:', error);
      showError(error instanceof Error ? error.message : '이미지 업로드에 실패했습니다.', 'error');
    } finally {
      setImageUploading(false);
    }
  };

  // 비밀번호 변경
  const handlePasswordSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!validatePasswordForm()) return;

    setSaving(true);
    try {
      const formData = new FormData();
      formData.append('csrf_token', 'dummy');
      formData.append('current_password', passwordForm.current_password);
      formData.append('new_password', passwordForm.new_password);
      formData.append('new_password_confirmation', passwordForm.new_password_confirmation);

      const response = await fetch('/profile/change-password', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`
        },
        body: formData
      });

      const result = await response.json();
      
      if (response.ok && result.message) {
        showSuccess(result.message);
        setPasswordForm({
          current_password: '',
          new_password: '',
          new_password_confirmation: '',
        });
      } else {
        throw new Error(result.error || '비밀번호 변경에 실패했습니다.');
      }
    } catch (error) {
      console.error('비밀번호 변경 실패:', error);
      showError(error instanceof Error ? error.message : '비밀번호 변경에 실패했습니다.', 'error');
    } finally {
      setSaving(false);
    }
  };
  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="프로필 정보를 불러오는 중..." />
      </div>
    );
  }

  if (!profileUser) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">프로필 정보를 찾을 수 없습니다</h2>
          <Link to="/profile">
            <Button>프로필로 돌아가기</Button>
          </Link>
        </div>
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
            className="inline-flex items-center text-purple-600 hover:text-purple-700 transition-colors"
          >
            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            프로필로 돌아가기
          </Link>
        </div>

        {/* 헤더 */}
        <div className="bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-2xl shadow-lg p-8 mb-8">
          <div className="flex items-center space-x-4">
            <div className="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
              <span className="text-2xl">✏️</span>
            </div>
            <div>
              <h1 className="text-3xl font-bold">프로필 편집</h1>
              <p className="text-blue-100 mt-2">
                프로필 정보를 수정하고 계정을 관리하세요.
              </p>
            </div>
          </div>
        </div>

        {/* 탭 메뉴 */}
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8">
          <div className="p-6 border-b border-gray-100">
            <nav className="flex space-x-8">
              {[
                { key: 'basic', label: '기본 정보', icon: '👤' },
                { key: 'image', label: '프로필 이미지', icon: '📷' },
                { key: 'social', label: '소셜 링크', icon: '🔗' },
                { key: 'password', label: '비밀번호', icon: '🔒' },
              ].map((tab) => (
                <button
                  key={tab.key}
                  onClick={() => setActiveTab(tab.key as any)}
                  className={`flex items-center py-3 px-4 rounded-lg font-medium text-sm transition-all duration-200 ${
                    activeTab === tab.key
                      ? 'bg-purple-50 text-purple-600 shadow-sm'
                      : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50'
                  }`}
                >
                  <span className="mr-2">{tab.icon}</span>
                  {tab.label}
                </button>
              ))}
            </nav>
          </div>

          {/* 탭 콘텐츠 */}
          <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            {/* 기본 정보 탭 */}
            {activeTab === 'basic' && (
              <form onSubmit={handleBasicSubmit} className="space-y-6">
                <div className="mb-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">기본 정보</h2>
                  <p className="text-gray-600">프로필에 표시될 기본 정보를 설정하세요.</p>
                </div>

                {/* 닉네임 */}
                <Input
                  label="닉네임"
                  name="nickname"
                  value={basicForm.nickname}
                  onChange={handleBasicFormChange}
                  error={errors.nickname}
                  placeholder="사용할 닉네임을 입력하세요"
                  required
                  fullWidth
                  maxLength={20}
                  hint={`${basicForm.nickname.length}/20`}
                />

                {/* 이메일 */}
                <Input
                  label="이메일"
                  type="email"
                  name="email"
                  value={basicForm.email}
                  onChange={handleBasicFormChange}
                  error={errors.email}
                  placeholder="example@email.com"
                  required
                  fullWidth
                />

                {/* 생년월일 */}
                <Input
                  label="생년월일"
                  type="date"
                  name="birth_date"
                  value={basicForm.birth_date}
                  onChange={handleBasicFormChange}
                  fullWidth
                />

                {/* 성별 */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">성별</label>
                  <select
                    name="gender"
                    value={basicForm.gender}
                    onChange={handleBasicFormChange}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  >
                    <option value="">선택 안함</option>
                    <option value="M">남성</option>
                    <option value="F">여성</option>
                    <option value="OTHER">기타</option>
                  </select>
                </div>

                {/* 자기소개 */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    자기소개
                  </label>
                  <ReactQuill
                    theme="snow"
                    value={bioContent}
                    onChange={setBioContent}
                    modules={quillModules}
                    formats={quillFormats}
                    placeholder="자신을 소개해보세요..."
                    style={{ height: '150px', marginBottom: '50px' }}
                  />
                  {errors.bio && (
                    <p className="text-red-600 text-sm mt-2">{errors.bio}</p>
                  )}
                  <p className="text-sm text-gray-500 mt-2">
                    현재 {bioContent.replace(/<[^>]*>/g, '').length}/2000자
                  </p>
                </div>

                {/* 마케팅 동의 */}
                <div className="flex items-center">
                  <input
                    id="marketing_agreed"
                    name="marketing_agreed"
                    type="checkbox"
                    checked={basicForm.marketing_agreed}
                    onChange={handleBasicFormChange}
                    className="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded"
                  />
                  <label htmlFor="marketing_agreed" className="ml-2 block text-sm text-gray-700">
                    마케팅 정보 수신에 동의합니다 (선택)
                  </label>
                </div>

                {/* 저장 버튼 */}
                <div className="pt-6 border-t border-gray-200">
                  <Button
                    type="submit"
                    loading={saving}
                    className="bg-purple-600 hover:bg-purple-700"
                  >
                    저장하기
                  </Button>
                </div>
              </form>
            )}

            {/* 프로필 이미지 탭 */}
            {activeTab === 'image' && (
              <div className="space-y-6">
                <div className="mb-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">프로필 이미지</h2>
                  <p className="text-gray-600">프로필에 표시될 이미지를 설정하세요.</p>
                </div>

                {/* 현재 이미지 */}
                <div className="text-center">
                  <div className="w-32 h-32 mx-auto rounded-full overflow-hidden border-4 border-gray-200 mb-4">
                    <img
                      src={getProfileImageUrl()}
                      alt="프로필 이미지"
                      className="w-full h-full object-cover"
                      onError={(e) => {
                        const target = e.target as HTMLImageElement;
                        target.src = '/assets/images/default-avatar.png';
                      }}
                    />
                  </div>
                </div>

                {/* 이미지 선택 */}
                <div className="text-center">
                  <input
                    type="file"
                    ref={fileInputRef}
                    accept="image/*"
                    onChange={handleImageSelect}
                    className="hidden"
                  />
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => fileInputRef.current?.click()}
                    className="mr-4"
                  >
                    이미지 선택
                  </Button>
                  {cropData && (
                    <Button
                      type="button"
                      loading={imageUploading}
                      onClick={handleImageUpload}
                      className="bg-purple-600 hover:bg-purple-700"
                    >
                      이미지 저장
                    </Button>
                  )}
                </div>

                <div className="text-center text-sm text-gray-500">
                  JPG, PNG 파일 (최대 5MB)
                </div>
              </div>
            )}

            {/* 소셜 링크 탭 */}
            {activeTab === 'social' && (
              <form onSubmit={handleSocialSubmit} className="space-y-6">
                <div className="mb-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">소셜 링크</h2>
                  <p className="text-gray-600">소셜 맸디어 링크를 설정하여 다른 사용자와 연결하세요.</p>
                </div>

                <Input
                  label="웹사이트"
                  name="website"
                  value={socialForm.website || ''}
                  onChange={handleSocialFormChange}
                  error={errors.website}
                  placeholder="https://example.com"
                  fullWidth
                />

                <Input
                  label="카카오톡 오픈채팅"
                  name="kakao"
                  value={socialForm.kakao || ''}
                  onChange={handleSocialFormChange}
                  error={errors.kakao}
                  placeholder="https://open.kakao.com/o/xxxxxxx"
                  fullWidth
                />

                <Input
                  label="인스타그램"
                  name="instagram"
                  value={socialForm.instagram || ''}
                  onChange={handleSocialFormChange}
                  error={errors.instagram}
                  placeholder="https://instagram.com/username"
                  fullWidth
                />

                <Input
                  label="페이스북"
                  name="facebook"
                  value={socialForm.facebook || ''}
                  onChange={handleSocialFormChange}
                  error={errors.facebook}
                  placeholder="https://facebook.com/username"
                  fullWidth
                />

                <Input
                  label="유튜브"
                  name="youtube"
                  value={socialForm.youtube || ''}
                  onChange={handleSocialFormChange}
                  error={errors.youtube}
                  placeholder="https://youtube.com/@channelname"
                  fullWidth
                />

                <Input
                  label="틱톡"
                  name="tiktok"
                  value={socialForm.tiktok || ''}
                  onChange={handleSocialFormChange}
                  error={errors.tiktok}
                  placeholder="https://tiktok.com/@username"
                  fullWidth
                />

                <div className="pt-6 border-t border-gray-200">
                  <Button
                    type="submit"
                    loading={saving}
                    className="bg-purple-600 hover:bg-purple-700"
                  >
                    저장하기
                  </Button>
                </div>
              </form>
            )}

            {/* 비밀번호 변경 탭 */}
            {activeTab === 'password' && (
              <form onSubmit={handlePasswordSubmit} className="space-y-6">
                <div className="mb-6">
                  <h2 className="text-2xl font-bold text-gray-900 mb-2">비밀번호 변경</h2>
                  <p className="text-gray-600">
                    계정 보안을 위해 정기적으로 비밀번호를 변경해주세요.
                  </p>
                </div>

                <Input
                  label="현재 비밀번호"
                  type="password"
                  name="current_password"
                  value={passwordForm.current_password}
                  onChange={handlePasswordFormChange}
                  error={errors.current_password}
                  placeholder="현재 비밀번호를 입력하세요"
                  required
                  fullWidth
                />

                <Input
                  label="새 비밀번호"
                  type="password"
                  name="new_password"
                  value={passwordForm.new_password}
                  onChange={handlePasswordFormChange}
                  error={errors.new_password}
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
                  onChange={handlePasswordFormChange}
                  error={errors.new_password_confirmation}
                  placeholder="새 비밀번호를 다시 입력하세요"
                  required
                  fullWidth
                />

                <div className="pt-6 border-t border-gray-200">
                  <Button
                    type="submit"
                    loading={saving}
                    className="bg-red-600 hover:bg-red-700"
                  >
                    비밀번호 변경
                  </Button>
                </div>
              </form>
            )}
          </div>
        </div>
      </div>

      {/* 이미지 크롭 모달 */}
      {showCropModal && imageFile && (
        <div className="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
          <div className="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div className="p-6 border-b border-gray-200">
              <h3 className="text-xl font-bold text-gray-900">이미지 크롭</h3>
              <p className="text-gray-600 mt-1">원하는 영역을 선택하여 크롭하세요.</p>
            </div>
            
            <div className="p-6">
              <div className="mb-4">
                <Cropper
                  ref={cropperRef}
                  style={{ height: 400, width: '100%' }}
                  zoomTo={0.5}
                  initialAspectRatio={1}
                  preview=".img-preview"
                  src={URL.createObjectURL(imageFile)}
                  viewMode={1}
                  minCropBoxHeight={100}
                  minCropBoxWidth={100}
                  background={false}
                  responsive={true}
                  autoCropArea={1}
                  checkOrientation={false}
                  onInitialized={(instance) => {
                    setCropper(instance);
                  }}
                  guides={true}
                  aspectRatio={1}
                />
              </div>
              
              <div className="flex justify-between items-center">
                <div className="flex items-center space-x-4">
                  <div className="text-sm text-gray-600">미리보기:</div>
                  <div
                    className="img-preview w-16 h-16 rounded-full overflow-hidden border-2 border-gray-200"
                    style={{ width: '64px', height: '64px' }}
                  />
                </div>
                
                <div className="flex space-x-3">
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => {
                      setShowCropModal(false);
                      setImageFile(null);
                    }}
                  >
                    취소
                  </Button>
                  <Button
                    type="button"
                    onClick={handleCropImage}
                    className="bg-purple-600 hover:bg-purple-700"
                  >
                    크롭 완료
                  </Button>
                </div>
              </div>
            </div>
          </div>

        </div>
      )}
    </div>
  );
};

export default EditProfilePage;