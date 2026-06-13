<?php

function h(string $val): string {
    return htmlspecialchars($val,ENT_QUOTES,'UTF-8');
}

function csrf_token(): string {
    if(empty($_SESSION['csrf_token'])){
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify() : void {
    if (!hash_equals($_SESSION['csrf_token'] ?? '',$_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        exit('不正なリクエストです');
    }
    unset($_SESSION['csrf_token']);
}

function flash_set(string $message, string $type = 'success'): void {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function flash_get(): ?array {
    if (isset($_SESSION['flash'])){
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function show_error(array $errors, string $field): void {
    if (isset($errors[$field])) {
        echo '<p class="color-red">' . h($errors[$field]) . '</p>';
    }
}