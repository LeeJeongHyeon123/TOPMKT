import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';
// import { EventService } from '../services/EventService';

// 타입 정의
interface Event {
  id: number;
  title: string;
  description: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  location: string;
  type: 'online' | 'offline' | 'hybrid';
  max_participants: number;
  current_participants: number;
  image: string;
  organizer: string;
  category: string;
  tags: string[];
  is_featured: boolean;
  is_free: boolean;
  price: number;
  registration_deadline: string;
  status: 'upcoming' | 'ongoing' | 'completed' | 'cancelled';
}

const EventsPage: React.FC = () => {
  const [events, setEvents] = useState<Event[]>([]);
  const [currentDate, setCurrentDate] = useState(new Date());
  const [viewMode, setViewMode] = useState<'calendar' | 'list'>('calendar');
  const [selectedCategory, setSelectedCategory] = useState('all');
  // const [loading, setLoading] = useState(true);
  const { isAuthenticated } = useAuth();
  
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '행사 일정',
    description: '네트워크 마케팅 관련 다양한 행사와 이벤트에 참여하여 네트워킹의 기회를 만들어보세요',
    ogType: 'website'
  });

  // 임시 이벤트 데이터
  const mockEvents: Event[] = [
    {
      id: 1,
      title: '글로벌 네트워크 마케팅 컨퍼런스 2024',
      description: '세계 최고의 네트워크 마케팅 리더들이 모이는 대규모 컨퍼런스',
      start_date: '2024-02-15',
      end_date: '2024-02-17',
      start_time: '09:00',
      end_time: '18:00',
      location: '코엑스 컨벤션센터',
      type: 'offline',
      max_participants: 500,
      current_participants: 342,
      image: '/assets/images/events/global-conference.jpg',
      organizer: '탑마케팅',
      category: '컨퍼런스',
      tags: ['네트워킹', '글로벌', '리더십', '성장'],
      is_featured: true,
      is_free: false,
      price: 150000,
      registration_deadline: '2024-02-10',
      status: 'upcoming'
    },
    {
      id: 2,
      title: 'SNS 마케팅 마스터클래스',
      description: '소셜미디어를 활용한 효과적인 마케팅 전략 워크샵',
      start_date: '2024-01-25',
      end_date: '2024-01-25',
      start_time: '14:00',
      end_time: '17:00',
      location: '온라인 (ZOOM)',
      type: 'online',
      max_participants: 100,
      current_participants: 78,
      image: '/assets/images/events/sns-masterclass.jpg',
      organizer: '김마케팅',
      category: '워크샵',
      tags: ['SNS', '디지털마케팅', '실무', '온라인'],
      is_featured: false,
      is_free: true,
      price: 0,
      registration_deadline: '2024-01-23',
      status: 'upcoming'
    },
    {
      id: 3,
      title: '신년 네트워킹 파티',
      description: '2024년 새해를 맞이하여 업계 전문가들과 함께하는 네트워킹 이벤트',
      start_date: '2024-01-30',
      end_date: '2024-01-30',
      start_time: '19:00',
      end_time: '22:00',
      location: '강남 그랜드 호텔',
      type: 'offline',
      max_participants: 200,
      current_participants: 156,
      image: '/assets/images/events/networking-party.jpg',
      organizer: '탑마케팅',
      category: '네트워킹',
      tags: ['신년회', '네트워킹', '파티', '만남'],
      is_featured: true,
      is_free: false,
      price: 50000,
      registration_deadline: '2024-01-28',
      status: 'upcoming'
    }
  ];

  const categories = ['all', '컨퍼런스', '워크샵', '세미나', '네트워킹', '웨비나', '교육'];
  const monthNames = [
    '1월', '2월', '3월', '4월', '5월', '6월',
    '7월', '8월', '9월', '10월', '11월', '12월'
  ];

  useEffect(() => {
    const loadEvents = async () => {
      try {
        // setLoading(true);
        setEvents(mockEvents);
      } catch (error) {
        console.error('Failed to load events:', error);
      } finally {
        // setLoading(false);
      }
    };

    loadEvents();
  }, []);

  // 월 변경
  const changeMonth = (direction: 'prev' | 'next') => {
    const newDate = new Date(currentDate);
    if (direction === 'prev') {
      newDate.setMonth(newDate.getMonth() - 1);
    } else {
      newDate.setMonth(newDate.getMonth() + 1);
    }
    setCurrentDate(newDate);
  };

  // 오늘로 이동
  const goToToday = () => {
    setCurrentDate(new Date());
  };

  // 캘린더 데이터 생성
  const generateCalendarData = () => {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const firstDay = new Date(year, month, 1);
    // const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const days = [];
    const current = new Date(startDate);
    
    for (let week = 0; week < 6; week++) {
      const weekDays = [];
      for (let day = 0; day < 7; day++) {
        const dateStr = current.toISOString().split('T')[0];
        const dayEvents = events.filter(event => 
          event.start_date <= dateStr && event.end_date >= dateStr
        );
        
        weekDays.push({
          date: new Date(current),
          events: dayEvents,
          isCurrentMonth: current.getMonth() === month,
          isToday: current.toDateString() === new Date().toDateString()
        });
        
        current.setDate(current.getDate() + 1);
      }
      days.push(weekDays);
    }
    
    return days;
  };

  const calendarData = generateCalendarData();
  const filteredEvents = selectedCategory === 'all' 
    ? events 
    : events.filter(event => event.category === selectedCategory);

  // 가격 포맷팅
  const formatPrice = (price: number) => {
    return price === 0 ? '무료' : `${price.toLocaleString()}원`;
  };

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('ko-KR', {
      month: 'long',
      day: 'numeric'
    });
  };

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="events-page">
        {/* 행사 헤더 */}
        <section className="events-header">
          <div className="container">
            <h1>
              <i className="fas fa-calendar-alt"></i>
              행사 일정
            </h1>
            <p>네트워크 마케팅 관련 다양한 행사와 이벤트에 참여하여 네트워킹의 기회를 만들어보세요</p>
            <div className="header-stats">
              <div className="stat-item">
                <span className="stat-number">{events.length}</span>
                <span className="stat-label">예정된 행사</span>
              </div>
              <div className="stat-item">
                <span className="stat-number">{events.filter(e => e.is_free).length}</span>
                <span className="stat-label">무료 행사</span>
              </div>
              <div className="stat-item">
                <span className="stat-number">{events.filter(e => e.type === 'online').length}</span>
                <span className="stat-label">온라인 행사</span>
              </div>
            </div>
          </div>
        </section>

        <div className="events-container">
          {/* 컨트롤 영역 */}
          <div className="events-controls">
            <div className="events-navigation">
              <div className="month-nav">
                <button className="nav-btn" onClick={() => changeMonth('prev')}>
                  <i className="fas fa-chevron-left"></i>
                </button>
                <span className="current-month">
                  {currentDate.getFullYear()}년 {monthNames[currentDate.getMonth()]}
                </span>
                <button className="nav-btn" onClick={() => changeMonth('next')}>
                  <i className="fas fa-chevron-right"></i>
                </button>
                <button className="today-btn" onClick={goToToday}>
                  오늘
                </button>
              </div>
              
              <div className="view-toggle">
                <button 
                  className={`toggle-btn ${viewMode === 'calendar' ? 'active' : ''}`}
                  onClick={() => setViewMode('calendar')}
                >
                  <i className="fas fa-calendar"></i>
                  캘린더
                </button>
                <button 
                  className={`toggle-btn ${viewMode === 'list' ? 'active' : ''}`}
                  onClick={() => setViewMode('list')}
                >
                  <i className="fas fa-list"></i>
                  목록
                </button>
              </div>
            </div>

            <div className="category-filters">
              {categories.map(category => (
                <button
                  key={category}
                  className={`category-btn ${selectedCategory === category ? 'active' : ''}`}
                  onClick={() => setSelectedCategory(category)}
                >
                  {category === 'all' ? '전체' : category}
                </button>
              ))}
            </div>
          </div>

          {/* 캘린더 뷰 */}
          {viewMode === 'calendar' && (
            <div className="calendar-view">
              <div className="calendar-header">
                {['일', '월', '화', '수', '목', '금', '토'].map(day => (
                  <div key={day} className="day-header">{day}</div>
                ))}
              </div>
              
              <div className="calendar-body">
                {calendarData.map((week, weekIndex) => (
                  <div key={weekIndex} className="calendar-week">
                    {week.map((day, dayIndex) => (
                      <div 
                        key={dayIndex} 
                        className={`calendar-day ${!day.isCurrentMonth ? 'other-month' : ''} ${day.isToday ? 'today' : ''}`}
                      >
                        <div className="day-number">{day.date.getDate()}</div>
                        <div className="day-events">
                          {day.events.slice(0, 3).map(event => (
                            <Link
                              key={event.id}
                              to={`/events/detail/${event.id}`}
                              className={`event-item ${event.is_featured ? 'featured' : ''}`}
                              title={event.title}
                            >
                              <span className="event-title">{event.title}</span>
                            </Link>
                          ))}
                          {day.events.length > 3 && (
                            <div className="more-events">
                              +{day.events.length - 3}개 더
                            </div>
                          )}
                        </div>
                      </div>
                    ))}
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* 목록 뷰 */}
          {viewMode === 'list' && (
            <div className="list-view">
              <div className="events-list">
                {filteredEvents.map(event => (
                  <div key={event.id} className={`event-card ${event.is_featured ? 'featured' : ''}`}>
                    <div className="event-image">
                      <img 
                        src={event.image} 
                        alt={event.title}
                        onError={(e) => { (e.target as HTMLImageElement).src = '/assets/images/default-event.jpg'; }}
                      />
                      <div className="event-overlay">
                        <div className="event-badges">
                          {event.is_featured && (
                            <span className="badge featured-badge">
                              <i className="fas fa-star"></i> 주요행사
                            </span>
                          )}
                          <span className="badge type-badge">
                            {event.type === 'online' ? '온라인' : event.type === 'offline' ? '오프라인' : '하이브리드'}
                          </span>
                          {event.is_free && (
                            <span className="badge free-badge">무료</span>
                          )}
                        </div>
                      </div>
                    </div>

                    <div className="event-content">
                      <div className="event-meta">
                        <span className="category">{event.category}</span>
                        <span className="organizer">주최: {event.organizer}</span>
                      </div>

                      <h3 className="event-title">
                        <Link to={`/events/detail/${event.id}`}>
                          {event.title}
                        </Link>
                      </h3>

                      <p className="event-description">
                        {event.description}
                      </p>

                      <div className="event-details">
                        <div className="detail-item">
                          <i className="fas fa-calendar"></i>
                          <span>
                            {formatDate(event.start_date)} 
                            {event.start_date !== event.end_date && ` ~ ${formatDate(event.end_date)}`}
                          </span>
                        </div>
                        <div className="detail-item">
                          <i className="fas fa-clock"></i>
                          <span>{event.start_time} ~ {event.end_time}</span>
                        </div>
                        <div className="detail-item">
                          <i className="fas fa-map-marker-alt"></i>
                          <span>{event.location}</span>
                        </div>
                        <div className="detail-item">
                          <i className="fas fa-users"></i>
                          <span>{event.current_participants}/{event.max_participants}명</span>
                        </div>
                      </div>

                      <div className="event-tags">
                        {event.tags.map(tag => (
                          <span key={tag} className="tag">#{tag}</span>
                        ))}
                      </div>

                      <div className="event-footer">
                        <div className="price-info">
                          <span className="price">{formatPrice(event.price)}</span>
                          {!event.is_free && (
                            <span className="deadline">
                              신청마감: {formatDate(event.registration_deadline)}
                            </span>
                          )}
                        </div>
                        
                        <div className="event-actions">
                          {isAuthenticated ? (
                            <Link 
                              to={`/events/detail/${event.id}`} 
                              className="btn btn-primary register-btn"
                            >
                              참가신청
                            </Link>
                          ) : (
                            <Link 
                              to={`/auth/login?redirect=/events/detail/${event.id}`} 
                              className="btn btn-outline-primary"
                            >
                              로그인 후 신청
                            </Link>
                          )}
                          <button className="btn btn-outline wishlist-btn">
                            <i className="far fa-heart"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}
        </div>
      </div>

      {/* 이벤트 페이지 스타일 */}
      <style>{`
        .events-page {
          background-color: #f8fafc;
          min-height: calc(100vh - 80px);
        }

        .events-header {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          padding: 3rem 0;
          text-align: center;
        }

        .events-header h1 {
          font-size: 2.5rem;
          font-weight: bold;
          margin-bottom: 0.5rem;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 1rem;
        }

        .events-header i {
          font-size: 2rem;
          color: #ffd700;
        }

        .events-header p {
          font-size: 1.1rem;
          opacity: 0.9;
          margin-bottom: 2rem;
        }

        .header-stats {
          display: flex;
          justify-content: center;
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

        .events-container {
          max-width: 1600px;
          margin: 0 auto;
          padding: 2rem 1rem;
        }

        .events-controls {
          display: flex;
          flex-direction: column;
          gap: 1.5rem;
          margin-bottom: 2rem;
          align-items: center;
        }

        .events-navigation {
          display: flex;
          align-items: center;
          gap: 2rem;
          flex-wrap: wrap;
          justify-content: center;
        }

        .month-nav {
          display: flex;
          align-items: center;
          gap: 1rem;
          background: white;
          padding: 0.75rem 1.5rem;
          border-radius: 50px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-btn {
          background: #4A90E2;
          color: white;
          border: none;
          padding: 0.5rem;
          border-radius: 50%;
          cursor: pointer;
          transition: background 0.3s;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
        }

        .nav-btn:hover {
          background: #357ABD;
        }

        .current-month {
          font-size: 1.2rem;
          font-weight: 600;
          color: #2d3748;
          min-width: 120px;
          text-align: center;
        }

        .today-btn {
          background: #48bb78;
          color: white;
          border: none;
          padding: 0.5rem 1rem;
          border-radius: 20px;
          cursor: pointer;
          font-weight: 500;
          transition: background 0.3s;
        }

        .today-btn:hover {
          background: #38a169;
        }

        .view-toggle {
          display: flex;
          background: white;
          border-radius: 25px;
          padding: 0.25rem;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .toggle-btn {
          padding: 0.5rem 1rem;
          border: none;
          background: transparent;
          border-radius: 20px;
          cursor: pointer;
          transition: all 0.3s;
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-weight: 500;
        }

        .toggle-btn.active {
          background: #4A90E2;
          color: white;
        }

        .category-filters {
          display: flex;
          gap: 0.5rem;
          flex-wrap: wrap;
          justify-content: center;
        }

        .category-btn {
          padding: 0.5rem 1rem;
          border: 1px solid #d1d5db;
          background: white;
          border-radius: 20px;
          cursor: pointer;
          transition: all 0.3s;
          font-size: 0.9rem;
        }

        .category-btn:hover {
          border-color: #1E3A8A;
          color: #1E3A8A;
        }

        .category-btn.active {
          background: #1E3A8A;
          color: white;
          border-color: #1E3A8A;
        }

        /* 캘린더 뷰 */
        .calendar-view {
          background: white;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .calendar-header {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          background: #1E3A8A;
          color: white;
        }

        .day-header {
          padding: 1rem;
          text-align: center;
          font-weight: 600;
        }

        .calendar-body {
          display: flex;
          flex-direction: column;
        }

        .calendar-week {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          border-bottom: 1px solid #e2e8f0;
        }

        .calendar-day {
          min-height: 120px;
          padding: 0.5rem;
          border-right: 1px solid #e2e8f0;
          background: white;
        }

        .calendar-day.other-month {
          background: #f7fafc;
          color: #a0aec0;
        }

        .calendar-day.today {
          background: #ebf8ff;
        }

        .day-number {
          font-weight: 600;
          margin-bottom: 0.5rem;
        }

        .calendar-day.today .day-number {
          background: #1E3A8A;
          color: white;
          width: 24px;
          height: 24px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.9rem;
        }

        .day-events {
          display: flex;
          flex-direction: column;
          gap: 0.25rem;
        }

        .event-item {
          padding: 0.25rem 0.5rem;
          background: #e6fffa;
          border-radius: 4px;
          font-size: 0.8rem;
          text-decoration: none;
          color: #234e52;
          border-left: 3px solid #38b2ac;
          transition: all 0.2s;
        }

        .event-item:hover {
          background: #b2f5ea;
          transform: translateX(2px);
        }

        .event-item.featured {
          background: #fed7d7;
          color: #742a2a;
          border-left-color: #f56565;
        }

        .event-title {
          display: block;
          white-space: nowrap;
          overflow: hidden;
          text-overflow: ellipsis;
        }

        .more-events {
          font-size: 0.75rem;
          color: #718096;
          font-style: italic;
          margin-top: 0.25rem;
        }

        /* 목록 뷰 */
        .events-list {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
          gap: 2rem;
        }

        .event-card {
          background: white;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 2px 8px rgba(0,0,0,0.08);
          transition: all 0.3s ease;
          border: 1px solid #e2e8f0;
        }

        .event-card:hover {
          transform: translateY(-4px);
          box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .event-card.featured {
          border-color: #f56565;
          background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
        }

        .event-image {
          position: relative;
          height: 200px;
          overflow: hidden;
        }

        .event-image img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 0.3s ease;
        }

        .event-card:hover .event-image img {
          transform: scale(1.05);
        }

        .event-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(to bottom, rgba(0,0,0,0.1), transparent, rgba(0,0,0,0.3));
          display: flex;
          justify-content: flex-start;
          align-items: flex-start;
          padding: 1rem;
        }

        .event-badges {
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

        .featured-badge {
          background-color: #f56565;
          color: white;
        }

        .type-badge {
          background-color: #4A90E2;
          color: white;
        }

        .free-badge {
          background-color: #48bb78;
          color: white;
        }

        .event-content {
          padding: 1.5rem;
        }

        .event-meta {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 1rem;
          font-size: 0.9rem;
        }

        .category {
          background-color: #e6fffa;
          color: #234e52;
          padding: 0.25rem 0.75rem;
          border-radius: 16px;
          font-weight: 500;
        }

        .organizer {
          color: #718096;
        }

        .event-title {
          font-size: 1.2rem;
          font-weight: bold;
          margin-bottom: 0.75rem;
          line-height: 1.4;
        }

        .event-title a {
          color: #1a202c;
          text-decoration: none;
          transition: color 0.2s ease;
        }

        .event-title a:hover {
          color: #4A90E2;
        }

        .event-description {
          color: #4a5568;
          line-height: 1.6;
          margin-bottom: 1rem;
          display: -webkit-box;
          -webkit-line-clamp: 2;
          -webkit-box-orient: vertical;
          overflow: hidden;
        }

        .event-details {
          display: flex;
          flex-direction: column;
          gap: 0.5rem;
          margin-bottom: 1rem;
        }

        .detail-item {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.9rem;
          color: #4a5568;
        }

        .detail-item i {
          color: #4A90E2;
          width: 16px;
          text-align: center;
        }

        .event-tags {
          display: flex;
          gap: 0.5rem;
          flex-wrap: wrap;
          margin-bottom: 1.5rem;
        }

        .tag {
          background-color: #f7fafc;
          color: #4a5568;
          padding: 0.25rem 0.5rem;
          border-radius: 12px;
          font-size: 0.8rem;
          border: 1px solid #e2e8f0;
        }

        .event-footer {
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

        .price {
          font-size: 1.2rem;
          font-weight: bold;
          color: #1a202c;
        }

        .deadline {
          font-size: 0.8rem;
          color: #e53e3e;
        }

        .event-actions {
          display: flex;
          gap: 0.5rem;
        }

        .register-btn {
          padding: 0.75rem 1.5rem;
          font-size: 0.9rem;
          font-weight: 600;
        }

        .wishlist-btn {
          padding: 0.75rem;
          min-width: auto;
        }

        @media (max-width: 768px) {
          .events-navigation {
            flex-direction: column;
            gap: 1rem;
          }

          .calendar-day {
            min-height: 80px;
            padding: 0.25rem;
          }

          .events-list {
            grid-template-columns: 1fr;
          }

          .event-footer {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
          }

          .event-actions {
            justify-content: stretch;
          }

          .register-btn {
            flex: 1;
          }
        }
      `}</style>
    </>
  );
};

export default EventsPage;