<?php echo "index.phpが表示されています";
$base = '//' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <h1>Study!! 開発用ジャンプページ</h1>

    <div>
        <h2>共通</h2>
        <ul>
            <li><a href="<?= $base ?>/register.php">新規登録</a></li>
            <li><a href="<?= $base ?>/login.php">ログイン</a></li>
            <li><a href="<?= $base ?>/logout.php">ログアウト</a></li>
            <li><a href="<?= $base ?>/mypage.php">マイページ</a></li>
        </ul>
    </div>

    <div>
        <h2>生徒</h2>
        <ul>
            <li><a href="<?= $base ?>/student/dashboard.php">ダッシュボード</a></li>
            <li><a href="<?= $base ?>/student/questions/index.php">問題一覧</a></li>
            <li><a href="<?= $base ?>/student/questions/show.php?id=1">問題詳細（id=1）</a></li>
            <li><a href="<?= $base ?>/student/messages/index.php">質問一覧</a></li>
            <li><a href="<?= $base ?>/student/messages/create.php">質問作成</a></li>
            <li><a href="<?= $base ?>/student/messages/show.php?id=1">質問スレッド（id=1）</a></li>
        </ul>
    </div>

    <div>
        <h2>先生</h2>
        <ul>
            <li><a href="<?= $base ?>/teacher/dashboard.php">ダッシュボード</a></li>
            <li><a href="<?= $base ?>/teacher/questions/index.php">問題管理</a></li>
            <li><a href="<?= $base ?>/teacher/questions/create.php">問題作成</a></li>
            <li><a href="<?= $base ?>/teacher/questions/edit.php?id=1">問題編集（id=1）</a></li>
            <li><a href="<?= $base ?>/teacher/questions/answers.php?id=1">回答一覧（id=1）</a></li>
            <li><a href="<?= $base ?>/teacher/messages/index.php">質問管理</a></li>
            <li><a href="<?= $base ?>/teacher/messages/show.php?id=1">質問返信（id=1）</a></li>
        </ul>
    </div>
</body>
</html>