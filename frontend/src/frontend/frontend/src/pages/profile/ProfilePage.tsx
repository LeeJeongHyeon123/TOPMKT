import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { User, Post, Lecture } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const ProfilePage: React.FC = () => {
  const { userId } = useParams<{ userId?: string }>();
  const { user: currentUser, isAuthenticated } = useAuth();
  const { request } = useApi();

  const [profileUser, setProfileUser] = useState<User | null>(null);
  const [userPosts, setUserPosts] = useState<Post[]>([]);
  const [userLectures, setUserLectures] = useState<Lecture[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'posts' | 'lectures' | 'about'>('posts');
  const [isFollowing, setIsFollowing] = useState(false);

  const isOwnProfile = !userId || (currentUser && currentUser.id.toString() === userId);

  // 프로필 데이터 조회
  useEffect(() => {
    const fetchProfile = async () => {
      setLoading(true);
      try {
        let targetUserId = userId;
        
        // userId가 없으면 현재 로그인한 사용자의 프로필
        if (!userId && currentUser) {
          targetUserId = currentUser.id.toString();
        }

        if (targetUserId) {
          // 사용자 정보 조회
          const userResponse = await request<User>({
            url: `/users/${targetUserId}`,
            method: 'GET',
          });

          if (userResponse.success && userResponse.data) {
            setProfileUser(userResponse.data);
            setIsFollowing(userResponse.data.is_following || false);
          }

          // 사용자의 게시글 조회
          const postsResponse = await request<{ data: Post[] }>({
            url: '/posts',
            method: 'GET',
            params: {
              user_id: targetUserId,
              status: 'PUBLISHED',
              per_page: 10,
            },
          });

          if (postsResponse.success && postsResponse.data) {
            setUserPosts(postsResponse.data.data || []);
          }

          // 사용자의 강의 조회 (강사인 경우)
          const lecturesResponse = await request<{ data: Lecture[] }>({
            url: '/lectures',
            method: 'GET',
            params: {
              instructor_id: targetUserId,
              status: 'ACTIVE',
              per_page: 10,
            },
          });

          if (lecturesResponse.success && lecturesResponse.data) {
            setUserLectures(lecturesResponse.data.data || []);
          }
        }
      } catch (error) {
        console.error('프로필 조회 실패:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchProfile();
  }, [userId, currentUser]);

  // 팔로우/언팔로우
  const handleFollow = async () => {
    if (!isAuthenticated || !profileUser) return;

    try {
      const response = await request({
        url: `/users/${profileUser.id}/follow`,
        method: isFollowing ? 'DELETE' : 'POST',
      });

      if (response.success) {
        setIsFollowing(!isFollowing);
      }
    } catch (error) {
      console.error('팔로우 처리 실패:', error);
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
        <LoadingSpinner size="lg" message="프로필을 불러오는 중..." />
      </div>
    );
  }

  if (!profileUser) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">사용자를 찾을 수 없습니다</h2>
          <Link to="/">
            <Button>홈으로 돌아가기</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 프로필 헤더 */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="flex flex-col md:flex-row items-center md:items-end space-y-4 md:space-y-0 md:space-x-6">
            {/* 프로필 이미지 */}
            <div className="w-32 h-32 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
              {profileUser.profile_image ? (
                <img
                  src={profileUser.profile_image}
                  alt={profileUser.nickname}
                  className="w-32 h-32 rounded-full object-cover border-4 border-white"
                />
              ) : (
                <svg className="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              )}
            </div>

            {/* 프로필 정보 */}
            <div className="flex-1 text-center md:text-left">
              <h1 className="text-4xl font-bold mb-2">
                {profileUser.nickname}
              </h1>
              {profileUser.introduction && (
                <p className="text-xl text-blue-100 mb-4">
                  {profileUser.introduction}
                </p>
              )}
              <div className="flex flex-wrap justify-center md:justify-start items-center space-x-6 text-sm text-blue-100">
                <span className="flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2" />
                  </svg>
                  {profileUser.role === 'ROLE_ADMIN' ? '관리자' : 
                   profileUser.role === 'ROLE_CORP' ? '기업회원' : '일반회원'}
                </span>
                <span className="flex items-center">
                  <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2" />
                  </svg>
                  {formatDate(profileUser.created_at)} 가입
                </span>
                {profileUser.phone_verified && (
                  <span className="flex items-center">
                    <svg className="w-4 h-4 mr-1 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    휴대폰 인증
                  </span>
                )}
              </div>
            </div>

            {/* 액션 버튼 */}
            <div className="flex space-x-3">
              {isOwnProfile ? (
                <Link to="/profile/edit">
                  <Button
                    variant="outline"
                    className="border-white text-white hover:bg-white hover:text-blue-600"
                  >
                    프로필 수정
                  </Button>
                </Link>
              ) : (
                <>
                  {isAuthenticated && (
                    <Button
                      onClick={handleFollow}
                      variant={isFollowing ? 'outline' : 'primary'}
                      className={isFollowing 
                        ? 'border-white text-white hover:bg-white hover:text-blue-600'
                        : 'bg-white text-blue-600 hover:bg-gray-100'
                      }
                    >
                      {isFollowing ? '팔로우 취소' : '팔로우'}
                    </Button>
                  )}
                  <Button
                    variant="outline"
                    className="border-white text-white hover:bg-white hover:text-blue-600"
                  >
                    메시지 보내기
                  </Button>
                </>
              )}
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 탭 메뉴 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8">
              {[
                { key: 'posts', label: `게시글 (${userPosts.length})`, icon: 'document-text' },
                { key: 'lectures', label: `강의 (${userLectures.length})`, icon: 'academic-cap' },
                { key: 'about', label: '소개', icon: 'information-circle' },
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
                    {tab.icon === 'document-text' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    )}
                    {tab.icon === 'academic-cap' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
                    )}
                    {tab.icon === 'information-circle' && (
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    )}
                  </svg>
                  {tab.label}
                </button>
              ))}
            </nav>
          </div>
        </div>

        {/* 탭 콘텐츠 */}
        <div className="space-y-6">
          {/* 게시글 탭 */}
          {activeTab === 'posts' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                최근 게시글
              </h2>
              {userPosts.length > 0 ? (
                <div className="space-y-4">
                  {userPosts.map((post) => (
                    <Link
                      key={post.id}
                      to={`/community/${post.id}`}
                      className="block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all"
                    >
                      <h3 className="font-semibold text-gray-900 mb-2 hover:text-blue-600">
                        {post.title}
                      </h3>
                      <p className="text-gray-600 text-sm mb-3 line-clamp-2">
                        {post.content.replace(/<[^>]*>/g, '').substring(0, 150)}...
                      </p>
                      <div className="flex items-center justify-between text-xs text-gray-500">
                        <span>{formatDate(post.created_at)}</span>
                        <div className="flex space-x-4">
                          <span>조회 {post.views.toLocaleString()}</span>
                          <span>좋아요 {post.likes_count.toLocaleString()}</span>
                          <span>댓글 {post.comments_count.toLocaleString()}</span>
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
                  {isOwnProfile && (
                    <Link to="/community/write">
                      <Button className="mt-4">첫 게시글 작성하기</Button>
                    </Link>
                  )}
                </div>
              )}
            </div>
          )}

          {/* 강의 탭 */}
          {activeTab === 'lectures' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                강의 목록
              </h2>
              {userLectures.length > 0 ? (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {userLectures.map((lecture) => (
                    <Link
                      key={lecture.id}
                      to={`/lectures/${lecture.id}`}
                      className="group"
                    >
                      <div className="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all">
                        <div className="aspect-video bg-gradient-to-br from-blue-500 to-purple-600">
                          {lecture.thumbnail ? (
                            <img
                              src={lecture.thumbnail}
                              alt={lecture.title}
                              className="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            />
                          ) : (
                            <div className="flex items-center justify-center h-full">
                              <svg className="w-12 h-12 text-white opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                              </svg>
                            </div>
                          )}
                        </div>
                        <div className="p-4">
                          <h3 className="font-semibold text-gray-900 mb-2 group-hover:text-blue-600">
                            {lecture.title}
                          </h3>
                          <p className="text-sm text-gray-600 mb-3 line-clamp-2">
                            {lecture.description}
                          </p>
                          <div className="flex items-center justify-between text-xs text-gray-500">
                            <span>{lecture.price === 0 ? '무료' : `${lecture.price.toLocaleString()}원`}</span>
                            <span>수강생 {lecture.enrollment_count.toLocaleString()}명</span>
                          </div>
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
                    아직 등록한 강의가 없습니다
                  </h3>
                  {isOwnProfile && profileUser.role !== 'ROLE_USER' && (
                    <Button className="mt-4">강의 등록하기</Button>
                  )}
                </div>
              )}
            </div>
          )}

          {/* 소개 탭 */}
          {activeTab === 'about' && (
            <div className="bg-white rounded-xl shadow-sm p-6">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                소개
              </h2>
              <div className="space-y-6">
                {profileUser.introduction ? (
                  <div>
                    <h3 className="text-lg font-semibold text-gray-900 mb-3">자기소개</h3>
                    <p className="text-gray-700 leading-relaxed">
                      {profileUser.introduction}
                    </p>
                  </div>
                ) : (
                  <div className="text-center py-8">
                    <p className="text-gray-500">아직 자기소개가 없습니다.</p>
                    {isOwnProfile && (
                      <Link to="/profile/edit">
                        <Button className="mt-4">프로필 수정하기</Button>
                      </Link>
                    )}
                  </div>
                )}

                <div className="border-t border-gray-200 pt-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">활동 정보</h3>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div className="text-center p-4 bg-gray-50 rounded-lg">
                      <div className="text-2xl font-bold text-blue-600">
                        {userPosts.length}
                      </div>
                      <div className="text-sm text-gray-600">작성한 게시글</div>
                    </div>
                    <div className="text-center p-4 bg-gray-50 rounded-lg">
                      <div className="text-2xl font-bold text-green-600">
                        {userLectures.length}
                      </div>
                      <div className="text-sm text-gray-600">등록한 강의</div>
                    </div>
                    <div className="text-center p-4 bg-gray-50 rounded-lg">
                      <div className="text-2xl font-bold text-purple-600">
                        0
                      </div>
                      <div className="text-sm text-gray-600">팔로워</div>
                    </div>
                    <div className="text-center p-4 bg-gray-50 rounded-lg">
                      <div className="text-2xl font-bold text-orange-600">
                        0
                      </div>
                      <div className="text-sm text-gray-600">팔로잉</div>
                    </div>
                  </div>
                </div>

                <div className="border-t border-gray-200 pt-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">계정 정보</h3>
                  <div className="space-y-3 text-sm">
                    <div className="flex justify-between">
                      <span className="text-gray-600">회원 등급</span>
                      <span className="font-medium">
                        {profileUser.role === 'ROLE_ADMIN' ? '관리자' : 
                         profileUser.role === 'ROLE_CORP' ? '기업회원' : '일반회원'}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">가입일</span>
                      <span className="font-medium">{formatDate(profileUser.created_at)}</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">마지막 로그인</span>
                      <span className="font-medium">
                        {profileUser.last_login_at ? formatDate(profileUser.last_login_at) : '정보 없음'}
                      </span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-gray-600">인증 상태</span>
                      <div className="flex space-x-2">
                        {profileUser.phone_verified && (
                          <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            휴대폰 인증
                          </span>
                        )}
                        {profileUser.email_verified && (
                          <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            이메일 인증
                          </span>
                        )}
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

export default ProfilePage;