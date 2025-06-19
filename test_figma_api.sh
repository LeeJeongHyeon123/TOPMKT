#!/bin/bash

echo "=== 피그마 API 테스트 시작 ==="
echo "현재 시간: $(date)"
echo "현재 디렉토리: $(pwd)"
echo "curl 버전: $(curl --version | head -1)"
echo ""

echo "=== 테스트 1: HTTP/2 기본 ==="
curl -k -X GET "https://api.figma.com/v1/files/XHCLRt7FpQhgOdmwGLU7Fy" \
  -H "X-Figma-Token: figd_MTtixA5s0UPLflqDcEnhvC2towXtFXJeSmAhx9zm" \
  -w "\n응답코드: %{http_code}\n연결시간: %{time_connect}s\n총시간: %{time_total}s\n" \
  -o /tmp/figma_response1.json 2>/tmp/figma_error1.log

echo "응답 내용 (처음 200자):"
head -c 200 /tmp/figma_response1.json
echo ""
echo "에러 로그:"
cat /tmp/figma_error1.log
echo ""

echo "=== 테스트 2: HTTP/1.1 강제 ==="
curl -k --http1.1 -X GET "https://api.figma.com/v1/files/XHCLRt7FpQhgOdmwGLU7Fy" \
  -H "X-Figma-Token: figd_MTtixA5s0UPLflqDcEnhvC2towXtFXJeSmAhx9zm" \
  -w "\n응답코드: %{http_code}\n연결시간: %{time_connect}s\n총시간: %{time_total}s\n" \
  -o /tmp/figma_response2.json 2>/tmp/figma_error2.log

echo "응답 내용 (처음 200자):"
head -c 200 /tmp/figma_response2.json
echo ""
echo "에러 로그:"
cat /tmp/figma_error2.log
echo ""

echo "=== 테스트 3: 간단한 GET ==="
curl -s "https://api.figma.com/v1/files/XHCLRt7FpQhgOdmwGLU7Fy" \
  -H "X-Figma-Token: figd_MTtixA5s0UPLflqDcEnhvC2towXtFXJeSmAhx9zm" \
  -o /tmp/figma_response3.json

echo "응답 내용 (처음 200자):"
head -c 200 /tmp/figma_response3.json
echo ""

echo "=== 파일 크기 비교 ==="
ls -la /tmp/figma_response*.json

echo "=== 테스트 완료 ==="