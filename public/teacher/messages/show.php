<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$auth = new Auth();

$auth->requireRole('teacher');

$db = new Database();
$id = $_SESSION['user_id'] ?? null;
$threadId = $_GET['id'] ?? null;

$errors = [];

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

    $repliesText = trim($_POST['repliesText'] ?? '');

    $validator = new Validator($_POST);
    $validator->required('repliesText', '返信内容');

      if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {
    $db->execute(
        "INSERT INTO message_replies ( message_thread_id , sender_role , sender_id , body) VALUES (?, ?, ?, ?)",
        [$threadId, 'teacher', $id, $repliesText]
    );
    flash_set('返信しました');
    header("Location: /teacher/messages/show.php?id=" . $threadId);
    exit;
}
}

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php $flash = flash_get(); ?>
<?php if ($flash): ?>
    <p><?= h($flash['message']) ?></p>
<?php endif; ?>

<hr>

<h3><?= h($message[0]['title']) ?></h3>
<table border="1">
    <tr>
        <th>本文</th>
        <th>日付</th>
        <th>投稿者</th>
    </tr>
    <?php foreach ($replies as $reply): ?>

        <tr>
            <td>
                <p> <?= h($reply['body']) ?></p>
            </td>
            <td>
                <p> <?= h($reply['created_at']) ?></p>
            </td>
            <td>
                <p> <?= h($reply['sender_name']) ?></p>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<h4>返信を入力</h4>

<form action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <textarea name="repliesText" id=""></textarea><?php show_error($errors ?? [], 'repliesText')  ?>
    <button type="submit">返信</button>
</form>
<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>