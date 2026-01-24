<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /signup.php");
    exit();
}

$username = $_POST["username"];
$password = $_POST["password"];
$password_repeat = $_POST["password_repeat"];

if ($password != $password_repeat) {
    toast_message("error", "두 비밀번호가 맞지 않아요.");
    header("Location: /signup.php");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        toast_message("error", "해당 유저이름을 이미 사용하고 있어요.");
        header("Location: /signup.php");
        exit();
    }

    $sql = "insert into users (username, password) values (?, SHA2(?, 256))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

$_SESSION["username"] = $username;

toast_message("success", "가입성공!");

header("Location: /");
?>
