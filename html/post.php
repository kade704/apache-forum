<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";
require_once "functions/datetime.php";
require_once "includes/csrf_token.php";

$user_id = $_SESSION["user_id"] ?? null;

$post_id = $_GET["post_id"] ?? null;
if (!isset($post_id)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from posts where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();
    if (!$post) {
        http_response_code(404);
        echo "게시글을 찾을 수 없음";
        exit();
    }

    $sql = "update posts set views = views + 1 where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $sql = "select * from comments where post_id = ? order by created_at";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $comments = $stmt->get_result();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}
?>

<?php require_once "includes/header.php"; ?>
<main class="pt-20 mx-auto max-w-4xl">
    <div class="p-6 bg-base-100 rounded-lg shadow-md">
        <?php echo <<<HTML
        <div class="flex justify-between">
            <h2 class='text-xl font-bold'>{$post->title}</h2>
            <div class="flex items-center gap-2 opacity-50 text-sm">
                <a href="profile.php?user_id={$post->user_id}" class="font-semibold underline">{$post->user_id}</a>
                <p>·</p>
                <p class="text-sm">{$post->created_at}</p>
                <p>·</p>
                <p class="text-sm flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><g fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M20.188 10.934c.388.472.582.707.582 1.066s-.194.594-.582 1.066C18.768 14.79 15.636 18 12 18s-6.768-3.21-8.188-4.934c-.388-.472-.582-.707-.582-1.066s.194-.594.582-1.066C5.232 9.21 8.364 6 12 6s6.768 3.21 8.188 4.934Z"/></g></svg>
                    {$post->views}
                </p>
            </div>
        </div>
        <div class="divider my-2"></div>
        HTML; ?>
        <?php if ($post->image_path) {
            echo <<<HTML
                <img src="/actions/download_image.php?post_id={$post->id}" class="mb-4" />
            HTML;
        } ?>
        <?php echo <<<HTML
        <h1 class='text-lg min-h-24'>{$post->content}</h1>
        HTML; ?>
        <?php if ($user_id == $post->user_id) {
            echo <<<HTML
            <div class="flex gap-4">
                <a href="/edit.php?post_id={$post->id}">
                    <button type="submit" class="mt-4 btn btn-sm btn-primary">게시글 수정</button>
                </a>
                <a href="/delete.php?post_id={$post->id}">
                    <button type="submit" class="mt-4 btn btn-sm btn-secondary">게시글 삭제</button>
                </a>
            </div>
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
                    <a href="profile.php?user_id={$comment->user_id}" class="opacity-50 min-w-14 underline">{$comment->user_id}</a>
                    <p class='flex-1 font-semibold'> {$comment->content}</p>
                    <p class='opacity-50'>{$time}</p>
                HTML;
                if ($user_id == $comment->user_id) {
                    echo <<<HTML
                    <form method="POST" action="actions/delete_comment.php">
                        <input type="hidden" name="csrf_token" value="{$csrf_token}" />
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
        <?php if (isset($user_id)) {
            echo <<<HTML
            <form method="POST" action="actions/create_comment.php">
                <input type="hidden" name="csrf_token" value="{$csrf_token}" />
                <li class="list-row items-center">
                    <p class="text-sm opacity-50 ">{$user_id}</p>
                    <input name="user_id" value={$user_id} hidden />
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
