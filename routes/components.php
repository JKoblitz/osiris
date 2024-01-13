<?php

/**
 * Routing file for components and modules
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

Route::get('/get-modules', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";

    $form = array();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $mongoid = $DB->to_ObjectID($_GET['id']);
        $form = $osiris->activities->findOne(['_id' => $mongoid]);
    }

    $Modules = new Modules($form, $_GET['copy'] ?? false);
    if (isset($_GET['modules'])) {
        $Modules->print_modules($_GET['modules']);
    } else {
        $Modules->print_all_modules();
    }
});


Route::get('/components/([A-Za-z0-9\-]*)', function ($path) {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/components/$path.php";
});
