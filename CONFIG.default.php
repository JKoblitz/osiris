<?php

// define relative path to your root folder 
define('ROOTPATH', '');

// define ADMIN user name
define('ADMIN', 'juk20');

// define LDAP connection
define("LDAP_IP", "100.10.100.0");
define("LDAP_PORT", 389);
define("LDAP_USER", "osiris");
define("LDAP_DOMAIN", "@domain.local");
define("LDAP_PASSWORD", "ldap_password");
define("LDAP_BASEDN", "OU=Users,OU=DSMZ,DC=dsmz,DC=local");

// define DB connection
define("DB_IP", "localhost");
define("DB_PORT", 27017);
define("DB_DBNAME", "osiris");
define("DB_PASSWORD", "example_pw");
define("DB_USER", "example_user");

// define API keys
define("WOS_STARTER_KEY", "wos starter key");

define("WOS_JOURNAL_INFO", 2021);

define("ORCID_APP_ID", null);
define("ORCID_SECRET_KEY", null);
