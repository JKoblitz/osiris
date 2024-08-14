<?php

/**
 * Routing file for projects and collaborations
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

Route::get('/projects', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Projects", "Projekte")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/projects/projects.php";
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
    include BASEPATH . "/pages/projects/edit.php";
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
    include BASEPATH . "/pages/projects/project.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/projects/(edit|collaborators|finance|public)/([a-zA-Z0-9]*)', function ($page, $id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $mongo_id = $DB->to_ObjectID($id);
    $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    if (empty($project)) {
        header("Location: " . ROOTPATH . "/projects?msg=not-found");
        die;
    }

    switch ($page) {
        case 'collaborators':
            $name = lang("Collaborators", "Kooperationspartner");
            break;
        case 'finance':
            $name = lang("Finance", "Finanzen");
            break;
        case 'public':
            $name = lang("Public representation", "Öffentliche Darstellung");
            break;
        default:
            $name = lang("Edit", "Bearbeiten");
    }

    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' =>  $project['name'], 'path' => "/projects/view/$id"],
        ['name' => $name]
    ];

    global $form;
    $form = DB::doc2Arr($project);

    include BASEPATH . "/header.php";
    switch ($page) {
        case 'collaborators':
            include BASEPATH . "/pages/projects/collaborators.php";
            break;
        case 'finance':
            include BASEPATH . "/pages/projects/finance.php";
            break;
        case 'public':
            include BASEPATH . "/pages/projects/public.php";
            break;
        default:
            include BASEPATH . "/pages/projects/edit.php";
    }
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/projects/subproject/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    // get project
    if (DB::is_ObjectID($id)) {
        $mongo_id = $DB->to_ObjectID($id);
        $project = $osiris->projects->findOne(['_id' => $mongo_id]);
    } else {
        $project = $osiris->projects->findOne(['name' => $id]);
        $id = strval($project['_id'] ?? '');
    }
    // check if project exists
    if (empty($project)) {
        header("Location: " . ROOTPATH . "/projects?msg=not-found");
        die;
    }

    // set breadcrumb
    $breadcrumb = [
        ['name' => lang('Projects', 'Projekte'), 'path' => "/projects"],
        ['name' => $project['name'], 'path' => "/projects/view/$id"],
        ['name' => lang("Add subproject", "Teilprojekt hinzufügen")]
    ];

    // create new form
    global $form;
    $form = DB::doc2Arr($project);
    // user abbreviation (first letter of first and last name)
    try {
        // in case of unicode errors or sth like that
        $suffix = $USER['first'][0] . $USER['last'][0];
    } catch (\Throwable $th) {
        $suffix = 'XX';
    }

    // add suffix to project name
    $form['name'] = $form['name'] . "-" . $suffix;
    // check if name is unique
    $project_exist = $osiris->projects->findOne(['name' => $form['name']]);
    if (!empty($project_exist)) {
        $form['name'] = $form['name'] . "-" . uniqid();
    }
    // delete stuff that should not be inherited
    unset($form['title']);
    unset($form['ressources']);
    unset($form['personnel']);
    unset($form['in-kind']);
    unset($form['_id']);

    // add parent project
    $form['parent'] = $project['name'];
    $form['parent_id'] = strval($project['_id']);

    // set type to subproject
    $type = 'Teilprojekt';

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/projects/edit.php";
    include BASEPATH . "/footer.php";
}, 'login');

/**
 * CRUD routes
 */

