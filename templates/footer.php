<?php if (isset($_SESSION['user_role'])) : ?>
    <hr>
    <hr>
    <nav class="menu margin-bottom">

        <?php if ($_SESSION['user_role'] === 'student') : ?>

            <h2 class="tight">生徒メニュー</h2>

            <a href="/student/questions/index.php">問題</a>
            <a href="/student/messages/index.php">質問を見る</a>
            <a href="/student/messages/create.php">質問する</a>
            <a href="/student/dashboard.php">ダッシュボード</a>

        <?php elseif ($_SESSION['user_role'] === 'teacher') : ?>

            <h2 class="tight">先生メニュー</h2>

            <a href="/teacher/messages/index.php">質問一覧</a>
            <a href="/teacher/categories/index.php">単元一覧</a>
            <a href="/teacher/questions/index.php">問題一覧</a>

        <?php endif; ?>

    </nav>

<?php endif; ?>

</body>

</html>