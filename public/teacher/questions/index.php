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

if (isset($_POST['action'])) {
    csrf_verify();
    $id = $_POST['delete_id'];
    $db->query(
        "DELETE FROM questions WHERE id = ? AND teacher_id = ? ",
        [$id, $teacherId]

    );
    header('Location: /teacher/questions/index.php');
    exit;
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<form method="POST">

    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <table border="1">
        <th>単元名<br><label class="date">クリックすると編集画面に飛びます</label></th>
        <th>概要</th>
        <th>削除する</th>
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

                <td>
                    <input type="checkbox" name="delete_id" value=" <?= h($category['id']) ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit" name="action" value="delete">削除</button>
</form>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>