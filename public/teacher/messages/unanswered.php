<?php

session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';


$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();

$threads = $db->query(
    "SELECT message_threads.*, users.name
 FROM message_threads
 INNER JOIN users ON message_threads.student_id = users.id
 WHERE teacher_id IS NULL"

);

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<!-- ここからHTML -->
<h2>未返信スレッド一覧</h2>

<hr>

<table border="1">
    <?php foreach ($threads as $thread) : ?>
        <tr>
            <th>
                <p><?= h($thread['name']) ?>さんの質問</p>
            </th>
            <td>

                <p><?= h($thread['title']) ?></p>

            </td>
            <td>
                <a href="/teacher/messages/show.php?id=<?= h($thread['id']) ?>">
                    <p>質問を見る</p>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>