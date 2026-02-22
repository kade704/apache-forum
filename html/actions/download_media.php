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

    $sql = "select * from posts where id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();
    if (!$post) {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    $full_path = "../uploads/" . $post->media_id;

    if (str_starts_with($post->media_type, "image")) {
        header("Content-Type: " . $post->media_type);
        header("Content-Length: " . filesize($full_path));

        readfile($full_path);
    } else if (str_starts_with($post->media_type, "video")) {
        $filesize = filesize($full_path);
        $file_stream = fopen($full_path, 'r');

        // HTTP Range 요청 확인
        if (isset($_SERVER['HTTP_RANGE'])) {
            // 1. "bytes=start-end" 형태의 Range 파싱
            preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches);
            $start = intval($matches[1]);
            $end = isset($matches[2]) ? intval($matches[2]) : $filesize - 1;

            // Range가 파일 크기보다 크면 보정
            if ($start > $filesize) $start = 0;
            if ($end >= $filesize) $end = $filesize - 1;

            $length = $end - $start + 1;

            // 2. 206 Partial Content 헤더 전송
            header('HTTP/1.1 206 Partial Content');
            header('Content-Range: bytes ' . $start . '-' . $end . '/' . $filesize);
            header('Content-Length: ' . $length);
            header('Accept-Ranges: bytes');

            // 3. 파일 포인터 이동 후 해당 부분만 읽기
            fseek($file_stream, $start);
            echo fread($file_stream, $length);
        } else {
            // 전체 파일 전송 (처음 재생 시)
            header('Content-Length: ' . $filesize);
            header('Accept-Ranges: bytes');
            readfile($full_path);
        }
        fclose($file_stream);
    }
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>
