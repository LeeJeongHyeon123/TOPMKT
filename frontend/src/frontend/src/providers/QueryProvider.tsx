import React from 'react';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { ReactQueryDevtools } from '@tanstack/react-query-devtools';

// QueryClient 설정 - 성능 최적화 옵션 포함
const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      // 캐시 시간: 5분
      staleTime: 5 * 60 * 1000,
      // 가비지 컬렉션 시간: 10분
      gcTime: 10 * 60 * 1000,
      // 에러 재시도 설정
      retry: (failureCount, error: any) => {
        // 4xx 에러는 재시도하지 않음
        if (error?.response?.status >= 400 && error?.response?.status < 500) {
          return false;
        }
        // 최대 3회 재시도
        return failureCount < 3;
      },
      // 재시도 지연시간 (지수 백오프)
      retryDelay: (attemptIndex) => Math.min(1000 * 2 ** attemptIndex, 30000),
      // 백그라운드에서 자동 갱신
      refetchOnWindowFocus: true,
      // 네트워크 재연결 시 갱신
      refetchOnReconnect: true,
      // 마운트 시 갱신 비활성화 (캐시된 데이터 우선 사용)
      refetchOnMount: false,
    },
    mutations: {
      // 뮤테이션 에러 재시도 설정
      retry: 1,
      retryDelay: 1000,
    },
  },
});

interface QueryProviderProps {
  children: React.ReactNode;
}

export const QueryProvider: React.FC<QueryProviderProps> = ({ children }) => {
  return (
    <QueryClientProvider client={queryClient}>
      {children}
      {/* 개발 환경에서만 DevTools 표시 */}
      {process.env.NODE_ENV === 'development' && (
        <ReactQueryDevtools 
          initialIsOpen={false}
        />
      )}
    </QueryClientProvider>
  );
};

export { queryClient };