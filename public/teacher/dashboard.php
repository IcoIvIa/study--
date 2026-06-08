<?php

session_start();

require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/helpers.php';
require_once __DIR__ . '/../../src/Database.php';


$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

$teacherId = $_SESSION['user_id'];

$unreadCount = $db->query(
    "SELECT COUNT(*) as count
    FROM message_threads
    WHERE teacher_id IS NULL"
)[0];

$questionStats = $db->query(
    "SELECT categories.id,
       categories.name,
       COUNT(answers.id) as total,
       SUM(answers.is_correct) as correct
    FROM categories
    LEFT JOIN questions ON categories.id = questions.category_id
    LEFT JOIN answers ON questions.id = answers.question_id
    WHERE questions.teacher_id = ?
    GROUP BY categories.id",
    [$teacherId]
);

$pageTitle = '先生ページ|study!!';
?>

<?php require_once __DIR__ . '/../../templates/header.php'; ?>

<hr>

<!-- ダッシュボードの中身 -->
<table border="1">
    <tr>
        <th>未返信の質問数</th>
        <th>未返信一覧</th>
    </tr>

    <tr>
        <td>
            <p><?= $unreadCount['count'] ?></p>
        </td>
        <td><a href="/teacher/messages/unanswered.php">未返信のみ見る</a></td>
    </tr>
</table>
<div>
    <h2>質問管理</h2>
    <ul>
        <li><a href="/teacher/messages/index.php">質問一覧</a></li>
    </ul>
</div>

<hr>

<table border="1">
    <tr>
        <th colspan="4">あなたが出題した問題のデータ</th>
    </tr>
    <tr>
        <th>問題</th>
        <th>回答数</th>
        <th>正答率</th>
        <th>未返答者</th>
    </tr>

    <?php foreach ($questionStats as $stat): ?>
        <tr>
            <td>
                <p> <?= h($stat['name']) ?></p>
            </td>
            <td>
                <p><?= $stat['total'] ?></p>
            </td>


            <?php $rate = $stat['total'] > 0
                ? round($stat['correct'] / $stat['total'] * 100)
                : 0; ?>

            <td>
                <p><?= $rate ?></p>
            </td>
            <td><a href="/teacher/questions/unanswered.php?category_id=<?= h($stat['id']) ?>">一覧ページへ</a></td>
        </tr>
    <?php endforeach; ?>
</table>



<div>
    <h2>単元管理</h2>
    <ul>
        <li><a href="/teacher/categories/index.php">単元一覧</a></li>
        <li><a href="/teacher/categories/create.php">単元新規作成</a></li>
    </ul>
</div>

<div>
    <h2>問題管理</h2>
    <ul>
        <li><a href="/teacher/questions/index.php">問題一覧</a></li>
        <li><a href="/teacher/questions/create.php">問題新規作成</a></li>
    </ul>
</div>

<div>
    <h2>テスト解答管理(テスト用削除)</h2>
    <ul>

        <li><a href="/teacher/questions/answers.php?id=3">回答一覧（id=1）</a></li>
    </ul>
</div>


<hr>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>