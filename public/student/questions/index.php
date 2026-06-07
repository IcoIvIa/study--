<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';


$auth = new Auth();
$auth->requireRole('student');
$db = new Database();
$student_id = $_SESSION['user_id'] ?? '';

$pageTitle = "問題ページ||study!!";

$categories = $db->query(
    "SELECT 
    categories.*,
    COUNT(questions.id) AS question_count ,
    ROUND(SUM(answers.is_correct) / COUNT(answers.id) * 100) AS correct_rate
    FROM categories
    LEFT JOIN questions ON categories.id = questions.category_id
    LEFT JOIN answers ON questions.id = answers.question_id 
    AND answers.student_id = ?
    GROUP BY categories.id",
    [$student_id]
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->

<div class="container">

    <h2>問題一覧</h2>
    <hr>

    <?php foreach ($categories as $category): ?>
        <a href="/student/questions/show.php?category_id=<?= h($category['id']) ?>&page=1">
            <div class="card card-sm">
                <h4><?= h($category['name']) ?> : <span class="date"><?= h($category['created_at']) ?></span></h4>

                <p><?= h($category['description'] ?? '' )?></p>

                <p><span class="date">問題数：</span><?= h($category['question_count']) ?><span class="date indent">正答率：</span><?= h($category['correct_rate'] ?? '解答なし') ?></p>

                <div class="text-center">
                    <span class="badge-correct">回答する</span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    <hr>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>