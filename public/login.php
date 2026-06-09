<?php
session_start();
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Validator.php';

$db = new Database();
$auth = new Auth();



//  POST時の処理



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = $_POST['email'];
    $password = $_POST['password'];




    // バリデーション
    $validator = new Validator($_POST);
    $validator->required('email', 'メールアドレス');
    $validator->required('password', 'パスワード');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {

        $users = $db->query(
            "SELECT * FROM users WHERE email =?",
            [$email]
        );
        $user = $users[0] ?? null;
        if (isset($user) && password_verify($password, $user['password_hash'])) {
            $auth->login($user);

            $redirect = match ($user['role']) {
                'student' => '/student/dashboard.php',
                'teacher' => '/teacher/dashboard.php'
            };
            header("Location: $redirect");

            exit;
        } else {
            $error = 'メールアドレスまたはパスワードが違います';
        }
    }
}



$pageTitle = "ログインページ|study!!";
?>

<?php require_once __DIR__ . '/../templates/header.php'; ?>

<!-- フォームのHTML -->

<?php if (isset($error)) : ?>
    <p><?= h($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="container">
        <h1 class="text-center">ログイン</h1>
        <hr>
        <br>
        <p>メールアドレス</p>
        <input type="email" name="email" id="">
        <?php show_error($errors ?? [], 'email') ?>
        <br>
        <p>パスワード</p>
        <input type="password" name="password">
        <?php show_error($errors ?? [], 'password') ?>


        <br>
        <input type="submit" value="ログイン" class="mx-auto">

        <h3 class="text-center">または</h3>
        <div class="text-center">
            <button type="button" onclick="location.href='./register.php'">新規登録</button>
        </div>
    </div>
    <hr>
</form>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>