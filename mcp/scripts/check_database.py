import firebase_admin
from firebase_admin import credentials, db
import json

# Firebase 초기화
cred = credentials.Certificate('config/google/service-account.json')
firebase_admin.initialize_app(cred, {
    'databaseURL': 'https://topmkt-832f2-default-rtdb.firebaseio.com'
})

try:
    # users 노드의 데이터 조회
    ref = db.reference('users')
    users_data = ref.get()
    
    if users_data:
        print("Realtime Database 사용자 데이터:")
        print(json.dumps(users_data, indent=2, ensure_ascii=False))
    else:
        print("Realtime Database에 사용자 데이터가 없습니다.")
        
except Exception as e:
    print(f"오류 발생: {str(e)}") 