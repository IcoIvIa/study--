<?PHP
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$db = new Database();
$auth = new Auth();
$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];
// 1. id を取得
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = $_GET['id'];
} else {
    header('Location: /teacher/questions/index.php');
    exit;
}

function checked(string $question, string $type): string
{
    if ($question === $type) {
        return 'checked';
    } else return '';
}
// 2. DBから該当の問題を取得


$questions = $db->query(
    "SELECT * FROM questions WHERE id = ? AND teacher_id = ?",
    [$id, $teacherId]
);
// 1件目をとりだす
$question = $questions[0] ?? null;
if ($question === null) {
    header('Location: /teacher/questions/index.php');
    exit;
}

// HTMLで読み込むためここに記述
$options = [];

if ($question['question_type'] === 'multiple_choice') {
    $options = $db->query(
        "SELECT * FROM question_options
        WHERE question_id = ?
        ORDER BY sort_order ASC",
        [$id]
    );
}

$categories = $db->query(
    "SELECT * FROM categories ORDER BY sort_order ASC"
);


// 3. POST処理（UPDATE）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $title         = $_POST['title'];
    $content       = $_POST['content'];
    $questionType  = $_POST['question_type'];
    $validator = new Validator($_POST);
    $validator->required('title', 'タイトル');
    $validator->required('content', '問題の内容');
    $validator->required('question_type', '問題種別');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {

        // 問題種別に応じて正解を取得（multiple_choiceはquestion_optionsで管理）
        $correctAnswer = match ($questionType) {
            'short_answer'    => $_POST['short_answer_answer'],
            'true_false'      => $_POST['true_false_answer'],
            'multiple_choice' => null,
        };


        $explanation   = $_POST['explanation'];

        $categoryId =  $_POST['category_id'] !== '' ? $_POST['category_id'] : null;

        $questions = $db->query(
            "UPDATE  questions SET  title = ? , content = ? , question_type = ? , correct_answer = ? , explanation = ? , category_id = ? WHERE id = ?",
            [
                $title,
                $content,
                $questionType,
                $correctAnswer,
                $explanation,
                $categoryId,
                $id
            ]
        );
        flash_set('問題を更新しました');
        header('Location: /teacher/questions/index.php');
        exit;
    }
}

?>

<!-- ここからHTML -->
<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<form action="" method="POST">

    <p>タイトルを編集</p>
    <input type="text" name="title" id="" value="<?= h($question['title']) ?>">
    <?php show_error($errors ?? [], 'title') ?>

    <p>問題の内容を編集</p>
    <textarea name="content" id="" cols="30" rows="10"><?= h($question['content']) ?></textarea>
    <?php show_error($errors ?? [], 'content') ?>

    <p>問題種別を選択</p>
    <label for="">
        <input type="radio" name="question_type" value="multiple_choice" <?= checked($question['question_type'], 'multiple_choice') ?>>
        <?php show_error($errors ?? [], 'question_type') ?>

        選択問題
    </label>
    <br>
    <label for="">
        <input type="radio" name="question_type" value="short_answer" <?= checked($question['question_type'], 'short_answer') ?>>
        記述問題
    </label>
    <br>
    <label for="">
        <input type="radio" name="question_type" value="true_false" <?= checked($question['question_type'], 'true_false') ?>>
        正誤問題
    </label>
    <br>

    <select name="category_id">
        <option value="">未選択</option>
        <?php foreach ($categories as $category) : ?>
            <option value="<?= h($category['id']) ?>"
                <?= ($question['category_id'] === (string)$category['id']) ? 'selected' : '' ?>>
                <?= h($category['name']) ?>
            </option>
        <?php endforeach; ?>

    </select>

    <p>回答を編集</p>
    <div id="answer-area">

        <input type="text" name="short_answer_answer" id="" value="<?= h($question['correct_answer']) ?>">
        <input type="text" name="true_false_answer" id="" value="<?= h($question['correct_answer']) ?>">

        <?php if (isset($options)): ?>
            <?php foreach ($options as $index => $option) : ?>
                <input type="text" name="options[<?= $index ?>][option_text]" value="<?= h($option['option_text']) ?>">
                <input type="radio" name="correct_option" value="<?= h($option['id']) ?>" <?= $option['is_correct'] ? 'checked' : '' ?>>
                <input type="hidden" name="options[<?= $index ?>][id]" value="<?= h($option['id']) ?>">

            <?php endforeach; ?>
            <button type="button" id="add-option">選択肢を追加</button>
        <?php endif; ?>


    </div>


    <p>解説を編集</p>
    <textarea name="explanation" id="" cols="30" rows="10"><?= h($question['explanation']) ?></textarea>

    <input type="submit" value="登録">

    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
</form>

<script>
     'use strict';
     const multipleChoice = document.querySelectorAll('');
     // ラジオボタンの選択に応じて回答入力欄を切り替える
     // 表示ロジックはCSSの[data-answer-type]セレクタにて（common.css参照）
    //  const radios = document.querySelectorAll('input[name="question_type"]');

    //  const answerArea = document.querySelector('#answer-area');

    //  radios.forEach(radio => {
    //       radio.addEventListener('change', () => {
    //            answerArea.dataset.answerType = radio.value;
    //       });
    //  });
</script>

<?php require_once __DIR__ . '/../../../templates/footer.php' ?>