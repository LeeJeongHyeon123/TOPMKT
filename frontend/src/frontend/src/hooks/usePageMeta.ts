// Unused imports commented out
// import { useEffect } from 'react';
// import { Helmet } from 'react-helmet-async';

interface PageMetaOptions {
  title?: string;
  description?: string;
  keywords?: string;
  ogType?: 'website' | 'article' | 'profile';
  ogTitle?: string;
  ogDescription?: string;
  ogImage?: string;
  structuredData?: object;
}

export const usePageMeta = (options: PageMetaOptions) => {
  const {
    title,
    description,
    keywords,
    ogType = 'website',
    ogTitle,
    ogDescription,
    ogImage,
    structuredData
  } = options;

  const siteName = '탑마케팅';
  const defaultDescription = '마케팅 전문가들이 모여 지식을 공유하고 함께 성장하는 플랫폼입니다. 세미나, 워크샵, 커뮤니티를 통해 최신 마케팅 트렌드를 만나보세요.';
  const defaultImage = `${window.location.origin}/assets/images/topmkt-og-image.png?v=${new Date().toISOString().slice(0, 10).replace(/-/g, '')}`;
  
  const finalTitle = title ? `${title} - ${siteName}` : `${siteName} - 마케팅 전문가들의 지식 공유 플랫폼`;
  const finalDescription = description || defaultDescription;
  const finalOgTitle = ogTitle || finalTitle;
  const finalOgDescription = ogDescription || finalDescription;
  const finalOgImage = ogImage || defaultImage;
  const currentUrl = window.location.href;

  return {
    title: finalTitle,
    description: finalDescription,
    keywords: keywords || '마케팅, 네트워크 마케팅, 세미나, 워크샵, 커뮤니티, 마케팅 교육, 온라인 강의, 탑마케팅, TopMKT, 비즈니스 매칭, 마케팅 플랫폼',
    ogType,
    ogTitle: finalOgTitle,
    ogDescription: finalOgDescription,
    ogImage: finalOgImage,
    ogUrl: currentUrl,
    structuredData
  };
};