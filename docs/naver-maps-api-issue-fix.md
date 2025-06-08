# 네이버 지도 API 인증 문제 해결 가이드

**작성일**: 2025-06-08
**상태**: 문제 진단 및 임시 해결 완료

## 🚨 발견된 문제

### 1. 네이버 지도 API 인증 실패
```
NAVER Maps JavaScript API v3 네이버 지도 Open API 인증이 실패하였습니다. 
클라이언트 아이디와 웹 서비스 URL을 확인해 주세요.
* Error Code / Error Message: 200 / Authentication Failed
* Client ID: c5yj6m062z
* URI: https://www.topmktx.com/lectures/86
```

### 2. CSP 정책 차단
- `https://kr-col-ext.nelo.navercorp.com` (네이버 로그 수집)
- `https://naveropenapi.apigw.ntruss.com` (Geocoding API)

### 3. Geocoding 서비스 접근 불가
- 주소 검색 기능이 CSP 정책으로 차단됨
- 네이버 클라우드 플랫폼 인증 오류

## ✅ 적용된 해결 방안

### 1. CSP 정책 업데이트
**파일**: `/workspace/public/.htaccess`

추가된 도메인:
- `https://kr-col-ext.nelo.navercorp.com` (네이버 로깅)
- `https://naveropenapi.apigw.ntruss.com` (Geocoding API)

### 2. 안정적인 지도 구현으로 변경
- Geocoding 기능 임시 비활성화
- 기본 위치 마커 표시
- 네이버 지도 웹사이트 외부 링크 제공

### 3. 사용자 경험 개선
- 인증 오류에도 지도 표시
- 외부 길찾기 링크 제공
- 정보창에 실용적인 정보 표시

## 🔧 현재 구현 상태

### 작동하는 기능
- ✅ 네이버 지도 기본 표시
- ✅ 커스텀 마커 표시
- ✅ 정보창 인터랙션
- ✅ 네이버 지도 외부 링크
- ✅ 로딩 애니메이션

### 제한된 기능
- ⚠️ 자동 주소 검색 (Geocoding) - 인증 문제로 비활성화
- ⚠️ 정확한 위치 표시 - 기본 위치 사용

## 🔄 필요한 추가 작업

### 1. 네이버 클라우드 플랫폼 설정 확인
1. **도메인 등록 확인**
   - `topmktx.com` 등록 상태 확인
   - `www.topmktx.com` 등록 상태 확인
   
2. **API 서비스 활성화 확인**
   - Maps API 서비스 상태
   - Geocoding API 서비스 상태

3. **클라이언트 ID 권한 확인**
   - 웹사이트 도메인 매칭
   - API 사용 권한 설정

### 2. 대안 해결책

#### A. 도메인 설정 수정
네이버 클라우드 플랫폼에서:
1. Console > AI·Application Service > Maps
2. 등록된 도메인 확인 및 수정
3. `https://www.topmktx.com` 정확히 등록

#### B. API 키 재발급
- 현재 키 삭제 후 새로 발급
- 도메인 설정을 정확히 하고 재발급

#### C. 정적 지도 대안 사용
```html
<!-- 정적 지도 이미지 사용 -->
<img src="https://naveropenapi.apigw.ntruss.com/map-static/v2/raster?w=300&h=200&center=127.0276,37.4979&level=16&X-NCP-APIGW-API-KEY-ID=클라이언트ID" alt="지도">
```

## 📋 현재 사용자 경험

1. **지도 표시**: 기본 위치에 네이버 지도 표시
2. **마커 정보**: 강의 장소 정보와 외부 링크 제공
3. **길찾기**: 네이버 지도 웹사이트로 직접 연결
4. **안정성**: 인증 오류에도 서비스 중단 없음

## 🎯 권장 해결 순서

1. **즉시 (임시)**: 현재 구현 유지 - 기본 기능 제공
2. **단기 (1-2일)**: 네이버 클라우드 플랫폼 도메인 설정 수정
3. **중기 (1주일)**: API 키 재발급 및 Geocoding 기능 복구
4. **장기**: 백업 지도 서비스 (카카오맵) 구현

---

**현재 상태**: 기본 지도 서비스 제공 중, 사용자 불편 최소화
**우선순위**: 네이버 클라우드 플랫폼 도메인 설정 확인 및 수정

---

**최종 업데이트**: 2025-06-08
**담당자**: Claude Code Assistant