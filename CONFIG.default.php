<?php

// define relative path to your root folder 
define('ROOTPATH', '');

// define ADMIN user name
define('ADMIN', 'juk20');

// if you do not use LDAP, change the following to 'AUTH'
define('USER_MANAGEMENT', 'LDAP');
// define LDAP connection
define("LDAP_IP", "100.10.100.0");
define("LDAP_PORT", 389);
define("LDAP_USER", "osiris");
define("LDAP_DOMAIN", "@domain.local");
define("LDAP_PASSWORD", "ldap_password");
define("LDAP_BASEDN", "OU=Users,OU=DSMZ,DC=dsmz,DC=local");

// define DB connection
define("DB_NAME", "osiris");
define("DB_STRING", "mongodb://localhost:27017/" . DB_NAME . "?retryWrites=true&w=majority");

// define API keys
define("WOS_STARTER_KEY", "wos starter key");

define("WOS_JOURNAL_INFO", 2021);

// not needed right now, but planned in the future
define("ORCID_APP_ID", null);
define("ORCID_SECRET_KEY", null);

// activate IDA integration here
define("IDA_INTEGRATION", false);

// Guest forms
define('GUEST_FORMS', false);
define("GUEST_SERVER", 'guests-osiris.dsmz.de');
define("GUEST_FORM_SECRET_KEY", "THIS-is-A-secret");
