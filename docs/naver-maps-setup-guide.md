# 네이버 지도 API 설정 가이드

**작성일**: 2025-06-08
**상태**: 네이버 클라우드 플랫폼 Maps API (NCP) 정식 구현 완료

## 🎯 전환 이유

- **한국 지역 정확도 높음**: 네이버 지도는 한국 주소 데이터에 최적화
- **안정적인 서비스**: 국내 서비스로 안정성과 지속성 보장
- **비용 효율성**: 더 합리적인 API 요금 체계
- **사용자 친화성**: 한국 사용자들에게 익숙한 지도 서비스

## 🔧 설치 및 설정

### 1. 네이버 클라우드 플랫폼 설정

1. **네이버 클라우드 플랫폼 가입**
   - https://www.ncloud.com/ 접속
   - 회원가입 및 본인인증 완료

2. **Maps API 신청**
   - Console > AI·Application Service > Maps 선택
   - Web Dynamic Map API 신청
   - 도메인 등록: `topmktx.com`, `www.topmktx.com`

3. **클라이언트 ID 발급**
   - API 신청 승인 후 클라이언트 ID 발급
   - 클라이언트 시크릿도 함께 발급됨

### 2. 프로젝트 설정 파일 업데이트

**파일**: `src/config/config.php`

```php
// 네이버 클라우드 플랫폼 Maps API 설정 (최신)
define('NAVER_MAPS_CLIENT_ID', 'c5yj6m062z'); // 네이버 클라우드 플랫폼에서 발급받은 실제 클라이언트 ID
define('NAVER_MAPS_CLIENT_SECRET', 'ifjGgFsON2vMO2DiIFW1QLRBnEQ7l1j4w5CciajG'); // 클라이언트 시크릿
```

**중요**: 네이버 클라우드 플랫폼에서 2025년 6월 8일 발급받은 최신 API 키 적용 완료

### 3. 구현된 기능

#### ✅ 완료된 구현사항

1. **강의 상세 페이지 지도 표시**
   - 파일: `src/views/lectures/detail.php` (999-1280번 라인)
   - 네이버 지도 API v3 사용
   - 주소 자동 geocoding
   - 커스텀 마커 및 향상된 정보창 표시
   - 로딩 애니메이션 및 부드러운 전환 효과

2. **주소 처리 로직**
   ```php
   // 주소 우선순위
   if (!empty($lecture['venue_address'])) {
       $mapAddress = $lecture['venue_address'];        // 1순위: 상세 주소
   } elseif (!empty($lecture['venue_name'])) {
       $mapAddress = $lecture['venue_name'];          // 2순위: 장소명
   } else {
       $mapAddress = '서울특별시 강남구 테헤란로 123'; // 3순위: 기본값
   }
   ```

3. **오류 처리 및 대체 방안**
   - API 로딩 실패 시 대체 메시지 표시
   - 네이버 지도 웹사이트 링크 제공
   - 주소 검색 실패 시 기본 위치 표시

#### 🔍 주요 기능 상세

1. **네이버 지도 컨테이너**
   ```html
   <!-- 로딩 인디케이터와 지도 컨테이너 -->
   <div style="position: relative;">
       <div id="mapLoading-{lecture_id}">로딩 애니메이션</div>
       <div id="naverMap-{lecture_id}" style="width:100%; height:250px;"></div>
   </div>
   ```

2. **API 스크립트 로딩 (네이버 클라우드 플랫폼 NCP 정식)**
   ```javascript
   <script src="https://oapi.map.naver.com/openapi/v3/maps.js?ncpKeyId={CLIENT_ID}&callback=initNaverMap"></script>
   ```
   
   **주요 변경사항**: 
   - `ncpClientId` → `ncpKeyId` (NCP 파라미터명 변경)
   - `callback=initNaverMap` 추가 (비동기 로딩)
   - 구버전 AI NAVER API에서 신버전 NCP Maps API로 완전 전환

