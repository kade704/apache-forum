<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";
require_once "functions/datetime.php";

$page = $_GET["page"] ?? null;
if (!isset($page)) {
    $page = 1;
}

$search_query = $_GET["query"] ?? null;
$search_context = $_GET["context"] ?? null;

$username = $_SESSION["username"] ?? null;

$offset = ($page - 1) * 10;

enable_exceptions();
try {
    $conn = open_db();

    if (isset($search_query) && isset($search_context)) {
        if (!in_array($search_context, ["title", "content", "username"])) {
            exit();
        }

        $sql = "select * from posts where {$search_context} like ? order by created_at desc limit ?,10";
        $search_query = "%" . $search_query . "%";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $search_query, $offset);
        $stmt->execute();

        $posts = $stmt->get_result();
    } else {
        $sql = "select * from posts order by created_at desc limit ?,10";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $offset);
        $stmt->execute();

        $posts = $stmt->get_result();
    }
} catch (mysqli_sql_exception $e) {
    error_log("MySQLi Error: " . $e->getMessage());
}
?>

<?php require_once "includes/header.php"; ?>
<script>
    function search() {
        const query = document.getElementById('query').value;
        const context = document.getElementById('context').value;
        location.href = "/?query=" + encodeURI(query) + "&context=" + encodeURI(context);
    }
    function initPageButtons() {
        const params = new URLSearchParams(window.location.search);
        const page = params.get('page') ?? 1;
        const page_list = document.getElementById("page_list");

        const start_idx = page > 4 ? page - 4 : 1;

        let html = "";
        for (let idx = start_idx; idx < start_idx + 10; idx++) {
            const url = `?page=${idx}`;
            const enabled = page == idx ? "btn-primary" : "";
            html += `<a href=${url}><button class="join-item btn ${enabled}">${idx}</button></a>`
        }
        page_list.innerHTML = html;
    }
    document.addEventListener('DOMContentLoaded', () => {
        initPageButtons();
    });
</script>
<main class="mt-10 mx-auto max-w-3xl space-y-4">
    <div class="flex items-center gap-4">
        <a href="/"><button class="btn btn-secondary">초기화</button></a>
        <input id="query" class="flex-1 input" placeholder="Search" onchange="search()"/>
        <select class="select w-30" id="context">
            <option value="title">제목</option>
            <option value="content">내용</option>
            <option value="username">유저이름</option>
        </select>
    </div>
    <ul class="list bg-base-100 rounded-box shadow-md">
        <li class="p-4 pb-2 text-xs opacity-60 tracking-wide">게시글 목록</li>
        <?php for ($i = 0; $i < 10; $i++) {
            $row = $posts->fetch_object();
            if ($row) {
                $url = "post.php?id=" . $row->id;
                $time = formatDistanceToNow($row->created_at);
                echo <<<HTML
                <a href={$url}>
                    <li class="list-row flex items-center items-center">
                        <p class='text-md w-10'> {$row->id}</p>
                        <p class='flex-1 text-lg font-semibold truncate'> {$row->title}</p>
                        <div class='flex items-center gap-2'>
                            <p class='text-sm font-semibold opacity-50'>{$row->username}</p>
                            <p>·</p>
                            <p class='text-sm opacity-50'>{$time}</p>
                        </div>
                    </li>
                </a>
                HTML;
            } else {
                echo <<<HTML
                <li class="list-row items-center h-15">
                </li>
                HTML;
            }
        } ?>
    </ul>
    <div class="flex items-center justify-between">
        <div class="join" id="page_list"></div>
        <a href="write.php">
            <button class="btn btn-primary">게시물 생성</button>
        </a>
    </div>
</main>
<?php require_once "includes/footer.php"; ?>
