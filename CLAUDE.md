# Claude Code 개발 가이드

## 📋 프로젝트 정보

이 프로젝트는 **탑마케팅 웹 애플리케이션**으로, PHP 백엔드와 React 프론트엔드로 구성된 마케팅 커뮤니티 플랫폼입니다.

## 🚀 최신 업데이트 (2025.06.29)

### 고성능 최적화 완료 🎯
- ✅ **React Query** 도입으로 API 캐싱 시스템 구축
- ✅ **무한 스크롤** 커뮤니티 리스트 성능 최적화
- ✅ **채팅 가상화** 2000+ 메시지 효율적 처리
- ✅ **이미지 최적화** WebP/AVIF 지원 지연 로딩
- ✅ **컴포넌트 메모이제이션** 불필요한 리렌더링 방지
- ✅ **번들 크기 최적화** 스마트 청크 분할 적용

### 주요 라이브러리 현대화 완료
- ✅ **Node.js 20.19.0** 업그레이드 완료
- ✅ **React 19.1.0** 최신 버전 적용
- ✅ **Vite 7.0.0** 차세대 빌드 도구
- ✅ **TypeScript 5.8.3** 향상된 타입 시스템
- ✅ **TailwindCSS 4.1.11** 새로운 @theme 구문
- ✅ **ESLint 9.29.0** Flat Config 형식
- ✅ **보안 취약점 0개** 달성

## 🛠️ 개발 환경

### 빌드 및 테스트 명령어
```bash
# 프론트엔드 개발 서버 (포트 3000)
cd frontend/src/frontend
npm run dev

# 프로덕션 빌드 (2.5초 완료)
npm run build

# 타입 체크
npm run type-check

# 코드 린팅 (ESLint 9)
npm run lint
```

### 시스템 요구사항
- **Node.js**: 20.19.0+ (현재 설치됨)
- **npm**: 10.8.2+
- **PHP**: 8.0.30+
- **MariaDB**: 10.6.5+

## 📁 프로젝트 구조

```
topmkt/
├── frontend/src/frontend/    # React 프론트엔드
│   ├── src/                 # React 소스 코드
│   ├── dist/               # 빌드 결과물
│   ├── package.json        # 의존성 (v1.1.0)
│   ├── vite.config.ts      # Vite 설정
│   ├── eslint.config.js    # ESLint 9 Flat Config
│   └── tsconfig.json       # TypeScript 설정
├── public/                  # PHP 백엔드 & 정적 파일
├── src/                    # PHP 백엔드 소스
├── package.json            # 루트 의존성 (v3.1.0)
└── docs/                   # 프로젝트 문서
```

## 🔧 주요 설정 파일

### ESLint 9 (Flat Config)
**파일**: `frontend/src/frontend/eslint.config.js`
- 새로운 Flat Config 형식 적용
- TypeScript, React 규칙 포함
- 브라우저/Node.js globals 설정

### TailwindCSS 4.x
**파일**: `frontend/src/frontend/src/index.css`
- 새로운 `@import "tailwindcss"` 구문
- `@theme` 블록에서 CSS 변수 정의
- JavaScript 설정 파일 → CSS 기반 설정

### Vite 7.0
**파일**: `frontend/src/frontend/vite.config.ts`
- 빌드 출력: `../../../public/frontend`
- 스마트 청크 분할: 라이브러리별, 페이지별 최적화
- Terser 압축으로 코드 크기 최소화
- HMR 최적화 설정

## 🔄 React Helmet → 직접 DOM 조작

### 변경사항
React 19 호환성을 위해 `react-helmet-async` 제거하고 직접 DOM 조작 방식으로 변경:

**파일**: `frontend/src/frontend/src/components/common/SEOHead.tsx`
```typescript
// useEffect를 사용한 직접 DOM 조작
useEffect(() => {
  document.title = title;
  
  const setMetaTag = (name: string, content: string, isProperty = false) => {
    // 메타 태그 직접 생성/업데이트
  };
  
  // SEO 메타 태그 설정
}, [title, description, ...]);
```

## 🚨 알려진 이슈 및 해결방법

### 1. Node.js 버전 경고
```
npm warn EBADENGINE required: { node: '>=20.0.0' }
```
**해결됨**: Node.js 20.19.0 설치 완료

### 2. React Router 7 경고
일부 컴포넌트에서 Node.js 20+ 요구 경고 (정상 동작)

### 3. TailwindCSS 4.x 설정
기존 `tailwind.config.js` → CSS 기반 `@theme` 구문 적용

