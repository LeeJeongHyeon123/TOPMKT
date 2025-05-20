import firebase_admin
from firebase_admin import credentials, firestore

# Firebase 초기화
cred = credentials.Certificate('config/google/service-account.json')
firebase_admin.initialize_app(cred)

try:
    # Firestore 클라이언트 초기화
    db = firestore.client()
    
    # users 컬렉션의 모든 문서 조회
    users_ref = db.collection('users')
    users = users_ref.get()
    
    if users:
        print("Firestore 사용자 데이터:")
        for user in users:
            print(f"문서 ID: {user.id}")
            print(f"데이터: {user.to_dict()}")
            print("-------------------")
    else:
        print("Firestore에 사용자 데이터가 없습니다.")
        
except Exception as e:
    print(f"오류 발생: {str(e)}") 