3. **지도 초기화 및 마커 표시**
   - 주소 → 좌표 변환 (Geocoding)
   - 커스텀 마커 아이콘 (그라디언트 + 이모지)
   - 향상된 정보창 (길찾기 링크 포함)
   - 로딩 완료 시 부드러운 페이드인 효과
   - 클릭 이벤트 처리

4. **UX/UI 개선사항**
   - 🎯 **커스텀 마커**: 그라디언트 배경 + 이모지 아이콘
   - 📍 **정확한 위치**: 초록색 마커 (📍)
   - 🏢 **참고 위치**: 주황색 마커 (🏢)
   - 💫 **로딩 애니메이션**: 스피너 + 페이드 전환
   - 🔗 **길찾기 링크**: 정보창에서 네이버 지도 바로 연결

4. **스타일링**
   ```css
   .naver-map-container {
       margin-top: 15px;
       border-radius: 8px;
       box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
       border: 1px solid #e2e8f0;
   }
   ```

## 📋 API 사용량 및 요금

### 무료 사용량
- **Web Dynamic Map API**: 월 300,000회 무료
- **Geocoding API**: 월 300,000회 무료
- **Static Map API**: 월 300,000회 무료

### 요금제 (무료 한도 초과 시)
- Web Dynamic Map: 1,000회당 1,000원
- Geocoding: 1,000회당 300원
- Static Map: 1,000회당 300원

## 🚀 구현 예시

### 기본 지도 표시
```javascript
var mapOptions = {
    center: new naver.maps.LatLng(37.4979, 127.0276), // 강남구 테헤란로
    zoom: 17,
    mapTypeControl: true,
    zoomControl: true
};

var map = new naver.maps.Map('map-container', mapOptions);
```

### 주소로 마커 표시
```javascript
naver.maps.Service.geocode({
    query: '서울특별시 강남구 테헤란로 123'
}, function(status, response) {
    if (status === naver.maps.Service.Status.OK) {
        var item = response.v2.addresses[0];
        var point = new naver.maps.LatLng(item.y, item.x);
        
        var marker = new naver.maps.Marker({
            position: point,
            map: map
        });
    }
});
```

## 🔄 구글 지도에서 마이그레이션된 내용

### 제거된 구글 지도 관련 코드
- Google Maps Embed API iframe
- Google Maps API 키 하드코딩
- `google-map-container` CSS 클래스

### 새로 추가된 네이버 지도 코드
- 네이버 Maps API v3 JavaScript 구현
- `naver-map-container` CSS 클래스
- 동적 지도 생성 및 마커 표시
- 주소 검색 및 좌표 변환

## 🛠️ 설정 방법

### 1. 네이버 클라우드 플랫폼에서 클라이언트 ID 발급
1. https://console.ncloud.com/ 로그인
2. AI·Application Service > Maps 선택
3. Web Dynamic Map 서비스 신청
4. 도메인 정보 등록: `www.topmktx.com`
5. 클라이언트 ID 복사

### 2. 설정 파일 업데이트
```php
// src/config/config.php 수정
define('NAVER_MAPS_CLIENT_ID', '발급받은_실제_클라이언트_ID');
define('NAVER_MAPS_CLIENT_SECRET', '발급받은_실제_시크릿');
```

### 3. 테스트 확인
- https://www.topmktx.com/lectures/86 접속
- 네이버 지도가 정상 표시되는지 확인
- 마커 클릭 시 정보창 표시 확인

## 📱 모바일 대응

네이버 지도 API는 자동으로 모바일 환경에 최적화됩니다:
- 터치 제스처 지원 (확대/축소, 이동)
- 반응형 레이아웃 자동 적용
- 모바일 브라우저 호환성 보장

## 🚨 주의사항

1. **클라이언트 ID 보안**
   - 실제 운영환경에서는 도메인 제한 설정 필수
   - 클라이언트 시크릿은 서버 사이드에서만 사용

