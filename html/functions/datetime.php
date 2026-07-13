<?php
function formatDistanceToNow(string $datetime): string
{
    $now = new DateTime("now");
    $target = new DateTime($datetime);

    $diff = $now->getTimestamp() - $target->getTimestamp();
    if ($diff < 60) {
        return "방금 전";
    } elseif ($diff < 60 * 60) {
        return round($diff / 60) . "분 전";
    } elseif ($diff < 60 * 60 * 24) {
        return round($diff / (60 * 60)) . "시간 전";
    } else {
        return $target->format("Y-m-d");
    }
}
?>
