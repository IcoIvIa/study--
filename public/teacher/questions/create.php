<?php
// ・title（問題タイトル）
// ・content（問題内容）
// ・question_type（種別：multiple_choice / short_answer / true_false）
// ・correct_answer（正解）
// ・explanation（解説）
// ・teacher_id（誰が作ったか）
session_start();
require_once __DIR__ . '/../../../src/Auth.php';
require_once __DIR__ . '/../../../src/helpers.php';
require_once __DIR__ . '/../../../src/Database.php';
require_once __DIR__ . '/../../../src/Validator.php';

$db = new Database();
$auth = new Auth();

$auth->requireRole('teacher');

$teacherId = $_SESSION['user_id'];



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

          $explanation   = $_POST['explanation'];
          // 問題種別に応じて正解を取得（multiple_choiceはquestion_optionsで管理）
          $correctAnswer = match ($questionType) {
               'short_answer'    => $_POST['short_answer_answer'],
               'true_false'      => $_POST['true_false_answer'],
               'multiple_choice' => null,
          };

          $questions = $db->query(
               "INSERT INTO questions (teacher_id , title , content, question_type, correct_answer, explanation) VALUES (? , ? , ? , ? , ? , ?)",
               [$teacherId, $title, $content, $questionType, $correctAnswer, $explanation]
          );

          flash_set('問題を作成しました');
          header('Location: /teacher/questions/index.php');
          exit;
     }
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<link rel="stylesheet" href="../../../css/common.css">

<!-- フォームのHTML -->
<form method="POST" id="">
     <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
     <p>タイトルを入力</p>
     <input type="text" name="title" id="">
     <?php show_error($errors ?? [], 'title') ?>

     <p>問題の内容を入力</p>
     <textarea name="content" id="" cols="30" rows="10"></textarea>
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

     <p>回答を入力</p>
     <div id="answer-area">
          <input type="text" name="multiple_choice_answer" id="" value="multiple_choice_answer_test message">
          <input type="text" name="short_answer_answer" id="" value="short_answer_answer_test message">
          <input type="text" name="true_false_answer" id="" value="true_false_answer_test message">
     </div>



     <p>解説を入力</p>
     <textarea name="explanation" id="" cols="30" rows="10"></textarea>

     <input type="submit" value="登録">

</form>

<script>
     'use strict';
     // ラジオボタンの選択に応じて回答入力欄を切り替える
     // 表示ロジックはCSSの[data-answer-type]セレクタにて（common.css参照）
     const radios = document.querySelectorAll('input[name="question_type"]');

     const answerArea = document.querySelector('#answer-area');

     radios.forEach(radio => {
          radio.addEventListener('change', () => {
               answerArea.dataset.answerType = radio.value;
          });
     });
</script>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>