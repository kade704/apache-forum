<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$curr_password = $_POST["curr_password"] ?? null;
if (!isset($curr_password)) {
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
        http_response_code(403);
        echo "권한 없음";
        exit();
    }

    $password_hash = $found->password_hash;
    if (!password_verify($curr_password, $password_hash)) {
        toast_message("error", "현재 비밀번호가 맞지 않습니다.");
        header("Location: /profile.php?user_id=" . $user_id);
        exit();
    }

    $sql = "delete from users where id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        http_response_code(403);
        echo "권한 없음";
        exit();
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

session_unset();
session_destroy();

header("Location: /");
?>
