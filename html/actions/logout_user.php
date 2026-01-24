<?php require_once "../includes/session.php"; ?>
<?php
$_SESSION["username"] = null;

header("Location: /");
?>
