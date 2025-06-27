import React from 'react'
import ReactDOM from 'react-dom/client'

// React DevTools 메시지 숨기기 (프로덕션 환경에서)
if (process.env.NODE_ENV === 'production') {
  const originalConsoleLog = console.log;
  console.log = (...args) => {
    if (typeof args[0] === 'string' && args[0].includes('React DevTools')) {
      return;
    }
    originalConsoleLog.apply(console, args);
  };
}
import { BrowserRouter } from 'react-router-dom'
// React Helmet removed - using direct DOM manipulation for SEO
import App from './App'
import './index.css'
import './styles/logo.css'
import './styles/main.css'

// Providers
import { AuthProvider } from '@/context/AuthContext'
import { LoadingProvider } from '@/context/LoadingContext'
import { ToastProvider } from '@/context/ToastContext'

// 로딩 시스템 초기화
import { initializeLoading } from '@/utils/loadingUtils'

// 로딩 시스템 초기화 실행
initializeLoading();

// 스크롤 복원을 수동으로 제어
if ('scrollRestoration' in history) {
  history.scrollRestoration = 'manual';
}

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <BrowserRouter 
      basename="/frontend"
    >
      <LoadingProvider>
        <ToastProvider>
          <AuthProvider>
            <App />
          </AuthProvider>
        </ToastProvider>
      </LoadingProvider>
    </BrowserRouter>
  </React.StrictMode>,
)