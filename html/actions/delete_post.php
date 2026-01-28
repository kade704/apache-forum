<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"] ?? null;
if (!isset($username)) {
    header("Location: /");
    exit();
}

$post_id = $_POST["post_id"] ?? null;
if (!isset($post_id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "delete from posts where id=? and username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $post_id, $username);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "게시글을 성공적으로 삭제했어요.");

header("Location: /");
?>
