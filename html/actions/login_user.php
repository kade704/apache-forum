<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /login.php");
    exit();
}

$email = $_POST["email"] ?? null;
$password = $_POST["password"] ?? null;
if (!isset($email) || !isset($password)) {
    header("Location: /");
    exit();
}

$username = null;

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where email = ? and password = SHA2(?, 256)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();

    $result = $stmt->get_result();
    $found = $result->fetch_object();
    if (!$found) {
        toast_message("error", "이메일 또는 비밀번호가 맞지 않아요.");
        header("Location: /login.php");
        exit();
    }
    $username = $found->username;
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

$_SESSION["username"] = $username;
toast_message("success", "{$username}님 환영합니다!");

header("Location: /");
?>
