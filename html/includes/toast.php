<?php
if (!isset($_SESSION["toast_msg"])) {
    return;
}
$toastMsg = htmlspecialchars($_SESSION["toast_msg"]);
$toastType = htmlspecialchars($_SESSION["toast_type"]);

$_SESSION["toast_msg"] = null;
$_SESSION["toast_type"] = null;

if ($toastType == "success") {
    echo <<<HTML
    <div role="alert" class="alert alert-success absolute translate-x-4 translate-y-20">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{$toastMsg}</span>
    </div>
    HTML;
} else if ($toastType == "error") {
    echo <<<HTML
    <div role="alert" class="alert alert-error absolute translate-x-4 translate-y-20">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
      <span>{$toastMsg}</span>
    </div>
    HTML;
} else {
    echo <<<HTML
    <div role="alert" class="alert alert-info absolute translate-x-4 translate-y-20">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info h-6 w-6 shrink-0">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      <span>{$toastMsg}</span>
    </div>
    HTML;
}
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            document.querySelectorAll(".alert").forEach(x => x.remove());
        }, 3000);
    });
</script>
