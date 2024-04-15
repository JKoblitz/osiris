<?php

/**
 * Core routing file
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

if (file_exists('CONFIG.php')) {
    require_once 'CONFIG.php';
    require_once 'CONFIG.fallback.php';
} else {
    require_once 'CONFIG.default.php';
}


// error_reporting(E_ERROR);

session_start();

define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);
define('OSIRIS_VERSION', '1.3.3');

// set time constants
$year = date("Y");
$month = date("n");
$quarter = ceil($month / 3);
define('CURRENTQUARTER', intval($quarter));
define('CURRENTMONTH', intval($month));
define('CURRENTYEAR', intval($year));

if (isset($_GET['OSIRIS-SELECT-MAINTENANCE-USER'])) {
    // someone tries to switch users
    include_once BASEPATH . "/php/init.php";
    $realusername = $_SESSION['realuser'] ?? $_SESSION['username'];
    $username = $_GET['OSIRIS-SELECT-MAINTENANCE-USER'];

    // check if the user is allowed to do that
    $allowed = $osiris->persons->count(['username' => $username, 'maintenance' => $realusername]);
    // change username if user is allowed
    if ($allowed == 1 || $realusername == $username) {
        $msg = "User switched!";
        $_SESSION['realuser'] = $realusername;
        $_SESSION['username'] = $username;
        header("Location: " . ROOTPATH . "/profile/$username");
    }

    // do nothing if user is not allowed
}

function lang($en, $de = null)
{
    if ($de === null) return $en;
    // Standard language = DE
    $lang = $_GET['lang'] ?? $_COOKIE['osiris-language'] ?? 'de';
    if ($lang == "en") return $en;
    if ($lang == "de") return $de;
    return $en;
}

include_once BASEPATH . "/php/Route.php";

Route::get('/', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        include BASEPATH . "/header.php";
        include BASEPATH . "/pages/userlogin.php";
        include BASEPATH . "/footer.php";
    } else {
        $path = ROOTPATH . "/profile/" . $_SESSION['username'];
        if (!empty($_SERVER['QUERY_STRING'])) $path .= "?" . $_SERVER['QUERY_STRING'];
        header("Location: $path");
    }
});


if (defined('USER_MANAGEMENT') && strtoupper(USER_MANAGEMENT) == 'AUTH') {
    require_once BASEPATH.'/addons/auth/index.php';
}


include_once BASEPATH . "/routes/components.php";
include_once BASEPATH . "/routes/controlling.php";
include_once BASEPATH . "/routes/database.php";
include_once BASEPATH . "/routes/docs.php";
include_once BASEPATH . "/routes/groups.php";
include_once BASEPATH . "/routes/import.php";
include_once BASEPATH . "/routes/journals.php";
include_once BASEPATH . "/routes/login.php";
include_once BASEPATH . "/routes/migrate.php";
include_once BASEPATH . "/routes/projects.php";
include_once BASEPATH . "/routes/queue.php";
include_once BASEPATH . "/routes/tags.php";
include_once BASEPATH . "/routes/static.php";
include_once BASEPATH . "/routes/teaching.php";
include_once BASEPATH . "/routes/users.php";
include_once BASEPATH . "/routes/visualize.php";
include_once BASEPATH . "/routes/activities.php";
include_once BASEPATH . "/routes/export.php";
include_once BASEPATH . "/routes/concepts.php";
include_once BASEPATH . "/routes/admin.php";
// include_once BASEPATH . "/routes/adminGeneral.php";
// include_once BASEPATH . "/routes/adminRoles.php";


include_once BASEPATH . "/routes/api.php";
include_once BASEPATH . "/routes/rest.php";
// include_once BASEPATH . "/routes/CRUD.php";

// if (IDA_INTEGRATION) {
    include_once BASEPATH . "/addons/ida/index.php";
// }


// if ($Settings->featureEnabled('guests')) {
    require_once BASEPATH.'/addons/guestforms/index.php';
// }

/**
 * Routes for OSIRIS Portal
 */

include_once BASEPATH . "/addons/portal/index.php";

Route::get('/error/([0-9]*)', function ($error) {
    // header("HTTP/1.0 $error");
    http_response_code($error);
    include BASEPATH . "/header.php";
    echo "Error " . $error;
    // include BASEPATH . "/pages/error.php";
    include BASEPATH . "/footer.php";
});

// Add a 404 not found route
Route::pathNotFound(function ($path) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    // header('HTTP/1.0 404 Not Found');
    http_response_code(404);
    $error = 404;
    // header('HTTP/1.0 404 Not Found');
    include BASEPATH . "/header.php";
    // $browser = $_SERVER['HTTP_USER_AGENT'];
    // var_dump($browser);
    include BASEPATH . "/pages/error.php";
    // echo "Error 404";
    include BASEPATH . "/footer.php";
});

// Add a 405 method not allowed route
Route::methodNotAllowed(function ($path, $method) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    header('HTTP/1.0 405 Method Not Allowed');
    $error = 405;
    include BASEPATH . "/header.php";
    // include BASEPATH . "/pages/error.php";
    echo "Error 405";
    include BASEPATH . "/footer.php";
});


Route::run(ROOTPATH);
