<?php require_once "../includes/session.php"; ?>
<?php

require_once "../includes/post_check.php";
require_once "../includes/csrf_check.php";

session_unset();
session_destroy();

header("Location: /");
?>