Route::post('/crud/projects/create', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Project.php";
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
    $persons = [];
    foreach (['contact', 'scholar', 'supervisor'] as $key) {
        if (!isset($values[$key]) || empty($values[$key])) continue;
        $persons[] = [
            'user' => $values[$key],
            'role' => ($key == 'contact' ? 'applicant' : $key),
            'name' => $DB->getNameFromId($values[$key])
        ];
    }
    if (!empty($persons)) {
        $values['persons'] = $persons;
    }

    if (isset($values['funding_number'])) {
        $values['funding_number'] = explode(',', $values['funding_number']);
        $values['funding_number'] = array_map('trim', $values['funding_number']);

        // check if there are already activities with this funding number
        $osiris->activities->updateMany(
            ['funding' => ['$in' => $values['funding_number']]],
            ['$push' => ['projects' => $values['name']]]
        );
    }

    // check if type is Teilprojekt
    if (isset($values['parent_id'])) {
        // get parent project
        $parent = $osiris->projects->findOne(['_id' => $DB->to_ObjectID($values['parent_id'])]);

        // take over parent projects parameters
        if (!empty($parent)) {
            $values['type'] = 'Teilprojekt';
            $values['parent'] = $parent['name'];
            foreach (Project::INHERITANCE as $key) {
                if (isset($parent[$key])) {
                    $values[$key] = $parent[$key];
                }
            }
            // add project to parent project
            $osiris->projects->updateOne(
                ['_id' => $DB->to_ObjectID($values['parent_id'])],
                ['$push' => ['subprojects' => $values['name']]]
            );
        }
    }

    // dump($values, true);
    // die;

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


    // $user_project = in_array($user, array_column(DB::doc2Arr($project['persons']), 'user'));
    // $edit_perm = ($Settings->hasPermission('projects.edit') || ($Settings->hasPermission('projects.edit-own') && $user_project));

    // if (!$edit_perm) {
    //     header("Location: " . ROOTPATH . "/projects/view/$id?msg=no-permission");
    //     die;
    // }

    $values = validateValues($_POST['values'], $DB);
    // add information on creating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = $_SESSION['username'];

    $values['public'] = boolval($values['public'] ?? false);

    if (isset($values['persons']) && !empty($values['persons'])) {
        $values['persons'] = array_values($values['persons']);
    }

    if (isset($values['funding_number'])) {
        $values['funding_number'] = explode(',', $values['funding_number']);
        $values['funding_number'] = array_map('trim', $values['funding_number']);
    }

    // update all children
    if ($osiris->projects->count(['parent_id' => $id]) > 0) {
        include_once BASEPATH . "/php/Project.php";
        $sub = [];
        foreach ($values as $key => $value) {
            if (in_array($key, Project::INHERITANCE)) {
                $sub[$key] = $value;
            }
        }
        $collection->updateMany(
            ['parent_id' => $id],
            ['$set' => $sub]
        );
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


Route::post('/crud/projects/delete/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $project = $osiris->projects->findOne(['_id' => $DB->to_ObjectID($id)]);

    // check if user has permission to delete project
    $edit_perm = (
        $Settings->hasPermission('projects.delete')
        ||
        ($Settings->hasPermission('projects.delete-own') &&
            (
                $project['created_by'] == $_SESSION['username']
                ||
                in_array($_SESSION['username'], array_column(DB::doc2Arr($project['persons']), 'user'))
            ))
    );

    // if user has no permission: redirect to project view
    if (!$edit_perm) {
        header("Location: " . ROOTPATH . "/projects/view/$id?msg=no-permission");
        die;
    }

    // remove project name from activities
    $osiris->activities->updateMany(
        ['projects' => $project['name']],
        ['$pull' => ['projects' => $project['name']]]
    );

    // remove project
    $osiris->projects->deleteOne(
        ['_id' => $DB::to_ObjectID($id)]
    );

    $_SESSION['msg'] = lang("Project has been deleted successfully.", "Projekt wurde erfolgreich gelöscht.");
    header("Location: " . ROOTPATH . "/projects");
});


Route::post('/crud/projects/update-persons/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $values = $_POST['persons'];
    foreach ($values as $i => $p) {
        $values[$i]['name'] =  $DB->getNameFromId($p['user']);
    }
    // avoid object transformation
    $values = array_values($values);

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


Route::post('/crud/projects/update-public/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $values = $_POST['values'];

    $values['public'] = boolval($values['public'] ?? false);

    $target_dir = BASEPATH . "/uploads/";
    if (!is_writable($target_dir)) {
        die("Upload directory $target_dir is unwritable. Please contact admin.");
    }
    $target_dir .= "projects/";
    if (isset($_FILES["file"]) && $_FILES["file"]["size"] > 0) {

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777);
        }
        // random filename
        $filename = $id . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        // $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["file"]["size"];
        $values['public_image'] = "projects/" . $filename;

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            $errorMsg = match ($_FILES['file']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            printMsg($errorMsg, "error");
        } else if ($filesize > 16000000) {
            printMsg(lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."), "error");
        } else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
            printMsg(lang("The file $filename has been uploaded.", "Die Datei <q>$filename</q> wurde hochgeladen."), "success");
        } else {
            $_SESSION['msg'] = lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload.");
        }
    } else if (isset($_POST['delete'])) {
        $filename = $_POST['delete'];
        if (file_exists($target_dir . $filename)) {
            // Use unlink() function to delete a file
            if (!unlink($target_dir . $filename)) {
                $_SESSION['msg'] = lang("$filename cannot be deleted due to an error.", "$filename kann nicht gelöscht werden, da ein Fehler aufgetreten ist.");
            } else {
                $_SESSION['msg'] = lang("$filename has been deleted.", "$filename wurde gelöscht.");
            }
        }

        $osiris->projects->updateOne(
            ['_id' => $DB::to_ObjectID($id)],
            ['$set' => ["public_image" => null]]
        );
        // printMsg("File has been deleted from the database.", "success");

        header("Location: " . ROOTPATH . "/projects/view/$id?msg=update-success");
        die();
    }

    $osiris->projects->updateOne(
        ['_id' => $DB::to_ObjectID($id)],
        ['$set' => $values]
    );

    header("Location: " . ROOTPATH . "/projects/view/$id?msg=update-success");
    die;
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

    if (isset($_POST['delete'])) {
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
