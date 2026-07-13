<?php require_once "includes/session.php"; ?>
<?php require_once "includes/csrf_token.php"; ?>
<?php require_once "includes/header.php"; ?>
<form method="post" action="actions/signup_user.php" class="mt-20 mx-auto max-w-lg p-4 flex flex-col min-w-md bg-base-100 rounded-lg shadow-lg">
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
    <h1 class="mx-auto card-title text-center text-4xl">Forum</h1>
    <div class="mt-10 relative">
        <p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">아이디 <span class="opacity-50">(최소 4자)</span></p>
        <input type="text" name="user_id" pattern="[a-zA-Z0-9\s]*" minlength="4" class="input w-full h-14 pt-3" required />
    </div>
    <div class="mt-4 relative">
       	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">비밀번호 <span class="opacity-50">(최소 8자, 최대 20자, 영문, 숫자, 특수문자 포함)</span></p>
        <input type="password" name="password" pattern="[a-zA-Z0-9\s]*" minlength="8" maxlength="20" class="input w-full h-14 pt-3" required/>
    </div>
    <div class="mt-4 relative">
        <p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">비밀번호 확인</p>
        <input type="password" name="password_repeat" pattern="[a-zA-Z0-9\s]*" minlength="8" maxlength="20" class="input w-full h-14 pt-3" required/>
    </div>
    <div class="mt-4 relative">
        <p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">이메일</p>
        <input type="email" name="email" class="input w-full h-14 pt-3" required />
    </div>
    <button type="submit" class="mt-10 h-12 btn btn-primary text-lg">회원가입</button>
    <div class="mt-4 w-full flex items-center justify-center gap-2">
        <p class="text-sm">이미 계정이 있으신가요?</p>
        <a class="text-sm font-semibold underline" href="login.php">로그인</a>
    </div>
</form>
<?php require_once "includes/footer.php"; ?>
