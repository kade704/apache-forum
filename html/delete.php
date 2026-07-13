<?php require_once "includes/session.php"; ?>
<?php
require_once "includes/login_check.php";
require_once "includes/csrf_token.php";

$post_id = $_GET["post_id"] ?? null;
if (!isset($post_id)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}
?>

<?php require_once "includes/header.php"; ?>
<main class="mt-10 mx-auto max-w-lg space-y-4">
    <?php
    echo <<<HTML
    <form method="POST" action="actions/delete_post.php" class="flex flex-col p-4 bg-base-100 rounded-lg shadow-md">
        <h2 class='text-xl font-bold'>삭제 확인</h2>
        <div class="divider my-2"></div>
        <input name="post_id" value={$post_id} hidden />
        <input name="csrf_token" value={$csrf_token} hidden />
        <button type="submit" class="mt-4 btn btn-secondary">정말로 삭제하시겠어요?</button>
    </form>
    HTML;
    ?>
</main>
<?php require_once "includes/footer.php"; ?>
