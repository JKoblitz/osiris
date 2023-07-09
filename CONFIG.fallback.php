<?php

// define relative path to your root folder 
if (!defined('ROOTPATH'))
    define('ROOTPATH', '');

// define ADMIN user name
if (!defined('ADMIN'))
    define('ADMIN', 'juk20');

// if you do not use LDAP, change the following to 'AUTH'
if (!defined('USER_MANAGEMENT'))
    define('USER_MANAGEMENT', 'LDAP');
// define LDAP connection
if (USER_MANAGEMENT == 'LDAP' && !defined('LDAP_IP')) {
    define("LDAP_IP", "100.10.100.0");
    define("LDAP_PORT", 389);
    define("LDAP_USER", "osiris");
    define("LDAP_DOMAIN", "@domain.local");
    define("LDAP_PASSWORD", "ldap_password");
    define("LDAP_BASEDN", "OU=Users,OU=DSMZ,DC=dsmz,DC=local");
}
// define DB connection
if (!defined('DB_NAME'))
    define("DB_NAME", "osiris");
if (!defined('DB_STRING'))
    define("DB_STRING", "mongodb://localhost:27017/" . DB_NAME . "?retryWrites=true&w=majority");

// define API keys
if (!defined('WOS_STARTER_KEY'))
    define("WOS_STARTER_KEY", "wos starter key");

if (!defined('WOS_JOURNAL_INFO'))
    define("WOS_JOURNAL_INFO", 2021);

// not needed right now, but planned in the future
if (!defined('ORCID_APP_ID'))
    define("ORCID_APP_ID", null);
if (!defined('ORCID_SECRET_KEY'))
    define("ORCID_SECRET_KEY", null);

// activate IDA integration here
if (!defined('IDA_INTEGRATION'))
    define("IDA_INTEGRATION", false);
