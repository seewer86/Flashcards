<?php
$appName = 'Karteikarten';

$ldapScheme = ($_POST['ldap_scheme'] ?? 'ldap') === 'ldaps' ? 'ldaps' : 'ldap';
$ldapHostIn = trim($_POST['ldap_host'] ?? '');
$ldapUrl    = $ldapHostIn !== '' ? $ldapScheme . '://' . $ldapHostIn : '';

$config = [
    'app' => [
        'name' => $appName,
        'default_locale' => trim($_POST['default_locale'] ?? 'de'),
    ],
    'auth' => [
        'mode' => ($_POST['auth_mode'] ?? 'local') === 'ldap' ? 'ldap' : 'local',
    ],
    'ldap' => [
        'host'        => $ldapUrl,
        'basedn'      => trim($_POST['ldap_basedn'] ?? ''),
        'group_dn'    => trim($_POST['ldap_group_dn'] ?? ''),
        'bind_dn'     => trim($_POST['ldap_bind_dn'] ?? ''),
        'bind_pass'   => trim($_POST['ldap_bind_pass'] ?? ''),
    ],
    'db' => [
        'host'    => trim($_POST['db_host'] ?? ''),
        'name'    => trim($_POST['db_name'] ?? ''),
        'user'    => trim($_POST['db_user'] ?? ''),
        'pass'    => trim($_POST['db_pass'] ?? ''),
        'charset' => 'utf8mb4',
    ],
];

$file = "<?php\nreturn " . var_export($config, true) . ";\n";
file_put_contents(__DIR__ . '/../config/config.php', $file);

// ab hier DB verbinden und ggf. initialen Admin anlegen
require __DIR__ . '/../src/db.php';

// Grundtabellen anlegen (falls noch nicht vorhanden)
$schemaSql = "
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(191) NOT NULL UNIQUE,
    display_name VARCHAR(191) DEFAULT NULL,
    password_hash VARCHAR(255) DEFAULT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    name VARCHAR(191) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cards (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_progress (
   id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   user_id INT UNSIGNED NOT NULL,
   card_id INT UNSIGNED NOT NULL,
   correct_count TINYINT(1) NOT NULL DEFAULT 0,
   last_seen_at TIMESTAMP NULL DEFAULT NULL,
   last_result TINYINT(1) DEFAULT NULL,
   reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
   FOREIGN KEY (card_id) REFERENCES cards(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$mysqli->multi_query($schemaSql);
while ($mysqli->more_results() && $mysqli->next_result()) { /* Resultsets leeren */ }

// Initial-Admin nur im Local-Modus
if ($config['auth']['mode'] === 'local') {
    $username = trim($_POST['admin_username'] ?? '');
    $password = $_POST['admin_password'] ?? '';

    if ($username !== '' && $password !== '') {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$exists) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $displayName = $username;
            $isAdmin = 1;

            $stmt = $mysqli->prepare(
                "INSERT INTO users (username, display_name, password_hash, is_admin)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param('sssi', $username, $displayName, $hash, $isAdmin);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header('Location: ../public/login.php');
exit;
