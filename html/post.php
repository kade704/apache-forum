<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";
require_once "functions/datetime.php";

$username = $_SESSION["username"] ?? null;

$id = $_GET["id"];
if (!isset($id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from posts where id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();
    if (!$post) {
        header("Location: /");
        exit();
    }

    $sql = "select * from comments where post_id=? order by created_at";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $comments = $stmt->get_result();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>

<?php require_once "includes/header.php"; ?>
<main class="pt-20 mx-auto max-w-3xl">
    <div class="p-6 bg-base-100 rounded-lg shadow-md">
        <?php echo <<<HTML
        <div class="flex justify-between">
            <h2 class='text-xl font-bold'>{$post->title}</h2>
            <div class="flex items-center gap-2 opacity-50 text-sm">
                <a href="profile.php?username={$post->username}" class="font-semibold underline">{$post->username}</a>
                <h3>·</h3>
                <h3>{$post->created_at}</h3>
            </div>
        </div>
        <div class="divider my-2"></div>
        HTML; ?>
        <?php if ($post->image_url) {
            echo <<<HTML
            <img src={$post->image_url} class="mb-4" />
            HTML;
        } ?>
        <?php echo <<<HTML
        <h1 class='text-lg min-h-24'>{$post->content}</h1>
        HTML; ?>
        <?php if ($username == $post->username) {
            echo <<<HTML
            <form method="POST" action="actions/delete_post.php">
                <input name="post_id" value={$post->id} hidden />
                <button type="submit" class="mt-4 btn btn-sm btn-secondary">게시글 삭제</button>
            </form>
            HTML;
        } ?>
    </div>
    <ul class="mt-6 list bg-base-100 rounded-box shadow-md">
        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">댓글 목록</li>
        <?php if ($comments->num_rows > 0) {
            while ($comment = $comments->fetch_object()) {
                $time = formatDistanceToNow($comment->created_at);
                echo <<<HTML
                <li class="list-row items-center text-sm">
                    <a href="profile.php?username={$comment->username}" class="opacity-50 min-w-14 underline">{$comment->username}</a>
                    <p class='flex-1 font-semibold'> {$comment->content}</p>
                    <p class='opacity-50'>{$time}</p>
                HTML;
                if ($username == $comment->username) {
                    echo <<<HTML
                    <form method="POST" action="actions/delete_comment.php">
                        <input name="post_id" value={$post->id} hidden />
                        <input name="comment_id" value={$comment->id} hidden />
                        <button type="submit" class="btn btn-sm btn-secondary">Delete</button>
                    </form>
                    HTML;
                }
                echo "</li>";
            }
        } else {
            echo <<<HTML
            <li class="list-row items-center">
                <p class="text-sm opacity-20">댓글이 비었습니다.</p>
            </li>
            HTML;
        } ?>
        <?php if (isset($username)) {
            echo <<<HTML
            <form method="POST" action="actions/create_comment.php">
                <li class="list-row items-center">
                    <p class="text-sm opacity-50 ">{$username}</p>
                    <input name="username" value={$username} hidden />
                    <input name="post_id" value={$post->id} hidden />
                    <input type="text" name="content" class="flex-1 input w-full" placeholder="댓글을 작성하세요" required />
                    <button type="submit" class="btn btn-primary">등록</button>
                </li>
            </form>
            HTML;
        } ?>
    </ul>
</main>
<?php require_once "includes/footer.php"; ?>
