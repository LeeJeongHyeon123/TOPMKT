# 탑마케팅 React 프론트엔드

## 📋 프로젝트 개요

탑마케팅 웹 애플리케이션의 React 기반 프론트엔드입니다. 기존 PHP 뷰를 완전히 대체하여 현대적인 SPA(Single Page Application)로 구현되었습니다.

## 🚀 최신 업데이트 (2025.06.27)

### 주요 라이브러리 업그레이드
- ✅ **React 19.1.0**: 최신 성능 최적화 및 새로운 기능
- ✅ **Node.js 20.19.0**: 최신 JavaScript 런타임 환경
- ✅ **Vite 7.0.0**: 차세대 빌드 도구 (2.5초 빌드)
- ✅ **TypeScript 5.8.3**: 향상된 타입 시스템
- ✅ **TailwindCSS 4.1.11**: 새로운 @theme 구문
- ✅ **React Router 7.6.2**: 최신 라우팅 시스템
- ✅ **ESLint 9.29.0**: Flat Config 형식
- ✅ **보안 취약점 0개**: 모든 의존성 보안 이슈 해결

## 🛠️ 기술 스택

### 핵심 라이브러리
- **React 19.1.0**: UI 라이브러리
- **TypeScript 5.8.3**: 타입 안전성
- **Vite 7.0.0**: 빌드 도구 및 개발 서버
- **React Router 7.6.2**: 라우팅 시스템

### 스타일링
- **TailwindCSS 4.1.11**: 유틸리티 CSS 프레임워크
- **PostCSS 8.5.6**: CSS 후처리기
- **Autoprefixer 10.4.21**: 자동 CSS 접두사

### 개발 도구
- **ESLint 9.29.0**: 코드 품질 관리 (Flat Config)
- **TypeScript ESLint 8.35.0**: TypeScript 린팅
- **Globals 16.2.0**: 전역 변수 정의

### 유틸리티
- **Axios 1.10.0**: HTTP 클라이언트
- **clsx 2.1.1**: 조건부 CSS 클래스
- **tailwind-merge 3.3.1**: TailwindCSS 클래스 병합

## 📁 프로젝트 구조

```
frontend/src/frontend/
├── src/
│   ├── components/        # 재사용 가능한 컴포넌트
│   │   ├── common/       # 공통 컴포넌트
│   │   ├── forms/        # 폼 관련 컴포넌트
│   │   └── layout/       # 레이아웃 컴포넌트
│   ├── pages/            # 페이지 컴포넌트
│   ├── hooks/            # 커스텀 훅
│   ├── context/          # Context API 상태 관리
│   ├── utils/            # 유틸리티 함수
│   ├── types/            # TypeScript 타입 정의
│   ├── config/           # 설정 파일
│   └── styles/           # 스타일 파일
├── public/               # 정적 파일
├── dist/                 # 빌드 결과물
├── package.json          # 의존성 관리
├── tsconfig.json         # TypeScript 설정
├── vite.config.ts        # Vite 설정
├── eslint.config.js      # ESLint 설정 (Flat Config)
├── postcss.config.js     # PostCSS 설정
└── tailwind.config.js.backup  # 백업된 Tailwind 설정
```

## 🚀 시작하기

### 시스템 요구사항
- **Node.js**: 20.19.0 이상
- **npm**: 10.8.2 이상

### 설치 및 실행

```bash
# 의존성 설치
npm install

# 개발 서버 실행 (localhost:3000)
npm run dev

# 프로덕션 빌드
npm run build

# 빌드 결과 미리보기
npm run preview
```

### 개발 명령어

```bash
# 타입 체크
npm run type-check

# 코드 린팅
npm run lint

# 백엔드 통합 빌드
npm run build:integration
```

## 🔧 설정 파일

### ESLint 9 (Flat Config)
```javascript
// eslint.config.js
import js from '@eslint/js';
import typescript from '@typescript-eslint/eslint-plugin';
import globals from 'globals';

export default [
  js.configs.recommended,
  {
    files: ['**/*.{ts,tsx,js,jsx}'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.es2020,
        ...globals.node,
      },
    },
    // ... 규칙 설정
  }
];
```

### TailwindCSS 4.x (@theme 구문)
```css
/* src/index.css */
@import "tailwindcss";

@theme {
  --color-primary-500: #3b82f6;
  --font-family-sans: Inter, system-ui, sans-serif;
  /* ... 커스텀 테마 설정 */
}
```

### Vite 설정
```typescript
// vite.config.ts
export default defineConfig({
  plugins: [react()],
  base: '/frontend/',
  build: {
    outDir: '../../../public/frontend',
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom'],
          router: ['react-router-dom'],
          utils: ['axios', 'clsx', 'tailwind-merge']
        }
      }
    }
  }
});
```

