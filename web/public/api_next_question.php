<?php
// api_next_question.php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../src/auth.php';
require_login();

$user = current_user();
$userId = (int)$user['id'];
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Gesamtzahl aller Karten dieses Users (optional gefiltert nach Kategorie)
$sqlTotal = "
SELECT COUNT(*) AS c
FROM cards
WHERE user_id = ?
  AND (category_id = ? OR ? = 0)
";
$stmtTotal = $mysqli->prepare($sqlTotal);
$stmtTotal->bind_param('iii', $userId, $categoryId, $categoryId);
$stmtTotal->execute();
$resTotal = $stmtTotal->get_result();
$rowTotal = $resTotal->fetch_assoc();
$stmtTotal->close();
$totalCards = (int)$rowTotal['c'];

// Anzahl gelernter Karten (correct_count >= 3) für diesen User und Kategorie
$sqlDone = "
SELECT COUNT(*) AS c
FROM user_progress up
JOIN cards c ON c.id = up.card_id
WHERE up.user_id = ?
  AND up.correct_count >= 3
  AND c.user_id = ?
  AND (c.category_id = ? OR ? = 0)
";
$stmtDone = $mysqli->prepare($sqlDone);
$stmtDone->bind_param('iiii', $userId, $userId, $categoryId, $categoryId);
$stmtDone->execute();
$resDone = $stmtDone->get_result();
$rowDone = $resDone->fetch_assoc();
$stmtDone->close();
$doneCards = (int)$rowDone['c'];
$remainingCards = max(0, $totalCards - $doneCards);

// Eine ungelernte Karte dieses Users ziehen
$sql = "
SELECT c.id, c.question, c.answer
FROM cards c
LEFT JOIN user_progress up
  ON up.card_id = c.id AND up.user_id = ?
WHERE c.user_id = ?
  AND (c.category_id = ? OR ? = 0)
  AND (up.correct_count IS NULL OR up.correct_count < 3)
ORDER BY RAND()
LIMIT 1
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param('iiii', $userId, $userId, $categoryId, $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$card = $result->fetch_assoc();
$stmt->close();

if (!$card) {
    echo json_encode([
        'success' => true,
        'finished' => true,
        'stats' => [
            'total'      => $totalCards,
            'done'       => $doneCards,
            'remaining'  => $remainingCards
        ]
    ]);
    exit;
}

// Falsche Antworten nur aus Karten dieses Users (gleiche Kategorie)
$falseCount = rand(2, 4);
$sql2 = "
SELECT answer
FROM cards
WHERE id != ?
  AND user_id = ?
  AND (category_id = ? OR ? = 0)
ORDER BY RAND()
LIMIT ?
";
$stmt2 = $mysqli->prepare($sql2);
$stmt2->bind_param('iiiii', $card['id'], $userId, $categoryId, $categoryId, $falseCount);
$stmt2->execute();
$res2 = $stmt2->get_result();
$falseAnswers = [];
while ($row = $res2->fetch_assoc()) {
    $falseAnswers[] = $row['answer'];
}
$stmt2->close();

// Antworten mischen
$options = $falseAnswers;
$options[] = $card['answer'];
shuffle($options);

echo json_encode([
    'success' => true,
    'finished' => false,
    'stats' => [
        'total'      => $totalCards,
        'done'       => $doneCards,
        'remaining'  => $remainingCards
    ],
    'card' => [
        'id'            => (int)$card['id'],
        'question'      => $card['question'],
        'correctAnswer' => $card['answer']
    ],
    'options' => $options
]);
