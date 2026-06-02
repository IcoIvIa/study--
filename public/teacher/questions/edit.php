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

function checked(string $question, string $type): string
{
    return $question === $type ? 'checked' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    csrf_verify();

    foreach ($_POST['questions'] as $id => $question) {

        $db->execute(
            "UPDATE questions
             SET title = ?,
                 content = ?,
                 question_type = ?,
                 explanation = ?
             WHERE id = ?",
            [
                $question['title'],
                $question['content'],
                $question['question_type'],
                $question['explanation'],
                $id
            ]
        );
    }

    flash_set('問題を更新しました');

    header('Location: /teacher/questions/index.php');
    exit;
}

$categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);

if (!$categoryId) {
    header('Location: /teacher/questions/index.php');
    exit;
}

$questions = $db->query(
    "SELECT questions.*, categories.name, categories.description
     FROM questions
     INNER JOIN categories
        ON questions.category_id = categories.id
     WHERE questions.teacher_id = ?
       AND questions.category_id = ?
     ORDER BY questions.id",
    [$teacherId, $categoryId]
);
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<form action="" method="POST">

<?php foreach ($questions as $question): ?>

<table border="1">

    <tr>
        <td colspan="2">
            <p>問題ID: <?= $question['id'] ?></p>
        </td>
    </tr>

    <tr>
        <td>タイトル</td>
        <td>
            <input
                type="text"
                name="questions[<?= $question['id'] ?>][title]"
                value="<?= h($question['title']) ?>"
            >
        </td>
    </tr>

    <tr>
        <td>問題内容</td>
        <td>
            <textarea
                name="questions[<?= $question['id'] ?>][content]"
                cols="50"
                rows="5"
            ><?= h($question['content']) ?></textarea>
        </td>
    </tr>

    <tr>
        <td>問題種別</td>
        <td>

            <label>
                <input
                    type="radio"
                    name="questions[<?= $question['id'] ?>][question_type]"
                    value="multiple_choice"
                    <?= checked($question['question_type'], 'multiple_choice') ?>
                >
                選択問題
            </label>

            <label>
                <input
                    type="radio"
                    name="questions[<?= $question['id'] ?>][question_type]"
                    value="short_answer"
                    <?= checked($question['question_type'], 'short_answer') ?>
                >
                記述問題
            </label>

            <label>
                <input
                    type="radio"
                    name="questions[<?= $question['id'] ?>][question_type]"
                    value="true_false"
                    <?= checked($question['question_type'], 'true_false') ?>
                >
                正誤問題
            </label>

        </td>
    </tr>

    <tr>
        <td>解説</td>
        <td>
            <textarea
                name="questions[<?= $question['id'] ?>][explanation]"
                cols="50"
                rows="5"
            ><?= h($question['explanation']) ?></textarea>
        </td>
    </tr>

</table>

<br>

<?php endforeach; ?>

<input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

<input type="submit" value="一括更新">

</form>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>