<?php require_once "../includes/session.php"; ?>
<?php
require_once "../functions/db.php";
require_once "../functions/toast.php";

require_once "../includes/post_check.php";
require_once "../includes/login_check.php";
require_once "../includes/csrf_check.php";

$title = $_POST["title"] ?? null;
$content = $_POST["content"] ?? null;
if (!isset($title) || !isset($content)) {
    http_response_code(400);
    echo "필수 입력값이 누락됨";
    exit();
}

// XSS 방지
$title = htmlspecialchars($title, ENT_QUOTES, "UTF-8");
$content = htmlspecialchars($content, ENT_QUOTES, "UTF-8");

// 줄바꿈 처리
$content = nl2br($content, false);

$image_file = $_FILES["image_file"] ?? null;
if (isset($image_file) && $image_file["error"] === UPLOAD_ERR_OK) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $image_type = finfo_file($finfo, $image_file["tmp_name"]);

    $type_map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!isset($type_map[$image_type])) {
        http_response_code(400);
        echo "허용되지 않은 이미지 형식";
        exit();
    }

    $image_id = bin2hex(random_bytes(8));
    $image_ext = $type_map[$image_type];
    $image_path = $image_id . "." . $image_ext;

    $upload_dir = "/data/images/";
    $upload_path = $upload_dir . $image_path;

    if (!move_uploaded_file($image_file["tmp_name"], $upload_path)) {
        http_response_code(500);
        echo "이미지 업로드 실패";
        exit();
    }
} else {
    $image_path = null;
    $image_type = null;
}

enable_exceptions();
try {
    $conn = open_db();

    $sql = "insert into posts (user_id, title, content, image_path, image_type) values (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $user_id, $title, $content, $image_path, $image_type);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
}

toast_message("success", "게시물 작성에 성공했습니다.");

header("Location: /");
?>
