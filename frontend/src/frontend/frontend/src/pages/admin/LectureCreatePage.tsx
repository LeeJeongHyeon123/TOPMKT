import React, { useState, useEffect } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { CreateLectureRequest, Lecture } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const LectureCreatePage: React.FC = () => {
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  
  // 강의 정보 폼
  const [formData, setFormData] = useState({
    title: '',
    description: '',
    content: '',
    price: 0,
    duration: 0,
    category: '',
    status: 'ACTIVE' as 'ACTIVE' | 'INACTIVE',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});

  // 파일 업로드
  const [thumbnail, setThumbnail] = useState<File | null>(null);
  const [thumbnailPreview, setThumbnailPreview] = useState<string>('');
  const [videoFile, setVideoFile] = useState<File | null>(null);
  const [uploadingThumbnail, setUploadingThumbnail] = useState(false);
  const [uploadingVideo, setUploadingVideo] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');

  // 권한 체크
  useEffect(() => {
    if (!isAuthenticated || !user) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login', { state: { from: location.pathname } });
      return;
    }

    // 관리자나 기업회원이 아닌 경우 접근 제한
    if (user.role === 'ROLE_USER') {
      error('강의 등록 권한이 없습니다.');
      navigate('/');
      return;
    }
  }, [isAuthenticated, user]);

  // 폼 입력 처리
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    const { name, value, type } = e.target;
    
    setFormData(prev => ({
      ...prev,
      [name]: type === 'number' ? (value === '' ? 0 : Number(value)) : value
    }));

    // 에러 메시지 클리어
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  // 썸네일 선택 처리
  const handleThumbnailSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // 파일 타입 검증
    if (!file.type.startsWith('image/')) {
      error('이미지 파일만 업로드할 수 있습니다.');
      return;
    }

    // 파일 크기 검증 (10MB 제한)
    if (file.size > 10 * 1024 * 1024) {
      error('파일 크기는 10MB를 초과할 수 없습니다.');
      return;
    }

    setThumbnail(file);
    
    // 미리보기 생성
    const reader = new FileReader();
    reader.onload = (e) => {
      setThumbnailPreview(e.target?.result as string);
    };
    reader.readAsDataURL(file);
  };

  // 동영상 선택 처리
  const handleVideoSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;

    // 파일 타입 검증
    if (!file.type.startsWith('video/')) {
      error('동영상 파일만 업로드할 수 있습니다.');
      return;
    }

    // 파일 크기 검증 (500MB 제한)
    if (file.size > 500 * 1024 * 1024) {
      error('동영상 파일 크기는 500MB를 초과할 수 없습니다.');
      return;
    }

    setVideoFile(file);
  };

  // 유효성 검사
  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.title.trim()) {
      newErrors.title = '강의 제목을 입력해주세요.';
    } else if (formData.title.length < 5) {
      newErrors.title = '강의 제목은 5자 이상 입력해주세요.';
    } else if (formData.title.length > 100) {
      newErrors.title = '강의 제목은 100자 이하로 입력해주세요.';
    }

    if (!formData.description.trim()) {
      newErrors.description = '강의 설명을 입력해주세요.';
    } else if (formData.description.length < 20) {
      newErrors.description = '강의 설명은 20자 이상 입력해주세요.';
    } else if (formData.description.length > 500) {
      newErrors.description = '강의 설명은 500자 이하로 입력해주세요.';
    }

    if (!formData.content.trim()) {
      newErrors.content = '강의 내용을 입력해주세요.';
    } else if (formData.content.length < 50) {
      newErrors.content = '강의 내용은 50자 이상 입력해주세요.';
    }

    if (!formData.category.trim()) {
      newErrors.category = '카테고리를 선택해주세요.';
    }

    if (formData.price < 0) {
      newErrors.price = '가격은 0원 이상이어야 합니다.';
    }

    if (formData.duration < 0) {
      newErrors.duration = '강의 시간은 0분 이상이어야 합니다.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // 썸네일 업로드
  const uploadThumbnail = async (): Promise<string> => {
    if (!thumbnail) return '';

    setUploadingThumbnail(true);
    try {
      const formData = new FormData();
      formData.append('file', thumbnail);

      const response = await request({
        url: '/upload/lecture-thumbnail',
        method: 'POST',
        data: formData,
        headers: { 'Content-Type': 'multipart/form-data' },
      });

      if (response.success && response.data) {
        return response.data.url;
      }
      return '';
    } catch (err) {
      console.error('썸네일 업로드 실패:', err);
      return '';
    } finally {
      setUploadingThumbnail(false);
    }
  };

  // 동영상 업로드
  const uploadVideo = async (): Promise<string> => {
    if (!videoFile) return '';

    setUploadingVideo(true);
    try {
      const formData = new FormData();
      formData.append('file', videoFile);

      const response = await request({
        url: '/upload/lecture-video',
        method: 'POST',
        data: formData,
        headers: { 'Content-Type': 'multipart/form-data' },
        timeout: 300000, // 5분 타임아웃
      });

      if (response.success && response.data) {
        return response.data.url;
      }
      return '';
    } catch (err) {
      console.error('동영상 업로드 실패:', err);
      return '';
    } finally {
      setUploadingVideo(false);
    }
  };

  // 폼 제출
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) return;

    setSubmitting(true);
    try {
      // 파일 업로드
      const [thumbnailUrl, uploadedVideoUrl] = await Promise.all([
        uploadThumbnail(),
        uploadVideo(),
      ]);

      // 강의 데이터 생성
      const lectureData: CreateLectureRequest = {
        title: formData.title.trim(),
        description: formData.description.trim(),
        content: formData.content.trim(),
        price: formData.price,
        duration: formData.duration || undefined,
        thumbnail: thumbnailUrl || undefined,
        video_url: uploadedVideoUrl || videoUrl || undefined,
      };

      const response = await request<Lecture>({
        url: '/lectures',
        method: 'POST',
        data: lectureData,
      });

      if (response.success && response.data) {
        success('강의가 성공적으로 등록되었습니다!');
        navigate(`/lectures/${response.data.id}`);
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '강의 등록에 실패했습니다.';
      error(errorMessage);
    } finally {
      setSubmitting(false);
    }
  };

  // 강의 카테고리 목록
  const categories = [
    { value: '', label: '카테고리 선택' },
    { value: 'marketing', label: '마케팅 기초' },
    { value: 'sales', label: '영업 전략' },
    { value: 'leadership', label: '리더십' },
    { value: 'communication', label: '커뮤니케이션' },
    { value: 'mindset', label: '마인드셋' },
    { value: 'business', label: '사업 운영' },
    { value: 'advanced', label: '고급 전략' },
  ];

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="페이지를 불러오는 중..." />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 네비게이션 */}
        <div className="mb-6">
          <Link
            to="/admin/lectures"
            className="inline-flex items-center text-blue-600 hover:text-blue-700"
          >
            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            강의 관리로 돌아가기
          </Link>
        </div>

        {/* 헤더 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <h1 className="text-3xl font-bold text-gray-900">
            새 강의 등록
          </h1>
          <p className="text-gray-600 mt-2">
            새로운 강의를 등록하여 학습자들에게 지식을 전달해보세요.
          </p>
        </div>

        {/* 강의 등록 폼 */}
        <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow-sm p-6">
          <div className="space-y-8">
            {/* 기본 정보 섹션 */}
            <div>
              <h2 className="text-xl font-bold text-gray-900 mb-6">기본 정보</h2>
              <div className="grid grid-cols-1 gap-6">
                {/* 강의 제목 */}
                <Input
                  label="강의 제목"
                  name="title"
                  value={formData.title}
                  onChange={handleInputChange}
                  error={errors.title}
                  placeholder="매력적인 강의 제목을 입력하세요"
                  required
                  fullWidth
                  maxLength={100}
                  hint={`${formData.title.length}/100`}
                />

                {/* 강의 설명 */}
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    강의 설명 <span className="text-red-500">*</span>
                  </label>
                  <textarea
                    name="description"
                    value={formData.description}
                    onChange={handleInputChange}
                    placeholder="강의에 대한 간단한 설명을 작성해주세요"
                    rows={3}
                    maxLength={500}
                    className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                      errors.description ? 'border-red-300' : 'border-gray-300'
                    }`}
                  />
                  <div className="flex justify-between items-center mt-2">
                    {errors.description && (
                      <p className="text-red-600 text-sm">{errors.description}</p>
                    )}
                    <div className="text-sm text-gray-500 ml-auto">
                      {formData.description.length}/500
                    </div>
                  </div>
                </div>

                {/* 카테고리와 가격 */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      카테고리 <span className="text-red-500">*</span>
                    </label>
                    <select
                      name="category"
                      value={formData.category}
                      onChange={handleInputChange}
                      className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent ${
                        errors.category ? 'border-red-300' : 'border-gray-300'
                      }`}
                    >
                      {categories.map((category) => (
                        <option key={category.value} value={category.value}>
                          {category.label}
                        </option>
                      ))}
                    </select>
                    {errors.category && (
                      <p className="text-red-600 text-sm mt-1">{errors.category}</p>
                    )}
                  </div>

                  <Input
                    label="강의 가격"
                    type="number"
                    name="price"
                    value={formData.price.toString()}
                    onChange={handleInputChange}
                    error={errors.price}
                    placeholder="0"
                    min={0}
                    fullWidth
                    hint="0원 입력시 무료 강의로 등록됩니다"
                  />
                </div>

                {/* 강의 시간 */}
                <Input
                  label="강의 시간 (분)"
                  type="number"
                  name="duration"
                  value={formData.duration.toString()}
                  onChange={handleInputChange}
                  error={errors.duration}
                  placeholder="60"
                  min={0}
                  fullWidth
                  hint="예상 강의 시간을 분 단위로 입력해주세요"
                />
              </div>
            </div>

            {/* 미디어 섹션 */}
            <div className="border-t border-gray-200 pt-8">
              <h2 className="text-xl font-bold text-gray-900 mb-6">미디어</h2>
              
              {/* 썸네일 업로드 */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-4">
                  강의 썸네일
                </label>
                <div className="flex items-start space-x-6">
                  <div className="w-48 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center overflow-hidden">
                    {thumbnailPreview ? (
                      <img
                        src={thumbnailPreview}
                        alt="썸네일 미리보기"
                        className="w-full h-full object-cover"
                      />
                    ) : (
                      <div className="text-center">
                        <svg className="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p className="text-sm text-gray-500">썸네일 없음</p>
                      </div>
                    )}
                  </div>
                  <div>
                    <input
                      type="file"
                      id="thumbnail"
                      accept="image/*"
                      onChange={handleThumbnailSelect}
                      className="hidden"
                    />
                    <label htmlFor="thumbnail">
                      <Button
                        type="button"
                        variant="outline"
                        loading={uploadingThumbnail}
                        onClick={() => document.getElementById('thumbnail')?.click()}
                      >
                        썸네일 선택
                      </Button>
                    </label>
                    <p className="text-xs text-gray-500 mt-2">
                      JPG, PNG 파일 (최대 10MB)<br />
                      권장 크기: 1280 x 720
                    </p>
                  </div>
                </div>
              </div>

              {/* 동영상 업로드 */}
              <div className="mb-6">
                <label className="block text-sm font-medium text-gray-700 mb-4">
                  강의 동영상
                </label>
                <div className="space-y-4">
                  {/* 파일 업로드 */}
                  <div>
                    <input
                      type="file"
                      id="video"
                      accept="video/*"
                      onChange={handleVideoSelect}
                      className="hidden"
                    />
                    <div className="flex items-center space-x-4">
                      <label htmlFor="video">
                        <Button
                          type="button"
                          variant="outline"
                          loading={uploadingVideo}
                          onClick={() => document.getElementById('video')?.click()}
                        >
                          동영상 파일 선택
                        </Button>
                      </label>
                      {videoFile && (
                        <span className="text-sm text-gray-600">
                          선택된 파일: {videoFile.name}
                        </span>
                      )}
                    </div>
                    <p className="text-xs text-gray-500 mt-2">
                      MP4, MOV, AVI 파일 (최대 500MB)
                    </p>
                  </div>

                  {/* 또는 URL 입력 */}
                  <div className="relative">
                    <div className="absolute inset-0 flex items-center">
                      <div className="w-full border-t border-gray-300" />
                    </div>
                    <div className="relative flex justify-center text-sm">
                      <span className="px-2 bg-white text-gray-500">또는</span>
                    </div>
                  </div>

                  <Input
                    label="동영상 URL"
                    name="video_url"
                    value={videoUrl}
                    onChange={(e) => setVideoUrl(e.target.value)}
                    placeholder="https://example.com/video.mp4"
                    fullWidth
                    hint="YouTube, Vimeo 등의 동영상 URL을 입력할 수 있습니다"
                  />
                </div>
              </div>
            </div>

            {/* 강의 내용 섹션 */}
            <div className="border-t border-gray-200 pt-8">
              <h2 className="text-xl font-bold text-gray-900 mb-6">강의 내용</h2>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  강의 내용 <span className="text-red-500">*</span>
                </label>
                <textarea
                  name="content"
                  value={formData.content}
                  onChange={handleInputChange}
                  placeholder="강의의 상세 내용을 작성해주세요. 마크다운 문법을 사용할 수 있습니다."
                  rows={15}
                  className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                    errors.content ? 'border-red-300' : 'border-gray-300'
                  }`}
                />
                <div className="flex justify-between items-center mt-2">
                  {errors.content && (
                    <p className="text-red-600 text-sm">{errors.content}</p>
                  )}
                  <div className="text-sm text-gray-500 ml-auto">
                    {formData.content.length.toLocaleString()} 자
                  </div>
                </div>
              </div>
            </div>

            {/* 강의 상태 */}
            <div className="border-t border-gray-200 pt-8">
              <h2 className="text-xl font-bold text-gray-900 mb-6">강의 설정</h2>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  강의 상태
                </label>
                <select
                  name="status"
                  value={formData.status}
                  onChange={handleInputChange}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                  <option value="ACTIVE">활성 (수강 신청 가능)</option>
                  <option value="INACTIVE">비활성 (수강 신청 불가)</option>
                </select>
              </div>
            </div>

            {/* 등록 가이드 */}
            <div className="bg-blue-50 rounded-lg p-6">
              <h3 className="text-lg font-semibold text-blue-900 mb-3">
                📚 강의 등록 가이드
              </h3>
              <ul className="text-sm text-blue-800 space-y-2">
                <li>• <strong>제목:</strong> 학습자가 쉽게 이해할 수 있는 명확한 제목을 작성하세요</li>
                <li>• <strong>설명:</strong> 강의의 핵심 내용과 학습 목표를 간결하게 요약하세요</li>
                <li>• <strong>썸네일:</strong> 강의 내용을 잘 표현하는 매력적인 이미지를 사용하세요</li>
                <li>• <strong>내용:</strong> 체계적이고 이해하기 쉬운 구조로 강의를 구성하세요</li>
                <li>• <strong>가격:</strong> 강의의 가치와 시장 상황을 고려하여 적정 가격을 책정하세요</li>
              </ul>
            </div>

            {/* 버튼 영역 */}
            <div className="flex justify-between items-center pt-6 border-t border-gray-200">
              <div className="flex space-x-3">
                <Button
                  type="button"
                  variant="ghost"
                  onClick={() => navigate('/admin/lectures')}
                >
                  취소
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  onClick={() => {
                    // TODO: 임시저장 기능 구현
                    success('임시저장되었습니다.');
                  }}
                  disabled={submitting}
                >
                  임시저장
                </Button>
              </div>

              <Button
                type="submit"
                loading={submitting || uploadingThumbnail || uploadingVideo}
                disabled={!formData.title.trim() || !formData.description.trim() || !formData.content.trim()}
                className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
              >
                강의 등록
              </Button>
            </div>
          </div>
        </form>
      </div>
    </div>
  );
};

export default LectureCreatePage;