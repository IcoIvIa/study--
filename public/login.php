<?php
session_start();
require_once '../src/Auth.php';
require_once '../src/helpers.php';
require_once '../src/Database.php';

$db = new Database();
$auth = new Auth();

//  POST時の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email = $_POST['email'];
    $password = $_POST['password'];

    $users = $db->query(
        "SELECT * FROM users WHERE email =?",
        [$email]
    );
    $user = $users[0] ?? null;

    if (isset($user) && password_verify($password, $user['password_hash'])) {
        $auth->login($user);

        $redirect = match($user['role']) {
            'student' => '/student/dashboard.php',
            'teacher' => '/teacher/dashboard.php'
        };
        header("Location: $redirect");
        
        exit;
    } else {
        $error = 'メールアドレスまたはパスワードが違います';
    }
}





$pageTitle = "stdy!!|ログインページ";
?>

<?php require_once '../templates/header.php'; ?>

<!-- フォームのHTML -->

<?php if(isset($error)) : ?>
    <p><?= h($error) ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <input type="email" name="email" id="">

    <input type="password" name="password" id="">

    <input type="submit" value="ログイン">
</form>

<?php require_once '../templates/footer.php'; ?>