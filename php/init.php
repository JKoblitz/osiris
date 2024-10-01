<?php

require_once BASEPATH . '/php/Settings.php';
include_once BASEPATH . "/php/_config.php";
include_once BASEPATH . "/php/DB.php";

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
if (!empty($Groups->tree)){
$Departments = array_column($Groups->tree['children'], 'name', 'id');
} else $Departments = [];
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