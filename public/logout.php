<?php

session_start();

require_once __DIR__.'/../src/Auth.php';

$auth = new Auth();
$auth -> logout();