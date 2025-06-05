# 🚀 Claude CLI 설정 가이드 (Claude App 방식)

Claude CLI가 Docker를 통해 성공적으로 설치되었습니다!

## 📋 설치 완료 항목

✅ Docker CE 26.1.4 설치 완료  
✅ Claude CLI Docker 래퍼 스크립트 생성 (`/usr/local/bin/claude`)  
✅ 시스템 서비스 활성화 완료  
✅ Claude App (OAuth) 인증 방식 설정 완료
✅ Headless 서버 환경 최적화 완료

## 🔑 Claude App 인증 설정 (GUI 없는 서버용)

이 설정은 **claude.ai의 Pro 또는 Max 플랜 구독**을 통해 사용하는 방식입니다.

### 🎯 첫 실행 시 인증 과정 (GUI 없는 서버):

1. **Claude CLI 실행**:
   ```bash
   claude
   ```

2. **인증 방식 선택**:
   - "Claude App (with Pro or Max plan)" 선택
   - OAuth 인증 프로세스 시작

3. **인증 URL 확인**:
   - 터미널에 인증 URL이 표시됩니다 (예: `https://auth.claude.ai/...`)
   - 이 URL을 **복사**하세요

4. **다른 기기에서 인증**:
   - **PC, 스마트폰, 태블릿** 등의 브라우저에서 복사한 URL 접속
   - claude.ai 계정으로 로그인
   - 권한 승인 클릭

5. **터미널에서 완료**:
   - 서버 터미널로 돌아와서 인증 완료 대기
   - 성공하면 Claude CLI 사용 시작!

### 🔄 대안 방법들:

#### 방법 A: SSH 터널링 (고급 사용자)
```bash
# 로컬 PC에서 실행
ssh -L 8080:localhost:8080 user@your-server
```

#### 방법 B: 로컬에서 인증 후 토큰 복사
1. 로컬 PC에 Claude CLI 설치
2. 로컬에서 인증 완료
3. `~/.claude` 폴더를 서버로 복사

### 💳 필요한 구독:

- **Claude Pro** ($20/월): 기본적인 Claude Code 사용
- **Claude Max** ($200/월): 더 많은 사용량과 고급 기능

구독은 [claude.ai](https://claude.ai/)에서 설정할 수 있습니다.

## 🎯 사용 방법

### 기본 실행
```bash
claude
```

### 직접 명령 실행
```bash
claude "코드 리뷰를 해주세요"
claude "이 프로젝트의 구조를 설명해주세요"
```

### 프로젝트 초기화
```bash
claude "/init"
```

## 🔧 작동 원리

- 현재 디렉토리가 Docker 컨테이너 내부의 `/workspace`에 마운트됩니다
- Node.js 18 Ubuntu 이미지를 사용하여 호환성 문제를 해결합니다
- 첫 실행 시 자동으로 Claude CLI를 설치합니다
- OAuth 토큰이 `~/.claude` 디렉토리에 안전하게 저장됩니다
- 인증 정보가 Docker 볼륨으로 마운트되어 재사용됩니다
- 컨테이너 이름 충돌 자동 해결

## 🆚 Claude App vs Anthropic Console

| 구분 | Claude App (현재 설정) | Anthropic Console |
|------|----------------------|-------------------|
| **인증** | OAuth (claude.ai 계정) | API 키 |
| **결제** | 월 구독 ($20 또는 $200) | 사용량 기반 |
| **설정** | 브라우저에서 간편 인증 | API 키 환경변수 설정 |
| **관리** | claude.ai에서 통합 관리 | console.anthropic.com |
| **GUI 없는 서버** | URL 복사 방식으로 가능 | 환경변수만 설정하면 됨 |

## 🚨 주의사항

1. **구독 필요**: claude.ai Pro 또는 Max 플랜이 필요합니다
2. **네트워크 연결**: 인터넷 연결이 필요합니다
3. **Docker 서비스**: Docker 서비스가 실행 중이어야 합니다
4. **브라우저 접근**: 다른 기기의 브라우저를 통한 인증 필요
5. **URL 복사**: 인증 URL을 정확히 복사해야 합니다

## 🔍 문제 해결

### 인증 문제가 있을 때
```bash
# 저장된 인증 정보 초기화
rm -rf ~/.claude
claude  # 다시 인증 시작
```

### 컨테이너 충돌 문제
```bash
# 수동으로 컨테이너 정리
docker rm -f claude-cli-session
claude
```

### Docker 서비스 상태 확인
```bash
sudo systemctl status docker
```

### Docker 서비스 재시작
```bash
sudo systemctl restart docker
```

### 스크립트 권한 확인
```bash
ls -la /usr/local/bin/claude
```

## 📱 모바일 기기 인증 팁

스마트폰으로 인증할 때:
1. 터미널의 URL을 메신저나 메모앱에 복사
2. 스마트폰에서 URL 클릭
3. Claude 앱이나 브라우저에서 로그인
4. 완료 후 서버에서 작업 계속

## 📞 도움이 필요하면

- [Claude.ai 구독 페이지](https://claude.ai/)
- [Claude 공식 문서](https://docs.anthropic.com/ko/docs/claude-code/getting-started)
- [Docker 공식 문서](https://docs.docker.com/)

---

설치 일시: $(date)  
시스템: CentOS 7.9  
Docker 버전: 26.1.4  
인증 방식: Claude App (OAuth)  
환경: Headless Server 최적화 