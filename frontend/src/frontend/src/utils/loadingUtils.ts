// 로딩 유틸리티 함수들 - 기존 PHP JavaScript와 호환성을 위해
import { TopMarketingLoading } from '../context/LoadingContext';

// 페이지 이동 시 로딩 표시
export const showPageLoading = (message: string = '페이지를 이동하는 중...') => {
  TopMarketingLoading.show(message);
};

// 폼 제출 시 로딩 표시
export const showFormLoading = (message: string = '처리 중...') => {
  TopMarketingLoading.show(message);
};

// 로딩 숨김
export const hideLoading = () => {
  TopMarketingLoading.hide();
};

// 진행률 설정
export const setProgress = (percent: number) => {
  TopMarketingLoading.setProgress(percent);
};

// 메시지 설정
export const setMessage = (message: string) => {
  TopMarketingLoading.setMessage(message);
};

// 단계별 로딩
export const setStage = (stage: string) => {
  TopMarketingLoading.setStage(stage);
};

// 커스텀 로딩 (단계별)
export const customLoading = (options: {
  stages?: string[];
  duration?: number;
  autoHide?: boolean;
} = {}) => {
  TopMarketingLoading.custom(options);
};

// React Router Link 클릭 시 자동 로딩 표시
export const setupLinkLoading = () => {
  // React Router는 SPA이므로 실제 페이지 이동이 아님
  // 필요시 React Router의 useNavigate와 함께 사용
};

// 전역 fetch 인터셉터 (기존 PHP JavaScript와 동일)
export const setupFetchInterceptor = () => {
  if (typeof window !== 'undefined' && window.fetch) {
    const originalFetch = window.fetch;
    let activeRequests = 0;
    
    window.fetch = function(...args) {
      activeRequests++;
      if (activeRequests === 1) {
        TopMarketingLoading.show('서버와 연결 중...');
      }
      
      return originalFetch.apply(this, args)
        .finally(() => {
          activeRequests--;
          if (activeRequests === 0) {
            setTimeout(() => {
              TopMarketingLoading.hide();
            }, 300);
          }
        });
    };
  }
};

// 전역 이벤트 리스너 설정 (기존 PHP JavaScript와 동일)
export const setupGlobalLoadingEvents = () => {
  if (typeof window === 'undefined') return;

  // 폼 제출 시 로딩 표시
  document.addEventListener('submit', (e) => {
    if (!e.defaultPrevented) {
      TopMarketingLoading.show('처리 중...');
    }
  });

  // 페이지 언로드 시 로딩 표시
  window.addEventListener('beforeunload', () => {
    TopMarketingLoading.show('페이지를 이동하는 중...');
  });

  // 페이지 히스토리 변경 감지
  window.addEventListener('pageshow', (e) => {
    if (e.persisted) {
      TopMarketingLoading.hide();
    }
  });
};

// 초기화 함수
export const initializeLoading = () => {
  setupFetchInterceptor();
  setupGlobalLoadingEvents();
};

// 기존 PHP JavaScript API와 호환을 위한 전역 객체
if (typeof window !== 'undefined') {
  (window as any).TopMarketingLoading = TopMarketingLoading;
}