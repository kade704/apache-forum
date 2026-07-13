<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$post_id = $_POST["post_id"] ?? null;
$comment_id = $_POST["comment_id"] ?? null;
if (!isset($post_id) || !isset($comment_id)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "delete from comments where id=? and user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $comment_id, $user_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "댓글을 성공적으로 삭제했습니다.");
header("Location: /post.php?post_id=" . $post_id);
?>
