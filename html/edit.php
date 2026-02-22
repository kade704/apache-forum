<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";

$username = $_SESSION["username"] ?? null;

if (!$username) {
    header("Location: /login.php");
    exit();
}

$post_id = $_GET["post_id"] ?? null;
if (!isset($post_id)) {
    header("Location: /");
    exit();
}

enable_exceptions();
try {
    $conn = open_db();

    $sql =
        "select * from posts where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $post = $result->fetch_object();

    if (!isset($post) || $post->username != $username) {
        header("Location: /");
        exit();
    }
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>

<?php require_once "includes/header.php"; ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('file-input');
    const preview = document.getElementById('preview');
    const defaultImage = document.getElementById('default-image');
    const urlParams = new URLSearchParams(window.location.search);

    preview.src = "/actions/download_media.php?post_id=" + urlParams.get("post_id");
    preview.onload = () => {
        defaultImage.style.display = "none";
    }

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(event) {
            preview.src = event.target.result;
            preview.style.display = 'block';
          }
          reader.readAsDataURL(file);
          defaultImage.style.display = "none";
        }
    });
  });
</script>
<main class="pt-20 mx-auto max-w-3xl">
    <?php echo <<<HTML
    <form method="POST" enctype="multipart/form-data" action="actions/edit_post.php" class="p-6 flex flex-col bg-base-100 rounded-lg shadow-md">
        <div class="flex justify-between">
            <h2 class='text-xl font-bold'>게시글 수정</h2>
            <h2 class='opacity-50'>{$username}</h2>
        </div>
        <div class="divider my-2"></div>
        <div class="space-y-4">
            <input type="text" name="post_id" hidden value={$post->id} />
            <input type="text" name="title" class="flex-1 input w-full"  placeholder="제목" required value={$post->title} />
            <textarea type="text" name="content" class="textarea w-full min-h-48" placeholder="내용">{$post->content}</textarea>
            <div class="p-2 w-full border border-content border-dashed rounded-md">
                <p class="text-sm">사진 등록</p>
                <div class="w-full my-4 flex flex-col items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 24 24" id="default-image" class="opacity-50">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1"><path stroke-dasharray="66" stroke-width="2" d="M3 14v-9h18v14h-18v-5"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.6s" values="66;0"/></path><path stroke-dasharray="26" stroke-dashoffset="26" d="M3 16l4 -3l3 2l6 -5l5 4"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.6s" dur="0.4s" to="0"/></path></g><g fill="currentColor"><circle cx="7.5" cy="9.5" r="1.5" opacity="0"><animate fill="freeze" attributeName="opacity" begin="1s" dur="0.2s" to="1"/></circle><path fill-opacity="0" d="M3 16l4 -3l3 2l6 -5l5 4v5h-18Z"><animate fill="freeze" attributeName="fill-opacity" begin="1.2s" dur="0.4s" to="1"/></path></g>
                    </svg>
                    <img id="preview" class="rounded-lg" src="#" alt=""/>
                    <input type="file"  name="image_file" class="file-input" id="file-input" accept="image/*"/>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-full">등록</button>
        </div>
    </form>
    HTML; ?>
</main>
<?php require_once "includes/footer.php"; ?>
