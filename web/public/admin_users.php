<?php
require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/i18n.php';
require_login();

if (!current_user()['is_admin']) {
    http_response_code(403);
    exit('Verboten');
}

if (($config['auth']['mode'] ?? 'local') !== 'local') {
    exit('Benutzerverwaltung ist nur im Local-Modus aktiv.');
}

// Neuer lokaler Benutzer speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['mode'] ?? '') === 'create_user') {
    $username    = trim($_POST['username'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');
    $password    = $_POST['password'] ?? '';
    $isAdmin     = isset($_POST['is_admin']) ? 1 : 0;

    if ($username !== '' && $password !== '') {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$exists) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare(
                "INSERT INTO users (username, display_name, password_hash, is_admin)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('sssi', $username, $displayName, $passwordHash, $isAdmin);
            $stmt->execute();
            $stmt->close();
        }
    }

    header('Location: admin_users.php');
    exit;
}

// Benutzer laden
$stmt = $mysqli->prepare(
    "SELECT id, username, display_name, is_admin
     FROM users
     ORDER BY username ASC"
);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLocale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('admin_users_title') ?? 'Benutzerverwaltung') ?></title>
    <style>
    body { font-family: system-ui, sans-serif; margin: 0; padding: 1rem; background: #f5f5f5; display: flex; justify-content: center; }
    .wrap { width: 100%; max-width: 700px; }
    a { color:#1976d2; text-decoration:none; }
    a:hover { text-decoration:underline; }
    h1 { margin-top:0; margin-bottom:1rem; font-size:26px; }
    h2 { margin:1.2rem 0 .6rem; font-size:18px; }
    form { margin-bottom:1.5rem; }
    label { display:block; margin-top:.3rem; font-size:14px; }
    input[type="text"], input[type="password"] { width:100%; box-sizing:border-box; font-size:14px; }
    button { padding:.4rem .8rem; margin-top:.3rem; font-size:14px; }
    table { border-collapse:collapse; margin-top:.5rem; width:100%; }
    th, td { border:1px solid #ccc; padding:.3rem .6rem; text-align:left; font-size:14px; }
    .topbar { margin-bottom:1rem; font-size:14px; }
    .logo { text-align:center; margin-bottom:0.75rem; }
    .logo img { max-height:40px; width:auto; }
    </style>
</head>
<body>
<div class="wrap">
  <div class="logo">
    <img src="logo.png" alt="Karteikarten Logo">
  </div>
  <div class="topbar">
        <a href="admin.php"><?= htmlspecialchars(t('admin')) ?></a> |
        <a href="learn.php"><?= htmlspecialchars(t('learn')) ?></a>
    </div>

    <h1><?= htmlspecialchars(t('admin_users_title') ?? 'Benutzerverwaltung') ?></h1>

    <h2><?= htmlspecialchars(t('admin_users_create_local') ?? 'Lokalen Benutzer anlegen') ?></h2>
    <form method="post">
        <input type="hidden" name="mode" value="create_user">

        <label><?= htmlspecialchars(t('username')) ?>
            <input type="text" name="username" required>
        </label>
        <label><?= htmlspecialchars(t('display_name') ?? 'Anzeigename') ?>
            <input type="text" name="display_name">
        </label>
        <label><?= htmlspecialchars(t('password')) ?>
            <input type="password" name="password" required>
        </label>
        <label>
            <input type="checkbox" name="is_admin" value="1">
            <?= htmlspecialchars(t('admin_flag') ?? 'Admin') ?>
        </label>

        <button type="submit"><?= htmlspecialchars(t('admin_users_create_button') ?? 'Benutzer anlegen') ?></button>
    </form>

    <h2><?= htmlspecialchars(t('admin_users_existing') ?? 'Bestehende Benutzer') ?></h2>
    <table>
        <tr>
            <th><?= htmlspecialchars(t('username')) ?></th>
            <th><?= htmlspecialchars(t('display_name') ?? 'Anzeigename') ?></th>
            <th><?= htmlspecialchars(t('admin_flag') ?? 'Admin') ?></th>
        </tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['display_name'] ?? '') ?></td>
                <td> <?= $u['is_admin']
                  ? htmlspecialchars(t('yes') ?? 'Ja')
                  : htmlspecialchars(t('no') ?? 'Nein') ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
