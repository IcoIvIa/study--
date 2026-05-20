<?php

session_start();

require_once __DIR__ .'/../../src/Auth.php';
require_once __DIR__ .'/../../src/helpers.php';
require_once __DIR__ .'/../../src/Database.php';

$db = new Database();
$auth =new Auth();

$auth->requireRole('student');

$pageTitle = '生徒ページ|study!!';
?>

<?php require_once __DIR__.'/../../templates/header.php'; ?>

<!-- ダッシュボードの中身 -->
<p>testmassage</p>

<?php require_once __DIR__.'/../../templates/footer.php'; ?>

