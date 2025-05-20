<?php
/**
 * Firebase Authentication 테스트 스크립트
 * 
 * Firebase Authentication 서비스 연동이 제대로 작동하는지 확인합니다.
 * 휴대폰 번호 인증 기능을 테스트합니다.
 */

// 부트스트랩 로드
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/app/bootstrap.php';

use App\Services\Firebase\AuthService;

echo "Firebase Authentication 연결 테스트를 시작합니다...\n\n";

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

// 2. Auth API 가용성 테스트
try {
    echo "\n2. Firebase Auth API 가용성 테스트 중...\n";
    
    // Auth 인스턴스 가져오기
    $auth = $authService->getAuth();
    
    if ($auth) {
        echo "   - Firebase Auth API 인스턴스 획득 성공\n";
        
        // Firebase Auth 버전 확인
        $reflection = new ReflectionClass($auth);
        echo "   - Firebase Auth 클래스: " . $reflection->getName() . "\n";
        
        // 기본 사용 가능한 기능 확인 (Firebase 콘솔에서 활성화해야 함)
        echo "   - 참고: 전화번호 인증 기능은 Firebase 콘솔에서 활성화해야 합니다\n";
        echo "   - 설정 경로: Firebase Console > Authentication > Sign-in method > 전화번호 활성화\n";
        
        // Firebase 프로젝트 정보 확인
        $config = require APP_ROOT . '/config/firebase.php';
        echo "   - Firebase 프로젝트 ID: " . $config['project_id'] . "\n";
    } else {
        echo "   - Firebase Auth API 인스턴스를 가져올 수 없습니다.\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - Firebase Auth API 가용성 테스트 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 3. 인증 시도 기록 테스트
try {
    echo "\n3. 인증 시도 기록 테스트 중...\n";
    
    // 테스트용 전화번호 (국가 코드 포함, E.164 형식)
    $testPhoneNumber = '+821012345678';
    
    // 전송 가능 여부 확인
    $canSend = $authService->canSendVerificationCode($testPhoneNumber);
    echo "   - 인증번호 전송 가능 여부: " . ($canSend['allowed'] ? "가능" : "불가능") . "\n";
    echo "   - 메시지: {$canSend['message']}\n";
    
    if ($canSend['allowed']) {
        // 테스트용 인증 시도 기록 (실제 SMS는 발송되지 않음)
        $authService->logAuthAttempt($testPhoneNumber, true, 'send');
        echo "   - 인증번호 전송 기록 저장 성공\n";
        
        // 인증 성공 시도 테스트
        $authService->logAuthAttempt($testPhoneNumber, true, 'verify');
        echo "   - 인증 성공 기록 저장 성공\n";
        
        // 인증 실패 시도 테스트
        $authService->logAuthAttempt($testPhoneNumber, false, 'verify');
        echo "   - 인증 실패 기록 저장 성공\n";
    }
} catch (\Exception $e) {
    echo "   - 인증 시도 기록 테스트 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 4. 전화번호 인증 차단 정책 검증
try {
    echo "\n4. 전화번호 인증 차단 정책 검증 중...\n";
    echo "   - 정책: 1시간 내 인증번호 5회 오류 시 24시간 동안 인증 불가\n";
    
    // 테스트용 임시 전화번호 (실제 사용하지 않는 번호)
    $tempPhoneNumber = '+821099998888';
    
    // 5회 연속 실패 기록 생성 (실제 정책 테스트)
    echo "   - 테스트용 5회 연속 실패 기록 생성 중...\n";
    
    for ($i = 0; $i < 5; $i++) {
        $authService->logAuthAttempt($tempPhoneNumber, false, 'verify');
    }
    
    // 차단 여부 확인
    $canSend = $authService->canSendVerificationCode($tempPhoneNumber);
    echo "   - 5회 연속 실패 후 인증번호 전송 가능 여부: " . ($canSend['allowed'] ? "가능" : "불가능") . "\n";
    echo "   - 메시지: {$canSend['message']}\n";
    
    if (!$canSend['allowed'] && $canSend['remainingTime'] > 0) {
        echo "   - 남은 차단 시간: 약 " . floor($canSend['remainingTime'] / 3600) . "시간\n";
        echo "   - 차단 정책 정상 작동 확인\n";
    } else {
        echo "   - 차단 정책이 예상대로 작동하지 않음\n";
        $success = false;
    }
} catch (\Exception $e) {
    echo "   - 전화번호 인증 차단 정책 검증 실패: " . $e->getMessage() . "\n";
    $success = false;
}

// 5. 휴대폰 인증 프로세스 테스트 (클라이언트 측 구현 필요 안내)
echo "\n5. 휴대폰 인증 프로세스 안내\n";
echo "   - 실제 휴대폰 인증은 Firebase JS SDK를 사용하여 클라이언트 측에서 구현해야 합니다.\n";
echo "   - 필요한 구성 요소:\n";
echo "     1) Firebase 콘솔에서 휴대폰 인증 활성화\n";
echo "     2) reCAPTCHA 설정 (스팸 방지 정책에 따라 필요)\n";
echo "     3) 클라이언트 측 JS 코드 구현\n";
echo "     4) UI 구현 (전화번호 입력, 인증코드 입력 폼)\n";
echo "   - 기본정책 확인:\n";
echo "     * 비밀번호 없이 휴대폰 번호만으로 인증\n";
echo "     * 인증된 전화번호를 로그인 ID로 사용\n";
echo "     * 로그인 시 로그인 정보(ID, 시간, IP 등)를 DB에 기록\n";
echo "     * 로그아웃 전까지 로그인 세션 유지\n";
echo "     * 인증번호 1시간 내 5회 오류 시 24시간 동안 인증 불가\n";

// 6. 클라이언트 측 구현을 위한 코드 예시
echo "\n6. 클라이언트 측 구현을 위한 코드 예시\n";
echo "   - HTML 및 JavaScript 코드는 다음과 같이 구현할 수 있습니다:\n";
echo "```html\n";
echo "<script src=\"https://www.gstatic.com/firebasejs/9.x.x/firebase-app.js\"></script>\n";
echo "<script src=\"https://www.gstatic.com/firebasejs/9.x.x/firebase-auth.js\"></script>\n";
echo "\n";
echo "<div id=\"phone-auth-container\">\n";
echo "  <div id=\"phone-input-section\">\n";
echo "    <input type=\"text\" id=\"phone-number\" placeholder=\"+82 10-1234-5678\">\n";
echo "    <button id=\"send-code-button\">인증번호 전송</button>\n";
echo "    <div id=\"recaptcha-container\"></div>\n";
echo "  </div>\n";
echo "  <div id=\"code-input-section\" style=\"display:none;\">\n";
echo "    <input type=\"text\" id=\"verification-code\" placeholder=\"인증번호 입력\">\n";
echo "    <button id=\"verify-code-button\">인증 확인</button>\n";
echo "  </div>\n";
echo "</div>\n";
echo "```\n";
echo "\n";
echo "```javascript\n";
echo "// Firebase 초기화\n";
echo "const firebaseConfig = {\n";
echo "  apiKey: \"YOUR_API_KEY\",\n";
echo "  authDomain: \"topmkt-832f2.firebaseapp.com\",\n";
echo "  projectId: \"topmkt-832f2\",\n";
echo "  storageBucket: \"topmkt-832f2.firebasestorage.app\",\n";
echo "  messagingSenderId: \"YOUR_MESSAGING_SENDER_ID\",\n";
echo "  appId: \"YOUR_APP_ID\"\n";
echo "};\n";
echo "\n";
echo "// Initialize Firebase\n";
echo "const app = initializeApp(firebaseConfig);\n";
echo "const auth = getAuth(app);\n";
echo "\n";
echo "// reCAPTCHA 렌더링\n";
echo "window.recaptchaVerifier = new RecaptchaVerifier('recaptcha-container', {\n";
echo "  'size': 'normal',\n";
echo "  'callback': (response) => {\n";
echo "    // reCAPTCHA 인증 성공\n";
echo "    document.getElementById('send-code-button').disabled = false;\n";
echo "  }\n";
echo "}, auth);\n";
echo "\n";
echo "// 인증번호 전송\n";
echo "document.getElementById('send-code-button').addEventListener('click', () => {\n";
echo "  const phoneNumber = document.getElementById('phone-number').value;\n";
echo "  \n";
echo "  // 서버에 인증 시도 가능 여부 확인 요청 (API 호출)\n";
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
echo "        document.getElementById('phone-input-section').style.display = 'none';\n";
echo "        document.getElementById('code-input-section').style.display = 'block';\n";
echo "        \n";
echo "        // 인증번호 전송 기록 (API 호출)\n";
echo "        fetch('/api/auth/log-attempt', {\n";
echo "          method: 'POST',\n";
echo "          headers: { 'Content-Type': 'application/json' },\n";
echo "          body: JSON.stringify({\n";
echo "            phone: phoneNumber,\n";
echo "            success: true,\n";
echo "            action: 'send'\n";
echo "          })\n";
echo "        });\n";
echo "      }).catch((error) => {\n";
echo "        // SMS 전송 실패\n";
echo "        alert('인증번호 전송에 실패했습니다: ' + error.message);\n";
echo "      });\n";
echo "  });\n";
echo "});\n";
echo "\n";
echo "// 인증코드 확인\n";
echo "document.getElementById('verify-code-button').addEventListener('click', () => {\n";
echo "  const code = document.getElementById('verification-code').value;\n";
echo "  const phoneNumber = document.getElementById('phone-number').value;\n";
echo "  \n";
echo "  window.confirmationResult.confirm(code)\n";
echo "    .then((result) => {\n";
echo "      // 인증 성공\n";
echo "      const user = result.user;\n";
echo "      \n";
echo "      // 인증 성공 기록 및 로그인 처리 (API 호출)\n";
echo "      fetch('/api/auth/login', {\n";
echo "        method: 'POST',\n";
echo "        headers: { 'Content-Type': 'application/json' },\n";
echo "        body: JSON.stringify({\n";
echo "          phone: phoneNumber,\n";
echo "          uid: user.uid,\n";
echo "          token: user.accessToken\n";
echo "        })\n";
echo "      })\n";
echo "      .then(response => response.json())\n";
echo "      .then(data => {\n";
echo "        // 로그인 성공 시 메인 페이지로 이동\n";
echo "        window.location.href = '/main';\n";
echo "      });\n";
echo "    }).catch((error) => {\n";
echo "      // 인증 실패\n";
echo "      alert('인증에 실패했습니다: ' + error.message);\n";
echo "      \n";
echo "      // 인증 실패 기록 (API 호출)\n";
echo "      fetch('/api/auth/log-attempt', {\n";
echo "        method: 'POST',\n";
echo "        headers: { 'Content-Type': 'application/json' },\n";
echo "        body: JSON.stringify({\n";
echo "          phone: phoneNumber,\n";
echo "          success: false,\n";
echo "          action: 'verify'\n";
echo "        })\n";
echo "      });\n";
echo "    });\n";
echo "});\n";
echo "```\n";

// 테스트 결과 출력
echo "\n==================================================\n";
if ($success) {
    echo "✅ Firebase Authentication 연결 테스트가 성공적으로 완료되었습니다!\n";
    echo "   Firebase Authentication이 정상적으로 연동되었습니다.\n";
} else {
    echo "❌ Firebase Authentication 연결 테스트 중 일부 실패가 발생했습니다.\n";
    echo "   위 오류 메시지를 확인하고 문제를 해결해 주세요.\n";
}
echo "==================================================\n";
echo "\n📱 휴대폰 인증 UI 구현은 기본정책에 따라 추후 별도 지시가 있을 때 진행합니다.\n"; 