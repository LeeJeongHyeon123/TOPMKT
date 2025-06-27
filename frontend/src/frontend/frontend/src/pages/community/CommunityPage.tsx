import React, { useState, useEffect } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import { Post, PostSearchParams, PaginatedResponse } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const CommunityPage: React.FC = () => {
  const [searchParams, setSearchParams] = useSearchParams();
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState(searchParams.get('search') || '');
  const [sortBy, setSortBy] = useState(searchParams.get('sort') || 'latest');
  const [currentPage, setCurrentPage] = useState(parseInt(searchParams.get('page') || '1'));

  const { isAuthenticated } = useAuth();
  const { request } = useApi();

  // 게시글 목록 조회
  const fetchPosts = async (params: PostSearchParams = {}) => {
    setLoading(true);
    try {
      const queryParams = {
        query: searchQuery,
        sort_by: sortBy === 'latest' ? 'created_at' : sortBy === 'popular' ? 'likes_count' : 'views',
        sort_direction: 'desc',
        page: currentPage,
        per_page: 10,
        status: 'PUBLISHED',
        ...params,
      };

      const response = await request<PaginatedResponse<Post>>({
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
  }, [searchQuery, sortBy, currentPage]);

  // 검색 처리
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    const newParams = new URLSearchParams();
    if (searchQuery) newParams.set('search', searchQuery);
    if (sortBy !== 'latest') newParams.set('sort', sortBy);
    newParams.set('page', '1');
    setSearchParams(newParams);
  };

  // 정렬 변경
  const handleSortChange = (newSort: string) => {
    setSortBy(newSort);
    setCurrentPage(1);
    const newParams = new URLSearchParams();
    if (searchQuery) newParams.set('search', searchQuery);
    if (newSort !== 'latest') newParams.set('sort', newSort);
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
    <div className="min-h-screen bg-gray-50">
      {/* 헤더 섹션 */}
      <div className="bg-gradient-to-r from-green-600 to-blue-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="text-center">
            <h1 className="text-4xl font-bold mb-4">
              커뮤니티
            </h1>
            <p className="text-xl text-green-100 max-w-2xl mx-auto">
              네트워크 마케팅 전문가들과 소통하고, 경험을 공유하며, 함께 성장해보세요.
            </p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 검색 및 액션 바 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-8">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            {/* 검색 */}
            <form onSubmit={handleSearch} className="flex-1 max-w-md">
              <Input
                placeholder="게시글 제목이나 내용으로 검색"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                name="search"
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                }
                rightIcon={
                  <button type="submit" className="p-1">
                    <svg className="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  </button>
                }
              />
            </form>

            {/* 글쓰기 버튼 */}
            {isAuthenticated && (
              <Link to="/community/write">
                <Button
                  leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                    </svg>
                  }
                  className="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700"
                >
                  글쓰기
                </Button>
              </Link>
            )}
          </div>

          {/* 정렬 옵션 */}
          <div className="mt-4 pt-4 border-t border-gray-200">
            <div className="flex space-x-1">
              {[
                { value: 'latest', label: '최신순' },
                { value: 'popular', label: '인기순' },
                { value: 'views', label: '조회순' },
              ].map((option) => (
                <button
                  key={option.value}
                  onClick={() => handleSortChange(option.value)}
                  className={`px-4 py-2 text-sm font-medium rounded-lg transition-colors ${
                    sortBy === option.value
                      ? 'bg-blue-100 text-blue-700'
                      : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
                  }`}
                >
                  {option.label}
                </button>
              ))}
            </div>
          </div>
        </div>

        {/* 게시글 목록 */}
        {loading ? (
          <div className="flex justify-center py-16">
            <LoadingSpinner size="lg" message="게시글을 불러오는 중..." />
          </div>
        ) : (
          <div className="space-y-4">
            {posts.length > 0 ? (
              posts.map((post) => (
                <Link
                  key={post.id}
                  to={`/community/${post.id}`}
                  className="block group"
                >
                  <div className="bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 p-6">
                    <div className="flex items-start justify-between">
                      {/* 메인 컨텐츠 */}
                      <div className="flex-1 min-w-0">
                        {/* 제목 */}
                        <h3 className="text-xl font-semibold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors">
                          {post.title}
                        </h3>

                        {/* 내용 미리보기 */}
                        <p className="text-gray-600 mb-4 line-clamp-2">
                          {getPreviewText(post.content)}
                        </p>

                        {/* 작성자 및 메타 정보 */}
                        <div className="flex items-center justify-between">
                          <div className="flex items-center">
                            <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                              {post.user.profile_image ? (
                                <img
                                  src={post.user.profile_image}
                                  alt={post.user.nickname}
                                  className="w-8 h-8 rounded-full object-cover"
                                />
                              ) : (
                                <svg className="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                              )}
                            </div>
                            <div>
                              <div className="text-sm font-medium text-gray-900">
                                {post.user.nickname}
                              </div>
                              <div className="text-xs text-gray-500">
                                {formatDate(post.created_at)}
                              </div>
                            </div>
                          </div>

                          {/* 통계 정보 */}
                          <div className="flex items-center space-x-4 text-sm text-gray-500">
                            <span className="flex items-center">
                              <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                              </svg>
                              {post.views.toLocaleString()}
                            </span>
                            <span className="flex items-center">
                              <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                              </svg>
                              {post.likes_count.toLocaleString()}
                            </span>
                            <span className="flex items-center">
                              <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                              </svg>
                              {post.comments_count.toLocaleString()}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </Link>
              ))
            ) : (
              <div className="text-center py-16">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  등록된 게시글이 없습니다
                </h3>
                <p className="text-gray-600 mb-6">
                  첫 번째 게시글을 작성해보세요!
                </p>
                {isAuthenticated ? (
                  <Link to="/community/write">
                    <Button className="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700">
                      첫 게시글 작성하기
                    </Button>
                  </Link>
                ) : (
                  <Link to="/auth/login">
                    <Button variant="outline">로그인하고 참여하기</Button>
                  </Link>
                )}
              </div>
            )}
          </div>
        )}

        {/* 인기 토픽 섹션 */}
        <div className="mt-16">
          <div className="bg-white rounded-xl shadow-sm p-6">
            <h2 className="text-2xl font-bold text-gray-900 mb-6">
              인기 토픽
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[
                {
                  title: '마케팅 전략',
                  description: '효과적인 네트워크 마케팅 전략을 공유해보세요.',
                  icon: '🎯',
                  color: 'bg-blue-100 text-blue-700',
                },
                {
                  title: '성공 사례',
                  description: '실제 성공한 마케터들의 경험담을 들어보세요.',
                  icon: '🏆',
                  color: 'bg-yellow-100 text-yellow-700',
                },
                {
                  title: '질문과 답변',
                  description: '궁금한 점이 있다면 언제든 질문해주세요.',
                  icon: '💡',
                  color: 'bg-green-100 text-green-700',
                },
                {
                  title: '도구 및 팁',
                  description: '유용한 마케팅 도구와 팁을 공유해보세요.',
                  icon: '🛠️',
                  color: 'bg-purple-100 text-purple-700',
                },
                {
                  title: '이벤트 소식',
                  description: '다양한 마케팅 이벤트 정보를 확인하세요.',
                  icon: '🎉',
                  color: 'bg-pink-100 text-pink-700',
                },
                {
                  title: '자유 토론',
                  description: '자유롭게 의견을 나누는 공간입니다.',
                  icon: '💬',
                  color: 'bg-indigo-100 text-indigo-700',
                },
              ].map((topic, index) => (
                <div
                  key={index}
                  className="p-4 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors cursor-pointer"
                >
                  <div className={`w-12 h-12 rounded-lg ${topic.color} flex items-center justify-center mb-3`}>
                    <span className="text-2xl">{topic.icon}</span>
                  </div>
                  <h3 className="font-semibold text-gray-900 mb-2">
                    {topic.title}
                  </h3>
                  <p className="text-sm text-gray-600">
                    {topic.description}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* 커뮤니티 가이드라인 */}
        <div className="mt-8">
          <div className="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-3">
              💝 커뮤니티 가이드라인
            </h3>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
              <div className="space-y-2">
                <div className="flex items-center">
                  <span className="text-green-600 mr-2">✓</span>
                  서로를 존중하며 예의바른 소통을 해주세요
                </div>
                <div className="flex items-center">
                  <span className="text-green-600 mr-2">✓</span>
                  건설적이고 도움이 되는 내용을 공유해주세요
                </div>
                <div className="flex items-center">
                  <span className="text-green-600 mr-2">✓</span>
                  질문할 때는 구체적으로 작성해주세요
                </div>
              </div>
              <div className="space-y-2">
                <div className="flex items-center">
                  <span className="text-red-500 mr-2">✗</span>
                  스팸, 광고성 게시글은 삼가해주세요
                </div>
                <div className="flex items-center">
                  <span className="text-red-500 mr-2">✗</span>
                  개인정보나 민감한 정보 공유는 주의해주세요
                </div>
                <div className="flex items-center">
                  <span className="text-red-500 mr-2">✗</span>
                  욕설이나 비방은 절대 금지입니다
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CommunityPage;