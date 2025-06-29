import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../hooks/useToast';
import LoadingSpinner from '../../components/common/LoadingSpinner';

// 타입 정의
interface DashboardStats {
  totalUsers: number;
  activeUsers: number;
  totalPosts: number;
  totalLectures: number;
  totalEvents: number;
  monthlyGrowth: number;
}

interface RecentActivity {
  id: string;
  type: 'user_register' | 'post_create' | 'lecture_enroll' | 'event_register';
  description: string;
  user: string;
  timestamp: string;
}

const AdminDashboard: React.FC = () => {
  const [stats, setStats] = useState<DashboardStats | null>(null);
  const [recentActivities, setRecentActivities] = useState<RecentActivity[]>([]);
  const [loading, setLoading] = useState(true);
  const { user, isAuthenticated } = useAuth();
  const { error: showError } = useToast();

  // 권한 확인
  useEffect(() => {
    if (!isAuthenticated || user?.role !== 'ROLE_ADMIN') {
      showError('관리자 권한이 필요합니다.');
      return;
    }
  }, [isAuthenticated, user]);

  // 대시보드 데이터 로드
  useEffect(() => {
    const loadDashboardData = async () => {
      try {
        setLoading(true);
        
        // 임시 통계 데이터
        const mockStats: DashboardStats = {
          totalUsers: 1247,
          activeUsers: 892,
          totalPosts: 3456,
          totalLectures: 42,
          totalEvents: 18,
          monthlyGrowth: 15.7
        };

        // 임시 최근 활동 데이터
        const mockActivities: RecentActivity[] = [
          {
            id: '1',
            type: 'user_register',
            description: '새로운 사용자가 가입했습니다',
            user: '김신규',
            timestamp: '2024-01-26 15:30:00'
          },
          {
            id: '2',
            type: 'post_create',
            description: '새로운 게시글이 작성되었습니다',
            user: '이마케터',
            timestamp: '2024-01-26 14:45:00'
          },
          {
            id: '3',
            type: 'lecture_enroll',
            description: 'SNS 마케팅 강의에 수강 신청했습니다',
            user: '박학습자',
            timestamp: '2024-01-26 13:20:00'
          },
          {
            id: '4',
            type: 'event_register',
            description: '글로벌 컨퍼런스에 참가 신청했습니다',
            user: '최참가자',
            timestamp: '2024-01-26 12:10:00'
          }
        ];

        setStats(mockStats);
        setRecentActivities(mockActivities);
      } catch (error) {
        console.error('대시보드 데이터 로드 실패:', error);
        showError('대시보드 데이터를 불러오는데 실패했습니다.');
      } finally {
        setLoading(false);
      }
    };

    if (isAuthenticated && user?.role === 'ROLE_ADMIN') {
      loadDashboardData();
    }
  }, [isAuthenticated, user]);

  const formatTimestamp = (timestamp: string) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diffTime = Math.abs(now.getTime() - date.getTime());
    const diffHours = Math.ceil(diffTime / (1000 * 60 * 60));

    if (diffHours < 1) {
      return '방금 전';
    } else if (diffHours < 24) {
      return `${diffHours}시간 전`;
    } else {
      return date.toLocaleDateString('ko-KR', {
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    }
  };

  const getActivityIcon = (type: string) => {
    switch (type) {
      case 'user_register':
        return 'fas fa-user-plus';
      case 'post_create':
        return 'fas fa-edit';
      case 'lecture_enroll':
        return 'fas fa-graduation-cap';
      case 'event_register':
        return 'fas fa-calendar-check';
      default:
        return 'fas fa-info-circle';
    }
  };

  if (!isAuthenticated || user?.role !== 'ROLE_ADMIN') {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="text-6xl mb-4">🚫</div>
          <h2 className="text-2xl font-bold text-gray-900 mb-4">접근 권한이 없습니다</h2>
          <p className="text-gray-600 mb-6">관리자만 접근할 수 있는 페이지입니다.</p>
          <Link to="/" className="bg-blue-800 text-white px-6 py-3 rounded-lg hover:bg-blue-900 transition-colors">
            홈으로 돌아가기
          </Link>
        </div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="대시보드 로딩 중..." />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 헤더 */}
      <div className="bg-gradient-to-r from-blue-800 to-blue-700 text-white rounded-2xl shadow-lg p-8 mb-8">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <div className="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
              <span className="text-2xl">👑</span>
            </div>
            <div>
              <h1 className="text-3xl font-bold">관리자 대시보드</h1>
              <p className="text-blue-100 mt-2">
                탑마케팅 플랫폼의 전체 현황을 확인하고 관리하세요.
              </p>
            </div>
          </div>
          <div className="text-right">
            <p className="text-blue-100">관리자</p>
            <p className="text-xl font-semibold">{user?.nickname}</p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* 통계 카드 */}
        {stats && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-users text-blue-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">전체 사용자</p>
                  <p className="text-2xl font-semibold text-gray-900">{stats.totalUsers.toLocaleString()}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-user-check text-green-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">활성 사용자</p>
                  <p className="text-2xl font-semibold text-gray-900">{stats.activeUsers.toLocaleString()}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-edit text-purple-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">총 게시글</p>
                  <p className="text-2xl font-semibold text-gray-900">{stats.totalPosts.toLocaleString()}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-graduation-cap text-yellow-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">총 강의</p>
                  <p className="text-2xl font-semibold text-gray-900">{stats.totalLectures}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-calendar-alt text-red-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">총 이벤트</p>
                  <p className="text-2xl font-semibold text-gray-900">{stats.totalEvents}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div className="flex items-center">
                <div className="flex-shrink-0">
                  <div className="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i className="fas fa-chart-line text-indigo-600"></i>
                  </div>
                </div>
                <div className="ml-4">
                  <p className="text-sm font-medium text-gray-500">월간 성장률</p>
                  <p className="text-2xl font-semibold text-gray-900">+{stats.monthlyGrowth}%</p>
                </div>
              </div>
            </div>
          </div>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
          {/* 빠른 액션 */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">빠른 관리</h3>
            <div className="grid grid-cols-2 gap-4">
              <Link
                to="/admin/users"
                className="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
              >
                <i className="fas fa-users text-2xl text-blue-600 mb-2"></i>
                <span className="text-sm font-medium text-blue-800">사용자 관리</span>
              </Link>
              <Link
                to="/admin/posts"
                className="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
              >
                <i className="fas fa-edit text-2xl text-green-600 mb-2"></i>
                <span className="text-sm font-medium text-green-800">게시글 관리</span>
              </Link>
              <Link
                to="/admin/lectures"
                className="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors"
              >
                <i className="fas fa-graduation-cap text-2xl text-yellow-600 mb-2"></i>
                <span className="text-sm font-medium text-yellow-800">강의 관리</span>
              </Link>
              <Link
                to="/admin/events"
                className="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
              >
                <i className="fas fa-calendar-alt text-2xl text-red-600 mb-2"></i>
                <span className="text-sm font-medium text-red-800">이벤트 관리</span>
              </Link>
            </div>
          </div>

          {/* 최근 활동 */}
          <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 className="text-lg font-semibold text-gray-900 mb-4">최근 활동</h3>
            <div className="space-y-4">
              {recentActivities.map((activity) => (
                <div key={activity.id} className="flex items-start space-x-3">
                  <div className="flex-shrink-0">
                    <div className="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                      <i className={`${getActivityIcon(activity.type)} text-gray-600 text-sm`}></i>
                    </div>
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm text-gray-900">
                      <span className="font-medium">{activity.user}</span>님이 {activity.description}
                    </p>
                    <p className="text-xs text-gray-500">{formatTimestamp(activity.timestamp)}</p>
                  </div>
                </div>
              ))}
            </div>
            <div className="mt-4 pt-4 border-t border-gray-200">
              <Link
                to="/admin/activities"
                className="text-sm text-blue-600 hover:text-blue-800 font-medium"
              >
                모든 활동 보기 →
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AdminDashboard;