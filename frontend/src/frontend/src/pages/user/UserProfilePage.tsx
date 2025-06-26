import React, { useState, useEffect } from 'react';
import Layout from '../../components/common/Layout';

interface User {
  id: number;
  nickname: string;
  email?: string;
  phone?: string;
  birth_date?: string;
  gender?: 'M' | 'F' | 'OTHER';
  bio?: string;
  profile_image_profile?: string;
  profile_image_thumb?: string;
  profile_image_original?: string;
  social_links?: Record<string, string>;
  created_at: string;
  last_login?: string;
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

const UserProfilePage: React.FC = () => {
  const [user] = useState<User | null>(null);
  const [stats] = useState<UserStats>({
    post_count: 0,
    comment_count: 0,
    like_count: 0,
    join_days: 0
  });
  const [recentPosts] = useState<Post[]>([]);
  const [recentComments] = useState<Comment[]>([]);
  const [isOwnProfile] = useState(false);
  const [showImageModal, setShowImageModal] = useState(false);
  const [modalImageSrc, setModalImageSrc] = useState('');

  useEffect(() => {
    // 실제 구현시 API에서 프로필 데이터 가져오기
    loadUserProfile();
  }, []);

  const loadUserProfile = async () => {
    // API 호출 시뮬레이션
    // 실제로는 userService.getProfile() 사용
  };

  const getProfileImageUrl = () => {
    if (user?.profile_image_profile) {
      return user.profile_image_profile;
    }
    if (user?.profile_image_thumb) {
      return user.profile_image_thumb;
    }
    return '/assets/images/default-avatar.png';
  };

  const getAge = () => {
    if (!user?.birth_date) return null;
    const birthDate = new Date(user.birth_date);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
      return age - 1;
    }
    return age;
  };

  const getJoinDate = () => {
    if (!user?.created_at) return '알 수 없음';
    const date = new Date(user.created_at);
    return `${date.getFullYear()}년 ${date.getMonth() + 1}월 ${date.getDate()}일`;
  };

  const getLastLogin = () => {
    if (!user?.last_login) return '정보 없음';
    const lastLoginTime = new Date(user.last_login).getTime();
    const timeDiff = Date.now() - lastLoginTime;
    
    if (timeDiff < 60000) return '방금 전';
    if (timeDiff < 3600000) return `${Math.floor(timeDiff / 60000)}분 전`;
    if (timeDiff < 86400000) return `${Math.floor(timeDiff / 3600000)}시간 전`;
    if (timeDiff < 2592000000) return `${Math.floor(timeDiff / 86400000)}일 전`;
    
    return new Date(user.last_login).toLocaleDateString();
  };

  const getGenderName = (gender?: string) => {
    switch (gender) {
      case 'M': return '남성';
      case 'F': return '여성';
      case 'OTHER': return '기타';
      default: return '알 수 없음';
    }
  };

  const showImageModalHandler = (imageSrc: string) => {
    setModalImageSrc(imageSrc);
    setShowImageModal(true);
    document.body.style.overflow = 'hidden';
  };

  const hideImageModal = () => {
    setShowImageModal(false);
    setModalImageSrc('');
    document.body.style.overflow = '';
  };

  const shareContent = () => {
    const profileTitle = `${user?.nickname}님의 프로필 - 탑마케팅`;
    const profileUrl = `https://${window.location.host}/profile/${encodeURIComponent(user?.nickname || '')}`;
    const profileDescription = `탑마케팅에서 ${user?.nickname}님의 프로필을 확인해보세요!`;

    if (navigator.share) {
      navigator.share({
        title: profileTitle,
        text: profileDescription,
        url: profileUrl
      }).catch(() => {
        fallbackShare(profileTitle, profileUrl);
      });
    } else {
      fallbackShare(profileTitle, profileUrl);
    }
  };

