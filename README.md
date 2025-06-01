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
```

## 기술 스택
- PHP 7.4+
- MySQL 5.7+
- HTML/CSS/JavaScript
- GitHub Actions

## 배포 정보
자세한 CI/CD 파이프라인 정보는 [CI/CD GitHub Actions 가이드](docs/9.CI-CD_GitHub_Actions.md)를 참조하세요.

## GitHub 연동 상태
- GitHub 저장소: [LeeJeongHyeon123/topmkt](https://github.com/LeeJeongHyeon123/topmkt.git)
- GitHub Actions CI/CD 파이프라인 연동 완료
- GitHub Actions 워크플로우 권한 설정 완료 (Read and write permissions)
- GitHub Actions 워크플로우 개선: 태그 푸시 권한 추가 및 명시적 토큰 사용
- GitHub Actions 워크플로우 개선: composer.json 체크 로직 추가

## 라이센스
이 프로젝트는 MIT 라이센스 하에 제공됩니다. 