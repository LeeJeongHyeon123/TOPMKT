import { useEffect, useRef } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { useLoading } from '../context/LoadingContext';

// React Router 이동 시 로딩 표시 훅
export const useRouterLoading = () => {
  const location = useLocation();
  const navigate = useNavigate();
  const { showLoading, hideLoading } = useLoading();
  const previousPath = useRef<string>('');

  useEffect(() => {
    // 첫 로드가 아니고 실제로 경로가 변경된 경우에만 로딩 표시
    if (previousPath.current !== '' && previousPath.current !== location.pathname) {
      showLoading('페이지를 불러오는 중...');
      
      // 페이지 로드 완료 후 숨김
      const timer = setTimeout(() => {
        hideLoading();
      }, 300);

      previousPath.current = location.pathname;

      return () => {
        clearTimeout(timer);
        hideLoading();
      };
    } else {
      previousPath.current = location.pathname;
    }
  }, [location.pathname]);

  // 네비게이션 함수에 로딩 추가
  const navigateWithLoading = (to: string, options?: any) => {
    showLoading('페이지를 이동하는 중...');
    navigate(to, options);
  };

  return {
    navigateWithLoading
  };
};

export default useRouterLoading;