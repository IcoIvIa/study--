<?php
session_start();

require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';

$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();
$teacherId = $_SESSION['user_id'];

function checked(string $question, string $type): string
{
    return $question === $type ? 'checked' : '';
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

<hr>

    <?php foreach ($questions as $question): ?>

        <table border="1">

            <tr>
                <td colspan="2">
                    <p>問題ID: <?= h($question['id']) ?></p>
                </td>
                <td>
                    生徒の回答一覧
                </td>
            </tr>

            <tr>
                <td>タイトル</td>
                <td><?= h($question['title']) ?></td>
                <td rowspan="4"><a href="/teacher/questions/answers.php?id=<?= h($question['id']) ?>">見る</a></td>
            </tr>

            <tr>
                <td>問題内容</td>
                <td><?= h($question['content']) ?></td>
            </tr>

            <tr>
                <td>問題種別</td>
                <td>

                    <label>
                        <input
                            type="radio"
                            name="questions[<?= h($question['id']) ?>][question_type]"
                            value="multiple_choice"
                            <?= checked($question['question_type'], 'multiple_choice') ?> disabled>
                        選択問題
                    </label>

                    <label>
                        <input
                            type="radio"
                            value="short_answer"
                            <?= checked($question['question_type'], 'short_answer') ?> disabled>
                        記述問題
                    </label>

                    <label>
                        <input
                            type="radio"
                            value="true_false"
                            <?= checked($question['question_type'], 'true_false') ?> disabled>
                        正誤問題
                    </label>

                </td>
            </tr>

            <tr>
                <td>解説</td>
                <td><?= h($question['explanation']) ?></td>
            </tr>

        </table>

        <br>

    <?php endforeach; ?>

<hr>
<a href="/teacher/dashboard.php">
    <h4>ダッシュボードに戻る</h4>
</a>
<hr>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>