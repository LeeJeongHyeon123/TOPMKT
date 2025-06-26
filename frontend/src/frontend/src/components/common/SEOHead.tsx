import React from 'react';
import { Helmet } from 'react-helmet-async';

interface SEOHeadProps {
  title: string;
  description: string;
  keywords: string;
  ogType: 'website' | 'article' | 'profile';
  ogTitle: string;
  ogDescription: string;
  ogImage: string;
  ogUrl: string;
  structuredData?: object;
}

const SEOHead: React.FC<SEOHeadProps> = ({
  title,
  description,
  keywords,
  ogType,
  ogTitle,
  ogDescription,
  ogImage,
  ogUrl,
  structuredData
}) => {
  return (
    <Helmet>
      {/* Basic Meta Tags */}
      <title>{title}</title>
      <meta name="description" content={description} />
      <meta name="keywords" content={keywords} />
      <meta name="author" content="(주)윈카드" />
      <meta name="robots" content="index, follow" />
      <meta name="googlebot" content="index, follow" />
      <meta name="theme-color" content="#6366f1" />
      <meta name="msapplication-navbutton-color" content="#6366f1" />
      <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
      <meta name="apple-mobile-web-app-capable" content="yes" />
      <meta name="mobile-web-app-capable" content="yes" />
      
      {/* Canonical URL */}
      <link rel="canonical" href={ogUrl} />
      
      {/* Open Graph / Facebook */}
      <meta property="og:type" content={ogType} />
      <meta property="og:url" content={ogUrl} />
      <meta property="og:title" content={ogTitle} />
      <meta property="og:description" content={ogDescription} />
      <meta property="og:image" content={ogImage} />
      <meta property="og:image:width" content="1200" />
      <meta property="og:image:height" content="630" />
      <meta property="og:site_name" content="탑마케팅" />
      <meta property="og:locale" content="ko_KR" />
      
      {/* Twitter Card */}
      <meta property="twitter:card" content="summary_large_image" />
      <meta property="twitter:url" content={ogUrl} />
      <meta property="twitter:title" content={ogTitle} />
      <meta property="twitter:description" content={ogDescription} />
      <meta property="twitter:image" content={ogImage} />
      
      {/* Structured Data */}
      {structuredData && (
        <script type="application/ld+json">
          {JSON.stringify(structuredData)}
        </script>
      )}
      
      {/* Default Structured Data for Website */}
      <script type="application/ld+json">
        {JSON.stringify({
          "@context": "https://schema.org",
          "@type": "WebSite",
          "name": "탑마케팅",
          "alternateName": "TopMKT",
          "url": "https://www.topmktx.com",
          "description": "글로벌 네트워크 마케팅 리더들의 커뮤니티 플랫폼",
          "publisher": {
            "@type": "Organization",
            "name": "(주)윈카드",
            "logo": {
              "@type": "ImageObject",
              "url": "https://www.topmktx.com/assets/images/logo.png"
            }
          },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "https://www.topmktx.com/community?search={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        })}
      </script>
    </Helmet>
  );
};

export default SEOHead;