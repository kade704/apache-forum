<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/csrf_check.php";

$user_id = $_POST["user_id"] ?? null;
$email = $_POST["email"] ?? null;
$password = $_POST["password"] ?? null;
$password_repeat = $_POST["password_repeat"] ?? null;
if (!isset($user_id) || !isset($email) || !isset($password) || !isset($password_repeat)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

// XSS 방지
$user_id = htmlspecialchars($user_id, ENT_QUOTES, "UTF-8");
$email = htmlspecialchars($email, ENT_QUOTES, "UTF-8");


// 아이디 최소 4글자
if (strlen($user_id) < 4) {
    toast_message("error", "아이디는 4글자 이상이어야 합니다.");
    header("Location: /signup.php");
    exit();
}


// 비밀번호 최소 8글자, 최대 20글자
// 최소 1개의 영문, 1개의 숫자, 1개의 특수문자 포함
if (
    strlen($password) < 8 ||
    strlen($password) > 20 ||
    !preg_match("/[a-zA-Z]/", $password) ||
    !preg_match("/[0-9]/", $password) ||
    !preg_match("/[\W_]/", $password)
) {
    toast_message(
        "error",
        "비밀번호는 8~20자이며, 영문, 숫자, 특수문자를 모두 포함해야 합니다."
    );
    header("Location: /signup.php");
    exit();
}

if ($password != $password_repeat) {
    toast_message("error", "두 비밀번호가 맞지 않습니다.");
    header("Location: /signup.php");
    exit();
}

// 이메일 형식 검증
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    toast_message("error", "올바르지 않은 이메일 형식입니다.");
    header("Location: /signup.php");
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
    if ($result->num_rows > 0) {
        toast_message("error", "해당 아이디는 이미 사용하고 있습니다.");
        header("Location: /signup.php");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "insert into users (id, email, password_hash) values (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user_id, $email, $password_hash);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "가입성공, 로그인을 해주세요");

header("Location: /");
?>
