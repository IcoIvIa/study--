<?php
session_start();
//必要なファイル読み込み
require_once __DIR__.'/../src/Auth.php';
require_once __DIR__.'/../src/helpers.php';
require_once __DIR__.'/../src/Database.php';

$db = new Database();

//POST処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    csrf_verify();

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $passwordHash = password_hash($password,PASSWORD_DEFAULT);

    // DBにINSERT
        $users = $db->query(
        "INSERT INTO users (name , email , password_hash , role) VALUES (? , ? , ? , ?)",
        [$name , $email , $passwordHash , 'student']
        
    );
    // リダイレクト
        header("Location: /login.php");
        exit;
}

$pageTitle = "ユーザー登録|study!!";
?>

<?php require_once __DIR__.'/../templates/header.php'; ?>

<!-- 登録フォーム -->
 <form method="POST">
    <p>ユーザー登録</p>

    <p>名前</p>
    <input type="text" name="name" id="">
    <p>メールアドレス</p>
    <input type="email" name="email" id="">
    <p>パスワード</p>
    <input type="password" name="password" id="">

    <br>
    <input type="submit" value="登録">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
 </form>

<?php require_once __DIR__.'/../templates/footer.php'; ?>