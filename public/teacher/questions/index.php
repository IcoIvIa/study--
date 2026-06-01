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

if (isset($_POST['action'])) {
    csrf_verify();
    $id = $_POST['id'];
    $db->query(
        "DELETE FROM questions WHERE id = ? AND teacher_id = ? ",
        [$id, $teacherId]

    );
    header('Location: /teacher/questions/index.php');
    exit;
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<?php foreach ($questions as $question): ?>
    <a href="http://localhost:8080/teacher/questions/edit.php?id=<?= h($question['id']) ?>">
        <p> <?= h($question['title']) ?></p>
    </a>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= h($question['id']) ?>">
        <button type="submit" name="action" value="delete">削除</button>

    </form>

<?php endforeach; ?>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>