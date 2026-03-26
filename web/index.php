<?php
// index.php – entscheidet zwischen Setup und App

if (!file_exists(__DIR__ . '/config/config.php')) {
    header('Location: setup/index.php');
    exit;
}

header('Location: public/login.php');
exit;
