<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"];
if (!isset($username)) {
    header("Location: /");
    exit();
}

$title = $_POST["title"];
if (!isset($title)) {
    header("Location: /");
    exit();
}

$title = htmlspecialchars($title, ENT_QUOTES, "UTF-8");

$content = $_POST["content"];
if (isset($content)) {
    $content = htmlspecialchars($content, ENT_QUOTES, "UTF-8");
}

$image_file = $_FILES["image_file"];
if (isset($image_file) && $image_file["error"] === UPLOAD_ERR_OK) {
    $image_file_name = $image_file["name"];
    $image_file_ext = strtolower(
        pathinfo($image_file_name, PATHINFO_EXTENSION),
    );

    $image_file_tmp = $image_file["tmp_name"];

    $new_file_name = uniqid() . "." . $image_file_ext;

    $upload_path = "../uploads/" . $new_file_name;
    $image_url = "/uploads/" . $new_file_name;

    move_uploaded_file($image_file_tmp, $upload_path);
}

enable_exceptions();
try {
    $conn = open_db();

    $sql =
        "insert into posts (username, title, content, image_url) values (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $title, $content, $image_url);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "게시물 작성에 성공했습니다.");

header("Location: /");
?>
