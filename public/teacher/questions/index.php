<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];

$categories = $db->query(
    "SELECT DISTINCT c.id, c.name, c.description
    FROM categories c
    INNER JOIN questions q ON c.id = q.category_id
    WHERE q.teacher_id = ?",
    [$teacherId]
);

$questions = $db->query(
    "SELECT * FROM questions WHERE teacher_id = ?",
    [$teacherId]
);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<form method="POST">

    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <table border="1">
        <th>単元名<br><label class="date">クリックすると編集画面に飛びます</label></th>
        <th>概要</th>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td>
                    <a href="/teacher/questions/edit.php?category_id=<?= $category['id'] ?>">
                        <p> <?= h($category['name']) ?></p>
                    </a>
                </td>
                <td>
                    <p> <?= h($category['description']) ?></p>
                </td>

            </tr>
        <?php endforeach; ?>
    </table>

</form>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>