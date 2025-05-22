// 전역 네임스페이스 TOPMKT 생성 또는 확인
var TOPMKT = TOPMKT || {};

$(document).ready(function() {
    console.log("[DEBUG] main.js - DOMContentLoaded");

    // TOPMKT 객체 내 함수 정의
    TOPMKT.isLoggedIn = function() {
        const authToken = localStorage.getItem('auth_token');
        const userId = localStorage.getItem('user_id');
        const keepLogin = localStorage.getItem('keep_login');
        
        // 로깅 추가: localStorage에서 가져온 값 확인
        console.log("[DEBUG] main.js - isLoggedIn() - localStorage values: authToken:", authToken, ", userId:", userId, ", keepLogin:", keepLogin);

        // 조건 수정:
        // 1. authToken (Firebase ID Token)이 존재해야 함.
        // 2. userId (UUID)가 존재해야 함 (null, undefined, 빈 문자열, '0'이 아니어야 함).
        // 3. keepLogin이 문자열 'true' 이어야 함.
        const isLoggedInValid = authToken && userId && userId !== '0' && typeof userId === 'string' && userId.length > 1 && keepLogin === 'true';
        
        console.log("[DEBUG] main.js - isLoggedIn() - result:", isLoggedInValid);
        return isLoggedInValid;
    };

    TOPMKT.updateHeaderUI = function() {
        console.log("[DEBUG] main.js - updateHeaderUI() 호출됨");
        const isLoggedIn = TOPMKT.isLoggedIn();
        const userNickname = localStorage.getItem('user_nickname');

        // 로깅 추가: isLoggedIn 결과와 닉네임 확인
        console.log("[DEBUG] main.js - updateHeaderUI() - isLoggedIn:", isLoggedIn, ", userNickname:", userNickname);

        const authContainer = $('#auth-container');
        if (!authContainer.length) {
            console.warn("[DEBUG] main.js - updateHeaderUI() - #auth-container not found.");
            return; 
        }

        if (isLoggedIn && userNickname) {
            console.log("[DEBUG] main.js - updateHeaderUI() - Logged in. Nickname:", userNickname);
            authContainer.html(
                '<span class="nickname"><strong>' + userNickname + '</strong>님 환영합니다!</span>' +
                '<a href="/mypage" class="btn btn-sm btn-outline-secondary">마이페이지</a> ' +
                '<button id="logoutButton" class="btn btn-sm btn-outline-danger">로그아웃</button>'
            );
            // 로그아웃 버튼에 이벤트 리스너 바인딩 (중복 방지 없음 - 필요시 off().on() 사용)
            // 이전에는 handleLogoutClick을 전역으로 호출했는데, 이제 TOPMKT 객체 내부 함수로 변경
            $('#logoutButton').on('click', TOPMKT.handleLogoutClick);
            console.log("[DEBUG] main.js - updateHeaderUI() - Logout button event listener attached.");
        } else {
            console.log("[DEBUG] main.js - updateHeaderUI() - Not logged in.");
            authContainer.html(
                '<a href="/auth" class="btn btn-sm btn-outline-primary">로그인/회원가입</a>'
            );
            // 로그인하지 않은 상태에서는 logoutButton이 없으므로, 이벤트 리스너 제거 로직은 필요 없음.
            // 만약 이전에 logoutButton에 이벤트가 걸려있었다면, 여기서 제거하는 것이 안전할 수 있지만,
            // 현재는 innerHTML로 교체되므로 이전 이벤트 리스너는 자동으로 제거됨.
        }
        console.log("[DEBUG] main.js - updateHeaderUI() 완료.");
    };

    TOPMKT.handleLogoutClick = function() {
        console.log("[DEBUG] main.js - handleLogoutClick() 호출됨");
        // Firebase 로그아웃 (선택 사항, 클라이언트 측에서만 로그아웃 처리)
        if (firebase && firebase.auth && firebase.auth().currentUser) {
            firebase.auth().signOut().then(() => {
                console.log("[DEBUG] main.js - Firebase sign-out successful.");
            }).catch((error) => {
                console.error("[DEBUG] main.js - Firebase sign-out error:", error);
            });
        }

        // localStorage에서 사용자 정보 삭제
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_id');
        localStorage.removeItem('user_nickname');
        localStorage.removeItem('keep_login');
        // verification_session_info는 인증 과정에서만 사용되므로, 로그아웃 시 명시적으로 삭제할 필요는 없으나, 정리 차원에서 삭제 가능
        localStorage.removeItem('verification_session_info'); 

        console.log("[DEBUG] main.js - handleLogoutClick() - localStorage cleared for user session.");

        // 서버에 로그아웃 요청 (선택 사항, 서버 세션이 있다면)
        // $.post('/api/auth/logout.php', function(response) {
        //     console.log("[DEBUG] main.js - Server logout response:", response);
        // });

        // 헤더 UI 업데이트 및 페이지 리로드
        TOPMKT.updateHeaderUI();
        // window.location.reload(); // 페이지를 새로고침하여 완전히 새로운 상태로 시작
        window.location.href = '/'; // 홈으로 리다이렉트하여 상태 반영
        console.log("[DEBUG] main.js - handleLogoutClick() - UI updated and redirected to home.");
    };

    // 페이지 로드 시 헤더 UI 업데이트
    console.log("[DEBUG] main.js - Initial call to updateHeaderUI()");
    TOPMKT.updateHeaderUI();

    // SPA 스타일의 페이지 이동 감지 (예: Turbo, htmx 사용 시)
    // $(document).on('turbo:load', function() {
    //     console.log("[DEBUG] main.js - turbo:load event - Calling updateHeaderUI()");
    //     TOPMKT.updateHeaderUI();
    // });

    // 로딩 오버레이 관련 로직은 loading-overlay.js로 분리되어 있다고 가정
    // 만약 main.js에 있다면 아래와 같이 처리될 수 있음:
    // console.log("[DEBUG] main.js - Setting up loading overlay logic if any.");
    // if (typeof TOPMKT.setLoading === 'function') {
    //     // 페이지 로드 완료 시 로딩 오버레이 숨기기 (예시)
    //     $(window).on('load', function() {
    //         console.log("[DEBUG] main.js - window.load event - Hiding loading overlay.");
    //         setTimeout(function() { // 약간의 지연을 주어 컨텐츠 로드를 확실히 한 후 숨김
    //             TOPMKT.setLoading(false);
    //         }, 100); 
    //     });
    // }
});

// 로딩 오버레이 제어 함수 (만약 loading-overlay.js에 없다면 여기에 정의)
// TOPMKT.setLoading = function(isLoading) {
//     const overlay = document.getElementById('loadingOverlay') || document.getElementById('loading-overlay');
//     if (overlay) {
//         console.log(`[DEBUG] main.js - setLoading(${isLoading}) called. Overlay found:`, overlay);
//         if (isLoading) {
//             overlay.classList.remove('hidden');
//         } else {
//             overlay.classList.add('hidden');
//         }
//         console.log("[DEBUG] main.js - setLoading - Overlay classes:", overlay.classList.toString());
//     } else {
//         console.warn("[DEBUG] main.js - setLoading - Loading overlay element not found.");
//     }
// };

console.log("[DEBUG] main.js execution finished.");