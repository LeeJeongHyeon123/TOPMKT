# 86번 강의 강사 3명으로 업데이트 가이드

## 🎯 목표
86번 강의의 강사를 1명에서 3명으로 변경하여 각각의 전문 이미지와 상세한 소개가 표시되도록 합니다.

## 📋 새로운 강사 구성

### 1. 김마케팅 (디지털 마케팅 전문가)
- **경력**: 10년
- **전문분야**: 구글 애즈, 네이버 광고, 페이스북 마케팅
- **이미지**: `/assets/uploads/instructors/instructor-kim.jpg`

### 2. 박소셜 (SNS 마케팅 전문가)  
- **경력**: 8년
- **전문분야**: 바이럴 캠페인, 브랜드 스토리텔링
- **이미지**: `/assets/uploads/instructors/instructor-park.jpg`

### 3. 이데이터 (빅데이터 분석 전문가)
- **경력**: 6년
- **전문분야**: 개인화 마케팅, AI 마케팅 도구
- **이미지**: `/assets/uploads/instructors/instructor-lee.jpg`

## 🚀 업데이트 방법

### 방법 1: 웹 인터페이스 사용 (추천)
```
브라우저에서 접속: https://www.topmktx.com/quick_update_86.php
→ "강사 3명으로 업데이트하기" 버튼 클릭
→ 완료 후 https://www.topmktx.com/lectures/86 에서 확인
```

### 방법 2: 직접 SQL 실행
```sql
UPDATE lectures SET 
    instructor_name = '김마케팅, 박소셜, 이데이터',
    instructor_info = '김마케팅은 10년 경력의 디지털 마케팅 전문가로, 다수 기업의 온라인 마케팅 전략 수립 및 브랜드 성장을 이끌어왔습니다. 구글 애즈, 네이버 광고, 페이스북 마케팅 전문가로 ROI 극대화에 탁월한 능력을 보유하고 있습니다.|||박소셜은 8년 경력의 SNS 마케팅 및 인플루언서 마케팅 전문가입니다. 바이럴 캠페인 기획과 브랜드 스토리텔링 분야에서 뛰어난 성과를 거두었으며, 젊은 세대와의 소통에 특화된 마케팅 전략을 구사합니다.|||이데이터는 6년 경력의 빅데이터 분석 및 마케팅 인사이트 전문가입니다. 고객 데이터 분석을 통한 개인화 마케팅과 AI 마케팅 도구 활용에 능숙하며, 데이터 기반 의사결정을 통해 마케팅 효율성을 극대화하는 전문가입니다.'
WHERE id = 86;
```

## 📁 이미지 파일
다음 이미지들이 준비되어 있습니다:
- `/workspace/public/assets/uploads/instructors/instructor-kim.jpg`
- `/workspace/public/assets/uploads/instructors/instructor-park.jpg` 
- `/workspace/public/assets/uploads/instructors/instructor-lee.jpg`

## 💻 코드 개선사항
- 강사 이름별 이미지 매칭 로직 구현
- 여러 강사 정보 파싱 (쉼표로 구분된 이름, `|||`로 구분된 소개)
- 각 강사별 전문 분야 자동 표시
- 반응형 카드 레이아웃

## ✅ 예상 결과
업데이트 후 https://www.topmktx.com/lectures/86 페이지에서:
1. 3명의 강사가 각각의 사진과 함께 카드 형태로 표시
2. 각 강사의 전문 분야와 상세한 경력 정보 표시
3. 모바일에서도 최적화된 레이아웃으로 표시