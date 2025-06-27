import { useEffect } from 'react';
import { useLocation } from 'react-router-dom';

// 페이지 이동 시 스크롤을 최상단으로 이동시키는 컴포넌트
const ScrollToTop: React.FC = () => {
  const { pathname } = useLocation();

  useEffect(() => {
    // 페이지가 변경될 때마다 스크롤을 최상단으로 이동
    // 다양한 방법으로 스크롤 리셋을 시도
    const scrollToTop = () => {
      // 1. window.scrollTo 사용
      window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
      
      // 2. document.documentElement.scrollTop 직접 설정
      document.documentElement.scrollTop = 0;
      document.body.scrollTop = 0;
      
      // 3. 혹시 모를 지연을 위해 setTimeout 추가
      setTimeout(() => {
        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        document.documentElement.scrollTop = 0;
        document.body.scrollTop = 0;
      }, 0);
    };

    scrollToTop();
  }, [pathname]);

  return null;
};

export default ScrollToTop;