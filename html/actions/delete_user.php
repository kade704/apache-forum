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

$curr_password = $_POST["curr_password"] ?? null;
if (!isset($curr_password)) {
    toast_message("error", "비밀번호를 입력하세요.");
    header("Location: /profile.php?username=" . $username);
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select password from users where password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $curr_password);
    $stmt->execute();

    $result = $stmt->get_result();
    $found = $result->fetch_object();
    if (!$found) {
        toast_message("error", "현재 비밀번호가 맞지 않아요.");
        header("Location: /profile.php?username=" . $username);
        exit();
    }

    $sql = "delete from users where username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

$_SESSION["username"] = null;
toast_message("success", "유저를 성공적으로 삭제했어요. 다음에 다시 만나요!");
header("Location: /");
?>
