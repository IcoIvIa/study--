<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

$teacherId = $_SESSION['user_id'];

$errors = [];
$name        = '';
$description = '';
$sort_order  = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $validator = new Validator($_POST);
    $validator->required('name', '単元名');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sort_order = trim($_POST['sort_order'] ?? 0);

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {


        $db->execute(
            "INSERT INTO categories (name, description ,sort_order) VALUES (?,?,?)",
            [$name, $description, $sort_order]
        );
        flash_set('単元を作成しました');
        header('Location: /teacher/categories/create.php');
        exit;
    }
}



?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>


<!-- フォームのHTML -->
<form method="POST" id="">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <p>単元名</p>
    <input type="text" name="name" value="<?= h($name ?? '') ?>" required>
    <?php show_error($errors ?? [], 'name') ?>

    <p>単元の説明を入力（任意）</p>
    <textarea name="description" cols="30" rows="10"><?= h($description ?? '') ?></textarea>

    <p>章の値を選択（この数値を基準に並び替えます 任意）</p>

    <input type="number" name="sort_order" value="0" min="0">
    <?php show_error($errors ?? [], 'sort_order') ?>


    <br>

    <input type="submit" value="登録">

    <hr>
    <a href="/teacher/dashboard.php"><h4>ダッシュボードに戻る</h4></a>
    <hr>

</form>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>