
<?php
// apt-get install php-ldap

function login($username, $password, $readall = false)
{
    $return = array("msg" => '', "success" => false);

    $ldap_address = "ldap://172.18.240.3";
    $ldap_port = 389;

    if ($readall === true) {
        $base_dn = 'CN=bacmedia,OU=ServiceAccounts,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn
        $password = 'TheH4mmer4ndThunder#?!'; // entsprechendes password
        $dn = "bacmedia@dsmz.local";
    } else {
        $dn = "$username@dsmz.local";
    }
    $base_dn = 'OU=Users,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn

    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $person = "$username";
            $fields = "(|(samaccountname=$person))";

            $search = ldap_search($connect, $base_dn, $fields);
            $result = ldap_get_entries($connect, $search);

            $ldap_username = $result[0]['samaccountname'][0];
            $ldap_first_name = $result[0]['givenname'][0];
            $ldap_last_name = $result[0]['sn'][0];
            // $ldap_status = $result[0]['useraccountcontrol'][0];

            if ($readall === true) {
                print_r($result[0]["memberof"]);
            }

            // // Prüfen ob Konto gesperrt ist
            // if ($ldap_status == 512) {
            // Nicht gesperrt
            $_SESSION['username'] = $ldap_username;
            $_SESSION['name'] = $ldap_first_name . " " . $ldap_last_name;
            $_SESSION['loggedin'] = true;

            $return["status"] = true;
            // } else {
            //     // Gesperrt
            //     $_SESSION['loggedin'] = false;
            //     $return["status"] = false;
            //     $return["msg"] = "User is blocked.";
            // }

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
    $ldap_address = "ldap://172.18.240.3";
    $ldap_port = 389;

    $base_dn = 'CN=bacmedia,OU=ServiceAccounts,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn
    $password = 'TheH4mmer4ndThunder#?!'; // entsprechendes password
    $dn = "bacmedia@dsmz.local";

    $base_dn = 'OU=Users,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn

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

            $fields = "(|(cn=*$name*))";

            $search = ldap_search($connect, $base_dn, $fields);
            $result = ldap_get_entries($connect, $search);

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

function getGroups($v)
{
    $m = array();
    if (preg_match ('/CN=([^,]+)[,$]/', $v, $matches))
      return $matches[1];
    return false;
}


function getUserName($name)
{
    $ldap_address = "ldap://172.18.240.3";
    $ldap_port = 389;

    $base_dn = 'CN=bacmedia,OU=ServiceAccounts,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn
    $password = 'TheH4mmer4ndThunder#?!'; // entsprechendes password
    $dn = "bacmedia@dsmz.local";

    $base_dn = 'OU=Users,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn

    if ($connect = ldap_connect($ldap_address . ":" . $ldap_port)) {
        // Verbindung erfolgreich
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

        // Authentifizierung des Benutzers
        // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
        $bind = ldap_bind($connect, $dn, $password);

        if ($bind) {
            $result = array();

            $fields = "(|(samaccountname=$name))";

            $search = ldap_search($connect, $base_dn, $fields);
            $user = ldap_get_entries($connect, $search);
            if (empty($user)|| empty($user[0]['samaccountname'])) {
                return 'User not found';
            }

            $user = $user[0];

            $result['user'] = $user['samaccountname'][0];
            $result['firstname'] = $user['givenname'][0];
            $result['lastname'] = $user['sn'][0];
            $groups = array_map('getGroups', $user['memberof']);
            $result['groups'] = $groups;
            $result['group'] = relevantGroup($groups);
            var_dump($result);
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

function relevantGroup($groups){
    $groups = array_reverse($groups);
    $relevant = array('Verwaltung', 'Mutz', 'Patent', 'IT', 'Mios');
    $irrelevant = array('AG-Loga', 'AG-Phagen-Phagomed-P2G', 'AG-Phagen-4Cure', 'AG-Phagen-Phagomed');
    foreach ($groups as $group ) {
        if (in_array($group, $irrelevant)) continue;
        if (substr( $group, 0, 3 ) === "AG-" || in_array($group, $relevant)) return $group;
    }
    return 'unknown';
}
// function getKuratoren(){
//     $ldap_address = "ldap://172.18.240.3";
//     $ldap_port = 389;

//     $base_dn = 'CN=bacmedia,OU=ServiceAccounts,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn
//     $password = 'TheH4mmer4ndThunder#?!'; // entsprechendes password
//     $dn = "bacmedia@dsmz.local";
   
//     $base_dn = 'OU=Users,OU=DSMZ,DC=dsmz,DC=local'; // ldap rdn oder dn

//     if ($connect = ldap_connect($ldap_address . ":". $ldap_port)) {
//         // Verbindung erfolgreich
//         ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
//         ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
//         // define(LDAP_OPT_DIAGNOSTIC_MESSAGE, 0x0032);

//         // Authentifizierung des Benutzers
//         // if ($bind = ldap_bind( $connect, $ldaprdn, $ldappass)) { // service account
//         $bind=ldap_bind($connect, $dn, $password);

//         if ($bind) {
//             $res = array();
//             // $fields = "(&(objectClass=Group)(cn=Kuratoren))";
//             $fields = "(&(objectClass=person)(objectClass=user))";

//             $search = ldap_search($connect, $base_dn, $fields);
//             $result = ldap_get_entries($connect, $search);
//             echo "yo";
            
//             // $ldap_username = $result[0]['samaccountname'][0];
//             foreach ($result as $entry) {
//                 array_push($res, array($entry['samaccountname'][0], $entry['memberof']));
//             }
//             ldap_close($connect);
//             return $res;
//         } else {
//             // Login fehlgeschlagen / Benutzer nicht vorhanden
//             return "Login fehlgeschlagen / Benutzer nicht vorhanden";
//         }
//     } else {
//        return "Verbindung zum Server fehlgeschlagen.";
//     }
// }
