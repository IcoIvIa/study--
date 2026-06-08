<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';


$auth = new Auth();
$auth->requireRole('teacher');
$db = new Database();
$teacherId = $_SESSION['user_id'];

$categories = $db->query(
    "SELECT DISTINCT c.id, c.name, c.description
    FROM categories c
    INNER JOIN questions q ON c.id = q.category_id
    WHERE q.teacher_id = ?",
    [$teacherId]
);

// $questions = $db->query(
//     "SELECT * FROM questions WHERE teacher_id = ?",
//     [$teacherId]
// );

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<!-- <form method="POST"> -->

    <!-- <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>"> -->
    <table border="1">
        <tr>
            <th>単元名</th>
            <th>概要</th>
            <th>単元内の問題一覧を見る</th>
            <th>単元内の問題を編集する<br><label class="date">編集画面に飛びます</label></th>
        </tr>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td>
                    <p> <?= h($category['name']) ?></p>
                </td>
                <td>
                    <p> <?= h($category['description']) ?></p>
                </td>

                <td>
                <a href="/teacher/questions/questions.php?category_id=<?= h($category['id']) ?>">
                    クリックでジャンプします
                </a>
                </td>

                <td>
                <a href="/teacher/questions/edit.php?category_id=<?= h($category['id']) ?>">
                    クリックでジャンプします
                </a>
                </td>


            </tr>
        <?php endforeach; ?>
    </table>

<!-- </form> -->

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>