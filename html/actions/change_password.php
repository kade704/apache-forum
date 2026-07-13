<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$curr_password = $_POST["curr_password"] ?? null;
$new_password = $_POST["new_password"] ?? null;
if (!isset($curr_password) || !isset($new_password)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

// 비밀번호 최소 8글자, 최대 20글자
// 최소 1개의 영문, 1개의 숫자, 1개의 특수문자 포함
if (
    strlen($new_password) < 8 ||
    strlen($new_password) > 20 ||
    !preg_match("/[a-zA-Z]/", $new_password) ||
    !preg_match("/[0-9]/", $new_password) ||
    !preg_match("/[\W_]/", $new_password)
) {
    toast_message(
        "error",
        "비밀번호는 8~20자이며, 영문, 숫자, 특수문자를 모두 포함해야 합니다."
    );
    header("Location: /profile.php?user_id=" . $user_id);
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
    if (!$found || !password_verify($curr_password, $found->password_hash)) {
        toast_message("error", "현재 비밀번호가 맞지 않습니다.");
        header("Location: /profile.php?user_id=" . $user_id);
        exit();
    }

    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    $sql = "update users set password_hash = ? where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $password_hash, $user_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "비밀번호를 성공적으로 변경했습니다.");
header("Location: /profile.php?user_id=" . $user_id);
?>