  const fallbackShare = (_title: string, url: string) => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(url).then(() => {
        alert('🔗 링크가 클립보드에 복사되었습니다!\n다른 곳에 붙여넣기하여 공유하세요.');
      }).catch(() => {
        showShareModal(_title, url);
      });
    } else {
      showShareModal(_title, url);
    }
  };

  const showShareModal = (_title: string, url: string) => {
    // 공유 모달 표시 로직
    alert(`공유 링크: ${url}`);
  };

  const socialConfigs = {
    website: { icon: 'fas fa-globe', name: '웹사이트', color: '#6366f1' },
    kakao: { icon: 'fas fa-comment', name: '카카오톡', color: '#FEE500' },
    instagram: { icon: 'fab fa-instagram', name: '인스타그램', color: '#E4405F' },
    facebook: { icon: 'fab fa-facebook', name: '페이스북', color: '#1877F2' },
    youtube: { icon: 'fab fa-youtube', name: '유튜브', color: '#FF0000' },
    tiktok: { icon: 'fab fa-tiktok', name: '틱톡', color: '#000000' }
  };

  const displayOrder = ['website', 'kakao', 'instagram', 'facebook', 'youtube', 'tiktok'];

  const age = getAge();

  return (
    <Layout>
      <div className="profile-container">
        {/* 프로필 헤더 */}
        <div className="profile-header-section">
          <div className="profile-main-info">
            <div className="profile-image-container">
              {user?.profile_image_profile ? (
                <img 
                  src={getProfileImageUrl()} 
                  alt={`${user.nickname}님의 프로필 이미지`}
                  className="profile-image"
                  onClick={() => showImageModalHandler(user.profile_image_original || getProfileImageUrl())}
                />
              ) : (
                <div className="profile-image-fallback">
                  {user?.nickname?.charAt(0) || '?'}
                </div>
              )}
            </div>
            
            <div className="profile-details">
              <h1 className="profile-name">{user?.nickname || '사용자'}</h1>
              <div className="profile-meta">
                <span>🗓️ 가입일: {getJoinDate()}</span>
                {stats.join_days > 0 && (
                  <span>⏰ 활동 {stats.join_days}일째</span>
                )}
                {!isOwnProfile && (
                  <span>👀 최근 접속: {getLastLogin()}</span>
                )}
              </div>
            </div>
            
            <div className="profile-actions">
              {isOwnProfile && (
                <a href="/profile/edit" className="btn btn-secondary">
                  ✏️ 프로필 편집
                </a>
              )}
              
              <button className="btn btn-secondary" onClick={shareContent}>
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
              {user?.bio ? (
                <div className="bio-content">{user.bio}</div>
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
                        <a href={`/community/posts/${post.id}`}>
                          {post.title}
                        </a>
                      </div>
                      <div className="activity-meta">
                        <span>📅 {new Date(post.created_at).toLocaleString()}</span>
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
                        <a href={`/community/posts/${comment.post_id}#comment-${comment.id}`}>
                          {comment.post_title}
                        </a>에 댓글
                      </div>
                      <div className="activity-meta">
                        <span>📅 {new Date(comment.created_at).toLocaleString()}</span>
                        <span>💬 {comment.content.length > 50 ? comment.content.substring(0, 50) + '...' : comment.content}</span>
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
                {user?.email && (
                  <li className="info-item">
                    <i className="info-icon fas fa-envelope"></i>
                    <div className="info-content">
                      <div className="info-label">이메일</div>
                      <div className="info-value">{user.email}</div>
                    </div>
                  </li>
                )}
                
                {user?.birth_date && age !== null && (
                  <li className="info-item">
                    <i className="info-icon fas fa-birthday-cake"></i>
                    <div className="info-content">
                      <div className="info-label">나이</div>
                      <div className="info-value">{age}세</div>
                    </div>
                  </li>
                )}
                
                {user?.gender && (
                  <li className="info-item">
                    <i className="info-icon fas fa-venus-mars"></i>
                    <div className="info-content">
                      <div className="info-label">성별</div>
                      <div className="info-value">{getGenderName(user.gender)}</div>
                    </div>
                  </li>
                )}
              </ul>
            </div>
            
            {/* 소셜 링크 & 웹사이트 */}
            {user?.social_links && Object.keys(user.social_links).length > 0 ? (
              <div className="profile-card social-connections-card">
                <h2 className="card-title">
                  <i className="fas fa-globe-americas"></i> 소셜 & 웹사이트
                </h2>
                
                <div className="social-section">
                  <div className="social-grid">
                    {displayOrder.map(platform => {
                      const url = user.social_links?.[platform];
                      if (!url || !socialConfigs[platform as keyof typeof socialConfigs]) return null;
                      
                      const config = socialConfigs[platform as keyof typeof socialConfigs];
                      
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
            ) : (
              isOwnProfile && (
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
                      <p>인스타그램, 유튜브, 개인 웹사이트 등을<br/>프로필에 추가하여 더 많은 사람들과 소통하세요.</p>
                    </div>
                    <a href="/profile/edit" className="btn-add-social">
                      <i className="fas fa-plus"></i> 소셜 링크 추가하기
                    </a>
                  </div>
                </div>
              )
            )}
          </div>
        </div>
      </div>

      {/* 이미지 확대 모달 */}
      {showImageModal && (
        <div className="image-modal" onClick={hideImageModal}>
          <span className="modal-close" onClick={hideImageModal}>×</span>
          <div className="image-modal-content">
            <img src={modalImageSrc} alt="프로필 이미지" />
          </div>
        </div>
      )}

      <style>{`
        /* 프로필 페이지 전용 스타일 */
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
          margin-left: 10px;
        }

        .btn-secondary {
          background: rgba(255, 255, 255, 0.2);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        /* 콘텐츠 그리드 */
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

        /* 카드 공통 스타일 */
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

        /* 자기소개 카드 */
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

        /* 통계 카드 */
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

        /* 기본 정보 카드 */
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

        /* 소셜 연결 카드 */
        .social-section {
          margin-bottom: 20px;
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

        .social-connection-item:hover {
          transform: translateY(-4px);
          box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
          border-color: var(--social-color);
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
        }

        .social-connection-item:hover .social-connection-icon {
          background: var(--social-color);
          color: white;
          transform: scale(1.1);
        }

        .social-connection-content {
          flex: 1;
        }

        .social-connection-name {
          font-weight: 600;
          font-size: 1rem;
          margin-bottom: 2px;
          transition: color 0.3s ease;
        }

        .social-connection-item:hover .social-connection-name {
          color: var(--social-color);
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
        }

        .social-connection-item:hover .social-connection-arrow {
          transform: translateX(4px);
          color: var(--social-color);
        }

        /* 빈 소셜 상태 */
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

        /* 최근 활동 */
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

        /* 이미지 모달 */
        .image-modal {
          display: flex;
          position: fixed;
          z-index: 10000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.9);
          backdrop-filter: blur(5px);
          justify-content: center;
          align-items: center;
        }

        .image-modal-content {
          position: relative;
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

        /* 반응형 디자인 */
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
    </Layout>
  );
};

export default UserProfilePage;