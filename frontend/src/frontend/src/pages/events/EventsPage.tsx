import React, { useState, useEffect } from 'react';
import Layout from '../../components/common/Layout';

interface Event {
  id: number;
  title: string;
  start_date: string;
  start_time: string;
  location_type: string;
  venue_name?: string;
  registration_fee?: number;
  event_scale?: string;
  has_networking?: boolean;
}

interface CalendarDay {
  date: string;
  day: number;
  class: string;
}

const EventsPage: React.FC = () => {
  const [currentYear, setCurrentYear] = useState(new Date().getFullYear());
  const [currentMonth, setCurrentMonth] = useState(new Date().getMonth() + 1);
  const [view, setView] = useState<'calendar' | 'list'>('calendar');
  const [events] = useState<Event[]>([]);
  const [calendarData, setCalendarData] = useState<CalendarDay[][]>([]);
  const [isLoggedIn] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [modalEvents, setModalEvents] = useState<Event[]>([]);
  const [modalDate, setModalDate] = useState('');

  const monthNames = [
    '', '1월', '2월', '3월', '4월', '5월', '6월',
    '7월', '8월', '9월', '10월', '11월', '12월'
  ];

  useEffect(() => {
    generateCalendar();
    // 실제 구현시 API에서 이벤트 데이터 가져오기
    // loadEvents();
  }, [currentYear, currentMonth]);

  const generateCalendar = () => {
    const firstDay = new Date(currentYear, currentMonth - 1, 1);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    const calendar: CalendarDay[][] = [];
    let week: CalendarDay[] = [];

    for (let i = 0; i < 42; i++) {
      const currentDate = new Date(startDate);
      currentDate.setDate(startDate.getDate() + i);
      
      const isCurrentMonth = currentDate.getMonth() === currentMonth - 1;
      const isToday = currentDate.toDateString() === new Date().toDateString();
      
      let className = '';
      if (!isCurrentMonth) className = 'other-month';
      if (isToday) className += ' today';

      week.push({
        date: currentDate.toISOString().split('T')[0],
        day: currentDate.getDate(),
        class: className.trim()
      });

      if (week.length === 7) {
        calendar.push(week);
        week = [];
      }
    }

    setCalendarData(calendar);
  };

  const navigateMonth = (year: number, month: number) => {
    setCurrentYear(year);
    setCurrentMonth(month);
  };

  const getPrevMonth = () => {
    const prevMonth = currentMonth === 1 ? 12 : currentMonth - 1;
    const prevYear = currentMonth === 1 ? currentYear - 1 : currentYear;
    return { year: prevYear, month: prevMonth };
  };

  const getNextMonth = () => {
    const nextMonth = currentMonth === 12 ? 1 : currentMonth + 1;
    const nextYear = currentMonth === 12 ? currentYear + 1 : currentYear;
    return { year: nextYear, month: nextMonth };
  };

  const showDayEvents = (date: string) => {
    const dayEvents = events.filter(event => event.start_date === date);
    if (dayEvents.length === 0) return;
    
    setModalEvents(dayEvents);
    setModalDate(date);
    setShowModal(true);
  };

  const closeEventModal = () => {
    setShowModal(false);
    setModalEvents([]);
    setModalDate('');
  };

  const getEventsForDay = (date: string) => {
    return events.filter(event => event.start_date === date);
  };

  const getScaleClass = (scale?: string) => {
    switch (scale) {
      case 'large': return 'scale-large';
      case 'medium': return 'scale-medium';
      case 'small': return 'scale-small';
      default: return '';
    }
  };

  const getScaleName = (scale?: string) => {
    switch (scale) {
      case 'large': return '대규모';
      case 'medium': return '중규모';
      case 'small': return '소규모';
      default: return '';
    }
  };

  const prevMonth = getPrevMonth();
  const nextMonth = getNextMonth();

  return (
    <Layout>
      <div className="events-container">
        {/* 헤더 */}
        <div className="events-header">
          <h1>🎉 행사 일정</h1>
          <p>다양한 마케팅 행사와 네트워킹 행사에 참여하세요</p>
        </div>

        {/* 컨트롤 영역 */}
        <div className="events-controls">
          <div className="events-navigation">
            {/* 월 네비게이션 */}
            <div className="month-nav">
              <button 
                className="nav-btn" 
                onClick={() => navigateMonth(prevMonth.year, prevMonth.month)}
              >
                <i className="fas fa-chevron-left"></i>
              </button>
              <div className="current-month">
                {currentYear}년 {monthNames[currentMonth]}
              </div>
              <button 
                className="nav-btn" 
                onClick={() => navigateMonth(nextMonth.year, nextMonth.month)}
              >
                <i className="fas fa-chevron-right"></i>
              </button>
            </div>

            {/* 뷰 토글 */}
            <div className="view-toggle">
              <button 
                className={`view-btn ${view === 'calendar' ? 'active' : ''}`}
                onClick={() => setView('calendar')}
              >
                <i className="fas fa-calendar-alt"></i> 캘린더
              </button>
              <button 
                className={`view-btn ${view === 'list' ? 'active' : ''}`}
                onClick={() => setView('list')}
              >
                <i className="fas fa-list"></i> 목록
              </button>
            </div>
          </div>

          {/* 행사 등록 버튼 */}
          {isLoggedIn ? (
            <a href="/events/create" className="create-event-btn">
              <i className="fas fa-plus"></i>
              새 행사 등록
            </a>
          ) : (
            <a href="/auth/login" className="create-event-btn">
              <i className="fas fa-sign-in-alt"></i>
              로그인 후 등록
            </a>
          )}
        </div>

        {/* 캘린더 */}
        <div className="calendar-container">
          {/* 요일 헤더 */}
          <div className="calendar-header">
            {['일', '월', '화', '수', '목', '금', '토'].map(day => (
              <div key={day} className="calendar-day-header">{day}</div>
            ))}
          </div>

          {/* 캘린더 그리드 */}
          <div className="calendar-grid">
            {calendarData.map((week, weekIndex) =>
              week.map((day, dayIndex) => {
                const dayEvents = getEventsForDay(day.date);
                const displayEvents = dayEvents.slice(0, 3);
                const remainingCount = dayEvents.length - 3;

                return (
                  <div 
                    key={`${weekIndex}-${dayIndex}`} 
                    className={`calendar-day ${day.class}`} 
                    data-date={day.date}
                  >
                    <div className="calendar-day-number">{day.day}</div>
                    
                    {displayEvents.map((event, eventIndex) => (
                      <div 
                        key={eventIndex}
                        className={`event-item ${getScaleClass(event.event_scale)}`}
                        onClick={() => window.location.href = `/events/detail?id=${event.id}`}
                        title={event.title}
                      >
                        {event.title.length > 15 ? event.title.substring(0, 15) + '...' : event.title}
                        {event.has_networking && (
                          <i className="fas fa-users networking-icon" title="네트워킹 포함"></i>
                        )}
                      </div>
                    ))}
                    
                    {remainingCount > 0 && (
                      <div 
                        className="more-events" 
                        onClick={() => showDayEvents(day.date)}
                      >
                        +{remainingCount}개 더보기
                      </div>
                    )}
                  </div>
                );
              })
            )}
          </div>
        </div>
      </div>

      {/* 이벤트 모달 */}
      {showModal && (
        <div className="event-modal" style={{ display: 'flex' }}>
          <div className="event-modal-content">
            <div className="event-modal-header">
              <h3 className="event-modal-title">{modalDate} 행사 목록</h3>
              <button className="modal-close" onClick={closeEventModal}>×</button>
            </div>
            <ul className="event-list">
              {modalEvents.map((event, index) => (
                <li 
                  key={index}
                  className="event-list-item"
                  onClick={() => window.location.href = `/events/detail?id=${event.id}`}
                  style={{ cursor: 'pointer' }}
                >
                  <div className="event-title">
                    {event.title}
                    {event.event_scale && (
                      <span className={`event-scale-badge ${event.event_scale}`}>
                        {getScaleName(event.event_scale)}
                      </span>
                    )}
                    {event.has_networking && (
                      <i className="fas fa-users networking-icon" title="네트워킹 포함"></i>
                    )}
                  </div>
                  <div className="event-details">
                    <div><i className="fas fa-clock"></i> {event.start_time}</div>
                    <div>
                      <i className="fas fa-map-marker-alt"></i> 
                      {event.location_type === 'online' ? '온라인' : event.venue_name || '오프라인'}
                    </div>
                    {event.registration_fee && (
                      <div>
                        <i className="fas fa-won-sign"></i> 
                        {event.registration_fee.toLocaleString()}원
                      </div>
                    )}
                  </div>
                </li>
              ))}
            </ul>
          </div>
        </div>
      )}

      <style>{`
        /* 행사 일정 페이지 스타일 (파란색 테마) */
        .events-container {
          max-width: 1600px;
          margin: 0 auto;
          padding: 30px 15px 20px 15px;
          min-height: calc(100vh - 200px);
          overflow-x: auto;
        }

        .events-header {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          padding: 40px 20px;
          text-align: center;
          margin-top: 60px;
          margin-bottom: 30px;
          border-radius: 12px;
          max-width: 1600px;
          margin-left: auto;
          margin-right: auto;
        }

        .events-header h1 {
          font-size: 2.5rem;
          margin-bottom: 10px;
          font-weight: 700;
        }

        .events-header p {
          font-size: 1.1rem;
          opacity: 0.9;
          margin-bottom: 20px;
        }

        .events-controls {
          display: flex;
          flex-direction: column;
          gap: 20px;
          margin-bottom: 30px;
          align-items: center;
        }

        .events-navigation {
          display: flex;
          align-items: center;
          gap: 20px;
          flex-wrap: wrap;
          justify-content: center;
        }

        .month-nav {
          display: flex;
          align-items: center;
          gap: 15px;
          background: white;
          padding: 10px 20px;
          border-radius: 50px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-btn {
          background: #4A90E2;
          color: white;
          border: none;
          padding: 8px 12px;
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
          font-size: 1.3rem;
          font-weight: 600;
          color: #2E86AB;
          min-width: 120px;
          text-align: center;
        }

        .view-toggle {
          display: flex;
          background: white;
          border-radius: 50px;
          overflow: hidden;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .view-btn {
          padding: 10px 20px;
          border: none;
          background: white;
          color: #666;
          cursor: pointer;
          transition: all 0.3s;
          font-weight: 500;
        }

        .view-btn.active {
          background: #4A90E2;
          color: white;
        }

        .create-event-btn {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          border: none;
          padding: 12px 24px;
          border-radius: 50px;
          text-decoration: none !important;
          font-weight: 600;
          box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
          transition: all 0.3s;
          display: inline-flex;
          align-items: center;
          gap: 8px;
        }

        .create-event-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
          color: white;
          text-decoration: none !important;
        }

        /* 캘린더 스타일 */
        .calendar-container {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.1);
          overflow: hidden;
          margin-bottom: 30px;
        }

        .calendar-grid {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          gap: 1px;
          background: #f1f5f9;
        }

        .calendar-header {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          background: #2E86AB;
          color: white;
        }

        .calendar-day-header {
          padding: 15px;
          text-align: center;
          font-weight: 600;
          font-size: 0.9rem;
        }

        .calendar-day {
          background: white;
          min-height: 120px;
          padding: 8px;
          position: relative;
          transition: background 0.2s;
        }

        .calendar-day:hover {
          background: #f8fafc;
        }

        .calendar-day.other-month {
          background: #f8fafc;
          color: #94a3b8;
        }

        .calendar-day.today {
          background: #e0f2fe;
        }

        .calendar-day-number {
          font-weight: 600;
          margin-bottom: 5px;
          color: #1e293b;
        }

        .calendar-day.other-month .calendar-day-number {
          color: #94a3b8;
        }

        .event-item {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          padding: 4px 8px;
          margin-bottom: 2px;
          border-radius: 4px;
          font-size: 0.75rem;
          cursor: pointer;
          transition: transform 0.2s;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .event-item:hover {
          transform: scale(1.02);
        }

        .event-item.scale-large {
          background: linear-gradient(135deg, #FF6B6B 0%, #EE5A24 100%);
        }

        .event-item.scale-medium {
          background: linear-gradient(135deg, #FFA726 0%, #FF7043 100%);
        }

        .event-item.scale-small {
          background: linear-gradient(135deg, #66BB6A 0%, #43A047 100%);
        }

        .more-events {
          color: #4A90E2;
          font-size: 0.7rem;
          cursor: pointer;
          text-align: center;
          padding: 2px;
          border-radius: 3px;
          background: #e0f2fe;
        }

        .more-events:hover {
          background: #b3e5fc;
        }

        /* 이벤트 모달 */
        .event-modal {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0,0,0,0.5);
          z-index: 1000;
          justify-content: center;
          align-items: center;
        }

        .event-modal-content {
          background: white;
          border-radius: 12px;
          padding: 30px;
          max-width: 500px;
          width: 90%;
          max-height: 80vh;
          overflow-y: auto;
        }

        .event-modal-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          padding-bottom: 15px;
          border-bottom: 2px solid #e2e8f0;
        }

        .event-modal-title {
          color: #2E86AB;
          font-size: 1.5rem;
          font-weight: 700;
        }

        .modal-close {
          background: none;
          border: none;
          font-size: 1.5rem;
          cursor: pointer;
          color: #64748b;
          padding: 5px;
        }

        .modal-close:hover {
          color: #2E86AB;
        }

        .event-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .event-list-item {
          padding: 15px;
          border-bottom: 1px solid #e2e8f0;
          transition: background 0.2s;
        }

        .event-list-item:hover {
          background: #f8fafc;
        }

        .event-list-item:last-child {
          border-bottom: none;
        }

        .event-title {
          font-weight: 600;
          color: #1e293b;
          margin-bottom: 5px;
        }

        .event-details {
          color: #64748b;
          font-size: 0.9rem;
          display: flex;
          flex-direction: column;
          gap: 3px;
        }

        .event-scale-badge {
          display: inline-block;
          padding: 2px 8px;
          border-radius: 12px;
          font-size: 0.7rem;
          font-weight: 500;
          margin-left: 8px;
        }

        .event-scale-badge.large { background: #FFEBEE; color: #C62828; }
        .event-scale-badge.medium { background: #FFF3E0; color: #E65100; }
        .event-scale-badge.small { background: #E8F5E8; color: #2E7D32; }

        .networking-icon {
          color: #4A90E2;
          margin-left: 5px;
        }

        /* 반응형 */
        @media (max-width: 768px) {
          .events-container {
            padding: 20px 10px;
          }
          
          .events-header {
            margin-top: 20px;
            padding: 30px 20px;
          }
          
          .events-header h1 {
            font-size: 2rem;
          }
          
          .events-controls {
            flex-direction: column;
            gap: 15px;
          }
          
          .events-navigation {
            flex-direction: column;
            gap: 15px;
          }
          
          .calendar-day {
            min-height: 80px;
            padding: 5px;
          }
          
          .calendar-day-number {
            font-size: 0.9rem;
          }
          
          .event-item {
            font-size: 0.7rem;
            padding: 3px 6px;
          }
        }
      `}</style>
    </Layout>
  );
};

export default EventsPage;