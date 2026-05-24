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
    [$studentid , $title]
);
    // 3. message_repliesにINSERT
    $threadId = $db->lastInsertId();

    $messageReplies = $db->query(
    "INSERT INTO message_replies (message_thread_id, sender_role, sender_id, body) VALUES(?, ?, ?, ?)",
[$threadId, 'student', $studentid, $body]
    );
        header("Location: /student/messages/index.php");
    exit;
}

?>


<!-- フォームのHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<form action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
<input type="text" name="title" id="">
<textarea name="body" id=""></textarea>

<input type="submit" value="送信">
</form>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>