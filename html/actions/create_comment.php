<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$post_id = $_POST["post_id"];
$content = $_POST["content"];
if (!isset($post_id) || !isset($content)) {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: /post.php?id=" . $post_id);
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "insert into comments (username, content, post_id) values (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $username, $content, $post_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "댓글 작성에 성공했어요!");

header("Location: /post.php?id=" . $post_id);
?>
