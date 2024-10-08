<?php

/**
 * Routing for Concepts
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


Route::get('/concepts', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Concepts", "Konzepte")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/concepts.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/concepts/(.*)', function ($name) {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Concepts", "Konzepte"), 'path' => "/concepts"],
        ['name' => $name]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/concept.php";
    include BASEPATH . "/footer.php";
}, 'login');
