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



$replies = $db->query(
    "SELECT * FROM message_replies WHERE message_thread_id = ?",
    [$threadId]
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     csrf_verify();
$repliesText = $_POST['repliesText'];
$db->query(
    "INSERT INTO message_replies ( message_thread_id , sender_role , sender_id , body) VALUES (?, ?, ?, ?)",
    [$threadId, 'teacher' , $id , $repliesText]
);
header("Location: /teacher/messages/show.php?id=" . $threadId);
exit;
}

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 
 <?php foreach($replies as $replie): ?>
    <p> <?= h($replie['body']) ?></p>
    <hr>


<?php endforeach; ?>

<form action="" method="post">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <textarea name="repliesText" id="" ></textarea>
    <button type="submit">返信</button>
</form>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>