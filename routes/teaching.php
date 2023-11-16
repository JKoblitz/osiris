<?php

/**
 * Routing file for teaching
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

Route::get('/teaching', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Teaching", "Lehrveranstaltungen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/teaching.php";
    include BASEPATH . "/footer.php";
}, 'login');
