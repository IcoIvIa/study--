<?php
session_start();
//必要なファイル読み込み
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Validator.php';

$db = new Database();

//POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'];

    // バリデーション
    $validator = new Validator($_POST);
    $validator->required('name', '名前');
    $validator->required('email', 'メールアドレス');
    $validator->required('password', 'パスワード');



    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        // DBにINSERT
        $users = $db->execute(
            "INSERT INTO users (name , email , password_hash , role) VALUES (? , ? , ? , ?)",
            [$name, $email, $passwordHash, 'student']

        );
        // リダイレクト
        header("Location: /login.php");
        exit;
    }
}

$pageTitle = "ユーザー登録|study!!";
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>

<!-- 登録フォーム -->
<div class="container">
    <form method="POST">

        <h2 class="text-center">新規登録</h2>
        <hr>

        <p>名前</p>
        <input type="text" name="name" id="">
        <?php show_error($errors ?? [], 'name') ?>
        <p>メールアドレス</p>
        <input type="email" name="email" id="">
        <?php show_error($errors ?? [], 'email') ?>
        <p>パスワード</p>
        <input type="password" name="password" id="">
        <?php show_error($errors ?? [], 'password') ?>

        <br>
        <input class="mx-auto" type="submit" value="登録">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    </form>
    <hr>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>