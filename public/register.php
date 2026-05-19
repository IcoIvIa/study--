<?php
session_start();
//必要なファイル読み込み
require_once '../src/Auth.php';
require_once '../src/helpers.php';
require_once '../src/Database.php';

//POST処理
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    csrf_verify();

    $name = $_POST[''];
    $email = $_POST[''];
    $password = $_POST[''];

    $passwordHash = password_hash($password,PASSWORD_DEFAULT);

    // DBにINSERT
    // リダイレクト
}

$pageTitle = "stdy!!|会員登録";
?>

<?php require_once '../templates/header.php'; ?>

<!-- 登録フォーム -->

<?php require_once '../templates/footer.php'; ?>