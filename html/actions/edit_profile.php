<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$description = $_POST["content"] ?? null;
if (!isset($description)) {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"] ?? null;
if (!isset($username)) {
    header("Location: /post.php?id=" . $post_id);
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "update users set description = ? where username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $description, $username);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "프로필을 수정하였어요.");

header("Location: /profile.php?username=" . $username);
?>
