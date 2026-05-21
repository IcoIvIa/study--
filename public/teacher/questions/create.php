<?php
// ・title（問題タイトル）
// ・content（問題内容）
// ・question_type（種別：multiple_choice / short_answer / true_false）
// ・correct_answer（正解）
// ・explanation（解説）
// ・teacher_id（誰が作ったか）
?>

<?php require_once __DIR__ . '/../../../templates/header.php'; ?>

<link rel="stylesheet" href="../../../css/common.css">

<!-- フォームのHTML -->
<p>タイトルを入力</p>
<input type="text" name="title" id="">

<p>問題の内容を入力</p>
<textarea name="content" id="" cols="30" rows="10">

</textarea>

<p>問題種別を選択</p>
<label for="">
     <input type="radio" name="question_type" value="multiple_choice" id="">
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
<form>
     <input type="text" name="multiple_choice_answer" id="" value="multiple_choice_answer_test message" class="hidden">
     <input type="text" name="short_answer_answer" id="" value="short_answer_answer_test message" class="hidden">
     <input type="text" name="true_false_answer" id="" value="true_false_answer_test message" class="hidden">
</form>


<p>解説を入力</p>
<textarea name="explanation" id="" cols="30" rows="10">

</textarea>

<script>
     // 
     'use strict';
     const radios = document.querySelectorAll('input[name="question_type"]');

     const form = document.querySelector('form');

     radios.forEach(radio => {
          radio.addEventListener('change', () => {
               form.dataset.answerType = radio.value;
          });
     });
</script>
<?php require_once __DIR__ . '/../../../templates/footer.php'; ?>