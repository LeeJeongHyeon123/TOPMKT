import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';

const Header: React.FC = () => {
  const { user, isAuthenticated, logout } = useAuth();
  const { success } = useToast();
  const navigate = useNavigate();
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  // 디버깅 정보
  console.log('Header - isAuthenticated:', isAuthenticated);
  console.log('Header - user:', user);

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

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
    // 햄버거 메뉴 애니메이션을 위한 클래스 토글
    const button = document.getElementById('mobile-menu-toggle');
    if (button) {
      button.classList.toggle('active');
    }
  };

  return (
    <header className="main-header">
      <div className="container">
        <div className="header-content">
          {/* 로고 */}
          <div className="header-left">
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
          <nav className="nav-menu">
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                className="nav-link"
              >
                {item.name}
              </Link>
            ))}
          </nav>

          {/* 로그인 상태별 우측 메뉴 */}
          <div className="nav-auth">
            {isAuthenticated && user ? (
              <div className="user-menu" onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                const menu = e.currentTarget;
                // Check if dropdown exists but we don't need the reference
                const isActive = menu.classList.contains('active');
                
                // 기존 active 클래스 제거
                document.querySelectorAll('.user-menu').forEach(m => m.classList.remove('active'));
                
                if (!isActive) {
                  menu.classList.add('active');
                  // 외부 클릭 시 드롭다운 닫기
                  const closeDropdown = (event: MouseEvent) => {
                    if (!menu.contains(event.target as Node)) {
                      menu.classList.remove('active');
                      document.removeEventListener('click', closeDropdown);
                    }
                  };
                  setTimeout(() => document.addEventListener('click', closeDropdown), 0);
                }
              }}>
                <div className="user-avatar">
                  {user.profile_image_thumb ? (
                    <img 
                      src={user.profile_image_thumb} 
                      alt="프로필"
                      onError={(e) => {
                        e.currentTarget.style.display = 'none';
                        const fallback = e.currentTarget.nextElementSibling as HTMLElement;
                        if (fallback) fallback.style.display = 'flex';
                      }}
                    />
                  ) : null}
                  <div className="avatar-fallback" style={{ display: user.profile_image_thumb ? 'none' : 'flex' }}>
                    👤
                  </div>
                </div>
                <span className="user-name">{user.nickname}</span>
                <i className="fas fa-chevron-down"></i>
                
                <div className="user-dropdown">
                  <div className="dropdown-header">
                    <div className="user-info">
                      <span className="user-display-name">{user.nickname}</span>
                    </div>
                  </div>
                  <div className="dropdown-divider"></div>
                  <Link to="/profile" className="dropdown-item">
                    <i className="fas fa-user"></i>
                    <span>프로필</span>
                  </Link>
                  <Link to="/chat" className="dropdown-item">
                    <i className="fas fa-envelope"></i>
                    <span>채팅</span>
                  </Link>
                  <div className="dropdown-divider"></div>
                  <button 
                    onClick={(e) => {
                      e.preventDefault();
                      if (window.confirm('정말 로그아웃하시겠습니까?')) {
                        handleLogout();
                      }
                    }} 
                    className="dropdown-item logout-item"
                  >
                    <i className="fas fa-sign-out-alt"></i>
                    <span>로그아웃</span>
                  </button>
                </div>
              </div>
            ) : (
              <>
                <Link to="/login" className="nav-link login-btn">
                  <i className="fas fa-sign-in-alt"></i>
                  로그인
                </Link>
                <Link to="/signup" className="btn btn-primary">
                  <i className="fas fa-user-plus"></i>
                  회원가입
                </Link>
              </>
            )}
          </div>

          {/* 모바일 메뉴 토글 */}
          <button className="mobile-menu-toggle" id="mobile-menu-toggle" onClick={toggleMenu}>
            <span className="hamburger-line"></span>
            <span className="hamburger-line"></span>
            <span className="hamburger-line"></span>
          </button>
        </div>

        {/* 모바일 메뉴 */}
        <div className={`mobile-menu ${isMenuOpen ? 'mobile-menu-open' : ''}`}>
          <div className="mobile-menu-content">
            {navItems.map((item) => (
              <Link
                key={item.path}
                to={item.path}
                className="mobile-nav-link"
                onClick={() => setIsMenuOpen(false)}
              >
                {item.name}
              </Link>
            ))}
            
            <div className="mobile-menu-divider">
              {isAuthenticated && user ? (
                <div className="mobile-user-section">
                  <div className="mobile-user-info">
                    <div className="mobile-user-avatar">
                      {user.profile_image_thumb ? (
                        <img
                          src={user.profile_image_thumb}
                          alt={user.nickname}
                          className="mobile-avatar-image"
                        />
                      ) : (
                        <span className="mobile-avatar-initial">
                          {user.nickname.charAt(0).toUpperCase()}
                        </span>
                      )}
                    </div>
                    <div className="mobile-user-details">
                      <div className="mobile-user-name">
                        {user.nickname}
                      </div>
                      <div className="mobile-user-email">
                        {user.email}
                      </div>
                    </div>
                  </div>
                  <Link
                    to="/profile"
                    className="mobile-nav-link"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    프로필
                  </Link>
                  <button
                    onClick={() => {
                      window.open('/chat', 'chat', 'width=400,height=600,scrollbars=yes,resizable=yes');
                      setIsMenuOpen(false);
                    }}
                    className="mobile-nav-link mobile-nav-btn"
                  >
                    채팅
                  </button>
                  <button
                    onClick={() => {
                      handleLogout();
                      setIsMenuOpen(false);
                    }}
                    className="mobile-nav-link mobile-nav-btn mobile-logout-btn"
                  >
                    로그아웃
                  </button>
                </div>
              ) : (
                <div className="mobile-auth-section">
                  <Link
                    to="/login"
                    className="mobile-nav-link"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    로그인
                  </Link>
                  <Link
                    to="/signup"
                    className="mobile-nav-link mobile-signup-link"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    회원가입
                  </Link>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* 모바일 메뉴 오버레이 */}
        <div className={`mobile-menu-overlay ${isMenuOpen ? 'mobile-menu-overlay-open' : ''}`} onClick={toggleMenu}></div>
      </div>

      {/* 헤더 스타일 */}
      <style>{`
        /* 헤더 레이아웃 */
        .main-header {
          background: linear-gradient(to right, #1E3A8A, #3949ab);
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          position: sticky;
          top: 0;
          z-index: 100;
        }
        
        .container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 20px;
        }
        
        .header-content {
          display: flex;
          align-items: center;
          justify-content: space-between;
          width: 100%;
          padding: 15px 0;
        }
        
        .header-left {
          flex: 0 0 auto;
        }
        
        .nav-menu {
          flex: 1;
          display: flex;
          justify-content: center;
          margin: 0 40px;
          gap: 20px;
        }
        
        .nav-menu .nav-link {
          color: white !important;
          text-decoration: none;
          padding: 10px 15px;
          border-radius: 4px;
          transition: all 0.3s ease;
          font-weight: 500;
          font-size: 16px;
        }
        
        .nav-menu .nav-link:hover {
          background-color: rgba(255, 255, 255, 0.1);
          color: white !important;
          text-decoration: none;
        }
        
        .nav-auth {
          flex: 0 0 auto;
          display: flex;
          align-items: center;
          gap: 15px;
        }
        
        .login-btn {
          color: white;
          text-decoration: none;
          padding: 8px 16px;
          border-radius: 6px;
          transition: background-color 0.3s ease;
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 14px;
        }
        
        .login-btn:hover {
          background-color: rgba(255, 255, 255, 0.1);
          color: white;
        }
        
        .btn-primary {
          background: #F59E0B;
          color: white;
          padding: 8px 16px;
          border-radius: 6px;
          text-decoration: none;
          display: flex;
          align-items: center;
          gap: 8px;
          font-size: 14px;
          border: none;
          cursor: pointer;
          transition: background-color 0.3s ease;
        }
        
        .btn-primary:hover {
          background: #D97706;
        }
        
        /* 사용자 메뉴 스타일 */
        .user-menu {
          position: relative;
          display: flex;
          align-items: center;
          gap: 10px;
          padding: 8px 12px;
          background: rgba(0, 0, 0, 0.05);
          border-radius: 25px;
          cursor: pointer;
          transition: all 0.3s ease;
          backdrop-filter: blur(10px);
          border: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .user-menu:hover {
          background: rgba(0, 0, 0, 0.08);
        }
        
        .user-avatar {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          overflow: hidden;
          position: relative;
        }
        
        .user-avatar img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
        
        .avatar-fallback {
          width: 100%;
          height: 100%;
          background: #667eea;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 16px;
        }
        
        .user-name {
          color: white;
          font-size: 14px;
          font-weight: 500;
          max-width: 100px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
        
        .user-menu i {
          color: rgba(255, 255, 255, 0.8);
          font-size: 12px;
          transition: transform 0.3s ease;
        }
        
        .user-menu.active i {
          transform: rotate(180deg);
        }
        
        /* 드롭다운 메뉴 */
        .user-dropdown {
          position: absolute !important;
          top: calc(100% + 10px) !important;
          right: 0 !important;
          min-width: 200px !important;
          background: white !important;
          border-radius: 8px !important;
          box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
          opacity: 0 !important;
          visibility: hidden !important;
          transform: translateY(-10px) !important;
          transition: all 0.3s ease !important;
          z-index: 1000 !important;
          border: 1px solid #e5e7eb !important;
          display: block !important;
          pointer-events: none !important;
        }
        
        .user-menu.active .user-dropdown {
          opacity: 1 !important;
          visibility: visible !important;
          transform: translateY(0) !important;
          display: block !important;
          pointer-events: auto !important;
        }
        
        .dropdown-header {
          padding: 15px;
          border-bottom: 1px solid #f3f4f6;
        }
        
        .user-display-name {
          display: block;
          font-weight: 600;
          color: #1f2937;
          font-size: 14px;
        }
        
        .dropdown-divider {
          height: 1px;
          background: #f3f4f6;
          margin: 0;
        }
        
        .dropdown-item {
          display: flex;
          align-items: center;
          gap: 12px;
          padding: 12px 15px;
          color: #374151;
          text-decoration: none;
          font-size: 14px;
          transition: background-color 0.2s ease;
          border: none;
          background: none;
          width: 100%;
          cursor: pointer;
        }
        
        .dropdown-item:hover {
          background-color: #f9fafb;
        }
        
        .dropdown-item i {
          width: 16px;
          color: #6b7280;
        }
        
        .logout-item {
          color: #dc2626;
        }
        
        .logout-item:hover {
          background-color: #fef2f2;
        }
        
        .logout-item i {
          color: #dc2626;
        }
        
        /* 모바일 메뉴 토글 */
        .mobile-menu-toggle {
          display: none;
          flex-direction: column;
          justify-content: space-around;
          width: 30px;
          height: 30px;
          background: transparent;
          border: none;
          cursor: pointer;
          padding: 0;
          z-index: 10;
        }
        
        .hamburger-line {
          width: 25px;
          height: 3px;
          background-color: #374151;
          transition: all 0.3s ease;
        }
        
        .mobile-menu-toggle.active .hamburger-line:nth-child(1) {
          transform: rotate(-45deg) translate(-5px, 6px);
        }
        
        .mobile-menu-toggle.active .hamburger-line:nth-child(2) {
          opacity: 0;
        }
        
        .mobile-menu-toggle.active .hamburger-line:nth-child(3) {
          transform: rotate(45deg) translate(-5px, -6px);
        }
        
        /* 모바일 메뉴 */
        .mobile-menu {
          display: none;
          position: absolute;
          top: 100%;
          left: 0;
          right: 0;
          background: white;
          border-top: 1px solid #e5e7eb;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          z-index: 50;
        }
        
        .mobile-menu-open {
          display: block;
        }
        
        .mobile-menu-content {
          padding: 20px;
        }
        
        .mobile-nav-link {
          display: block;
          padding: 15px 0;
          color: #374151;
          text-decoration: none;
          border-bottom: 1px solid #f3f4f6;
          font-size: 16px;
          font-weight: 500;
        }
        
        .mobile-nav-link:hover {
          color: #1d4ed8;
        }
        
        .mobile-menu-divider {
          margin-top: 20px;
          padding-top: 20px;
          border-top: 2px solid #f3f4f6;
        }
        
        .mobile-user-section {
          background: #f9fafb;
          padding: 20px;
          border-radius: 8px;
          margin-bottom: 15px;
        }
        
        .mobile-user-info {
          display: flex;
          align-items: center;
          gap: 15px;
          margin-bottom: 20px;
        }
        
        .mobile-user-avatar {
          width: 50px;
          height: 50px;
          border-radius: 50%;
          overflow: hidden;
        }
        
        .mobile-avatar-image {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }
        
        .mobile-avatar-initial {
          width: 100%;
          height: 100%;
          background: #667eea;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 20px;
          font-weight: bold;
        }
        
        .mobile-user-details {
          flex: 1;
        }
        
        .mobile-user-name {
          font-size: 18px;
          font-weight: 600;
          color: #1f2937;
          margin-bottom: 5px;
        }
        
        .mobile-user-email {
          font-size: 14px;
          color: #6b7280;
        }
        
        .mobile-nav-btn {
          background: none;
          border: none;
          width: 100%;
          text-align: left;
          cursor: pointer;
        }
        
        .mobile-logout-btn {
          color: #dc2626;
        }
        
        .mobile-auth-section {
          background: #f9fafb;
          padding: 20px;
          border-radius: 8px;
        }
        
        .mobile-signup-link {
          background: #3b82f6;
          color: white !important;
          text-align: center;
          border-radius: 6px;
          margin-top: 10px;
        }
        
        /* 모바일 메뉴 오버레이 */
        .mobile-menu-overlay {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5);
          z-index: 40;
        }
        
        .mobile-menu-overlay-open {
          display: block;
        }
        
        /* 모바일 반응형 */
        @media (max-width: 768px) {
          .header-content {
            padding: 10px 0;
          }
          
          .nav-menu {
            display: none;
          }
          
          .mobile-menu-toggle {
            display: flex;
          }
          
          .mobile-menu-overlay {
            display: none;
          }
          
          .mobile-menu-overlay-open {
            display: block;
          }
          
          .user-name {
            display: none;
          }
          
          .user-dropdown {
            min-width: 180px;
            right: -10px;
          }
          
          .nav-auth {
            gap: 10px;
          }
          
          .login-btn {
            padding: 6px 12px;
            font-size: 13px;
          }
        }
        
        /* 🚀 헤더 로켓 애니메이션 */
        .header-rocket {
          display: inline-block;
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          transform-origin: center bottom;
          position: relative;
          color: #fbbf24;
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
          color: white;
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
          color: #fbbf24;
          text-shadow: 0 0 10px rgba(251, 191, 36, 0.3);
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