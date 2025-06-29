import React, { useCallback, useEffect, useRef, useState, useMemo } from 'react';
import { FixedSizeList as List } from 'react-window';
import InfiniteLoader from 'react-window-infinite-loader';
import { useInfiniteMessages, useRealtimeMessages, useSendMessage, useMarkMessagesAsRead, ChatMessage } from '../../hooks/api/useChatQueries';
import { useAuth } from '../../context/AuthContext';
import LoadingSpinner from '../common/LoadingSpinner';
import LazyImage from '../common/LazyImage';

interface VirtualizedChatProps {
  roomId: string;
  height?: number;
}

interface MessageItemProps {
  index: number;
  style: React.CSSProperties;
  data: {
    messages: ChatMessage[];
    currentUserId: string;
    hasNextPage: boolean;
    isNextPageLoading: boolean;
  };
}

// 메모이제이션된 메시지 아이템 컴포넌트
const MessageItem = React.memo<MessageItemProps>(({ index, style, data }) => {
  const { messages, currentUserId, hasNextPage, isNextPageLoading } = data;
  
  // 로딩 인디케이터 표시 (맨 위)
  if (index === 0 && hasNextPage) {
    return (
      <div style={style} className="message-loading">
        {isNextPageLoading ? (
          <div className="loading-indicator">
            <LoadingSpinner />
            <span>이전 메시지를 불러오는 중...</span>
          </div>
        ) : (
          <div className="load-more-trigger">
            ↑ 스크롤하여 이전 메시지 보기
          </div>
        )}
      </div>
    );
  }

  // 메시지 인덱스 조정 (로딩 인디케이터가 있는 경우)
  const messageIndex = hasNextPage ? index - 1 : index;
  const message = messages[messageIndex];

  if (!message) {
    return <div style={style} className="message-placeholder" />;
  }

  const isOwn = message.userId === currentUserId;
  const isFirstInGroup = messageIndex === 0 || 
    messages[messageIndex - 1]?.userId !== message.userId ||
    (message.timestamp - (messages[messageIndex - 1]?.timestamp || 0)) > 300000; // 5분 이상 차이

  return (
    <div style={style} className={`message-container ${isOwn ? 'own' : 'other'}`}>
      <div className="message-wrapper">
        {/* 프로필 이미지 (타인 메시지이고 그룹의 첫 메시지인 경우) */}
        {!isOwn && isFirstInGroup && (
          <div className="message-avatar">
            <LazyImage 
              src="/assets/images/default-avatar.png" 
              alt={message.userNickname}
              width={32}
              height={32}
              objectFit="cover"
              priority={false}
            />
          </div>
        )}
        
        <div className={`message-content ${!isOwn && !isFirstInGroup ? 'continuation' : ''}`}>
          {/* 닉네임 (타인 메시지이고 그룹의 첫 메시지인 경우) */}
          {!isOwn && isFirstInGroup && (
            <div className="message-author">{message.userNickname}</div>
          )}
          
          <div className="message-bubble-container">
            <div className={`message-bubble ${message.type || 'text'}`}>
              {message.type === 'text' && (
                <div className="message-text">{message.message}</div>
              )}
              {message.type === 'image' && (
                <div className="message-image">
                  <LazyImage 
                    src={message.message} 
                    alt="첨부 이미지" 
                    width={200}
                    height={200}
                    objectFit="cover"
                    priority={false}
                  />
                </div>
              )}
              {message.type === 'file' && (
                <div className="message-file">
                  <i className="fas fa-file"></i>
                  <span>{message.message}</span>
                </div>
              )}
              
              {message.edited && (
                <span className="edited-indicator">(편집됨)</span>
              )}
            </div>
            
            <div className="message-meta">
              <time className="message-time">
                {new Date(message.timestamp).toLocaleTimeString('ko-KR', {
                  hour: '2-digit',
                  minute: '2-digit'
                })}
              </time>
              {!message.isRead && isOwn && (
                <span className="unread-indicator">1</span>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
});

MessageItem.displayName = 'MessageItem';

// 메모이제이션된 메시지 입력 컴포넌트
const MessageInput = React.memo<{
  onSendMessage: (message: string) => void;
  disabled?: boolean;
}>(({ onSendMessage, disabled = false }) => {
  const [message, setMessage] = useState('');

  const handleSubmit = useCallback((e: React.FormEvent) => {
    e.preventDefault();
    if (message.trim() && !disabled) {
      onSendMessage(message.trim());
      setMessage('');
    }
  }, [message, onSendMessage, disabled]);

  const handleKeyPress = useCallback((e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSubmit(e as any);
    }
  }, [handleSubmit]);

  return (
    <form onSubmit={handleSubmit} className="message-input-container">
      <div className="message-input-wrapper">
        <textarea
          value={message}
          onChange={(e) => setMessage(e.target.value)}
          onKeyPress={handleKeyPress}
          placeholder="메시지를 입력하세요... (Shift+Enter로 줄바꿈)"
          className="message-input"
          rows={1}
          disabled={disabled}
        />
        <button 
          type="submit" 
          className="send-button"
          disabled={!message.trim() || disabled}
        >
          <i className="fas fa-paper-plane"></i>
        </button>
      </div>
    </form>
  );
});

MessageInput.displayName = 'MessageInput';

const VirtualizedChat: React.FC<VirtualizedChatProps> = ({ roomId, height = 600 }) => {
  const { user } = useAuth();
  const listRef = useRef<List>(null);
  const [autoScroll, setAutoScroll] = useState(true);
  
  // React Query 훅들
  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    isLoading,
    isError,
    error
  } = useInfiniteMessages(roomId);

  const { isConnected } = useRealtimeMessages(roomId);
  const sendMessageMutation = useSendMessage();
  const markAsReadMutation = useMarkMessagesAsRead();

  // 모든 메시지를 평면화
  const allMessages = useMemo(() => {
    return data?.pages.flatMap(page => page.messages) || [];
  }, [data]);

  // 아이템 개수 (로딩 인디케이터 포함)
  const itemCount = hasNextPage ? allMessages.length + 1 : allMessages.length;

  // 아이템이 로드되었는지 확인
  const isItemLoaded = useCallback((index: number) => {
    if (hasNextPage && index === 0) {
      return !isFetchingNextPage;
    }
    const messageIndex = hasNextPage ? index - 1 : index;
    return !!allMessages[messageIndex];
  }, [allMessages, hasNextPage, isFetchingNextPage]);

  // 더 많은 아이템 로드
  const loadMoreItems = useCallback(async () => {
    if (hasNextPage && !isFetchingNextPage) {
      await fetchNextPage();
    }
  }, [hasNextPage, isFetchingNextPage, fetchNextPage]);

  // 메시지 전송 핸들러
  const handleSendMessage = useCallback((message: string) => {
    if (!user) return;

    sendMessageMutation.mutate({
      roomId,
      message,
      userId: String(user.id),
      userNickname: user.nickname || user.email || 'Unknown User',
      type: 'text'
    });
  }, [user, roomId, sendMessageMutation]);

  // 자동 스크롤 (새 메시지가 추가될 때)
  useEffect(() => {
    if (autoScroll && allMessages.length > 0 && listRef.current) {
      // 새 메시지가 추가되면 맨 아래로 스크롤
      listRef.current.scrollToItem(itemCount - 1, 'end');
    }
  }, [allMessages.length, autoScroll, itemCount]);

  // 읽음 처리
  useEffect(() => {
    if (user && allMessages.length > 0) {
      markAsReadMutation.mutate({
        roomId,
        userId: String(user.id)
      });
    }
  }, [user, roomId, allMessages.length, markAsReadMutation]);

  // 스크롤 이벤트 핸들러
  const handleScroll = useCallback(({ scrollOffset, scrollUpdateWasRequested }: any) => {
    if (!scrollUpdateWasRequested) {
      // 사용자가 수동으로 스크롤한 경우
      const maxScrollOffset = Math.max(0, (itemCount * 80) - height); // 80px per item
      const isAtBottom = scrollOffset >= maxScrollOffset - 100;
      setAutoScroll(isAtBottom);
    }
  }, [itemCount, height]);

  // 맨 아래로 스크롤 버튼
  const scrollToBottom = useCallback(() => {
    if (listRef.current) {
      listRef.current.scrollToItem(itemCount - 1, 'end');
      setAutoScroll(true);
    }
  }, [itemCount]);

  if (isError) {
    return (
      <div className="chat-error">
        <i className="fas fa-exclamation-triangle"></i>
        <p>채팅을 불러오는데 실패했습니다</p>
        <p>{error?.message}</p>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div className="chat-loading">
        <LoadingSpinner />
        <p>채팅을 불러오는 중...</p>
      </div>
    );
  }

  return (
    <div className="virtualized-chat">
      {/* 연결 상태 표시 */}
      <div className={`connection-status ${isConnected ? 'connected' : 'disconnected'}`}>
        <i className={`fas fa-circle ${isConnected ? 'text-green-500' : 'text-red-500'}`}></i>
        {isConnected ? '연결됨' : '연결 끊어짐'}
      </div>

      {/* 메시지 리스트 */}
      <div className="messages-container">
        <InfiniteLoader
          isItemLoaded={isItemLoaded}
          itemCount={itemCount}
          loadMoreItems={loadMoreItems}
          threshold={5}
        >
          {({ onItemsRendered, ref }: { onItemsRendered: any; ref: any }) => (
            <List
              ref={(list) => {
                listRef.current = list;
                ref(list);
              }}
              height={height - 120} // 입력창과 상태바 높이 제외
              width="100%"
              itemCount={itemCount}
              itemSize={80}
              onItemsRendered={onItemsRendered}
              onScroll={handleScroll}
              itemData={{
                messages: allMessages,
                currentUserId: String(user?.id || ''),
                hasNextPage,
                isNextPageLoading: isFetchingNextPage
              }}
            >
              {MessageItem}
            </List>
          )}
        </InfiniteLoader>

        {/* 맨 아래로 스크롤 버튼 */}
        {!autoScroll && (
          <button onClick={scrollToBottom} className="scroll-to-bottom">
            <i className="fas fa-arrow-down"></i>
            <span>새 메시지</span>
          </button>
        )}
      </div>

      {/* 메시지 입력 */}
      <MessageInput 
        onSendMessage={handleSendMessage}
        disabled={sendMessageMutation.isPending}
      />

      {/* 스타일 */}
      <style>{`
        .virtualized-chat {
          display: flex;
          flex-direction: column;
          height: ${height}px;
          background: #f8fafc;
          border-radius: 12px;
          overflow: hidden;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .connection-status {
          padding: 0.5rem 1rem;
          background: white;
          border-bottom: 1px solid #e2e8f0;
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.85rem;
          font-weight: 500;
        }

        .connection-status.connected {
          color: #065f46;
        }

        .connection-status.disconnected {
          color: #dc2626;
        }

        .messages-container {
          flex: 1;
          position: relative;
          background: white;
        }

        .message-container {
          padding: 0.5rem 1rem;
          display: flex;
          align-items: flex-end;
        }

        .message-container.own {
          justify-content: flex-end;
        }

        .message-wrapper {
          display: flex;
          align-items: flex-end;
          gap: 0.5rem;
          max-width: 70%;
        }

        .message-avatar {
          width: 32px;
          height: 32px;
          border-radius: 50%;
          overflow: hidden;
          flex-shrink: 0;
        }

        .message-avatar img {
          width: 100%;
          height: 100%;
          object-fit: cover;
        }

        .message-content {
          display: flex;
          flex-direction: column;
          align-items: flex-start;
        }

        .message-container.own .message-content {
          align-items: flex-end;
        }

        .message-content.continuation {
          margin-left: 42px; /* 32px avatar + 10px gap */
        }

        .message-author {
          font-size: 0.75rem;
          color: #6b7280;
          margin-bottom: 0.25rem;
          font-weight: 500;
        }

        .message-bubble-container {
          display: flex;
          align-items: flex-end;
          gap: 0.5rem;
        }

        .message-container.own .message-bubble-container {
          flex-direction: row-reverse;
        }

        .message-bubble {
          background: white;
          border: 1px solid #e5e7eb;
          border-radius: 18px;
          padding: 0.75rem 1rem;
          max-width: 100%;
          word-wrap: break-word;
          position: relative;
        }

        .message-container.own .message-bubble {
          background: #3b82f6;
          color: white;
          border-color: #3b82f6;
        }

        .message-text {
          line-height: 1.4;
          white-space: pre-wrap;
        }

        .message-image img {
          max-width: 200px;
          max-height: 200px;
          border-radius: 8px;
          cursor: pointer;
        }

        .message-file {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          padding: 0.5rem;
          background: #f3f4f6;
          border-radius: 8px;
          cursor: pointer;
        }

        .message-container.own .message-file {
          background: rgba(255, 255, 255, 0.2);
        }

        .edited-indicator {
          font-size: 0.75rem;
          opacity: 0.7;
          font-style: italic;
          margin-left: 0.5rem;
        }

        .message-meta {
          display: flex;
          align-items: center;
          gap: 0.25rem;
          font-size: 0.7rem;
          color: #9ca3af;
          white-space: nowrap;
        }

        .message-time {
          font-size: 0.7rem;
          color: #9ca3af;
        }

        .unread-indicator {
          background: #f59e0b;
          color: white;
          border-radius: 50%;
          width: 16px;
          height: 16px;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 0.6rem;
          font-weight: 600;
        }

        .message-loading {
          display: flex;
          justify-content: center;
          align-items: center;
          padding: 1rem;
          color: #6b7280;
        }

        .loading-indicator {
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.85rem;
        }

        .load-more-trigger {
          font-size: 0.85rem;
          color: #9ca3af;
          cursor: pointer;
          padding: 0.5rem;
          border-radius: 8px;
          transition: all 0.2s ease;
        }

        .load-more-trigger:hover {
          background: #f3f4f6;
          color: #6b7280;
        }

        .message-placeholder {
          height: 80px;
        }

        .scroll-to-bottom {
          position: absolute;
          bottom: 1rem;
          right: 1rem;
          background: #3b82f6;
          color: white;
          border: none;
          border-radius: 24px;
          padding: 0.75rem 1rem;
          display: flex;
          align-items: center;
          gap: 0.5rem;
          font-size: 0.85rem;
          cursor: pointer;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
          transition: all 0.2s ease;
          z-index: 10;
        }

        .scroll-to-bottom:hover {
          background: #2563eb;
          transform: translateY(-2px);
          box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        }

        .message-input-container {
          background: white;
          border-top: 1px solid #e2e8f0;
          padding: 1rem;
        }

        .message-input-wrapper {
          display: flex;
          align-items: flex-end;
          gap: 0.75rem;
          background: #f8fafc;
          border: 1px solid #e2e8f0;
          border-radius: 24px;
          padding: 0.75rem 1rem;
          transition: all 0.2s ease;
        }

        .message-input-wrapper:focus-within {
          border-color: #3b82f6;
          box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .message-input {
          flex: 1;
          border: none;
          background: transparent;
          resize: none;
          outline: none;
          font-size: 0.9rem;
          line-height: 1.4;
          min-height: 20px;
          max-height: 100px;
          padding: 0;
        }

        .message-input::placeholder {
          color: #9ca3af;
        }

        .send-button {
          background: #3b82f6;
          color: white;
          border: none;
          border-radius: 50%;
          width: 36px;
          height: 36px;
          display: flex;
          align-items: center;
          justify-content: center;
          cursor: pointer;
          transition: all 0.2s ease;
          flex-shrink: 0;
        }

        .send-button:hover:not(:disabled) {
          background: #2563eb;
          transform: scale(1.05);
        }

        .send-button:disabled {
          background: #d1d5db;
          cursor: not-allowed;
          transform: none;
        }

        .chat-error,
        .chat-loading {
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          height: ${height}px;
          color: #6b7280;
        }

        .chat-error i,
        .chat-loading i {
          font-size: 3rem;
          margin-bottom: 1rem;
          opacity: 0.5;
        }

        .chat-error {
          color: #dc2626;
        }

        @media (max-width: 768px) {
          .message-wrapper {
            max-width: 85%;
          }

          .message-bubble {
            padding: 0.5rem 0.75rem;
          }

          .message-input-wrapper {
            padding: 0.5rem 0.75rem;
          }
        }
      `}</style>
    </div>
  );
};

export default VirtualizedChat;