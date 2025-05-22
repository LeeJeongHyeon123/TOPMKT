#!/usr/bin/env python3
import firebase_admin
from firebase_admin import credentials, auth
import sys
import os

def delete_firebase_user(uid):
    try:
        # Firebase 프로젝트 인증 정보 로드
        cred = credentials.Certificate('/var/www/html/topmkt/config/firebase/firebase-credentials.json')
        
        # Firebase 초기화
        try:
            firebase_admin.initialize_app(cred)
        except ValueError:
            # 이미 초기화된 경우 무시
            pass
        
        # 사용자 삭제
        auth.delete_user(uid)
        print(f"Firebase 사용자 삭제 성공: {uid}")
        return True
        
    except auth.UserNotFoundError:
        print(f"사용자를 찾을 수 없음: {uid}")
        return False
    except Exception as e:
        print(f"오류 발생: {str(e)}")
        return False

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("사용법: python3 delete_firebase_user.py <firebase_uid>")
        sys.exit(1)
        
    uid = sys.argv[1]
    delete_firebase_user(uid) 