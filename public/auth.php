    <div class="auth-container">
        <div class="auth-header">
            <h1>로그인/회원가입</h1>
            <div id="authSubtitle" class="auth-subtitle">휴대폰 번호로 간편하게 로그인하세요</div>
        </div>
        <div class="auth-tabs">
            <!-- 탭 버튼 -->
            <button id="loginTab" class="auth-tab active">로그인</button>
            <button id="registerTab" class="auth-tab">회원가입</button>
            
            <!-- 에러 메시지 표시 영역 -->
            <div id="errorMessage" class="error-message"></div>
            
            <!-- 로그인 폼 -->
            <div id="loginForm" class="auth-form">
                <input type="text" id="phone" name="phone" placeholder="휴대폰 번호를 입력하세요">
                <button id="loginButton">로그인</button>
            </div>

            <!-- 회원가입 폼 -->
            <div id="registerForm" class="auth-form" style="display: none;">
                <input type="text" id="registerPhone" name="registerPhone" placeholder="휴대폰 번호를 입력하세요">
                <button id="registerButton">회원가입</button>
            </div>
        </div>
    </div>
</div> 