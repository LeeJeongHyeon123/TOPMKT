import { useQuery, useInfiniteQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import { useCallback, useEffect, useRef, useState } from 'react';

// Firebase 모듈 임포트 (실제 환경에서)
// import { database } from '../../services/firebase';
// import { ref, push, onValue, off, query, orderByChild, limitToLast, endBefore } from 'firebase/database';

export interface ChatMessage {
  id: string;
  userId: string;
  userNickname: string;
  message: string;
  timestamp: number;
  roomId: string;
  type?: 'text' | 'image' | 'file';
  isRead?: boolean;
  edited?: boolean;
  editedAt?: number;
  replyTo?: string;
}

export interface ChatRoom {
  id: string;
  name: string;
  type: 'direct' | 'group' | 'general';
  lastMessage?: string;
  lastMessageTime?: string;
  unreadCount: number;
  participants: string[];
  avatar?: string;
  description?: string;
  createdBy?: string;
  createdAt?: number;
}

// Query Keys
export const CHAT_QUERY_KEYS = {
  all: ['chat'] as const,
  rooms: () => [...CHAT_QUERY_KEYS.all, 'rooms'] as const,
  room: (roomId: string) => [...CHAT_QUERY_KEYS.all, 'room', roomId] as const,
  messages: (roomId: string) => [...CHAT_QUERY_KEYS.all, 'messages', roomId] as const,
  participants: (roomId: string) => [...CHAT_QUERY_KEYS.all, 'participants', roomId] as const,
} as const;

// Mock 데이터 (실제 환경에서는 Firebase에서 가져옴)
const generateMockMessages = (roomId: string, count: number = 1000): ChatMessage[] => {
  const messages: ChatMessage[] = [];
  const users = [
    { id: 'user1', nickname: '김마케터' },
    { id: 'user2', nickname: '이성공' },
    { id: 'user3', nickname: '박네트워크' },
    { id: 'user4', nickname: '최리더' },
    { id: 'user5', nickname: '정전문가' },
  ];

  const sampleMessages = [
    '안녕하세요! 새로 가입했습니다.',
    '네트워크 마케팅 관련 질문이 있습니다.',
    '이번 달 실적이 정말 좋네요!',
    '새로운 제품 출시 소식 들으셨나요?',
    '팀 미팅 시간 조율 가능하신가요?',
    '마케팅 전략 공유해주세요.',
    '성공 사례를 들려주시겠어요?',
    '온라인 세미나 참석하실 분?',
    '멘토링 프로그램에 관심 있으시면 연락주세요.',
    '다음 주 워크샵 일정 확인해주세요.',
  ];

  for (let i = 0; i < count; i++) {
    const user = users[Math.floor(Math.random() * users.length)];
    const message = sampleMessages[Math.floor(Math.random() * sampleMessages.length)];
    const timestamp = Date.now() - (count - i) * 60000 + Math.random() * 30000; // 1분 간격

    messages.push({
      id: `msg_${i + 1}`,
      userId: user.id,
      userNickname: user.nickname,
      message,
      timestamp,
      roomId,
      type: 'text',
      isRead: Math.random() > 0.3,
    });
  }

  return messages.sort((a, b) => a.timestamp - b.timestamp);
};

// Mock 채팅방 데이터
const mockRooms: ChatRoom[] = [
  {
    id: 'general',
    name: '전체 채팅',
    type: 'general',
    lastMessage: '안녕하세요! 새로 가입했습니다.',
    lastMessageTime: new Date().toISOString(),
    unreadCount: 3,
    participants: ['user1', 'user2', 'user3', 'user4', 'user5'],
    description: '모든 회원이 참여하는 전체 채팅방입니다.'
  },
  {
    id: 'marketing-tips',
    name: '마케팅 팁 공유',
    type: 'group',
    lastMessage: 'SNS 광고 효과가 정말 좋네요',
    lastMessageTime: new Date(Date.now() - 3600000).toISOString(),
    unreadCount: 1,
    participants: ['user1', 'user2', 'user5'],
    description: '마케팅 노하우와 팁을 공유하는 공간입니다.'
  },
  {
    id: 'success-stories',
    name: '성공사례 공유',
    type: 'group',
    lastMessage: '이번 달 목표 달성했습니다!',
    lastMessageTime: new Date(Date.now() - 7200000).toISOString(),
    unreadCount: 0,
    participants: ['user1', 'user3', 'user4'],
    description: '성공 사례를 공유하고 동기부여를 받는 공간입니다.'
  },
];

// 채팅방 목록 조회 훅
export const useChatRooms = () => {
  return useQuery({
    queryKey: CHAT_QUERY_KEYS.rooms(),
    queryFn: async () => {
      // 실제로는 Firebase에서 데이터를 가져옴
      await new Promise(resolve => setTimeout(resolve, 500)); // 로딩 시뮬레이션
      return mockRooms;
    },
    staleTime: 2 * 60 * 1000, // 2분
    gcTime: 10 * 60 * 1000, // 10분
  });
};

// 무한 스크롤 메시지 조회 훅
export const useInfiniteMessages = (roomId: string, enabled: boolean = true) => {
  const [allMessages] = useState(() => generateMockMessages(roomId, 2000)); // 2000개 메시지

  return useInfiniteQuery({
    queryKey: CHAT_QUERY_KEYS.messages(roomId),
    queryFn: async ({ pageParam = allMessages.length }) => {
      // 실제로는 Firebase에서 이전 메시지를 가져옴
      await new Promise(resolve => setTimeout(resolve, 300)); // 로딩 시뮬레이션
      
      const limit = 50;
      const startIndex = Math.max(0, pageParam - limit);
      const endIndex = pageParam;
      
      const messages = allMessages.slice(startIndex, endIndex).reverse(); // 최신순
      
      return {
        messages,
        nextCursor: startIndex > 0 ? startIndex : undefined,
        hasMore: startIndex > 0,
      };
    },
    getNextPageParam: (lastPage) => lastPage.nextCursor,
    initialPageParam: allMessages.length,
    enabled,
    staleTime: 30 * 1000, // 30초
    gcTime: 5 * 60 * 1000, // 5분
  });
};

// 실시간 메시지 구독 훅
export const useRealtimeMessages = (roomId: string, enabled: boolean = true) => {
  const queryClient = useQueryClient();
  const [isConnected, setIsConnected] = useState(false);
  const listenerRef = useRef<any>(null);

  const subscribeToMessages = useCallback(() => {
    if (!enabled || !roomId) return;

    // 실제로는 Firebase 리스너 설정
    // const messagesRef = ref(database, `chats/${roomId}/messages`);
    // const messagesQuery = query(messagesRef, orderByChild('timestamp'), limitToLast(1));
    
    setIsConnected(true);
    
    // Mock 실시간 메시지 시뮬레이션
    const mockRealtimeListener = setInterval(() => {
      if (Math.random() > 0.7) { // 30% 확률로 새 메시지
        const newMessage: ChatMessage = {
          id: `realtime_${Date.now()}`,
          userId: 'user2',
          userNickname: '실시간유저',
          message: `실시간 메시지 ${new Date().toLocaleTimeString()}`,
          timestamp: Date.now(),
          roomId,
          type: 'text',
          isRead: false,
        };

        // 기존 캐시에 새 메시지 추가
        queryClient.setQueryData(
          CHAT_QUERY_KEYS.messages(roomId),
          (oldData: any) => {
            if (!oldData) return oldData;
            
            const newPages = [...oldData.pages];
            if (newPages[0]) {
              newPages[0] = {
                ...newPages[0],
                messages: [newMessage, ...newPages[0].messages],
              };
            }
            
            return {
              ...oldData,
              pages: newPages,
            };
          }
        );

        // 채팅방 목록의 마지막 메시지 업데이트
        queryClient.setQueryData(
          CHAT_QUERY_KEYS.rooms(),
          (oldRooms: ChatRoom[] | undefined) => {
            if (!oldRooms) return oldRooms;
            
            return oldRooms.map(room => 
              room.id === roomId 
                ? {
                    ...room,
                    lastMessage: newMessage.message,
                    lastMessageTime: new Date(newMessage.timestamp).toISOString(),
                    unreadCount: room.unreadCount + 1,
                  }
                : room
            );
          }
        );
      }
    }, 10000); // 10초마다 체크

    listenerRef.current = mockRealtimeListener;

    return () => {
      clearInterval(mockRealtimeListener);
      setIsConnected(false);
    };
  }, [enabled, roomId, queryClient]);

  useEffect(() => {
    const cleanup = subscribeToMessages();
    return cleanup;
  }, [subscribeToMessages]);

  return { isConnected };
};

// 메시지 전송 뮤테이션
export const useSendMessage = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: {
      roomId: string;
      message: string;
      userId: string;
      userNickname: string;
      type?: 'text' | 'image' | 'file';
      replyTo?: string;
    }) => {
      // 실제로는 Firebase에 메시지 저장
      await new Promise(resolve => setTimeout(resolve, 200)); // 전송 시뮬레이션

      const newMessage: ChatMessage = {
        id: `msg_${Date.now()}_${Math.random()}`,
        userId: data.userId,
        userNickname: data.userNickname,
        message: data.message,
        timestamp: Date.now(),
        roomId: data.roomId,
        type: data.type || 'text',
        isRead: false,
        replyTo: data.replyTo,
      };

      return newMessage;
    },
    onMutate: async (data) => {
      // 낙관적 업데이트
      await queryClient.cancelQueries({
        queryKey: CHAT_QUERY_KEYS.messages(data.roomId),
      });

      const tempMessage: ChatMessage = {
        id: `temp_${Date.now()}`,
        userId: data.userId,
        userNickname: data.userNickname,
        message: data.message,
        timestamp: Date.now(),
        roomId: data.roomId,
        type: data.type || 'text',
        isRead: false,
        replyTo: data.replyTo,
      };

      // 임시 메시지를 캐시에 추가
      queryClient.setQueryData(
        CHAT_QUERY_KEYS.messages(data.roomId),
        (oldData: any) => {
          if (!oldData) return oldData;
          
          const newPages = [...oldData.pages];
          if (newPages[0]) {
            newPages[0] = {
              ...newPages[0],
              messages: [tempMessage, ...newPages[0].messages],
            };
          }
          
          return {
            ...oldData,
            pages: newPages,
          };
        }
      );

      return { tempMessage };
    },
    onSuccess: (newMessage, variables, context) => {
      // 실제 메시지로 교체
      queryClient.setQueryData(
        CHAT_QUERY_KEYS.messages(variables.roomId),
        (oldData: any) => {
          if (!oldData) return oldData;
          
          const newPages = [...oldData.pages];
          if (newPages[0]) {
            newPages[0] = {
              ...newPages[0],
              messages: newPages[0].messages.map((msg: ChatMessage) =>
                msg.id === context?.tempMessage.id ? newMessage : msg
              ),
            };
          }
          
          return {
            ...oldData,
            pages: newPages,
          };
        }
      );

      // 채팅방 목록 업데이트
      queryClient.setQueryData(
        CHAT_QUERY_KEYS.rooms(),
        (oldRooms: ChatRoom[] | undefined) => {
          if (!oldRooms) return oldRooms;
          
          return oldRooms.map(room => 
            room.id === variables.roomId 
              ? {
                  ...room,
                  lastMessage: newMessage.message,
                  lastMessageTime: new Date(newMessage.timestamp).toISOString(),
                }
              : room
          );
        }
      );
    },
    onError: (error, variables, context) => {
      // 에러 시 임시 메시지 제거
      queryClient.setQueryData(
        CHAT_QUERY_KEYS.messages(variables.roomId),
        (oldData: any) => {
          if (!oldData || !context?.tempMessage) return oldData;
          
          const newPages = [...oldData.pages];
          if (newPages[0]) {
            newPages[0] = {
              ...newPages[0],
              messages: newPages[0].messages.filter(
                (msg: ChatMessage) => msg.id !== context.tempMessage.id
              ),
            };
          }
          
          return {
            ...oldData,
            pages: newPages,
          };
        }
      );
      
      console.error('메시지 전송 실패:', error);
    },
  });
};

// 메시지 읽음 처리 뮤테이션
export const useMarkMessagesAsRead = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: async (data: { roomId: string; userId: string }) => {
      // 실제로는 Firebase에서 읽음 상태 업데이트
      await new Promise(resolve => setTimeout(resolve, 100));
      return data;
    },
    onSuccess: (data) => {
      // 해당 방의 읽지 않은 메시지 수 초기화
      queryClient.setQueryData(
        CHAT_QUERY_KEYS.rooms(),
        (oldRooms: ChatRoom[] | undefined) => {
          if (!oldRooms) return oldRooms;
          
          return oldRooms.map(room => 
            room.id === data.roomId 
              ? { ...room, unreadCount: 0 }
              : room
          );
        }
      );
    },
  });
};