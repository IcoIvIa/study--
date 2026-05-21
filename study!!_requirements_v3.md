Study!! 要件定義 v3

バニラPHP + バニラJavaScript + MySQL による学習支援Webアプリ
v3：correct_answer設計の見直し・Tailwind削除・CSSファイルパス追加


1. システム概要
項目内容システム名Study!!目的先生と生徒が利用する学習支援Webアプリ技術スタックPHP / JavaScript / HTML / CSS / MySQLバージョン管理GitHub
概要

生徒は問題に回答し、先生に質問できる
先生は問題を作成し、生徒からの質問に回答できる
ロールごとに利用できる機能を制御する


2. ユーザー種別と権限
機能生徒先生問題閲覧○○問題作成・編集・削除×○問題回答○×質問送信○×質問返信×○生徒進捗確認×○会員登録○× (管理者が作成)

3. 機能要件
3-1. 共通機能

ユーザー登録（生徒のみ。先生アカウントは管理者が作成）
ログイン / ログアウト
プロフィール編集
パスワード変更
フラッシュメッセージ（操作後の成功・エラー通知）

3-2. 生徒機能

問題一覧表示（ページネーション付き）
問題詳細表示（問題種別に応じた表示）
回答送信・正誤判定
質問スレッド作成
質問・返信履歴表示
回答履歴確認・正答率グラフ表示（Chart.js）

3-3. 先生機能

問題CRUD（選択式・記述式・○×対応）
選択肢の追加・編集・削除
回答一覧表示・正誤確認
質問スレッドへの返信
生徒別進捗確認（正答率・回答数）


