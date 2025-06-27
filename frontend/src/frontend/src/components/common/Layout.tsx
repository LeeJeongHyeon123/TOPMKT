import { ReactNode } from 'react'
import { Outlet } from 'react-router-dom'
import Header from './Header'
import Footer from './Footer'
import LoadingSpinner from './LoadingSpinner'
import ToastContainer from './ToastContainer'
import { useLoading } from '@/context/LoadingContext'
import { useRouterLoading } from '@/hooks/useRouterLoading'

interface LayoutProps {
  children?: ReactNode
}

const Layout: React.FC<LayoutProps> = ({ children }) => {
  const { isLoading, message } = useLoading();
  
  // 라우터 로딩 초기화
  useRouterLoading();

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      <Header />
      
      <main className="flex-grow">
        {children || <Outlet />}
      </main>
      
      <Footer />
      
      {/* 전역 로딩 스피너 */}
      {isLoading && (
        <LoadingSpinner 
          message={message} 
          fullScreen 
        />
      )}
      
      {/* 토스트 알림 */}
      <ToastContainer />
    </div>
  );
};

export default Layout;