<?php require_once "includes/session.php"; ?>
<?php
require_once "functions/db.php";
require_once "functions/datetime.php";
require_once "includes/csrf_token.php";

$page = $_GET["page"] ?? null;
if (!isset($page) || !is_numeric($page) || $page < 1) {
    $page = 1;
}

$search_query = $_GET["query"] ?? null;
$search_context = $_GET["context"] ?? null;

$user_id = $_SESSION["user_id"] ?? null;

$offset = ($page - 1) * 10;

enable_exceptions();
try {
    $conn = open_db();

    if (isset($search_query) && isset($search_context)) {
        if (!in_array($search_context, ["title", "content", "user_id"])) {
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
    http_response_code(500);
    echo "서버 오류가 발생했습니다.";
    exit();
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
<main class="mt-10 mx-auto max-w-4xl space-y-4">
    <div class="flex items-center gap-4">
        <a href="/"><button class="btn btn-secondary">초기화</button></a>
        <input id="query" class="flex-1 input" placeholder="Search" onchange="search()"/>
        <select class="select w-30" id="context">
            <option value="title">제목</option>
            <option value="content">내용</option>
            <option value="user_id">유저 ID</option>
        </select>
    </div>
    <div class="table p-4 bg-base-100 rounded-lg shadow-md">
        <div class="table-header-group ">
            <div class="table-row ">
                <div class="p-2 table-cell w-10 opacity-50 border-b">ID</div>
                <div class="p-2 table-cell text-xs opacity-50 border-b">제목</div>
                <div class="p-2 table-cell w-20 text-xs opacity-50 border-b">작성자</div>
                <div class="p-2 table-cell w-30 text-xs opacity-50 border-b">작성일</div>
                <div class="p-2 table-cell w-14 text-xs opacity-50 border-b">조회수</div>
            </div>
        </div>
        <div class="table-row-group">
        <?php for ($i = 0; $i < 10; $i++) {
            $row = $posts->fetch_object();          
            if ($row) {
                $url = "post.php?post_id=" . $row->id;
                $time = formatDistanceToNow($row->created_at);
                echo <<<HTML
                    <a href={$url} class="table-row">
                        <div class='p-2 table-cell text-md w-10 border-b border-base-content/50'> {$row->id}</div>
                        <div class='p-2 table-cell flex-1 text-lg font-semibold truncate border-b border-base-content/50'> {$row->title}</div>
                        <div class='p-2 table-cell w-20 border-b border-base-content/50'> {$row->user_id}</div>
                        <div class='p-2 table-cell w-30 border-b border-base-content/50'> {$time}</div>
                        <div class='p-2 table-cell w-14 border-b border-base-content/50'> {$row->views}</div>
                    </a>
                HTML;
            } else {
                echo <<<HTML
                    <div class="table-row">
                        <div class='p-2 table-cell text-md w-10 border-b border-base-content/50'> - </div>
                        <div class='p-2 table-cell flex-1 text-lg font-semibold truncate border-b border-base-content/50'> - </div>
                        <div class='p-2 table-cell w-20 border-b border-base-content/50'> - </div>
                        <div class='p-2 table-cell w-30 border-b border-base-content/50'> - </div>
                        <div class='p-2 table-cell w-14 border-b border-base-content/50'> - </div>
                    </div>
                HTML;
            }
        } ?>
        </div>
    </div>
    <div class="flex items-center justify-between">
        <div class="join" id="page_list"></div>
        <?php if (isset($user_id)) {
            echo <<<HTML
                <a href="write.php">
                    <button class="btn btn-primary">게시물 생성</button>
                </a>
            HTML;
        } ?>
    </div>
</main>
<?php require_once "includes/footer.php"; ?>
