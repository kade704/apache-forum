<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$post_id = $_POST["post_id"] ?? null;
$content = $_POST["content"] ?? null;
if (!isset($post_id) || !isset($content)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

// XXS 방지
$content = htmlspecialchars($content, ENT_QUOTES, "UTF-8");

enable_exceptions();
try {
    $conn = open_db();

    $sql = "insert into comments (user_id, content, post_id) values (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $user_id, $content, $post_id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "댓글 작성에 성공했습니다.");

header("Location: /post.php?post_id=" . $post_id);
?>
