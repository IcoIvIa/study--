<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';


$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

// 1. URLから問題IDを取得
$questionId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if ($questionId === null) {
    header('Location: /teacher/questions/index.php');
    exit;
}
// 2. その問題の情報を取得
$question = $db->query(
    "SELECT * FROM questions WHERE id = ?",
    [$questionId]
)[0] ?? null;
if ($question === null) {
    header('Location: /teacher/questions/index.php');
    exit;
}

$answers = $db->query(
    "SELECT answers.*, users.name 
    FROM answers 
    JOIN users ON answers.student_id = users.id
    WHERE answers.question_id = ?",
    [$questionId]
);
?>


<!-- フォームのHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<hr>
<h4>問題</h4>
<p><?= h($question['content']) ?></p>
<hr>

<table border="1">
    <?php if(!isset($answers)) : ?>
    <tr>
    <th>回答者</th>
    <th>回答</th>
    <th>結果</th>
    </tr>
    

    <?php foreach ($answers as $answer) : ?>
        <tr>

            <td>
                <p><?= h($answer['name']) ?></p>
            </td>
            <td>
                <p><?= h($answer['answer_text']) ?></p>
            </td>
            <td>
                <p><?= h($answer['is_correct']) ? '正解' : '不正解' ?></p>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<?php else :?>
    <p>回答者なし</p>
<?php endif; ?>

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>