<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";
require_once "includes/csrf_token.php";

$user_id = $_GET["user_id"] ?? null;
if (!isset($user_id)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        http_response_code(404);
        echo "존재하지 않는 유저";
        exit();
    }

    $user = $result->fetch_object();
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());

    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

$user_id_session = $_SESSION["user_id"] ?? null;
?>

<?php require_once "includes/header.php"; ?>
<main class="mt-10 mx-auto max-w-2xl space-y-4">
    <div class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
        <?php echo <<<HTML
        <div class="flex justify-between items-center">
            <h2 class='text-xl font-bold'>{$user->id}의 프로필</h2>
            <h2 class='text-sm opacity-50'>{$user->created_at} 가입</h2>
        </div>
        <div class="divider my-2"></div>

        HTML; ?>
        <?php if ($user_id == $user_id_session) {
            echo <<<HTML
            <form method="POST" action="actions/edit_profile.php" class="flex flex-col">
                <input type="hidden" name="csrf_token" value="{$csrf_token}" />
                <div class="mt-4 relative">
                   	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">이메일</p>
                    <input type="text" name="email" class="input w-full h-14 pt-3" value="{$user->email}" required />
                </div>
                <div class="mt-4 relative">
                   	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">자기소개</p>
                    <textarea type="text" name="description" class="textarea w-full h-20 pt-6" required>{$user->description}</textarea>
                </div>
                <button type="submit" class="mt-6 h-12 btn btn-primary">프로필 변경</button>
            </form>
            HTML;
        } else {
            echo "<p class='min-h-30'>{$user->description}</p>";
        }
        ?>
    </div>
    <?php if ($user_id == $user_id_session) {
        echo <<<HTML
        <form method="POST" action="actions/change_password.php" class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
            <input type="hidden" name="csrf_token" value="{$csrf_token}" />
            <h2 class='text-xl font-bold'>비밀번호 변경</h2>
            <div class="divider my-2"></div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">현재 비밀번호</p>
                <input type="password" name="curr_password" class="input w-full h-14 pt-3" required/>
            </div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">새로운 비밀번호  <span class="opacity-50">(최소 8자, 최대 20자, 영문, 숫자, 특수문자 포함)</span></p>
                <input type="password" name="new_password" class="input w-full h-14 pt-3" required/>
            </div>
            <button type="submit" class="mt-6 h-12 btn btn-primary">비밀번호 변경</button>
        </form>
        <form method="POST" action="actions/delete_user.php" class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
            <input type="hidden" name="csrf_token" value="{$csrf_token}" />
            <h2 class='text-xl font-bold'>유저 삭제</h2>
            <div class="divider my-2"></div>
            <div class="mt-4 relative">
               	<p class="absolute text-sm z-10 translate-x-3 translate-y-1 opacity-50">현재 비밀번호</p>
                <input type="password" name="curr_password" class="input w-full h-14 pt-3" required/>
            </div>
            <p class="mt-4">주의, 작성한 모든 글과 댓글이 삭제됩니다.</p>
            <button type="submit" class="mt-6 h-12 btn btn-secondary">유저 삭제</button>
        </form>
        HTML;
    } ?>
</main>
<?php require_once "includes/footer.php"; ?>
