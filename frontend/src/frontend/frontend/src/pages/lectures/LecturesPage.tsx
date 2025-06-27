import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Lecture, LectureSearchParams, PaginatedResponse } from '../../types';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
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

  const { request } = useApi();

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

      const response = await request<PaginatedResponse<Lecture>>({
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
    <div className="min-h-screen bg-gray-50">
      {/* 헤더 섹션 */}
      <div className="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="text-center">
            <h1 className="text-4xl font-bold mb-4">
              전문가 강의
            </h1>
            <p className="text-xl text-blue-100 max-w-2xl mx-auto">
              네트워크 마케팅 전문가들의 실전 노하우를 학습하고 성공을 향한 여정을 시작하세요.
            </p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 검색 및 필터 섹션 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-8">
          <form onSubmit={handleSearch} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {/* 검색어 입력 */}
              <Input
                placeholder="강의 제목이나 강사명으로 검색"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                name="search"
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                }
              />

              {/* 카테고리 선택 */}
              <select
                value={selectedCategory}
                onChange={(e) => setSelectedCategory(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                {categories.map((category) => (
                  <option key={category.value} value={category.value}>
                    {category.label}
                  </option>
                ))}
              </select>

              {/* 가격 범위 */}
              <div className="flex space-x-2">
                <Input
                  placeholder="최소 가격"
                  type="number"
                  value={priceRange.min}
                  onChange={(e) => setPriceRange(prev => ({ ...prev, min: e.target.value }))}
                  name="min_price"
                />
                <Input
                  placeholder="최대 가격"
                  type="number"
                  value={priceRange.max}
                  onChange={(e) => setPriceRange(prev => ({ ...prev, max: e.target.value }))}
                  name="max_price"
                />
              </div>

              {/* 정렬 옵션 */}
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="latest">최신순</option>
                <option value="popular">인기순</option>
                <option value="price">가격 낮은 순</option>
                <option value="rating">평점 높은 순</option>
              </select>
            </div>

            <div className="flex justify-between items-center">
              <Button type="submit" className="bg-blue-600 hover:bg-blue-700">
                검색
              </Button>
              <Button type="button" variant="ghost" onClick={clearFilters}>
                필터 초기화
              </Button>
            </div>
          </form>
        </div>

        {/* 강의 목록 */}
        {loading ? (
          <div className="flex justify-center py-16">
            <LoadingSpinner size="lg" message="강의를 불러오는 중..." />
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {lectures.length > 0 ? (
              lectures.map((lecture) => (
                <Link
                  key={lecture.id}
                  to={`/lectures/${lecture.id}`}
                  className="group"
                >
                  <div className="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                    {/* 강의 썸네일 */}
                    <div className="relative aspect-video bg-gradient-to-br from-blue-500 to-purple-600 overflow-hidden">
                      {lecture.thumbnail ? (
                        <img
                          src={lecture.thumbnail}
                          alt={lecture.title}
                          className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                        />
                      ) : (
                        <div className="flex items-center justify-center h-full">
                          <svg className="w-16 h-16 text-white opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                          </svg>
                        </div>
                      )}
                      
                      {/* 가격 배지 */}
                      <div className="absolute top-3 right-3">
                        <span className={`px-2 py-1 text-xs font-semibold rounded-full ${
                          lecture.price === 0 
                            ? 'bg-green-500 text-white' 
                            : 'bg-white text-gray-900'
                        }`}>
                          {formatPrice(lecture.price)}
                        </span>
                      </div>

                      {/* 재생 시간 */}
                      {lecture.duration && (
                        <div className="absolute bottom-3 right-3">
                          <span className="px-2 py-1 text-xs font-medium bg-black bg-opacity-70 text-white rounded">
                            {Math.floor(lecture.duration / 60)}분
                          </span>
                        </div>
                      )}
                    </div>

                    <div className="p-5">
                      {/* 강의 제목 */}
                      <h3 className="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors">
                        {lecture.title}
                      </h3>

                      {/* 강사 정보 */}
                      <div className="flex items-center mb-3">
                        <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2">
                          {lecture.instructor.profile_image ? (
                            <img
                              src={lecture.instructor.profile_image}
                              alt={lecture.instructor.nickname}
                              className="w-8 h-8 rounded-full object-cover"
                            />
                          ) : (
                            <svg className="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                          )}
                        </div>
                        <span className="text-sm text-gray-600">
                          {lecture.instructor.nickname}
                        </span>
                      </div>

                      {/* 강의 설명 */}
                      <p className="text-sm text-gray-600 mb-4 line-clamp-2">
                        {lecture.description}
                      </p>

                      {/* 통계 정보 */}
                      <div className="flex items-center justify-between text-xs text-gray-500">
                        <div className="flex items-center space-x-3">
                          <span className="flex items-center">
                            <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {lecture.views.toLocaleString()}
                          </span>
                          <span className="flex items-center">
                            <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {lecture.likes_count.toLocaleString()}
                          </span>
                          <span className="flex items-center">
                            <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                            {lecture.enrollment_count.toLocaleString()}
                          </span>
                        </div>
                        
                        {lecture.status === 'ACTIVE' ? (
                          <span className="text-green-600 font-medium">수강 가능</span>
                        ) : (
                          <span className="text-gray-400">수강 불가</span>
                        )}
                      </div>
                    </div>
                  </div>
                </Link>
              ))
            ) : (
              <div className="col-span-full text-center py-16">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  등록된 강의가 없습니다
                </h3>
                <p className="text-gray-600">
                  검색 조건을 변경하거나 필터를 초기화해보세요.
                </p>
              </div>
            )}
          </div>
        )}

        {/* 무료 강의 추천 섹션 */}
        {!loading && lectures.length > 0 && (
          <div className="mt-16">
            <div className="text-center mb-8">
              <h2 className="text-3xl font-bold text-gray-900 mb-4">
                지금 바로 시작할 수 있는 무료 강의
              </h2>
              <p className="text-xl text-gray-600">
                부담 없이 시작해보세요. 품질 높은 무료 강의들을 만나보세요.
              </p>
            </div>
            
            <div className="text-center">
              <Button
                onClick={() => {
                  setSelectedCategory('');
                  setPriceRange({ min: '0', max: '0' });
                  setSortBy('popular');
                }}
                size="lg"
                className="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700"
              >
                무료 강의 둘러보기
              </Button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default LecturesPage;