2. **API 호출 최적화**
   - 같은 주소에 대한 중복 geocoding 방지
   - 캐싱 구현 고려

3. **브라우저 호환성**
   - IE 9 이상 지원
   - 모던 브라우저에서 최적 성능

## 🔍 문제 해결

### 지도가 표시되지 않는 경우
1. 클라이언트 ID 확인
2. 도메인 등록 상태 확인
3. 브라우저 콘솔 오류 메시지 확인
4. API 사용량 한도 확인

### 주소 검색이 되지 않는 경우
1. 주소 형식 확인 (도로명 주소 권장)
2. 특수문자 제거
3. 검색어 길이 확인

## 📈 성능 최적화

1. **지연 로딩**: 페이지 로딩 후 지도 API 스크립트 로드
2. **오류 처리**: API 로딩 실패 시 대체 UI 제공
3. **캐싱**: 좌표 정보 로컬 스토리지 캐싱

---

## ✅ 네이버 클라우드 플랫폼 Maps API 구현 완료

### 🎉 성공적으로 해결된 문제들
1. **✅ 네이버 클라우드 플랫폼 최신 API 적용**: `oapi.map.naver.com` 도메인 사용
2. **✅ 2025년 6월 8일 발급 API 키 적용**: 클라이언트 ID `c5yj6m062z` 활성화
3. **✅ CSP 정책 완전 해결**: 모든 네이버 지도 관련 도메인 허용

### 🚀 구현된 최신 기능들
- ✅ **네이버 클라우드 플랫폼 Maps API v3** 최신 버전 사용
- ✅ **강화된 오류 처리**: API 로딩 실패 시 자동 대체 메시지
- ✅ **지능형 Geocoding**: 주소 검색 성공 시 정확한 위치 표시
- ✅ **이중 마커 시스템**: 정확한 위치(📍) vs 참고 위치(🏢)
- ✅ **향상된 정보창**: 길찾기 링크 및 외부 지도 연동
- ✅ **부드러운 로딩 애니메이션**: 페이드인/아웃 효과

### 📍 현재 활성화된 기능
1. **실시간 지도 표시**: https://www.topmktx.com/lectures/86
2. **주소 자동 검색**: 강의 주소 기반 정확한 위치 표시
3. **인터랙티브 마커**: 클릭 시 정보창 토글
4. **외부 연동**: 네이버 지도 웹사이트 직접 연결

### 🔧 기술 세부사항
- **API 도메인**: `oapi.map.naver.com` (네이버 클라우드 플랫폼 NCP 전용)
- **파라미터**: `ncpKeyId` (기존 `ncpClientId`에서 변경)
- **클라이언트 ID**: `c5yj6m062z` (2025-06-08 발급)
- **로딩 방식**: 콜백 기반 비동기 로딩
- **CSP 정책**: 모든 네이버 지도 도메인 허용 완료

---

## 🎯 최종 마이그레이션 상황

🎉 **구글 지도 → 네이버 클라우드 플랫폼 (NCP) Maps API 전환 100% 완료**
- ✅ 구글 지도 완전 제거
- ✅ 구버전 AI NAVER API → 신버전 NCP Maps API 전환 완료
- ✅ `ncpKeyId` 파라미터 및 콜백 기반 로딩 적용
- ✅ 2025년 6월 8일 발급 최신 API 키 적용
- ✅ 2025년 4월 17일 이후 신규 차단 이슈 해결
- ✅ 모든 CSP 정책 및 보안 이슈 해결

**현재 상태**: 네이버 클라우드 플랫폼 Maps API 정상 서비스 제공 중 ✨

---

**최종 업데이트**: 2025-06-08
**담당자**: Claude Code Assistant
**상태**: 네이버 클라우드 플랫폼 Maps API 완전 구현 완료, 정상 운영 중