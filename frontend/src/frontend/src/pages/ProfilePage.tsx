import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';

// 타입 정의
interface UserProfile {
  id: number;
  nickname: string;
  email: string;
  bio?: string;
  profile_image_profile?: string;
  profile_image_thumb?: string;
  profile_image_original?: string;
  birth_date?: string;
  gender?: 'M' | 'F' | 'OTHER';
  created_at: string;
  last_login?: string;
  social_links?: Record<string, string>;
}

interface UserStats {
  post_count: number;
  comment_count: number;
  like_count: number;
  join_days: number;
}

interface Post {
  id: number;
  title: string;
  created_at: string;
  view_count: number;
  comment_count: number;
  like_count: number;
}

interface Comment {
  id: number;
  post_id: number;
  post_title: string;
  content: string;
  created_at: string;
}

const ProfilePage: React.FC = () => {
  const { nickname } = useParams<{ nickname: string }>();
  const [userProfile, setUserProfile] = useState<UserProfile | null>(null);
  const [stats, setStats] = useState<UserStats>({
    post_count: 0,
    comment_count: 0,
    like_count: 0,
    join_days: 0
  });
  const [recentPosts, setRecentPosts] = useState<Post[]>([]);
  const [recentComments, setRecentComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [showImageModal, setShowImageModal] = useState(false);
  const [modalImageSrc, setModalImageSrc] = useState('');
  const { user, isAuthenticated } = useAuth();

  // 현재 사용자의 프로필인지 확인
  const isOwnProfile = isAuthenticated && user?.nickname === nickname;

  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: `${userProfile?.nickname || nickname}님의 프로필`,
    description: `탑마케팅에서 ${userProfile?.nickname || nickname}님의 프로필을 확인해보세요`,
    ogType: 'profile'
  });

  // 임시 데이터
  const mockUserProfile: UserProfile = {
    id: 1,
    nickname: nickname || '사용자',
    email: 'user@example.com',
    bio: '안녕하세요! 네트워크 마케팅에 관심이 많은 마케터입니다. 항상 새로운 것을 배우고 성장하려고 노력하고 있습니다.',
    profile_image_profile: '/assets/images/default-avatar.png',
    created_at: '2023-06-15T10:30:00Z',
    last_login: '2024-01-26T15:30:00Z',
    birth_date: '1990-05-15',
    gender: 'M',
    social_links: {
      website: 'https://example.com',
      instagram: 'https://instagram.com/user',
      youtube: 'https://youtube.com/@user'
    }
  };

  const mockStats: UserStats = {
    post_count: 42,
    comment_count: 128,
    like_count: 256,
    join_days: 225
  };

  const mockRecentPosts: Post[] = [
    {
      id: 1,
      title: 'SNS 마케팅 성공 사례 공유',
      created_at: '2024-01-25T14:30:00Z',
      view_count: 156,
      comment_count: 23,
      like_count: 45
    },
    {
      id: 2,
      title: '네트워크 마케팅 팁 정리',
      created_at: '2024-01-23T10:15:00Z',
      view_count: 89,
      comment_count: 12,
      like_count: 34
    }
  ];

  const mockRecentComments: Comment[] = [
    {
      id: 1,
      post_id: 10,
      post_title: '마케팅 전략에 대한 고민',
      content: '정말 좋은 정보 감사합니다!',
      created_at: '2024-01-26T09:30:00Z'
    }
  ];

  useEffect(() => {
    const loadProfile = async () => {
      try {
        setLoading(true);
        // 실제 구현시 API 호출
        setUserProfile(mockUserProfile);
        setStats(mockStats);
        setRecentPosts(mockRecentPosts);
        setRecentComments(mockRecentComments);
      } catch (error) {
        console.error('Failed to load profile:', error);
      } finally {
        setLoading(false);
      }
    };

    if (nickname) {
      loadProfile();
    }
  }, [nickname]);

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

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  };

  // 마지막 로그인 시간 포맷팅
  const formatLastLogin = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / (1000 * 60));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (minutes < 1) return '방금 전';
    if (minutes < 60) return `${minutes}분 전`;
    if (hours < 24) return `${hours}시간 전`;
    if (days < 30) return `${days}일 전`;
    return date.toLocaleDateString('ko-KR');
  };

  // 이미지 모달 열기
  const openImageModal = (imageSrc: string) => {
    setModalImageSrc(imageSrc);
    setShowImageModal(true);
    document.body.style.overflow = 'hidden';
  };

  // 이미지 모달 닫기
  const closeImageModal = () => {
    setShowImageModal(false);
    setModalImageSrc('');
    document.body.style.overflow = '';
  };

  // 공유 기능
  const shareProfile = async () => {
    const profileTitle = `${userProfile?.nickname}님의 프로필 - 탑마케팅`;
    const profileUrl = `${window.location.origin}/profile/${userProfile?.nickname}`;
    const profileDescription = `탑마케팅에서 ${userProfile?.nickname}님의 프로필을 확인해보세요!`;

    if (navigator.share) {
      try {
        await navigator.share({
          title: profileTitle,
          text: profileDescription,
          url: profileUrl
        });
      } catch (error) {
        console.log('공유 실패:', error);
        copyToClipboard(profileUrl);
      }
    } else {
      copyToClipboard(profileUrl);
    }
  };

  // 클립보드 복사
  const copyToClipboard = async (text: string) => {
    try {
      await navigator.clipboard.writeText(text);
      alert('🔗 링크가 클립보드에 복사되었습니다!');
    } catch (error) {
      console.error('복사 실패:', error);
      alert('링크 복사에 실패했습니다.');
    }
  };

  // 소셜 링크 설정
  const socialConfigs = {
    website: { icon: 'fas fa-globe', name: '웹사이트', color: '#6366f1' },
    kakao: { icon: 'fas fa-comment', name: '카카오톡', color: '#FEE500' },
    instagram: { icon: 'fab fa-instagram', name: '인스타그램', color: '#E4405F' },
    facebook: { icon: 'fab fa-facebook', name: '페이스북', color: '#1877F2' },
    youtube: { icon: 'fab fa-youtube', name: '유튜브', color: '#FF0000' },
    tiktok: { icon: 'fab fa-tiktok', name: '틱톡', color: '#000000' }
  };

  if (loading) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="profile-page">
          <div className="profile-container">
            <div className="text-center py-12">
              <div className="text-gray-500">프로필을 불러오는 중...</div>
            </div>
          </div>
        </div>
      </>
    );
  }

  if (!userProfile) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="profile-page">
          <div className="profile-container">
            <div className="text-center py-12">
              <div className="text-gray-500">프로필을 찾을 수 없습니다.</div>
            </div>
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="profile-page">
        <div className="profile-container">
          {/* 프로필 헤더 */}
          <div className="profile-header-section">
            <div className="profile-main-info">
              <div className="profile-image-container">
                {userProfile.profile_image_profile ? (
                  <img
                    src={userProfile.profile_image_profile}
                    alt={`${userProfile.nickname}님의 프로필 이미지`}
                    className="profile-image"
                    onClick={() => openImageModal(userProfile.profile_image_original || userProfile.profile_image_profile!)}
                  />
                ) : (
                  <div className="profile-image-fallback">
                    {userProfile.nickname.charAt(0).toUpperCase()}
                  </div>
                )}
              </div>
              
              <div className="profile-details">
                <h1 className="profile-name">{userProfile.nickname}</h1>
                <div className="profile-meta">
                  <span>🗓️ 가입일: {formatDate(userProfile.created_at)}</span>
                  {stats.join_days > 0 && (
                    <span>⏰ 활동 {stats.join_days}일째</span>
                  )}
                  {!isOwnProfile && userProfile.last_login && (
                    <span>👀 최근 접속: {formatLastLogin(userProfile.last_login)}</span>
                  )}
                </div>
              </div>
              
              <div className="profile-actions">
                {isOwnProfile && (
                  <Link to="/profile/edit" className="btn btn-secondary">
                    ✏️ 프로필 편집
                  </Link>
                )}
                
                <button className="btn btn-secondary" onClick={shareProfile}>
                  🔗 공유하기
                </button>
              </div>
            </div>
          </div>
          
          {/* 프로필 콘텐츠 */}
          <div className="profile-content">
            {/* 메인 콘텐츠 */}
            <div className="profile-main">
              {/* 자기소개 */}
              <div className="profile-card">
                <h2 className="card-title">
                  <i className="fas fa-user"></i> 자기소개
                </h2>
                {userProfile.bio ? (
                  <div className="bio-content">{userProfile.bio}</div>
                ) : (
                  <div className="bio-empty">
                    {isOwnProfile ? '자기소개를 작성해보세요!' : '아직 자기소개가 없습니다.'}
                  </div>
                )}
              </div>
              
              {/* 최근 게시글 */}
              <div className="profile-card">
                <h2 className="card-title">
                  <i className="fas fa-newspaper"></i> 최근 게시글
                </h2>
                {recentPosts.length > 0 ? (
                  <ul className="activity-list">
                    {recentPosts.map(post => (
                      <li key={post.id} className="activity-item">
                        <div className="activity-title">
                          <Link to={`/community/posts/${post.id}`}>
                            {post.title}
                          </Link>
                        </div>
                        <div className="activity-meta">
                          <span>📅 {new Date(post.created_at).toLocaleDateString('ko-KR')}</span>
                          <span>👁️ {post.view_count.toLocaleString()}</span>
                          <span>💬 {post.comment_count.toLocaleString()}</span>
                          <span>❤️ {post.like_count.toLocaleString()}</span>
                        </div>
                      </li>
                    ))}
                  </ul>
                ) : (
                  <div className="activity-empty">
                    {isOwnProfile ? '아직 작성한 게시글이 없습니다. 첫 번째 글을 작성해보세요!' : '아직 작성한 게시글이 없습니다.'}
                  </div>
                )}
              </div>
              
              {/* 최근 댓글 */}
              <div className="profile-card">
                <h2 className="card-title">
                  <i className="fas fa-comments"></i> 최근 댓글
                </h2>
                {recentComments.length > 0 ? (
                  <ul className="activity-list">
                    {recentComments.map(comment => (
                      <li key={comment.id} className="activity-item">
                        <div className="activity-title">
                          <Link to={`/community/posts/${comment.post_id}#comment-${comment.id}`}>
                            {comment.post_title}
                          </Link>에 댓글
                        </div>
                        <div className="activity-meta">
                          <span>📅 {new Date(comment.created_at).toLocaleDateString('ko-KR')}</span>
                          <span>💬 {comment.content.substring(0, 50)}...</span>
                        </div>
                      </li>
                    ))}
                  </ul>
                ) : (
                  <div className="activity-empty">
                    {isOwnProfile ? '아직 작성한 댓글이 없습니다.' : '아직 작성한 댓글이 없습니다.'}
                  </div>
                )}
              </div>
            </div>
            
            {/* 사이드바 */}
            <div className="profile-sidebar">
              {/* 활동 통계 */}
              <div className="profile-card">
                <h2 className="card-title">
                  <i className="fas fa-chart-bar"></i> 활동 통계
                </h2>
                <div className="stats-grid">
                  <div className="stat-item">
                    <span className="stat-value">{stats.post_count.toLocaleString()}</span>
                    <span className="stat-label">게시글</span>
                  </div>
                  <div className="stat-item">
                    <span className="stat-value">{stats.comment_count.toLocaleString()}</span>
                    <span className="stat-label">댓글</span>
                  </div>
                  <div className="stat-item">
                    <span className="stat-value">{stats.like_count.toLocaleString()}</span>
                    <span className="stat-label">좋아요</span>
                  </div>
                  <div className="stat-item">
                    <span className="stat-value">{stats.join_days.toLocaleString()}</span>
                    <span className="stat-label">활동일</span>
                  </div>
                </div>
              </div>
              
              {/* 기본 정보 */}
              <div className="profile-card">
                <h2 className="card-title">
                  <i className="fas fa-info-circle"></i> 기본 정보
                </h2>
                <ul className="info-list">
                  {userProfile.email && (
                    <li className="info-item">
                      <i className="info-icon fas fa-envelope"></i>
                      <div className="info-content">
                        <div className="info-label">이메일</div>
                        <div className="info-value">{userProfile.email}</div>
                      </div>
                    </li>
                  )}
                  
                  {userProfile.birth_date && (
                    <li className="info-item">
                      <i className="info-icon fas fa-birthday-cake"></i>
                      <div className="info-content">
                        <div className="info-label">나이</div>
                        <div className="info-value">{calculateAge(userProfile.birth_date)}세</div>
                      </div>
                    </li>
                  )}
                  
                  {userProfile.gender && (
                    <li className="info-item">
                      <i className="info-icon fas fa-venus-mars"></i>
                      <div className="info-content">
                        <div className="info-label">성별</div>
                        <div className="info-value">
                          {userProfile.gender === 'M' ? '남성' : userProfile.gender === 'F' ? '여성' : '기타'}
                        </div>
                      </div>
                    </li>
                  )}
                </ul>
              </div>
              
              {/* 소셜 링크 */}
              {userProfile.social_links && Object.keys(userProfile.social_links).length > 0 ? (
                <div className="profile-card social-connections-card">
                  <h2 className="card-title">
                    <i className="fas fa-globe-americas"></i> 소셜 & 웹사이트
                  </h2>
                  <div className="social-section">
                    <div className="social-grid">
                      {Object.entries(userProfile.social_links).map(([platform, url]) => {
                        const config = socialConfigs[platform as keyof typeof socialConfigs];
                        if (!config || !url) return null;
                        
                        return (
                          <a
                            key={platform}
                            href={url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="social-connection-item"
                            style={{ '--social-color': config.color } as React.CSSProperties}
                            title={`${config.name}에서 만나요`}
                          >
                            <div className="social-connection-icon">
                              <i className={config.icon}></i>
                            </div>
                            <div className="social-connection-content">
                              <div className="social-connection-name">{config.name}</div>
                              <div className="social-connection-action">방문하기</div>
                            </div>
                            <div className="social-connection-arrow">
                              <i className="fas fa-chevron-right"></i>
                            </div>
                          </a>
                        );
                      })}
                    </div>
                  </div>
                </div>
              ) : isOwnProfile ? (
                <div className="profile-card social-connections-card empty-social">
                  <h2 className="card-title">
                    <i className="fas fa-globe-americas"></i> 소셜 & 웹사이트
                  </h2>
                  <div className="empty-social-content">
                    <div className="empty-social-icon">
                      <i className="fas fa-share-alt"></i>
                    </div>
                    <div className="empty-social-text">
                      <h3>소셜 프로필을 연결해보세요</h3>
                      <p>인스타그램, 유튜브, 개인 웹사이트 등을<br />프로필에 추가하여 더 많은 사람들과 소통하세요.</p>
                    </div>
                    <Link to="/profile/edit" className="btn-add-social">
                      <i className="fas fa-plus"></i> 소셜 링크 추가하기
                    </Link>
                  </div>
                </div>
              ) : null}
            </div>
          </div>
        </div>
        
        {/* 이미지 확대 모달 */}
        {showImageModal && (
          <div className="image-modal" onClick={closeImageModal}>
            <span className="modal-close" onClick={closeImageModal}>&times;</span>
            <div className="image-modal-content">
              <img src={modalImageSrc} alt="프로필 이미지" />
            </div>
          </div>
        )}
      </div>

      {/* 프로필 페이지 스타일 */}
      <style>{`
        .profile-page {
          background: #f8fafc;
          min-height: calc(100vh - 80px);
        }

        .profile-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 20px;
          min-height: calc(100vh - 200px);
        }

        .profile-header-section {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          border-radius: 16px;
          padding: 40px;
          margin-top: 60px;
          margin-bottom: 30px;
          position: relative;
          overflow: hidden;
        }

        .profile-header-section::before {
          content: '';
          position: absolute;
          top: -50%;
          right: -50%;
          width: 200%;
          height: 200%;
          background: url('/assets/images/favicon.svg') no-repeat center;
          background-size: 100px;
          opacity: 0.1;
          transform: rotate(15deg);
        }

        .profile-main-info {
          display: flex;
          align-items: center;
          gap: 30px;
          position: relative;
          z-index: 2;
        }

        .profile-image-container {
          position: relative;
          flex-shrink: 0;
        }

        .profile-image {
          width: 120px;
          height: 120px;
          border-radius: 50%;
          border: 4px solid rgba(255, 255, 255, 0.3);
          object-fit: cover;
          cursor: pointer;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-image:hover {
          transform: scale(1.05);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .profile-image-fallback {
          width: 120px;
          height: 120px;
          border-radius: 50%;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border: 4px solid rgba(255, 255, 255, 0.3);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 3rem;
          font-weight: bold;
          color: white;
          cursor: pointer;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-image-fallback:hover {
          transform: scale(1.05);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .profile-details {
          flex: 1;
        }

        .profile-name {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 10px;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .profile-meta {
          display: flex;
          gap: 20px;
          font-size: 0.9rem;
          opacity: 0.8;
        }

        .profile-actions {
          text-align: right;
          position: relative;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 12px 24px;
          border: none;
          border-radius: 8px;
          font-size: 14px;
          font-weight: 600;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.3s ease;
          margin-left: 8px;
        }

        .btn-secondary {
          background: rgba(255, 255, 255, 0.2);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        .profile-content {
          display: grid;
          grid-template-columns: 1fr 320px;
          gap: 30px;
        }

        .profile-main {
          display: flex;
          flex-direction: column;
          gap: 25px;
        }

        .profile-sidebar {
          display: flex;
          flex-direction: column;
          gap: 25px;
        }

        .profile-card {
          background: white;
          border-radius: 16px;
          padding: 25px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
        }

        .card-title {
          font-size: 1.2rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        .bio-content {
          color: #4a5568;
          line-height: 1.6;
          white-space: pre-wrap;
        }

        .bio-empty {
          color: #a0aec0;
          font-style: italic;
          text-align: center;
          padding: 20px;
        }

        .stats-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 15px;
        }

        .stat-item {
          text-align: center;
          padding: 12px 8px;
          background: #f8fafc;
          border-radius: 12px;
          border: 1px solid #e2e8f0;
        }

        .stat-value {
          display: block;
          font-size: 1.2rem;
          font-weight: 700;
          color: #667eea;
          margin-bottom: 4px;
        }

        .stat-label {
          font-size: 0.8rem;
          color: #718096;
          font-weight: 500;
        }

        .info-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .info-item {
          display: flex;
          align-items: center;
          padding: 12px 0;
          border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
          border-bottom: none;
        }

        .info-icon {
          width: 20px;
          text-align: center;
          color: #667eea;
          margin-right: 12px;
        }

        .info-content {
          flex: 1;
        }

        .info-label {
          font-size: 0.85rem;
          color: #718096;
          margin-bottom: 2px;
        }

        .info-value {
          font-size: 0.95rem;
          color: #2d3748;
          font-weight: 500;
        }

        .social-connections-card {
          position: relative;
          overflow: visible;
        }

        .social-grid {
          display: grid;
          gap: 12px;
        }

        .social-connection-item {
          display: flex;
          align-items: center;
          gap: 15px;
          padding: 16px;
          background: white;
          border: 2px solid #f1f5f9;
          border-radius: 16px;
          text-decoration: none !important;
          color: #374151;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          position: relative;
          overflow: hidden;
        }

        .social-connection-item::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: var(--social-color);
          opacity: 0;
          transition: opacity 0.3s ease;
          z-index: 0;
        }

        .social-connection-item:hover {
          transform: translateY(-4px);
          box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
          border-color: var(--social-color);
          text-decoration: none !important;
        }

        .social-connection-item:hover::before {
          opacity: 0.05;
        }

        .social-connection-item:hover .social-connection-icon {
          background: var(--social-color);
          color: white;
          transform: scale(1.1);
        }

        .social-connection-item:hover .social-connection-name {
          color: var(--social-color);
        }

        .social-connection-item:hover .social-connection-arrow {
          transform: translateX(4px);
          color: var(--social-color);
        }

        .social-connection-icon {
          width: 48px;
          height: 48px;
          border-radius: 12px;
          background: #f8fafc;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 20px;
          color: #6b7280;
          transition: all 0.3s ease;
          position: relative;
          z-index: 1;
        }

        .social-connection-content {
          flex: 1;
          position: relative;
          z-index: 1;
        }

        .social-connection-name {
          font-weight: 600;
          font-size: 1rem;
          margin-bottom: 2px;
          transition: color 0.3s ease;
        }

        .social-connection-action {
          font-size: 0.85rem;
          color: #9ca3af;
          font-weight: 500;
        }

        .social-connection-arrow {
          color: #d1d5db;
          font-size: 14px;
          transition: all 0.3s ease;
          position: relative;
          z-index: 1;
        }

        .empty-social .empty-social-content {
          text-align: center;
          padding: 40px 20px;
        }

        .empty-social-icon {
          width: 80px;
          height: 80px;
          border-radius: 50%;
          background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 32px;
          color: #9ca3af;
          margin: 0 auto 20px;
        }

        .empty-social-text h3 {
          font-size: 1.25rem;
          font-weight: 600;
          color: #374151;
          margin-bottom: 12px;
        }

        .empty-social-text p {
          color: #6b7280;
          line-height: 1.6;
          margin-bottom: 24px;
        }

        .btn-add-social {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 12px 24px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          border-radius: 8px;
          text-decoration: none;
          font-weight: 600;
          font-size: 14px;
          transition: all 0.3s ease;
          box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-add-social:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .activity-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .activity-item {
          padding: 15px 0;
          border-bottom: 1px solid #e2e8f0;
        }

        .activity-item:last-child {
          border-bottom: none;
        }

        .activity-title {
          font-size: 0.95rem;
          font-weight: 500;
          color: #2d3748;
          margin-bottom: 5px;
          line-height: 1.4;
        }

        .activity-title a {
          color: #667eea;
          text-decoration: none;
        }

        .activity-title a:hover {
          text-decoration: underline;
        }

        .activity-meta {
          font-size: 0.8rem;
          color: #a0aec0;
          display: flex;
          gap: 15px;
        }

        .activity-empty {
          text-align: center;
          color: #a0aec0;
          font-style: italic;
          padding: 20px;
        }

        .image-modal {
          position: fixed;
          z-index: 10000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.9);
          backdrop-filter: blur(5px);
        }

        .image-modal-content {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          max-width: 90%;
          max-height: 90%;
        }

        .image-modal img {
          width: 100%;
          height: auto;
          border-radius: 8px;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .modal-close {
          position: absolute;
          top: 20px;
          right: 30px;
          color: white;
          font-size: 40px;
          font-weight: bold;
          cursor: pointer;
          z-index: 10001;
        }

        .modal-close:hover {
          opacity: 0.7;
        }

        @media (max-width: 768px) {
          .profile-container {
            padding: 15px;
          }
          
          .profile-header-section {
            padding: 25px 20px;
            margin-bottom: 20px;
          }
          
          .profile-main-info {
            flex-direction: column;
            text-align: center;
            gap: 20px;
          }
          
          .profile-name {
            font-size: 2rem;
          }
          
          .profile-meta {
            justify-content: center;
            flex-wrap: wrap;
          }
          
          .profile-content {
            grid-template-columns: 1fr;
            gap: 20px;
          }
          
          .profile-sidebar {
            order: -1;
          }
          
          .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
          }
          
          .stat-item {
            padding: 8px 4px;
          }
          
          .stat-value {
            font-size: 1.2rem;
          }
        }

        @media (max-width: 480px) {
          .profile-image,
          .profile-image-fallback {
            width: 100px;
            height: 100px;
          }
          
          .profile-image-fallback {
            font-size: 2.5rem;
          }
          
          .profile-card {
            padding: 20px 15px;
          }
        }
      `}</style>
    </>
  );
};

export default ProfilePage;