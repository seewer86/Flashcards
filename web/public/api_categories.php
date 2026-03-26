<?php
// api_categories.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../src/auth.php';
require_login();

$user = current_user();
$userId = (int)$user['id'];

$stmt = $mysqli->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
$cats = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

echo json_encode([
    'success' => true,
    'categories' => $cats
]);
