<?php
session_start();

require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

$errors = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order  = trim($_POST['sort_order'] ?? 0);

    $validator = new Validator($_POST);
    $validator->required('name', '単元名');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {

        $db->execute(
            "UPDATE categories SET name = ?, description = ?, sort_order = ? WHERE id = ?",
            [$name, $description, $sort_order, $_POST['category_id'] ?? null]
        );

        flash_set('単元を更新しました');

        header('Location: /teacher/categories/edit.php?category_id=' . $_POST['category_id']);
        exit;
    }
}

$categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if (!$categoryId) {
    header('Location: /teacher/categories/index.php');
    exit;
}


$categories = $db->query(
    "SELECT * FROM categories WHERE id = ?",
    [$categoryId]
);
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<form action="" method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <?php foreach ($categories as $category): ?>

        <table border="1">

            <tr>
                <td colspan="2">
                    <p>単元ID: <?= h($category['id']) ?></p>
                    <input type="hidden" name="category_id" value="<?= h($category['id']) ?>">
                </td>
            </tr>

            <tr>
                <td>単元名</td>
                <td>
                    <input
                        type="text"
                        name="name"
                        value="<?= h($category['name']) ?>">
                </td>
            </tr>

            <tr>
                <td>単元の説明</td>
                <td>
                    <textarea
                        name="description"
                        cols="50"
                        rows="5"><?= h($category['description']) ?></textarea>
                </td>
            </tr>

            <tr>
                <td>章の値を変更</td>
                <td>
                    <input type="number" name="sort_order" value="<?= h($category['sort_order']) ?>" min="0">
                </td>
            </tr>

        </table>

        <br>

    <?php endforeach; ?>

    <input type="submit" value="更新">

</form>

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>