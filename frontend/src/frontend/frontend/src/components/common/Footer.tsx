import React from 'react';
import { Link } from 'react-router-dom';

const Footer: React.FC = () => {
  const currentYear = new Date().getFullYear();

  const footerLinks = {
    company: [
      { name: '회사소개', href: '/about' },
      { name: '채용정보', href: '/careers' },
      { name: '언론보도', href: '/press' },
      { name: '파트너십', href: '/partnership' },
    ],
    service: [
      { name: '강의 등록', href: '/lectures/create' },
      { name: '이벤트 등록', href: '/events/create' },
      { name: '기업회원', href: '/corp/info' },
      { name: 'API', href: '/developers' },
    ],
    support: [
      { name: '고객센터', href: '/support' },
      { name: 'FAQ', href: '/faq' },
      { name: '이용가이드', href: '/guide' },
      { name: '문의하기', href: '/contact' },
    ],
    legal: [
      { name: '이용약관', href: '/legal/terms' },
      { name: '개인정보처리방침', href: '/legal/privacy' },
      { name: '쿠키정책', href: '/legal/cookies' },
      { name: '청소년보호정책', href: '/legal/youth' },
    ],
  };

  const socialLinks = [
    {
      name: 'Facebook',
      href: 'https://facebook.com/topmkt',
      icon: (
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
        </svg>
      ),
    },
    {
      name: 'Instagram',
      href: 'https://instagram.com/topmkt',
      icon: (
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.328-1.297C4.243 14.814 3.752 13.663 3.752 12.366c0-1.297.49-2.448 1.297-3.328.857-.857 2.008-1.297 3.328-1.297s2.448.49 3.328 1.297c.857.857 1.297 2.008 1.297 3.328 0 1.297-.49 2.448-1.297 3.328-.857.857-2.008 1.297-3.328 1.297z"/>
        </svg>
      ),
    },
    {
      name: 'YouTube',
      href: 'https://youtube.com/topmkt',
      icon: (
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
        </svg>
      ),
    },
    {
      name: 'Blog',
      href: 'https://blog.topmktx.com',
      icon: (
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.568 8.16c-.172 1.691-.828 3.362-1.897 4.731-.738.945-1.668 1.74-2.748 2.334-1.08.594-2.31.891-3.636.891-1.311 0-2.541-.297-3.636-.891-1.08-.594-2.01-1.389-2.748-2.334C2.832 11.522 2.176 9.851 2.004 8.16 1.832 6.469 2.268 4.777 3.312 3.312 4.356 1.847 5.904 1.044 7.596 1.044s3.24.803 4.284 2.268c1.044 1.465 1.48 3.157 1.308 4.848z"/>
        </svg>
      ),
    },
  ];

  return (
    <footer className="bg-gray-900 text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
          {/* 회사 정보 */}
          <div className="lg:col-span-1">
            <div className="flex items-center mb-4">
              <img 
                className="h-8 w-auto filter brightness-0 invert" 
                src="/assets/images/topmkt-logo-og.svg" 
                alt="탑마케팅" 
                onError={(e) => {
                  e.currentTarget.style.display = 'none';
                  e.currentTarget.nextElementSibling?.classList.remove('hidden');
                }}
              />
              <span className="hidden text-xl font-bold ml-2">
                탑마케팅
              </span>
            </div>
            <p className="text-gray-400 text-sm mb-4">
              네트워크 마케팅 전문가들을 위한 
              <br />커뮤니티 플랫폼
            </p>
            <div className="flex space-x-4">
              {socialLinks.map((item) => (
                <a
                  key={item.name}
                  href={item.href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-gray-400 hover:text-white transition-colors"
                  aria-label={item.name}
                >
                  {item.icon}
                </a>
              ))}
            </div>
          </div>

          {/* 링크 섹션들 */}
          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">
              회사
            </h3>
            <ul className="space-y-2">
              {footerLinks.company.map((item) => (
                <li key={item.name}>
                  <Link
                    to={item.href}
                    className="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">
              서비스
            </h3>
            <ul className="space-y-2">
              {footerLinks.service.map((item) => (
                <li key={item.name}>
                  <Link
                    to={item.href}
                    className="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">
              지원
            </h3>
            <ul className="space-y-2">
              {footerLinks.support.map((item) => (
                <li key={item.name}>
                  <Link
                    to={item.href}
                    className="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <h3 className="text-sm font-semibold text-white uppercase tracking-wider mb-4">
              법률
            </h3>
            <ul className="space-y-2">
              {footerLinks.legal.map((item) => (
                <li key={item.name}>
                  <Link
                    to={item.href}
                    className="text-gray-400 hover:text-white text-sm transition-colors"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* 하단 정보 */}
        <div className="border-t border-gray-800 mt-8 pt-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="text-gray-400 text-sm">
              <p className="mb-2 md:mb-0">
                © {currentYear} 탑마케팅. All rights reserved.
              </p>
              <p className="text-xs">
                사업자등록번호: 123-45-67890 | 대표: 홍길동 | 주소: 서울특별시 강남구 테헤란로 123
              </p>
            </div>
            <div className="mt-4 md:mt-0">
              <p className="text-gray-400 text-sm">
                고객센터: 1588-0000 | 이메일: support@topmktx.com
              </p>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;