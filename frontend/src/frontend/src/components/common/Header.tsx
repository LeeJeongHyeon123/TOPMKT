import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import Button from './Button';
import { cn } from '../../utils/cn';

const Header: React.FC = () => {
  const { user, isAuthenticated, logout } = useAuth();
  const { success } = useToast();
  const navigate = useNavigate();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  const handleLogout = async () => {
    try {
      await logout();
      success('성공적으로 로그아웃되었습니다.');
      navigate('/');
    } catch (error) {
      console.error('Logout error:', error);
    }
  };

  const navItems = [
    { name: '홈', path: '/', public: true },
    { name: '커뮤니티', path: '/community', public: true },
    { name: '강의 일정', path: '/lectures', public: true },
    { name: '행사 일정', path: '/events', public: true },
  ];

  const toggleMenu = () => setIsMenuOpen(!isMenuOpen);

  return (
    <header className="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* 로고 */}
          <div className="flex-shrink-0">
            <h1 className="logo">
              <Link to="/" className="logo-link">
                <div className="logo-icon">
                  <i className="fas fa-rocket header-rocket"></i>
                </div>
                <span className="logo-text">탑마케팅</span>
              </Link>
            </h1>
          </div>

          {/* 데스크톱 네비게이션 */}
          <nav className="hidden md:flex space-x-8">
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                className="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors"
              >
                {item.name}
              </Link>
            ))}
          </nav>

          {/* 사용자 메뉴 */}
          <div className="hidden md:flex items-center space-x-4">
            {isAuthenticated && user ? (
              <div className="flex items-center space-x-4">
                {/* 사용자 정보 */}
                <div className="flex items-center space-x-3">
                  <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    {user.profile_image_thumb ? (
                      <img
                        src={user.profile_image_thumb}
                        alt={user.nickname}
                        className="w-8 h-8 rounded-full object-cover"
                      />
                    ) : (
                      <span className="text-blue-600 text-sm font-medium">
                        {user.nickname.charAt(0).toUpperCase()}
                      </span>
                    )}
                  </div>
                  <span className="text-sm font-medium text-gray-700">
                    {user.nickname}
                  </span>
                </div>

                {/* 메뉴 버튼들 */}
                <div className="relative">
                  <div className="flex items-center space-x-6">
                    <Link
                      to="/profile"
                      className="text-gray-600 hover:text-blue-600 text-sm font-medium transition-colors px-3 py-2 rounded-lg hover:bg-gray-50"
                    >
                      프로필
                    </Link>
                    <button
                      onClick={() => {
                        // 채팅 기능 - Firebase 채팅창 열기
                        window.open('/chat', 'chat', 'width=400,height=600,scrollbars=yes,resizable=yes');
                      }}
                      className="text-gray-600 hover:text-green-600 text-sm font-medium transition-colors px-3 py-2 rounded-lg hover:bg-gray-50 bg-transparent border-none cursor-pointer"
                      title="채팅"
                    >
                      채팅
                    </button>
                    <button
                      onClick={handleLogout}
                      className="text-gray-600 hover:text-red-600 text-sm font-medium transition-colors px-3 py-2 rounded-lg hover:bg-gray-50 bg-transparent border-none cursor-pointer"
                    >
                      로그아웃
                    </button>
                  </div>
                </div>
              </div>
            ) : (
              <div className="flex items-center space-x-3">
                <Link to="/login">
                  <Button variant="ghost" size="sm">
                    로그인
                  </Button>
                </Link>
                <Link to="/signup">
                  <Button variant="primary" size="sm">
                    회원가입
                  </Button>
                </Link>
              </div>
            )}
          </div>

          {/* 모바일 메뉴 버튼 */}
          <div className="md:hidden">
            <button
              onClick={toggleMenu}
              className="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2"
            >
              <svg
                className="h-6 w-6"
                fill="none"
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                {isMenuOpen ? (
                  <path d="M6 18L18 6M6 6l12 12" />
                ) : (
                  <path d="M4 6h16M4 12h16M4 18h16" />
                )}
              </svg>
            </button>
          </div>
        </div>

        {/* 모바일 메뉴 */}
        <div className={cn(
          'md:hidden transition-all duration-300 ease-in-out overflow-hidden',
          isMenuOpen ? 'max-h-96 pb-4' : 'max-h-0'
        )}>
          <div className="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200 mt-2">
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                className="block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors"
                onClick={() => setIsMenuOpen(false)}
              >
                {item.name}
              </Link>
            ))}
            
            <div className="border-t border-gray-200 pt-3 mt-3">
              {isAuthenticated && user ? (
                <div className="space-y-2">
                  <div className="flex items-center px-3 py-2">
                    <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                      {user.profile_image_thumb ? (
                        <img
                          src={user.profile_image_thumb}
                          alt={user.nickname}
                          className="w-10 h-10 rounded-full object-cover"
                        />
                      ) : (
                        <span className="text-blue-600 font-medium">
                          {user.nickname.charAt(0).toUpperCase()}
                        </span>
                      )}
                    </div>
                    <div>
                      <div className="text-sm font-medium text-gray-900">
                        {user.nickname}
                      </div>
                      <div className="text-xs text-gray-500">
                        {user.email}
                      </div>
                    </div>
                  </div>
                  <Link
                    to="/profile"
                    className="block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    프로필
                  </Link>
                  <button
                    onClick={() => {
                      window.open('/chat', 'chat', 'width=400,height=600,scrollbars=yes,resizable=yes');
                      setIsMenuOpen(false);
                    }}
                    className="block w-full text-left px-3 py-2 text-base font-medium text-gray-600 hover:text-green-600 hover:bg-gray-50 rounded-md transition-colors"
                  >
                    채팅
                  </button>
                  <button
                    onClick={() => {
                      handleLogout();
                      setIsMenuOpen(false);
                    }}
                    className="block w-full text-left px-3 py-2 text-base font-medium text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded-md transition-colors"
                  >
                    로그아웃
                  </button>
                </div>
              ) : (
                <div className="space-y-2">
                  <Link
                    to="/login"
                    className="block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    로그인
                  </Link>
                  <Link
                    to="/signup"
                    className="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    회원가입
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* 로고 애니메이션 스타일 */}
      <style>{`
        /* 🚀 헤더 로켓 애니메이션 */
        .header-rocket {
          display: inline-block;
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          transform-origin: center bottom;
          position: relative;
          color: #3b82f6;
          font-size: 1.8rem;
        }
        
        /* 페이지 로딩 시 로켓 착륙 애니메이션 */
        .header-rocket {
          animation: rocketLanding 2.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards,
                     headerRocketFloat 4s ease-in-out infinite 2.5s;
        }
        
        @keyframes rocketLanding {
          0% {
            transform: translateX(-150vw) translateY(-50vh) rotate(-45deg) scale(0.3);
            opacity: 0;
            filter: blur(3px);
          }
          20% {
            opacity: 0.3;
            filter: blur(2px);
          }
          40% {
            transform: translateX(-80vw) translateY(-20vh) rotate(-30deg) scale(0.5);
            opacity: 0.6;
            filter: blur(1px);
          }
          60% {
            transform: translateX(-20vw) translateY(-5vh) rotate(-15deg) scale(0.8);
            opacity: 0.8;
            filter: blur(0.5px);
          }
          80% {
            transform: translateX(-5vw) translateY(-1vh) rotate(-5deg) scale(0.95);
            opacity: 0.9;
            filter: blur(0px);
          }
          90% {
            transform: translateX(0) translateY(2px) rotate(5deg) scale(1.1);
            opacity: 1;
          }
          95% {
            transform: translateX(0) translateY(-2px) rotate(-2deg) scale(1.05);
          }
          100% {
            transform: translateX(0) translateY(0) rotate(0deg) scale(1);
            opacity: 1;
            filter: blur(0px);
          }
        }
        
        /* 기본 헤더 로켓 애니메이션 - 우주에서 떠다니는 느낌 (착륙 후) */
        @keyframes headerRocketFloat {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          20% {
            transform: translateY(-2px) rotate(3deg);
          }
          40% {
            transform: translateY(-4px) rotate(0deg);
          }
          60% {
            transform: translateY(-2px) rotate(-3deg);
          }
          80% {
            transform: translateY(-1px) rotate(1deg);
          }
        }
        
        /* 로고 링크 호버 시 로켓 특수 효과 */
        .logo-link {
          position: relative;
          text-decoration: none;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
        }
        
        /* 착륙 시 추진 효과 */
        .logo-icon::before {
          content: '';
          position: absolute;
          bottom: -8px;
          left: 50%;
          width: 0;
          height: 0;
          background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, #ef4444, transparent);
          transform: translateX(-50%);
          opacity: 0;
          transition: all 0.3s ease;
          animation: landingThruster 2.5s ease-out;
        }
        
        .logo-icon::after {
          content: '💨';
          position: absolute;
          left: -35px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0;
          font-size: 0.8rem;
          animation: landingSmoke 2.5s ease-out;
        }
        
        /* 착륙 추진 효과 애니메이션 */
        @keyframes landingThruster {
          0%, 70% {
            width: 0;
            height: 0;
            opacity: 0;
          }
          75% {
            width: 30px;
            height: 3px;
            opacity: 0.8;
            box-shadow: 0 0 10px #fbbf24, 0 0 20px #f59e0b;
          }
          85% {
            width: 40px;
            height: 5px;
            opacity: 1;
            box-shadow: 0 0 15px #fbbf24, 0 0 30px #f59e0b, 0 0 45px #ef4444;
          }
          95% {
            width: 20px;
            height: 2px;
            opacity: 0.5;
          }
          100% {
            width: 0;
            height: 0;
            opacity: 0;
          }
        }
        
        /* 착륙 연기 효과 */
        @keyframes landingSmoke {
          0%, 60% {
            opacity: 0;
            left: -35px;
          }
          70% {
            opacity: 0.8;
            left: -25px;
          }
          80% {
            opacity: 1;
            left: -20px;
          }
          90% {
            opacity: 0.6;
            left: -15px;
          }
          100% {
            opacity: 0;
            left: -10px;
          }
        }
        
        /* 착륙 완료 시 충격파 효과 */
        @keyframes landingShockwave {
          0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
          }
          25% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0.5);
          }
          50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 20px rgba(59, 130, 246, 0.3);
          }
          75% {
            transform: scale(1.02);
            box-shadow: 0 0 0 30px rgba(59, 130, 246, 0.1);
          }
          100% {
            transform: scale(1);
            box-shadow: 0 0 0 40px rgba(59, 130, 246, 0);
          }
        }
        
        /* 로고 아이콘 */
        .logo-icon {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(29, 78, 216, 0.05));
          transition: all 0.3s ease;
          margin-right: 12px;
          position: relative;
          overflow: visible;
          opacity: 0;
          animation: logoIconAppear 2.6s ease-out forwards,
                     landingShockwave 1s ease-out 2.3s;
        }
        
        @keyframes logoIconAppear {
          0%, 50% {
            opacity: 0;
            transform: scale(0.8);
          }
          70% {
            opacity: 0.5;
            transform: scale(0.9);
          }
          85% {
            opacity: 0.8;
            transform: scale(1.05);
          }
          100% {
            opacity: 1;
            transform: scale(1);
          }
        }
        
        /* 로고 텍스트 */
        .logo-text {
          transition: all 0.3s ease;
          color: #1f2937;
          font-weight: 700;
          font-size: 1.5rem;
          opacity: 0;
          animation: logoTextAppear 2.8s ease-out forwards;
        }
        
        @keyframes logoTextAppear {
          0%, 60% {
            opacity: 0;
            transform: translateY(10px);
          }
          70% {
            opacity: 0.3;
            transform: translateY(5px);
          }
          80% {
            opacity: 0.6;
            transform: translateY(2px);
          }
          90% {
            opacity: 0.8;
            transform: translateY(-1px);
          }
          100% {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        /* 호버 효과 */
        .logo-link:hover .header-rocket {
          animation: headerRocketIgnition 0.8s ease-in-out;
          transform: translateY(-3px) rotate(-8deg) scale(1.1);
          color: #1d4ed8;
          filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.4));
        }
        
        @keyframes headerRocketIgnition {
          0% {
            transform: translateY(0px) rotate(0deg) scale(1);
          }
          30% {
            transform: translateY(-1px) rotate(-4deg) scale(1.05);
          }
          60% {
            transform: translateY(-2px) rotate(-6deg) scale(1.08);
          }
          100% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
          }
        }
        
        .logo-link:hover .logo-text {
          color: #1d4ed8;
          text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }
        
        .logo-link:hover .logo-icon {
          background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(29, 78, 216, 0.1));
          box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }
        
        /* 클릭 시 로켓 발사! */
        .logo-link:active .header-rocket {
          animation: headerRocketLaunch 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          transform: translateY(-8px) rotate(-20deg) scale(1.15);
        }
        
        @keyframes headerRocketLaunch {
          0% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
          }
          40% {
            transform: translateY(-5px) rotate(-15deg) scale(1.12);
          }
          70% {
            transform: translateY(-10px) rotate(-18deg) scale(1.18);
          }
          100% {
            transform: translateY(-8px) rotate(-20deg) scale(1.15);
          }
        }
        
        /* 로고 */
        .logo {
          margin: 0;
          padding: 0;
        }
        
        /* 모바일 반응형 */
        @media (max-width: 768px) {
          .header-rocket {
            font-size: 1.5rem;
          }
          
          .logo-text {
            font-size: 1.3rem;
          }
          
          .logo-icon {
            width: 35px;
            height: 35px;
            margin-right: 8px;
          }
        }
      `}</style>
    </header>
  );
};

export default Header;