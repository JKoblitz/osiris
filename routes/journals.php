<?php
    
/**
 * Routing file for journals
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


Route::get('/journal', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang('Table', 'Tabelle')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journals-table.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/journal/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => $data['journal']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-view.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/add', function () {
    include_once BASEPATH . "/php/init.php";
    $id = null;
    $data = [];
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang("Add", "Hinzufügen")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => $data['journal'], 'path' => "/journal/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');
