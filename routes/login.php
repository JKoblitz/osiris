<?php
    
/**
 * Routing file for login and -out
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

Route::get('/user/login', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang('User login', 'Login')]
    ];
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true  && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/profile/$_SESSION[username]");
        die;
    }
    include BASEPATH . "/header.php";

    if (isset($_GET['redirect'])) {
        echo (lang("You need to be logged in to see this page.", "Du musst eingeloggt sein, um diese Seite zu sehen."));
    }
    include BASEPATH . "/pages/userlogin.php";
    include BASEPATH . "/footer.php";
});


Route::post('/user/login', function () {
    include_once BASEPATH . "/php/init.php";
    $page = "userlogin";
    $msg = "?msg=welcome";
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/profile/$_SESSION[username]");
        die;
    }

    if (defined('USER_MANAGEMENT') && strtoupper(USER_MANAGEMENT) == 'AUTH') {
        require_once 'addons/auth/_login.php';
    } else {
        include BASEPATH . "/php/_login.php";
    }

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_SERVER['SERVER_NAME'] == 'testserver' && false) {
            // on the test server: log in
            // check if user exists in our database
            $_SESSION['username'] = $_POST['username'];
            $useracc = $DB->getPerson($_SESSION['username']);
            $_SESSION['name'] = $useracc['displayname'];

            $_SESSION['loggedin'] = true;

            if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                header("Location: " . $_POST['redirect'] . $msg);
                die();
            }
            header("Location: " . ROOTPATH . "/" . $msg);
            die();
        } else {
            $auth = login($_POST['username'], $_POST['password']);
            if (isset($auth["status"]) && $auth["status"] == true) {

                // check if user exists in our database
                $USER = $DB->getPerson($_SESSION['username']);
                if (empty($USER)) {
                    // create user from LDAP
                    $new_user = newUser($_SESSION['username']);
                    if (empty($new_user)) {
                        die('Sorry, the user does not exist. Please contact system administrator!');
                    }
                    $osiris->persons->insertOne($new_user);

                    $user = $new_user['account']['username'];

                    $USER = $DB->getPerson($user);

                    // try to connect the user with existing authors
                    $updateResult = $osiris->activities->updateMany(
                        [
                            'authors.last' => $USER['last'],
                            'authors.first' => new MongoDB\BSON\Regex('^' . $USER['first'][0] . '.*')
                        ],
                        ['$set' => ["authors.$.user" => ($user)]]
                    );
                    $n = $updateResult->getModifiedCount();
                    $msg .= "&new=$n";
                }

                $_SESSION['username'] = $USER['username'];
                $_SESSION['name'] = $USER['displayname'];

                $updateResult = $osiris->persons->updateOne(
                    ['username' => $_POST['username']],
                    ['$set' => ["lastlogin" => date('d.m.Y')]]
                );

                if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                    header("Location: " . $_POST['redirect'] . $msg);
                    die();
                }
                header("Location: " . ROOTPATH . "/" . $msg);
                die();
            }
        }
    }
    $breadcrumb = [
        ['name' => lang('User Login', 'Login')]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/userlogin.php";
    if (isset($auth)) {
        printMsg($auth["msg"], "error", "");
    }
    if (empty($_POST['username'])) {
        printMsg("Username is required!", "error", "");
    }
    if (empty($_POST['password'])) {
        printMsg("Password is required!", "error", "");
    }
    include BASEPATH . "/footer.php";
});


Route::get('/user/logout', function () {
    unset($_SESSION["username"]);
    $_SESSION['loggedin'] = false;
    header("Location: " . ROOTPATH . "/");
}, 'login');


?>
