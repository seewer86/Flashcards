<?php
// src/auth.php
session_start();

$config = require __DIR__ . '/../config/config.php';
require __DIR__ . '/db.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        header('Location: login.php');
        exit;
    }
}

// Lokaler Login (users-Tabelle, password_hash)
function login_local(string $username, string $password): ?array {
    global $mysqli;

    $stmt = $mysqli->prepare(
        "SELECT id, username, display_name, password_hash, is_admin
         FROM users
         WHERE username = ?"
    );
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res  = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user) {
        return null;
    }
    if (!password_verify($password, $user['password_hash'] ?? '')) {
        return null;
    }

    return [
        'id'           => (int)$user['id'],
        'username'     => $user['username'],
        'display_name' => $user['display_name'],
        'is_admin'     => (int)$user['is_admin'],
    ];
}

// LDAP-Login: Auth über CLI-Skript, danach User in DB nachführen
function login_ldap(string $username, string $password): ?array {
    global $mysqli;

    // CLI-Skript aufrufen, das den LDAP-Bind macht
    $cmd = sprintf(
        'php %s %s %s',
        escapeshellarg(__DIR__ . '/ldap_cli_auth.php'),
        escapeshellarg($username),
        escapeshellarg($password)
    );
    $out = trim(shell_exec($cmd));
    if ($out === '' || $out === 'ERR') {
        return null;        // LDAP-Login fehlgeschlagen
    }

    // Aus dem CLI-Skript kommt der DN zurück (eine Zeile)
    $userDn = $out;

    // Anzeigename erst mal einfach auf username setzen
    $displayName = $username;

    // In lokaler users-Tabelle nachführen (ohne Passwort)
    $stmt = $mysqli->prepare(
        "SELECT id, username, display_name, is_admin
         FROM users
         WHERE username = ?"
    );
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res  = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();

    if (!$user) {
        $stmt = $mysqli->prepare(
            "INSERT INTO users (username, display_name, password_hash, is_admin)
             VALUES (?, ?, NULL, 0)"
        );
        $stmt->bind_param('ss', $username, $displayName);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        $user = [
            'id'           => $id,
            'username'     => $username,
            'display_name' => $displayName,
            'is_admin'     => 0,
        ];
    } else {
        // Anzeigename ggf. aktualisieren
        if ($user['display_name'] !== $displayName) {
            $stmt = $mysqli->prepare("UPDATE users SET display_name = ? WHERE id = ?");
            $stmt->bind_param('si', $displayName, $user['id']);
            $stmt->execute();
            $stmt->close();
            $user['display_name'] = $displayName;
        }
    }

    return [
        'id'           => (int)$user['id'],
        'username'     => $user['username'],
        'display_name' => $user['display_name'],
        'is_admin'     => (int)$user['is_admin'],
    ];
}

// Wrapper: wählt je nach Config die passende Methode
function login_user(string $username, string $password): ?array {
    global $config;

    $mode = $config['auth']['mode'] ?? 'local';

    if ($mode === 'ldap') {
        $user = login_ldap($username, $password);
        if ($user) {
            $_SESSION['auth_mode'] = 'ldap';
        }
        return $user;
    } else {
        $user = login_local($username, $password);
        if ($user) {
            $_SESSION['auth_mode'] = 'local';
        }
        return $user;
    }
}
