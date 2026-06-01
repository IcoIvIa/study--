<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');

$categories = $db->query(
    "SELECT * FROM categories"
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
                <p><?= h($category['name']) ?> : <span class="date"><?= h($category['created_at']) ?></span></p>
                <div class="text-center">
                    <span class="badge-correct">回答する</span>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    <hr>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>