4. データベース設計
4-1. users
sqlCREATE TABLE users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role         ENUM('student', 'teacher') NOT NULL,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
);
4-2. questions
sqlCREATE TABLE questions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    teacher_id    INT UNSIGNED NOT NULL,
    title         VARCHAR(255) NOT NULL,
    content       TEXT NOT NULL,
    question_type ENUM('multiple_choice', 'short_answer', 'true_false') NOT NULL DEFAULT 'short_answer',
    correct_answer VARCHAR(255) NULL,
    -- short_answer : テキストをそのまま格納
    -- true_false   : "true" または "false" を格納
    -- multiple_choice : NULL（正解は question_options.is_correct で管理）
    explanation   TEXT,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
);
4-3. question_options（選択肢テーブル）
sql-- multiple_choice 問題の選択肢を管理
-- 正解は is_correct = 1 で管理する（correct_answer カラムは使用しない）
CREATE TABLE question_options (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    is_correct  TINYINT(1) NOT NULL DEFAULT 0,
    sort_order  INT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
4-4. answers
sqlCREATE TABLE answers (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id INT UNSIGNED NOT NULL,
    student_id  INT UNSIGNED NOT NULL,
    answer_text VARCHAR(255) NOT NULL,
    is_correct  TINYINT(1) NOT NULL DEFAULT 0,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id)  REFERENCES users(id) ON DELETE CASCADE
);
4-5. messages
sqlCREATE TABLE message_threads (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id INT UNSIGNED NOT NULL,
    teacher_id INT UNSIGNED,               -- 返信した先生（NULL=未返信）
    title      VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE message_replies (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    message_thread_id INT UNSIGNED NOT NULL,
    sender_role       ENUM('student', 'teacher') NOT NULL,
    sender_id         INT UNSIGNED NOT NULL,
    body              TEXT NOT NULL,
    created_at        DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (message_thread_id) REFERENCES message_threads(id) ON DELETE CASCADE
);
正解の判定ロジック（v3変更点）
question_typecorrect_answerの値正解判定方法short_answerテキスト文字列answers.answer_text === questions.correct_answertrue_false"true" or "false"answers.answer_text === questions.correct_answermultiple_choiceNULLquestion_options.is_correct = 1 の選択肢IDと一致するか
ER図（概略）
users ─────────────┬──── questions ──── question_options
  │  (teacher_id)  │
  │                └──── answers
  │  (student_id)
  └──── message_threads ──── message_replies

5. ファイル構成
study!!/
├── public/                      ← Webルート（ここだけ公開）
│   ├── index.php                   トップページ
│   ├── register.php                会員登録
│   ├── login.php                   ログイン
│   ├── logout.php                  ログアウト
│   ├── mypage.php                  マイページ
│   ├── css/
│   │   └── common.css              共通スタイルシート
│   ├── student/
│   │   ├── dashboard.php           生徒ダッシュボード
│   │   ├── questions/
│   │   │   ├── index.php           問題一覧
│   │   │   └── show.php            問題詳細・回答
│   │   └── messages/
│   │       ├── index.php           質問一覧
│   │       ├── create.php          質問作成
│   │       └── show.php            質問スレッド
│   └── teacher/
│       ├── dashboard.php           先生ダッシュボード
│       ├── questions/
│       │   ├── index.php           問題管理一覧
│       │   ├── create.php          問題作成
│       │   ├── edit.php            問題編集
│       │   └── answers.php         回答一覧
│       └── messages/
│           ├── index.php           質問管理一覧
│           └── show.php            質問スレッド・返信
├── src/                         ← アプリケーションロジック（非公開）
│   ├── Database.php                PDOラッパー
│   ├── Auth.php                    認証・セッション管理
│   ├── CSRF.php                    CSRFトークン管理
│   ├── Validator.php               バリデーション
│   └── helpers.php                 共通関数（h()など）
├── templates/                   ← HTMLテンプレート
│   ├── header.php
│   └── footer.php
└── config/
    └── database.php             ← DB接続情報（.gitignoreに追加）

ポイント：public/ 以外をWebルートに置かないことで、ソースコードやDB設定ファイルへの直接アクセスを防ぐ。


6. セキュリティ要件（具体的実装）
脅威対策実装方法パスワード漏洩ハッシュ化password_hash() / password_verify()SQLインジェクションプリペアドステートメントPDO + プレースホルダー ?XSS出力エスケープhtmlspecialchars($val, ENT_QUOTES, 'UTF-8') を h() 関数でラップCSRFトークン検証セッションにトークン保存、フォーム送信時に照合セッション固定攻撃セッションID再生成ログイン直後に session_regenerate_id(true)ディレクトリトラバーサルパスバリデーションbasename() + ホワイトリスト不正アクセスロール確認各ページ冒頭で $auth->requireRole('teacher')
共通ヘルパー関数例
php// src/helpers.php

// XSS対策：出力時は必ずこの関数を通す
function h(string $val): string {
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

// CSRF対策：トークン生成
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF対策：トークン検証
function csrf_verify(): void {
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('不正なリクエストです');
    }
}

7. 画面一覧
共通
画面名URL説明トップページ/サービス説明・ログイン誘導新規登録/register.php生徒アカウント作成ログイン/login.phpメール・パスワード認証マイページ/mypage.phpプロフィール・パスワード変更
生徒
画面名URL説明ダッシュボード/student/dashboard.php正答率グラフ・最近の回答履歴問題一覧/student/questions/index.phpページネーション・フィルター付き問題詳細/student/questions/show.php?id=種別に応じた回答フォーム質問一覧/student/messages/index.php自分の質問スレッド一覧質問作成/student/messages/create.php新規質問送信質問スレッド/student/messages/show.php?id=やりとり表示
先生
画面名URL説明ダッシュボード/teacher/dashboard.php未返信の質問数・生徒回答状況問題管理/teacher/questions/index.php問題一覧・CRUD操作問題作成/teacher/questions/create.php種別選択・選択肢入力問題編集/teacher/questions/edit.php?id=内容・選択肢の更新回答一覧/teacher/questions/answers.php?id=生徒別回答・正誤確認質問管理/teacher/messages/index.php全質問スレッド一覧質問返信/teacher/messages/show.php?id=スレッド表示・返信送信

8. 開発フェーズ
フェーズ1 — MVP（目安：2週間）

 DB設計・テーブル作成
 PDOラッパー・共通関数整備
 ユーザー登録・ログイン・ログアウト
 ロール制御（Auth クラス）
 問題CRUD（先生）
 問題回答・正誤判定（生徒）

フェーズ2 — 基本完成（目安：2週間）

 質問スレッド作成・返信機能
 生徒ダッシュボード（回答履歴）
 先生ダッシュボード（未返信通知・進捗）
 ページネーション
 フラッシュメッセージ

フェーズ3 — 品質向上（目安：1週間）

 セキュリティ強化（CSRF・XSS・セッション固定対策）
 バリデーション強化
 レスポンシブUI（vanilla CSS）
 エラーハンドリング（404・403ページ）

フェーズ4 — 差別化（任意）

 正答率グラフ（Chart.js）
 問題検索・フィルター機能
 生徒別進捗グラフ（先生向け）
 デプロイ（さくらVPS / Renderなど）


9. v2 からの主な変更点
項目v2v3correct_answer の設計multiple_choice のとき選択肢IDを格納NULL を許容。正解は question_options.is_correct で管理CSSフレームワークTailwind CSS を使用使用しない。public/css/common.css で管理技術スタック表記XAMPP を記載削除（PHP組み込みサーバー + Homebrew MySQL を使用）フェーズ1進捗未着手DB・認証・ログイン・登録・ログアウト・ダッシュボード完了

10. ポートフォリオとしての強み
この1作品で以下を示せます。
技術領域実装内容認証セッション管理・パスワードハッシュ・セッション固定攻撃対策ロール管理student / teacher の権限制御CRUD問題・選択肢・スレッドの作成・編集・削除DB設計正規化・外部キー・ENUM型・スレッド型メッセージ設計セキュリティCSRF・XSS・SQLインジェクション対策UI/UXレスポンシブ・ダッシュボード・ページネーションJavaScriptChart.js による正答率グラフ・動的フォーム（選択肢追加）

Study!! 要件定義 v3 — 作成日：2026年