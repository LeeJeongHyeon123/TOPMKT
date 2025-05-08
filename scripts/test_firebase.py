import firebase_admin
from firebase_admin import credentials

try:
    # Firebase Admin SDK 초기화 시도
    cred = credentials.Certificate('config/google/service-account.json')
    firebase_admin.initialize_app(cred)
    print("Firebase Admin SDK가 성공적으로 설치되었습니다!")
except Exception as e:
    print(f"오류 발생: {str(e)}") 