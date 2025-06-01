<?php include SRC_PATH . '/views/templates/header.php'; ?>

<div class="auth-form-container">
    <h2>로그인</h2>
    <form action="/auth/login" method="post" class="auth-form">
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">로그인</button>
            <a href="/auth/signup" class="link-signup">회원가입</a>
        </div>
    </form>
</div>

<?php include SRC_PATH . '/views/templates/footer.php'; ?> 