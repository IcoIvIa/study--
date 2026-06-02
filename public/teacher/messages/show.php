<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();
$id = $_SESSION['user_id'];
$auth->requireRole('teacher');
$threadId = $_GET['id'];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $repliesText = $_POST['repliesText'];
    $db->query(
        "INSERT INTO message_replies ( message_thread_id , sender_role , sender_id , body) VALUES (?, ?, ?, ?)",
        [$threadId, 'teacher', $id, $repliesText]
    );
    header("Location: /teacher/messages/show.php?id=" . $threadId);
    exit;
}

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <hr>
<h3><?= $message[0]['title'] ?></h3>
<table border="1">
    <tr>
        <th>本文</th>
        <th>日付</th>
        <th>投稿者</th>
    </tr>
    <?php foreach ($replies as $replie): ?>

        <tr>
            <td>
                <p> <?= h($replie['body']) ?></p>
            </td>
            <td>
                <p> <?= h($replie['created_at']) ?></p>
            </td>
            <td>
                <p> <?= h($replie['sender_name']) ?></p>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h4>返信を入力</h4>

<form action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <textarea name="repliesText" id=""></textarea>
    <button type="submit">返信</button>
</form>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>