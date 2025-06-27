import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Post, Lecture, LectureEnrollment, Notification } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const MyPage: React.FC = () => {
  const { user, isAuthenticated } = useAuth();
  const { request } = useApi();

  const [loading, setLoading] = useState(true);
  const [myPosts, setMyPosts] = useState<Post[]>([]);
  const [myLectures, setMyLectures] = useState<Lecture[]>([]);
  const [enrolledLectures, setEnrolledLectures] = useState<LectureEnrollment[]>([]);
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [stats, setStats] = useState({
    totalPosts: 0,
    totalLikes: 0,
    totalViews: 0,
    totalEnrollments: 0,
  });

  // 마이페이지 데이터 로드
  useEffect(() => {
    if (!isAuthenticated || !user) return;

    const fetchMyPageData = async () => {
      setLoading(true);
      try {
        // 내가 작성한 게시글
        const postsResponse = await request<{ data: Post[] }>({
          url: '/posts',
          method: 'GET',
          params: {
            user_id: user.id,
            per_page: 5,
          },
        });
        if (postsResponse.success) {
          setMyPosts(postsResponse.data?.data || []);
        }

        // 내가 등록한 강의 (강사인 경우)
        const lecturesResponse = await request<{ data: Lecture[] }>({
          url: '/lectures',
          method: 'GET',
          params: {
            instructor_id: user.id,
            per_page: 5,
          },
        });
        if (lecturesResponse.success) {
          setMyLectures(lecturesResponse.data?.data || []);
        }

        // 수강 중인 강의
        const enrollmentsResponse = await request<{ data: LectureEnrollment[] }>({
          url: '/my/enrollments',
          method: 'GET',
          params: {
            per_page: 5,
          },
        });
        if (enrollmentsResponse.success) {
          setEnrolledLectures(enrollmentsResponse.data?.data || []);
        }

        // 알림
        const notificationsResponse = await request<{ data: Notification[] }>({
          url: '/notifications',
          method: 'GET',
          params: {
            per_page: 5,
          },
        });
        if (notificationsResponse.success) {
          setNotifications(notificationsResponse.data?.data || []);
        }

        // 통계 데이터
        const statsResponse = await request<any>({
          url: '/my/stats',
          method: 'GET',
        });
        if (statsResponse.success) {
          setStats(statsResponse.data || stats);
        }
      } catch (error) {
        console.error('마이페이지 데이터 로드 실패:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchMyPageData();
  }, [isAuthenticated, user]);

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now.getTime() - date.getTime());
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    if (diffDays === 1) {
      return '오늘';
    } else if (diffDays === 2) {
      return '어제';
    } else if (diffDays <= 7) {
      return `${diffDays - 1}일 전`;
    } else {
      return date.toLocaleDateString('ko-KR');
    }
  };

  // 알림 타입별 아이콘
  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'POST_COMMENT':
        return (
          <svg className="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
        );
      case 'LECTURE_ENROLLMENT':
        return (
          <svg className="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
          </svg>
        );
      case 'EVENT_REGISTRATION':
        return (
          <svg className="w-5 h-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2" />
          </svg>
        );
      default:
        return (
          <svg className="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        );
    }
  };

  if (!isAuthenticated || !user) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">로그인이 필요합니다</h2>
          <Link to="/auth/login">
            <Button>로그인하기</Button>
          </Link>
        </div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="마이페이지를 불러오는 중..." />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 헤더 */}
      <div className="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="text-center">
            <h1 className="text-4xl font-bold mb-4">
              안녕하세요, {user.nickname}님! 👋
            </h1>
            <p className="text-xl text-indigo-100">
              오늘도 탑마케팅과 함께 성장해보세요.
            </p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 통계 카드 */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">작성한 게시글</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalPosts.toLocaleString()}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">받은 좋아요</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalLikes.toLocaleString()}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">총 조회수</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalViews.toLocaleString()}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">수강 중인 강의</p>
                <p className="text-2xl font-bold text-gray-900">{enrolledLectures.length.toLocaleString()}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* 왼쪽 컬럼 */}
          <div className="lg:col-span-2 space-y-6">
            {/* 최근 활동 */}
            <div className="bg-white rounded-xl shadow-sm p-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-xl font-bold text-gray-900">최근 활동</h2>
                <Link to="/profile" className="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  전체 보기
                </Link>
              </div>
              
              {myPosts.length > 0 ? (
                <div className="space-y-4">
                  {myPosts.slice(0, 3).map((post) => (
                    <Link
                      key={post.id}
                      to={`/community/${post.id}`}
                      className="block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all"
                    >
                      <h3 className="font-medium text-gray-900 mb-2 hover:text-blue-600">
                        {post.title}
                      </h3>
                      <div className="flex items-center justify-between text-sm text-gray-500">
                        <span>{formatDate(post.created_at)}</span>
                        <div className="flex space-x-3">
                          <span>조회 {post.views}</span>
                          <span>좋아요 {post.likes_count}</span>
                          <span>댓글 {post.comments_count}</span>
                        </div>
                      </div>
                    </Link>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">
                    아직 작성한 게시글이 없습니다
                  </h3>
                  <p className="text-gray-600 mb-4">
                    커뮤니티에 첫 게시글을 작성해보세요!
                  </p>
                  <Link to="/community/write">
                    <Button>첫 게시글 작성하기</Button>
                  </Link>
                </div>
              )}
            </div>

            {/* 수강 중인 강의 */}
            <div className="bg-white rounded-xl shadow-sm p-6">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-xl font-bold text-gray-900">수강 중인 강의</h2>
                <Link to="/my/lectures" className="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  전체 보기
                </Link>
              </div>

              {enrolledLectures.length > 0 ? (
                <div className="space-y-4">
                  {enrolledLectures.slice(0, 3).map((enrollment) => (
                    <Link
                      key={enrollment.id}
                      to={`/lectures/${enrollment.lecture.id}`}
                      className="block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all"
                    >
                      <div className="flex items-center space-x-4">
                        <div className="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                          {enrollment.lecture.thumbnail ? (
                            <img
                              src={enrollment.lecture.thumbnail}
                              alt={enrollment.lecture.title}
                              className="w-16 h-16 rounded-lg object-cover"
                            />
                          ) : (
                            <svg className="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                          )}
                        </div>
                        <div className="flex-1">
                          <h3 className="font-medium text-gray-900 mb-1 hover:text-blue-600">
                            {enrollment.lecture.title}
                          </h3>
                          <p className="text-sm text-gray-600 mb-2">
                            {enrollment.lecture.instructor.nickname}
                          </p>
                          <div className="w-full bg-gray-200 rounded-full h-2">
                            <div
                              className="bg-blue-600 h-2 rounded-full"
                              style={{ width: `${enrollment.progress}%` }}
                            ></div>
                          </div>
                          <p className="text-xs text-gray-500 mt-1">
                            진행률 {enrollment.progress}%
                          </p>
                        </div>
                      </div>
                    </Link>
                  ))}
                </div>
              ) : (
                <div className="text-center py-8">
                  <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                  </div>
                  <h3 className="text-lg font-medium text-gray-900 mb-2">
                    아직 수강 중인 강의가 없습니다
                  </h3>
                  <p className="text-gray-600 mb-4">
                    다양한 강의를 둘러보고 학습을 시작해보세요!
                  </p>
                  <Link to="/lectures">
                    <Button>강의 둘러보기</Button>
                  </Link>
                </div>
              )}
            </div>
          </div>

          {/* 오른쪽 사이드바 */}
          <div className="space-y-6">
            {/* 빠른 액션 */}
            <div className="bg-white rounded-xl shadow-sm p-6">
              <h2 className="text-xl font-bold text-gray-900 mb-4">빠른 액션</h2>
              <div className="space-y-3">
                <Link to="/community/write">
                  <Button fullWidth variant="outline" leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                    </svg>
                  }>
                    새 게시글 작성
                  </Button>
                </Link>
                <Link to="/lectures">
                  <Button fullWidth variant="outline" leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  }>
                    강의 찾아보기
                  </Button>
                </Link>
                <Link to="/profile/edit">
                  <Button fullWidth variant="outline" leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  }>
                    프로필 수정
                  </Button>
                </Link>
              </div>
            </div>

            {/* 알림 */}
            <div className="bg-white rounded-xl shadow-sm p-6">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-xl font-bold text-gray-900">최근 알림</h2>
                <Link to="/notifications" className="text-blue-600 hover:text-blue-700 text-sm font-medium">
                  전체 보기
                </Link>
              </div>

              {notifications.length > 0 ? (
                <div className="space-y-3">
                  {notifications.slice(0, 5).map((notification) => (
                    <div
                      key={notification.id}
                      className={`p-3 rounded-lg transition-colors ${
                        notification.read_at ? 'bg-gray-50' : 'bg-blue-50 border-l-4 border-blue-400'
                      }`}
                    >
                      <div className="flex items-start space-x-3">
                        <div className="flex-shrink-0">
                          {getNotificationIcon(notification.type)}
                        </div>
                        <div className="flex-1 min-w-0">
                          <p className="text-sm font-medium text-gray-900">
                            {notification.title}
                          </p>
                          <p className="text-xs text-gray-600 mt-1">
                            {notification.message}
                          </p>
                          <p className="text-xs text-gray-500 mt-1">
                            {formatDate(notification.created_at)}
                          </p>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="text-center py-6">
                  <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-5 5v-5z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <p className="text-sm text-gray-500">새로운 알림이 없습니다</p>
                </div>
              )}
            </div>

            {/* 추천 강의 */}
            <div className="bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl p-6 text-white">
              <h2 className="text-xl font-bold mb-3">추천 강의</h2>
              <p className="text-purple-100 mb-4">
                당신에게 맞는 강의를 찾아보세요
              </p>
              <Link to="/lectures">
                <Button
                  variant="outline"
                  className="border-white text-white hover:bg-white hover:text-purple-600"
                  fullWidth
                >
                  강의 둘러보기
                </Button>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default MyPage;