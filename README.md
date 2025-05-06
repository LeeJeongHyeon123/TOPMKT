# 탑마케팅(TOPMKT)

## 프로젝트 소개
탑마케팅(TOPMKT)은 기업의 마케팅 전략을 지원하는 웹 애플리케이션입니다. 반응형 웹 디자인을 통해 모든 디바이스에서 최적의 경험을 제공합니다.

## 기술 스택
- 백엔드: PHP 7.4.33
- 데이터베이스: MariaDB 10.4.34
- 프론트엔드: HTML5, CSS3, JavaScript
- 실시간 데이터: Firebase Firestore
- 파일 저장소: Firebase Storage
- 웹 서버: Apache 2.4.58
- 운영체제: CentOS 7.9.2009

## 디렉토리 구조
프로젝트의 디렉토리 구조는 `디렉토리구조.note` 파일에 상세히 정의되어 있습니다.

## 개발 환경 설정
1. 저장소 클론:
```bash
git clone https://github.com/LeeJeongHyeon123/TOPMKT.git
cd TOPMKT
```

2. 종속성 설치 (Composer가 설치되어 있어야 함):
```bash
composer install
```

3. 환경 설정:
```bash
cp .env.example .env
# .env 파일 편집하여 환경 설정 완료
```

4. 데이터베이스 설정:
```bash
cp config/database.sample.php config/database.php
# database.php 파일 편집하여 데이터베이스 연결 설정 완료
```

5. Firebase 설정 (필요한 경우):
```bash
cp config/firebase.sample.php config/firebase.php
# firebase.php 파일 편집하여 Firebase 연결 설정 완료
```

## 다국어 지원
- 한국어 (기본)
- 영어
- 중국어 간체
- 중국어 번체
- 일본어

## 개발 정책
- 개발 관련 정책은 `기본정책.note` 파일에 정의되어 있습니다.
- 모든 커밋 메시지는 한글로 작성합니다.
- 모든 코드 주석은 한글로 작성합니다.

## 라이센스
이 프로젝트는 비공개 소프트웨어이며 무단 복제 및 배포를 금지합니다.
