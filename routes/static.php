<?php

/**
 * Routing file for all static contents
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


Route::get('/impress', function () {
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/impressum.html";
    include BASEPATH . "/footer.php";
});

Route::get('/new-stuff', function () {

    $breadcrumb = [
        ['name' => lang('News', 'Neuigkeiten')]
    ];

    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/MyParsedown.php";

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/news.php";
    include BASEPATH . "/footer.php";
});
Route::get('/about', function () {
    $breadcrumb = [
        ['name' => lang('About OSIRIS', 'Über OSIRIS')]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/about.php";
    include BASEPATH . "/footer.php";
});


Route::get('/license', function () {

    $breadcrumb = [
        ['name' => lang('License', 'Lizenz')]
    ];

    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/license.html";
    include BASEPATH . "/footer.php";
});
