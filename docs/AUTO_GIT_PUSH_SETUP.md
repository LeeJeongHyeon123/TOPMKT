# 자동 Git 푸시 설정 가이드

**작성일**: 2025-06-15  
**목적**: Claude Code가 커밋한 내용을 자동으로 GitHub에 푸시하는 시스템 구축

---

## 🎯 시스템 개요

Claude Code가 코드 변경 및 커밋을 완료하면, crontab을 통해 자동으로 GitHub에 푸시되는 시스템입니다.

### 동작 원리:
1. **Claude Code**: 코드 수정 → 커밋 → 태그 생성
2. **Crontab**: 정기적으로 로컬 저장소 확인
3. **자동 푸시**: 새 커밋이 있으면 GitHub에 자동 푸시
4. **로깅**: 모든 작업 내역을 로그파일에 기록

---

## 🚀 설정 방법

### 1단계: 스크립트 권한 설정

```bash
# 실행 권한 부여
chmod +x /var/www/html/topmkt/scripts/auto-git-push.sh

# 로그 디렉토리 확인
sudo mkdir -p /var/log
sudo touch /var/log/auto-git-push.log
sudo chown root:root /var/log/auto-git-push.log
sudo chmod 644 /var/log/auto-git-push.log
```

### 2단계: Git 인증 설정 확인

```bash
# 프로젝트 디렉토리로 이동
cd /var/www/html/topmkt

# Git 인증이 저장되어 있는지 확인
git config credential.helper store

# 테스트 푸시 (한 번만 인증하면 저장됨)
git push origin master --tags
```

### 3단계: Crontab 설정

```bash
# crontab 편집
crontab -e

# 다음 라인 추가 (옵션 중 선택):

# 옵션 1: 5분마다 체크 (빠른 반영)
*/5 * * * * /var/www/html/topmkt/scripts/auto-git-push.sh >/dev/null 2>&1

# 옵션 2: 15분마다 체크 (권장)
*/15 * * * * /var/www/html/topmkt/scripts/auto-git-push.sh >/dev/null 2>&1

# 옵션 3: 30분마다 체크 (여유있게)
*/30 * * * * /var/www/html/topmkt/scripts/auto-git-push.sh >/dev/null 2>&1

# 옵션 4: 1시간마다 체크 (안정적)
0 * * * * /var/www/html/topmkt/scripts/auto-git-push.sh >/dev/null 2>&1
```

### 4단계: 동작 확인

```bash
# 수동으로 스크립트 실행 테스트
/var/www/html/topmkt/scripts/auto-git-push.sh

# 로그 확인
tail -f /var/log/auto-git-push.log

# crontab 목록 확인
crontab -l
```

---

## 📋 스크립트 기능

### 🔍 주요 기능:
- **지능형 체크**: 로컬 저장소가 원격보다 앞서 있을 때만 푸시
- **안전한 푸시**: Git 상태를 확인 후 안전하게 푸시
- **상세 로깅**: 모든 작업 내역을 `/var/log/auto-git-push.log`에 기록
- **로그 로테이션**: 로그파일이 10MB 초과 시 자동 로테이션
- **에러 처리**: 실패 시 상세한 에러 정보 로깅

### 📊 로그 예시:
```
2025-06-15 14:30:01 - INFO: Local repository is 2 commits ahead. Pushing to GitHub...
2025-06-15 14:30:03 - SUCCESS: Successfully pushed 2 commits to GitHub
2025-06-15 14:30:03 - INFO: Latest commit - 3d3c78f 웹사이트 성능 최적화 및 SEO 완성
2025-06-15 14:45:01 - INFO: Repository is up to date (Local: 125, Remote: 125)
```

---

## ⚡ 권장 설정

### 🎯 최적의 crontab 설정:
```bash
# 15분마다 체크 (권장) - 너무 자주 체크하지 않으면서도 빠른 반영
*/15 * * * * /var/www/html/topmkt/scripts/auto-git-push.sh >/dev/null 2>&1
```

### 🔧 이유:
- **5분**: 너무 자주 체크하여 서버 부하 증가
- **15분**: 적당한 주기로 빠른 반영과 서버 부하 균형
- **30분+**: 너무 늦은 반영으로 개발 속도 저하

---

## 🛠️ 문제 해결

### 문제 1: 푸시 실패
```bash
# 인증 정보 재설정
cd /var/www/html/topmkt
git config credential.helper store
git push origin master --tags
```

### 문제 2: 스크립트 실행 안됨
```bash
# 권한 확인
ls -la /var/www/html/topmkt/scripts/auto-git-push.sh

# 권한 재설정
chmod +x /var/www/html/topmkt/scripts/auto-git-push.sh
```

### 문제 3: 로그 확인
```bash
# 실시간 로그 모니터링
tail -f /var/log/auto-git-push.log

# 최근 로그 확인
tail -20 /var/log/auto-git-push.log

# 에러 로그만 확인
grep "ERROR" /var/log/auto-git-push.log
```

### 문제 4: crontab 작동 안됨
```bash
# cron 서비스 상태 확인
systemctl status cron

# cron 서비스 재시작
sudo systemctl restart cron

# crontab 문법 확인
crontab -l
```

---

## 🔐 보안 고려사항

### ✅ 보안 조치:
1. **로그 권한**: 로그파일을 root 권한으로 설정
2. **스크립트 권한**: 실행 권한만 부여, 수정 권한 제한
3. **인증 정보**: Git credential store 사용으로 안전한 토큰 저장
4. **에러 정보**: 민감한 정보는 로그에 기록하지 않음

### 🚨 주의사항:
- Personal Access Token이 만료되면 재설정 필요
- 로그파일이 계속 증가하므로 정기적으로 정리 권장
- crontab 설정 후 반드시 동작 테스트 필요

---

## 📈 사용 후 효과

### ⚡ 개발 속도 향상:
- **Claude Code**: 코드 작성 + 커밋만 담당
- **자동화**: GitHub 푸시 자동 처리
- **실시간 반영**: 15분 내 모든 변경사항 GitHub 반영

### 🎯 워크플로우:
```
Claude Code 작업 → 커밋 완료 → 15분 대기 → 자동 GitHub 푸시 → 완료!
```

---

## 📞 지원

문제 발생 시:
1. **로그 확인**: `/var/log/auto-git-push.log`
2. **수동 테스트**: 스크립트 직접 실행
3. **Git 상태 확인**: `git status`, `git remote -v`

**연락처**: jh@wincard.kr