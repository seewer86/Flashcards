<?php
// public/login_check.php
require __DIR__ . '/../src/auth.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    header('Location: login.php');
    exit;
}

$user = login_user($username, $password);

if (!$user) {
    // optional: Fehlermeldung in Session schreiben
    header('Location: login.php');
    exit;
}

$_SESSION['user'] = $user;

header('Location: learn.php');
exit;
