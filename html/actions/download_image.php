<?php
require_once "../functions/db.php";

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);
    echo "허용되지 않은 요청 방식";
    exit();
}

$post_id = $_GET['post_id'] ?? null;
if (!isset($post_id)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select image_path, image_type from posts where id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();
    if (!$post || !isset($post->image_path) || !isset($post->image_type)) {
        http_response_code(404);
        echo "이미지를 찾을 수 없음";
        exit();
    }

    $upload_dir = "/data/images/";
    $full_path = $upload_dir . $post->image_path;

    header("Content-Type: " . $post->image_type);

    readfile($full_path);
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>