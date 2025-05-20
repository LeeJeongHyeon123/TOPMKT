import firebase_admin
from firebase_admin import credentials, auth, db
import json
import os

# 서비스 계정 키 파일 경로
cred = credentials.Certificate('config/google/service-account.json')

# Firebase 초기화
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://topmkt-832f2-default-rtdb.firebaseio.com'
})

# 삭제할 사용자의 전화번호
phone_number = '+821012341234'  # 우리집탄이의 전화번호

try:
    # 전화번호로 사용자 검색
    user = auth.get_user_by_phone_number(phone_number)
    
    if user:
        # 사용자 삭제
        auth.delete_user(user.uid)
        print(f"사용자 {user.uid}가 성공적으로 삭제되었습니다.")
        
        # 추가 데이터가 있다면 삭제
        ref = db.reference(f'users/{user.uid}')
        ref.delete()
        print("사용자 데이터가 성공적으로 삭제되었습니다.")
    else:
        print("해당 전화번호로 등록된 사용자가 없습니다.")

except Exception as e:
    print(f"오류 발생: {str(e)}") 