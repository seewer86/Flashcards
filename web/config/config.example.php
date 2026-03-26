<?php
// config/config.example.php
return [
    'db' => [
        'host' => 'db',
        'name' => 'flashcards',
        'user' => 'flashcards_user',
        'pass' => 'secret',
        'charset' => 'utf8mb4',
    ],
    'auth' => [
        // 'ldap' oder 'local'
        'mode' => 'local',
    ],
    'ldap' => [
        'host'      => 'ldap://localhost',
        'basedn'    => 'ou=users,dc=example,dc=com',
        'group_dn'  => 'cn=elearning,ou=group,dc=example,dc=com',
        'bind_dn'   => 'cn=admin,dc=example,dc=com',
        'bind_pass' => 'secret',
    ],
    'app' => [
        'default_locale' => 'de',
    ],
];
