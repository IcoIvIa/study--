<?php
// ・title（問題タイトル）
// ・content（問題内容）
// ・question_type（種別：multiple_choice / short_answer / true_false）
// ・correct_answer（正解）
// ・explanation（解説）
// ・teacher_id（誰が作ったか）
?>

<?php require_once __DIR__ .'/../templates/header.php'; ?>

<!-- フォームのHTML -->
 <p>タイトルを入力</p>
<input type="text" name="title" id="">

 <p>問題の内容を入力</p>
<input type="text" name="content" id="">

<p>問題種別を選択</p>
<label for=""><input type="checkbox" name="multiple_choice" id="">選択問題</label><br>
<label for=""><input type="checkbox" name="short_answer" id="">記述問題</label><br>
<label for=""><input type="checkbox" name="true_false" id="">正誤問題</label><br>

<?php match (expression) {
     => ,
     => ,
}

<p>解説を入力</p>
<input type="text" name="explanation" id="">
<?php require_once __DIR__ . '/../templates/footer.php'; ?>