## ⚡ 성능 지표

### 빌드 성능
- **빌드 시간**: 12.97초 (최적화 적용)
- **타입 체크**: 즉시 완료
- **HMR**: 밀리초 단위 업데이트

### 최적화된 번들 크기
- **react-vendor**: 348.15 kB (React 관련)
- **other-vendor**: 590.49 kB (기타 라이브러리)
- **http-vendor**: 34.78 kB (Axios 등 HTTP)
- **community-pages**: 103.02 kB (커뮤니티 페이지)
- **api-hooks**: 4.08 kB (API 훅들)
- **index**: 453.47 kB (메인 애플리케이션)

### 성능 최적화 효과
- ✅ **메모리 사용량**: 가상화로 대폭 감소
- ✅ **네트워크 요청**: 캐싱 및 디바운스로 최적화
- ✅ **초기 로딩**: 청크 분할로 빠른 초기 렌더링
- ✅ **사용자 경험**: 스켈레톤 UI 및 낙관적 업데이트

## 🔐 보안 상태

### 의존성 보안
```bash
npm audit
# 결과: found 0 vulnerabilities ✅
```

### 주요 보안 업데이트
- **Axios 1.10.0**: 보안 패치 적용
- **React 19.1.0**: 최신 보안 개선
- **모든 의존성**: 최신 버전 적용

## 🚀 개발 워크플로우

### 1. 프론트엔드 개발
```bash
cd frontend/src/frontend
npm run dev  # 개발 서버 시작
```

### 2. 코드 검증
```bash
npm run type-check  # TypeScript 검증
npm run lint        # ESLint 검사
```

### 3. 프로덕션 빌드
```bash
npm run build  # 빌드 실행
```

### 4. 통합 테스트
빌드 결과물이 `public/frontend/`에 배포되어 PHP 백엔드와 통합

## 📊 모니터링

### 빌드 모니터링
- 빌드 시간: 2.5초 이하 유지
- 번들 크기: 500kB 경고 발생 시 최적화
- 타입 에러: 0개 유지

### 성능 모니터링
- 개발 서버: HMR 속도
- 프로덕션: 로딩 시간 < 2초

## 🎯 성능 최적화 아키텍처

### React Query 시스템
- **캐싱 전략**: 5분 stale time, 10분 garbage collection
- **낙관적 업데이트**: 즉각적인 UI 반응
- **에러 처리**: 지능적인 재시도 로직

### 무한 스크롤 구현
- **Intersection Observer**: 효율적인 스크롤 감지
- **디바운스 검색**: 500ms 지연으로 API 호출 최적화
- **스켈레톤 로딩**: 부드러운 사용자 경험

### 채팅 가상화
- **react-window**: 2000+ 메시지 효율적 렌더링
- **실시간 업데이트**: Firebase 시뮬레이션
- **무한 로딩**: 50개씩 배치 로딩

### 이미지 최적화
- **지연 로딩**: Intersection Observer 기반
- **포맷 최적화**: WebP/AVIF 자동 감지
- **반응형 이미지**: srcSet 자동 생성

## 🔮 향후 계획

### 단기 (1개월)
- [ ] PWA 기능 추가
- ✅ **성능 최적화 완료** (코드 스플리팅, 가상화)
- [ ] 테스트 커버리지 향상

### 중기 (3개월)
- [ ] React Server Components 적용
- [ ] 마이크로 프론트엔드 아키텍처
- [ ] 성능 모니터링 도구 통합

## 🆘 문제 해결

### 자주 발생하는 문제

1. **빌드 실패**
   ```bash
   npm run type-check  # 타입 에러 확인
   npm run lint        # 린트 에러 확인
   ```

2. **의존성 설치 오류**
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

3. **포트 충돌**
   ```bash
   # 기본 포트 3000 사용 중일 때
   npm run dev -- --port 3001
   ```

## 📞 지원

### 개발 관련 문의
- **이메일**: jh@wincard.kr
- **GitHub**: https://github.com/LeeJeongHyeon123/topmkt

### Claude Code 특화 정보
이 프로젝트는 Claude Code 환경에서 개발되었으며, 모든 설정이 최적화되어 있습니다.

---

**업데이트**: 2025년 6월 29일  
**Claude Code 호환성**: ✅ 완전 지원  
**프로젝트 상태**: 🎯 고성능 최적화 완료  
**성능 등급**: ⚡ Enterprise Ready