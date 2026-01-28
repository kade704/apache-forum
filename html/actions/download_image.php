<?php
require_once "../functions/db.php";

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header("Location: /");
    exit();
}

$post_id = $_GET['post_id'] ?? null;
if (!isset($post_id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "select image_url from posts where id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();
    if (!$post) {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    $full_path = "../uploads/" . $post->image_url;

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $full_path);

    header("Content-Type: " . $mime_type);
    header("Content-Length: " . filesize($full_path));

    readfile($full_path);
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>
