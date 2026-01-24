<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";

$username = $_GET["username"];
if (!isset($username)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        header("Location: /");
        exit();
    }

    $user = $result->fetch_object();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

$username_session = $_SESSION["username"];
?>

<?php require_once "includes/header.php"; ?>
<main class="mt-10 mx-auto max-w-lg space-y-4">
    <div class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
        <?php echo <<<HTML
        <div class="flex justify-between items-center">
            <h2 class='text-xl font-bold'>{$username}의 프로필</h2>
            <h2 class='text-sm opacity-50'>{$user->created_at} 가입</h2>
        </div>
        <div class="divider my-2"></div>
        HTML; ?>
        <?php if ($username == $username_session) {
            echo <<<HTML
            <form method="POST" action="actions/edit_profile.php" class="flex flex-col">
                <div class="mt-4 relative">
                   	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">자기소개</p>
                    <textarea type="text" name="content" class="textarea w-full h-20 pt-6" required>{$user->description}</textarea>
                </div>
                <button type="submit" class="mt-6 h-12 btn btn-primary">프로필 변경</button>
            </form>
            HTML;
        } else {
            echo "<p class='min-h-30'>{$user->description}</p>";
        }
        ?>
    </div>
    <?php if ($username == $username_session) {
        echo <<<HTML
        <form method="POST" action="actions/change_password.php" class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
            <h2 class='text-xl font-bold'>비밀번호 변경</h2>
            <div class="divider my-2"></div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">현재 비밀번호</p>
                <input type="password" name="curr_password" class="input w-full h-14 pt-3" required/>
            </div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">새로운 비밀번호</p>
                <input type="password" name="new_password" class="input w-full h-14 pt-3" required/>
            </div>
            <button type="submit" class="mt-6 h-12 btn btn-primary">비밀번호 변경</button>
        </form>
        <form method="POST" action="actions/delete_user.php" class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
            <h2 class='text-xl font-bold'>유저 삭제</h2>
            <div class="divider my-2"></div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">현재 비밀번호</p>
                <input type="password" name="curr_password" class="input w-full h-14 pt-3" required/>
            </div>
            <p class="mt-4">주의, 작성한 모든 글과 댓글이 삭제됩니다.</p>
            <button type="submit" class="mt-6 h-12 btn btn-secondary">Delete User</button>
        </form>
        HTML;
    } ?>
</main>
<?php require_once "includes/footer.php"; ?>
