<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';


$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

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

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>