import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { Lecture, PaginatedResponse } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const LectureManagePage: React.FC = () => {
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [loading, setLoading] = useState(true);
  const [lectures, setLectures] = useState<Lecture[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [statusFilter, setStatusFilter] = useState<'ALL' | 'ACTIVE' | 'INACTIVE'>('ALL');
  const [sortBy, setSortBy] = useState<'latest' | 'popular' | 'earnings'>('latest');
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  // 통계 데이터
  const [stats, setStats] = useState({
    totalLectures: 0,
    activeLectures: 0,
    totalStudents: 0,
    totalEarnings: 0,
    avgRating: 0,
  });

  // 권한 체크
  useEffect(() => {
    if (!isAuthenticated || !user) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login');
      return;
    }

    if (user.role === 'ROLE_USER') {
      error('강의 관리 권한이 없습니다.');
      navigate('/');
      return;
    }
  }, [isAuthenticated, user]);

  // 강의 목록 및 통계 조회
  useEffect(() => {
    fetchLectures();
    fetchStats();
  }, [searchQuery, statusFilter, sortBy, currentPage]);

  // 강의 목록 조회
  const fetchLectures = async () => {
    if (!user) return;

    setLoading(true);
    try {
      const params: any = {
        instructor_id: user.role === 'ROLE_ADMIN' ? undefined : user.id,
        query: searchQuery || undefined,
        status: statusFilter === 'ALL' ? undefined : statusFilter,
        sort_by: sortBy === 'latest' ? 'created_at' : sortBy === 'popular' ? 'enrollment_count' : 'earnings',
        sort_direction: 'desc',
        page: currentPage,
        per_page: 10,
      };

      const response = await request<PaginatedResponse<Lecture>>({
        url: '/lectures',
        method: 'GET',
        params,
      });

      if (response.success && response.data) {
        setLectures(response.data.data);
        setTotalPages(response.data.meta.last_page);
      }
    } catch (err) {
      console.error('강의 목록 조회 실패:', err);
    } finally {
      setLoading(false);
    }
  };

  // 통계 조회
  const fetchStats = async () => {
    if (!user) return;

    try {
      const endpoint = user.role === 'ROLE_ADMIN' ? '/admin/lecture-stats' : '/my/lecture-stats';
      const response = await request<any>({
        url: endpoint,
        method: 'GET',
      });

      if (response.success && response.data) {
        setStats(response.data);
      }
    } catch (err) {
      console.error('통계 조회 실패:', err);
    }
  };

  // 강의 상태 변경
  const handleStatusChange = async (lectureId: number, newStatus: 'ACTIVE' | 'INACTIVE') => {
    try {
      const response = await request({
        url: `/lectures/${lectureId}`,
        method: 'PUT',
        data: { status: newStatus },
      });

      if (response.success) {
        success(`강의가 ${newStatus === 'ACTIVE' ? '활성화' : '비활성화'}되었습니다.`);
        setLectures(prev => prev.map(lecture => 
          lecture.id === lectureId ? { ...lecture, status: newStatus } : lecture
        ));
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '상태 변경에 실패했습니다.';
      error(errorMessage);
    }
  };

  // 강의 삭제
  const handleDelete = async (lectureId: number, lectureTitle: string) => {
    if (!window.confirm(`"${lectureTitle}" 강의를 삭제하시겠습니까?\n이 작업은 되돌릴 수 없습니다.`)) {
      return;
    }

    try {
      const response = await request({
        url: `/lectures/${lectureId}`,
        method: 'DELETE',
      });

      if (response.success) {
        success('강의가 삭제되었습니다.');
        setLectures(prev => prev.filter(lecture => lecture.id !== lectureId));
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '강의 삭제에 실패했습니다.';
      error(errorMessage);
    }
  };

  // 검색 처리
  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    fetchLectures();
  };

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    });
  };

  // 가격 포맷팅
  const formatPrice = (price: number) => {
    if (price === 0) return '무료';
    return `${price.toLocaleString()}원`;
  };

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 헤더 */}
      <div className="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-4xl font-bold mb-4">
                강의 관리
              </h1>
              <p className="text-xl text-blue-100">
                {user?.role === 'ROLE_ADMIN' ? '모든 강의를 관리하고' : '나의 강의를 관리하고'} 통계를 확인하세요.
              </p>
            </div>
            <Link to="/admin/lectures/create">
              <Button
                size="lg"
                className="bg-white text-blue-600 hover:bg-gray-100"
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
                  </svg>
                }
              >
                새 강의 등록
              </Button>
            </Link>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 통계 카드 */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">총 강의</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalLectures}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">활성 강의</p>
                <p className="text-2xl font-bold text-gray-900">{stats.activeLectures}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">총 수강생</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalStudents.toLocaleString()}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">총 수익</p>
                <p className="text-2xl font-bold text-gray-900">{stats.totalEarnings.toLocaleString()}원</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-xl shadow-sm p-6">
            <div className="flex items-center">
              <div className="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg className="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
              </div>
              <div className="ml-4">
                <p className="text-sm text-gray-600">평균 평점</p>
                <p className="text-2xl font-bold text-gray-900">{stats.avgRating.toFixed(1)}</p>
              </div>
            </div>
          </div>
        </div>

        {/* 검색 및 필터 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <form onSubmit={handleSearch} className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
              <div className="md:col-span-2">
                <Input
                  placeholder="강의 제목으로 검색"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  name="search"
                  leftIcon={
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                  }
                />
              </div>

              <select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value as any)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="ALL">전체 상태</option>
                <option value="ACTIVE">활성</option>
                <option value="INACTIVE">비활성</option>
              </select>

              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value as any)}
                className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="latest">최신순</option>
                <option value="popular">인기순</option>
                <option value="earnings">수익순</option>
              </select>
            </div>

            <div className="flex justify-end">
              <Button type="submit">검색</Button>
            </div>
          </form>
        </div>

        {/* 강의 목록 */}
        {loading ? (
          <div className="flex justify-center py-16">
            <LoadingSpinner size="lg" message="강의 목록을 불러오는 중..." />
          </div>
        ) : (
          <div className="bg-white rounded-xl shadow-sm overflow-hidden">
            {lectures.length > 0 ? (
              <>
                <div className="overflow-x-auto">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          강의 정보
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          가격
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          수강생
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          조회수
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          상태
                        </th>
                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                          등록일
                        </th>
                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                          액션
                        </th>
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                      {lectures.map((lecture) => (
                        <tr key={lecture.id} className="hover:bg-gray-50">
                          <td className="px-6 py-4 whitespace-nowrap">
                            <div className="flex items-center">
                              <div className="flex-shrink-0 h-12 w-20">
                                {lecture.thumbnail ? (
                                  <img
                                    className="h-12 w-20 rounded object-cover"
                                    src={lecture.thumbnail}
                                    alt={lecture.title}
                                  />
                                ) : (
                                  <div className="h-12 w-20 bg-gray-200 rounded flex items-center justify-center">
                                    <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                  </div>
                                )}
                              </div>
                              <div className="ml-4">
                                <div className="text-sm font-medium text-gray-900 line-clamp-2">
                                  <Link
                                    to={`/lectures/${lecture.id}`}
                                    className="hover:text-blue-600"
                                  >
                                    {lecture.title}
                                  </Link>
                                </div>
                                <div className="text-sm text-gray-500 line-clamp-1">
                                  {lecture.description}
                                </div>
                              </div>
                            </div>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {formatPrice(lecture.price)}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {lecture.enrollment_count.toLocaleString()}명
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {lecture.views.toLocaleString()}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap">
                            <select
                              value={lecture.status}
                              onChange={(e) => handleStatusChange(lecture.id, e.target.value as any)}
                              className={`text-xs font-medium px-2 py-1 rounded border-0 focus:ring-2 focus:ring-blue-500 ${
                                lecture.status === 'ACTIVE'
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-gray-100 text-gray-800'
                              }`}
                            >
                              <option value="ACTIVE">활성</option>
                              <option value="INACTIVE">비활성</option>
                            </select>
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {formatDate(lecture.created_at)}
                          </td>
                          <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div className="flex items-center justify-end space-x-2">
                              <Link to={`/admin/lectures/${lecture.id}/edit`}>
                                <Button variant="outline" size="sm">
                                  수정
                                </Button>
                              </Link>
                              <Button
                                variant="outline"
                                size="sm"
                                onClick={() => handleDelete(lecture.id, lecture.title)}
                                className="text-red-600 border-red-200 hover:bg-red-50"
                              >
                                삭제
                              </Button>
                            </div>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                {/* 페이지네이션 */}
                {totalPages > 1 && (
                  <div className="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div className="flex items-center justify-between">
                      <div className="text-sm text-gray-700">
                        페이지 {currentPage} / {totalPages}
                      </div>
                      <div className="flex space-x-2">
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                          disabled={currentPage === 1}
                        >
                          이전
                        </Button>
                        <Button
                          variant="outline"
                          size="sm"
                          onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                          disabled={currentPage === totalPages}
                        >
                          다음
                        </Button>
                      </div>
                    </div>
                  </div>
                )}
              </>
            ) : (
              <div className="text-center py-16">
                <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z" />
                  </svg>
                </div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  등록된 강의가 없습니다
                </h3>
                <p className="text-gray-600 mb-6">
                  첫 번째 강의를 등록해보세요!
                </p>
                <Link to="/admin/lectures/create">
                  <Button className="bg-blue-600 hover:bg-blue-700">
                    강의 등록하기
                  </Button>
                </Link>
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default LectureManagePage;