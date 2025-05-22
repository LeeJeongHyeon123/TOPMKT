# 회원가입 데이터 초기화 스크립트 사용 설명서

## 개요

이 스크립트는 회원가입 테스트에 사용된 데이터를 초기화하는 도구입니다. 테스트 과정에서 생성된 회원 계정을 다음 두 곳에서 모두 삭제합니다:

1. MariaDB의 users 테이블
2. Firebase Authentication의 사용자 계정

이 스크립트를 사용하면 회원가입 테스트를 반복적으로 수행하고 테스트 데이터를 쉽게 정리할 수 있습니다.

## 실행 환경 지원

이 스크립트는 두 가지 언어로 구현되어 있어 다양한 환경에서 실행할 수 있습니다:

1. **Python 버전** - Python 환경에서 실행 가능 (권장)
2. **PHP 버전** - Python이 사용 불가능한 환경에서 대체 실행 가능

## 필요 사항

### Python 버전 실행 시 필요사항
- Python 3.6 이상
- 필요 패키지:
  - firebase-admin
  - pymysql

필요한 패키지가 설치되어 있지 않은 경우, 다음 명령어로 설치할 수 있습니다:

```bash
pip3 install --user firebase-admin pymysql
```

### PHP 버전 실행 시 필요사항
- PHP 7.0 이상
- PHP PDO 확장
- (선택적) Firebase PHP SDK - Firebase 삭제 기능 사용 시 필요

## 사용 방법

### 1. 쉘 스크립트 (권장)

쉘 스크립트를 통해 간편하게 실행할 수 있습니다. 쉘 스크립트는 자동으로 Python 또는 PHP 스크립트를 선택하여 실행합니다:

```bash
# 모든 사용자 데이터 삭제
./cleanup_user.sh

# 특정 전화번호의 사용자 데이터 삭제
./cleanup_user.sh 01012345678
```

### 2. Python 스크립트 직접 실행

Python 스크립트를 직접 실행하여 더 상세한 옵션을 지정할 수도 있습니다:

```bash
# 모든 사용자 데이터 삭제 (확인 메시지 표시)
python3 cleanup_test_users.py --all

# 특정 전화번호의 사용자 데이터 삭제
python3 cleanup_test_users.py --phone 01012345678

# 확인 메시지 없이 바로 삭제 실행
python3 cleanup_test_users.py --all --confirm
```

### 3. PHP 스크립트 직접 실행

Python이 사용 불가능한 환경에서는 PHP 스크립트를 직접 실행할 수 있습니다:

```bash
# 모든 사용자 데이터 삭제 (확인 메시지 표시)
php cleanup_test_users.php --all

# 특정 전화번호의 사용자 데이터 삭제
php cleanup_test_users.php --phone=01012345678

# 확인 메시지 없이 바로 삭제 실행
php cleanup_test_users.php --all --confirm
```

## 옵션 설명

- `--all`: 모든 테스트 사용자 삭제 (주의: 실제 사용자도 삭제될 수 있음)
- `--phone 전화번호` / `--phone=전화번호`: 특정 전화번호로 등록된 사용자만 삭제
- `--confirm`: 확인 없이 바로 실행 (기본적으로는 삭제 전 확인 요청)

## 주의 사항

1. 이 스크립트는 데이터를 **영구적으로 삭제**합니다. 신중하게 사용하세요.
2. `--all` 옵션 사용 시 모든 사용자 데이터가 삭제되므로, 실제 운영 환경에서는 사용하지 마세요.
3. 관리자 권한이 있는 계정에서 실행해야 합니다.

## 일반적인 사용 패턴

1. 회원가입 테스트 수행
2. 테스트 확인 완료
3. 스크립트를 사용하여 테스트 데이터 초기화
4. 다시 회원가입 테스트 수행

## 문제 해결

- **권한 오류**: 스크립트에 실행 권한이 없는 경우 다음 명령어로 권한을 부여하세요:
  ```bash
  chmod +x cleanup_test_users.py cleanup_test_users.php cleanup_user.sh
  ```

- **데이터베이스 연결 오류**: 데이터베이스 설정이 올바른지 확인하세요. 설정은 `/config/database.php` 파일에서 자동으로 로드됩니다.

- **Firebase 인증 오류**: Firebase 서비스 계정 키 파일(`/config/google/service-account.json`)이 올바르게 설정되었는지 확인하세요.

- **Python 모듈 오류**: Python 스크립트 실행 시 모듈 오류가 발생하면 다음 명령어로 필요한 모듈을 설치하세요:
  ```bash
  pip3 install --user firebase-admin pymysql
  ```

- **PHP 모듈 오류**: PHP 스크립트에서 Firebase 기능을 사용하기 위해서는 Firebase PHP SDK가 설치되어 있어야 합니다. 