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

    <p>testmassage header ok </p>

    <?php $flash = flash_get(); ?>
    <?php if ($flash): ?>
        <p><?= h($flash['message']) ?></p>
        <?php endif; ?>