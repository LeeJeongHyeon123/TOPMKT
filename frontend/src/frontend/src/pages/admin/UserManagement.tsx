import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../hooks/useToast';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';

// 타입 정의
interface AdminUser {
  id: number;
  nickname: string;
  email: string;
  phone: string;
  role: 'ROLE_USER' | 'ROLE_CORP' | 'ROLE_ADMIN';
  phone_verified: boolean;
  email_verified: boolean;
  created_at: string;
  last_login?: string;
  status: 'active' | 'suspended' | 'banned';
  post_count: number;
  comment_count: number;
}

const UserManagement: React.FC = () => {
  const [users, setUsers] = useState<AdminUser[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState('');
  const [roleFilter, setRoleFilter] = useState<string>('all');
  const [statusFilter, setStatusFilter] = useState<string>('all');
  // const [currentPage] = useState(1);
  // const [totalPages] = useState(1);
  const [selectedUsers, setSelectedUsers] = useState<number[]>([]);
  
  const { user, isAuthenticated } = useAuth();
  const { success, error: showError } = useToast();

  // 권한 확인
  useEffect(() => {
    if (!isAuthenticated || user?.role !== 'ROLE_ADMIN') {
      showError('관리자 권한이 필요합니다.');
      return;
    }
  }, [isAuthenticated, user]);

  // 사용자 목록 로드
  useEffect(() => {
    const loadUsers = async () => {
      try {
        setLoading(true);
        
        // 임시 사용자 데이터
        const mockUsers: AdminUser[] = [
          {
            id: 1,
            nickname: '김마케터',
            email: 'kim@example.com',
            phone: '010-1234-5678',
            role: 'ROLE_USER',
            phone_verified: true,
            email_verified: true,
            created_at: '2024-01-15',
            last_login: '2024-01-26 14:30:00',
            status: 'active',
            post_count: 23,
            comment_count: 67
          },
          {
            id: 2,
            nickname: '이성공',
            email: 'lee@example.com',
            phone: '010-2345-6789',
            role: 'ROLE_CORP',
            phone_verified: true,
            email_verified: true,
            created_at: '2024-01-10',
            last_login: '2024-01-26 13:45:00',
            status: 'active',
            post_count: 45,
            comment_count: 123
          },
          {
            id: 3,
            nickname: '박관리자',
            email: 'admin@example.com',
            phone: '010-3456-7890',
            role: 'ROLE_ADMIN',
            phone_verified: true,
            email_verified: true,
            created_at: '2024-01-01',
            last_login: '2024-01-26 15:20:00',
            status: 'active',
            post_count: 5,
            comment_count: 12
          },
          {
            id: 4,
            nickname: '최정지',
            email: 'suspended@example.com',
            phone: '010-4567-8901',
            role: 'ROLE_USER',
            phone_verified: true,
            email_verified: false,
            created_at: '2024-01-20',
            last_login: '2024-01-25 10:30:00',
            status: 'suspended',
            post_count: 8,
            comment_count: 15
          }
        ];

        setUsers(mockUsers);
        // setTotalPages(1); // 임시로 1페이지
      } catch (error) {
        console.error('사용자 목록 로드 실패:', error);
        showError('사용자 목록을 불러오는데 실패했습니다.');
      } finally {
        setLoading(false);
      }
    };

    if (isAuthenticated && user?.role === 'ROLE_ADMIN') {
      loadUsers();
    }
  }, [isAuthenticated, user, searchQuery, roleFilter, statusFilter]);

  // 필터링된 사용자 목록
  const filteredUsers = users.filter(user => {
    const matchesSearch = user.nickname.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         user.email.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesRole = roleFilter === 'all' || user.role === roleFilter;
    const matchesStatus = statusFilter === 'all' || user.status === statusFilter;
    
    return matchesSearch && matchesRole && matchesStatus;
  });

  // 체크박스 선택 처리
  const handleSelectUser = (userId: number) => {
    setSelectedUsers(prev => 
      prev.includes(userId) 
        ? prev.filter(id => id !== userId)
        : [...prev, userId]
    );
  };

  const handleSelectAll = () => {
    if (selectedUsers.length === filteredUsers.length) {
      setSelectedUsers([]);
    } else {
      setSelectedUsers(filteredUsers.map(user => user.id));
    }
  };

  // 사용자 상태 변경
  const changeUserStatus = async (userId: number, newStatus: 'active' | 'suspended' | 'banned') => {
    try {
      // API 호출 시뮬레이션
      setUsers(prev => prev.map(user => 
        user.id === userId ? { ...user, status: newStatus } : user
      ));
      
      success(`사용자 상태가 ${newStatus}로 변경되었습니다.`);
    } catch (error) {
      showError('사용자 상태 변경에 실패했습니다.');
    }
  };

  // 사용자 역할 변경
  const changeUserRole = async (userId: number, newRole: 'ROLE_USER' | 'ROLE_CORP' | 'ROLE_ADMIN') => {
    try {
      // API 호출 시뮬레이션
      setUsers(prev => prev.map(user => 
        user.id === userId ? { ...user, role: newRole } : user
      ));
      
      success(`사용자 역할이 변경되었습니다.`);
    } catch (error) {
      showError('사용자 역할 변경에 실패했습니다.');
    }
  };

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  // 역할 표시
  const getRoleDisplay = (role: string) => {
    switch (role) {
      case 'ROLE_ADMIN':
        return <span className="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">👑 관리자</span>;
      case 'ROLE_CORP':
        return <span className="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">🏢 기업</span>;
      default:
        return <span className="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">👤 일반</span>;
    }
  };

  // 상태 표시
  const getStatusDisplay = (status: string) => {
    switch (status) {
      case 'active':
        return <span className="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">✅ 활성</span>;
      case 'suspended':
        return <span className="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">⏸️ 정지</span>;
      case 'banned':
        return <span className="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">🚫 차단</span>;
      default:
        return <span className="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">❓ 알 수 없음</span>;
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
        <LoadingSpinner size="lg" message="사용자 목록 로딩 중..." />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* 네비게이션 */}
      <div className="mb-6">
        <Link
          to="/admin"
          className="inline-flex items-center text-blue-600 hover:text-blue-700 transition-colors"
        >
          <i className="fas fa-arrow-left mr-2"></i>
          관리자 대시보드로 돌아가기
        </Link>
      </div>

      {/* 헤더 */}
      <div className="bg-gradient-to-r from-blue-800 to-blue-700 text-white rounded-2xl shadow-lg p-8 mb-8">
        <div className="flex items-center space-x-4">
          <div className="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <span className="text-2xl">👥</span>
          </div>
          <div>
            <h1 className="text-3xl font-bold">사용자 관리</h1>
            <p className="text-blue-100 mt-2">
              등록된 사용자들을 관리하고 권한을 설정하세요.
            </p>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* 검색 및 필터 */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div className="md:col-span-2">
              <Input
                placeholder="사용자 검색 (닉네임, 이메일)"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                leftIcon={<i className="fas fa-search"></i>}
              />
            </div>
            <div>
              <select
                value={roleFilter}
                onChange={(e) => setRoleFilter(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
              >
                <option value="all">모든 역할</option>
                <option value="ROLE_USER">일반 사용자</option>
                <option value="ROLE_CORP">기업 회원</option>
                <option value="ROLE_ADMIN">관리자</option>
              </select>
            </div>
            <div>
              <select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-800 focus:border-transparent"
              >
                <option value="all">모든 상태</option>
                <option value="active">활성</option>
                <option value="suspended">정지</option>
                <option value="banned">차단</option>
              </select>
            </div>
          </div>
        </div>

        {/* 사용자 목록 */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200">
          <div className="p-6 border-b border-gray-200">
            <div className="flex items-center justify-between">
              <h2 className="text-lg font-semibold text-gray-900">
                사용자 목록 ({filteredUsers.length}명)
              </h2>
              <div className="flex items-center space-x-4">
                {selectedUsers.length > 0 && (
                  <span className="text-sm text-gray-600">
                    {selectedUsers.length}명 선택됨
                  </span>
                )}
                <Button size="sm" variant="outline">
                  <i className="fas fa-download mr-2"></i>
                  엑셀 다운로드
                </Button>
              </div>
            </div>
          </div>

          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    <input
                      type="checkbox"
                      checked={selectedUsers.length === filteredUsers.length && filteredUsers.length > 0}
                      onChange={handleSelectAll}
                      className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    />
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    사용자 정보
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    역할
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    상태
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    활동
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    가입일
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    마지막 로그인
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    관리
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {filteredUsers.map((user) => (
                  <tr key={user.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <input
                        type="checkbox"
                        checked={selectedUsers.includes(user.id)}
                        onChange={() => handleSelectUser(user.id)}
                        className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                      />
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="flex-shrink-0 h-10 w-10">
                          <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <span className="text-sm font-medium text-blue-800">
                              {user.nickname.charAt(0)}
                            </span>
                          </div>
                        </div>
                        <div className="ml-4">
                          <div className="text-sm font-medium text-gray-900">{user.nickname}</div>
                          <div className="text-sm text-gray-500">{user.email}</div>
                          <div className="text-xs text-gray-400">{user.phone}</div>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getRoleDisplay(user.role)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {getStatusDisplay(user.status)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div>게시글: {user.post_count}개</div>
                      <div>댓글: {user.comment_count}개</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {formatDate(user.created_at)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {user.last_login ? formatDate(user.last_login) : '로그인 기록 없음'}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <div className="flex items-center space-x-2">
                        <select
                          value={user.status}
                          onChange={(e) => changeUserStatus(user.id, e.target.value as any)}
                          className="text-xs border border-gray-300 rounded px-2 py-1"
                        >
                          <option value="active">활성</option>
                          <option value="suspended">정지</option>
                          <option value="banned">차단</option>
                        </select>
                        <select
                          value={user.role}
                          onChange={(e) => changeUserRole(user.id, e.target.value as any)}
                          className="text-xs border border-gray-300 rounded px-2 py-1"
                        >
                          <option value="ROLE_USER">일반</option>
                          <option value="ROLE_CORP">기업</option>
                          <option value="ROLE_ADMIN">관리자</option>
                        </select>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default UserManagement;