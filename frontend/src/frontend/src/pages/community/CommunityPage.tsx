import React, { useState, useCallback, useMemo, useEffect } from 'react';
import { Link, useSearchParams, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useInfinitePosts } from '../../hooks/api/useCommunityQueries';
import { useDebounce } from '../../hooks/useDebounce';
import { useIntersectionObserver } from '../../hooks/useIntersectionObserver';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import LazyImage from '../../components/common/LazyImage';
import { CommunityPost } from '../../services/CommunityService';
import SEOHead from '../../components/common/SEOHead';
import { usePageMeta } from '../../hooks/usePageMeta';

// 메모이제이션된 게시글 아이템 컴포넌트
const PostItem = React.memo<{ post: CommunityPost; onClick: () => void }>(({ post, onClick }) => {
  return (
    <article className="post-item" onClick={onClick}>
      <div className="post-content">
        <div className="post-header">
          <div className="post-author">
            <LazyImage 
              src={post.profile_image || '/assets/images/default-avatar.png'} 
              alt={post.author_name}
              className="author-avatar"
              width={48}
              height={48}
              objectFit="cover"
              priority={false}
            />
            <div className="author-info">
              <span className="author-name">{post.author_name}</span>
              <time className="post-date">
                {new Date(post.created_at).toLocaleDateString('ko-KR')}
              </time>
            </div>
          </div>
          {post.is_pinned && (
            <span className="pinned-badge">
              <i className="fas fa-thumbtack"></i>
              고정됨
            </span>
          )}
        </div>
        
        <h2 className="post-title">{post.title}</h2>
        <p className="post-preview">{post.content_preview}</p>
        
        {post.image_path && (
          <div className="post-image">
            <LazyImage 
              src={post.image_path} 
              alt="게시글 이미지"
              width={600}
              height={200}
              objectFit="cover"
              priority={false}
            />
          </div>
        )}
        
        <div className="post-meta">
          <span className="meta-item">
            <i className="fas fa-eye"></i>
            {post.view_count.toLocaleString()}
          </span>
          <span className="meta-item">
            <i className={`fas fa-heart ${post.is_liked ? 'liked' : ''}`}></i>
            {post.like_count.toLocaleString()}
          </span>
          <span className="meta-item">
            <i className="fas fa-comment"></i>
            {post.comment_count.toLocaleString()}
          </span>
        </div>
      </div>
    </article>
  );
});

PostItem.displayName = 'PostItem';

// 메모이제이션된 검색 컴포넌트
const SearchSection = React.memo<{
  searchQuery: string;
  searchFilter: string;
  onSearchChange: (query: string) => void;
  onFilterChange: (filter: string) => void;
  onSubmit: () => void;
}>(({ searchQuery, searchFilter, onSearchChange, onFilterChange, onSubmit }) => {
  const handleSubmit = useCallback((e: React.FormEvent) => {
    e.preventDefault();
    onSubmit();
  }, [onSubmit]);

  return (
    <section className="search-section">
      <div className="search-container">
        <form onSubmit={handleSubmit} className="search-form">
          <div className="search-filters">
            <select 
              value={searchFilter} 
              onChange={(e) => onFilterChange(e.target.value)}
              className="search-filter"
            >
              <option value="all">전체</option>
              <option value="title">제목</option>
              <option value="content">내용</option>
              <option value="author">작성자</option>
            </select>
          </div>
          <div className="search-input-container">
            <input
              type="text"
              value={searchQuery}
              onChange={(e) => onSearchChange(e.target.value)}
              placeholder="검색어를 입력하세요..."
              className="search-input"
            />
            <button type="submit" className="search-button">
              <i className="fas fa-search"></i>
            </button>
          </div>
        </form>
      </div>
    </section>
  );
});

SearchSection.displayName = 'SearchSection';

// 로딩 스켈레톤 컴포넌트
const PostSkeleton = React.memo(() => (
  <div className="post-skeleton">
    <div className="skeleton-header">
      <div className="skeleton-avatar"></div>
      <div className="skeleton-author">
        <div className="skeleton-line skeleton-name"></div>
        <div className="skeleton-line skeleton-date"></div>
      </div>
    </div>
    <div className="skeleton-title"></div>
    <div className="skeleton-content">
      <div className="skeleton-line"></div>
      <div className="skeleton-line"></div>
      <div className="skeleton-line short"></div>
    </div>
    <div className="skeleton-meta">
      <div className="skeleton-line skeleton-meta-item"></div>
      <div className="skeleton-line skeleton-meta-item"></div>
      <div className="skeleton-line skeleton-meta-item"></div>
    </div>
  </div>
));

