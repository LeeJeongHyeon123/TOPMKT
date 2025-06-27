import React, { useState, useEffect } from 'react';
import { cn } from '../../utils/cn';

interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg' | 'xl';
  message?: string;
  overlay?: boolean;
  className?: string;
  fullScreen?: boolean; // 기존 탑마케팅 스타일 로딩
}

const LoadingSpinner: React.FC<LoadingSpinnerProps> = ({
  size = 'md',
  message,
  overlay = false,
  className,
  fullScreen = false
}) => {
  const [currentMessage, setCurrentMessage] = useState(0);
  const [progress, setProgress] = useState(20);

  const loadingMessages = [
    '데이터를 불러오는 중...',
    '서버와 연결 중...',
    '콘텐츠 준비 중...',
    '거의 완료되었습니다...'
  ];

  useEffect(() => {
    if (!fullScreen) return;

    // 메시지 순환
    const messageInterval = setInterval(() => {
      setCurrentMessage((prev) => (prev + 1) % loadingMessages.length);
    }, 1000);

    // 진행률 애니메이션
    const progressInterval = setInterval(() => {
      setProgress((prev) => {
        if (prev < 90) {
          return Math.min(prev + Math.random() * 15, 90);
        }
        return prev;
      });
    }, 300);

    return () => {
      clearInterval(messageInterval);
      clearInterval(progressInterval);
    };
  }, [fullScreen]);

  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-8 h-8',
    lg: 'w-12 h-12',
    xl: 'w-16 h-16'
  };

  // 🚀 기존 탑마케팅 로딩 UI (fullScreen = true일 때)
  if (fullScreen) {
    return (
      <>
        <style>
          {`
            /* 🚀 탑마케팅 로딩 UI - 기존과 완전 동일 */
            .loading-overlay {
              position: fixed;
              top: 0;
              left: 0;
              width: 100vw;
              height: 100vh;
              background: rgba(15, 23, 42, 0.6);
              display: flex;
              flex-direction: column;
              justify-content: center;
              align-items: center;
              z-index: 10000;
              opacity: 1;
              transition: opacity 0.3s ease-out;
              backdrop-filter: blur(5px);
            }
            
            .loading-overlay.hide {
              opacity: 0;
              pointer-events: none;
            }
            
            .loading-container {
              text-align: center;
              position: relative;
              z-index: 2;
              max-width: 400px;
              width: 90%;
            }
            
            .loading-icon {
              position: relative;
              margin-bottom: 30px;
              height: 80px;
              display: flex;
              align-items: center;
              justify-content: center;
            }
            
            .rocket-main {
              font-size: 3rem;
              color: #3b82f6;
              display: inline-block;
              animation: gentleFloat 2s ease-in-out infinite;
              filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.3));
              position: relative;
              z-index: 2;
            }
            
            .loading-spinner {
              position: absolute;
              width: 100px;
              height: 100px;
              border: 3px solid rgba(59, 130, 246, 0.1);
              border-top: 3px solid #3b82f6;
              border-radius: 50%;
              animation: spin 1s linear infinite;
            }
            
            @keyframes gentleFloat {
              0%, 100% {
                transform: translateY(0px);
              }
              50% {
                transform: translateY(-10px);
              }
            }
            
            @keyframes spin {
              0% {
                transform: rotate(0deg);
              }
              100% {
                transform: rotate(360deg);
              }
            }
            
            .loading-stage {
              color: #ffffff;
              font-size: 1rem;
              font-weight: 500;
              margin-bottom: 20px;
              opacity: 1;
              transition: opacity 0.3s ease;
              text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
            }
            
            .progress-container {
              width: 100%;
              max-width: 200px;
              height: 4px;
              background: rgba(255, 255, 255, 0.1);
              border-radius: 2px;
              overflow: hidden;
              margin: 0 auto;
              position: relative;
            }
            
            .progress-bar {
              height: 100%;
              background: #3b82f6;
              border-radius: 2px;
              width: 0%;
              transition: width 0.5s ease;
              position: relative;
            }
            
            @media (max-width: 768px) {
              .loading-container {
                max-width: 90%;
              }
              
              .rocket-main {
                font-size: 2.5rem;
              }
              
              .loading-spinner {
                width: 80px;
                height: 80px;
              }
              
              .loading-stage {
                font-size: 0.9rem;
              }
            }
          `}
        </style>
        <div className="loading-overlay">
          <div className="loading-container">
            <div className="loading-icon">
              <div className="rocket-main">🚀</div>
              <div className="loading-spinner"></div>
            </div>
            
            <div className="loading-stage">
              {message || loadingMessages[currentMessage]}
            </div>
            
            <div className="progress-container">
              <div 
                className="progress-bar" 
                style={{ width: `${progress}%` }}
              ></div>
            </div>
          </div>
        </div>
      </>
    );
  }

  const spinnerElement = (
    <div className={cn('flex flex-col items-center justify-center', className)}>
      <div className="relative">
        <div
          className={cn(
            'animate-spin rounded-full border-4 border-gray-200 border-t-blue-600',
            sizes[size]
          )}
        />
      </div>
      {message && (
        <p className="mt-3 text-sm text-gray-600 text-center max-w-xs">
          {message}
        </p>
      )}
    </div>
  );

  if (overlay) {
    return (
      <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div className="bg-white rounded-lg p-8 max-w-sm w-full mx-4">
          {spinnerElement}
        </div>
      </div>
    );
  }

  return spinnerElement;
};

export default LoadingSpinner;