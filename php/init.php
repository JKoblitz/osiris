<?php

// implement newer functions in case they don't exist
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle)
    {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}

// Language settings and cookies
if (!empty($_GET['language'])) {
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

require_once BASEPATH . '/php/Settings.php';
include_once BASEPATH . "/php/_config.php";
include_once BASEPATH . "/php/DB.php";

global $DB;
$DB = new DB;

global $osiris;
$osiris = $DB->db;

include_once BASEPATH . "/php/Groups.php";
global $Groups;
$Groups = new Groups();
global $Departments;
$Departments = array_column($Groups->tree['children'], 'name', 'id');

global $USER;
$USER = $DB->initUser();


global $Settings;
$Settings = new Settings($USER);
?>