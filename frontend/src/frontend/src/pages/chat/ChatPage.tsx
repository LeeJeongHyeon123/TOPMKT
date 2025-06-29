import React, { useState, useCallback } from 'react';
import { useChatRooms } from '../../hooks/api/useChatQueries';
import { useAuth } from '../../context/AuthContext';
import VirtualizedChat from '../../components/chat/VirtualizedChat';
import LoadingSpinner from '../../components/common/LoadingSpinner';
import LazyImage from '../../components/common/LazyImage';
import SEOHead from '../../components/common/SEOHead';
import { usePageMeta } from '../../hooks/usePageMeta';

// 메모이제이션된 채팅방 목록 아이템
const ChatRoomItem = React.memo<{
  room: any;
  isActive: boolean;
  onClick: () => void;
}>(({ room, isActive, onClick }) => {
  return (
    <div 
      className={`chat-room-item ${isActive ? 'active' : ''}`}
      onClick={onClick}
    >
      <div className="room-avatar">
        <LazyImage 
          src={room.avatar || '/assets/images/default-room.png'} 
          alt={room.name}
          width={48}
          height={48}
          objectFit="cover"
          priority={false}
        />
        {room.unreadCount > 0 && (
          <span className="unread-badge">{room.unreadCount}</span>
        )}
      </div>
      
      <div className="room-info">
        <div className="room-header">
          <h3 className="room-name">{room.name}</h3>
          <span className="room-type">{
            room.type === 'general' ? '전체' :
            room.type === 'group' ? '그룹' : '개인'
          }</span>
        </div>
        
        {room.lastMessage && (
          <p className="last-message">{room.lastMessage}</p>
        )}
        
        {room.lastMessageTime && (
          <time className="last-message-time">
            {new Date(room.lastMessageTime).toLocaleDateString('ko-KR', {
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            })}
          </time>
        )}
        
        {room.description && (
          <p className="room-description">{room.description}</p>
        )}
      </div>
    </div>
  );
});

ChatRoomItem.displayName = 'ChatRoomItem';

