<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
$auth = new Auth();
$auth->requireRole('student');

$db = new Database();
$threadId = $_GET['id'] ?? null;

$pageTitle = "質問ページ||study!!";

$message = $db->query(
    "SELECT * FROM message_threads WHERE id = ?",
    [$threadId]
);


$replies = $db->query(
    "SELECT 
    message_replies.*, 
    users.name AS sender_name 
    FROM message_replies 
    JOIN users ON message_replies.sender_id = users.id 
    WHERE message_replies.message_thread_id = ?",
    [$threadId]
);
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<!-- ここからHTML -->
<div class="container">

    <h2><?= h($message[0]['title']) ?></h2>
    <hr>
    <?php foreach ($replies as $reply): ?>

        <div><?php if ($reply['sender_role'] === 'teacher'): ?>
                <div class="date"><?= h($reply['sender_name']) ?>の回答 : <?= h($reply['created_at']) ?></div>
            <?php else: ?>
                <div class="date">あなたの質問 : <?= h($reply['created_at']) ?></div>
            <?php endif; ?>
        </div>

        <p class="indent"><?= h($reply['body']) ?>
        </p>

    <?php endforeach; ?>

    <div>
        <a href="/student/messages/index.php">質問一覧に戻る</a>
    </div>

    <hr>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>