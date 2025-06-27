import React, { createContext, useContext, useState, useCallback, ReactNode } from 'react';
import LoadingSpinner from '../components/common/LoadingSpinner';

interface LoadingContextType {
  isLoading: boolean;
  message: string | undefined;
  activeRequests: number;
  showLoading: (message?: string) => void;
  hideLoading: () => void;
  setProgress: (percent: number) => void;
  setMessage: (message: string) => void;
  startRequest: () => void;
  endRequest: () => void;
}

const LoadingContext = createContext<LoadingContextType | undefined>(undefined);

interface LoadingProviderProps {
  children: ReactNode;
}

export const LoadingProvider: React.FC<LoadingProviderProps> = ({ children }) => {
  const [isLoading, setIsLoading] = useState(false);
  const [activeRequests, setActiveRequests] = useState(0);
  const [currentMessage, setCurrentMessage] = useState<string | undefined>();
  const [minLoadingTime] = useState(300); // 최소 로딩 시간
  const [loadingStartTime, setLoadingStartTime] = useState<number | null>(null);

  const showLoading = useCallback((message?: string) => {
    setIsLoading(true);
    setLoadingStartTime(Date.now());
    setCurrentMessage(message);
    
    // body overflow 숨김
    document.body.style.overflow = 'hidden';
  }, []);

  const hideLoading = useCallback(() => {
    const elapsedTime = loadingStartTime ? Date.now() - loadingStartTime : 0;
    const remainingTime = Math.max(0, minLoadingTime - elapsedTime);

    setTimeout(() => {
      setIsLoading(false);
      setCurrentMessage(undefined);
      setLoadingStartTime(null);
      
      // body overflow 복원
      document.body.style.overflow = '';
    }, remainingTime);
  }, [loadingStartTime, minLoadingTime]);

  const setProgress = useCallback((_percent: number) => {
    // Progress는 LoadingSpinner 컴포넌트 내부에서 자체적으로 처리
    // 필요시 상태를 추가할 수 있음
  }, []);

  const setMessage = useCallback((message: string) => {
    setCurrentMessage(message);
  }, []);

  const startRequest = useCallback(() => {
    setActiveRequests(prev => {
      const newCount = prev + 1;
      if (newCount === 1) {
        showLoading('서버와 연결 중...');
      }
      return newCount;
    });
  }, [showLoading]);

  const endRequest = useCallback(() => {
    setActiveRequests(prev => {
      const newCount = Math.max(0, prev - 1);
      if (newCount === 0) {
        setTimeout(hideLoading, 300);
      }
      return newCount;
    });
  }, [hideLoading]);

  const value: LoadingContextType = {
    isLoading,
    message: currentMessage,
    activeRequests,
    showLoading,
    hideLoading,
    setProgress,
    setMessage,
    startRequest,
    endRequest
  };

  return (
    <LoadingContext.Provider value={value}>
      {children}
      {isLoading && (
        <LoadingSpinner
          fullScreen={true}
          message={currentMessage}
        />
      )}
    </LoadingContext.Provider>
  );
};

export const useLoading = (): LoadingContextType => {
  const context = useContext(LoadingContext);
  if (context === undefined) {
    throw new Error('useLoading must be used within a LoadingProvider');
  }
  return context;
};

// 전역 로딩 헬퍼 함수들 (기존 PHP JavaScript와 동일한 API)
export const TopMarketingLoading = {
  show: (message?: string) => {
    // React Context를 사용할 수 없는 경우를 위한 대체 방법
    window.dispatchEvent(new CustomEvent('showLoading', { detail: { message } }));
  },
  hide: () => {
    window.dispatchEvent(new CustomEvent('hideLoading'));
  },
  setProgress: (percent: number) => {
    window.dispatchEvent(new CustomEvent('setProgress', { detail: { percent } }));
  },
  setMessage: (message: string) => {
    window.dispatchEvent(new CustomEvent('setMessage', { detail: { message } }));
  },
  setStage: (stage: string) => {
    window.dispatchEvent(new CustomEvent('setMessage', { detail: { message: stage } }));
  },
  custom: (options: {
    stages?: string[];
    duration?: number;
    autoHide?: boolean;
  } = {}) => {
    window.dispatchEvent(new CustomEvent('customLoading', { detail: options }));
  }
};

// 전역 이벤트 리스너를 위한 LoadingEventHandler 컴포넌트
export const LoadingEventHandler: React.FC = () => {
  const loading = useLoading();

  React.useEffect(() => {
    const handleShowLoading = (event: CustomEvent) => {
      loading.showLoading(event.detail?.message);
    };

    const handleHideLoading = () => {
      loading.hideLoading();
    };

    const handleSetProgress = (event: CustomEvent) => {
      loading.setProgress(event.detail?.percent || 0);
    };

    const handleSetMessage = (event: CustomEvent) => {
      loading.setMessage(event.detail?.message || '');
    };

    const handleCustomLoading = (event: CustomEvent) => {
      const { stages = ['처리 중...'], duration = 3000, autoHide = true } = event.detail || {};
      
      loading.showLoading();
      
      if (stages.length > 0) {
        const stageInterval = duration / stages.length;
        let currentStageIndex = 0;
        
        // 첫 번째 스테이지 표시
        loading.setMessage(stages[0]);
        
        const stageTimer = setInterval(() => {
          currentStageIndex++;
          if (currentStageIndex < stages.length) {
            loading.setMessage(stages[currentStageIndex]);
          } else {
            clearInterval(stageTimer);
            if (autoHide) {
              setTimeout(() => {
                loading.hideLoading();
              }, 500);
            }
          }
        }, stageInterval);
      }
    };

    window.addEventListener('showLoading', handleShowLoading as EventListener);
    window.addEventListener('hideLoading', handleHideLoading as EventListener);
    window.addEventListener('setProgress', handleSetProgress as EventListener);
    window.addEventListener('setMessage', handleSetMessage as EventListener);
    window.addEventListener('customLoading', handleCustomLoading as EventListener);

    return () => {
      window.removeEventListener('showLoading', handleShowLoading as EventListener);
      window.removeEventListener('hideLoading', handleHideLoading as EventListener);
      window.removeEventListener('setProgress', handleSetProgress as EventListener);
      window.removeEventListener('setMessage', handleSetMessage as EventListener);
      window.removeEventListener('customLoading', handleCustomLoading as EventListener);
    };
  }, [loading]);

  return null;
};