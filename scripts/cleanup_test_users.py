#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
회원가입 데이터 초기화 스크립트

테스트로 가입한 회원 데이터를 다음 두 곳에서 삭제합니다:
1. MariaDB의 users 테이블
2. Firebase Authentication의 사용자 계정

사용법:
python3 cleanup_test_users.py [--all] [--phone 전화번호] [--confirm]

옵션:
  --all: 모든 테스트 사용자 삭제 (주의: 실제 사용자도 삭제될 수 있음)
  --phone: 특정 전화번호로 등록된 사용자만 삭제
  --confirm: 확인 없이 바로 실행 (기본적으로는 삭제 전 확인 요청)
"""

import os
import sys
import argparse
import pymysql
import firebase_admin
from firebase_admin import credentials, auth
import json
import datetime
import re

# 현재 스크립트 경로
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
# 프로젝트 루트 경로
ROOT_DIR = os.path.dirname(SCRIPT_DIR)

# 색상 코드
class Colors:
    RED = '\033[91m'
    GREEN = '\033[92m'
    YELLOW = '\033[93m'
    BLUE = '\033[94m'
    MAGENTA = '\033[95m'
    CYAN = '\033[96m'
    BOLD = '\033[1m'
    UNDERLINE = '\033[4m'
    END = '\033[0m'

def print_color(text, color):
    print(color + text + Colors.END)

def print_success(text):
    print_color(f"✅ {text}", Colors.GREEN)

def print_warning(text):
    print_color(f"⚠️ {text}", Colors.YELLOW)

def print_error(text):
    print_color(f"❌ {text}", Colors.RED)

def print_info(text):
    print_color(f"ℹ️ {text}", Colors.CYAN)

def print_header(text):
    print("\n" + Colors.BOLD + Colors.BLUE + "=" * 80)
    print(f"  {text}")
    print("=" * 80 + Colors.END + "\n")

def load_db_config():
    """데이터베이스 설정 로드"""
    config_path = os.path.join(ROOT_DIR, 'config', 'database.php')
    
    try:
        with open(config_path, 'r') as f:
            content = f.read()
            
            # PHP 배열에서 필요한 값 추출
            db_name_match = re.search(r"'db_name'\s*=>\s*['\"](.*?)['\"]", content)
            db_user_match = re.search(r"'db_user'\s*=>\s*['\"](.*?)['\"]", content)
            db_pass_match = re.search(r"'db_pass'\s*=>\s*['\"](.*?)['\"]", content)
            db_host_match = re.search(r"'db_host'\s*=>\s*['\"](.*?)['\"]", content)
            
            if not all([db_name_match, db_user_match, db_pass_match]):
                print_error("데이터베이스 설정을 찾을 수 없습니다.")
                sys.exit(1)
                
            db_config = {
                'db_name': db_name_match.group(1),
                'db_user': db_user_match.group(1),
                'db_pass': db_pass_match.group(1),
                'db_host': db_host_match.group(1) if db_host_match else 'localhost'
            }
            
            return db_config
    except Exception as e:
        print_error(f"데이터베이스 설정 로드 중 오류: {str(e)}")
        sys.exit(1)

def initialize_firebase():
    """Firebase 초기화"""
    try:
        cred_path = os.path.join(ROOT_DIR, 'config', 'google', 'service-account.json')
        
        # 이미 초기화된 경우 건너뛰기
        if not firebase_admin._apps:
            cred = credentials.Certificate(cred_path)
            firebase_admin.initialize_app(cred)
            
        return True
    except Exception as e:
        print_error(f"Firebase 초기화 중 오류: {str(e)}")
        sys.exit(1)

def connect_to_database(db_config):
    """데이터베이스 연결"""
    try:
        connection = pymysql.connect(
            host=db_config['db_host'],
            user=db_config['db_user'],
            password=db_config['db_pass'],
            database=db_config['db_name'],
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        return connection
    except Exception as e:
        print_error(f"데이터베이스 연결 중 오류: {str(e)}")
        sys.exit(1)

def get_users_to_delete(connection, phone_number=None):
    """삭제할 사용자 목록 조회"""
    try:
        with connection.cursor() as cursor:
            if phone_number:
                # 전화번호로 특정 사용자 조회
                cursor.execute(
                    "SELECT id, firebase_uid, phone_number, nickname FROM users WHERE phone_number = %s",
                    (phone_number,)
                )
            else:
                # 모든 사용자 조회
                cursor.execute("SELECT id, firebase_uid, phone_number, nickname FROM users")
            
            users = cursor.fetchall()
            return users
    except Exception as e:
        print_error(f"사용자 목록 조회 중 오류: {str(e)}")
        return []

def delete_from_database(connection, user_id):
    """MariaDB에서 사용자 삭제"""
    try:
        with connection.cursor() as cursor:
            cursor.execute("DELETE FROM users WHERE id = %s", (user_id,))
            connection.commit()
            return True
    except Exception as e:
        print_error(f"데이터베이스에서 사용자 삭제 중 오류: {str(e)}")
        return False

def delete_from_firebase_auth(firebase_uid):
    """Firebase Authentication에서 사용자 삭제"""
    try:
        if not firebase_uid:
            return False
            
        auth.delete_user(firebase_uid)
        return True
    except auth.UserNotFoundError:
        print_warning(f"Firebase에서 사용자를 찾을 수 없습니다: {firebase_uid}")
        return False
    except Exception as e:
        print_error(f"Firebase에서 사용자 삭제 중 오류: {str(e)}")
        return False

def main():
    parser = argparse.ArgumentParser(description='테스트 회원 데이터 초기화 스크립트')
    parser.add_argument('--all', action='store_true', help='모든 테스트 사용자 삭제')
    parser.add_argument('--phone', type=str, help='특정 전화번호로 등록된 사용자만 삭제')
    parser.add_argument('--confirm', action='store_true', help='확인 없이 바로 실행')
    
    args = parser.parse_args()
    
    if not args.all and not args.phone:
        print_error("--all 또는 --phone 옵션을 지정해야 합니다.")
        parser.print_help()
        sys.exit(1)
    
    print_header("🧹 TOPMKT 회원가입 데이터 초기화 스크립트")
    
    # 데이터베이스 설정 로드
    db_config = load_db_config()
    print_info(f"데이터베이스: {db_config['db_name']}")
    
    # Firebase 초기화
    print_info("Firebase 초기화 중...")
    initialize_firebase()
    print_success("Firebase 초기화 완료")
    
    # 데이터베이스 연결
    print_info("데이터베이스 연결 중...")
    connection = connect_to_database(db_config)
    print_success("데이터베이스 연결 완료")
    
    # 삭제할 사용자 목록 조회
    users = get_users_to_delete(connection, args.phone)
    
    if not users:
        print_warning("삭제할 사용자가 없습니다.")
        sys.exit(0)
    
    print_info(f"{len(users)}명의 사용자를 찾았습니다.")
    
    # 사용자 목록 출력
    print("\n" + Colors.YELLOW + "삭제 대상 사용자 목록:" + Colors.END)
    print(f"{'ID':<36} | {'Firebase UID':<36} | {'전화번호':<15} | {'닉네임'}")
    print("-" * 100)
    
    for user in users:
        print(f"{user['id']:<36} | {user['firebase_uid'] or 'N/A':<36} | {user['phone_number']:<15} | {user['nickname']}")
    
    # 확인 메시지
    if not args.confirm:
        confirmation = input(Colors.RED + "\n위 사용자들의 데이터를 삭제하시겠습니까? (y/N): " + Colors.END)
        if confirmation.lower() != 'y':
            print_info("작업이 취소되었습니다.")
            sys.exit(0)
    
    # 사용자 삭제
    success_count = 0
    fail_count = 0
    
    print_header("🚮 사용자 데이터 삭제 중...")
    
    for user in users:
        print(f"\n{Colors.BOLD}사용자 '{user['nickname']}' ({user['phone_number']}) 삭제 중...{Colors.END}")
        
        # Firebase Authentication에서 사용자 삭제
        if user['firebase_uid']:
            print_info(f"Firebase Authentication에서 사용자 삭제 중... (UID: {user['firebase_uid']})")
            if delete_from_firebase_auth(user['firebase_uid']):
                print_success("Firebase Authentication에서 사용자 삭제 완료")
            else:
                print_warning("Firebase Authentication에서 사용자 삭제 실패")
        
        # 데이터베이스에서 사용자 삭제
        print_info("데이터베이스에서 사용자 삭제 중...")
        if delete_from_database(connection, user['id']):
            print_success("데이터베이스에서 사용자 삭제 완료")
            success_count += 1
        else:
            print_error("데이터베이스에서 사용자 삭제 실패")
            fail_count += 1
    
    # 결과 출력
    print_header("🏁 삭제 작업 완료")
    print_success(f"성공: {success_count}명")
    
    if fail_count > 0:
        print_error(f"실패: {fail_count}명")
    
    # 연결 종료
    connection.close()
    print_info("데이터베이스 연결 종료")
    
    print("\n" + Colors.GREEN + "회원가입 데이터 초기화 작업이 완료되었습니다." + Colors.END)

if __name__ == "__main__":
    main() 