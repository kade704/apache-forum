<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /");
    exit();
}

$username = $_SESSION["username"] ?? null;
$title = $_POST["title"] ?? null;
$content = $_POST["content"] ?? null;

if (!isset($username) || !isset($title) || !isset($content)) {
    header("Location: /");
    exit();
}

$title = htmlspecialchars($title, ENT_QUOTES, "UTF-8");
$content = htmlspecialchars($content, ENT_QUOTES, "UTF-8");

$media_file = $_FILES["media_file"] ?? null;
if (isset($media_file) && $media_file["error"] !== UPLOAD_ERR_NO_FILE) {
    if ($media_file["error"] === UPLOAD_ERR_INI_SIZE) {
        toast_message("error", "이미지가 업로드하기에 너무 커요.");
        header("Location: /write.php");
        exit();
    }
    if ($media_file["error"] !== UPLOAD_ERR_OK) {
        toast_message("error", "이미지 업로드중 알수없는 오류가 발생했어요.");
        header("Location: /write.php");
        exit();
    }

    if (!is_dir("../uploads")) {
        mkdir("../uploads");
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $media_type = finfo_file($finfo, $media_file["tmp_name"]);

    if (!(str_starts_with($media_type, "image") || str_starts_with($media_type, "video"))) {
        toast_message("error", "지원하지 않는 미디어 타입이예요.");
        header("Location: /write.php");
        exit();
    }

    $media_id = uniqid();

    $upload_path = "../uploads/" . $media_id;
    move_uploaded_file($media_file["tmp_name"], $upload_path);
}

enable_exceptions();
try {
    $conn = open_db();

    $sql =
        "insert into posts (username, title, content, media_id, media_type) values (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $title, $content, $media_id, $media_type);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}

toast_message("success", "게시물 작성에 성공했어요!");

header("Location: /");
?>
