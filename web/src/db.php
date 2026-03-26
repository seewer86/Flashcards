<?php
// src/db.php
$config = require __DIR__ . '/../config/config.php';

$DB_HOST = $config['db']['host'];
$DB_USER = $config['db']['user'];
$DB_PASS = $config['db']['pass'];
$DB_NAME = $config['db']['name'];

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo 'DB-Fehler';
    exit;
}
$mysqli->set_charset($config['db']['charset'] ?? 'utf8mb4');
