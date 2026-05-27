<?php

session_start();
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Validator.php';

$db = new Database();
$auth = new Auth();

$id = $_SESSION['user_id'];

$auth->requireLogin();

// 1. ログイン中のユーザー情報を表示
$user = $db->query(
    "SELECT * FROM users WHERE id = ? ",
    [$id]
)[0];
// 2. 名前・メールアドレスを変更できる
// 3. パスワードを変更できる

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $validator = new Validator($_POST);
    $validator->required('name', '名前');
    $validator->required('email', 'メールアドレス');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        if (!empty($password)) {
            $user = $db->query(
                "UPDATE users SET name = ?, email = ?, password_hash = ? WHERE id = ?",
                [$name, $email, $passwordHash, $id]
            );
        } else {
            $user = $db->query(
                "UPDATE users SET name = ?, email = ? WHERE id = ?",
                [$name, $email, $id]
            );
        }
        header("Location: /mypage.php");
        exit;
    }
}
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>
<!-- ユーザー情報 -->
<?= h($user['name']) ?>
<?= h($user['email']) ?>
<!-- 登録フォーム -->
<form method="POST">
    <p>名前</p>
    <input type="text" name="name" id="" value="<?= h($user['name']) ?>">
    <?php show_error($errors ?? [], 'name') ?>
    <p>メールアドレス</p>
    <input type="email" name="email" id="" value="<?= h($user['email']) ?>">
    <?php show_error($errors ?? [], 'email') ?>
    <p>パスワード</p>
    <input type="password" name="password" id="">

    <br>
    <input type="submit" value="更新">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
</form>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>