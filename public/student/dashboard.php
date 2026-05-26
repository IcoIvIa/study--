<?php

session_start();

require_once __DIR__ .'/../../src/Auth.php';
require_once __DIR__ .'/../../src/helpers.php';
require_once __DIR__ .'/../../src/Database.php';

$db = new Database();
$auth =new Auth();

$auth->requireRole('student');

$studentId = $_SESSION['user_id'];
// 回答履歴（最新5件

$recentAnswers = $db->query(
    "SELECT answers.*, questions.title
    FROM answers
    JOIN questions ON answers.question_id = questions.id
    WHERE answers.student_id = ?
    ORDER BY answers.created_at DESC
    LIMIT 5",
    [$studentId]
);
// 正答率
$stats = $db->query(
    "SELECT
        COUNT(*) as total,
        SUM(is_correct) as correct
    FROM answers
    WHERE student_id = ?",
    [$studentId]
)[0];

$rate = $stats['total'] >0 
    ? round($stats['correct']/$stats['total'] * 100)
    : 0;

$pageTitle = '生徒ページ|study!!';
?>

<?php require_once __DIR__.'/../../templates/header.php'; ?>

<!-- ダッシュボードの中身 -->
<p>総回答数：<?=  $stats['total'] ?></p>
<p>正答率：<?= $rate ?></p>

<?php foreach ($recentAnswers as $answer): ?>
    <p><?=  h($answer['title']) ?></p>
    <p><?=  $answer['is_correct'] ? '正解' : '不正解' ?></p>
<?php endforeach; ?>

<?php require_once __DIR__.'/../../templates/footer.php'; ?>

