import firebase_admin
from firebase_admin import credentials, auth

# Firebase 초기화
cred = credentials.Certificate('config/google/service-account.json')
firebase_admin.initialize_app(cred)

try:
    # 모든 사용자 목록 조회
    users = auth.list_users()
    
    if users.users:
        print("등록된 사용자 목록:")
        for user in users.users:
            print(f"UID: {user.uid}")
            print(f"이메일: {user.email}")
            print(f"전화번호: {user.phone_number}")
            print(f"생성일: {user.user_metadata.creation_timestamp}")
            print("-------------------")
    else:
        print("등록된 사용자가 없습니다.")
        
except Exception as e:
    print(f"오류 발생: {str(e)}") 