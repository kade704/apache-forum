<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /login.php");
    exit();
}

$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
if (!isset($username) || !isset($password)) {
    toast_message("error", "유저이름 또는 비밀번호를 입력하세요.");
    header("Location: /login.php");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where username = ? and password = SHA2(?, 256)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    $found = $result->fetch_object();
    if (!$found) {
        toast_message("error", "유저이름 또는 비밀번호가 맞지 않아요.");
        header("Location: /login.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

$_SESSION["username"] = $username;
toast_message("success", "{$username}님 환영합니다!");

header("Location: /");
?>
