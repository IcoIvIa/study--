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
}