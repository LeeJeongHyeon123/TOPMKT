import React, { useEffect } from 'react';

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
  useEffect(() => {
    // Set document title
    document.title = title;

    // Helper function to set or update meta tag
    const setMetaTag = (name: string, content: string, isProperty = false) => {
      const attribute = isProperty ? 'property' : 'name';
      let meta = document.querySelector(`meta[${attribute}="${name}"]`) as HTMLMetaElement;
      if (!meta) {
        meta = document.createElement('meta');
        meta.setAttribute(attribute, name);
        document.head.appendChild(meta);
      }
      meta.content = content;
    };

    // Helper function to set or update link tag
    const setLinkTag = (rel: string, href: string) => {
      let link = document.querySelector(`link[rel="${rel}"]`) as HTMLLinkElement;
      if (!link) {
        link = document.createElement('link');
        link.rel = rel;
        document.head.appendChild(link);
      }
      link.href = href;
    };

    // Helper function to set or update script tag
    const setScriptTag = (id: string, content: object) => {
      let script = document.querySelector(`script[data-id="${id}"]`) as HTMLScriptElement;
      if (!script) {
        script = document.createElement('script');
        script.type = 'application/ld+json';
        script.setAttribute('data-id', id);
        document.head.appendChild(script);
      }
      script.textContent = JSON.stringify(content);
    };

    // Basic Meta Tags
    setMetaTag('description', description);
    setMetaTag('keywords', keywords);
    setMetaTag('author', '(주)윈카드');
    setMetaTag('robots', 'index, follow');
    setMetaTag('googlebot', 'index, follow');
    setMetaTag('theme-color', '#6366f1');
    setMetaTag('msapplication-navbutton-color', '#6366f1');
    setMetaTag('apple-mobile-web-app-status-bar-style', 'black-translucent');
    setMetaTag('apple-mobile-web-app-capable', 'yes');
    setMetaTag('mobile-web-app-capable', 'yes');

    // Canonical URL
    setLinkTag('canonical', ogUrl);

    // Open Graph / Facebook
    setMetaTag('og:type', ogType, true);
    setMetaTag('og:url', ogUrl, true);
    setMetaTag('og:title', ogTitle, true);
    setMetaTag('og:description', ogDescription, true);
    setMetaTag('og:image', ogImage, true);
    setMetaTag('og:image:width', '1200', true);
    setMetaTag('og:image:height', '630', true);
    setMetaTag('og:site_name', '탑마케팅', true);
    setMetaTag('og:locale', 'ko_KR', true);

    // Twitter Card
    setMetaTag('twitter:card', 'summary_large_image', true);
    setMetaTag('twitter:url', ogUrl, true);
    setMetaTag('twitter:title', ogTitle, true);
    setMetaTag('twitter:description', ogDescription, true);
    setMetaTag('twitter:image', ogImage, true);

    // Structured Data
    if (structuredData) {
      setScriptTag('custom-structured-data', structuredData);
    }

    // Default Structured Data for Website
    setScriptTag('default-structured-data', {
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
    });
  }, [title, description, keywords, ogType, ogTitle, ogDescription, ogImage, ogUrl, structuredData]);

  return null; // This component only manipulates the document head
};

export default SEOHead;