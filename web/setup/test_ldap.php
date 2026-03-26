<?php
// setup/test_ldap.php
header('Content-Type: application/json; charset=utf-8');

$availableLocales = ['de', 'en', 'fr'];
$requested = $_GET['lang'] ?? 'de';
if (!in_array($requested, $availableLocales, true)) {
    $requested = 'de';
}
$currentLocale = $requested;

$t = [
    'de' => [
        'ldap_test_fill'        => 'Bitte Host, Base DN, Bind DN und Passwort ausfüllen.',
        'ldap_test_connect_fail'=> 'Verbindung (ldap_connect) fehlgeschlagen.',
        'ldap_test_bind_fail'   => 'Bind fehlgeschlagen: ',
        'ldap_test_search_fail' => 'Suche fehlgeschlagen: ',
        'ldap_test_ok'          => 'Verbindung ok, Benutzer gefunden: ',
    ],
    'en' => [
        'ldap_test_fill'        => 'Please fill host, base DN, bind DN and password.',
        'ldap_test_connect_fail'=> 'Connection (ldap_connect) failed.',
        'ldap_test_bind_fail'   => 'Bind failed: ',
        'ldap_test_search_fail' => 'Search failed: ',
        'ldap_test_ok'          => 'Connection ok, users found: ',
    ],
    'fr' => [
        'ldap_test_fill'        => 'Veuillez renseigner l’hôte, le Base DN, le Bind DN et le mot de passe.',
        'ldap_test_connect_fail'=> 'Connexion (ldap_connect) échouée.',
        'ldap_test_bind_fail'   => 'Échec du bind : ',
        'ldap_test_search_fail' => 'Échec de la recherche : ',
        'ldap_test_ok'          => 'Connexion ok, utilisateurs trouvés : ',
    ],
];

$tr = $t[$currentLocale] ?? $t['de'];

$scheme   = ($_POST['ldap_scheme'] ?? 'ldap') === 'ldaps' ? 'ldaps' : 'ldap';
$host     = trim($_POST['ldap_host'] ?? '');
$basedn   = trim($_POST['ldap_basedn'] ?? '');
$bindDn   = trim($_POST['ldap_bind_dn'] ?? '');
$bindPass = trim($_POST['ldap_bind_pass'] ?? '');

if ($host === '' || $basedn === '' || $bindDn === '' || $bindPass === '') {
    echo json_encode(['ok' => false, 'message' => $tr['ldap_test_fill']]);
    exit;
}

$uri = $scheme . '://' . $host;

$ad = @ldap_connect($uri);
if (!$ad) {
    echo json_encode(['ok' => false, 'message' => $tr['ldap_test_connect_fail']]);
    exit;
}
ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

if (!@ldap_bind($ad, $bindDn, $bindPass)) {
    $err = ldap_error($ad);
    ldap_unbind($ad);
    echo json_encode(['ok' => false, 'message' => $tr['ldap_test_bind_fail'] . $err]);
    exit;
}

$filter  = '(uid=*)';
$result  = @ldap_search($ad, $basedn, $filter, ['uid']);
if ($result === false) {
    $err = ldap_error($ad);
    ldap_unbind($ad);
    echo json_encode(['ok' => false, 'message' => $tr['ldap_test_search_fail'] . $err]);
    exit;
}

$entries = ldap_get_entries($ad, $result);
$count   = $entries['count'] ?? 0;

ldap_unbind($ad);

echo json_encode([
    'ok'      => true,
    'message' => $tr['ldap_test_ok'] . $count,
]);
