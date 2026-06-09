<?php
require_once __DIR__.'/../src/helpers.php';
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'study!!' ?></title>
    <link rel="stylesheet" href="/css/common.css">
</head>

<body>

<header class="header">
    <div class="container flex-between">

        <h1 class="logo">
            <a href="/">study!!</a>
        </h1>

        <?php if (isset($_SESSION['user_name'])) : ?>
            <nav class="nav">
                <span><?= h($_SESSION['user_name']) ?>さん</span>
                <a href="/mypage.php">マイページ</a>
                <a href="/logout.php">ログアウト</a>
            </nav>
        <?php endif; ?>

    </div>
</header>

<main class="container">

<?php $flash = flash_get(); ?>
<?php if ($flash): ?>
    <p class="card"><?= h($flash['message']) ?></p>
<?php endif; ?>