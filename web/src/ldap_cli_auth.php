<?php
require __DIR__ . '/ldap_config.php';

$username = $argv[1] ?? '';
$password = $argv[2] ?? '';

$ad = ldap_connect($LDAP_HOST);
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

if (!@ldap_bind($ad, $LDAP_BIND_DN, $LDAP_BIND_PASSWORD)) {
    exit("ERR\n");
}

$filter = '(uid=' . ldap_escape($username, '', LDAP_ESCAPE_FILTER) . ')';
$result = ldap_search($ad, $LDAP_BASEDN, $filter, ['dn', 'cn']);
$entries = ldap_get_entries($ad, $result);
if ($entries['count'] <= 0) {
    exit("ERR\n");
}
$userEntry = $entries[0];
$userDn    = $userEntry['dn'];

if (!@ldap_bind($ad, $userDn, $password)) {
    exit("ERR\n");
}

echo $userDn, "\n";
