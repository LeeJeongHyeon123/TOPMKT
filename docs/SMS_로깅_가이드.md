# SMS 인증 시스템 로깅 가이드

**작성일:** 2025-06-27  
**최종 수정일:** 2025-06-27  
**목적:** SMS 인증번호 발송 시스템의 로그 위치 및 모니터링 방법  

---

## 🔍 로그 위치 정리

### 1. **topmkt 프로젝트 전용 로그** ⭐ **권장**
```bash
# topmkt 프로젝트 모든 로그 (SMS, 라우팅, DB 등)
/var/www/html/topmkt/logs/topmkt_errors.log
```
- **용도**: topmkt 프로젝트의 모든 로그 (SMS, 라우팅, 컨트롤러, DB 등)
- **설정 위치**: `/workspace/public/index.php:32-33`
- **포맷**: PHP 표준 error_log 포맷
- **장점**: 다른 프로젝트에 영향 없이 topmkt만 독립적 로깅
- **Claude Code 접근**: `/workspace/logs/topmkt_errors.log`로 실시간 확인 가능

### 2. **PHP-FPM 시스템 로그** (참고용)
```bash
# 다른 프로젝트들과 공통으로 사용하는 시스템 로그
/var/log/php-fpm/www-error.log
```
- **용도**: 모든 프로젝트의 PHP 에러가 기록됨 (topmkt + 다른 프로젝트들)
- **단점**: 로그가 섞여서 분석이 어려움
- **권장**: topmkt 전용 로그 사용 권장

---

## 🚀 SMS 디버깅 정보

### 현재 SMS 발송 시 기록되는 정보:

#### AuthController.php (인증번호 발송)
```
=== SMS 인증번호 발송 디버깅 ===
원본 입력 데이터: {"phone":"010-2659-1346","type":"SIGNUP"}
처리된 전화번호: 010-2659-1346
reCAPTCHA 토큰: [토큰값]
인증번호 생성됨: 1234
SMS 발송 시작...
SMS 발송 결과: {"success":true,"message":"SMS 발송이 완료되었습니다."}
SMS 발송 성공
```

#### SmsService.php (실제 API 호출)
```
=== SmsService::sendSms 시작 ===
수신자: 010-2659-1346
메시지: [탑마케팅] 인증번호 1234
제목: 
메시지 타입: 
포맷팅된 전화번호: 010-2659-1346
API 호출 전송 데이터: {"key":"ukqd...","userid":"neungsoft","sender":"070-4136-8899","receiver":"010-2659-1346","msg":"[탑마케팅] 인증번호 1234","msg_type":"SMS","testmode_yn":"N"}
API URL: https://apis.aligo.in/send/
API 응답: {"result_code":"1","message":"success","msg_id":"1089061854","success_cnt":1,"error_cnt":0,"msg_type":"SMS"}
API 결과 코드: 1
발송 성공 여부: true
```

---

## 📊 로그 확인 방법

### 1. **호스트에서 직접 확인** ⭐ **권장**
```bash
# 최근 로그 확인
tail -50 /var/www/html/topmkt/logs/topmkt_errors.log

# SMS 관련 로그만 필터링
grep -i "SMS\|인증번호" /var/www/html/topmkt/logs/topmkt_errors.log | tail -20

# 실시간 모니터링
tail -f /var/www/html/topmkt/logs/topmkt_errors.log

# 로그 동기화 (Claude Code 접근용)
sh /var/www/html/topmkt/sync_logs_host.sh
```

### 2. **로그 뷰어 스크립트 사용**
```bash
# 다양한 로그 경로를 자동으로 확인하고 SMS 관련 로그 검색
/workspace/read_logs.sh -f SMS
/workspace/read_logs.sh -f 인증번호
/workspace/read_logs.sh -n 100  # 최근 100줄
```

---

## 🔧 로그 활성화 방법

### 1. **로그 디렉토리 생성**
```bash
# 메인 프로젝트 로그 디렉토리
sudo mkdir -p /var/www/html/topmkt/logs
sudo chmod 777 /var/www/html/topmkt/logs

# 표준 로그 디렉토리  
sudo mkdir -p /var/log/topmkt
sudo chmod 777 /var/log/topmkt
```

### 2. **PHP 에러 로그 활성화**
```bash
# php.ini 설정 확인
php -i | grep error_log

# 또는 런타임에서 설정 (database.php에서 이미 설정됨)
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/topmkt/logs/topmkt_errors.log');
```

### 3. **권한 설정**
```bash
# 웹서버가 로그 파일에 쓸 수 있도록 권한 설정
sudo chown -R www-data:www-data /var/www/html/topmkt/logs
sudo chown -R www-data:www-data /var/log/topmkt
```

---

## 🎯 실시간 디버깅 팁

### 1. **SMS 발송 테스트 시 로그 확인**
```bash
# 터미널 1: 실시간 로그 모니터링
php /workspace/view_logs.php monitor

# 터미널 2: 웹에서 인증번호 발송 버튼 클릭
# SMS 발송 과정이 실시간으로 로그에 표시됨
```

### 2. **특정 시간대 로그 확인**
```bash
# 오늘 SMS 관련 모든 로그
grep -i "sms\|aligo\|인증번호" /var/www/html/topmkt/logs/topmkt_errors.log

# 시간대별 필터링 (예: 오후 2시~3시)
grep "14:" /var/www/html/topmkt/logs/topmkt_errors.log | grep -i sms
```

### 3. **에러 발생 시 즉시 확인**
```bash
# 최근 에러 로그만 확인
tail -50 /var/www/html/topmkt/logs/topmkt_errors.log | grep -i error

# 실시간 에러 모니터링
tail -f /var/www/html/topmkt/logs/topmkt_errors.log | grep --color=always -i "error\|fail\|exception"
```

---

## 📈 문제 해결 체크리스트

### SMS 발송이 안 될 때:

1. **로그 디렉토리 존재 확인**
   ```bash
   ls -la /var/www/html/topmkt/logs/
   ls -la /var/log/topmkt/
   ```

2. **권한 확인**
   ```bash
   ls -la /var/www/html/topmkt/logs/topmkt_errors.log
   ```

3. **PHP CURL 확장 확인**
   ```bash
   php -m | grep curl
   ```

4. **실시간 로그 확인**
   ```bash
   php /workspace/view_logs.php monitor
   ```

5. **API 응답 확인**
   - 로그에서 `API 응답:` 부분 확인
   - `result_code: 1`이면 성공
   - `error_cnt: 0`이면 정상

---

## 📋 로그 레벨 의미

### Aligo API 응답 코드:
- **result_code: "1"** ✅ 성공
- **result_code: "0"** ❌ 실패  
- **success_cnt: 1** ✅ 발송 성공 건수
- **error_cnt: 0** ✅ 발송 실패 건수

### SMS 발송 프로세스:
1. 프론트엔드 → AuthController::sendVerification()
2. AuthController → sendAuthCodeSms() 
3. sendAuthCodeSms() → SmsService::sendSms()
4. SmsService → Aligo API 호출
5. Aligo API → SMS 발송 완료

---

**이 가이드를 통해 SMS 인증 시스템의 모든 로그를 체계적으로 모니터링할 수 있습니다.**