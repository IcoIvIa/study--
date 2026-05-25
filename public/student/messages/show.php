<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();
$id = $_SESSION['user_id'];
$auth->requireRole('student');
$threadId = $_GET['id'];


$replies = $db->query(
    "SELECT * FROM message_replies WHERE message_thread_id = ?",
    [$threadId]
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php foreach($replies as $replie): ?>
    <p> <?= h($replie['body']) ?></p>
    <hr>

<?php endforeach; ?>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>