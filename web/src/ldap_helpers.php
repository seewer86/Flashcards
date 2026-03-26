<?php
// src/ldap_helpers.php
require_once __DIR__ . '/ldap_config.php';

function ldap_connect_bind($username, $password, &$userEntry = null) {
    global $LDAP_HOST, $LDAP_BASEDN, $LDAP_BIND_DN, $LDAP_BIND_PASSWORD;

    $ad = ldap_connect($LDAP_HOST);    // EIN Argument, kompletter URI aus config
    if (!$ad) {
        error_log('ldap_connect failed for ' . $LDAP_HOST);
        return false;
    }
    ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

    // 1) Mit Service-Account binden
    if (!@ldap_bind($ad, $LDAP_BIND_DN, $LDAP_BIND_PASSWORD)) {
        error_log('LDAP service bind failed: ' . ldap_error($ad));
        ldap_unbind($ad);
        return false;
    }

    if (empty($LDAP_BASEDN)) {
        error_log('LDAP_BASEDN is empty before search');
        ldap_unbind($ad);
        return false;
    }

    // 2) User-DN suchen (als Admin)
    $filter = '(uid=' . ldap_escape($username, '', LDAP_ESCAPE_FILTER) . ')';
    $result = ldap_search($ad, $LDAP_BASEDN, $filter, ['dn', 'cn', 'uid']);
    if (!$result) {
        error_log('ldap_search failed: ' . ldap_error($ad));
        ldap_unbind($ad);
        return false;
    }

    $entries = ldap_get_entries($ad, $result);
    if ($entries['count'] <= 0) {
        ldap_unbind($ad);
        return false;
    }

    $userEntry = $entries[0];
    $userDn    = $userEntry['dn'];

    // 3) Mit User-DN + Passwort binden (Passwort prüfen)
    if (!@ldap_bind($ad, $userDn, $password)) {
        error_log('LDAP user bind failed: ' . ldap_error($ad));
        ldap_unbind($ad);
        $userEntry = null;
        return false;
    }

    // Erfolgreich als User gebunden
    return $ad;
}

function ldap_user_in_group($adUnused, $userDn) {
    global $LDAP_HOST, $LDAP_GROUP_DN, $LDAP_BIND_DN, $LDAP_BIND_PASSWORD;

    // Neuer eigener LDAP-Handle als Admin
    $ad = ldap_connect($LDAP_HOST);   // wieder: ein Argument, URI
    if (!$ad) {
        error_log('ldap_connect (group) failed for ' . $LDAP_HOST);
        return false;
    }
    ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);

    if (!@ldap_bind($ad, $LDAP_BIND_DN, $LDAP_BIND_PASSWORD)) {
        error_log('LDAP group bind failed: ' . ldap_error($ad));
        ldap_unbind($ad);
        return false;
    }

    $base   = $LDAP_GROUP_DN;
    $filter = '(objectClass=groupOfUniqueNames)';
    $attrs  = ['uniqueMember'];

    $result = @ldap_read($ad, $base, $filter, $attrs);
    if ($result === false) {
        error_log('ldap_read (group) failed: ' . ldap_error($ad));
        ldap_unbind($ad);
        return false;
    }

    $entries = ldap_get_entries($ad, $result);
    if ($entries['count'] <= 0) {
        ldap_unbind($ad);
        return false;
    }

    $group   = $entries[0];
    $inGroup = false;

    if (isset($group['uniquemember'])) {
        for ($i = 0; $i < $group['uniquemember']['count']; $i++) {
            if (strcasecmp($group['uniquemember'][$i], $userDn) === 0) {
                $inGroup = true;
                break;
            }
        }
    }

    ldap_unbind($ad);
    return $inGroup;
}
