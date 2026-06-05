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


$categories = $db->query(
     "SELECT * FROM categories"
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     csrf_verify();

     $title         = $_POST['title'];
     $categoryId    = $_POST['category_id'];
     $content       = $_POST['content'];
     $questionType  = $_POST['question_type'] ?? '';

     $validator = new Validator($_POST);
     $validator->required('title', 'タイトル');
     $validator->required('content', '問題の内容');
     $validator->required('question_type', '問題種別');

     $errors = $validator->getErrors();
     if ($questionType === 'multiple_choice') {
          if (empty($_POST['options'])) {
               $errors['options'] = '選択肢を1つ以上入力してください';
          }
          if (!isset($_POST['correct_option'])) {
               $errors['correct_option'] = '正解を選択してください';
          }
     }

     if (empty($errors)) {
          $explanation   = $_POST['explanation'];
          // 問題種別に応じて正解を取得（multiple_choiceはquestion_optionsで管理）
          $correctAnswer = match ($questionType) {
               'short_answer'    => $_POST['short_answer_answer'] ?? '',
               'true_false'      => $_POST['true_false_answer'] ?? '',
               'multiple_choice' => null,
               default           => null,
          };

          $questions = $db->execute(
               "INSERT INTO questions (teacher_id , category_id , title , content, question_type, correct_answer, explanation) VALUES (? , ? , ? , ? , ? , ? , ?)",
               [$teacherId, $categoryId, $title, $content, $questionType, $correctAnswer, $explanation]
          );

          $questionId = $db->lastInsertId();

          if ($questionType === 'multiple_choice') {
               foreach ($_POST['options'] as $index => $option) {
                    $isCorrect = ($_POST['correct_option'] == $index) ? 1 : 0;
                    $db->execute(
                         "INSERT INTO question_options (question_id, option_text, is_correct) VALUES (?,?,?)",
                         [$questionId, $option['text'], $isCorrect]
                    );
               }
          }

          flash_set('問題を作成しました');
          header('Location: /teacher/questions/index.php');
          exit;
     }
}
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<link rel="stylesheet" href="../../../css/questions_create.css">

<!-- フォームのHTML -->
<form method="POST" id="">
     <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
     <hr>
     <h4>単元の選択</h4>
     <select name="category_id">
          <option value="">未選択</option>
          <?php foreach ($categories as $category): ?>
               <option value="<?= h($category['id']) ?>">
                    <?= h($category['name']) ?>
               </option>
          <?php endforeach; ?>
     </select>
     <hr>
     <h4>タイトルを入力</h4>
     <input type="text" name="title" id="">
     <?php show_error($errors ?? [], 'title') ?>

     <hr>

     <h4>問題の内容を入力</h4>
     <textarea name="content" id="" cols="30" rows="10"></textarea>
     <?php show_error($errors ?? [], 'content') ?>

     <hr>

     <h4>問題種別を選択</h4>
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

     <h4>回答を入力</h4>
     <div id="answer-area">
          <input type="text" name="short_answer_answer" id="" value="">

          <input type="text" name="true_false_answer" id="" value="">

          <div id="options-area">
               <table border="1">
                    <thead>
                         <tr>
                              <th>選択肢</th>
                              <th>正解</th>
                              <th>削除</th>
                         </tr>
                    </thead>
                    <tbody id="option-rows">
                         <tr>
                              <td><input type="text" name="options[0][text]" placeholder="選択肢を入力" size="130"></td>
                              <td><input type="radio" name="correct_option" value="0">正解</td>
                              <td></td>
                         </tr>
                    </tbody>
               </table>
               <button type="button" id="add-option">選択肢を追加</button>
          </div>
          <?php show_error($errors ?? [], 'options') ?>
          <?php show_error($errors ?? [], 'correct_option') ?>

     </div>

     <hr>

     <h4>解説を入力</h4>
     <textarea name="explanation" id="" cols="30" rows="10"></textarea>

     <hr>

     <div class="text-center">
          <input type="submit" value="登録">
     </div>

</form>

<hr>
<a href="/teacher/dashboard.php">
     <h4>ダッシュボードに戻る</h4>
</a>
<hr>

<script>
     'use strict';
     // ラジオボタンの選択に応じて回答入力欄を切り替える
     // 表示ロジックはCSSの[data-answer-type]セレクタにて（questions_create.css参照）
     const radios = document.querySelectorAll('input[name="question_type"]');
     const answerArea = document.querySelector('#answer-area');

     radios.forEach(radio => {
          radio.addEventListener('change', () => {
               answerArea.dataset.answerType = radio.value;
          });
     });

     // 選択肢追加処理
     const addOption = document.getElementById('add-option');
     let id = 1;
     addOption.addEventListener('click', () => {
          document.getElementById('option-rows').insertAdjacentHTML('beforeend', `
        <tr>
            <td><input type="text" name="options[${id}][text]" placeholder="選択肢を入力"></td>
            <td><input type="radio" name="correct_option" value="${id}">正解</td>
            <td><button type="button" class="delete_button" onclick="this.closest('tr').remove()">削除</button></td>
        </tr>
    `);
          id++;
     });
</script>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>