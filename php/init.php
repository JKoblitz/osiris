<?php

require_once BASEPATH . '/php/Settings.php';
include_once BASEPATH . "/php/_config.php";
include_once BASEPATH . "/php/DB.php";

// Language settings and cookies
if ($_SERVER['REQUEST_METHOD'] === 'GET' && array_key_exists('language', $_GET)) {
    $_COOKIE['osiris-language'] = $_GET['language'] === 'en' ? 'en' : 'de';
    $domain = ($_SERVER['HTTP_HOST'] != 'testserver') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('osiris-language', $_COOKIE['osiris-language'], [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ]);
}
// check if accessibility settings are given
if ($_SERVER['REQUEST_METHOD'] === 'GET' && array_key_exists('accessibility', $_GET)) {
    // define base parameter
    $domain = $_SERVER['HTTP_HOST'];
    $cookie_settings = [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ];

    // set cookies for current sessions
    $_COOKIE['D3-accessibility-contrast'] = $_GET['accessibility']['contrast'] ?? '';
    $_COOKIE['D3-accessibility-transitions'] = $_GET['accessibility']['transitions'] ?? '';
    $_COOKIE['D3-accessibility-dyslexia'] = $_GET['accessibility']['dyslexia'] ?? '';

    // save cookies for persistent use
    setcookie('D3-accessibility-dyslexia', $_COOKIE['D3-accessibility-dyslexia'], $cookie_settings);
    setcookie('D3-accessibility-contrast', $_COOKIE['D3-accessibility-contrast'], $cookie_settings);
    setcookie('D3-accessibility-transitions', $_COOKIE['D3-accessibility-transitions'], $cookie_settings);
}

// Database connection
global $DB;
$DB = new DB;

global $osiris;
$osiris = $DB->db;


// get installed OSIRIS version
$version = $osiris->system->findOne(['key' => 'version']);
if (empty($version)){ 
    die ('OSIRIS has not been installed yet. <a href="'.ROOTPATH.'/install">Click here to install it</a>.');
}
define('OSIRIS_DB_VERSION', $version['value']);


// Get organizational units (Groups)
include_once BASEPATH . "/php/Groups.php";
global $Groups;
$Groups = new Groups();
global $Departments;
$Departments = array_column($Groups->tree['children'], 'name', 'id');

// Activity categories and types
include_once BASEPATH . "/php/Categories.php";
global $Categories;
$Categories = new Categories();

// initialize user
global $USER;
$USER = $DB->initUser();

// Get all Settings
global $Settings;
$Settings = new Settings($USER);

?>