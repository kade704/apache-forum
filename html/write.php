<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";

$username = $_SESSION["username"];

if (!$username) {
    header("Location: /login.php");
    exit();
}
?>

<?php require_once "includes/header.php"; ?>
<main class="pt-20 mx-auto max-w-3xl">
    <?php echo <<<HTML
    <form method="POST" enctype="multipart/form-data" action="actions/create_post.php" class="p-6 flex flex-col bg-base-100 rounded-lg shadow-md">
        <div class="flex justify-between">
            <h2 class='text-xl font-bold'>게시글 작성</h2>
            <h2 class='opacity-50'>{$username}</h2>
        </div>
        <div class="divider my-2"></div>
        <div class="space-y-4">
            <input type="text" name="title" class="flex-1 input w-full"  placeholder="제목" required />
            <textarea type="text" name="content" class="textarea w-full min-h-48" placeholder="내용"></textarea>
            <fieldset class="fieldset">
                <legend class="fieldset-legend">사진 등록</legend>
                <input type="file" name="image_file" class="file-input" accept="image/*" />
                <label class="label">Max size 2MB</label>
            </fieldset>
            <button type="submit" class="btn btn-primary w-full">등록</button>
        </div>
    </form>
    HTML; ?>
</main>
<?php require_once "includes/footer.php"; ?>
