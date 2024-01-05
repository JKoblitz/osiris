
<?php
// apt-get install php-ldap

if (!defined(LDAP_IP)) {
    if (file_exists('CONFIG.php')) {
        require_once 'CONFIG.php';
    } else {
        require_once 'CONFIG.default.php';
    }
}

if (!isset($Settings)) {
    require_once BASEPATH . '/php/Settings.php';
    global $Settings;
    $Settings = new Settings();
}

function login($username, $password)
{
    global $Settings;
    $return = array("msg" => '', "success" => false);

    if (!defined('LDAP_IP')) {
        die("LDAP Settings are missing. Please enter details in CONFIG.php");
    } else {
        $ip = LDAP_IP;
        $ldap_port = LDAP_PORT;
        $dn = $username . LDAP_DOMAIN;
        $base_dn = LDAP_BASEDN;
    }

    $ldap_address = "ldap://" . $ip;



    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $person = "$username";
            $fields = "(|(samaccountname=$person))";

            $search = ldap_search($connect, $base_dn, $fields);
            $result = ldap_get_entries($connect, $search);

            $ldap_username = $result[0]['samaccountname'][0];
            $ldap_first_name = $result[0]['givenname'][0];
            $ldap_last_name = $result[0]['sn'][0];

            $_SESSION['username'] = $ldap_username;
            $_SESSION['name'] = $ldap_first_name . " " . $ldap_last_name;
            $_SESSION['loggedin'] = true;

            $return["status"] = true;

            ldap_close($connect);
        } else {
            // Login fehlgeschlagen / Benutzer nicht vorhanden
            $return["msg"] = "Login failed or user not found.";
        }
    } else {
        $return["msg"] = "Connection to LDAP server failed.";
    }

    return $return;
}

function getUser($name)
{
    if (!defined('LDAP_IP')) {
        die("LDAP Settings are missing. Please enter details in CONFIG.php");
    } else {
        $ip = LDAP_IP;
        $ldap_port = LDAP_PORT;
        $dn = LDAP_USER . LDAP_DOMAIN;
        $base_dn = LDAP_BASEDN;
        $password = LDAP_PASSWORD;
    }

    $ldap_address = "ldap://" . $ip;

    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $res = array();

            $fields = "(|(samaccountname=*$name*))";

            $search = ldap_search($connect, $base_dn, $fields);
            $result = ldap_get_entries($connect, $search);
            // dump(ldap_get_dn($connect, ldap_first_entry($connect, $search)));
            // dump(ldap_first_entry($connect, $search))['uid'];
            return $result;
            $ldap_username = $result[0]['samaccountname'][0];
            $ldap_last_name = $result[0]['cn'][0];


            foreach ($result as $entry) {
                $res[$entry['samaccountname'][0]] = $entry['cn'][0];
            }
            ldap_close($connect);
            return $res;
        } else {
            // Login fehlgeschlagen / Benutzer nicht vorhanden
            return "Login fehlgeschlagen / Benutzer nicht vorhanden";
        }
    } else {
        return "Verbindung zum Server fehlgeschlagen.";
    }
}


function getUsers()
{
    if (!defined('LDAP_IP')) {
        die("LDAP Settings are missing. Please enter details in CONFIG.php");
    } else {
        $ip = LDAP_IP;
        $ldap_port = LDAP_PORT;
        $dn = LDAP_USER . LDAP_DOMAIN;
        $base_dn = LDAP_BASEDN;
        $password = LDAP_PASSWORD;
    }

    $ldap_address = "ldap://" . $ip;

    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $res = array();

            $fields = "(|(samaccountname=*))";

            $search = ldap_search($connect, $base_dn, $fields);
            $result = ldap_get_entries($connect, $search);
            // return $result;
            // $ldap_username = $result[0]['samaccountname'][0];
            // $ldap_last_name = $result[0]['cn'][0];

            foreach ($result as $entry) {
                if (!isset($entry['samaccountname'][0])) continue;
                // dump($entry['dn']);
                $active = !str_contains($entry['dn'], 'DeaktivierteUser');
                $res[$entry['samaccountname'][0]] = $active;
            }
            ldap_close($connect);
            return $res;
        } else {
            // Login fehlgeschlagen / Benutzer nicht vorhanden
            return "Login fehlgeschlagen / Benutzer nicht vorhanden";
        }
    } else {
        return "Verbindung zum Server fehlgeschlagen.";
    }
}

function getGroups($v)
{
    $m = array();
    if (preg_match('/CN=([^,]+)[,$]/', $v, $matches))
        return $matches[1];
    return false;
}



function newUser($username)
{
    $Settings = new Settings();
    // get user from ldap
    $ldap_users = getUser($username);
    if (empty($ldap_users) || $ldap_users['count'] == 0) return false;
    $ldap_user = $ldap_users[0];

    $person = array();
    $keys = [
        "username" => "samaccountname",
        "first" => "givenname",
        "last" => "sn",
        "displayname" => "displayname",
        "formalname" => "cn",
        "department" => "department",
        "unit" => "description",
        "telephone" => "telephonenumber",
        "mail" => "mail"
    ];
    foreach ($keys as $key => $name) {
        // dump($value, true);
        $person[$key] = $ldap_user[$name][0] ?? null;
    }
    $person['academic_title'] = null;
    $person['orcid'] = null;
    $person['dept'] = null;

    $departments = [];
    foreach ($Settings->get('departments') as $D) {
        $departments[strtolower($D['id'])] = $D['id'];
        $departments[strtolower($D['name'])] = $D['id'];
    }
    $unit = strtolower($person['unit']);
    if (array_key_exists($unit, $departments))
        $person['dept'] = $departments[$unit];
    else {
        $unit = trim(explode('/', $unit)[0]);
        if (array_key_exists($unit, $departments))
            $person['dept'] = $departments[$unit];
    }

    $person['is_active'] = !str_contains($ldap_user['dn'], 'DeaktivierteUser');
    $person['created'] = date('Y-m-d');
    $person['roles'] = [];

    return $person;
}
