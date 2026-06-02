<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();
$auth->requireRole('teacher');


$messages = $db->query(
    "SELECT message_threads.*, users.name
     FROM message_threads
     INNER JOIN users
        ON message_threads.student_id = users.id"
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<table border="1">
    <tr>
        <th>タイトル</th>
        <th>生徒名</th>
        <th>質問日</th>
        <th></th>
    </tr>
    <?php foreach ($messages as $message): ?>
        <tr>
            <td>
                <p> <?= h($message['title']) ?></p>
            </td>
            <td>
                <p> <?= h($message['name']) ?> </p>
            </td>
            <td>
                <p> <?= h($message['created_at']) ?></p>
            </td>
            <td>
                <a href="/teacher/messages/show.php?id=<?= h($message['id']) ?>">見る</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>