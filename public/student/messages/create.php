<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('student');
$student_id = $_SESSION['user_id'];

$errors = [];
$title = '';
$body  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $body  = trim($_POST['body'] ?? '');

    $validator = new Validator($_POST);
    $validator->required('title', 'タイトル');
    $validator->required('body', '本文');
    $validator->maxLength('body', '本文', 255);

    $errors = $validator->getErrors();

    if ($validator->hasErrors()) {
    } else {

        $db->execute(
            "INSERT INTO message_threads (student_id, title) VALUES(? ,?)",
            [$student_id, $title]
        );

        $threadId = $db->lastInsertId();

        $db->execute(
            "INSERT INTO message_replies (message_thread_id, sender_role, sender_id, body) VALUES(?, ?, ?, ?)",
            [$threadId, 'student', $student_id, $body]
        );

        flash_set('質問を投稿しました');

        header("Location: /student/messages/index.php");
        exit;
    }
}
?>


<!-- フォームのHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<div class="container">
    <form action="" method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <h2 class="text-center">質問投稿フォーム</h2>
        <hr>

        <label for="title">
            タイトルを入力してください
        </label>

        <input type="text" name="title" id="title" value="<?= h($title) ?>" required>
        <?php show_error($errors, 'title'); ?>

        <label for="body">
            本文を入力してください
        </label>
        <textarea name="body" maxlength="255" id="body"><?= h($body) ?></textarea>
        <?php show_error($errors, 'body'); ?>

        <input class="mx-auto" type="submit" value="質問を投稿する">
        <hr>
    </form>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>