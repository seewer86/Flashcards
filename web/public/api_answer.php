<?php
// api_answer.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../src/auth.php';
require_login();

$user = current_user();
$userId = (int)$user['id'];

$data = json_decode(file_get_contents('php://input'), true);
$cardId = (int)($data['card_id'] ?? 0);
$isCorrect = !empty($data['is_correct']);

if ($cardId <= 0) {
    echo json_encode(['success' => false, 'message' => 'card_id fehlt']);
    exit;
}

$sql = "SELECT id, correct_count FROM user_progress WHERE user_id = ? AND card_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('ii', $userId, $cardId);
$stmt->execute();
$res = $stmt->get_result();
$progress = $res->fetch_assoc();
$stmt->close();

if ($progress) {
    $correctCount = (int)$progress['correct_count'];
    $correctCount = $isCorrect ? $correctCount + 1 : 0;
    $sql2 = "UPDATE user_progress SET correct_count = ?, last_seen_at = NOW() WHERE id = ?";
    $stmt2 = $mysqli->prepare($sql2);
    $stmt2->bind_param('ii', $correctCount, $progress['id']);
    $stmt2->execute();
    $stmt2->close();
} else {
    $correctCount = $isCorrect ? 1 : 0;
    $sql3 = "INSERT INTO user_progress (user_id, card_id, correct_count, last_seen_at)
             VALUES (?, ?, ?, NOW())";
    $stmt3 = $mysqli->prepare($sql3);
    $stmt3->bind_param('iii', $userId, $cardId, $correctCount);
    $stmt3->execute();
    $stmt3->close();
}

echo json_encode([
    'success' => true,
    'correct' => $isCorrect,
    'correctCount' => $correctCount,
    'done' => $correctCount >= 3
]);
