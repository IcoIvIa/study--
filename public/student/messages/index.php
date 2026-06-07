<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$auth = new Auth();
$auth->requireRole('student');

$db = new Database();
$student_id = $_SESSION['user_id'];

$pageTitle = "質問ページ||study!!";

$messages = $db->query(
    "SELECT 
        message_threads.id,
        message_threads.title,
        message_replies.body,
        message_replies.created_at
    FROM message_threads
    JOIN message_replies 
    ON message_threads.id = message_replies.message_thread_id
    WHERE message_replies.id = (
    SELECT MIN(id) 
    FROM message_replies 
    WHERE message_thread_id = message_threads.id
        
    )
AND student_id = ?
ORDER BY message_replies.created_at DESC",
    [$student_id]
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<div class="container">
    <hr>

    <?php $flash = flash_get(); ?>
    <?php if ($flash): ?>
        <p><?= h($flash['message']) ?></p>
    <?php endif; ?>

    <h2>あなたの質問一覧</h2>
    <hr>
    <?php foreach ($messages as $message): ?>

        <a href="/student/messages/show.php?id=<?= h($message['id']) ?>">
            <div class="card card-sm">
                <h4 class="tight">
                    <?= h($message['title']) ?>
                    <label class="date">
                        <?= " : " . h($message['created_at']) ?>
                    </label>
                </h4>

                <p><?= h($message['body']) ?></p>
                <div class="text-center">
                    <span class="badge-correct">確認する</span>
                </div>
            </div>
        </a>

    <?php endforeach; ?>

    <hr>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>