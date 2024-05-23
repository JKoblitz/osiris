<?php

/**
 * Routing file for projects and collaborations
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
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


/**
 * CRUD routes
 */

 Route::post('/crud/projects/create', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->projects;

    $values = validateValues($_POST['values'], $DB);

    // check if project name already exists:
    $project_exist = $collection->findOne(['name' => $values['name']]);
    if (!empty($project_exist)) {
        header("Location: " . $red . "?msg=project ID does already exist.");
        die();
    }

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['end-delay'] = endOfCurrentQuarter(true);
    $values['created_by'] = $_SESSION['username'];

    // add false checkbox values
    $values['public'] = boolval($values['public'] ?? false);

    // add persons
    if (!empty($values['contact'])) {
        $values['persons'] = [
            [
                'user' => $values['contact'],
                'role' => 'PI',
                'name' => $DB->getNameFromId($values['contact'])
            ]
        ];
    }
    if (isset($values['funding_number'])) {
        $values['funding_number'] = explode(',', $values['funding_number']);
        $values['funding_number'] = array_map('trim', $values['funding_number']);

        // check if there are already activities with this funding number
        $test = $osiris->activities->updateMany(
            ['funding' => ['$in' => $values['funding_number']]],
            ['$push' => ['projects' => $values['name']]]
        );
        // dump($test->getModifiedCount());
    }

    $insertOneResult  = $collection->insertOne($values);
    $id = $insertOneResult->getInsertedId();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=success");
        die();
    }

    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
    ]);
});


Route::post('/crud/projects/update/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->projects;

    $values = validateValues($_POST['values'], $DB);
    // add information on creating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = $_SESSION['username'];

    $values['public'] = boolval($values['public'] ?? false);

    // check if module already exists:
    // $project_exist = $collection->findOne(['name' => $values['name']]);

    if (isset($values['funding_number'])) {
        $values['funding_number'] = explode(',', $values['funding_number']);
        $values['funding_number'] = array_map('trim', $values['funding_number']);
    }

    $id = $DB->to_ObjectID($id);
    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }

    echo json_encode([
        'inserted' => $updateResult->getModifiedCount(),
        'id' => $id,
    ]);
});


Route::post('/crud/projects/update-persons/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $values = $_POST['persons'];
    foreach ($values as $i => $p) {
        $values[$i]['name'] =  $DB->getNameFromId($p['user']);
    }

    $osiris->projects->updateOne(
        ['_id' => $DB::to_ObjectID($id)],
        ['$set' => ["persons" => $values]]
    );

    header("Location: " . ROOTPATH . "/projects/view/$id?msg=update-success");
});

Route::post('/crud/projects/update-collaborators/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $values = $_POST['values'];

    $collaborators = [];
    foreach ($values as $key => $values) {
        foreach ($values as $i => $val) {
            $collaborators[$i][$key] = $val;
        }
    }

    $osiris->projects->updateOne(
        ['_id' => $DB::to_ObjectID($id)],
        ['$set' => ["collaborators" => $collaborators]]
    );

    header("Location: " . ROOTPATH . "/projects/view/$id?msg=update-success");
});



Route::post('/crud/projects/connect-activities', function () {
    include_once BASEPATH . "/php/init.php";

    if (!isset($_POST['project']) || empty($_POST['project'])) {
        header("Location: " . $_POST['redirect'] . "?error=no-project-given");
        die;
    }
    if (!isset($_POST['activity']) || empty($_POST['activity'])) {
        header("Location: " . $_POST['redirect'] . "?error=no-activity-given");
        die;
    }

    $project = $_POST['project'];
    $activity = $_POST['activity'];

    if (isset($_POST['delete'])){
        $osiris->activities->updateOne(
            ['_id' => $DB::to_ObjectID($activity)],
            ['$pull' => ["projects" => $project]]
        );
        header("Location: " . $_POST['redirect'] . "?msg=disconnected-activity-from-project#add-activity");
        die;
    }

    $osiris->activities->updateOne(
        ['_id' => $DB::to_ObjectID($activity)],
        ['$push' => ["projects" => $project]]
    );
    
    header("Location: " . $_POST['redirect'] . "?msg=connected-activity-to-project#add-activity");
    die;
    
    header("Location: " . ROOTPATH . "/activities/view/$id?msg=update-success");
});