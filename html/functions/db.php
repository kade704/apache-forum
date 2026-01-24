<?php
function open_db()
{
    $host = getenv("DB_HOST");
    $user = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");
    $dbname = getenv("DB_DATABASE");

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        echo "MySql connection failed";
        return null;
    }

    return $conn;
}

function enable_exceptions()
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}
?>
