<?php
if (
    !isset($_POST["csrf_token"], $_SESSION["csrf_token"]) ||
    !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
) {
    http_response_code(403);
    echo "CSRF 토큰이 유효하지 않습니다.";
    exit();
}  
?>