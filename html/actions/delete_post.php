<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$post_id = $_POST["post_id"] ?? null;
if (!isset($post_id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "delete from posts where id=? and user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $post_id, $user_id);
    $stmt->execute();
    if ($stmt->affected_rows === 0) {
        http_response_code(403);
        echo "권한이 없습니다.";
        exit();
    }
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "게시글을 성공적으로 삭제했습니다.");

header("Location: /");
?>
