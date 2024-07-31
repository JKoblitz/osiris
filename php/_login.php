
<?php
// apt-get install php-ldap

if (!defined(LDAP_IP)) {
    if (file_exists('CONFIG.php')) {
        require_once 'CONFIG.php';
    } else {
        require_once 'CONFIG.default.php';
    }
}

require_once BASEPATH . '/php/Groups.php';
// if (!isset($Groups)) {
//     global $Groups;
//     $Groups = new Groups();
// }

function login($username, $password)
{
    $return = array("msg" => '', "success" => false);

    if (!defined('LDAP_IP')) {
        die("LDAP Settings are missing. Please enter details in CONFIG.php");
    } else {
        $ip = LDAP_IP;
        $ldap_port = LDAP_PORT;
        $dn = $username . LDAP_DOMAIN;
        $base_dn = LDAP_BASEDN;
    }
    if ($ldap_port == 636) $ldap_address = "ldaps://" . $ip;
    else $ldap_address = "ldap://" . $ip;



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
            if ($search === false) {
                $return['msg'] = "Login failed or user not found.";
            } else {
                $result = ldap_get_entries($connect, $search);

                $ldap_username = $result[0]['samaccountname'][0];
                $ldap_first_name = $result[0]['givenname'][0];
                $ldap_last_name = $result[0]['sn'][0];

                $_SESSION['username'] = $ldap_username;
                $_SESSION['name'] = $ldap_first_name . " " . $ldap_last_name;
                $_SESSION['loggedin'] = true;

                $return["status"] = true;

                ldap_close($connect);
            }
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
    if ($ldap_port == 636) $ldap_address = "ldaps://" . $ip;
    else $ldap_address = "ldap://" . $ip;

    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $fields = "(|(samaccountname=$name))";

            $search = ldap_search($connect, $base_dn, $fields);
            if ($search === false) {
                return "Login fehlgeschlagen / Benutzer nicht vorhanden";
            }
            $result = ldap_get_entries($connect, $search);
            // dump(ldap_get_dn($connect, ldap_first_entry($connect, $search)));
            // dump(ldap_first_entry($connect, $search))['uid'];

            ldap_close($connect);
            return $result;
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
    if (!defined('LDAP_IP') || !defined('LDAP_PORT') || !defined('LDAP_USER') || !defined('LDAP_DOMAIN') || !defined('LDAP_BASEDN') || !defined('LDAP_PASSWORD')) {
        die("LDAP Einstellungen fehlen. Bitte Details in CONFIG.php eingeben.");
    }

    $ip = LDAP_IP;
    $ldap_port = LDAP_PORT;
    $dn = LDAP_USER . LDAP_DOMAIN;
    $base_dn = LDAP_BASEDN;
    $password = LDAP_PASSWORD;

    $ldap_address = ($ldap_port == 636) ? "ldaps://" . $ip : "ldap://" . $ip;

    $connect = ldap_connect($ldap_address . ":" . $ldap_port);
    if (!$connect) {
        return "Verbindung zum Server fehlgeschlagen.";
    }

    ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);

    $bind = ldap_bind($connect, $dn, $password);
    if (!$bind) {
        ldap_close($connect);
        return "Login fehlgeschlagen / Benutzer nicht vorhanden.";
    }

    $res = array();
    $cookie = '';

    do {
        $filter = '(cn=*)';
        // overwrite filter if set in CONFIG
        if (defined('LDAP_FILTER') && !empty(LDAP_FILTER)) $filter = LDAP_FILTER;
        $attributes = ['samaccountname', 'useraccountcontrol'];

        $result = @ldap_search(
            $connect, $base_dn, $filter, $attributes, 0, 0, 0, LDAP_DEREF_NEVER,
            [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 1000, 'cookie' => $cookie]]]
        );

        if ($result === false) {
            $error = ldap_error($connect);
            ldap_close($connect);
            return "Fehler bei der LDAP-Suche: " . $error;
        }

        $parseResult = ldap_parse_result($connect, $result, $errcode, $matcheddn, $errmsg, $referrals, $controls);
        if ($parseResult === false) {
            $error = ldap_error($connect);
            ldap_close($connect);
            return "Fehler beim Parsen des LDAP-Ergebnisses: " . $error;
        }

        $entries = ldap_get_entries($connect, $result);
        if ($entries === false) {
            $error = ldap_error($connect);
            ldap_close($connect);
            return "Fehler beim Abrufen der LDAP-Einträge: " . $error;
        }

        foreach ($entries as $entry) {
            if (!isset($entry['samaccountname'][0])) continue;

            $accountControl = isset($entry['useraccountcontrol'][0]) ? (int)$entry['useraccountcontrol'][0] : 0;
            $active = !($accountControl & 2); // 2 = ACCOUNTDISABLE

            $res[$entry['samaccountname'][0]] = $active;
        }

        if (isset($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'])) {
            $cookie = $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'];
        } else {
            $cookie = '';
        }
    } while (!empty($cookie));

    ldap_close($connect);
    return $res;
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
    $Groups = new Groups();
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
    foreach ($Groups->groups as $D) {
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

    // $person['is_active'] = !str_contains($ldap_user['dn'], 'DeaktivierteUser');
    $accountControl = isset($result[0]['useraccountcontrol'][0]) ? (int)$result[0]['useraccountcontrol'][0] : 0;
    $person['is_active'] = !($accountControl & 2); // 2 entspricht ACCOUNTDISABLE

    $person['created'] = date('Y-m-d');
    $person['roles'] = [];

    return $person;
}
