# 탑마케팅 웹 애플리케이션

## 소개
탑마케팅은 네트워크 마케팅 전문가들을 위한 통합 커뮤니티 플랫폼입니다. 마케팅 노하우 공유, 강의 참여, 실시간 소통이 가능한 현대적인 웹 애플리케이션입니다.

## 주요 기능

### 🚀 핵심 기능
- **사용자 관리 시스템**: 회원가입, 로그인, 프로필 관리
- **커뮤니티 게시판**: 마케팅 정보 공유 및 토론
- **실시간 댓글 시스템**: AJAX 기반 동적 댓글 기능
- **좋아요 시스템**: 게시글 및 댓글 좋아요 기능
- **강의 일정 관리**: 마케팅 강의 예약 및 관리
- **행사 일정 관리**: 마케팅 이벤트 및 세미나 관리

### 🔐 보안 및 인증
- **JWT 기반 인증**: 현대적이고 확장 가능한 토큰 기반 인증 시스템
- **장기 로그인 지원**: 30일간 자동 로그인 상태 유지
- **SMS 인증**: Aligo API 연동 휴대폰 인증
- **reCAPTCHA v3**: Google reCAPTCHA 보안 검증
- **CSRF 보호**: Cross-Site Request Forgery 방지
- **자동 토큰 갱신**: 백그라운드에서 seamless 토큰 리프레시

### 🎨 사용자 경험
- **반응형 디자인**: 모바일/태블릿/데스크톱 최적화
- **프로필 시스템**: 개인 프로필 페이지 및 공유 기능
- **소셜 링크 연동**: 다양한 SNS 플랫폼 연결
- **실시간 로딩 UI**: 사용자 친화적 로딩 애니메이션
- **한국어 URL 지원**: 한글 닉네임 기반 프로필 URL

### ⚡ 성능 최적화
- **검색 시스템**: 고성능 게시글 검색 기능
- **캐싱 시스템**: Redis 기반 성능 최적화
- **이미지 최적화**: 자동 이미지 리사이징 및 압축
- **데이터베이스 최적화**: 인덱싱 및 쿼리 최적화
- **프로필 이미지 지연 로딩**: 0.025초 로딩 시간 달성
- **AJAX 기반 모달**: 필요시에만 원본 이미지 로딩

## 최신 업데이트 (2025.06)

### 🚀 주요 라이브러리 업그레이드 (NEW!)
- ✅ **React 19.1.0 업그레이드**: 최신 React 성능 개선 및 새로운 기능 적용
- ✅ **Node.js 20.19.0 업그레이드**: 최신 JavaScript 런타임 환경 적용
- ✅ **Vite 7.0.0 업그레이드**: 차세대 빌드 도구로 빌드 성능 대폭 향상
- ✅ **TypeScript 5.8.3 업그레이드**: 개선된 타입 시스템 및 컴파일 성능
- ✅ **TailwindCSS 4.1.11 업그레이드**: 새로운 @theme 구문 및 성능 개선
- ✅ **React Router 7.6.2 업그레이드**: 최신 라우팅 시스템 기능
- ✅ **ESLint 9.29.0 업그레이드**: 새로운 flat config 형식 적용
- ✅ **보안 취약점 완전 해결**: 모든 의존성 보안 이슈 0개 달성

### 🔧 기술 스택 현대화
- ✅ **React Helmet 제거**: React 19 호환을 위한 직접 DOM 조작 방식 적용
- ✅ **PostCSS 설정 최신화**: TailwindCSS 4.x 지원을 위한 설정 변경
- ✅ **ESLint 설정 마이그레이션**: flat config 형식으로 완전 전환
- ✅ **성능 최적화**: 빌드 시간 단축 및 번들 크기 최적화

### 🆕 신규 기능 (2025.01)
- ✅ 프로필 공유 시스템 (Web Share API 지원)
- ✅ 실제 좋아요 수 계산 및 표시
- ✅ 한국어 닉네임 URL 인코딩 지원
- ✅ 커스텀 로딩 UI 시스템
- ✅ SMS 인증 시스템 완전 구현
- ✅ 프로필 이미지 지연 로딩 시스템
- ✅ 대용량 프로필 이미지 모달 뷰어

