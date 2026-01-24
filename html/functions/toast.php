<?php
function toast_message(string $type, string $msg)
{
    $_SESSION["toast_type"] = $type;
    $_SESSION["toast_msg"] = $msg;
}
?>
