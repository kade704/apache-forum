<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /signup.php");
    exit();
}

$email = $_POST["email"] ?? null;
$username = $_POST["username"] ?? null;
$password = $_POST["password"] ?? null;
$password_repeat = $_POST["password_repeat"] ?? null;
if (!isset($email) || !isset($username) || !isset($password) || !isset($password_repeat)) {
    header("Location: /");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    toast_message("error", "올바르지 않은 이메일 형식이예요.");
    header("Location: /signup.php");
    exit();
}

$username = htmlspecialchars($username, ENT_QUOTES, "UTF-8");
if (strlen($username) < 4) {
    toast_message("error", "유저이름은 4글자 이상이어야 해요.");
    header("Location: /signup.php");
    exit();
}

if (strlen($password) < 6) {
    toast_message("error", "비밀번호는 6글자 이상이어야 해요.");
    header("Location: /signup.php");
    exit();
}

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
        toast_message("error", "해당 유저이름은 이미 사용하고 있어요.");
        header("Location: /signup.php");
        exit();
    }

    $sql = "insert into users (email, username, password) values (?, ?, SHA2(?, 256))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $username, $password);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "가입성공! 로그인을 진행하세요");

header("Location: /");
?>
