<?php
// src/ldap_config.php
$config = require __DIR__ . '/../config/config.php';

$LDAP_HOST      = $config['ldap']['host'];
$LDAP_BASEDN    = $config['ldap']['basedn'];
$LDAP_GROUP_DN  = $config['ldap']['group_dn'];

$LDAP_BIND_DN       = $config['ldap']['bind_dn'];
$LDAP_BIND_PASSWORD = $config['ldap']['bind_pass'];

error_log('LDAP_HOST=' . $LDAP_HOST);
error_log('LDAP_BASEDN=' . $LDAP_BASEDN);
error_log('LDAP_GROUP_DN=' . $LDAP_GROUP_DN);
error_log('LDAP_BIND_DN=' . $LDAP_BIND_DN);