### 🔧 개선 사항  
- ✅ **프론트엔드 모던화**: PHP 뷰에서 React로 완전 전환
- ✅ **타입 안전성 확보**: TypeScript 도입으로 런타임 오류 방지
- ✅ **컴포넌트 재사용성**: 모듈화된 컴포넌트 시스템 구축
- ✅ **상태 관리 최적화**: Context API 기반 전역 상태 관리
- ✅ **API 통신 표준화**: 일관된 API 호출 및 에러 처리
- ✅ **개발 환경 개선**: Hot Reload, ESLint, 자동 빌드 시스템
- ✅ **성능 최적화**: 코드 스플리팅, 지연 로딩 적용
- ✅ **사용자 경험 향상**: 로딩 상태, 에러 처리, 토스트 알림

## 설치 방법

### 시스템 요구사항
- PHP 8.0.30 이상
- MariaDB 10.6.5 이상
- Apache/Nginx 웹서버
- Composer
- Node.js 20.19.0+ 이상
- npm 10.8.2+ 이상

### 설치 과정
```bash
# 저장소 복제
git clone https://github.com/LeeJeongHyeon123/topmkt.git

# 디렉토리 이동
cd topmkt

# PHP 의존성 설치
composer install

# Node.js 의존성 설치
npm install

# 프론트엔드 의존성 설치 및 빌드
cd frontend/src/frontend
npm install
npm run build

# 환경 설정
cp .env.example .env
# .env 파일에서 데이터베이스 및 API 키 설정

# 데이터베이스 마이그레이션
mysql -u root -p < database/setup.sql
```

