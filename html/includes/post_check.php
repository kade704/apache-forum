<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo "허용되지 않은 요청 방식";
    exit();
}
?>