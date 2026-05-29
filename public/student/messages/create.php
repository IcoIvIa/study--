<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');
$studentid = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = $_POST['title'];
    $body  = $_POST['body'];

    // 1. message_threadsにINSERT
    $messageThreads = $db->query(
        "INSERT INTO message_threads (student_id, title) VALUES(? ,?)",
        [$studentid, $title]
    );
    // 3. message_repliesにINSERT
    $threadId = $db->lastInsertId();

    $messageReplies = $db->query(
        "INSERT INTO message_replies (message_thread_id, sender_role, sender_id, body) VALUES(?, ?, ?, ?)",
        [$threadId, 'student', $studentid, $body]
    );
    header("Location: /student/dashboard.php");
    exit;
}

?>


<!-- フォームのHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<div class="container">
    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <h2 class="text-center">質問投稿フォーム</h2>
        <hr>
        <p>タイトルを入力してください</p>
        
        <input type="text" name="title">
        
        <p>本文を入力してください</p>
        <textarea name="body" class="text-area"></textarea>

        <input class="mx-auto" type="submit" value="質問を投稿する">
        <hr>
    </form>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>