<?php require_once "includes/session.php"; ?>
<?php require_once "includes/header.php"; ?>
    <form method="post" action="actions/login_user.php" class="mt-20 mx-auto max-w-lg p-4 flex flex-col min-w-md bg-base-100 rounded-lg shadow-lg">
        <h1 class="mx-auto text-center font-bold text-4xl">Forum</h1>
        <div class="mt-10 relative">
            <p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">유저이름</p>
            <input type="text" id="username" name="username" pattern="[a-zA-Z0-9\s]*" class="input w-full h-14 pt-3" required />
        </div>
        <div class="mt-4 relative">
           	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">비밀번호</p>
            <input type="password" id="password" name="password" pattern="[a-zA-Z0-9\s]*" class="input w-full h-14 pt-3" required/>
        </div>
        <button type="submit" class="mt-10 h-12 btn btn-primary text-lg">로그인</button>
        <div class="mt-4 w-full flex items-center justify-center gap-2 text-sm">
            <h2>계정이 없으신가요?</h2>
            <a class="font-semibold underline" href="signup.php">가입하기</a>
        </div>
    </form>
<?php require_once "includes/footer.php"; ?>
