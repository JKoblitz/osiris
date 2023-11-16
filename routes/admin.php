<?php

/**
 * Routing file for admin settings
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

Route::post('/reset-settings', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin-panel')) die('You have no permission to be here.');


    $filename = BASEPATH . '/settings.default.json';
    $msg = 'settings-resetted';
    if (isset($_FILES["settings"])) {

        if ($_FILES['settings']['error'] != UPLOAD_ERR_OK) {
            $msg = match ($_FILES['settings']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            // printMsg($errorMsg, "error");
        } else {
            $filename = $_FILES["settings"]["tmp_name"];
            $msg = 'settings-replaced';
        }
    }
    $json = file_get_contents($filename);
    // $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents(BASEPATH . "/settings.json", $json);
    header("Location: " . ROOTPATH . "/admin/general?msg=$msg");
}, 'login');


Route::get('/admin/(activities|departments|general|roles|features)', function ($page) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin-panel')) die('You have no permission to be here.');
    $breadcrumb = [
        ['name' => lang("Admin Panel $page")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/admin-$page.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/admin/(activities|departments|general|roles|features)', function ($page) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin-panel')) die('You have no permission to be here.');


    $json = $Settings->settings;

    foreach (['activities', 'departments', 'affiliation', 'general', 'roles', 'features'] as $key) {
        if (isset($_POST[$key])) {
            $values = $_POST[$key];

            if ($key == 'roles') {
                foreach ($values['rights'] as $k => $v) {
                    $values['rights'][$k] = array_map('boolval', $v);
                }
            }
            // dump($values, true);
            // die;
            $json[$key] = $values;
        }
    }
    $msg = 'settings-saved';


    if (isset($_FILES["logo"])) {
        $filename = htmlspecialchars(basename($_FILES["logo"]["name"]));
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["logo"]["size"];
        $filepath = BASEPATH . "/img/logo-custom." . $filetype;

        // $filepath = ROOTPATH . "/uploads/$id/$filename";

        if ($_FILES['logo']['error'] != UPLOAD_ERR_OK) {
            $msg = match ($_FILES['logo']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            // printMsg($errorMsg, "error");
            $json['affiliation']['logo'] = $Settings->get('affiliation_details')['logo'];
        } else if ($filesize > 2000000) {
            $msg = lang("File is too big: max 2 MB is allowed.", "Die Datei ist zu groß: maximal 2 MB sind erlaubt.");
            $json['affiliation']['logo'] = $Settings->get('affiliation_details')['logo'];
        }
        if (file_exists($filepath)) {
            chmod($filepath, 0755); //Change the file permissions if allowed
            unlink($filepath); //remove the file
        }
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filepath)) {
            $json['affiliation']['logo'] = "logo-custom." . $filetype;
        } else {
            $msg = lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload.");
            $json['affiliation']['logo'] = $Settings->get('affiliation_details')['logo'];
        }
    }
    $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents(BASEPATH . "/settings.json", $json);
    header("Location: " . ROOTPATH . "/admin/$page?msg=settings-saved");
}, 'login');


Route::get('/coins', function () {

    $breadcrumb = [
        ['name' => lang('LOM', 'LOM')]
    ];

    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/lom.php";
    include BASEPATH . "/footer.php";
});

Route::post('/coins', function () {
    $json = json_encode($_POST['json'], JSON_PRETTY_PRINT);
    file_put_contents(BASEPATH . "/matrix.json", $json);
    header("Location: " . ROOTPATH . "/coins?msg=success");
});
