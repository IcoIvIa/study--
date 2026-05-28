<?php

session_start();

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');

$studentId = $_SESSION['user_id'];

// 履歴（最新5件
$questions = $db->query(
    "SELECT * FROM questions 
    ORDER BY id DESC
    LIMIT 5"
);

$messages = $db->query(
    "SELECT * FROM message_threads 
    WHERE student_id = ?
    ORDER BY id DESC
    LIMIT 5",
    [$studentId]
);

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

// 未回答の問題
$unansweredQuestions = $db->query(
    "SELECT * FROM questions
    WHERE id NOT IN (
        SELECT question_id FROM answers WHERE student_id = ?
    )",
    [$studentId]
);


$rate = $stats['total'] > 0
    ? round($stats['correct'] / $stats['total'] * 100)
    : 0;

$pageTitle = '生徒ページ|study!!';
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<!-- ダッシュボードの中身 -->
<div class="container">
    <h2><?= h($_SESSION['user_name']) ?>さんのダッシュボート</h2>
    <div class="grid-2 stats-area">

        <div>
            <br>
            <p class="indent">総回答数：<span class="size-l"><?= $stats['total'] ?></span></p>
            <p class="indent">正答率：<span class="size-l"><?= $rate ?></span></p>
        </div>

        <div>
            <p>最新の解答５件</p>
            <?php foreach ($recentAnswers as $answer): ?>
                <p class="margin-bottom-zero"><span class="date"><?= h($answer['created_at']) ?></span> <?= h($answer['title']) ?></p>
                <p class="tight  <?= $answer['is_correct'] ? 'badge-correct' : 'badge-incorrect' ?>">
                    <?= $answer['is_correct'] ? '正解' : '不正解' ?>
                </p>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">


            <a href="/student/questions/index.php">
                <h2 class="text-center">問題ページを見る</h2>
                <?php if (!empty($unansweredQuestions)): ?>
                    <p class="color-red text-center">未回答の問題があります！</p>
                <?php endif; ?>
                <?php foreach ($questions as $question): ?>
                    <p><span class="date"><?= h($question['created_at']) ?></span> <?= h($question['title']) ?></p>
                <?php endforeach; ?>
            </a>
        </div>

        <div class="card">
            <a href="/student/messages/index.php">
                <h2 class="text-center">質問ページを見る</h2>
                <?php foreach ($messages as $message): ?>
                    <p><span class="date"><?= h($message['created_at']) ?></span> <?= h($message['title']) ?></p>
                <?php endforeach; ?>
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>