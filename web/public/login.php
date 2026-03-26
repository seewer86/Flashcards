<?php
// public/login.php
require __DIR__ . '/../src/auth.php';
require __DIR__ . '/../src/i18n.php';

if (current_user()) {
    header('Location: learn.php');
    exit;
}
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLocale) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(t('login_title')) ?></title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f5f5f7;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card {
            background: #fff;
            padding: 32px 40px;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            max-width: 420px;
            width: 100%;
        }
        h1 {
            margin-top: 0;
            margin-bottom: 24px;
            font-size: 26px;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 14px;
        }
        .field {
            margin-bottom: 12px;
        }
        button {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            background: #e5e7eb;
            font-size: 15px;
            cursor: pointer;
        }
        button:hover {
            background: #d1d5db;
        }
        .lang-switch {
            font-size: 12px;
            margin-bottom: 12px;
            text-align: right;
        }
        .lang-switch a {
            color: #2563eb;
            text-decoration: none;
            margin-left: 6px;
        }
        .lang-switch a.active {
            font-weight: 600;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="lang-switch">
        <?= htmlspecialchars(t('language')) ?>:
        <?php foreach (['de' => 'DE', 'en' => 'EN', 'fr' => 'FR'] as $loc => $label): ?>
            <a href="?lang=<?= $loc ?>" class="<?= $currentLocale === $loc ? 'active' : '' ?>"><?= $label ?></a>
        <?php endforeach; ?>
    </div>

    <h1><?= htmlspecialchars(t('login_title')) ?></h1>

    <form method="post" action="login_check.php">
        <div class="field">
            <label><?= htmlspecialchars(t('username')) ?></label>
            <input type="text" name="username" required autofocus>
        </div>
        <div class="field">
            <label><?= htmlspecialchars(t('password')) ?></label>
            <input type="password" name="password" required>
        </div>
        <button type="submit"><?= htmlspecialchars(t('login')) ?></button>
    </form>
</div>
</body>
</html>
