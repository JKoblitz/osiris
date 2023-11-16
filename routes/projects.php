<?php

/**
 * Routing file for projects and collaborations
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

Route::get('/projects', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Projects", "Projekte")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/projects.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/projects/new', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' => lang("New", "Neu")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-project.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/projects/view/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    if (DB::is_ObjectID($id)) {
        $mongo_id = $DB->to_ObjectID($id);
        $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    } else {
        $project = $osiris->projects->findOne(['name' => $id]);
        $id = strval($project['_id'] ?? '');
    }
    if (empty($project)) {
        header("Location: " . ROOTPATH . "/projects?msg=not-found");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' => $project['name']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/project.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/projects/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $mongo_id = $DB->to_ObjectID($id);
    $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    if (empty($project)) {
        header("Location: " . ROOTPATH . "/projects?msg=not-found");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' =>  $project['name'], 'path' => "/projects/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    global $form;
    $form = DB::doc2Arr($project);

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-project.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/projects/collaborators/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $mongo_id = $DB->to_ObjectID($id);
    $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    if (empty($project)) {
        header("Location: " . ROOTPATH . "/projects?msg=not-found");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' =>  $project['name'], 'path' => "/projects/view/$id"],
        ['name' => lang("Collaborators", "Kooperationspartner")]
    ];
    global $form;
    $form = DB::doc2Arr($project);

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/project-collaborators.php";
    include BASEPATH . "/footer.php";
}, 'login');