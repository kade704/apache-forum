<?php require_once "includes/session.php"; ?>
<?php require_once "includes/csrf_token.php"; ?>
<?php require_once "includes/header.php"; ?>
    <form method="post" action="actions/login_user.php" class="mt-20 mx-auto max-w-lg p-4 flex flex-col min-w-md bg-base-100 rounded-lg shadow-lg">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <h1 class="mx-auto text-center font-bold text-4xl">Forum</h1>
        <div class="mt-10 relative">
            <p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">아이디</p>
            <input type="text" name="user_id" class="input w-full h-14 pt-3" required />
        </div>
        <div class="mt-4 relative">
           	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">비밀번호</p>
            <input type="password" name="password" class="input w-full h-14 pt-3" required/>
        </div>
        <button type="submit" class="mt-10 h-12 btn btn-primary text-lg">로그인</button>
        <div class="mt-4 w-full flex items-center justify-center gap-2">
            <p class="text-sm">아직 회원이 아니신가요? </p>
            <a class="text-sm font-semibold underline" href="signup.php">회원가입</a>
        </div>
    </form>
<?php require_once "includes/footer.php"; ?>
