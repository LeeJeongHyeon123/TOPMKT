import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

const HomePage: React.FC = () => {
  const { isAuthenticated } = useAuth();

  return (
    <div className="min-h-screen">
      {/* 1. 히어로 섹션 - 트렌디한 그라디언트 배경 */}
      <section className="hero-section modern-hero">
        <div className="hero-background">
          <div className="gradient-overlay"></div>
          <div className="animated-shapes">
            <div className="shape shape-1"></div>
            <div className="shape shape-2"></div>
            <div className="shape shape-3"></div>
          </div>
        </div>
        <div className="container">
          <div className="hero-content">
            <div className="hero-badge">
              <span className="badge-icon">🚀</span>
              <span className="badge-text">네트워크 마케팅의 새로운 패러다임</span>
            </div>
            <h1 className="hero-title">
              <span className="gradient-text">글로벌 리더들과 함께</span><br />
              <span className="typing-effect">성공을 만들어가세요</span>
            </h1>
            <p className="hero-description">
              전 세계 네트워크 마케팅 전문가들이 모인 커뮤니티에서<br />
              지식을 공유하고, 인사이트를 얻으며, 함께 성장하세요
            </p>
            <div className="hero-actions">
              <Link 
                to={isAuthenticated ? '/community' : '/signup'} 
                className="btn btn-primary-gradient rocket-launch-btn"
              >
                <span>무료로 시작하기</span>
                <i className="fas fa-rocket rocket-icon"></i>
              </Link>
              <a href="#features" className="btn btn-ghost">
                <i className="fas fa-play"></i>
                <span>둘러보기</span>
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* 2. 핵심 기능 섹션 */}
      <section id="features" className="features-section">
        <div className="container">
          <div className="section-header">
            <span className="section-badge">핵심 기능</span>
            <h2 className="section-title">탑마케팅이 제공하는 가치</h2>
            <p className="section-subtitle">성공적인 네트워크 마케팅을 위한 모든 도구가 여기에</p>
          </div>
          
          <div className="features-grid">
            <div className="feature-card featured">
              <div className="feature-icon">
                <div className="icon-bg">
                  <i className="fas fa-users"></i>
                </div>
              </div>
              <h3>커뮤니티 네트워킹</h3>
              <p>전 세계 네트워크 마케팅 전문가들과 연결되어 경험과 노하우를 공유하세요</p>
              <Link to="/community" className="feature-link">
                <span>시작하기</span>
                <i className="fas fa-arrow-right"></i>
              </Link>
            </div>
            
            <div className="feature-card">
              <div className="feature-icon">
                <div className="icon-bg green">
                  <i className="fas fa-graduation-cap"></i>
                </div>
              </div>
              <h3>전문 강의</h3>
              <p>업계 전문가들의 실전 강의를 통해 실무 역량을 키워보세요</p>
              <Link to="/lectures" className="feature-link">
                <span>강의듣기</span>
                <i className="fas fa-arrow-right"></i>
              </Link>
            </div>
            
            <div className="feature-card">
              <div className="feature-icon">
                <div className="icon-bg purple">
                  <i className="fas fa-calendar-alt"></i>
                </div>
              </div>
              <h3>행사 참여</h3>
              <p>다양한 네트워킹 행사와 컨퍼런스에 참여하여 새로운 기회를 만나보세요</p>
              <Link to="/events" className="feature-link">
                <span>둘러보기</span>
                <i className="fas fa-arrow-right"></i>
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* 6. CTA 섹션 */}
      <section className="cta-section">
        <div className="container">
          <div className="cta-content">
            <div className="cta-text">
              <h2>성공의 여정을 함께 시작하세요</h2>
              <p>전 세계 네트워크 마케팅 리더들과 연결되어 새로운 기회를 발견하고 성공을 만들어가세요</p>
              <ul className="cta-benefits">
                <li><i className="fas fa-check"></i> 무료 회원가입 및 기본 기능 이용</li>
                <li><i className="fas fa-check"></i> 전문가 네트워크 액세스</li>
                <li><i className="fas fa-check"></i> 독점 행사 및 강의 참여</li>
              </ul>
            </div>
            <div className="cta-actions">
              <Link 
                to={isAuthenticated ? '/community' : '/signup'} 
                className="btn btn-primary-gradient btn-large rocket-launch-btn"
              >
                <span>지금 시작하기</span>
                <i className="fas fa-rocket rocket-icon"></i>
              </Link>
              <p className="cta-note">가입은 무료이며, 언제든지 탈퇴 가능합니다</p>
            </div>
          </div>
        </div>
      </section>

      {/* 로켓 애니메이션 및 모든 스타일 */}
      <style>{`
        /* 로켓 애니메이션 효과 */
        .rocket-icon {
          display: inline-block;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          transform-origin: center bottom;
          position: relative;
        }

        .rocket-launch-btn {
          position: relative;
          overflow: hidden;
          transition: all 0.3s ease;
        }

        .rocket-launch-btn::before {
          content: '';
          position: absolute;
          bottom: -2px;
          left: 50%;
          width: 0;
          height: 2px;
          background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, #d97706, transparent);
          transform: translateX(-50%);
          transition: width 0.6s ease;
        }

        .rocket-launch-btn::after {
          content: '💨';
          position: absolute;
          left: -30px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0;
          font-size: 0.8rem;
          transition: all 0.3s ease;
        }

        /* 기본 로켓 애니메이션 - 둥둥 떠다니기 */
        .rocket-icon {
          animation: rocketFloat 3s ease-in-out infinite;
        }

        @keyframes rocketFloat {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          25% {
            transform: translateY(-3px) rotate(2deg);
          }
          50% {
            transform: translateY(-6px) rotate(0deg);
          }
          75% {
            transform: translateY(-3px) rotate(-2deg);
          }
        }

        /* 호버 시 로켓 발사 준비 */
        .rocket-launch-btn:hover .rocket-icon {
          animation: rocketPrepare 0.6s ease-in-out;
          transform: translateY(-5px) rotate(-10deg) scale(1.1);
        }

        @keyframes rocketPrepare {
          0% {
            transform: translateY(0px) rotate(0deg) scale(1);
          }
          50% {
            transform: translateY(-2px) rotate(-5deg) scale(1.05);
          }
          100% {
            transform: translateY(-5px) rotate(-10deg) scale(1.1);
          }
        }

        /* 호버 시 추진 불꽃 효과 */
        .rocket-launch-btn:hover::before {
          width: 60px;
          animation: thrusterFlame 0.3s ease-in-out infinite alternate;
        }

        .rocket-launch-btn:hover::after {
          opacity: 1;
          left: -15px;
          animation: smokeTrail 1s ease-in-out infinite;
        }

        @keyframes thrusterFlame {
          0% {
            height: 2px;
            box-shadow: 0 0 5px #fbbf24;
          }
          100% {
            height: 4px;
            box-shadow: 0 0 10px #f59e0b, 0 0 20px #d97706;
          }
        }

        @keyframes smokeTrail {
          0% {
            opacity: 0.8;
            transform: translateY(-50%) scale(1);
          }
          100% {
            opacity: 0.4;
            transform: translateY(-50%) scale(1.2);
          }
        }

        /* 클릭 시 로켓 발사 애니메이션 */
        .rocket-launch-btn:active .rocket-icon {
          animation: rocketLaunch 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          transform: translateY(-20px) rotate(-15deg) scale(1.2);
        }

        @keyframes rocketLaunch {
          0% {
            transform: translateY(-5px) rotate(-10deg) scale(1.1);
          }
          30% {
            transform: translateY(-15px) rotate(-12deg) scale(1.15);
          }
          60% {
            transform: translateY(-25px) rotate(-15deg) scale(1.25);
          }
          100% {
            transform: translateY(-20px) rotate(-15deg) scale(1.2);
          }
        }

        /* 클릭 시 강력한 추진력 효과 */
        .rocket-launch-btn:active::before {
          width: 80px;
          height: 6px;
          box-shadow: 
            0 0 15px #fbbf24, 
            0 0 30px #f59e0b, 
            0 0 45px #d97706,
            0 2px 0 #ef4444,
            0 4px 0 #dc2626;
          animation: superThruster 0.2s ease-in-out infinite;
        }

        .rocket-launch-btn:active::after {
          content: '💨💨💨';
          left: -40px;
          font-size: 1rem;
          animation: intenseSmokeTrail 0.4s ease-in-out infinite;
        }

        @keyframes superThruster {
          0% {
            transform: translateX(-50%) scaleX(1);
          }
          100% {
            transform: translateX(-50%) scaleX(1.1);
          }
        }

        @keyframes intenseSmokeTrail {
          0% {
            opacity: 1;
            transform: translateY(-50%) translateX(0) scale(1);
          }
          100% {
            opacity: 0.6;
            transform: translateY(-50%) translateX(-10px) scale(1.3);
          }
        }

        /* 터치 기기를 위한 추가 효과 */
        @media (hover: hover) {
          .rocket-launch-btn:hover {
            transform: translateY(-2px);
            box-shadow: 
              0 8px 25px rgba(102, 126, 234, 0.3),
              0 4px 15px rgba(102, 126, 234, 0.2),
              0 0 0 1px rgba(255, 255, 255, 0.1);
          }
        }

        /* 모바일에서의 터치 효과 */
        @media (hover: none) {
          .rocket-launch-btn:active {
            transform: translateY(-1px) scale(0.98);
          }
        }

        /* 히어로 섹션 기본 스타일 */
        .hero-section {
          position: relative;
          min-height: 70vh;
          display: flex;
          align-items: center;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          overflow: hidden;
        }

        .hero-background {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 1;
        }

        .gradient-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .animated-shapes {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 2;
        }

        .shape {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.1);
          animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
          width: 100px;
          height: 100px;
          top: 20%;
          left: 10%;
          animation-delay: 0s;
        }

        .shape-2 {
          width: 150px;
          height: 150px;
          top: 60%;
          right: 15%;
          animation-delay: 2s;
        }

        .shape-3 {
          width: 80px;
          height: 80px;
          bottom: 20%;
          left: 20%;
          animation-delay: 4s;
        }

        @keyframes float {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          50% {
            transform: translateY(-20px) rotate(180deg);
          }
        }

        .container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 1rem;
          position: relative;
          z-index: 3;
        }

        .hero-content {
          text-align: center;
          color: white;
          max-width: 800px;
          margin: 0 auto;
        }

        .hero-badge {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          background: rgba(255, 255, 255, 0.1);
          padding: 8px 16px;
          border-radius: 50px;
          margin-bottom: 32px;
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .badge-icon {
          font-size: 1.2rem;
        }

        .badge-text {
          font-size: 0.9rem;
          font-weight: 500;
        }

        .hero-title {
          font-size: 3.5rem;
          font-weight: 700;
          line-height: 1.1;
          margin-bottom: 24px;
        }

        .gradient-text {
          color: #fbbf24;
          font-weight: 700;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .typing-effect {
          display: inline-block;
          position: relative;
        }

        .typing-effect::after {
          content: '|';
          position: absolute;
          right: -8px;
          top: 0;
          color: white;
          font-weight: 400;
          animation: blink 1s infinite;
        }

        @keyframes blink {
          0%, 50% {
            opacity: 1;
          }
          51%, 100% {
            opacity: 0;
          }
        }

        .hero-description {
          font-size: 1.25rem;
          line-height: 1.6;
          margin-bottom: 40px;
          opacity: 0.9;
        }

        .hero-actions {
          display: flex;
          gap: 16px;
          justify-content: center;
          flex-wrap: wrap;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 16px 32px;
          border-radius: 12px;
          font-weight: 600;
          font-size: 1rem;
          text-decoration: none;
          transition: all 0.3s ease;
          border: none;
          cursor: pointer;
        }

        .btn-primary-gradient {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-ghost {
          background: rgba(255, 255, 255, 0.1);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
          backdrop-filter: blur(10px);
        }

        .btn-ghost:hover {
          background: rgba(255, 255, 255, 0.2);
          color: white;
        }

        /* 기능 섹션 */
        .features-section {
          padding: 80px 0;
          background: #f8fafc;
        }

        .section-header {
          text-align: center;
          margin-bottom: 60px;
        }

        .section-badge {
          display: inline-block;
          background: rgba(102, 126, 234, 0.1);
          color: #667eea;
          padding: 8px 16px;
          border-radius: 50px;
          font-size: 0.875rem;
          font-weight: 600;
          margin-bottom: 16px;
        }

        .section-title {
          font-size: 2.5rem;
          font-weight: 700;
          color: #1f2937;
          margin-bottom: 16px;
        }

        .section-subtitle {
          font-size: 1.125rem;
          color: #6b7280;
          max-width: 600px;
          margin: 0 auto;
        }

        .features-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 32px;
          max-width: 1000px;
          margin: 0 auto;
        }

        .feature-card {
          background: white;
          padding: 40px 32px;
          border-radius: 20px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          text-align: center;
          transition: all 0.3s ease;
          border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card.featured {
          border: 2px solid #667eea;
          position: relative;
        }

        .feature-card.featured::before {
          content: '인기';
          position: absolute;
          top: -10px;
          left: 32px;
          background: #667eea;
          color: white;
          padding: 4px 12px;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 600;
        }

        .feature-icon {
          margin-bottom: 24px;
        }

        .icon-bg {
          width: 80px;
          height: 80px;
          border-radius: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto;
          background: #667eea;
        }

        .icon-bg.green {
          background: #10b981;
        }

        .icon-bg.purple {
          background: #8b5cf6;
        }

        .icon-bg i {
          font-size: 2rem;
          color: white;
        }

        .feature-card h3 {
          font-size: 1.5rem;
          font-weight: 700;
          color: #1f2937;
          margin-bottom: 16px;
        }

        .feature-card p {
          font-size: 1rem;
          color: #6b7280;
          line-height: 1.6;
          margin-bottom: 24px;
        }

        .feature-link {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          color: #667eea;
          font-weight: 600;
          text-decoration: none;
          transition: all 0.3s ease;
        }

        .feature-link:hover {
          color: #5a67d8;
          transform: translateX(4px);
        }

        /* CTA 섹션 */
        .cta-section {
          padding: 80px 0;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
        }

        .cta-content {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 60px;
          align-items: center;
          max-width: 1000px;
          margin: 0 auto;
        }

        .cta-text h2 {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 20px;
        }

        .cta-text p {
          font-size: 1.125rem;
          margin-bottom: 32px;
          opacity: 0.9;
          line-height: 1.6;
        }

        .cta-benefits {
          list-style: none;
          padding: 0;
        }

        .cta-benefits li {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 12px;
          font-size: 1rem;
        }

        .cta-benefits i {
          color: #10b981;
          font-size: 1.125rem;
        }

        .cta-actions {
          text-align: center;
        }

        .btn-large {
          padding: 20px 40px;
          font-size: 1.125rem;
        }

        .cta-note {
          margin-top: 16px;
          font-size: 0.875rem;
          opacity: 0.7;
        }

        /* 반응형 디자인 */
        @media (max-width: 768px) {
          .hero-title {
            font-size: 2.5rem;
          }

          .hero-description {
            font-size: 1.125rem;
          }

          .hero-actions {
            flex-direction: column;
            align-items: center;
          }

          .section-title {
            font-size: 2rem;
          }

          .features-grid {
            grid-template-columns: 1fr;
            gap: 24px;
          }

          .cta-content {
            grid-template-columns: 1fr;
            gap: 40px;
            text-align: center;
          }

          .cta-text h2 {
            font-size: 2rem;
          }
        }
      `}</style>
    </div>
  );
};

export default HomePage;