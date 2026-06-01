<?php
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$db = new Database();
$auth = new Auth();


$auth->requireRole('student');

$category_id = $_GET['category_id'];
$page        = (int)$_GET['page'];



$questions = $db->query(
    "SELECT * FROM questions WHERE category_id = ?",
    [$category_id]
);

$question   = $questions[$page - 1];
$totalPages = count($questions);

$options = $db->query(
    "SELECT * FROM question_options WHERE question_id = ?",
    [$question['id']]
);

$answer = $db->query(
    "SELECT * FROM answers WHERE question_id = ?",
    [$question['id']]
);

// multiple_choiceの正解テキストを取得
$correctText = null;
foreach ($options as $option) {
    if ($option['is_correct'] == 1) {
        $correctText = $option['option_text'];
        break;
    }
}


// 4. POST処理（回答を保存・正誤判定）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $validator = new Validator($_POST);
    $validator->required('answer', '回答');

    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
        flash_set('回答を選んでください', 'error');
        header('Location: /student/questions/show.php?category_id=' . $category_id . '&page=' . $page);
        exit;
    } 

        $answer = $_POST['answer'];

        // 正誤判定（問題種別で分岐）
        if ($question['question_type'] === 'multiple_choice') {
            foreach ($options as $option) {
                if ($option['id'] == $answer) {
                    $isCorrect = $option['is_correct'];
                    break;
                }
            }
        } else {

            $isCorrect = ($answer === $question['correct_answer']) ? 1 : 0;
            $correctText = $question['correct_answer'];
        }

        // answersテーブルにINSERT
        $db->query(
            "INSERT INTO answers(question_id, student_id, answer_text, is_correct) VALUES ( ? , ? , ? , ?)",
            [$question['id'], $_SESSION['user_id'], $answer, $isCorrect]
        );

        $_SESSION['result'] = [
            'is_correct'     => $isCorrect,
            'correct_answer' => $question['correct_answer']
        ];

        header('Location: /student/questions/show.php?category_id=' . $category_id . '&page=' . $page);
        exit;
    }


$result = $_SESSION['result'] ?? null;
unset($_SESSION['result']);

function handleAnswer(?string $correctText, array $question)
{
    if ($correctText) {
        return $correctText;
    } elseif ($question['question_type'] === 'true_false') {
        return $question['correct_answer'] === 'true'
            ?  '正しい'
            :  '誤り';
    } else {
        return $question['correct_answer'];
    }
}

?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>


<!-- ここからHTML -->
<div class="flex">
    <?php if ($page === 1): ?>
        <div class="gray">[ BACK ]</div>
    <?php else: ?>
        <a href="/student/questions/show.php?category_id=<?= $category_id ?>&page=<?= $page - 1 ?>">
            <div>[ BACK ]</div>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
        <a href="/student/questions/show.php?category_id=<?= $category_id ?>&page=<?= $i ?>">
            <?php if ($page === $i) : ?>
                <div>[ ◯ ]</div>
            <?php else : ?>
                <div>[ <?= $i ?> ]</div>
            <?php endif; ?>
        </a>
    <?php endfor ?>

    <?php if ($page === $totalPages): ?>
        <div class="gray">[ NEXT ]</div>
    <?php else: ?>
        <a href="/student/questions/show.php?category_id=<?= $category_id ?>&page=<?= $page + 1 ?>">
            <div>[ NEXT ]</div>
        </a>
    <?php endif; ?>
</div>

<hr>

<h4 class="tight">問題：<?= h($page) ?></h4>
<h2><?= h($question['title']) ?></h2>
<p><?= h($question['content']) ?></p>

<form method="POST" action="/student/questions/show.php?category_id=<?= $category_id ?>&page=<?= $page ?>">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <?php if ($question['question_type'] === 'short_answer'): ?>
        <input type="text" name="answer">

    <?php elseif ($question['question_type'] === 'true_false'): ?>
        <input type="radio" name="answer" value="true">
        <label for="true">正しい</label>
        <input type="radio" name="answer" value="false">
        <label for="false">誤り</label>

    <?php elseif ($question['question_type'] === 'multiple_choice'): ?>
        <?php foreach ($options as $option): ?>
            <label>
                <input type="radio" name="answer" value="<?= h($option['id']) ?>">
                <?= h($option['option_text']) ?>
            </label>
        <?php endforeach; ?>

    <?php endif; ?>


    <?php if ($result): ?>

        <p>答え</p>
        <p><?= h(handleAnswer($correctText, $question)) ?></p>
    <?php endif; ?>

    <div class="text-center">
        <button type="submit">回答する</button>
    </div>

    <div>
        <a href="/student/questions/index.php">問題一覧に戻る</a>
    </div>

</form>

<hr>

<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>