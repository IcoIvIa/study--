<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');


$questions = $db->query(
    "SELECT * FROM questions"
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php foreach($questions as $question): ?>
    <p> <?= h($question['title']) ?></p>

    <a href="/student/questions/show.php?id=<?= h($question['id']) ?>">回答する</a>

<?php endforeach; ?>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>