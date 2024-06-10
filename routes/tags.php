<?php
    
/**
 * Routing file for research data
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

Route::get('/tags', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Tags", "Schlagwörter")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/tags.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/tags/(.*)', function ($name) {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Tags", "Schlagwörter"), 'path' => "/tags"],
        ['name' => $name]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/tags-detail.php";
    include BASEPATH . "/footer.php";
}, 'login');
