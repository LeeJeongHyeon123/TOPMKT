<?php
// 헤더 포함
require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">휴대폰 인증</h4>
                </div>
                <div class="card-body">
                    <!-- 휴대폰 번호 입력 폼 -->
                    <form id="phoneForm" class="mb-4">
                        <div class="form-group">
                            <label for="phone">휴대폰 번호</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   placeholder="+82 10-1234-5678" required>
                            <small class="form-text text-muted">국제 형식으로 입력해주세요 (예: +82 10-1234-5678)</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" id="sendCodeBtn">
                            인증번호 받기
                        </button>
                    </form>

                    <!-- 인증번호 입력 폼 (처음에는 숨김) -->
                    <form id="verificationForm" style="display: none;">
                        <div class="form-group">
                            <label for="code">인증번호</label>
                            <input type="text" class="form-control" id="code" name="code" 
                                   placeholder="6자리 인증번호" required>
                            <small class="form-text text-muted">SMS로 전송된 6자리 인증번호를 입력해주세요</small>
                        </div>
                        <button type="submit" class="btn btn-success mt-3" id="verifyCodeBtn">
                            인증번호 확인
                        </button>
                    </form>

                    <!-- 에러 메시지 표시 영역 -->
                    <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.x.x/firebase-auth-compat.js"></script>

<!-- 인증 관련 JavaScript -->
<script src="/assets/js/auth.js"></script>

<?php
// 푸터 포함
require_once __DIR__ . '/../../../includes/footer.php';
?> 