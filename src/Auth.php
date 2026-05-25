<?php

class Auth {


    public function login($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
    }

    public function logout() {
        $_SESSION = [];
        session_destroy();
        header('Location: /login.php');
    }

    public function requireRole($role) {
        if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            header('Location: /login.php');
            exit;
        }
    }

    public function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login.php');
        exit;
    }
}
}