PostSkeleton.displayName = 'PostSkeleton';

const CommunityPage: React.FC = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const navigate = useNavigate();
  
  // 검색 상태
  const [searchQuery, setSearchQuery] = useState(searchParams.get('search') || '');
  const [searchFilter, setSearchFilter] = useState(searchParams.get('filter') || 'all');
  const [appliedFilters, setAppliedFilters] = useState({
    search: searchParams.get('search') || undefined,
    filter: (searchParams.get('filter') || 'all') as 'all' | 'title' | 'content' | 'author'
  });

  const { isAuthenticated } = useAuth();
  
  // 디바운스된 검색어
  const debouncedSearchQuery = useDebounce(searchQuery, 500);

  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '커뮤니티',
    description: '네트워크 마케팅 전문가들과 정보를 공유하고 소통하는 공간입니다.',
    ogType: 'website'
  });

  // 무한 스크롤 쿼리
  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
    isError,
    error,
    refetch
  } = useInfinitePosts(appliedFilters);

  // 교차점 관찰자
  const { targetRef, isIntersecting } = useIntersectionObserver({
    threshold: 0.1,
    rootMargin: '100px',
    enabled: hasNextPage && !isFetchingNextPage
  });

  // 교차점에 도달하면 다음 페이지 로드
  useEffect(() => {
    if (isIntersecting && hasNextPage && !isFetchingNextPage) {
      fetchNextPage();
    }
  }, [isIntersecting, hasNextPage, isFetchingNextPage, fetchNextPage]);

  // 모든 게시글을 평면화
  const allPosts = useMemo(() => {
    return data?.pages.flatMap(page => page.posts) || [];
  }, [data]);

  // 총 게시글 수
  const totalCount = useMemo(() => {
    return data?.pages[0]?.pagination.total_count || 0;
  }, [data]);

  // 검색 적용
  const applySearch = useCallback(() => {
    const newFilters = {
      search: debouncedSearchQuery || undefined,
      filter: (searchFilter === 'all' ? 'all' : searchFilter) as 'all' | 'title' | 'content' | 'author'
    };
    
    setAppliedFilters(newFilters);
    
    // URL 업데이트
    const newSearchParams = new URLSearchParams();
    if (newFilters.search) newSearchParams.set('search', newFilters.search);
    if (newFilters.filter) newSearchParams.set('filter', newFilters.filter);
    setSearchParams(newSearchParams);
  }, [debouncedSearchQuery, searchFilter, setSearchParams]);

  // 디바운스된 검색어가 변경되면 자동 검색
  useEffect(() => {
    applySearch();
  }, [applySearch]);

  // 게시글 클릭 핸들러
  const handlePostClick = useCallback((postId: number) => {
    navigate(`/community/post/${postId}`);
  }, [navigate]);

  // 에러 처리
  if (isError) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="community-page">
          <div className="error-container">
            <i className="fas fa-exclamation-triangle"></i>
            <h2>게시글을 불러오는데 실패했습니다</h2>
            <p>{error?.message || '알 수 없는 오류가 발생했습니다.'}</p>
            <button onClick={() => refetch()} className="retry-button">
              다시 시도
            </button>
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="community-page">
        {/* 헤더 섹션 */}
        <section className="community-header">
          <div className="container">
            <h1>
              <i className="fas fa-users"></i>
              커뮤니티
            </h1>
            <p>네트워크 마케팅 전문가들과 정보를 공유하고 소통하는 공간입니다</p>
            <div className="header-actions">
              {isAuthenticated && (
                <Link to="/community/write" className="btn btn-primary">
                  <i className="fas fa-pen"></i>
                  글쓰기
                </Link>
              )}
            </div>
          </div>
        </section>

        {/* 검색 섹션 */}
        <SearchSection
          searchQuery={searchQuery}
          searchFilter={searchFilter}
          onSearchChange={setSearchQuery}
          onFilterChange={setSearchFilter}
          onSubmit={applySearch}
        />

        {/* 메인 컨텐츠 */}
        <div className="container">
          <div className="community-content">
            {/* 통계 정보 */}
            {!isLoading && (
              <div className="community-stats">
                <span className="total-count">
                  총 <strong>{totalCount.toLocaleString()}</strong>개의 게시글
                </span>
                {appliedFilters.search && (
                  <span className="search-info">
                    '<strong>{appliedFilters.search}</strong>' 검색 결과
                  </span>
                )}
              </div>
            )}

            {/* 게시글 목록 */}
            <div className="posts-container">
              {isLoading ? (
                // 초기 로딩 스켈레톤
                <div className="posts-list">
                  {Array.from({ length: 6 }, (_, i) => (
                    <PostSkeleton key={i} />
                  ))}
                </div>
              ) : allPosts.length > 0 ? (
                <>
                  <div className="posts-list">
                    {allPosts.map((post, index) => (
                      <PostItem
                        key={`${post.id}-${index}`}
                        post={post}
                        onClick={() => handlePostClick(post.id)}
                      />
                    ))}
                  </div>

                  {/* 무한 스크롤 트리거 */}
                  {hasNextPage && (
                    <div ref={targetRef} className="infinite-scroll-trigger">
                      {isFetchingNextPage && (
                        <div className="loading-more">
                          <LoadingSpinner />
                          <span>더 많은 게시글을 불러오는 중...</span>
                        </div>
                      )}
                    </div>
                  )}

                  {/* 끝 도달 메시지 */}
                  {!hasNextPage && allPosts.length > 0 && (
                    <div className="end-message">
                      <i className="fas fa-flag-checkered"></i>
                      모든 게시글을 확인했습니다!
                    </div>
                  )}
                </>
              ) : (
                // 빈 상태
                <div className="empty-state">
                  <i className="fas fa-search"></i>
                  <h3>게시글이 없습니다</h3>
                  <p>
                    {appliedFilters.search 
                      ? '검색 조건에 맞는 게시글이 없습니다.' 
                      : '첫 번째 게시글을 작성해보세요!'
                    }
                  </p>
                  {isAuthenticated && !appliedFilters.search && (
                    <Link to="/community/write" className="btn btn-primary">
                      <i className="fas fa-pen"></i>
                      첫 게시글 작성하기
                    </Link>
                  )}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* 스타일 */}
      <style>{`
        .community-page {
          background: #f8fafc;
          min-height: calc(100vh - 80px);
        }

        .community-header {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          padding: 3rem 0;
          text-align: center;
        }

        .community-header h1 {
          font-size: 2.5rem;
          font-weight: bold;
          margin-bottom: 1rem;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 1rem;
        }

        .community-header i {
          font-size: 2rem;
          color: #ffd700;
        }

        .community-header p {
          font-size: 1.1rem;
          opacity: 0.9;
          margin-bottom: 2rem;
        }

        .header-actions {
          display: flex;
          justify-content: center;
          gap: 1rem;
        }

        .search-section {
          background: white;
          border-bottom: 1px solid #e2e8f0;
          padding: 1.5rem 0;
        }

        .search-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 1rem;
        }

        .search-form {
          display: flex;
          gap: 1rem;
          align-items: center;
        }

        .search-filters {
          min-width: 120px;
        }

        .search-filter {
          width: 100%;
          padding: 0.75rem;
          border: 1px solid #d1d5db;
          border-radius: 8px;
          font-size: 0.9rem;
          background: white;
        }

        .search-input-container {
          flex: 1;
          display: flex;
          position: relative;
        }

        .search-input {
          flex: 1;
          padding: 0.75rem 3rem 0.75rem 1rem;
          border: 1px solid #d1d5db;
          border-radius: 8px;
          font-size: 0.9rem;
        }

        .search-input:focus {
          outline: none;
          border-color: #667eea;
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-button {
          position: absolute;
          right: 8px;
          top: 50%;
          transform: translateY(-50%);
          background: #667eea;
          color: white;
          border: none;
          border-radius: 6px;
          width: 36px;
          height: 36px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
        }

        .search-button:hover {
          background: #5a67d8;
        }

        .community-content {
          padding: 2rem 0;
        }

        .community-stats {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 2rem;
          padding: 1rem;
          background: white;
          border-radius: 12px;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .total-count {
          color: #4a5568;
          font-size: 0.95rem;
        }

        .search-info {
          color: #667eea;
          font-size: 0.9rem;
        }

        .posts-container {
          position: relative;
        }

        .posts-list {
          display: grid;
          gap: 1.5rem;
        }

        .post-item {
          background: white;
          border-radius: 12px;
          padding: 1.5rem;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
          cursor: pointer;
          transition: all 0.2s ease;
        }

        .post-item:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
          border-color: #667eea;
        }

        .post-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1rem;
        }

        .post-author {
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .author-avatar {
          width: 48px;
          height: 48px;
          border-radius: 50%;
          object-fit: cover;
          border: 2px solid #e2e8f0;
        }

        .author-info {
          display: flex;
          flex-direction: column;
        }

        .author-name {
          font-weight: 600;
          color: #2d3748;
          font-size: 0.9rem;
        }

        .post-date {
          color: #718096;
          font-size: 0.8rem;
        }

        .pinned-badge {
          background: #f56565;
          color: white;
          padding: 0.25rem 0.75rem;
          border-radius: 20px;
          font-size: 0.75rem;
          font-weight: 600;
          display: flex;
          align-items: center;
          gap: 0.25rem;
        }

        .post-title {
          font-size: 1.25rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 0.75rem;
          line-height: 1.4;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .post-preview {
          color: #4a5568;
          line-height: 1.6;
          margin-bottom: 1rem;
          display: -webkit-box;
          -webkit-line-clamp: 3;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .post-image {
          margin-bottom: 1rem;
          border-radius: 8px;
          overflow: hidden;
        }

        .post-image img {
          width: 100%;
          height: 200px;
          object-fit: cover;
        }

        .post-meta {
          display: flex;
          gap: 1.5rem;
          color: #718096;
          font-size: 0.85rem;
        }

        .meta-item {
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .meta-item i.liked {
          color: #f56565;
        }

        .infinite-scroll-trigger {
          padding: 2rem 0;
          display: flex;
          justify-content: center;
        }

        .loading-more {
          display: flex;
          flex-direction: column;
          align-items: center;
          gap: 1rem;
          color: #718096;
        }

        .end-message {
          text-align: center;
          padding: 2rem;
          color: #718096;
        }

        .end-message i {
          font-size: 2rem;
          margin-bottom: 1rem;
          display: block;
        }

        .empty-state {
          text-align: center;
          padding: 4rem 2rem;
          color: #718096;
        }

        .empty-state i {
          font-size: 4rem;
          margin-bottom: 1.5rem;
          display: block;
          opacity: 0.5;
        }

        .empty-state h3 {
          font-size: 1.5rem;
          color: #4a5568;
          margin-bottom: 0.5rem;
        }

        .empty-state p {
          margin-bottom: 2rem;
        }

        .error-container {
          text-align: center;
          padding: 4rem 2rem;
          color: #e53e3e;
        }

        .error-container i {
          font-size: 4rem;
          margin-bottom: 1.5rem;
          display: block;
        }

        .retry-button {
          background: #667eea;
          color: white;
          border: none;
          padding: 0.75rem 1.5rem;
          border-radius: 8px;
          cursor: pointer;
          margin-top: 1rem;
        }

        /* 스켈레톤 로딩 */
        .post-skeleton {
          background: white;
          border-radius: 12px;
          padding: 1.5rem;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
        }

        .skeleton-header {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          margin-bottom: 1rem;
        }

        .skeleton-avatar {
          width: 48px;
          height: 48px;
          border-radius: 50%;
          background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
          background-size: 200% 100%;
          animation: loading 1.5s infinite;
        }

        .skeleton-author {
          flex: 1;
        }

        .skeleton-line {
          background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
          background-size: 200% 100%;
          animation: loading 1.5s infinite;
          border-radius: 4px;
          margin-bottom: 0.5rem;
        }

        .skeleton-name {
          height: 16px;
          width: 100px;
        }

        .skeleton-date {
          height: 12px;
          width: 80px;
        }

        .skeleton-title {
          height: 24px;
          background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
          background-size: 200% 100%;
          animation: loading 1.5s infinite;
          border-radius: 4px;
          margin-bottom: 0.75rem;
        }

        .skeleton-content .skeleton-line {
          height: 16px;
        }

        .skeleton-content .skeleton-line.short {
          width: 60%;
        }

        .skeleton-meta {
          display: flex;
          gap: 1.5rem;
        }

        .skeleton-meta-item {
          height: 14px;
          width: 60px;
          margin-bottom: 0;
        }

        @keyframes loading {
          0% {
            background-position: 200% 0;
          }
          100% {
            background-position: -200% 0;
          }
        }

        @media (max-width: 768px) {
          .community-header h1 {
            font-size: 2rem;
          }

          .search-form {
            flex-direction: column;
          }

          .search-filters {
            min-width: 100%;
          }

          .community-stats {
            flex-direction: column;
            gap: 0.5rem;
            text-align: center;
          }

          .post-meta {
            justify-content: center;
          }
        }
      `}</style>
    </>
  );
};

export default CommunityPage;