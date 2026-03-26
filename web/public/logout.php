<?php
// logout.php
require __DIR__ . '/../src/auth.php';

$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;
