<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$email = $_POST["email"] ?? null;
$description = $_POST["description"] ?? null;
if (!isset($email) || !isset($description)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

// 이메일 형식 검증
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    toast_message("error", "올바르지 않은 이메일 형식입니다.");
    header("Location: /profile.php?user_id=" . $user_id);
    exit();
}

// XXS 방지
$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");
$description = htmlspecialchars($description, ENT_QUOTES, "UTF-8");

enable_exceptions();
try {
    $conn = open_db();

    $sql = "update users set email = ?, description = ? where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $description, $user_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "프로필을 성공적으로 수정했습니다.");

header("Location: /profile.php?user_id=" . $user_id);
?>
