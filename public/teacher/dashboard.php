<?php

session_start();

require_once __DIR__ .'/../../src/Auth.php';
require_once __DIR__ .'/../../src/helpers.php';
require_once __DIR__ .'/../../src/Database.php';

$db = new Database();
$auth =new Auth();

$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];

$unreadCount = $db->query(
    "SELECT COUNT(*) as count
    FROM message_threads
    WHERE teacher_id IS NULL"
)[0];

$questionStats = $db->query(
    "SELECT questions.title,
        COUNT(answers.id) as total,
        SUM(answers.is_correct) as correct
    FROM questions
    LEFT JOIN answers ON questions.id = answers.question_id
    WHERE questions.teacher_id = ?
    GROUP BY questions.id",
    [$teacherId]
);

$pageTitle = '先生ページ|study!!';
?>

<?php require_once __DIR__.'/../../templates/header.php'; ?>

<!-- ダッシュボードの中身 -->

<p>未返信の質問数：<?= $unreadCount['count'] ?></p>

<?php foreach($questionStats as $stat): ?>
<p> <?= h($stat['title']) ?></p>
<p>回答数：<?= $stat['total'] ?></p>

// 正答率
<?php   $rate = $stat['total'] >0 
    ? round($stat['correct']/$stat['total'] * 100)
    : 0; ?>

<p>正答率：<?= $rate ?></p>
<?php endforeach; ?>


<?php require_once __DIR__.'/../../templates/footer.php'; ?>
