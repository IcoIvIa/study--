<?php
session_start();

require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$auth = new Auth();
$auth->requireRole('teacher');

$db = new Database();
$teacher_id = $_SESSION['user_id'];

function checked(string $value, string $target): string
{
    return $value === $target ? 'checked' : '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    foreach ($_POST['questions'] as $id => $question) {

        // タイトル・内容・種別・解説を更新
        $db->execute(
            "UPDATE questions
             SET title = ?,
                 content = ?,
                 question_type = ?,
                 correct_answer = ?,
                 explanation = ?
             WHERE id = ?",
            [
                trim($question['title'] ?? ''),
                trim($question['content'] ?? ''),
                trim($question['question_type'] ?? ''),
                trim($question['correct_answer'] ?? ''),
                trim($question['explanation'] ?? ''),
                $id
            ]
        );

        // multiple_choiceの場合は選択肢の正解を更新
        if (($question['question_type'] ?? '') === 'multiple_choice' && isset($question['correct_option'])) {
            // まず全選択肢をis_correct=0にリセット
            $db->execute(
                "UPDATE question_options SET is_correct = 0 WHERE question_id = ?",
                [$id]
            );
            // 選択した選択肢をis_correct=1に更新
            $db->execute(
                "UPDATE question_options SET is_correct = 1 WHERE id = ?",
                [$question['correct_option']]
            );
        }
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
    "SELECT questions.*, categories.name AS category_name
     FROM questions
     INNER JOIN categories ON questions.category_id = categories.id
     WHERE questions.teacher_id = ?
       AND questions.category_id = ?
     ORDER BY questions.id",
    [$teacher_id, $categoryId]
);

// 各問題の選択肢を取得
$optionsMap = [];
foreach ($questions as $question) {
    $optionsMap[$question['id']] = $db->query(
        "SELECT * FROM question_options WHERE question_id = ? ORDER BY sort_order",
        [$question['id']]
    );
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<div class="container">
    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <?php foreach ($questions as $question): ?>
            <table border="1">
                <tr>
                    <td colspan="2">
                        <p>問題ID: <?= h($question['id']) ?></p>
                    </td>
                </tr>

                <tr>
                    <td>タイトル</td>
                    <td>
                        <input
                            type="text"
                            name="questions[<?= h($question['id']) ?>][title]"
                            value="<?= h($question['title']) ?>">
                    </td>
                </tr>

                <tr>
                    <td>問題内容</td>
                    <td>
                        <textarea
                            name="questions[<?= h($question['id']) ?>][content]"
                            cols="50"
                            rows="5"><?= h($question['content']) ?></textarea>
                    </td>
                </tr>

                <tr>
                    <td>問題種別</td>
                    <td>
                        <input type="hidden"
                            name="questions[<?= h($question['id']) ?>][question_type]"
                            value="<?= h($question['question_type']) ?>">
                        <label>
                            <input type="radio"
                                name="questions[<?= h($question['id']) ?>][question_type]"
                                value="multiple_choice"
                                <?= checked($question['question_type'], 'multiple_choice') ?> disabled>
                            選択問題
                        </label>
                        <label>
                            <input type="radio"
                                name="questions[<?= h($question['id']) ?>][question_type]"
                                value="short_answer"
                                <?= checked($question['question_type'], 'short_answer') ?> disabled>
                            記述問題
                        </label>
                        <label>
                            <input type="radio"
                                name="questions[<?= h($question['id']) ?>][question_type]"
                                value="true_false"
                                <?= checked($question['question_type'], 'true_false') ?> disabled>
                            正誤問題

                        </label>
                    </td>
                </tr>

                <tr>
                    <td>正解</td>
                    <td>
                        <?php if ($question['question_type'] === 'short_answer'): ?>
                            <input
                                type="text"
                                name="questions[<?= h($question['id']) ?>][correct_answer]"
                                value="<?= h($question['correct_answer']) ?>">

                        <?php elseif ($question['question_type'] === 'true_false'): ?>
                            <label>
                                <input type="radio"
                                    name="questions[<?= h($question['id']) ?>][correct_answer]"
                                    value="true"
                                    <?= checked($question['correct_answer'], 'true') ?>>
                                正しい
                            </label>
                            <label>
                                <input type="radio"
                                    name="questions[<?= h($question['id']) ?>][correct_answer]"
                                    value="false"
                                    <?= checked($question['correct_answer'], 'false') ?>>
                                誤り
                            </label>

                        <?php elseif ($question['question_type'] === 'multiple_choice'): ?>
                            <?php foreach ($optionsMap[$question['id']] as $option): ?>
                                <label>
                                    <input type="radio"
                                        name="questions[<?= h($question['id']) ?>][correct_option]"
                                        value="<?= h($option['id']) ?>"
                                        <?= $option['is_correct'] ? 'checked' : '' ?>>
                                    <?= h($option['option_text']) ?>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </td>
                </tr>

                <tr>
                    <td>解説</td>
                    <td>
                        <textarea
                            name="questions[<?= h($question['id']) ?>][explanation]"
                            cols="50"
                            rows="5"><?= h($question['explanation']) ?></textarea>
                    </td>
                </tr>
            </table>
            <br>
        <?php endforeach; ?>

        <input type="submit" value="一括更新">
    </form>

    <hr>
    <a href="/teacher/dashboard.php">
        <h4>ダッシュボードに戻る</h4>
    </a>
    <hr>
</div>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>