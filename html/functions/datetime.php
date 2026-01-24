<?php
function formatDistanceToNow(string $datetime): string
{
    $timezone = new DateTimeZone("Asia/Seoul");

    $now = new DateTime("now", $timezone);
    $target = new DateTime($datetime, $timezone);

    $diff = $now->getTimestamp() - $target->getTimestamp();
    if ($diff < 30) {
        return "방금 전";
    } elseif ($diff < 90) {
        return "1분 전";
    } elseif ($diff < 2670) {
        return round($diff / 60) . "분 전";
    } elseif ($diff < 5370) {
        return "약 1시간 전";
    } elseif ($diff < 75600) {
        return "약 " . round($diff / 3600) . "시간 전";
    } elseif ($diff < 129600) {
        return "1일 전";
    } elseif ($diff < 2160000) {
        return round($diff / 86400) . "일 전";
    } elseif ($diff < 3888000) {
        return "약 1달 전";
    } elseif ($diff < 27648000) {
        return round($diff / 2592000) . "달 전";
    } elseif ($diff < 47304000) {
        return "약 1년 전";
    } else {
        return round($diff / 31536000) . "년 전";
    }
}
?>
