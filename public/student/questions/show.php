<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();



$auth->requireRole('student');
// 1. URLからidを取得
$id = $_GET['id'];
// 2. DBから問題を取得
$questions = $db->query(
    "SELECT * FROM questions WHERE id = ?",
    [$id]
);
$question = $questions[0] ?? null;
if ($question === null){
    if ($question === null) {
    header('Location: /student/questions/index.php');
    exit;
}
}
// 4. POST処理（回答を保存・正誤判定）

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $answer = $_POST['answer'];
    
    // 正誤判定
    $isCorrect = ($answer === $question['correct_answer']) ? 1 : 0;
    
    // answersテーブルにINSERT
    $db->query(
    "INSERT INTO answers(question_id, student_id, answer_text, is_correct) VALUES ( ? , ? , ? , ?)",
    [$id, $_SESSION['user_id'], $answer ,$isCorrect]

);    
header('Location: /student/questions/index.php');
    exit;}


?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->

<h2><?= h($question['title']) ?></h2>
<p><?= h($question['content']) ?></p>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="text" name="answer">
    <button type="submit">回答する</button>
</form>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>