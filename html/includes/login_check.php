<?php
$user_id = $_SESSION["user_id"] ?? null;
if (!isset($user_id)) {
    http_response_code(401);
    echo "로그인 필요";
    exit();
}
?>