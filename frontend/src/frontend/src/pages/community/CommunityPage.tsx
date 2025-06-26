import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Post, PostSearchParams } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useApi } from '../../hooks/useApi';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const CommunityPage: React.FC = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState(searchParams.get('search') || '');
  const [searchFilter, setSearchFilter] = useState(searchParams.get('filter') || 'all');
  const [currentPage, setCurrentPage] = useState(parseInt(searchParams.get('page') || '1'));

  const { isAuthenticated } = useAuth();
  const { execute } = useApi(async (data: any) => data);

  // 게시글 목록 조회
  const fetchPosts = async (params: PostSearchParams = {}) => {
    setLoading(true);
    try {
      const queryParams = {
        query: searchQuery,
        filter: searchFilter,
        sort_by: 'created_at',
        sort_direction: 'desc',
        page: currentPage,
        per_page: 10,
        status: 'PUBLISHED',
        ...params,
      };

      const response = await execute({
        url: '/posts',
        method: 'GET',
        params: queryParams,
      });

      if (response.success && response.data) {
        setPosts(response.data.data);
      }
    } catch (error) {
      console.error('게시글 목록 조회 실패:', error);
    } finally {
      setLoading(false);
    }
  };

  // 초기 데이터 로드
  useEffect(() => {
    fetchPosts();
  }, [searchQuery, searchFilter, currentPage]);

  // 검색 처리
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    const newParams = new URLSearchParams();
    if (searchQuery) newParams.set('search', searchQuery);
    if (searchFilter !== 'all') newParams.set('filter', searchFilter);
    newParams.set('page', '1');
    setSearchParams(newParams);
  };


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
      return date.toLocaleDateString('ko-KR', {
        month: 'short',
        day: 'numeric',
      });
    }
  };

  // 내용 미리보기 (HTML 태그 제거)
  const getPreviewText = (html: string, maxLength: number = 150) => {
    const text = html.replace(/<[^>]*>/g, '');
    return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
  };

  return (
    <>
      <style>
        {`
          .community-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 200px);
          }
          
          .community-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 12px;
          }
          
          .community-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
          }
          
          .community-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
          }
          
          .board-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
          }
          
          .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
          }
          
          .search-filter {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 100px;
          }
          
          .search-filter:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
          }
          
          .search-input {
            padding: 12px 45px 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            width: 250px;
            transition: all 0.3s ease;
            background: #fff;
            position: relative;
          }
          
          .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
          }
          
          .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            color: #ffffff;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
          }
          
          .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(55, 65, 81, 0.4);
          }
          
          .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
          }
          
          .btn-write {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: white;
            font-weight: 700;
          }
          
          .btn-write:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(55, 65, 81, 0.4);
            text-decoration: none;
          }
          
          .btn-secondary {
            background: #718096;
            color: white;
          }
          
          .btn-secondary:hover {
            background: #4a5568;
          }
          
          .board-stats {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
          }
          
          .stats-text {
            color: #4a5568;
            font-size: 14px;
            margin: 0;
          }
          
          .post-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
          }
          
          .post-item {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
            cursor: pointer;
            display: flex;
            gap: 15px;
            align-items: flex-start;
          }
          
          .post-item:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
          }
          
          .post-item:last-child {
            border-bottom: none;
          }
          
          .post-author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
          }
          
          .post-author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
          }
          
          .post-content-wrapper {
            flex: 1;
            min-width: 0;
          }
          
          .post-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            line-height: 1.4;
          }
          
          .post-title:hover {
            color: #667eea;
          }
          
          .post-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 10px;
          }
          
          .post-author {
            font-weight: 600;
            color: #4a5568;
          }
          
          .post-date {
            color: #a0aec0;
          }
          
          .post-content-preview {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-top: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
          }
          
          .post-stats {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #a0aec0;
            margin-top: 10px;
          }
          
          .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
          }
          
          .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
          }
          
          .page-link {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s ease;
          }
          
          .page-link:hover {
            background: #f8fafc;
            border-color: #667eea;
            color: #667eea;
          }
          
          .page-link.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
          }
          
          .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
          }
          
          .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
          }
          
          .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #4a5568;
          }
          
          .empty-state p {
            font-size: 0.9rem;
            margin-bottom: 20px;
          }
          
          @media (max-width: 768px) {
            .community-container {
              padding: 15px;
            }
            
            .community-header {
              padding: 30px 20px;
            }
            
            .community-header h1 {
              font-size: 2rem;
            }
            
            .board-controls {
              flex-direction: column;
              align-items: stretch;
            }
            
            .search-form {
              justify-content: center;
              margin-bottom: 15px;
            }
            
            .search-input {
              width: 100%;
              max-width: 300px;
            }
            
            .post-item {
              padding: 15px;
            }
            
            .post-meta {
              flex-direction: column;
              align-items: flex-start;
              gap: 5px;
            }
            
            .pagination {
              flex-wrap: wrap;
            }
          }
        `}
      </style>
      
      <div className="community-container">
        {/* 헤더 섹션 */}
        <div className="community-header">
          <h1>💬 커뮤니티 게시판</h1>
          <p>탑마케팅 커뮤니티에서 정보를 공유하고 함께 성장하세요</p>
        </div>
        
        {/* 게시판 컨트롤 영역 */}
        <div className="board-controls">
          {/* 검색 폼 */}
          <div className="search-wrapper">
            <form onSubmit={handleSearch} className="search-form">
              {/* 검색 필터 선택 */}
              <select 
                value={searchFilter} 
                onChange={(e) => setSearchFilter(e.target.value)}
                className="search-filter"
              >
                <option value="all">전체</option>
                <option value="title">제목만</option>
                <option value="content">내용만</option>
                <option value="author">작성자</option>
              </select>
              
              <div style={{ position: 'relative', flex: '1', minWidth: '250px' }}>
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="검색어를 입력하세요..."
                  className="search-input"
                  maxLength={100}
                  autoComplete="off"
                />
                <button type="submit" className="search-btn">
                  <i className="fas fa-search"></i>
                </button>
              </div>
              
              {searchQuery && (
                <Link to="/community" className="btn btn-secondary">
                  ✖️ 검색 해제
                </Link>
              )}
            </form>
          </div>
          
          {/* 글쓰기 버튼 */}
          {isAuthenticated ? (
            <Link to="/community/write" className="btn btn-write">
              <i className="fas fa-pen"></i> 글쓰기
            </Link>
          ) : (
            <Link to={`/auth/login?redirect=${encodeURIComponent(window.location.pathname)}`} className="btn btn-primary">
              🔑 로그인 후 글쓰기
            </Link>
          )}
        </div>
        
        {/* 게시판 통계 */}
        <div className="board-stats">
          <p className="stats-text">
            📊 총 <strong>{posts.length}</strong>개의 게시글이 있습니다
            {searchQuery && ' (검색 결과)'}
          </p>
        </div>

        {/* 게시글 목록 */}
        {loading ? (
          <div style={{ display: 'flex', justifyContent: 'center', padding: '60px 0' }}>
            <LoadingSpinner size="lg" message="게시글을 불러오는 중..." />
          </div>
        ) : (
          <div className="post-list">
            {posts.length > 0 ? (
              posts.map((post) => (
                <div
                  key={post.id}
                  className="post-item"
                  onClick={() => window.location.href = `/community/${post.id}`}
                >
                  {/* 작성자 프로필 이미지 */}
                  <div 
                    className="post-author-avatar profile-image-clickable"
                    data-user-id={post.user?.id}
                    data-user-name={post.user?.nickname || '익명'}
                    title="프로필 이미지 크게 보기"
                    onClick={(e) => {
                      e.stopPropagation();
                      // TODO: Profile modal functionality  
                    }}
                  >
                    {post.user?.profile_image ? (
                      <img
                        src={post.user.profile_image}
                        alt={post.user?.nickname || '익명'}
                        loading="lazy"
                        width="50"
                        height="50"
                        style={{ objectFit: 'cover', borderRadius: '50%' }}
                      />
                    ) : (
                      (post.user?.nickname || '?').charAt(0)
                    )}
                  </div>
                  
                  {/* 게시글 내용 */}
                  <div className="post-content-wrapper">
                    <div className="post-title">
                      {post.title}
                      {(post.comments_count || 0) > 0 && (
                        <span style={{ color: '#e53e3e', fontSize: '0.9rem' }}>
                          [{post.comments_count}]
                        </span>
                      )}
                    </div>
                    
                    <div className="post-meta">
                      <span className="post-author">👤 {post.user?.nickname || '익명'}</span>
                      <span className="post-date">📅 {formatDate(post.created_at)}</span>
                    </div>
                    
                    <div className="post-content-preview">
                      {getPreviewText(post.content)}
                    </div>
                    
                    <div className="post-stats">
                      <span className="stat-item">
                        👁️ {(post.views || 0).toLocaleString()}
                      </span>
                      <span className="stat-item">
                        💬 {(post.comments_count || 0).toLocaleString()}
                      </span>
                      <span className="stat-item">
                        ❤️ {(post.likes_count || 0).toLocaleString()}
                      </span>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="empty-state">
                <div style={{ fontSize: '3rem', marginBottom: '20px', color: '#cbd5e0' }}>📝</div>
                <h3>
                  {searchQuery ? `"${searchQuery}" 검색 결과가 없습니다` : '첫 번째 게시글을 작성해보세요!'}
                </h3>
                <p>
                  {searchQuery ? (
                    <>💡 검색 팁:<br />
                    • 검색어의 철자를 확인해보세요<br />
                    • 더 간단한 키워드로 다시 검색해보세요<br />
                    • 관련된 다른 단어로 검색해보세요</>
                  ) : (
                    '탑마케팅 커뮤니티의 첫 번째 이야기를 시작해보세요.'
                  )}
                </p>
                {isAuthenticated && (
                  <Link to="/community/write" className="btn btn-primary">
                    <i className="fas fa-pen"></i> 글쓰기
                  </Link>
                )}
              </div>
            )}
          </div>
        )}
      </div>
    </>
  );
};

export default CommunityPage;