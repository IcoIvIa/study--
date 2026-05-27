<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('teacher');

// 1. URLから問題IDを取得
$questionId = $_GET['id'];
// 2. その問題の情報を取得
$question = $db->query(
    "SELECT * FROM questions WHERE id = ?",
    [$questionId]
)[0] ?? null;
if ($question === null) {
    header('Location: /teacher/questions/index.php');
    exit;
}





$ansers = $db->query(
    "SELECT answers.*, users.name 
FROM answers 
JOIN users ON answers.student_id = users.id
WHERE answers.question_id = ?",
    [$questionId]
);
?>


// 3. その問題への全生徒の回答一覧を取得して表示

<!-- フォームのHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<?php foreach($ansers as $anser) : ?>
    <p><?= h($anser['name']) ?></p>
    <p><?= h($anser['answer_text']) ?></p>
    <p><?= $anser['is_correct'] ? '正解' : '不正解' ?></p>
<?php endforeach; ?>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>