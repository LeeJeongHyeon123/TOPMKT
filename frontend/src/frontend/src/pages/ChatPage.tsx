import React, { useState, useEffect, useRef } from 'react';
import { useAuth } from '../context/AuthContext';
import { usePageMeta } from '../hooks/usePageMeta';
import SEOHead from '../components/common/SEOHead';

// 타입 정의
interface ChatRoom {
  id: string;
  name: string;
  type: 'direct' | 'group' | 'general';
  lastMessage?: string;
  lastMessageTime?: string;
  unreadCount: number;
  participants: string[];
  avatar?: string;
}

interface Message {
  id: string;
  roomId: string;
  senderId: string;
  senderName: string;
  content: string;
  timestamp: string;
  type: 'text' | 'image' | 'file';
  isRead: boolean;
}

const ChatPage: React.FC = () => {
  const [chatRooms, setChatRooms] = useState<ChatRoom[]>([]);
  const [activeRoom, setActiveRoom] = useState<string | null>(null);
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const [isConnected, setIsConnected] = useState(false);
  const [onlineUsers, setOnlineUsers] = useState<string[]>([]);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const { user, isAuthenticated } = useAuth();
  
  // SEO 메타 데이터
  const metaData = usePageMeta({
    title: '실시간 채팅',
    description: '네트워크 마케팅 전문가들과 실시간으로 소통하고 정보를 공유하세요',
    ogType: 'website'
  });

  // 임시 채팅방 데이터
  const mockChatRooms: ChatRoom[] = [
    {
      id: 'general',
      name: '전체 채팅',
      type: 'general',
      lastMessage: '안녕하세요! 새로 가입했습니다.',
      lastMessageTime: '14:32',
      unreadCount: 3,
      participants: ['user1', 'user2', 'user3', 'user4']
    },
    {
      id: 'marketing-tips',
      name: '마케팅 팁 공유',
      type: 'group',
      lastMessage: 'SNS 광고 효과가 정말 좋네요',
      lastMessageTime: '13:45',
      unreadCount: 1,
      participants: ['user1', 'user2', 'user5']
    },
    {
      id: 'success-stories',
      name: '성공사례 공유',
      type: 'group',
      lastMessage: '이번 달 목표 달성했습니다!',
      lastMessageTime: '12:30',
      unreadCount: 0,
      participants: ['user1', 'user3', 'user6']
    },
    {
      id: 'global-network',
      name: '글로벌 네트워크',
      type: 'group',
      lastMessage: 'Hello from Singapore!',
      lastMessageTime: '11:15',
      unreadCount: 5,
      participants: ['user1', 'user7', 'user8']
    },
    {
      id: 'direct-user2',
      name: '김마케터',
      type: 'direct',
      lastMessage: '내일 미팅 시간 조율 가능하신가요?',
      lastMessageTime: '10:20',
      unreadCount: 2,
      participants: ['user1', 'user2']
    }
  ];

  // 임시 메시지 데이터
  const mockMessages: Message[] = [
    {
      id: '1',
      roomId: 'general',
      senderId: 'user2',
      senderName: '김마케터',
      content: '안녕하세요! 새로 가입했습니다.',
      timestamp: '2024-01-26 14:32:00',
      type: 'text',
      isRead: true
    },
    {
      id: '2',
      roomId: 'general',
      senderId: 'user3',
      senderName: '이성공',
      content: '환영합니다! 궁금한 점 있으시면 언제든 물어보세요.',
      timestamp: '2024-01-26 14:33:00',
      type: 'text',
      isRead: true
    },
    {
      id: '3',
      roomId: 'general',
      senderId: 'user1',
      senderName: user?.nickname || '나',
      content: '네 감사합니다! 잘 부탁드립니다.',
      timestamp: '2024-01-26 14:34:00',
      type: 'text',
      isRead: true
    }
  ];

  useEffect(() => {
    if (isAuthenticated) {
      setChatRooms(mockChatRooms);
      setActiveRoom('general');
      setMessages(mockMessages);
      setIsConnected(true);
      setOnlineUsers(['user2', 'user3', 'user5']);
    }
  }, [isAuthenticated]);

  // 메시지 목록 끝으로 스크롤
  useEffect(() => {
    scrollToBottom();
  }, [messages]);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  // 채팅방 선택
  const selectRoom = (roomId: string) => {
    setActiveRoom(roomId);
    // 실제 구현시 해당 채팅방 메시지 로드
    const roomMessages = mockMessages.filter(msg => msg.roomId === roomId);
    setMessages(roomMessages);
    
    // 읽지 않은 메시지 수 업데이트
    setChatRooms(prev => prev.map(room => 
      room.id === roomId ? { ...room, unreadCount: 0 } : room
    ));
  };

  // 메시지 전송
  const sendMessage = (e: React.FormEvent) => {
    e.preventDefault();
    if (!newMessage.trim() || !activeRoom || !user) return;

    const message: Message = {
      id: Date.now().toString(),
      roomId: activeRoom,
      senderId: user.id?.toString() || 'user1',
      senderName: user.nickname,
      content: newMessage.trim(),
      timestamp: new Date().toISOString(),
      type: 'text',
      isRead: false
    };

    setMessages(prev => [...prev, message]);
    setNewMessage('');

    // 채팅방 목록의 마지막 메시지 업데이트
    setChatRooms(prev => prev.map(room => 
      room.id === activeRoom 
        ? { 
            ...room, 
            lastMessage: message.content,
            lastMessageTime: new Date().toLocaleTimeString('ko-KR', { 
              hour: '2-digit', 
              minute: '2-digit' 
            })
          }
        : room
    ));
  };

  // 시간 포맷팅
  const formatTime = (timestamp: string) => {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('ko-KR', { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  };

  // 날짜 포맷팅
  const formatDate = (timestamp: string) => {
    const date = new Date(timestamp);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
      return '오늘';
    } else if (date.toDateString() === yesterday.toDateString()) {
      return '어제';
    } else {
      return date.toLocaleDateString('ko-KR', {
        month: 'long',
        day: 'numeric'
      });
    }
  };

  const activeRoomData = chatRooms.find(room => room.id === activeRoom);
  const roomMessages = messages.filter(msg => msg.roomId === activeRoom);

  if (!isAuthenticated) {
    return (
      <>
        <SEOHead {...metaData} />
        <div className="chat-page">
          <div className="chat-login-required">
            <div className="login-message">
              <i className="fas fa-lock"></i>
              <h2>로그인이 필요합니다</h2>
              <p>실시간 채팅 기능을 사용하려면 로그인해주세요.</p>
              <a href="/auth/login" className="btn btn-primary">
                로그인하기
              </a>
            </div>
          </div>
        </div>
      </>
    );
  }

  return (
    <>
      <SEOHead {...metaData} />
      
      <div className="chat-page">
        {/* 채팅 헤더 */}
        <section className="chat-header">
          <div className="container">
            <h1>
              <i className="fas fa-comments"></i>
              실시간 채팅
            </h1>
            <p>네트워크 마케팅 전문가들과 실시간으로 소통하고 정보를 공유하세요</p>
            <div className="connection-status">
              <span className={`status-indicator ${isConnected ? 'connected' : 'disconnected'}`}>
                <i className={`fas fa-circle ${isConnected ? 'text-green-500' : 'text-red-500'}`}></i>
                {isConnected ? '연결됨' : '연결 중...'}
              </span>
              <span className="online-count">
                <i className="fas fa-users"></i>
                온라인 {onlineUsers.length}명
              </span>
            </div>
          </div>
        </section>

        <div className="chat-container">
          <div className="chat-layout">
            {/* 채팅방 목록 사이드바 */}
            <aside className="chat-sidebar">
              <div className="sidebar-header">
                <h3 className="sidebar-title">채팅방</h3>
                <button className="new-chat-btn">
                  <i className="fas fa-plus"></i>
                  새 채팅
                </button>
              </div>
              
              <div className="chat-rooms-list">
                {chatRooms.map(room => (
                  <div
                    key={room.id}
                    className={`chat-room-item ${activeRoom === room.id ? 'active' : ''}`}
                    onClick={() => selectRoom(room.id)}
                  >
                    <div className="room-info">
                      <div className="room-avatar">
                        {room.type === 'direct' ? (
                          <i className="fas fa-user"></i>
                        ) : room.type === 'group' ? (
                          <i className="fas fa-users"></i>
                        ) : (
                          <i className="fas fa-globe"></i>
                        )}
                      </div>
                      <div className="room-details">
                        <div className="room-name">{room.name}</div>
                        <div className="room-last-message">
                          {room.lastMessage}
                        </div>
                      </div>
                      <div className="room-meta">
                        <div className="room-time">{room.lastMessageTime}</div>
                        {room.unreadCount > 0 && (
                          <div className="unread-badge">{room.unreadCount}</div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </aside>

            {/* 채팅 메인 영역 */}
            <main className="chat-main">
              {activeRoom ? (
                <>
                  {/* 채팅방 헤더 */}
                  <div className="chat-room-header">
                    <div className="room-info">
                      <div className="room-avatar">
                        {activeRoomData?.type === 'direct' ? (
                          <i className="fas fa-user"></i>
                        ) : activeRoomData?.type === 'group' ? (
                          <i className="fas fa-users"></i>
                        ) : (
                          <i className="fas fa-globe"></i>
                        )}
                      </div>
                      <div className="room-details">
                        <h3 className="room-name">{activeRoomData?.name}</h3>
                        <div className="room-participants">
                          {activeRoomData?.participants.length}명 참여 중
                        </div>
                      </div>
                    </div>
                    <div className="room-actions">
                      <button className="action-btn">
                        <i className="fas fa-search"></i>
                      </button>
                      <button className="action-btn">
                        <i className="fas fa-cog"></i>
                      </button>
                    </div>
                  </div>

                  {/* 메시지 영역 */}
                  <div className="messages-container">
                    <div className="messages-list">
                      {roomMessages.map((message, index) => {
                        const prevMessage = roomMessages[index - 1];
                        const showDate = !prevMessage || 
                          formatDate(message.timestamp) !== formatDate(prevMessage.timestamp);
                        const isMyMessage = message.senderId === (user?.id?.toString() || 'user1');
                        const showSender = !prevMessage || 
                          prevMessage.senderId !== message.senderId ||
                          showDate;

                        return (
                          <div key={message.id}>
                            {showDate && (
                              <div className="date-divider">
                                <span>{formatDate(message.timestamp)}</span>
                              </div>
                            )}
                            <div className={`message ${isMyMessage ? 'my-message' : 'other-message'}`}>
                              {!isMyMessage && showSender && (
                                <div className="message-sender">{message.senderName}</div>
                              )}
                              <div className="message-content">
                                <div className="message-bubble">
                                  {message.content}
                                </div>
                                <div className="message-time">
                                  {formatTime(message.timestamp)}
                                </div>
                              </div>
                            </div>
                          </div>
                        );
                      })}
                      <div ref={messagesEndRef} />
                    </div>
                  </div>

                  {/* 메시지 입력 영역 */}
                  <div className="message-input-container">
                    <form onSubmit={sendMessage} className="message-input-form">
                      <div className="input-actions">
                        <button type="button" className="attachment-btn">
                          <i className="fas fa-paperclip"></i>
                        </button>
                        <button type="button" className="emoji-btn">
                          <i className="fas fa-smile"></i>
                        </button>
                      </div>
                      <input
                        type="text"
                        value={newMessage}
                        onChange={(e) => setNewMessage(e.target.value)}
                        placeholder="메시지를 입력하세요..."
                        className="message-input"
                        disabled={!isConnected}
                      />
                      <button 
                        type="submit" 
                        className="send-btn"
                        disabled={!newMessage.trim() || !isConnected}
                      >
                        <i className="fas fa-paper-plane"></i>
                      </button>
                    </form>
                  </div>
                </>
              ) : (
                <div className="no-room-selected">
                  <i className="fas fa-comments"></i>
                  <h3>채팅방을 선택하세요</h3>
                  <p>왼쪽에서 채팅방을 선택하여 대화를 시작하세요.</p>
                </div>
              )}
            </main>
          </div>
        </div>
      </div>

      {/* 채팅 페이지 스타일 */}
      <style>{`
        .chat-page {
          background: #f8fafc;
          min-height: calc(100vh - 80px);
          display: flex;
          flex-direction: column;
        }

        .chat-header {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          padding: 2rem 0;
          text-align: center;
          flex-shrink: 0;
        }

        .chat-header h1 {
          font-size: 2rem;
          font-weight: bold;
          margin-bottom: 0.5rem;
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 0.75rem;
        }

        .chat-header i {
          font-size: 1.75rem;
          color: #ffd700;
        }

        .chat-header p {
          font-size: 1rem;
          opacity: 0.9;
          margin-bottom: 1rem;
        }

        .connection-status {
          display: flex;
          justify-content: center;
          gap: 2rem;
          font-size: 0.9rem;
        }

        .status-indicator, .online-count {
          display: flex;
          align-items: center;
          gap: 0.5rem;
        }

        .chat-login-required {
          flex: 1;
          display: flex;
          align-items: center;
          justify-content: center;
        }

        .login-message {
          text-align: center;
          padding: 3rem;
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .login-message i {
          font-size: 3rem;
          color: #cbd5e0;
          margin-bottom: 1rem;
        }

        .login-message h2 {
          font-size: 1.5rem;
          color: #2d3748;
          margin-bottom: 0.5rem;
        }

        .login-message p {
          color: #718096;
          margin-bottom: 2rem;
        }

        .chat-container {
          max-width: 1400px;
          margin: 0 auto;
          padding: 1.5rem;
          flex: 1;
          display: flex;
          flex-direction: column;
          min-height: 0;
        }

        .chat-layout {
          display: grid;
          grid-template-columns: 320px 1fr;
          gap: 1.5rem;
          flex: 1;
          min-height: 0;
        }

        .chat-sidebar {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
          display: flex;
          flex-direction: column;
        }

        .sidebar-header {
          padding: 1.25rem;
          border-bottom: 1px solid #e2e8f0;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .sidebar-title {
          font-size: 1.1rem;
          font-weight: 700;
          color: #2d3748;
          margin: 0;
        }

        .new-chat-btn {
          background: #667eea;
          color: white;
          border: none;
          border-radius: 6px;
          padding: 0.5rem 0.75rem;
          font-size: 0.8rem;
          cursor: pointer;
          transition: all 0.2s ease;
          display: flex;
          align-items: center;
          gap: 0.25rem;
        }

        .new-chat-btn:hover {
          background: #5a67d8;
          transform: translateY(-1px);
        }

        .chat-rooms-list {
          flex: 1;
          overflow-y: auto;
          padding: 0.75rem;
        }

        .chat-room-item {
          padding: 0.75rem;
          border-radius: 8px;
          cursor: pointer;
          transition: all 0.2s ease;
          margin-bottom: 0.25rem;
        }

        .chat-room-item:hover {
          background: #f7fafc;
        }

        .chat-room-item.active {
          background: #edf2f7;
          border-left: 4px solid #667eea;
        }

        .room-info {
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .room-avatar {
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-weight: 600;
          font-size: 0.9rem;
          flex-shrink: 0;
        }

        .room-details {
          flex: 1;
          min-width: 0;
        }

        .room-name {
          font-weight: 600;
          color: #2d3748;
          font-size: 0.9rem;
          margin-bottom: 0.125rem;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .room-last-message {
          color: #718096;
          font-size: 0.8rem;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .room-meta {
          display: flex;
          flex-direction: column;
          align-items: flex-end;
          gap: 0.25rem;
        }

        .room-time {
          color: #a0aec0;
          font-size: 0.75rem;
        }

        .unread-badge {
          background: #f56565;
          color: white;
          border-radius: 50%;
          min-width: 20px;
          height: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.7rem;
          font-weight: 600;
        }

        .chat-main {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
          display: flex;
          flex-direction: column;
          min-height: 0;
        }

        .chat-room-header {
          padding: 1rem 1.5rem;
          border-bottom: 1px solid #e2e8f0;
          display: flex;
          justify-content: space-between;
          align-items: center;
          background: #fafafa;
          border-radius: 12px 12px 0 0;
        }

        .chat-room-header .room-info {
          display: flex;
          align-items: center;
          gap: 0.75rem;
        }

        .chat-room-header .room-name {
          font-size: 1.1rem;
          font-weight: 600;
          color: #2d3748;
          margin: 0;
        }

        .room-participants {
          color: #718096;
          font-size: 0.8rem;
        }

        .room-actions {
          display: flex;
          gap: 0.5rem;
        }

        .action-btn {
          width: 36px;
          height: 36px;
          border: 1px solid #e2e8f0;
          background: white;
          border-radius: 8px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
          color: #718096;
        }

        .action-btn:hover {
          background: #f7fafc;
          color: #4a5568;
        }

        .messages-container {
          flex: 1;
          overflow: hidden;
        }

        .messages-list {
          height: 100%;
          overflow-y: auto;
          padding: 1rem;
          display: flex;
          flex-direction: column;
          gap: 0.5rem;
        }

        .date-divider {
          text-align: center;
          margin: 1rem 0;
        }

        .date-divider span {
          background: #edf2f7;
          color: #718096;
          padding: 0.25rem 0.75rem;
          border-radius: 12px;
          font-size: 0.8rem;
        }

        .message {
          display: flex;
          flex-direction: column;
          margin-bottom: 0.5rem;
        }

        .message.my-message {
          align-items: flex-end;
        }

        .message.other-message {
          align-items: flex-start;
        }

        .message-sender {
          font-size: 0.8rem;
          color: #718096;
          margin-bottom: 0.25rem;
          padding: 0 0.75rem;
        }

        .message-content {
          display: flex;
          align-items: flex-end;
          gap: 0.5rem;
          max-width: 70%;
        }

        .message.my-message .message-content {
          flex-direction: row-reverse;
        }

        .message-bubble {
          background: #f7fafc;
          padding: 0.75rem 1rem;
          border-radius: 18px;
          word-wrap: break-word;
          line-height: 1.4;
        }

        .message.my-message .message-bubble {
          background: #667eea;
          color: white;
        }

        .message-time {
          font-size: 0.7rem;
          color: #a0aec0;
          white-space: nowrap;
        }

        .message-input-container {
          padding: 1rem 1.5rem;
          border-top: 1px solid #e2e8f0;
          background: #fafafa;
          border-radius: 0 0 12px 12px;
        }

        .message-input-form {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          background: white;
          border-radius: 24px;
          padding: 0.5rem;
          border: 1px solid #e2e8f0;
        }

        .input-actions {
          display: flex;
          gap: 0.25rem;
        }

        .attachment-btn, .emoji-btn {
          width: 36px;
          height: 36px;
          border: none;
          background: none;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
          color: #718096;
        }

        .attachment-btn:hover, .emoji-btn:hover {
          background: #f7fafc;
          color: #4a5568;
        }

        .message-input {
          flex: 1;
          border: none;
          outline: none;
          padding: 0.5rem;
          font-size: 0.9rem;
          background: none;
        }

        .message-input::placeholder {
          color: #a0aec0;
        }

        .send-btn {
          width: 36px;
          height: 36px;
          background: #667eea;
          color: white;
          border: none;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
        }

        .send-btn:hover:not(:disabled) {
          background: #5a67d8;
          transform: scale(1.05);
        }

        .send-btn:disabled {
          background: #e2e8f0;
          color: #a0aec0;
          cursor: not-allowed;
        }

        .no-room-selected {
          flex: 1;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          color: #a0aec0;
          text-align: center;
        }

        .no-room-selected i {
          font-size: 3rem;
          margin-bottom: 1rem;
        }

        .no-room-selected h3 {
          font-size: 1.25rem;
          color: #4a5568;
          margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
          .chat-layout {
            grid-template-columns: 1fr;
          }

          .chat-sidebar {
            order: 2;
            max-height: 300px;
          }

          .chat-main {
            order: 1;
            min-height: 400px;
          }

          .message-content {
            max-width: 85%;
          }
        }
      `}</style>
    </>
  );
};

export default ChatPage;