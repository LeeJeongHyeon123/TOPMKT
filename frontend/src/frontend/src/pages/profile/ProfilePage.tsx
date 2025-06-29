import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import Button from '../../components/common/Button';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import { useToast } from '../../hooks/useToast';
import { createSafeHtml } from '../../utils/sanitize';

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
  last_login?: string;
  created_at: string;
  updated_at: string;
}

interface ProfileStats {
  post_count: number;
  comment_count: number;
  like_count: number;
  join_days: number;
}

interface PostItem {
  id: number;
  title: string;
  content: string;
  views: number;
  likes_count: number;
  comments_count: number;
  created_at: string;
}

interface CommentItem {
  id: number;
  content: string;
  post_title: string;
  post_id: number;
  created_at: string;
}

const ProfilePage: React.FC = () => {
  const { nickname } = useParams<{ nickname?: string }>();
  const { user: currentUser, isAuthenticated } = useAuth();
  const { error: showError } = useToast();

  const [profileUser, setProfileUser] = useState<ProfileUser | null>(null);
  const [stats, setStats] = useState<ProfileStats | null>(null);
  const [recentPosts, setRecentPosts] = useState<PostItem[]>([]);
  const [recentComments, setRecentComments] = useState<CommentItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<'info' | 'posts' | 'comments'>('info');

  // 내 프로필인지 확인 (nickname이 없거나 현재 사용자와 동일한 경우)
  const isOwnProfile = !nickname || (currentUser && currentUser.nickname === nickname);

  // 프로필 데이터 조회
  useEffect(() => {
    const fetchProfile = async () => {
      setLoading(true);
      try {
        let targetNickname = nickname;
        
        // nickname이 없으면 현재 로그인한 사용자의 프로필
        if (!nickname && currentUser) {
          targetNickname = currentUser.nickname;
        }

        if (!targetNickname && !isAuthenticated) {
          showError('로그인이 필요합니다.', 'error');
          return;
        }

        // 프로필 데이터 조회 (PHP API 엔드포인트 사용)
        const url = targetNickname ? `/profile/${encodeURIComponent(targetNickname)}` : '/profile';
        const response = await fetch(url, {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'Authorization': isAuthenticated ? `Bearer ${localStorage.getItem('token')}` : ''
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        
        if (data.user) {
          setProfileUser(data.user);
        }
        
        if (data.stats) {
          setStats(data.stats);
        }
        
        if (data.recent_posts) {
          setRecentPosts(data.recent_posts);
        }
        
        if (data.recent_comments) {
          setRecentComments(data.recent_comments);
        }
      } catch (error) {
        console.error('프로필 조회 실패:', error);
        showError('프로필을 불러오는데 실패했습니다.', 'error');
      } finally {
        setLoading(false);
      }
    };

    fetchProfile();
  }, [nickname, currentUser, isAuthenticated]);

  // 프로필 이미지 경로 설정
  const getProfileImageUrl = (user: ProfileUser) => {
    if (user.profile_image_profile) {
      return user.profile_image_profile;
    }
    if (user.profile_image_thumb) {
      return user.profile_image_thumb;
    }
    return '/assets/images/default-avatar.png';
  };
  
  // 소셜 링크 렌더링
  const renderSocialLinks = (socialLinks: ProfileUser['social_links']) => {
    if (!socialLinks) return null;
    
    const socialPlatforms = [
      { key: 'website', label: '웹사이트', icon: '🌐', color: '#6366f1' },
      { key: 'kakao', label: '카카오톡', icon: '💬', color: '#FEE500' },
      { key: 'instagram', label: '인스타그램', icon: '📷', color: '#E4405F' },
      { key: 'facebook', label: '페이스북', icon: '👥', color: '#1877F2' },
      { key: 'youtube', label: '유튜브', icon: '📺', color: '#FF0000' },
      { key: 'tiktok', label: '틱톡', icon: '🎵', color: '#000000' }
    ];
    
    const activePlatforms = socialPlatforms.filter(platform => 
      socialLinks[platform.key as keyof typeof socialLinks]
    );
    
    if (activePlatforms.length === 0) return null;
    
    return (
      <div className="mt-6">
        <h4 className="text-sm font-medium text-gray-700 mb-3">소셜 미디어</h4>
        <div className="flex flex-wrap gap-2">
          {activePlatforms.map(platform => (
            <a
              key={platform.key}
              href={socialLinks[platform.key as keyof typeof socialLinks]}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition-all duration-200 hover:shadow-md"
              style={{
                backgroundColor: platform.color,
                color: platform.key === 'kakao' ? '#000' : '#fff'
              }}
            >
              <span className="mr-1">{platform.icon}</span>
              {platform.label}
            </a>
          ))}
        </div>
      </div>
    );
  };
  
  // 나이 계산
  const calculateAge = (birthDate: string) => {
    const birth = new Date(birthDate);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const monthDiff = today.getMonth() - birth.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
      age--;
    }
    
    return age;
  };
  
  // 성별 표시
  const getGenderLabel = (gender?: string) => {
    switch (gender) {
      case 'M': return '남성';
      case 'F': return '여성';
      case 'OTHER': return '기타';
      default: return null;
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
  
  // 상대적 시간 표시
  const getRelativeTime = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now.getTime() - date.getTime();
    const diffMins = Math.floor(diffMs / (1000 * 60));
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    
    if (diffMins < 1) return '방금 전';
    if (diffMins < 60) return `${diffMins}분 전`;
    if (diffHours < 24) return `${diffHours}시간 전`;
    if (diffDays < 30) return `${diffDays}일 전`;
    
    return formatDate(dateString);
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
          <p className="text-gray-600 mb-6">요청하신 프로필이 존재하지 않거나 접근할 수 없습니다.</p>
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
      <div className="bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900 text-white relative overflow-hidden">
        {/* 배경 패턴 */}
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-0 right-0 w-96 h-96 bg-white rounded-full transform translate-x-32 -translate-y-32"></div>
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full transform -translate-x-16 translate-y-16"></div>
        </div>
        
        <div className="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
          <div className="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
            {/* 프로필 이미지 */}
            <div className="relative">
              <div className="w-32 h-32 rounded-full overflow-hidden border-4 border-white shadow-2xl">
                <img
                  src={getProfileImageUrl(profileUser)}
                  alt={profileUser.nickname}
                  className="w-full h-full object-cover"
                  onError={(e) => {
                    const target = e.target as HTMLImageElement;
                    target.src = '/assets/images/default-avatar.png';
                  }}
                />
              </div>
              {/* 온라인 상태 표시 (향후 구현) */}
              <div className="absolute bottom-2 right-2 w-6 h-6 bg-green-400 border-2 border-white rounded-full"></div>
            </div>

            {/* 프로필 정보 */}
            <div className="flex-1 text-center md:text-left">
              <div className="flex flex-col md:flex-row md:items-center md:space-x-4 mb-4">
                <h1 className="text-3xl md:text-4xl font-bold">
                  {profileUser.nickname}
                </h1>
                <div className="flex justify-center md:justify-start items-center space-x-2 mt-2 md:mt-0">
                  <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white bg-opacity-20 text-white">
                    {profileUser.role === 'ROLE_ADMIN' ? '👑 관리자' : 
                     profileUser.role === 'ROLE_CORP' ? '🏢 기업회원' : '👤 일반회원'}
                  </span>
                  {profileUser.phone_verified && (
                    <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-500 bg-opacity-20 text-green-100">
                      ✓ 인증
                    </span>
                  )}
                </div>
              </div>
              
              {/* 기본 정보 */}
              {(profileUser.bio || profileUser.birth_date || profileUser.gender) && (
                <div className="mb-4 space-y-1">
                  {profileUser.bio && (
                    <div className="text-lg text-blue-100 leading-relaxed" 
                         dangerouslySetInnerHTML={createSafeHtml(profileUser.bio)} />
                  )}
                  <div className="flex flex-wrap justify-center md:justify-start items-center gap-4 text-sm text-blue-100">
                    {profileUser.birth_date && (
                      <span>🎂 {calculateAge(profileUser.birth_date)}세</span>
                    )}
                    {getGenderLabel(profileUser.gender) && (
                      <span>👤 {getGenderLabel(profileUser.gender)}</span>
                    )}
                    <span>📅 {formatDate(profileUser.created_at)} 가입</span>
                  </div>
                </div>
              )}

              {/* 액션 버튼 */}
              <div className="flex justify-center md:justify-start space-x-3">
                {isOwnProfile ? (
                  <Link to="/profile/edit">
                    <Button
                      variant="outline"
                      className="border-white text-white hover:bg-white hover:text-blue-800 transition-all duration-200"
                    >
                      ✏️ 프로필 수정
                    </Button>
                  </Link>
                ) : (
                  <>
                    <Button
                      variant="outline"
                      className="border-white text-white hover:bg-white hover:text-blue-800 transition-all duration-200"
                    >
                      💌 메시지 보내기
                    </Button>
                    <Button
                      variant="outline"
                      className="border-white text-white hover:bg-white hover:text-blue-800 transition-all duration-200"
                    >
                      👥 팔로우
                    </Button>
                  </>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* 메인 콘텐츠 */}
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* 메인 콘텐츠 */}
          <div className="lg:col-span-2 space-y-6">
            {/* 탭 메뉴 */}
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100">
              <div className="p-6 border-b border-gray-100">
                <nav className="flex space-x-8">
                  {[
                    { key: 'info', label: '기본 정보', icon: '👤' },
                    { key: 'posts', label: `게시글 (${stats?.post_count || 0})`, icon: '📝' },
                    { key: 'comments', label: `댓글 (${stats?.comment_count || 0})`, icon: '💬' },
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
              <div className="p-6">

                {/* 기본 정보 탭 */}
                {activeTab === 'info' && (
                  <div className="space-y-6">
                    {/* 자기소개 */}
                    {profileUser.bio && (
                      <div>
                        <h3 className="text-lg font-semibold text-gray-900 mb-3">자기소개</h3>
                        <div className="prose prose-sm max-w-none text-gray-700 leading-relaxed"
                             dangerouslySetInnerHTML={createSafeHtml(profileUser.bio)} />
                      </div>
                    )}
                    
                    {/* 소셜 링크 */}
                    {renderSocialLinks(profileUser.social_links)}
                    
                    {!profileUser.bio && !profileUser.social_links && (
                      <div className="text-center py-8">
                        <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                          <span className="text-2xl">📝</span>
                        </div>
                        <p className="text-gray-500 mb-4">아직 작성된 정보가 없습니다.</p>
                        {isOwnProfile && (
                          <Link to="/profile/edit">
                            <Button>프로필 작성하기</Button>
                          </Link>
                        )}
                      </div>
                    )}
                  </div>
                )}

                {/* 게시글 탭 */}
                {activeTab === 'posts' && (
                  <div>
                    {recentPosts.length > 0 ? (
                      <div className="space-y-4">
                        {recentPosts.map((post) => (
                          <Link
                            key={post.id}
                            to={`/community/${post.id}`}
                            className="block p-4 border border-gray-200 rounded-xl hover:border-purple-200 hover:shadow-sm transition-all duration-200 group"
                          >
                            <h3 className="font-semibold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors">
                              {post.title}
                            </h3>
                            <p className="text-gray-600 text-sm mb-3 line-clamp-2">
                              {post.content.replace(/<[^>]*>/g, '').substring(0, 150)}...
                            </p>
                            <div className="flex items-center justify-between text-xs text-gray-500">
                              <span>{getRelativeTime(post.created_at)}</span>
                              <div className="flex items-center space-x-4">
                                <span className="flex items-center">
                                  <span className="mr-1">👁️</span> {post.views.toLocaleString()}
                                </span>
                                <span className="flex items-center">
                                  <span className="mr-1">❤️</span> {post.likes_count.toLocaleString()}
                                </span>
                                <span className="flex items-center">
                                  <span className="mr-1">💬</span> {post.comments_count.toLocaleString()}
                                </span>
                              </div>
                            </div>
                          </Link>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                          <span className="text-2xl">📝</span>
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

                {/* 댓글 탭 */}
                {activeTab === 'comments' && (
                  <div>
                    {recentComments.length > 0 ? (
                      <div className="space-y-4">
                        {recentComments.map((comment) => (
                          <Link
                            key={comment.id}
                            to={`/community/${comment.post_id}`}
                            className="block p-4 border border-gray-200 rounded-xl hover:border-purple-200 hover:shadow-sm transition-all duration-200 group"
                          >
                            <div className="flex items-start justify-between mb-2">
                              <h4 className="font-medium text-gray-900 group-hover:text-purple-600 transition-colors text-sm">
                                {comment.post_title}
                              </h4>
                              <span className="text-xs text-gray-500 ml-2 flex-shrink-0">
                                {getRelativeTime(comment.created_at)}
                              </span>
                            </div>
                            <p className="text-gray-600 text-sm leading-relaxed">
                              {comment.content.replace(/<[^>]*>/g, '').substring(0, 200)}...
                            </p>
                          </Link>
                        ))}
                      </div>
                    ) : (
                      <div className="text-center py-8">
                        <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                          <span className="text-2xl">💬</span>
                        </div>
                        <h3 className="text-lg font-medium text-gray-900 mb-2">
                          아직 작성한 댓글이 없습니다
                        </h3>
                        {isOwnProfile && (
                          <Link to="/community">
                            <Button className="mt-4">커뮤니티 둘러보기</Button>
                          </Link>
                        )}
                      </div>
                    )}
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* 사이드바 */}
          <div className="space-y-6">
            {/* 활동 통계 */}
            {stats && (
              <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">활동 통계</h3>
                <div className="space-y-4">
                  <div className="flex items-center justify-between">
                    <span className="text-gray-600 text-sm flex items-center">
                      <span className="mr-2">📝</span>게시글
                    </span>
                    <span className="font-semibold text-purple-600">{stats.post_count.toLocaleString()}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-gray-600 text-sm flex items-center">
                      <span className="mr-2">💬</span>댓글
                    </span>
                    <span className="font-semibold text-blue-600">{stats.comment_count.toLocaleString()}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-gray-600 text-sm flex items-center">
                      <span className="mr-2">❤️</span>받은 좋아요
                    </span>
                    <span className="font-semibold text-red-500">{stats.like_count.toLocaleString()}</span>
                  </div>
                  <div className="flex items-center justify-between">
                    <span className="text-gray-600 text-sm flex items-center">
                      <span className="mr-2">📅</span>활동일
                    </span>
                    <span className="font-semibold text-green-600">{stats.join_days}일</span>
                  </div>
                </div>
              </div>
            )}

            {/* 계정 정보 */}
            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
              <h3 className="text-lg font-semibold text-gray-900 mb-4">계정 정보</h3>
              <div className="space-y-3 text-sm">
                <div className="flex justify-between items-center">
                  <span className="text-gray-600">회원 등급</span>
                  <span className="font-medium">
                    {profileUser.role === 'ROLE_ADMIN' ? '👑 관리자' : 
                     profileUser.role === 'ROLE_CORP' ? '🏢 기업회원' : '👤 일반회원'}
                  </span>
                </div>
                <div className="flex justify-between items-center">
                  <span className="text-gray-600">가입일</span>
                  <span className="font-medium">{formatDate(profileUser.created_at)}</span>
                </div>
                {profileUser.last_login && (
                  <div className="flex justify-between items-center">
                    <span className="text-gray-600">마지막 접속</span>
                    <span className="font-medium">{getRelativeTime(profileUser.last_login)}</span>
                  </div>
                )}
                <div className="pt-2 border-t border-gray-100">
                  <div className="flex flex-wrap gap-2">
                    {profileUser.phone_verified && (
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        ✓ 휴대폰 인증
                      </span>
                    )}
                    {profileUser.email_verified && (
                      <span className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        ✓ 이메일 인증
                      </span>
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

export default ProfilePage;