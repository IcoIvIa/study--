<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';


$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

$categoryId = $_GET['category_id'];
$categories = $db->query(
    "SELECT name FROM categories WHERE id = ?",
    [$categoryId]
)[0];

$unanswered = $db->query(
    "SELECT users.name FROM users
        WHERE users.role = 'student'
        AND users.id NOT IN (
            SELECT DISTINCT answers.student_id
            FROM answers
            JOIN questions ON answers.question_id = questions.id
            WHERE questions.category_id = ? )",
    [$categoryId]
);
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<!-- ここからHTML -->
<h2><?= h($categories['name']) ?> の未回答者一覧</h2>

<hr>

<?php foreach ($unanswered as $user) : ?>
    <ul>
        <li>
            <?= h($user['name']) ?>
        </li>
    </ul>
<?php endforeach; ?>

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>