<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/csrf_check.php";

$user_id = $_POST["user_id"] ?? null;
$password = $_POST["password"] ?? null;
if (!isset($user_id) || !isset($password)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select * from users where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $found = $result->fetch_object();
    if (!$found) {
        toast_message("error", "아이디 또는 비밀번호가 맞지 않습니다.");
        header("Location: /login.php");
        exit();
    }
    $password_hash = $found->password_hash;
    if (!password_verify($password, $password_hash)) {
        toast_message("error", "아이디 또는 비밀번호가 맞지 않습니다.");
        header("Location: /login.php");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

session_regenerate_id(true);
$_SESSION["user_id"] = $user_id;
$_SESSION["csrf_token"] = bin2hex(random_bytes(16));

toast_message("success", "{$user_id}님 환영합니다!");

header("Location: /");
?>
