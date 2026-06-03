<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();



    $validator = new Validator($_POST);
    $validator->required('name', '単元名');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {

        $name         = $_POST['name'];
        $description  = $_POST['description'] ?? '';
        $sort_order  = $_POST['sort_order'] ?? 0;
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

<link rel="stylesheet" href="../../../css/common.css">

<!-- フォームのHTML -->
<form method="POST" id="">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <p>単元名</p>
    <input type="text" name="name">
    <?php show_error($errors ?? [], 'name') ?>

    <p>単元の説明を入力（任意）</p>
    <textarea name="description" cols="30" rows="10"></textarea>

    <p>章の値を選択（この数値を基準に並び替えます 任意）</p>

    <input type="number" name="sort_order" value="0" min="0">
    <?php show_error($errors ?? [], 'sort_order') ?>


    <br>

    <input type="submit" value="登録">

</form>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>