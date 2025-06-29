import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { Lecture, UpdateLectureRequest, LectureEnrollment } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const LectureEditPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [lecture, setLecture] = useState<Lecture | null>(null);
  const [enrollments, setEnrollments] = useState<LectureEnrollment[]>([]);
  const [activeTab, setActiveTab] = useState<'edit' | 'students' | 'stats'>('edit');
  
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
  const [videoFile] = useState<File | null>(null);
  const [uploadingThumbnail, setUploadingThumbnail] = useState(false);
  const [uploadingVideo, setUploadingVideo] = useState(false);
  const [videoUrl, setVideoUrl] = useState('');

  // 강의 데이터 로드
  useEffect(() => {
    if (!id) return;

    const fetchLecture = async () => {
      setLoading(true);
      try {
        // 강의 정보 조회
        const lectureResponse = await request<Lecture>({
          url: `/lectures/${id}`,
          method: 'GET',
        });

        if (lectureResponse.success && lectureResponse.data) {
          const lectureData = lectureResponse.data;
          setLecture(lectureData);

          // 권한 체크 - 강의 작성자나 관리자만 수정 가능
          if (user && user.id !== lectureData.instructor.id && user.role !== 'ROLE_ADMIN') {
            error('강의를 수정할 권한이 없습니다.');
            navigate('/lectures');
            return;
          }

          // 폼 데이터 설정
          setFormData({
            title: lectureData.title,
            description: lectureData.description,
            content: lectureData.content,
            price: lectureData.price,
            duration: lectureData.duration || 0,
            category: 'marketing', // TODO: 실제 카테고리 필드 추가 필요
            status: lectureData.status,
          });

          setThumbnailPreview(lectureData.thumbnail || '');
          setVideoUrl(lectureData.video_url || '');
        }

        // 수강생 목록 조회
        const enrollmentsResponse = await request<{ data: LectureEnrollment[] }>({
          url: `/lectures/${id}/enrollments`,
          method: 'GET',
        });

        if (enrollmentsResponse.success) {
          setEnrollments(enrollmentsResponse.data?.data || []);
        }
      } catch (err) {
        console.error('강의 조회 실패:', err);
        error('강의 정보를 불러올 수 없습니다.');
        navigate('/admin/lectures');
      } finally {
        setLoading(false);
      }
    };

    fetchLecture();
  }, [id, user]);

  // 권한 체크
  useEffect(() => {
    if (!isAuthenticated || !user) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login', { state: { from: location.pathname } });
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

    if (!file.type.startsWith('image/')) {
      error('이미지 파일만 업로드할 수 있습니다.');
      return;
    }

    if (file.size > 10 * 1024 * 1024) {
      error('파일 크기는 10MB를 초과할 수 없습니다.');
      return;
    }

    setThumbnail(file);
    
    const reader = new FileReader();
    reader.onload = (e) => {
      setThumbnailPreview(e.target?.result as string);
    };
    reader.readAsDataURL(file);
  };

  // 동영상 선택 처리 (향후 구현 예정)
  // const handleVideoSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
  //   const file = e.target.files?.[0];
  //   if (!file) return;

  //   if (!file.type.startsWith('video/')) {
  //     error('동영상 파일만 업로드할 수 있습니다.');
  //     return;
  //   }

  //   if (file.size > 500 * 1024 * 1024) {
  //     error('동영상 파일 크기는 500MB를 초과할 수 없습니다.');
  //     return;
  //   }

  //   setVideoFile(file);
  // };

  // 유효성 검사
  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.title.trim()) {
      newErrors.title = '강의 제목을 입력해주세요.';
    } else if (formData.title.length < 5) {
      newErrors.title = '강의 제목은 5자 이상 입력해주세요.';
    }

    if (!formData.description.trim()) {
      newErrors.description = '강의 설명을 입력해주세요.';
    } else if (formData.description.length < 20) {
      newErrors.description = '강의 설명은 20자 이상 입력해주세요.';
    }

    if (!formData.content.trim()) {
      newErrors.content = '강의 내용을 입력해주세요.';
    }

    if (formData.price < 0) {
      newErrors.price = '가격은 0원 이상이어야 합니다.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // 파일 업로드
  const uploadFiles = async () => {
    const uploads = [];

    if (thumbnail) {
      setUploadingThumbnail(true);
      const thumbnailFormData = new FormData();
      thumbnailFormData.append('file', thumbnail);
      uploads.push(
        request({
          url: '/upload/lecture-thumbnail',
          method: 'POST',
          data: thumbnailFormData,
          headers: { 'Content-Type': 'multipart/form-data' },
        }).finally(() => setUploadingThumbnail(false))
      );
    } else {
      uploads.push(Promise.resolve({ success: true, data: { url: thumbnailPreview } }));
    }

    if (videoFile) {
      setUploadingVideo(true);
      const videoFormData = new FormData();
      videoFormData.append('file', videoFile);
      uploads.push(
        request({
          url: '/upload/lecture-video',
          method: 'POST',
          data: videoFormData,
          headers: { 'Content-Type': 'multipart/form-data' },
          timeout: 300000,
        }).finally(() => setUploadingVideo(false))
      );
    } else {
      uploads.push(Promise.resolve({ success: true, data: { url: videoUrl } }));
    }

    const results = await Promise.all(uploads);
    return {
      thumbnailUrl: results[0].success ? results[0].data?.url || '' : '',
      videoUrl: results[1].success ? results[1].data?.url || '' : '',
    };
  };

  // 폼 제출
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm() || !lecture) return;

    setSubmitting(true);
    try {
      // 파일 업로드
      const { thumbnailUrl, videoUrl: uploadedVideoUrl } = await uploadFiles();

      // 강의 수정 데이터
      const updateData: UpdateLectureRequest = {
        title: formData.title.trim(),
        description: formData.description.trim(),
        content: formData.content.trim(),
        price: formData.price,
        duration: formData.duration || undefined,
        status: formData.status,
      };

      if (thumbnailUrl) updateData.thumbnail = thumbnailUrl;
      if (uploadedVideoUrl) updateData.video_url = uploadedVideoUrl;

      const response = await request<Lecture>({
        url: `/lectures/${lecture.id}`,
        method: 'PUT',
        data: updateData,
      });

      if (response.success) {
        success('강의가 성공적으로 수정되었습니다!');
        navigate(`/lectures/${lecture.id}`);
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '강의 수정에 실패했습니다.';
      error(errorMessage);
    } finally {
      setSubmitting(false);
    }
  };

  // 강의 삭제
  const handleDelete = async () => {
    if (!lecture || !window.confirm('정말로 이 강의를 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.')) return;

    try {
      const response = await request({
        url: `/lectures/${lecture.id}`,
        method: 'DELETE',
      });

      if (response.success) {
        success('강의가 삭제되었습니다.');
        navigate('/admin/lectures');
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '강의 삭제에 실패했습니다.';
      error(errorMessage);
    }
  };

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="강의 정보를 불러오는 중..." />
      </div>
    );
  }

  if (!lecture) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">강의를 찾을 수 없습니다</h2>
          <Link to="/admin/lectures">
            <Button>강의 관리로 돌아가기</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">
                강의 수정
              </h1>
              <p className="text-gray-600 mt-2">
                {lecture.title}
              </p>
            </div>
            <div className="flex space-x-3">
              <Link to={`/lectures/${lecture.id}`}>
                <Button variant="outline">
                  강의 보기
                </Button>
              </Link>
              <Button
                variant="outline"
                onClick={handleDelete}
                className="text-red-600 border-red-200 hover:bg-red-50"
              >
                강의 삭제
              </Button>
            </div>
          </div>
        </div>

        {/* 탭 메뉴 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8">
              {[
                { key: 'edit', label: '강의 편집', icon: 'pencil' },
                { key: 'students', label: `수강생 관리 (${enrollments.length})`, icon: 'users' },
                { key: 'stats', label: '통계', icon: 'chart-bar' },
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
                    {tab.icon === 'pencil' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    )}
                    {tab.icon === 'users' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    )}
                    {tab.icon === 'chart-bar' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    )}
                  </svg>
                  {tab.label}
                </button>
              ))}
            </nav>
          </div>
        </div>

        {/* 탭 콘텐츠 */}
        {activeTab === 'edit' && (
          <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow-sm p-6">
            <div className="space-y-8">
              {/* 기본 정보 */}
              <div>
                <h2 className="text-xl font-bold text-gray-900 mb-6">기본 정보</h2>
                <div className="grid grid-cols-1 gap-6">
                  <Input
                    label="강의 제목"
                    name="title"
                    value={formData.title}
                    onChange={handleInputChange}
                    error={errors.title}
                    required
                    fullWidth
                    maxLength={100}
                  />

                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      강의 설명 <span className="text-red-500">*</span>
                    </label>
                    <textarea
                      name="description"
                      value={formData.description}
                      onChange={handleInputChange}
                      rows={3}
                      maxLength={500}
                      className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                        errors.description ? 'border-red-300' : 'border-gray-300'
                      }`}
                    />
                    {errors.description && (
                      <p className="text-red-600 text-sm mt-1">{errors.description}</p>
                    )}
                  </div>

                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Input
                      label="강의 가격"
                      type="number"
                      name="price"
                      value={formData.price.toString()}
                      onChange={handleInputChange}
                      error={errors.price}
                      min={0}
                      fullWidth
                    />

                    <Input
                      label="강의 시간 (분)"
                      type="number"
                      name="duration"
                      value={formData.duration.toString()}
                      onChange={handleInputChange}
                      min={0}
                      fullWidth
                    />
                  </div>

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
              </div>

              {/* 미디어 */}
              <div className="border-t border-gray-200 pt-8">
                <h2 className="text-xl font-bold text-gray-900 mb-6">미디어</h2>
                
                {/* 썸네일 */}
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
                          썸네일 변경
                        </Button>
                      </label>
                    </div>
                  </div>
                </div>

                {/* 동영상 URL */}
                <Input
                  label="동영상 URL"
                  name="video_url"
                  value={videoUrl}
                  onChange={(e) => setVideoUrl(e.target.value)}
                  placeholder="https://example.com/video.mp4"
                  fullWidth
                />
              </div>

              {/* 강의 내용 */}
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
                    rows={15}
                    className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                      errors.content ? 'border-red-300' : 'border-gray-300'
                    }`}
                  />
                  {errors.content && (
                    <p className="text-red-600 text-sm mt-1">{errors.content}</p>
                  )}
                </div>
              </div>

              {/* 버튼 */}
              <div className="flex justify-between pt-6 border-t border-gray-200">
                <Button
                  type="button"
                  variant="ghost"
                  onClick={() => navigate('/admin/lectures')}
                >
                  취소
                </Button>

                <Button
                  type="submit"
                  loading={submitting || uploadingThumbnail || uploadingVideo}
                  className="bg-blue-600 hover:bg-blue-700"
                >
                  수정 저장
                </Button>
              </div>
            </div>
          </form>
        )}

        {/* 수강생 관리 탭 */}
        {activeTab === 'students' && (
          <div className="bg-white rounded-xl shadow-sm p-6">
            <h2 className="text-xl font-bold text-gray-900 mb-6">
              수강생 관리 ({enrollments.length}명)
            </h2>
            
            {enrollments.length > 0 ? (
              <div className="space-y-4">
                {enrollments.map((enrollment) => (
                  <div key={enrollment.id} className="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                    <div className="flex items-center space-x-4">
                      <div className="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                        {enrollment.user.profile_image ? (
                          <img
                            src={enrollment.user.profile_image}
                            alt={enrollment.user.nickname}
                            className="w-12 h-12 rounded-full object-cover"
                          />
                        ) : (
                          <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                          </svg>
                        )}
                      </div>
                      <div>
                        <h3 className="font-medium text-gray-900">
                          {enrollment.user.nickname}
                        </h3>
                        <p className="text-sm text-gray-600">
                          등록일: {formatDate(enrollment.created_at)}
                        </p>
                        {enrollment.completed_at && (
                          <p className="text-sm text-green-600">
                            완료일: {formatDate(enrollment.completed_at)}
                          </p>
                        )}
                      </div>
                    </div>
                    <div className="text-right">
                      <div className="text-sm font-medium text-gray-900">
                        진행률: {enrollment.progress}%
                      </div>
                      <div className="w-32 bg-gray-200 rounded-full h-2 mt-1">
                        <div
                          className="bg-blue-600 h-2 rounded-full"
                          style={{ width: `${enrollment.progress}%` }}
                        ></div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="text-center py-8">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  아직 수강생이 없습니다
                </h3>
                <p className="text-gray-600">
                  강의가 공개되면 수강생들이 등록할 수 있습니다.
                </p>
              </div>
            )}
          </div>
        )}

        {/* 통계 탭 */}
        {activeTab === 'stats' && (
          <div className="bg-white rounded-xl shadow-sm p-6">
            <h2 className="text-xl font-bold text-gray-900 mb-6">강의 통계</h2>
            
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div className="bg-blue-50 rounded-lg p-6">
                <div className="flex items-center">
                  <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg className="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm text-blue-600">총 조회수</p>
                    <p className="text-2xl font-bold text-blue-900">{lecture.views.toLocaleString()}</p>
                  </div>
                </div>
              </div>

              <div className="bg-green-50 rounded-lg p-6">
                <div className="flex items-center">
                  <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg className="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                    </svg>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm text-green-600">수강생 수</p>
                    <p className="text-2xl font-bold text-green-900">{lecture.enrollment_count.toLocaleString()}</p>
                  </div>
                </div>
              </div>

              <div className="bg-purple-50 rounded-lg p-6">
                <div className="flex items-center">
                  <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg className="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm text-purple-600">좋아요 수</p>
                    <p className="text-2xl font-bold text-purple-900">{lecture.likes_count.toLocaleString()}</p>
                  </div>
                </div>
              </div>

              <div className="bg-orange-50 rounded-lg p-6">
                <div className="flex items-center">
                  <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg className="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                  </div>
                  <div className="ml-4">
                    <p className="text-sm text-orange-600">총 수익</p>
                    <p className="text-2xl font-bold text-orange-900">
                      {(lecture.price * lecture.enrollment_count).toLocaleString()}원
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div className="mt-8 p-6 bg-gray-50 rounded-lg">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">강의 정보</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                  <span className="text-gray-600">등록일:</span>
                  <span className="ml-2 font-medium">{formatDate(lecture.created_at)}</span>
                </div>
                <div>
                  <span className="text-gray-600">마지막 수정:</span>
                  <span className="ml-2 font-medium">{formatDate(lecture.updated_at)}</span>
                </div>
                <div>
                  <span className="text-gray-600">강의 상태:</span>
                  <span className={`ml-2 px-2 py-1 rounded text-xs font-medium ${
                    lecture.status === 'ACTIVE' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-gray-100 text-gray-800'
                  }`}>
                    {lecture.status === 'ACTIVE' ? '활성' : '비활성'}
                  </span>
                </div>
                <div>
                  <span className="text-gray-600">강의 가격:</span>
                  <span className="ml-2 font-medium">
                    {lecture.price === 0 ? '무료' : `${lecture.price.toLocaleString()}원`}
                  </span>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default LectureEditPage;