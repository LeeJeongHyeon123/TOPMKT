import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { Lecture, User } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const LectureDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [lecture, setLecture] = useState<Lecture | null>(null);
  const [loading, setLoading] = useState(true);
  const [enrolling, setEnrolling] = useState(false);
  const [isLiked, setIsLiked] = useState(false);
  const [likesCount, setLikesCount] = useState(0);
  const [showFullDescription, setShowFullDescription] = useState(false);

  // 강의 상세 정보 조회
  useEffect(() => {
    const fetchLecture = async () => {
      if (!id) return;

      setLoading(true);
      try {
        const response = await request<Lecture>({
          url: `/lectures/${id}`,
          method: 'GET',
        });

        if (response.success && response.data) {
          setLecture(response.data);
          setIsLiked(response.data.is_liked || false);
          setLikesCount(response.data.likes_count);
        }
      } catch (err) {
        console.error('강의 조회 실패:', err);
        error('강의 정보를 불러올 수 없습니다.');
        navigate('/lectures');
      } finally {
        setLoading(false);
      }
    };

    fetchLecture();
  }, [id]);

  // 강의 등록
  const handleEnroll = async () => {
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login', { state: { from: location.pathname } });
      return;
    }

    if (!lecture) return;

    setEnrolling(true);
    try {
      const response = await request({
        url: `/lectures/${lecture.id}/enroll`,
        method: 'POST',
      });

      if (response.success) {
        success('강의 등록이 완료되었습니다!', '수강 시작');
        setLecture(prev => prev ? { ...prev, is_enrolled: true, enrollment_count: prev.enrollment_count + 1 } : null);
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '강의 등록에 실패했습니다.';
      error(errorMessage);
    } finally {
      setEnrolling(false);
    }
  };

  // 좋아요 토글
  const handleLike = async () => {
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!lecture) return;

    try {
      const response = await request({
        url: `/lectures/${lecture.id}/like`,
        method: isLiked ? 'DELETE' : 'POST',
      });

      if (response.success) {
        setIsLiked(!isLiked);
        setLikesCount(prev => isLiked ? prev - 1 : prev + 1);
      }
    } catch (err) {
      console.error('좋아요 처리 실패:', err);
    }
  };

  // 가격 포맷팅
  const formatPrice = (price: number) => {
    if (price === 0) return '무료';
    return `${price.toLocaleString()}원`;
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
          <Link to="/lectures">
            <Button>강의 목록으로 돌아가기</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 강의 영상/썸네일 섹션 */}
      <div className="bg-black">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="aspect-video bg-gray-900 relative rounded-b-xl overflow-hidden">
            {lecture.video_url ? (
              <iframe
                src={lecture.video_url}
                title={lecture.title}
                className="w-full h-full"
                allowFullScreen
              />
            ) : lecture.thumbnail ? (
              <div className="relative w-full h-full">
                <img
                  src={lecture.thumbnail}
                  alt={lecture.title}
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                  <div className="text-center text-white">
                    <svg className="w-20 h-20 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clipRule="evenodd" />
                    </svg>
                    <p className="text-lg">미리보기 준비 중</p>
                  </div>
                </div>
              </div>
            ) : (
              <div className="flex items-center justify-center h-full text-white">
                <div className="text-center">
                  <svg className="w-20 h-20 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                  <p className="text-lg">강의 영상 준비 중</p>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* 메인 컨텐츠 */}
          <div className="lg:col-span-2">
            {/* 강의 기본 정보 */}
            <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
              <div className="mb-4">
                <h1 className="text-3xl font-bold text-gray-900 mb-2">
                  {lecture.title}
                </h1>
                <p className="text-lg text-gray-600">
                  {lecture.description}
                </p>
              </div>

              {/* 강사 정보 */}
              <div className="flex items-center mb-6">
                <div className="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                  {lecture.instructor.profile_image ? (
                    <img
                      src={lecture.instructor.profile_image}
                      alt={lecture.instructor.nickname}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                  ) : (
                    <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  )}
                </div>
                <div>
                  <h3 className="font-semibold text-gray-900">
                    {lecture.instructor.nickname}
                  </h3>
                  <p className="text-sm text-gray-600">강사</p>
                </div>
              </div>

              {/* 통계 정보 */}
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <div className="text-2xl font-bold text-blue-600">
                    {lecture.views.toLocaleString()}
                  </div>
                  <div className="text-sm text-gray-600">조회수</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <div className="text-2xl font-bold text-green-600">
                    {lecture.enrollment_count.toLocaleString()}
                  </div>
                  <div className="text-sm text-gray-600">수강생</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <div className="text-2xl font-bold text-red-600">
                    {likesCount.toLocaleString()}
                  </div>
                  <div className="text-sm text-gray-600">좋아요</div>
                </div>
                <div className="text-center p-3 bg-gray-50 rounded-lg">
                  <div className="text-2xl font-bold text-purple-600">
                    {lecture.duration ? `${Math.floor(lecture.duration / 60)}분` : '-'}
                  </div>
                  <div className="text-sm text-gray-600">강의 시간</div>
                </div>
              </div>

              {/* 액션 버튼 */}
              <div className="flex space-x-3">
                <Button
                  onClick={handleLike}
                  variant={isLiked ? 'primary' : 'outline'}
                  leftIcon={
                    <svg className="w-5 h-5" fill={isLiked ? 'currentColor' : 'none'} viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                  }
                >
                  {isLiked ? '좋아요 취소' : '좋아요'}
                </Button>
                
                <Button
                  variant="outline"
                  leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                    </svg>
                  }
                >
                  공유하기
                </Button>
              </div>
            </div>

            {/* 강의 상세 내용 */}
            <div className="bg-white rounded-xl shadow-sm p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-4">
                강의 소개
              </h2>
              <div className="prose max-w-none">
                <div 
                  className={`text-gray-700 leading-relaxed ${
                    !showFullDescription ? 'line-clamp-6' : ''
                  }`}
                  dangerouslySetInnerHTML={{ __html: lecture.content }}
                />
                {lecture.content.length > 500 && (
                  <button
                    onClick={() => setShowFullDescription(!showFullDescription)}
                    className="mt-4 text-blue-600 hover:text-blue-700 font-medium"
                  >
                    {showFullDescription ? '접기' : '더 보기'}
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* 사이드바 */}
          <div className="lg:col-span-1">
            {/* 수강 신청 카드 */}
            <div className="bg-white rounded-xl shadow-sm p-6 sticky top-8">
              <div className="text-center mb-6">
                <div className="text-3xl font-bold text-gray-900 mb-2">
                  {formatPrice(lecture.price)}
                </div>
                {lecture.price > 0 && (
                  <p className="text-sm text-gray-600">
                    일시불 결제
                  </p>
                )}
              </div>

              {lecture.status === 'ACTIVE' ? (
                <>
                  {lecture.is_enrolled ? (
                    <div className="space-y-3">
                      <Button
                        fullWidth
                        size="lg"
                        className="bg-green-600 hover:bg-green-700"
                        disabled
                      >
                        ✓ 수강 중
                      </Button>
                      <Button
                        fullWidth
                        variant="outline"
                        onClick={() => navigate(`/lectures/${lecture.id}/learn`)}
                      >
                        강의 시청하기
                      </Button>
                    </div>
                  ) : (
                    <Button
                      onClick={handleEnroll}
                      loading={enrolling}
                      fullWidth
                      size="lg"
                      className="bg-blue-600 hover:bg-blue-700"
                    >
                      {lecture.price === 0 ? '무료 수강 신청' : '수강 신청'}
                    </Button>
                  )}
                </>
              ) : (
                <Button
                  fullWidth
                  size="lg"
                  disabled
                  className="bg-gray-400"
                >
                  수강 불가
                </Button>
              )}

              {/* 강의 정보 */}
              <div className="mt-6 pt-6 border-t border-gray-200">
                <div className="space-y-3">
                  <div className="flex justify-between">
                    <span className="text-gray-600">등록일</span>
                    <span className="font-medium">{formatDate(lecture.created_at)}</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">마지막 업데이트</span>
                    <span className="font-medium">{formatDate(lecture.updated_at)}</span>
                  </div>
                  {lecture.duration && (
                    <div className="flex justify-between">
                      <span className="text-gray-600">총 강의 시간</span>
                      <span className="font-medium">
                        {Math.floor(lecture.duration / 60)}분
                      </span>
                    </div>
                  )}
                  <div className="flex justify-between">
                    <span className="text-gray-600">수강생 수</span>
                    <span className="font-medium">
                      {lecture.enrollment_count.toLocaleString()}명
                    </span>
                  </div>
                </div>
              </div>

              {/* 강사 정보 */}
              <div className="mt-6 pt-6 border-t border-gray-200">
                <h3 className="font-semibold text-gray-900 mb-3">강사 정보</h3>
                <div className="flex items-center">
                  <div className="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                    {lecture.instructor.profile_image ? (
                      <img
                        src={lecture.instructor.profile_image}
                        alt={lecture.instructor.nickname}
                        className="w-10 h-10 rounded-full object-cover"
                      />
                    ) : (
                      <svg className="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    )}
                  </div>
                  <div>
                    <div className="font-medium text-gray-900">
                      {lecture.instructor.nickname}
                    </div>
                    {lecture.instructor.introduction && (
                      <div className="text-sm text-gray-600 line-clamp-2">
                        {lecture.instructor.introduction}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LectureDetailPage;