## 🎨 주요 기능

### 컴포넌트 시스템
- **SEOHead**: React 19 호환 SEO 메타 태그 관리
- **Header/Footer**: 공통 레이아웃 컴포넌트
- **ProtectedRoute**: 인증 기반 라우트 보호
- **Input/Button**: 재사용 가능한 폼 컴포넌트

### 상태 관리
- **AuthContext**: 사용자 인증 상태
- **LoadingContext**: 로딩 상태 관리
- **ToastContext**: 알림 메시지 관리

### 커스텀 훅
- **useApi**: API 호출 및 상태 관리
- **usePageMeta**: 페이지 메타데이터 관리
- **useAuth**: 인증 상태 관리

## 🔐 보안 기능

### JWT 인증
- 액세스 토큰 및 리프레시 토큰 관리
- 자동 토큰 갱신
- 보안 토큰 저장 (httpOnly 쿠키)

### 입력 검증
- TypeScript 타입 검증
- 폼 입력 데이터 검증
- XSS 방지 처리

## 📱 반응형 디자인

### 지원 화면 크기
- **모바일**: 320px - 768px
- **태블릿**: 768px - 1024px
- **데스크톱**: 1024px 이상

### TailwindCSS 반응형 클래스
```css
<!-- 모바일 우선 디자인 -->
<div class="w-full md:w-1/2 lg:w-1/3">
  <!-- 모바일: 전체 너비 -->
  <!-- 태블릿: 절반 너비 -->
  <!-- 데스크톱: 1/3 너비 -->
</div>
```

## 🚀 성능 최적화

### 빌드 최적화
- **코드 스플리팅**: vendor, router, utils 청크 분리
- **트리 쉐이킹**: 사용하지 않는 코드 제거
- **압축**: Gzip 압축 적용 (166.87 kB → 13.96 kB CSS)

### 런타임 최적화
- **React 19**: 향상된 렌더링 성능
- **지연 로딩**: 컴포넌트 지연 로딩
- **메모화**: useMemo, useCallback 적절한 사용

## 🧪 개발 환경

### Hot Module Replacement (HMR)
- 코드 변경 시 즉시 반영
- 상태 유지 리로드
- 빠른 개발 경험

### 타입 안전성
- **TypeScript 5.8.3**: 강타입 시스템
- **타입 체크**: 빌드 전 타입 검증
- **인터페이스 정의**: API 응답 타입 정의

## 🔄 API 통합

### 백엔드 연동
- **Axios 1.10.0**: HTTP 클라이언트
- **Base URL**: 프록시 설정을 통한 API 연동
- **에러 처리**: 통합 에러 핸들링

### 인증 시스템
- **JWT 토큰**: 헤더 기반 인증
- **자동 갱신**: 만료 전 토큰 리프레시
- **로그아웃**: 토큰 무효화

## 📊 번들 분석

### 현재 번들 크기 (압축 후)
- **index.js**: 166.87 kB (메인 앱)
- **vendor.js**: 9.36 kB (React, React-DOM)
- **router.js**: 13.11 kB (React Router)
- **utils.js**: 14.15 kB (Axios, 유틸리티)
- **index.css**: 13.96 kB (TailwindCSS)

## 🐛 알려진 이슈

### React Router 7 경고
- Node.js 20+ 요구사항 경고 (정상 동작)
- 차후 Node.js 버전 업그레이드로 해결 예정

## 🔮 향후 계획

### 단기 계획
- [ ] PWA 기능 추가
- [ ] 오프라인 지원
- [ ] 푸시 알림

### 장기 계획
- [ ] React 19 Server Components 적용
- [ ] 마이크로 프론트엔드 아키텍처
- [ ] 성능 모니터링 도구 통합

## 🤝 기여하기

### 코딩 컨벤션
- **ESLint**: 코드 스타일 통일
- **TypeScript**: 타입 안전성 확보
- **Prettier**: 코드 포맷팅 (선택사항)

### 커밋 메시지
```
feat: 새로운 기능 추가
fix: 버그 수정
docs: 문서 업데이트
style: 코드 스타일 변경
refactor: 코드 리팩토링
test: 테스트 추가/수정
chore: 빌드 프로세스 변경
```

## 📞 지원

### 문제 해결
1. **npm install 오류**: Node.js 20.19.0+ 설치 확인
2. **빌드 오류**: 타입 체크 후 빌드 재시도
3. **개발 서버 오류**: 포트 3000 사용 가능 확인

### 연락처
- **이메일**: jh@wincard.kr
- **GitHub**: [topmkt 저장소](https://github.com/LeeJeongHyeon123/topmkt)

---

**마지막 업데이트**: 2025년 6월 27일  
**버전**: 1.1.0 (라이브러리 현대화)