### 환경 설정
```env
# 데이터베이스 설정
DB_HOST=localhost
DB_NAME=topmkt
DB_USER=your_user
DB_PASS=your_password

# SMS API 설정 (Aligo)
ALIGO_API_KEY=your_api_key
ALIGO_USER_ID=your_user_id
ALIGO_SENDER=your_phone_number

# reCAPTCHA 설정
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

## 기술 스택

### Backend
- **PHP 8.0.30**: 메인 서버 언어
- **MariaDB 10.6.5**: 주 데이터베이스
- **Redis**: 캐싱 및 세션 스토리지
- **Apache**: 웹서버

### Frontend
- **React 19.1.0**: 최신 컴포넌트 기반 UI 라이브러리 (React 19 최신 성능 개선)
- **TypeScript 5.8.3**: 향상된 타입 안전성을 위한 JavaScript 슈퍼셋
- **Vite 7.0.0**: 차세대 초고속 빌드 도구 및 개발 서버
- **React Router 7.6.2**: 최신 SPA 라우팅 시스템
- **TailwindCSS 4.1.11**: 차세대 유틸리티 기반 CSS 프레임워크
- **Axios 1.10.0**: 최신 HTTP 클라이언트 라이브러리
- **ESLint 9.29.0**: 최신 코드 품질 관리 도구
- **Web Share API**: 네이티브 공유 기능

### 외부 서비스
- **Aligo SMS API**: 휴대폰 인증
- **Google reCAPTCHA v3**: 보안 검증
- **Font Awesome**: 아이콘 라이브러리
- **Google Fonts**: 웹 폰트

### 개발 도구
- **Git**: 버전 관리
- **GitHub Actions**: CI/CD 파이프라인
- **Composer**: PHP 의존성 관리
- **npm**: JavaScript 의존성 관리
- **Node.js 20.19.0**: 최신 JavaScript 런타임 환경

## 프로젝트 구조
```
topmkt/
├── docs/                   # 프로젝트 문서
├── public/                 # 웹 루트 디렉토리 (PHP 백엔드)
│   ├── assets/            # 정적 자원
│   ├── frontend/          # React 빌드 결과물
│   └── index.php          # PHP 진입점
├── src/                   # PHP 백엔드 소스 코드
│   ├── config/           # 설정 파일
│   ├── controllers/      # 컨트롤러
│   ├── models/          # 모델
│   ├── views/           # PHP 뷰 (레거시)
│   ├── helpers/         # 헬퍼 함수
│   └── middlewares/     # 미들웨어
├── frontend/              # React 프론트엔드
│   └── src/frontend/     # React 소스 코드
│       ├── src/         # 컴포넌트, 페이지, 훅
│       ├── public/      # 정적 파일
│       └── package.json # 의존성 관리
├── database/             # 데이터베이스 스크립트
└── tests/               # 테스트 파일
```

## API 엔드포인트

### 인증 API
- `POST /auth/signup` - 회원가입
- `POST /auth/login` - 로그인 (JWT 토큰 발급)
- `POST /auth/logout` - 로그아웃 (JWT 토큰 무효화)
- `POST /auth/refresh` - JWT 토큰 갱신
- `GET /auth/me` - 현재 사용자 정보 및 토큰 상태 확인
- `POST /auth/send-verification` - SMS 인증 발송
- `POST /auth/verify-code` - 인증번호 확인

### 커뮤니티 API
- `GET /community` - 게시글 목록
- `GET /community/posts/{id}` - 게시글 상세
- `POST /community/posts` - 게시글 작성
- `PUT /community/posts/{id}` - 게시글 수정
- `DELETE /community/posts/{id}` - 게시글 삭제

### 댓글 API
- `GET /api/comments` - 댓글 목록
- `POST /api/comments` - 댓글 작성
- `PUT /api/comments/{id}` - 댓글 수정
- `DELETE /api/comments/{id}` - 댓글 삭제

### 좋아요 API
- `POST /api/posts/{id}/like` - 게시글 좋아요 토글
- `GET /api/posts/{id}/like` - 좋아요 상태 조회

### 사용자 API
- `GET /api/users/{id}/profile-image` - 사용자 프로필 이미지 정보

## 프로젝트 문서
- [문서 인덱스 (요약)](docs/0.문서_인덱스.md)
- [기획서](docs/1.%20기획서.md)
- [정책서](docs/2.%20정책서.md)
- [시스템 아키텍처](docs/3.%20시스템%20아키텍처.md)
- [DB 구조](docs/4.DB구조.md)
- [API 설계서](docs/5.%20API%20설계서.md)
- [디렉토리 구조](docs/6.디렉토리구조.md)
- [코딩 컨벤션 스타일 가이드](docs/7.코딩컨벤션스타일가이드.md)
- [개발 체크리스트](docs/8.개발체크리스트.md)
- [CI/CD GitHub Actions 가이드](docs/9.CI-CD_GitHub_Actions.md)

## 개발 현황

### ✅ 완료된 기능
- **React 프론트엔드 전환** (기존 디자인 완벽 재현)
- **JWT 기반 인증 시스템** (30일 장기 로그인 지원)  
- 사용자 인증 시스템 (SMS 인증 포함)
- 커뮤니티 게시판 (CRUD, React 구현)
- 댓글 시스템 (실시간 AJAX)
- 좋아요 시스템  
- 프로필 관리 시스템
- 프로필 공유 기능
- 반응형 UI/UX
- 검색 기능
- 강의/행사 일정 관리

### 🚧 개발 중인 기능  
- **React 전환 완료**: 사용자 프로필, 관리자 페이지 (75% 완료)
- 실시간 알림 시스템
- 파일 업로드 시스템  
- 모바일 앱 연동 API

### 📋 향후 계획
- PWA (Progressive Web App) 지원
- 다국어 지원
- 고급 분석 도구
- 소셜 로그인 연동

## GitHub 연동 상태
- ✅ GitHub 저장소: [LeeJeongHyeon123/topmkt](https://github.com/LeeJeongHyeon123/topmkt.git)
- ✅ GitHub Actions CI/CD 파이프라인 연동 완료
- ✅ 자동 배포 시스템 구축
- ✅ 코드 품질 검사 자동화
- ✅ 환경별 배포 분리 (Staging/Production)

## 성능 지표
- **페이지 로드 시간**: < 2초
- **검색 응답 시간**: < 500ms
- **동시 접속자**: 1000명 지원
- **데이터베이스 최적화**: 99.9% 쿼리 최적화

## 보안 기능
- 🔒 SQL Injection 방지
- 🔒 XSS (Cross-Site Scripting) 방지
- 🔒 CSRF (Cross-Site Request Forgery) 방지
- 🔒 입력 데이터 검증 및 새니타이징
- 🔒 세션 하이재킹 방지
- 🔒 브루트 포스 공격 방지

## 라이센스
이 프로젝트는 MIT 라이센스 하에 제공됩니다.

## 기여하기
1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 연락처
- **프로젝트 관리자**: LeeJeongHyeon123
- **이메일**: jh@wincard.kr
- **웹사이트**: https://www.topmktx.com

## 버전 정보
- **현재 버전**: 3.1.0 (라이브러리 현대화)
- **마지막 업데이트**: 2025년 6월
- **안정성**: Production Ready
- **주요 변경사항**: 모든 주요 라이브러리 최신 버전 업그레이드, 보안 강화, 성능 최적화 