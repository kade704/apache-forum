<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

$username = $_SESSION["username"] ?? null;
if (!isset($username)) {
    header("Location: /");
    exit();
}

$curr_password = $_POST["curr_password"] ?? null;
$new_password = $_POST["new_password"] ?? null;
if (!isset($curr_password) || !isset($new_password)) {
    toast_message("error", "비밀번호를 입력하세요.");
    header("Location: /profile.php?username=" . $username);
    exit();
}

if (strlen($new_password) < 4) {
    toast_message("error", "비밀번호는 4글자 이상이어야 해요.");
    header("Location: /profile.php?username=" . $username);
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where username = ? and password = SHA2(?, 256)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $curr_password);
    $stmt->execute();

    $result = $stmt->get_result();
    $found = $result->fetch_object();
    if (!$found) {
        toast_message("error", "현재 비밀번호가 맞지 않아요.");
        header("Location: /profile.php?username=" . $username);
        exit();
    }

    $sql = "update users set password = SHA2(256, ?) where username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $new_password, $username);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "비밀번호를 성공적으로 변경했어요.");
header("Location: /profile.php?username=" . $username);
?>
