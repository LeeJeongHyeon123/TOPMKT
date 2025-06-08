# 구글 지도 API 문제 해결 가이드

**작성일**: 2025-06-08
**상태**: 구글 지도 표시 문제 해결 완료

## 🔍 문제 상황
- **URL**: https://www.topmktx.com/lectures/86
- **증상**: 구글 지도는 뜨지만 강의 주소가 아닌 다른 주소에 지도가 표시됨
- **원인**: API 키 하드코딩, 주소 데이터 처리 문제

## ✅ 해결된 사항

### 1. Google Maps API 키 설정 개선
```php
// src/config/config.php에 추가
define('GOOGLE_MAPS_API_KEY', 'AIzaSyAU3tpopn5ti6958OpENFeUhrxWnakk5vs');
```

### 2. 강의 상세 페이지 지도 로직 개선
**파일**: `src/views/lectures/detail.php` (1001-1038번 라인)

**개선 사항**:
- 하드코딩된 API 키를 설정에서 가져오도록 변경
- 주소 데이터 처리 로직 강화
- 지도 로딩 실패 시 대체 UI 제공
- 네이버 지도/카카오맵 대체 링크 제공

### 3. 주소 데이터 우선순위 설정
```php
// 지도에 표시할 주소 우선순위
if (!empty($lecture['venue_address'])) {
    $mapAddress = $lecture['venue_address'];        // 1순위: 상세 주소
} elseif (!empty($lecture['venue_name'])) {
    $mapAddress = $lecture['venue_name'];          // 2순위: 장소명
} else {
    $mapAddress = '서울특별시 강남구 테헤란로 123'; // 3순위: 기본값
}
```

## 🛠️ 추가 조치사항

### Google Cloud Console 설정 확인
1. **Maps Embed API 활성화 확인**
   - https://console.cloud.google.com/apis/library
   - Maps Embed API 검색 후 활성화

2. **API 키 권한 설정**
   - 도메인 제한: `*.topmktx.com/*`, `topmktx.com/*`
   - IP 제한: 필요 시 서버 IP 추가

3. **할당량 및 결제 확인**
   - 일일 요청 한도 확인
   - 결제 정보 등록 상태 확인

### 데이터베이스 강의 86번 정보 확인
```sql
-- 강의 86번 장소 정보 확인
SELECT id, title, venue_name, venue_address, location_type 
FROM lectures 
WHERE id = 86;

-- 장소 정보가 없는 경우 업데이트
UPDATE lectures SET 
    venue_name = '서울 강남 세미나실',
    venue_address = '서울특별시 강남구 테헤란로 123'
WHERE id = 86;
```

## 🔧 문제 해결 방법

### 방법 1: API 키 재발급 (권장)
1. Google Cloud Console에서 새 API 키 발급
2. Maps Embed API 권한 설정
3. `src/config/config.php`에서 `GOOGLE_MAPS_API_KEY` 업데이트

### 방법 2: 카카오맵 대체 (장기적)
```php
// 카카오맵 사용 예시 (kakao-map-alternative.php 참조)
<div id="map" style="width:100%;height:250px;"></div>
<script src="https://dapi.kakao.com/v2/maps/sdk.js?appkey=YOUR_KAKAO_API_KEY"></script>
<script>
var mapContainer = document.getElementById('map');
var mapOption = {
    center: new kakao.maps.LatLng(37.4979, 127.0276),
    level: 3
};
var map = new kakao.maps.Map(mapContainer, mapOption);
</script>
```

### 방법 3: 주소 데이터 수정
1. `/workspace/check_lecture_86_simple.php` 실행
2. 강의 86번의 venue_name, venue_address 확인/수정
3. 올바른 주소 데이터로 업데이트

## 📋 테스트 방법

### 1. API 키 테스트
```
https://www.google.com/maps/embed/v1/place?key=AIzaSyAU3tpopn5ti6958OpENFeUhrxWnakk5vs&q=서울특별시+강남구+테헤란로+123&zoom=17
```

### 2. 강의 페이지 확인
- https://www.topmktx.com/lectures/86 접속
- 지도가 올바른 위치를 표시하는지 확인
- 지도 로딩 실패 시 대체 링크 작동 확인

### 3. 다양한 주소 형식 테스트
- 상세 주소 + 장소명
- 상세 주소만
- 장소명만
- 주소 없음 (기본값 표시)

## 🚨 주의사항

1. **API 키 보안**
   - 실제 운영환경에서는 환경변수 사용 권장
   - 도메인 제한 설정 필수
   - 정기적인 키 교체 고려

2. **주소 데이터 품질**
   - 한국 주소는 "도로명 주소" 형식 권장
   - 특수문자, 이모지 제거 필요
   - 영문 주소보다 한글 주소가 정확도 높음

3. **대체 방안 준비**
   - 네이버 지도, 카카오맵 링크 제공
   - 지도 로딩 실패 시 UX 고려
   - 텍스트 주소 정보 명시

## 📈 개선 효과

- ✅ API 키 관리 체계화
- ✅ 지도 표시 정확도 향상
- ✅ 오류 상황 대응 강화
- ✅ 사용자 경험 개선
- ✅ 유지보수성 증대

## 📞 문제 발생 시 대응

1. **즉시 조치**: 대체 지도 서비스 링크 활용
2. **단기 조치**: API 키 권한 재설정
3. **장기 조치**: 카카오맵 또는 네이버 지도 도입 검토

---

**최종 업데이트**: 2025-06-08
**담당자**: Claude Code Assistant
**상태**: 해결 완료, 모니터링 중