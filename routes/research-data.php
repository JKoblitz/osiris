<?php
    
/**
 * Routing file for research data
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

Route::get('/research-data', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Tags", "Schlagwörter")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/research-data.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/research-data/(.*)', function ($name) {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Tags", "Schlagwörter"), 'path' => "/research-data"],
        ['name' => $name]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/research-data-detail.php";
    include BASEPATH . "/footer.php";
}, 'login');
