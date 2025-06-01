# 탑마케팅 웹 애플리케이션

## 소개
탑마케팅은 효과적인 마케팅 솔루션을 제공하는 웹 애플리케이션입니다.

## 기능
- 사용자 관리
- 게시판 기능
- 댓글 기능
- CI/CD 자동화 (GitHub Actions)

## 설치 방법
```bash
# 저장소 복제
git clone https://github.com/LeeJeongHyeon123/topmkt.git

# 디렉토리 이동
cd topmkt

# 의존성 설치
composer install
npm install
```

## 기술 스택
- PHP 8.0.30
- MariaDB 10.6.5
- HTML/CSS/JavaScript
- GitHub Actions

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

## GitHub 연동 상태
- GitHub 저장소: [LeeJeongHyeon123/topmkt](https://github.com/LeeJeongHyeon123/topmkt.git)
- GitHub Actions CI/CD 파이프라인 연동 완료
- GitHub Actions 워크플로우 권한 설정 완료 (Read and write permissions)
- GitHub Actions 워크플로우 개선: 태그 푸시 권한 추가 및 명시적 토큰 사용
- GitHub Actions 워크플로우 개선: composer.json 체크 로직 추가
- GitHub Actions 워크플로우 개선: Staging/Production 환경 분리 설정 완료

## 개발 환경 설정
- 코딩 컨벤션 설정: PSR-12 기반 PHP 코딩 표준 및 ESLint 설정 완료
- 린팅 도구: PHP_CodeSniffer 및 ESLint 설정 완료
- Pull Request 템플릿 구성 완료

## 라이센스
이 프로젝트는 MIT 라이센스 하에 제공됩니다. 

## 버전 정보
현재 버전: 1.0.2 