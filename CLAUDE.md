# 탑마케팅 프로젝트 - Claude 작업 이력

## 프로젝트 개요
탑마케팅은 글로벌 네트워크 마케팅 전문가들을 위한 커뮤니티 플랫폼입니다. 강의 일정 관리, 사용자 등록 시스템, 실시간 채팅, 기업 회원 관리 등의 기능을 제공합니다.

## 최근 주요 작업 (2025-07-01)

### 🔥 긴급 시스템 복구 작업
**문제**: 강의 페이지(/lectures) 접근 시 404 오류 및 시스템 장애
**해결**: 울트라씽크 모드로 완전 진단 및 복구 완료

#### 1. 디버깅 시스템 구축
- **debug_fixed.php**: 완전한 실시간 디버깅 콘솔 개발
  - 6개 탭 구성: 콘솔, 서버, PHP, 데이터베이스, 시스템, 액션
  - JavaScript 함수 로딩 순서 문제 완전 해결
  - 실시간 로그 캡처 및 오류 추적
  - PHP 출력 버퍼링 문제 해결

- **추가 진단 도구들**:
  - `debug_php_errors.php`: PHP 오류 전용 진단
  - `check_html_output.php`: HTML 렌더링 상태 분석
  - `debug_simple_test.php`: 간단한 탭 기능 테스트
  - `test_lectures_route.php`: 라우팅 시스템 직접 테스트

#### 2. 근본 원인 파악 및 해결
**발견된 문제**:
```
Fatal error: Call to undefined method AuthMiddleware::getUserRole() 
in /var/www/html/topmkt/src/views/templates/header.php:141
```

**해결 과정**:
1. **AuthMiddleware 수정** (`/src/middlewares/AuthMiddleware.php`)
   - `getUserRole()` 메소드 추가 (호환성을 위한 별칭)
   - 기존 `getCurrentUserRole()` 메소드와 연동

2. **헤더 템플릿 수정** (`/src/views/templates/header.php`)
   - `APP_NAME` 상수 → '탑마케팅' 직접 대체
   - `$page_title` 변수 처리 개선

#### 3. 시스템 검증
- 라우팅 시스템: ✅ 정상 작동
- LectureController: ✅ 정상 작동  
- 데이터베이스 연결: ✅ 정상 작동
- JavaScript 기능: ✅ 모든 오류 해결
- 탭 전환 시스템: ✅ 6개 탭 모두 정상 작동

## 기술 스택

### Backend
- **PHP 8.x**: 서버사이드 로직
- **MySQL**: 데이터베이스
- **JWT**: 인증 시스템
- **MVC 패턴**: 아키텍처

### Frontend  
- **Vanilla JavaScript**: 클라이언트 로직
- **CSS Grid/Flexbox**: 레이아웃
- **Font Awesome**: 아이콘
- **Google Fonts**: 타이포그래피

### 주요 기능
- 강의 일정 관리 (캘린더/리스트 뷰)
- 사용자 인증 (JWT 기반)
- 실시간 채팅 (Firebase)
- 강의 신청 시스템
- 기업 회원 관리
- 커뮤니티 게시판

## 개발 환경 설정

### 필수 PHP 확장
```bash
# CentOS/RHEL
sudo yum install php-mysqli php-curl php-json php-session php-mbstring php-openssl php-zip
```

### 웹서버 재시작
```bash
sudo systemctl restart httpd
sudo systemctl restart php-fpm
```

## 디버깅 도구 사용법

### 실시간 디버깅 콘솔
```
https://www.topmktx.com/debug_fixed.php
```

**기능**:
- 실시간 JavaScript 콘솔 로그 캡처
- 서버 로그 분석
- PHP 환경 상태 확인
- 데이터베이스 연결 테스트
- 시스템 리소스 모니터링
- 긴급 복구 액션

### 라우팅 테스트
```
https://www.topmktx.com/test_lectures_route.php
```

## 파일 구조

```
/workspace/
├── public/
│   ├── debug_fixed.php          # 메인 디버깅 콘솔
│   ├── test_lectures_route.php  # 라우팅 테스트
│   └── index.php               # 메인 엔트리 포인트
├── src/
│   ├── controllers/
│   │   └── LectureController.php
│   ├── middlewares/
│   │   └── AuthMiddleware.php   # JWT 인증 미들웨어
│   ├── views/
│   │   └── templates/
│   │       └── header.php       # 공통 헤더
│   └── config/
│       └── routes.php           # 라우팅 설정
└── CLAUDE.md                   # 이 문서
```

## 커밋 이력

### v1.5.0 - 시스템 완전 복구 (2025-07-01)
- 강의 페이지 404 오류 완전 해결
- 실시간 디버깅 시스템 구축
- AuthMiddleware::getUserRole() 메소드 추가
- JavaScript 함수 로딩 순서 문제 해결
- PHP 출력 버퍼링 문제 해결
- 헤더 템플릿 변수 처리 개선

## 향후 개선 사항

### 단기 목표
- [ ] 성능 모니터링 시스템 구축
- [ ] 에러 로깅 시스템 개선
- [ ] 자동화된 헬스체크 도구

### 장기 목표  
- [ ] 마이크로서비스 아키텍처 전환
- [ ] Docker 컨테이너화
- [ ] CI/CD 파이프라인 구축

## 연락처
- 개발팀: (주)윈카드
- 플랫폼: 탑마케팅 (https://www.topmktx.com)

---

**마지막 업데이트**: 2025-07-01
**작업자**: Claude (Anthropic)
**작업 모드**: 울트라씽크 모드