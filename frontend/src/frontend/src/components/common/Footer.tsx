import React from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

const Footer: React.FC = () => {
  const { isAuthenticated } = useAuth();
  const currentYear = new Date().getFullYear();

  return (
    <footer className="main-footer modern-footer">
      <div className="container">
        <div className="footer-content">
          {/* 상단 영역 */}
          <div className="footer-top">
            <div className="footer-section">
              <div className="footer-logo">
                <div className="logo-icon">
                  <i className="fas fa-rocket"></i>
                </div>
                <span className="logo-text">탑마케팅</span>
              </div>
              <p className="footer-description">
                글로벌 네트워크 마케팅 리더들의 커뮤니티<br />
                함께 성장하고 성공을 만들어가는 플랫폼
              </p>
            </div>

            <div className="footer-section">
              <h3 className="footer-title">서비스</h3>
              <ul className="footer-links">
                <li><Link to="/community">커뮤니티</Link></li>
                <li><Link to="/lectures">강의 일정</Link></li>
                <li><Link to="/events">행사 일정</Link></li>
                {!isAuthenticated && (
                  <>
                    <li><Link to="/login">로그인</Link></li>
                    <li><Link to="/signup">회원가입</Link></li>
                  </>
                )}
              </ul>
            </div>

            <div className="footer-section">
              <h3 className="footer-title">정책</h3>
              <ul className="footer-links">
                <li><Link to="/terms">이용약관</Link></li>
                <li><Link to="/privacy">개인정보처리방침</Link></li>
              </ul>
            </div>
          </div>

          {/* 하단 영역 */}
          <div className="footer-bottom">
            <div className="footer-copyright">
              <p>&copy; {currentYear} 탑마케팅. All rights reserved.</p>
              <p className="company-info">
                상호명: (주)윈카드 | 대표자: 이정현 | 사업자등록번호: 133-88-02437 | 
                전화번호: <a href="tel:070-4138-8899">070-4138-8899</a> | 이메일: <a href="mailto:jh@wincard.kr">jh@wincard.kr</a> | 주소: 서울시 금천구 가산디지털1로 204, 반도 아이비밸리 6층
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* 푸터 스타일 */}
      <style>{`
        .main-footer {
          background: #1a1a1a;
          color: #e5e5e5;
          padding: 60px 0 30px;
          margin-top: auto;
        }

        .footer-content {
          max-width: 1200px;
          margin: 0 auto;
        }

        .footer-top {
          display: grid;
          grid-template-columns: 2fr 1fr 1fr;
          gap: 60px;
          margin-bottom: 50px;
        }

        .footer-section {
          display: flex;
          flex-direction: column;
        }

        .footer-logo {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 20px;
        }

        .footer-logo .logo-icon {
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.2rem;
        }

        .footer-logo .logo-text {
          font-size: 1.5rem;
          font-weight: 700;
          color: #fff;
        }

        .footer-description {
          color: #b0b0b0;
          line-height: 1.6;
          margin-bottom: 0;
        }

        .footer-title {
          font-size: 1.1rem;
          font-weight: 600;
          color: #fff;
          margin-bottom: 20px;
        }

        .footer-links {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .footer-links li {
          margin-bottom: 12px;
        }

        .footer-links a {
          color: #b0b0b0;
          text-decoration: none;
          transition: color 0.3s ease;
          font-size: 0.95rem;
        }

        .footer-links a:hover {
          color: #667eea;
        }

        .footer-bottom {
          border-top: 1px solid #333;
          padding-top: 30px;
        }

        .footer-copyright {
          text-align: left;
        }

        .footer-copyright p {
          margin: 0 0 8px 0;
          color: #888;
          font-size: 0.9rem;
        }

        .company-info {
          font-size: 0.85rem !important;
          line-height: 1.5;
        }

        .company-info a {
          color: #667eea;
          text-decoration: none;
        }

        .company-info a:hover {
          text-decoration: underline;
        }

        /* 반응형 */
        @media (max-width: 768px) {
          .footer-top {
            grid-template-columns: 1fr;
            gap: 40px;
          }

          .footer-logo .logo-text {
            font-size: 1.3rem;
          }

          .company-info {
            font-size: 0.8rem !important;
          }
        }
      `}</style>
    </footer>
  );
};

export default Footer;