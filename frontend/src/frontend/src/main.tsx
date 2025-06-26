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
import { HelmetProvider } from 'react-helmet-async'
import App from './App'
import './index.css'
import './styles/logo.css'
import './styles/main.css'

// Providers
import { AuthProvider } from '@/context/AuthContext'
import { LoadingProvider } from '@/context/LoadingContext'
import { ToastProvider } from '@/context/ToastContext'

ReactDOM.createRoot(document.getElementById('root')!).render(
  <React.StrictMode>
    <HelmetProvider>
      <BrowserRouter 
        basename="/frontend"
        future={{
          v7_startTransition: true,
          v7_relativeSplatPath: true
        }}
      >
        <LoadingProvider>
          <ToastProvider>
            <AuthProvider>
              <App />
            </AuthProvider>
          </ToastProvider>
        </LoadingProvider>
      </BrowserRouter>
    </HelmetProvider>
  </React.StrictMode>,
)