const ChatPage: React.FC = () => {
  const [selectedRoomId, setSelectedRoomId] = useState<string>('general');
  const { isAuthenticated, user } = useAuth();
  
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '실시간 채팅',
    description: '탑마케팅 회원들과 실시간으로 소통할 수 있는 채팅 공간입니다.',
    ogType: 'website'
  });

  // 채팅방 목록 조회
  const {
    data: chatRooms,
    isLoading: isRoomsLoading,
    isError: isRoomsError,
    error: roomsError
  } = useChatRooms();

  // 채팅방 선택 핸들러
  const handleRoomSelect = useCallback((roomId: string) => {
    setSelectedRoomId(roomId);
  }, []);

  // 인증되지 않은 사용자
  if (!isAuthenticated) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="chat-page">
          <div className="auth-required">
            <i className="fas fa-lock"></i>
            <h2>로그인이 필요합니다</h2>
            <p>채팅 기능을 사용하려면 로그인해주세요.</p>
            <a href="/login" className="btn btn-primary">
              로그인하기
            </a>
          </div>
        </div>
      </>
    );
  }

  // 채팅방 목록 로딩 중
  if (isRoomsLoading) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="chat-page">
          <div className="chat-loading">
            <LoadingSpinner />
            <p>채팅방 목록을 불러오는 중...</p>
          </div>
        </div>
      </>
    );
  }

  // 채팅방 목록 로딩 에러
  if (isRoomsError) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="chat-page">
          <div className="chat-error">
            <i className="fas fa-exclamation-triangle"></i>
            <h2>채팅방을 불러오는데 실패했습니다</h2>
            <p>{roomsError?.message || '알 수 없는 오류가 발생했습니다.'}</p>
          </div>
        </div>
      </>
    );
  }

  const selectedRoom = chatRooms?.find(room => room.id === selectedRoomId);

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="chat-page">
        {/* 페이지 헤더 */}
        <section className="chat-header">
          <div className="container">
            <h1>
              <i className="fas fa-comments"></i>
              실시간 채팅
            </h1>
            <p>탑마케팅 회원들과 실시간으로 소통하세요</p>
          </div>
        </section>

        {/* 메인 채팅 인터페이스 */}
        <div className="container">
          <div className="chat-container">
            {/* 채팅방 목록 */}
            <aside className="chat-sidebar">
              <div className="sidebar-header">
                <h2>채팅방 목록</h2>
                <span className="online-indicator">
                  <i className="fas fa-circle text-green-500"></i>
                  온라인
                </span>
              </div>
              
              <div className="rooms-list">
                {chatRooms?.map(room => (
                  <ChatRoomItem
                    key={room.id}
                    room={room}
                    isActive={room.id === selectedRoomId}
                    onClick={() => handleRoomSelect(room.id)}
                  />
                ))}
              </div>
              
              {/* 사용자 정보 */}
              <div className="user-info">
                <LazyImage 
                  src="/assets/images/default-avatar.png" 
                  alt={user?.email || '사용자'}
                  className="user-avatar"
                  width={36}
                  height={36}
                  objectFit="cover"
                  priority={false}
                />
                <div className="user-details">
                  <span className="user-name">{user?.email}</span>
                  <span className="user-status">온라인</span>
                </div>
              </div>
            </aside>

            {/* 메인 채팅 영역 */}
            <main className="chat-main">
              {selectedRoom ? (
                <>
                  {/* 채팅방 헤더 */}
                  <div className="chat-room-header">
                    <div className="room-info-header">
                      <h2>{selectedRoom.name}</h2>
                      <span className="participants-count">
                        <i className="fas fa-users"></i>
                        {selectedRoom.participants.length}명
                      </span>
                    </div>
                    {selectedRoom.description && (
                      <p className="room-description">{selectedRoom.description}</p>
                    )}
                  </div>

                  {/* 가상화된 채팅 컴포넌트 */}
                  <VirtualizedChat 
                    roomId={selectedRoomId} 
                    height={600}
                  />
                </>
              ) : (
                <div className="no-room-selected">
                  <i className="fas fa-comment-dots"></i>
                  <h3>채팅방을 선택해주세요</h3>
                  <p>왼쪽 목록에서 참여하고 싶은 채팅방을 선택하세요.</p>
                </div>
              )}
            </main>
          </div>
        </div>
      </div>

      {/* 스타일 */}
      <style>{`
        .chat-page {
          background: #f8fafc;
          min-height: calc(100vh - 80px);
        }

        .chat-header {
          background: linear-gradient(135deg, #10b981 0%, #059669 100%);
          color: white;
          padding: 2rem 0;
          text-align: center;
        }

        .chat-header h1 {
          font-size: 2rem;
          font-weight: bold;
          margin-bottom: 0.5rem;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 1rem;
        }

        .chat-header i {
          font-size: 1.5rem;
          color: #fbbf24;
        }

        .chat-header p {
          font-size: 1rem;
          opacity: 0.9;
        }

        .chat-container {
          display: grid;
          grid-template-columns: 300px 1fr;
          gap: 1.5rem;
          padding: 2rem 0;
          height: calc(100vh - 200px);
          min-height: 600px;
        }

        .chat-sidebar {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          display: flex;
          flex-direction: column;
          overflow: hidden;
        }

        .sidebar-header {
          padding: 1.5rem;
          border-bottom: 1px solid #e5e7eb;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .sidebar-header h2 {
          font-size: 1.1rem;
          font-weight: 600;
          color: #1f2937;
        }

        .online-indicator {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.85rem;
          color: #059669;
        }

        .rooms-list {
          flex: 1;
          overflow-y: auto;
          padding: 0.5rem;
        }

        .chat-room-item {
          display: flex;
          align-items: center;
          gap: 1rem;
          padding: 1rem;
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.2s ease;
          margin-bottom: 0.5rem;
        }

        .chat-room-item:hover {
          background: #f3f4f6;
        }

        .chat-room-item.active {
          background: #eff6ff;
          border: 1px solid #3b82f6;
        }

        .room-avatar {
          position: relative;
          width: 48px;
          height: 48px;
          border-radius: 50%;
          overflow: hidden;
          flex-shrink: 0;
        }

        .room-avatar img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }

        .unread-badge {
          position: absolute;
          top: -4px;
          right: -4px;
          background: #ef4444;
          color: white;
          border-radius: 50%;
          width: 20px;
          height: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.7rem;
          font-weight: 600;
        }

        .room-info {
          flex: 1;
          min-width: 0;
        }

        .room-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 0.25rem;
        }

        .room-name {
          font-size: 0.9rem;
          font-weight: 600;
          color: #1f2937;
          truncate: ellipsis;
        }

        .room-type {
          font-size: 0.7rem;
          color: #6b7280;
          background: #f3f4f6;
          padding: 0.2rem 0.5rem;
          border-radius: 12px;
        }

        .last-message {
          font-size: 0.8rem;
          color: #6b7280;
          margin: 0.25rem 0;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .last-message-time {
          font-size: 0.7rem;
          color: #9ca3af;
        }

        .room-description {
          font-size: 0.75rem;
          color: #9ca3af;
          margin-top: 0.25rem;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .user-info {
          padding: 1rem;
          border-top: 1px solid #e5e7eb;
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .user-avatar {
          width: 36px;
          height: 36px;
          border-radius: 50%;
          object-fit: cover;
        }

        .user-details {
          display: flex;
          flex-direction: column;
        }

        .user-name {
          font-size: 0.85rem;
          font-weight: 600;
          color: #1f2937;
        }

        .user-status {
          font-size: 0.75rem;
          color: #059669;
        }

        .chat-main {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          display: flex;
          flex-direction: column;
          overflow: hidden;
        }

        .chat-room-header {
          padding: 1.5rem;
          border-bottom: 1px solid #e5e7eb;
        }

        .room-info-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 0.5rem;
        }

        .room-info-header h2 {
          font-size: 1.25rem;
          font-weight: 600;
          color: #1f2937;
        }

        .participants-count {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.85rem;
          color: #6b7280;
        }

        .chat-room-header .room-description {
          font-size: 0.9rem;
          color: #6b7280;
          margin: 0;
          white-space: normal;
        }

        .no-room-selected {
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          color: #6b7280;
          text-align: center;
        }

        .no-room-selected i {
          font-size: 4rem;
          margin-bottom: 1rem;
          opacity: 0.5;
        }

        .no-room-selected h3 {
          font-size: 1.5rem;
          color: #374151;
          margin-bottom: 0.5rem;
        }

        .auth-required,
        .chat-loading,
        .chat-error {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          height: calc(100vh - 200px);
          text-align: center;
          color: #6b7280;
        }

        .auth-required i,
        .chat-loading i,
        .chat-error i {
          font-size: 4rem;
          margin-bottom: 1.5rem;
          opacity: 0.5;
        }

        .auth-required h2,
        .chat-error h2 {
          font-size: 1.5rem;
          color: #374151;
          margin-bottom: 0.5rem;
        }

        .auth-required p,
        .chat-loading p,
        .chat-error p {
          margin-bottom: 2rem;
        }

        .chat-error {
          color: #dc2626;
        }

        @media (max-width: 1024px) {
          .chat-container {
            grid-template-columns: 1fr;
            grid-template-rows: auto 1fr;
          }

          .chat-sidebar {
            height: 200px;
          }

          .rooms-list {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
          }

          .chat-room-item {
            min-width: 200px;
            margin-bottom: 0;
          }
        }

        @media (max-width: 768px) {
          .chat-header h1 {
            font-size: 1.5rem;
          }

          .chat-container {
            padding: 1rem 0;
          }

          .sidebar-header {
            padding: 1rem;
          }

          .chat-room-header {
            padding: 1rem;
          }

          .room-info-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
          }
        }
      `}</style>
    </>
  );
};

export default ChatPage;