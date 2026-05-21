<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];

$questions = $db->query(
    "SELECT * FROM questions WHERE teacher_id = ?",
    [$teacherId]
);
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php foreach($questions as $question): ?>
    <p> <?= h($question['title']) ?></p>
<?php endforeach; ?>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>