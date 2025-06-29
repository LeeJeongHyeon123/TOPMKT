import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';
// import { LectureService } from '../services/LectureService';

// 타입 정의
interface Lecture {
  id: number;
  title: string;
  description: string;
  instructor: string;
  instructor_profile: string;
  duration: string;
  level: string;
  price: number;
  discount_price: number;
  image: string;
  rating: number;
  students: number;
  category: string;
  tags: string[];
  is_popular: boolean;
  is_new: boolean;
  start_date: string;
  end_date: string;
  schedule: string;
  format: string;
}

const LecturesPage: React.FC = () => {
  const [lectures, setLectures] = useState<Lecture[]>([]);
  // const [loading, setLoading] = useState(true);
  // const [searchParams] = useSearchParams();
  const { isAuthenticated } = useAuth();
  
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '강의',
    description: '네트워크 마케팅 전문가들의 실전 강의 - 실무 역량을 키우고 성공을 향해 나아가세요',
    ogType: 'website'
  });

  // 임시 강의 데이터
  const mockLectures: Lecture[] = [
    {
      id: 1,
      title: 'SNS 마케팅 완전정복',
      description: '인스타그램, 유튜브, 틱톡을 활용한 효과적인 마케팅 전략을 배워보세요',
      instructor: '김마케팅',
      instructor_profile: '15년 경력의 디지털 마케팅 전문가',
      duration: '4주 과정',
      level: '초급',
      price: 150000,
      discount_price: 99000,
      image: '/assets/images/lectures/sns-marketing.jpg',
      rating: 4.8,
      students: 342,
      category: 'SNS 마케팅',
      tags: ['인스타그램', '유튜브', '틱톡', '콘텐츠 제작'],
      is_popular: true,
      is_new: false,
      start_date: '2024-01-15',
      end_date: '2024-02-12',
      schedule: '매주 월, 수 19:00~21:00',
      format: 'online'
    },
    {
      id: 2,
      title: '네트워크 마케팅 기초부터 실전까지',
      description: '네트워크 마케팅의 기본 원리부터 고급 전략까지 체계적으로 학습합니다',
      instructor: '이성공',
      instructor_profile: '업계 20년 경력, 다이아몬드 등급 리더',
      duration: '8주 과정',
      level: '초급~중급',
      price: 300000,
      discount_price: 250000,
      image: '/assets/images/lectures/network-marketing-basic.jpg',
      rating: 4.9,
      students: 578,
      category: '네트워크 마케팅',
      tags: ['기초', '실전', '리더십', '팀빌딩'],
      is_popular: true,
      is_new: false,
      start_date: '2024-01-20',
      end_date: '2024-03-16',
      schedule: '매주 화, 목 20:00~22:00',
      format: 'hybrid'
    },
    {
      id: 3,
      title: '글로벌 진출 전략과 해외 마케팅',
      description: '해외 시장 진출을 위한 전략 수립과 글로벌 마케팅 실무를 배웁니다',
      instructor: '박글로벌',
      instructor_profile: '해외 진출 컨설팅 전문가, MBA',
      duration: '6주 과정',
      level: '중급~고급',
      price: 200000,
      discount_price: 180000,
      image: '/assets/images/lectures/global-marketing.jpg',
      rating: 4.7,
      students: 124,
      category: '해외진출',
      tags: ['글로벌', '해외진출', '국제마케팅', '문화차이'],
      is_popular: false,
      is_new: true,
      start_date: '2024-02-01',
      end_date: '2024-03-14',
      schedule: '매주 수, 금 19:30~21:30',
      format: 'online'
    }
  ];

  const popularLectures = mockLectures.filter(lecture => lecture.is_popular);
  const newLectures = mockLectures.filter(lecture => lecture.is_new);
  const categories = ['전체', 'SNS 마케팅', '네트워크 마케팅', '해외진출', '데이터 분석', '개인브랜딩', '리더십', '영업전략'];

  useEffect(() => {
    const loadLectures = async () => {
      try {
        // setLoading(true);
        setLectures(mockLectures);
      } catch (error) {
        console.error('Failed to load lectures:', error);
      } finally {
        // setLoading(false);
      }
    };

    loadLectures();
  }, []);

  const formatPrice = (price: number) => {
    return price.toLocaleString() + '원';
  };

  const renderStars = (rating: number) => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
      if (i <= rating) {
        stars.push(<i key={i} className="fas fa-star"></i>);
      } else if (i - 0.5 <= rating) {
        stars.push(<i key={i} className="fas fa-star-half-alt"></i>);
      } else {
        stars.push(<i key={i} className="far fa-star"></i>);
      }
    }
    return stars;
  };

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="lectures-page">
        {/* 강의 헤더 */}
        <section className="lectures-header">
          <div className="container">
            <div className="header-content">
              <div className="header-text">
                <h1 className="page-title">
                  <i className="fas fa-graduation-cap"></i>
                  전문 강의
                </h1>
                <p className="page-description">
                  업계 최고 전문가들의 실전 강의로 실무 역량을 키우고 성공을 향해 나아가세요
                </p>
                <div className="lectures-stats">
                  <div className="stat-item">
                    <span className="stat-number">{lectures.length}</span>
                    <span className="stat-label">전체 강의</span>
                  </div>
                  <div className="stat-item">
                    <span className="stat-number">{lectures.reduce((sum, lecture) => sum + lecture.students, 0)}</span>
                    <span className="stat-label">수강생</span>
                  </div>
                  <div className="stat-item">
                    <span className="stat-number">{newLectures.length}</span>
                    <span className="stat-label">신규 강의</span>
                  </div>
                </div>
              </div>
              <div className="header-actions">
                {isAuthenticated ? (
                  <>
                    <Link to="/lectures/my" className="btn btn-outline-white">
                      <i className="fas fa-book-open"></i>
                      <span>내 강의</span>
                    </Link>
                    <Link to="/lectures/wishlist" className="btn btn-primary-gradient">
                      <i className="fas fa-heart"></i>
                      <span>찜한 강의</span>
                    </Link>
                  </>
                ) : (
                  <Link to="/auth/login?redirect=/lectures" className="btn btn-primary-gradient">
                    <i className="fas fa-sign-in-alt"></i>
                    <span>로그인 후 수강신청</span>
                  </Link>
                )}
              </div>
            </div>
          </div>
        </section>

        {/* 인기 강의 섹션 */}
        {popularLectures.length > 0 && (
          <section className="popular-lectures-section">
            <div className="container">
              <div className="section-header">
                <h2 className="section-title">
                  <i className="fas fa-fire"></i>
                  인기 강의
                </h2>
                <p className="section-subtitle">가장 많은 수강생들이 선택한 베스트 강의</p>
              </div>
              <div className="lectures-grid popular-grid">
                {popularLectures.map(lecture => (
                  <div key={lecture.id} className="lecture-card popular">
                    <div className="lecture-image">
                      <img 
                        src={lecture.image} 
                        alt={lecture.title}
                        onError={(e) => { (e.target as HTMLImageElement).src = '/assets/images/default-lecture.jpg'; }}
                      />
                      <div className="lecture-overlay">
                        <div className="lecture-badges">
                          <span className="badge popular-badge">
                            <i className="fas fa-fire"></i> 인기
                          </span>
                          <span className="badge format-badge">
                            {lecture.format === 'online' ? '온라인' : lecture.format === 'offline' ? '오프라인' : '하이브리드'}
                          </span>
                        </div>
                        <div className="lecture-actions">
                          <button className="action-btn wishlist-btn">
                            <i className="far fa-heart"></i>
                          </button>
                          <Link to={`/lectures/detail/${lecture.id}`} className="action-btn preview-btn">
                            <i className="fas fa-play"></i>
                          </Link>
                        </div>
                      </div>
                    </div>
                    
                    <div className="lecture-content">
                      <div className="lecture-meta">
                        <span className="category">{lecture.category}</span>
                        <span className="level">{lecture.level}</span>
                      </div>
                      
                      <h3 className="lecture-title">
                        <Link to={`/lectures/detail/${lecture.id}`}>
                          {lecture.title}
                        </Link>
                      </h3>
                      
                      <p className="lecture-description">
                        {lecture.description}
                      </p>
                      
                      <div className="instructor-info">
                        <div className="instructor-avatar">
                          <i className="fas fa-user-tie"></i>
                        </div>
                        <div className="instructor-details">
                          <span className="instructor-name">{lecture.instructor}</span>
                          <span className="instructor-profile">{lecture.instructor_profile}</span>
                        </div>
                      </div>
                      
                      <div className="lecture-stats">
                        <div className="rating">
                          <div className="stars">
                            {renderStars(lecture.rating)}
                          </div>
                          <span className="rating-score">{lecture.rating}</span>
                          <span className="students-count">({lecture.students}명)</span>
                        </div>
                        <div className="duration">
                          <i className="fas fa-clock"></i>
                          <span>{lecture.duration}</span>
                        </div>
                      </div>
                      
                      <div className="lecture-price">
                        <div className="price-info">
                          <span className="original-price">{formatPrice(lecture.price)}</span>
                          <span className="discount-price">{formatPrice(lecture.discount_price)}</span>
                          <span className="discount-rate">
                            {Math.round((1 - lecture.discount_price / lecture.price) * 100)}% 할인
                          </span>
                        </div>
                        <Link to={`/lectures/detail/${lecture.id}`} className="btn btn-primary enroll-btn">
                          수강신청
                        </Link>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}

        {/* 전체 강의 목록 */}
        <section className="all-lectures-section">
          <div className="container">
            <div className="section-header">
              <h2 className="section-title">전체 강의</h2>
              <div className="section-filters">
                <select className="category-filter">
                  <option value="">카테고리 선택</option>
                  {categories.map(category => (
                    <option key={category} value={category}>{category}</option>
                  ))}
                </select>
                <select className="sort-filter">
                  <option value="latest">최신순</option>
                  <option value="popular">인기순</option>
                  <option value="rating">평점순</option>
                  <option value="price_low">가격 낮은순</option>
                  <option value="price_high">가격 높은순</option>
                </select>
              </div>
            </div>
            
            <div className="lectures-grid">
              {lectures.map(lecture => (
                <div key={lecture.id} className="lecture-card">
                  <div className="lecture-image">
                    <img 
                      src={lecture.image} 
                      alt={lecture.title}
                      onError={(e) => { (e.target as HTMLImageElement).src = '/assets/images/default-lecture.jpg'; }}
                    />
                    <div className="lecture-overlay">
                      <div className="lecture-badges">
                        {lecture.is_new && (
                          <span className="badge new-badge">
                            <i className="fas fa-star"></i> 신규
                          </span>
                        )}
                        <span className="badge format-badge">
                          {lecture.format === 'online' ? '온라인' : lecture.format === 'offline' ? '오프라인' : '하이브리드'}
                        </span>
                      </div>
                      <div className="lecture-actions">
                        <button className="action-btn wishlist-btn">
                          <i className="far fa-heart"></i>
                        </button>
                        <Link to={`/lectures/detail/${lecture.id}`} className="action-btn preview-btn">
                          <i className="fas fa-play"></i>
                        </Link>
                      </div>
                    </div>
                  </div>
                  
                  <div className="lecture-content">
                    <div className="lecture-meta">
                      <span className="category">{lecture.category}</span>
                      <span className="level">{lecture.level}</span>
                    </div>
                    
                    <h3 className="lecture-title">
                      <Link to={`/lectures/detail/${lecture.id}`}>
                        {lecture.title}
                      </Link>
                    </h3>
                    
                    <p className="lecture-description">
                      {lecture.description}
                    </p>
                    
                    <div className="instructor-info">
                      <div className="instructor-avatar">
                        <i className="fas fa-user-tie"></i>
                      </div>
                      <div className="instructor-details">
                        <span className="instructor-name">{lecture.instructor}</span>
                        <span className="instructor-profile">{lecture.instructor_profile}</span>
                      </div>
                    </div>
                    
                    <div className="lecture-stats">
                      <div className="rating">
                        <div className="stars">
                          {renderStars(lecture.rating)}
                        </div>
                        <span className="rating-score">{lecture.rating}</span>
                        <span className="students-count">({lecture.students}명)</span>
                      </div>
                      <div className="duration">
                        <i className="fas fa-clock"></i>
                        <span>{lecture.duration}</span>
                      </div>
                    </div>
                    
                    <div className="lecture-price">
                      <div className="price-info">
                        <span className="original-price">{formatPrice(lecture.price)}</span>
                        <span className="discount-price">{formatPrice(lecture.discount_price)}</span>
                        <span className="discount-rate">
                          {Math.round((1 - lecture.discount_price / lecture.price) * 100)}% 할인
                        </span>
                      </div>
                      <Link to={`/lectures/detail/${lecture.id}`} className="btn btn-primary enroll-btn">
                        수강신청
                      </Link>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      </div>

      {/* 강의 페이지 스타일 */}
      <style>{`
        .lectures-page {
          background-color: #f8fafc;
          min-height: calc(100vh - 80px);
        }

        .lectures-header {
          background: linear-gradient(to right, #1E3A8A, #3949ab);
          color: white;
          padding: 3rem 0;
          position: relative;
          overflow: hidden;
        }

        .lectures-header::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="10" height="10" patternUnits="userSpaceOnUse"><circle cx="5" cy="5" r="1" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
          pointer-events: none;
        }

        .header-content {
          display: flex;
          justify-content: space-between;
          align-items: center;
          position: relative;
          z-index: 2;
        }

        .page-title {
          font-size: 2.5rem;
          font-weight: bold;
          margin-bottom: 0.5rem;
          display: flex;
          align-items: center;
          gap: 1rem;
        }

        .page-title i {
          font-size: 2rem;
          color: #ffd700;
        }

        .page-description {
          font-size: 1.1rem;
          opacity: 0.9;
          margin-bottom: 2rem;
        }

        .lectures-stats {
          display: flex;
          gap: 2rem;
        }

        .stat-item {
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .stat-number {
          font-size: 2rem;
          font-weight: bold;
          color: #ffd700;
        }

        .stat-label {
          font-size: 0.9rem;
          opacity: 0.8;
        }

        .header-actions {
          display: flex;
          gap: 1rem;
        }

        .popular-lectures-section, .all-lectures-section {
          padding: 3rem 0;
        }

        .section-header {
          text-align: center;
          margin-bottom: 3rem;
        }

        .section-title {
          font-size: 2rem;
          font-weight: bold;
          color: #1a202c;
          margin-bottom: 0.5rem;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.75rem;
        }

        .section-title i {
          color: #f56565;
        }

        .section-subtitle {
          color: #718096;
          font-size: 1.1rem;
        }

        .section-filters {
          display: flex;
          gap: 1rem;
          justify-content: center;
          margin-top: 1rem;
        }

        .category-filter, .sort-filter {
          padding: 0.75rem 1rem;
          border: 1px solid #d1d5db;
          border-radius: 8px;
          font-size: 0.9rem;
          background-color: white;
        }

        .lectures-grid {
          display: grid;
          grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
          gap: 2rem;
        }

        .popular-grid {
          grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }

        .lecture-card {
          background: white;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
          transition: all 0.3s ease;
          border: 1px solid #e2e8f0;
        }

        .lecture-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .lecture-card.popular {
          border-color: #f56565;
          background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        }

        .lecture-image {
          position: relative;
          height: 200px;
          overflow: hidden;
        }

        .lecture-image img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.3s ease;
        }

        .lecture-card:hover .lecture-image img {
          transform: scale(1.05);
        }

        .lecture-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(to bottom, rgba(0,0,0,0.1), transparent, rgba(0,0,0,0.3));
          display: flex;
          justify-content: space-between;
          align-items: flex-start;
          padding: 1rem;
          opacity: 0;
          transition: opacity 0.3s ease;
        }

        .lecture-card:hover .lecture-overlay {
          opacity: 1;
        }

        .lecture-badges {
          display: flex;
          flex-direction: column;
          gap: 0.5rem;
        }

        .badge {
          padding: 0.25rem 0.75rem;
          border-radius: 16px;
          font-size: 0.8rem;
          font-weight: 500;
          display: flex;
          align-items: center;
          gap: 0.25rem;
        }

        .popular-badge {
          background-color: #f56565;
          color: white;
        }

        .new-badge {
          background-color: #38a169;
          color: white;
        }

        .format-badge {
          background-color: rgba(255, 255, 255, 0.9);
          color: #4a5568;
        }

        .lecture-actions {
          display: flex;
          flex-direction: column;
          gap: 0.5rem;
        }

        .action-btn {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          border: none;
          background-color: rgba(255, 255, 255, 0.9);
          color: #4a5568;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
          text-decoration: none;
        }

        .action-btn:hover {
          background-color: white;
          color: #2d3748;
          transform: scale(1.1);
        }

        .lecture-content {
          padding: 1.5rem;
        }

        .lecture-meta {
          display: flex;
          gap: 0.75rem;
          margin-bottom: 1rem;
        }

        .category, .level {
          padding: 0.25rem 0.75rem;
          border-radius: 16px;
          font-size: 0.8rem;
          font-weight: 500;
        }

        .category {
          background-color: #e6fffa;
          color: #234e52;
        }

        .level {
          background-color: #e2e8f0;
          color: #4a5568;
        }

        .lecture-title {
          font-size: 1.2rem;
          font-weight: bold;
          margin-bottom: 0.75rem;
          line-height: 1.4;
        }

        .lecture-title a {
          color: #1a202c;
          text-decoration: none;
          transition: color 0.2s ease;
        }

        .lecture-title a:hover {
          color: #48bb78;
        }

        .lecture-description {
          color: #4a5568;
          line-height: 1.6;
          margin-bottom: 1rem;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .instructor-info {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          margin-bottom: 1rem;
          padding: 0.75rem;
          background-color: #f7fafc;
          border-radius: 8px;
        }

        .instructor-avatar {
          color: #48bb78;
          font-size: 1.5rem;
        }

        .instructor-name {
          font-weight: 600;
          color: #2d3748;
          font-size: 0.9rem;
          display: block;
        }

        .instructor-profile {
          color: #718096;
          font-size: 0.8rem;
        }

        .lecture-stats {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1.5rem;
        }

        .rating {
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .stars {
          display: flex;
          color: #ffd700;
          font-size: 0.9rem;
        }

        .rating-score {
          font-weight: 600;
          color: #2d3748;
          font-size: 0.9rem;
        }

        .students-count {
          color: #718096;
          font-size: 0.8rem;
        }

        .duration {
          display: flex;
          align-items: center;
          gap: 0.25rem;
          color: #718096;
          font-size: 0.8rem;
        }

        .lecture-price {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding-top: 1rem;
          border-top: 1px solid #e2e8f0;
        }

        .price-info {
          display: flex;
          flex-direction: column;
        }

        .original-price {
          text-decoration: line-through;
          color: #9ca3af;
          font-size: 0.9rem;
        }

        .discount-price {
          font-size: 1.2rem;
          font-weight: bold;
          color: #1a202c;
        }

        .discount-rate {
          color: #f56565;
          font-size: 0.8rem;
          font-weight: 500;
        }

        .enroll-btn {
          padding: 0.75rem 1.5rem;
          font-size: 0.9rem;
          font-weight: 600;
        }

        @media (max-width: 768px) {
          .header-content {
            flex-direction: column;
            gap: 2rem;
            text-align: center;
          }

          .lectures-stats {
            justify-content: center;
          }

          .lectures-grid {
            grid-template-columns: 1fr;
          }

          .section-filters {
            flex-direction: column;
            align-items: center;
          }

          .lecture-price {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
          }
        }
      `}</style>
    </>
  );
};

export default LecturesPage;