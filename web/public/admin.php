<?php
// admin.php
require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/i18n.php';
require_login();

$user = current_user();

$user      = $_SESSION['user'] ?? null;
$authMode  = $_SESSION['auth_mode'] ?? 'local';
$isAdmin   = $user['is_admin'] ?? 0;


// Lernfortschritt des aktuellen Benutzers zurücksetzen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'reset_progress_user') {
    $stmt = $mysqli->prepare("DELETE FROM user_progress WHERE user_id = ?");
    $stmt->bind_param('i', $user['id']);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Neue Kategorie anlegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'create_category') {
    $name = trim($_POST['category_name'] ?? '');
    if ($name !== '') {
        $stmt = $mysqli->prepare("INSERT INTO categories (user_id, name) VALUES (?, ?)");
        $stmt->bind_param('is', $user['id'], $name);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php');
    exit;
}

// Karte löschen (nur eigene)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $stmt = $mysqli->prepare("DELETE FROM cards WHERE id = ? AND user_id = ?");
        $stmt->bind_param('ii', $id, $user['id']);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php');
    exit;
}

// Neue Karte speichern (für aktuellen User)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'create') {
    $question   = trim($_POST['question'] ?? '');
    $answer     = trim($_POST['answer'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($categoryId <= 0) {
        $stmt = $mysqli->prepare("SELECT id FROM categories WHERE user_id = ? ORDER BY id ASC LIMIT 1");
        $stmt->bind_param('i', $user['id']);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();

        if (!$row) {
            die('Es existiert keine Kategorie.');
        }

        $categoryId = (int)$row['id'];
    }

    if ($question !== '' && $answer !== '') {
        $stmt = $mysqli->prepare(
            "INSERT INTO cards (user_id, category_id, question, answer) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param('iiss', $user['id'], $categoryId, $question, $answer);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php');
    exit;
}

// Karte aktualisieren (nur eigene)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'update') {
    $id         = (int)($_POST['id'] ?? 0);
    $question   = trim($_POST['question'] ?? '');
    $answer     = trim($_POST['answer'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);

    if ($id > 0 && $question !== '' && $answer !== '') {
        $stmt = $mysqli->prepare(
            "UPDATE cards SET category_id = ?, question = ?, answer = ? WHERE id = ? AND user_id = ?"
        );
        $stmt->bind_param('issii', $categoryId, $question, $answer, $id, $user['id']);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin.php');
    exit;
}

// Kategorien des Users laden
$stmtCat = $mysqli->prepare("SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
$stmtCat->bind_param('i', $user['id']);
$stmtCat->execute();
$resCat = $stmtCat->get_result();
$categories = $resCat->fetch_all(MYSQLI_ASSOC);
$stmtCat->close();

// Karten des aktuellen Users laden
$stmtList = $mysqli->prepare(
    "SELECT id, category_id, question, answer FROM cards WHERE user_id = ? ORDER BY id DESC"
);
$stmtList->bind_param('i', $user['id']);
$stmtList->execute();
$result = $stmtList->get_result();
$cards = $result->fetch_all(MYSQLI_ASSOC);
$stmtList->close();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLocale) ?>">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta name="apple-mobile-web-app-capable" content="yes">
 <meta name="apple-mobile-web-app-status-bar-style" content="default">
 <link rel="apple-touch-icon" href="../public/icons/apple-touch-icon.png">
 <title><?= htmlspecialchars(t('admin') . ' - ' . t('app_title')) ?></title>
 <style>
   body { font-family: system-ui, sans-serif; padding:1rem; background:#f5f5f5;margin:0; display:flex; justify-content:center; }
   .wrap { width:100%; max-width:700px; }
   a { color:#1976d2; text-decoration:none; }
   a:hover { text-decoration:underline; }
   form { margin-bottom:2rem; max-width:600px; }
   textarea { width:100%; margin:.3rem 0; font-family:inherit; }
   input, button, select { font-family:inherit; }
   button { padding:.4rem .8rem; margin-top:.3rem; }
   .card { border:1px solid #ccc; border-radius:6px; padding:.5rem; margin-bottom:.5rem; max-width:600px; background:#fff; }
   .topbar { margin-bottom:1rem; font-size:14px; }
   label { display:block; margin-top:.3rem; }
   .logo { text-align:center; margin-bottom:0.75rem; }
   .logo img { max-height:60px; width:auto; }
 </style>
</head>
<body>
<div class="wrap">
 <div class="logo">
    <img src="logo.png" alt="Karteikarten Logo">
 </div>
 <div class="topbar">
   <?= htmlspecialchars(t('app_title')) ?>: <?= htmlspecialchars($user['display_name']) ?> |
   <a href="learn.php"><?= htmlspecialchars(t('learn')) ?></a> |
   <?php if ($authMode === 'local' && $isAdmin): ?>
      <a href="admin_users.php"><?= htmlspecialchars(t('admin_users_nav') ?? 'User management') ?></a> | 
   <?php endif; ?>
   <a href="logout.php"><?= htmlspecialchars(t('logout')) ?></a>
 </div>

 <h1><?= htmlspecialchars(t('admin') . ' ' . t('app_title')) ?></h1>

 <h2><?= htmlspecialchars(t('admin_add_category') ?? 'Kategorie hinzufügen') ?></h2>
 <form method="post">
   <input type="hidden" name="mode" value="create_category">
   <label><?= htmlspecialchars(t('category_name') ?? 'Name') ?>:
     <input type="text" name="category_name" required>
   </label>
   <button type="submit"><?= htmlspecialchars(t('admin_save_category') ?? 'Kategorie speichern') ?></button>
 </form>

 <h2><?= htmlspecialchars(t('admin_new_card') ?? 'Neue Karte') ?></h2>
 <form method="post">
   <input type="hidden" name="mode" value="create">
   <label><?= htmlspecialchars(t('category')) ?>:
     <select name="category_id">
       <option value="0">(<?= htmlspecialchars(t('category_none') ?? 'ohne Kategorie') ?>)</option>
       <?php foreach ($categories as $cat): ?>
         <option value="<?= (int)$cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
       <?php endforeach; ?>
     </select>
   </label>
   <label><?= htmlspecialchars(t('question') ?? 'Frage') ?>:
     <textarea name="question" rows="3" required></textarea>
   </label>
   <label><?= htmlspecialchars(t('answer') ?? 'Antwort') ?>:
     <textarea name="answer" rows="2" required></textarea>
   </label>
   <button type="submit"><?= htmlspecialchars(t('save') ?? 'Speichern') ?></button>
 </form>

 <h2><?= htmlspecialchars(t('admin_reset_progress_title') ?? 'Deinen Fortschritt zurücksetzen') ?></h2>
 <form method="post"
       onsubmit="return confirm('<?= htmlspecialchars(t('admin_reset_confirm') ?? 'Wirklich alle Lernfortschritte zurücksetzen?') ?>');">
   <input type="hidden" name="mode" value="reset_progress_user">
   <button type="submit" style="background:#d32f2f;color:#fff;border:none;">
     <?= htmlspecialchars(t('admin_reset_button') ?? 'Meine gelösten Karten zurücksetzen') ?>
   </button>
 </form>

 <h2><?= htmlspecialchars(t('admin_your_cards') ?? 'Deine Karten') ?></h2>
 <?php foreach ($cards as $card): ?>
   <div class="card">
     <form method="post" style="margin:0;">
       <input type="hidden" name="mode" value="update">
       <input type="hidden" name="id" value="<?= (int)$card['id'] ?>">

       <label><?= htmlspecialchars(t('category')) ?>:
         <select name="category_id">
           <option value="0">(<?= htmlspecialchars(t('category_none') ?? 'ohne Kategorie') ?>)</option>
           <?php foreach ($categories as $cat): ?>
             <option value="<?= (int)$cat['id'] ?>"
               <?= (int)$card['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
               <?= htmlspecialchars($cat['name']) ?>
             </option>
           <?php endforeach; ?>
         </select>
       </label>

       <label><?= htmlspecialchars(t('question') ?? 'Frage') ?>:
         <textarea name="question" rows="2"><?= htmlspecialchars($card['question']) ?></textarea>
       </label>
       <label><?= htmlspecialchars(t('answer') ?? 'Antwort') ?>:
         <textarea name="answer" rows="2"><?= htmlspecialchars($card['answer']) ?></textarea>
       </label>
       <button type="submit"><?= htmlspecialchars(t('save') ?? 'Speichern') ?></button>
       <a href="admin.php?delete=<?= (int)$card['id'] ?>"
          onclick="return confirm('<?= htmlspecialchars(t('admin_delete_confirm') ?? 'Diese Karte wirklich löschen?') ?>');">
         <?= htmlspecialchars(t('delete') ?? 'Löschen') ?>
       </a>
     </form>
   </div>
 <?php endforeach; ?>
</div>
</body>
</html>
