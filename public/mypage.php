<?php

session_start();
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Validator.php';


$auth = new Auth();
$auth->requireLogin();

$db = new Database();
$id = $_SESSION['user_id'];



$user = $db->query(
    "SELECT * FROM users WHERE id = ? ",
    [$id]
)[0];

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
<div class="container">
    <h2>プロフィールを変更出来ます</h2>
    <hr>
    <br>
    <!-- ユーザー情報 -->
    <div class="text-center">
        <?= h($user['name']) ?>
        <?= " : " ?>
        <?= h($user['email']) ?>
    </div>
    <br>
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
        <input class="mx-auto" type="submit" value="変更">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    </form>
    <hr>
</div>
<?php require_once __DIR__ . '/../templates/footer.php'; ?>