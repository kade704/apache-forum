<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: /");
    exit();
}

$post_id = $_POST["post_id"];
$comment_id = $_POST["comment_id"];
if (!isset($post_id) || !isset($comment_id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "delete from comments where id=? and username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $comment_id, $username);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "댓글을 성공적으로 삭제했습니다.");
header("Location: /post.php?id=" . $post_id);
?>
