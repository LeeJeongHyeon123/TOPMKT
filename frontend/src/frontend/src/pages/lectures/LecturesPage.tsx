import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Lecture, LectureSearchParams } from '../../types';
import { useApi } from '../../hooks/useApi';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const LecturesPage: React.FC = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [lectures, setLectures] = useState<Lecture[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState(searchParams.get('search') || '');
  const [selectedCategory, setSelectedCategory] = useState(searchParams.get('category') || '');
  const [priceRange, setPriceRange] = useState({
    min: searchParams.get('min_price') || '',
    max: searchParams.get('max_price') || '',
  });
  const [sortBy, setSortBy] = useState(searchParams.get('sort') || 'latest');

  const { execute } = useApi(async (data: any) => data);

  // 강의 목록 조회
  const fetchLectures = async (params: LectureSearchParams = {}) => {
    setLoading(true);
    try {
      const queryParams = {
        query: searchQuery,
        category: selectedCategory,
        min_price: priceRange.min,
        max_price: priceRange.max,
        sort_by: sortBy === 'latest' ? 'created_at' : sortBy,
        sort_direction: sortBy === 'latest' || sortBy === 'popular' ? 'desc' : 'asc',
        page: 1,
        per_page: 12,
        ...params,
      };

      const response = await execute({
        url: '/lectures',
        method: 'GET',
        params: queryParams,
      });

      if (response.success && response.data) {
        setLectures(response.data.data);
      }
    } catch (error) {
      console.error('강의 목록 조회 실패:', error);
    } finally {
      setLoading(false);
    }
  };

  // 초기 데이터 로드
  useEffect(() => {
    fetchLectures();
  }, [searchQuery, selectedCategory, priceRange, sortBy]);

  // 검색 처리
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    const newParams = new URLSearchParams();
    if (searchQuery) newParams.set('search', searchQuery);
    if (selectedCategory) newParams.set('category', selectedCategory);
    if (priceRange.min) newParams.set('min_price', priceRange.min);
    if (priceRange.max) newParams.set('max_price', priceRange.max);
    if (sortBy !== 'latest') newParams.set('sort', sortBy);
    setSearchParams(newParams);
  };

  // 필터 초기화
  const clearFilters = () => {
    setSearchQuery('');
    setSelectedCategory('');
    setPriceRange({ min: '', max: '' });
    setSortBy('latest');
    setSearchParams(new URLSearchParams());
  };

  // 가격 포맷팅
  const formatPrice = (price: number) => {
    if (price === 0) return '무료';
    return `${price.toLocaleString()}원`;
  };

  // 강의 카테고리 목록
  const categories = [
    { value: '', label: '전체 카테고리' },
    { value: 'marketing', label: '마케팅 기초' },
    { value: 'sales', label: '영업 전략' },
    { value: 'leadership', label: '리더십' },
    { value: 'communication', label: '커뮤니케이션' },
    { value: 'mindset', label: '마인드셋' },
    { value: 'business', label: '사업 운영' },
  ];

  return (
    <div className="lectures-page">
      {/* 헤더 섹션 */}
      <div className="page-header">
        <div className="container">
          <div className="page-header-content">
            <h1 className="page-title">
              전문가 강의
            </h1>
            <p className="page-subtitle">
              네트워크 마케팅 전문가들의 실전 노하우를 학습하고 성공을 향한 여정을 시작하세요.
            </p>
          </div>
        </div>
      </div>

      <div className="container">
        <div className="lectures-content">
          {/* 검색 및 필터 섹션 */}
          <div className="search-filter-section">
            <form onSubmit={handleSearch} className="search-form">
              <div className="search-grid">
                {/* 검색어 입력 */}
                <div className="search-input-wrapper">
                  <i className="fas fa-search search-icon"></i>
                  <input
                    type="text"
                    placeholder="강의 제목이나 강사명으로 검색"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="search-input"
                  />
                </div>

                {/* 카테고리 선택 */}
                <select
                  value={selectedCategory}
                  onChange={(e) => setSelectedCategory(e.target.value)}
                  className="form-select"
                >
                  {categories.map((category) => (
                    <option key={category.value} value={category.value}>
                      {category.label}
                    </option>
                  ))}
                </select>

                {/* 가격 범위 */}
                <div className="price-range-group">
                  <input
                    type="number"
                    placeholder="최소 가격"
                    value={priceRange.min}
                    onChange={(e) => setPriceRange(prev => ({ ...prev, min: e.target.value }))}
                    className="form-input price-input"
                  />
                  <input
                    type="number"
                    placeholder="최대 가격"
                    value={priceRange.max}
                    onChange={(e) => setPriceRange(prev => ({ ...prev, max: e.target.value }))}
                    className="form-input price-input"
                  />
                </div>

                {/* 정렬 옵션 */}
                <select
                  value={sortBy}
                  onChange={(e) => setSortBy(e.target.value)}
                  className="form-select"
                >
                  <option value="latest">최신순</option>
                  <option value="popular">인기순</option>
                  <option value="price">가격 낮은 순</option>
                  <option value="rating">평점 높은 순</option>
                </select>
              </div>

              <div className="search-actions">
                <button type="submit" className="btn btn-primary">
                  <i className="fas fa-search"></i>
                  검색
                </button>
                <button type="button" className="btn btn-outline" onClick={clearFilters}>
                  <i className="fas fa-refresh"></i>
                  필터 초기화
                </button>
              </div>
          </form>
        </div>

          {/* 강의 목록 */}
          {loading ? (
            <div className="loading-section">
              <LoadingSpinner size="lg" message="강의를 불러오는 중..." />
            </div>
          ) : (
            <div className="lectures-grid">
              {lectures.length > 0 ? (
                lectures.map((lecture) => (
                  <Link
                    key={lecture.id}
                    to={`/lectures/${lecture.id}`}
                    className="lecture-card-link"
                  >
                    <div className="lecture-card">
                      {/* 강의 썸네일 */}
                      <div className="lecture-thumbnail">
                        {lecture.thumbnail ? (
                          <img
                            src={lecture.thumbnail}
                            alt={lecture.title}
                            className="thumbnail-image"
                          />
                        ) : (
                          <div className="thumbnail-placeholder">
                            <i className="fas fa-play-circle thumbnail-icon"></i>
                          </div>
                        )}
                        
                        {/* 가격 배지 */}
                        <div className="price-badge">
                          <span className={`price-tag ${lecture.price === 0 ? 'free' : 'paid'}`}>
                            {formatPrice(lecture.price)}
                          </span>
                        </div>

                        {/* 재생 시간 */}
                        {lecture.duration && (
                          <div className="duration-badge">
                            <span className="duration-text">
                              {Math.floor(lecture.duration / 60)}분
                            </span>
                          </div>
                        )}
                      </div>

                      <div className="lecture-content">
                        {/* 강의 제목 */}
                        <h3 className="lecture-title">
                          {lecture.title}
                        </h3>

                        {/* 강사 정보 */}
                        <div className="instructor-info">
                          <div className="instructor-avatar">
                            {lecture.instructor.profile_image ? (
                              <img
                                src={lecture.instructor.profile_image}
                                alt={lecture.instructor.nickname}
                                className="instructor-image"
                              />
                            ) : (
                              <i className="fas fa-user instructor-icon"></i>
                            )}
                          </div>
                          <span className="instructor-name">
                            {lecture.instructor.nickname}
                          </span>
                        </div>

                        {/* 강의 설명 */}
                        <p className="lecture-description">
                          {lecture.description}
                        </p>

                        {/* 통계 정보 */}
                        <div className="lecture-stats">
                          <div className="stats-group">
                            <span className="stat-item">
                              <i className="fas fa-eye"></i>
                              {lecture.views.toLocaleString()}
                            </span>
                            <span className="stat-item">
                              <i className="fas fa-heart"></i>
                              {lecture.likes_count.toLocaleString()}
                            </span>
                            <span className="stat-item">
                              <i className="fas fa-users"></i>
                              {lecture.enrollment_count.toLocaleString()}
                            </span>
                          </div>
                          
                          <div className="lecture-status">
                            {lecture.status === 'ACTIVE' ? (
                              <span className="status-active">수강 가능</span>
                            ) : (
                              <span className="status-inactive">수강 불가</span>
                            )}
                          </div>
                        </div>
                      </div>
                  </div>
                </Link>
              ))
              ) : (
                <div className="empty-state">
                  <div className="empty-icon">
                    <i className="fas fa-video"></i>
                  </div>
                  <h3 className="empty-title">
                    등록된 강의가 없습니다
                  </h3>
                  <p className="empty-description">
                    검색 조건을 변경하거나 필터를 초기화해보세요.
                  </p>
                </div>
              )}
            </div>
          )}

          {/* 무료 강의 추천 섹션 */}
          {!loading && lectures.length > 0 && (
            <div className="free-lectures-section">
              <div className="section-header">
                <h2 className="section-title">
                  지금 바로 시작할 수 있는 무료 강의
                </h2>
                <p className="section-subtitle">
                  부담 없이 시작해보세요. 품질 높은 무료 강의들을 만나보세요.
                </p>
              </div>
              
              <div className="section-actions">
                <button
                  onClick={() => {
                    setSelectedCategory('');
                    setPriceRange({ min: '0', max: '0' });
                    setSortBy('popular');
                  }}
                  className="btn btn-gradient btn-large"
                >
                  <i className="fas fa-gift"></i>
                  무료 강의 둘러보기
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default LecturesPage;