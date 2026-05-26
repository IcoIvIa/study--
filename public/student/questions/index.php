<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');

$perPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$total = $db->query("SELECT COUNT(*) as count FROM questions")[0]['count'];
$totalPages = ceil($total / $perPage);

$questions = $db->query(
    "SELECT * FROM questions LIMIT ? OFFSET ?",
    [$perPage, $offset]
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
 <?php foreach($questions as $question): ?>
    <p> <?= h($question['title']) ?></p>

    <a href="/student/questions/show.php?id=<?= h($question['id']) ?>">回答する</a>

<?php endforeach; ?>

<!-- ページネーション -->
 <?php if($page > 1): ?>
    <a href="?page=<?= $page - 1 ?>">前へ</a>
<?php endif; ?>

<?php if ($page < $totalPages): ?>
    <a href="?page=<?= $page + 1 ?>">次へ</a>
<?php endif; ?>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>