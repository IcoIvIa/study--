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
    "SELECT *
    FROM categories
    ORDER BY
    sort_order ASC"
);


if (isset($_POST['action'])) {
    csrf_verify();

    if (!isset($_POST['delete_id'])) {
        header('Location: /teacher/categories/index.php');
        exit;
    }

    $id = $_POST['delete_id'];
    $db->execute(
        "DELETE FROM categories WHERE id = ?",
        [$id]

    );
    header('Location: /teacher/categories/index.php');
    exit;
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>
<!-- ここからHTML -->
<form method="POST">

    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <table border="1">
        <th>単元名<br><label class="date">クリックすると編集画面に飛びます</label></th>
        <th>単元の説明</th>
        <th>章の値(この数値を基準に並び替えます)</th>
        <th>削除する</th>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td>
                    <a href="/teacher/categories/edit.php?category_id=<?= $category['id'] ?>">
                        <p> <?= h($category['name']) ?></p>
                    </a>
                </td>
                <td>
                    <p> <?= h($category['description']) ?></p>
                </td>
                <td>
                    <p> <?= h($category['sort_order']) ?></p>
                </td>

                <td>
                    <input type="checkbox" name="delete_id" value="<?= h($category['id']) ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit" name="action" value="delete">削除</button>
</form>

    <hr>
    <a href="/teacher/dashboard.php"><h4>ダッシュボードに戻る</h4></a>
    <hr>
    
<script>
    'use strict';


    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.addEventListener('click', (e) => {
        const checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');
        if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('項目が選択されていません。')
        return;
    }

        const confirmed = confirm('単元を削除します。よろしいですか？');
        if(!confirmed) {
            e.preventDefault();
        }
    });
</script>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>