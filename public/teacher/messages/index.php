<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();
$auth->requireRole('teacher');


$messages = $db->query(
    "SELECT * FROM message_threads "
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php foreach($messages as $message): ?>
    <p> <?= h($message['title']) ?></p>

<a href="/teacher/messages/show.php?id=<?= h($message['id']) ?>">見る</a>

<?php endforeach; ?>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>