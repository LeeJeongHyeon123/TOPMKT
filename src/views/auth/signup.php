<?php include SRC_PATH . '/views/templates/header.php'; ?>

<div class="auth-form-container">
    <h2>회원가입</h2>
    <form action="/auth/signup" method="post" class="auth-form">
        <div class="form-group">
            <label for="email">이메일</label>
            <input type="email" id="email" name="email" value="<?= $_SESSION['old_input']['email'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" id="name" name="name" value="<?= $_SESSION['old_input']['name'] ?? '' ?>" required>
        </div>
        <div class="form-group">
            <label for="password">비밀번호</label>
            <input type="password" id="password" name="password" required>
            <small>8자 이상의 영문, 숫자, 특수문자 조합</small>
        </div>
        <div class="form-group">
            <label for="password_confirm">비밀번호 확인</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        
        <?php if (isset($_SESSION['errors']) && is_array($_SESSION['errors'])): ?>
            <div class="form-errors">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">가입하기</button>
            <a href="/auth/login" class="link-login">이미 계정이 있으신가요?</a>
        </div>
    </form>
</div>

<?php include SRC_PATH . '/views/templates/footer.php'; ?> 