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
$id = $_GET['id'];
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

        $questions = $db->query(
            "UPDATE  questions SET  title = ? , content = ? , question_type = ? , correct_answer = ? , explanation = ? WHERE id = ? ",
            [$title, $content, $questionType, $correctAnswer, $explanation, $id]
        );
        flash_set('問題を更新しました');
        header('Location: /teacher/questions/index.php');
        exit;
    }
}

?>
// 4. フォーム表示（初期値あり）



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
        <input type="radio" name="question_type" value="multiple_choice" id="">
        <?php show_error($errors ?? [], 'question_type') ?>

        選択問題
    </label>
    <br>
    <label for="">
        <input type="radio" name="question_type" value="short_answer" id="">
        記述問題
    </label>
    <br>
    <label for="">
        <input type="radio" name="question_type" value="true_false" id="">
        正誤問題
    </label>
    <br>

    <p>回答を編集</p>
    <div id="answer-area">
        <input type="text" name="multiple_choice_answer" id="" value="multiple_choice_answer_test message">
        <input type="text" name="short_answer_answer" id="" value="short_answer_answer_test message">
        <input type="text" name="true_false_answer" id="" value="true_false_answer_test message">
    </div>



    <p>解説を編集</p>
    <textarea name="explanation" id="" cols="30" rows="10"><?= h($question['explanation']) ?></textarea>

    <input type="submit" value="登録">

    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
</form>

<?php require_once __DIR__ . '/../../../templates/footer.php' ?>