<?php
/**
 * Firebase Authentication 샘플 테스트 스크립트
 * 
 * 기본정책에 명시된 테스트용 전화번호를 사용하여 
 * Firebase Authentication 서비스가 제대로 작동하는지 확인합니다.
 */

// 부트스트랩 로드
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/app/bootstrap.php';

use App\Services\Firebase\AuthService;

echo "Firebase Authentication 샘플 테스트를 시작합니다...\n\n";

// 테스트 결과 상태
$success = true;

// 1. Firebase Auth 서비스 초기화
try {
    echo "1. Firebase Auth 서비스 초기화 중...\n";
    $authService = AuthService::getInstance();
    echo "   - Firebase Auth 서비스 초기화 성공\n";
} catch (\Exception $e) {
    echo "   - Firebase Auth 서비스 초기화 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 테스트를 계속할 수 있는지 확인
if (!$success) {
    echo "\n❌ Firebase Auth 서비스 초기화에 실패했습니다. 테스트를 중단합니다.\n";
    exit(1);
}

// 2. 테스트용 전화번호 사용
try {
    echo "\n2. 테스트용 전화번호 사용 테스트 중...\n";
    
    // 기본정책에 명시된 테스트용 전화번호
    $testPhoneNumber = '+821026591346';
    $testCode = '123456';
    
    echo "   - 테스트용 전화번호: {$testPhoneNumber}\n";
    echo "   - 테스트용 인증코드: {$testCode}\n";
    
    // 전송 가능 여부 확인
    $canSend = $authService->canSendVerificationCode($testPhoneNumber);
    echo "   - 인증번호 전송 가능 여부: " . ($canSend['allowed'] ? "가능" : "불가능") . "\n";
    echo "   - 메시지: {$canSend['message']}\n";
    
    if ($canSend['allowed']) {
        // 테스트용 인증 시도 기록
        $authService->logAuthAttempt($testPhoneNumber, true, 'send');
        echo "   - 인증번호 전송 기록 저장 성공\n";
        
        // 실제 Firebase에서는 여기서 SMS가 전송됨
        echo "   - 참고: 실제 환경에서는 Firebase가 SMS를 전송합니다\n";
        
        // 실제 인증 과정 시뮬레이션
        echo "   - 인증 과정 시뮬레이션 시작...\n";
        echo "   - 사용자가 인증코드 '{$testCode}'를 입력했다고 가정\n";
        
        // 인증 성공 기록
        $authService->logAuthAttempt($testPhoneNumber, true, 'verify');
        echo "   - 인증 성공 기록 저장 완료\n";
    }
} catch (\Exception $e) {
    echo "   - 테스트용 전화번호 사용 테스트 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 3. 보이지 않는 reCAPTCHA 설정 안내
echo "\n3. 보이지 않는 reCAPTCHA 설정 안내\n";
echo "   - 기본정책에 따라 스팸 방지를 위해 '보이지 않는 reCAPTCHA'를 사용해야 합니다.\n";
echo "   - Firebase 콘솔에서 설정 방법:\n";
echo "     1) Firebase 콘솔 > Authentication > Sign-in method > 전화번호 섹션\n";
echo "     2) reCAPTCHA 설정에서 '보이지 않는 reCAPTCHA'(invisible reCAPTCHA) 선택\n";
echo "   - 클라이언트 측 구현 방법:\n";
echo "```javascript\n";
echo "// 보이지 않는 reCAPTCHA 설정\n";
echo "window.recaptchaVerifier = new RecaptchaVerifier('send-code-button', {\n";
echo "  'size': 'invisible', // 'normal' 대신 'invisible' 사용\n";
echo "  'callback': (response) => {\n";
echo "    // reCAPTCHA 인증 성공 시 자동으로 인증번호 전송 프로세스 시작\n";
echo "    sendVerificationCode();\n";
echo "  }\n";
echo "}, auth);\n";
echo "\n";
echo "// 인증번호 전송 함수\n";
echo "function sendVerificationCode() {\n";
echo "  const phoneNumber = document.getElementById('phone-number').value;\n";
echo "  \n";
echo "  // 서버에 인증 시도 가능 여부 확인 요청\n";
echo "  fetch('/api/auth/can-send-verification', {\n";
echo "    method: 'POST',\n";
echo "    headers: { 'Content-Type': 'application/json' },\n";
echo "    body: JSON.stringify({ phone: phoneNumber })\n";
echo "  })\n";
echo "  .then(response => response.json())\n";
echo "  .then(data => {\n";
echo "    if (!data.allowed) {\n";
echo "      alert(data.message);\n";
echo "      return;\n";
echo "    }\n";
echo "    \n";
echo "    // Firebase에 인증번호 전송 요청\n";
echo "    signInWithPhoneNumber(auth, phoneNumber, window.recaptchaVerifier)\n";
echo "      .then((confirmationResult) => {\n";
echo "        // SMS 전송 성공\n";
echo "        window.confirmationResult = confirmationResult;\n";
echo "        // UI 업데이트\n";
echo "      }).catch((error) => {\n";
echo "        // 에러 처리\n";
echo "      });\n";
echo "  });\n";
echo "}\n";
echo "```\n";

// 테스트 결과 출력
echo "\n==================================================\n";
if ($success) {
    echo "✅ Firebase Authentication 샘플 테스트가 성공적으로 완료되었습니다!\n";
    echo "   테스트용 전화번호와 인증코드를 사용한 시뮬레이션이 정상 작동했습니다.\n";
    echo "   실제 환경에서는 Firebase Console에서 휴대폰 인증 기능을 활성화하고,\n";
    echo "   '보이지 않는 reCAPTCHA' 옵션을 사용하여 구현해야 합니다.\n";
} else {
    echo "❌ Firebase Authentication 샘플 테스트 중 일부 실패가 발생했습니다.\n";
    echo "   위 오류 메시지를 확인하고 문제를 해결해 주세요.\n";
}
echo "==================================================\n"; 