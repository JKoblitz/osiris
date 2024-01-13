<?php
/**
 * Routing file for activities
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

 
Route::get('/(activities|my-activities)', function ($page) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $path = $page;
    if ($page == 'activities') {
        $breadcrumb = [
            ['name' => lang("All activities", "Alle Aktivitäten")]
        ];
    } elseif (isset($_GET['user'])) {
        $user = $_GET['user'];
        $breadcrumb = [
            ['name' => lang("Activities of $user", "Aktivitäten von $user")]
        ];
    } else {
        $breadcrumb = [
            ['name' => lang("My activities", "Meine Aktivitäten")]
        ];
    }

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/all-activities.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/search/activities', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Search", "Suche")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/activity-search.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/add-activity', function () {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Add new", "Neu hinzufügen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/crud/activities/add-activity', function () {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    global $form;
    $form = $_POST['form'];
    // dump($form);
    $form = unserialize($form);
    $copy = true;

    $name = $form['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($form['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("New from Import", "Neu aus Import")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/pubmed-search', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Search in Pubmed", "Suche in Pubmed")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/search.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/(doi|pubmed)/(.*)', function ($type, $identifier) {
    include_once BASEPATH . "/php/init.php";

    $form = $osiris->activities->findOne([$type => $identifier]);
    if (!empty($form)) {
        $id = strval($form['_id']);
        header("Location: " . ROOTPATH . "/activities/view/$id");
    }
    echo "$type $identifier not found.";
});
Route::get('/activities/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Render.php";

    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);
    $activity = $osiris->activities->findOne(['_id' => $id], ['projection' => ['file' => 0]]);

    if (!empty($activity)) {

        $doc = json_decode(json_encode($activity->getArrayCopy()), true);
        $locked = $activity['locked'] ?? false;
        if ($doc['type'] == 'publication' && isset($doc['journal'])) {
            // fix old journal_ids
            if (isset($doc['journal_id']) && !preg_match("/^[0-9a-fA-F]{24}$/", $doc['journal_id'])) {
                $doc['journal_id'] = null;
                $osiris->activities->updateOne(
                    ['_id' => $activity['_id']],
                    ['$unset' => ['journal_id' => '']]
                );
            }
        }
        renderActivities(['_id' =>  $activity['_id']]);
        $user_activity = $DB->isUserActivity($doc, $user);

        $Format = new Document;
        $Format->setDocument($doc);

        $name = $activity['title'] ?? $id;
        if (strlen($name) > 20)
            $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
        $name = ucfirst($activity['type']) . ": " . $name;
        $breadcrumb = [
            ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
            ['name' => $name]
        ];
        if ($Format->hasSchema()) {
            $additionalHead = $Format->schema();
        }
    }
    include BASEPATH . "/header.php";

    if (empty($activity)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/activity.php";
    }
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/view/([a-zA-Z0-9]*)/file', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $activity = $osiris->activities->findOne(['_id' => $id]);

    if (empty($activity)) {
        echo "Activity not found!";
    } else if (!isset($activity['file']) || empty($activity['file'])) {
        echo "No file found.";
    } else {
        header('Content-type: application/pdf');
        // header('Content-Disposition: attachment; filename="my.pdf"');
        echo $activity['file']->serialize();
    }
}, 'login');


Route::get('/activities/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $mongoid = $DB->to_ObjectID($id);

    global $form;
    $form = $osiris->activities->findOne(['_id' => $mongoid]);
    $copy = false;
    if (($form['locked'] ?? false) && !$Settings->hasPermission('edit-locked')) {
        header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    }


    $name = $form['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($form['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => $name, 'path' => "/activities/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/activities/doublet/([a-zA-Z0-9]*)/([a-zA-Z0-9]*)', function ($id1, $id2) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";

    $Format = new Document(false, 'list');
    $Modules = new Modules();

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Doublet", "Dublette")]
    ];

    $form = [];
    $html = [];

    // first
    $form1 = $DB->getActivity($id1);
    // if (($form1['locked'] ?? false) && !$USER['is_controlling']) {
    //     header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    // }

    // second
    $form2 = $DB->getActivity($id2);


    include BASEPATH . "/header.php";
    if ($form1['type'] != $form2['type']) {
        echo "Error: Activities must be of the same type.";
    } else {

        // $form = array_merge_recursive($form1, $form2);
        $keys = array_keys(array_merge($form1, $form2));
        $ignore = [
            'rendered', 'editor-comment',  'updated', 'updated_by',  'created', 'created_by', 'duplicate'
        ];

        $Format->setDocument($form1);
        foreach ($keys as $key) {
            if (in_array($key, $ignore)) continue;
            $form[$key] = [
                $form1[$key] ?? null,
                $form2[$key] ?? null
            ];

            $html[$key] = [
                $Format->get_field($key),
                null
            ];
        }
        $Format->setDocument($form2);
        foreach ($keys as $key) {
            if (in_array($key, $ignore)) continue;
            $html[$key][1] = $Format->get_field($key);
        }
    }

    // dump($form, true);

    include BASEPATH . "/pages/doublets.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/copy/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);

    global $form;
    $form = $osiris->activities->findOne(['_id' => $id]);
    $copy = true;

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Copy", "Kopieren")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/edit/([a-zA-Z0-9]*)/(authors|editors)', function ($id, $role) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);

    $form = $osiris->activities->findOne(['_id' => $id]);
    if (($form['locked'] ?? false) && !$Settings->hasPermission('edit-locked')) {
        header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    }

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Edit", "Bearbeiten"), 'path' => "/activities/edit/$id"]
    ];
    if ($role == "authors") {
        $breadcrumb[] = ['name' => lang("Authors", "Autoren")];
    } else {
        $breadcrumb[] = ['name' => lang("Editors", "Editoren")];
    }

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/author-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


/**
 * CRUD routes
 */

Route::post('/crud/activities/create', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Render.php";
    if (!isset($_POST['values'])) die("no values given");
    // dump($_POST);
    // die();
    $collection = $osiris->activities;
    $required = [];
    $col = $_POST['values']['type'];

    $values = validateValues($_POST['values'], $DB);

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = strtolower($_SESSION['username']);

    if (isset($values['doi']) && !empty($values['doi'])) {
        $doi_exist = $collection->findOne(['doi' => $values['doi']]);
        if (!empty($doi_exist)) {
            header("Location: " . ROOTPATH . "/activities/view/$doi_exist[_id]?msg=DOI+already+exists");
            die;
        }

        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['doi' => $values['doi']]);
    }
    if (isset($values['pubmed']) && !empty($values['pubmed'])) {
        $pubmed_exist = $collection->findOne(['pubmed' => $values['pubmed']]);
        if (!empty($pubmed_exist)) {
            header("Location: " . ROOTPATH . "/activities/view/$pubmed_exist[_id]?msg=Pubmed-ID+already+exists");
            die;
        }
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['pubmed' => $values['pubmed']]);
    }

    foreach ($required as $req) {
        if (!isset($values[$req]) || empty($values[$req])) {
            echo "$req is required";
            die;
        }
    }

    // add projects if possible
    if ($Settings->featureEnabled('projects')) {
        if (isset($values['funding'])) {
            $values['funding'] = explode(',', $values['funding']);
            foreach ($values['funding'] as $key) {
                $project = $osiris->projects->findOne(['funding_number' => $key]);
                if (isset($project['name'])) $values['projects'][] = $project['name'];
            }
        }
    }

    $insertOneResult  = $collection->insertOne($values);
    $id = $insertOneResult->getInsertedId();

    renderActivities(['_id' => $id]);

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=add-success");
        die();
    }
    // include_once BASEPATH . "/php/Document.php";
    // $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        // 'result' => format($col, $result)
    ]);
});


Route::post('/crud/activities/update/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Render.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->activities;
    $values = validateValues($_POST['values'], $DB);

    if (isset($_POST['minor']) && $_POST['minor'] == 1) {
        unset($values['authors']);
        unset($values['editors']);
    }

    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = strtolower($_SESSION['username']);

    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = $DB->to_ObjectID($id);
    }
    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    renderActivities(['_id' => $id]);

    if (isset($values['doi']) && !empty($values['doi'])) {
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['doi' => $values['doi']]);
    }
    if (isset($values['pubmed']) && !empty($values['pubmed'])) {
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['pubmed' => $values['pubmed']]);
    }
    // cleanFields($id);
    // die;

    // addUserActivity('update');
    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount(),
        'result' => $collection->findOne(['_id' => $id])
    ]);
});

Route::post('/crud/activities/delete/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    // select the right collection

    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = $DB->to_ObjectID($id);
    }

    $updateResult = $osiris->activities->deleteOne(
        ['_id' => $id]
    );

    $deletedCount = $updateResult->getDeletedCount();

    // addUserActivity('delete');
    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=deleted-" . $deletedCount);
        die();
    }
    echo json_encode([
        'deleted' => $deletedCount
    ]);
});


Route::post('/crud/activities/upload-files/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $mongoid = DB::to_ObjectID($id);

    $target_dir = BASEPATH . "/uploads/";
    if (!is_writable($target_dir)) {
        die("Upload directory $target_dir is unwritable. Please contact admin.");
    }
    $target_dir .= "$id/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
        echo "<!-- The directory $target_dir was successfully created.-->";
    } else {
        echo "<!-- The directory $target_dir exists.-->";
    }


    if (isset($_FILES["file"])) {

        // $target_file = basename($_FILES["file"]["name"]);

        $filename = htmlspecialchars(basename($_FILES["file"]["name"]));
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["file"]["size"];
        $filepath = ROOTPATH . "/uploads/$id/$filename";

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
        } else if (file_exists($target_dir . $filename)) {
            printMsg(lang("Sorry, file already exists.", "Die Datei existiert bereits. Um sie zu überschreiben, muss sie zunächst gelöscht werden."), "error");
        } else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
            printMsg(lang("The file $filename has been uploaded.", "Die Datei <q>$filename</q> wurde hochgeladen."), "success");
            $values = [
                "filename" => $filename,
                "filetype" => $filetype,
                "filesize" => $filesize,
                "filepath" => $filepath,
            ];

            $osiris->activities->updateOne(
                ['_id' => $mongoid],
                ['$push' => ["files" => $values]]
            );
            // $files[] = $values;
        } else {
            printMsg(lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload."), "error");
        }

        header("Location: " . ROOTPATH . "/activities/view/" . $id . "?msg=upload-successful");
        die();
    } else if (isset($_POST['delete'])) {
        $filename = $_POST['delete'];
        if (file_exists($target_dir . $filename)) {
            // Use unlink() function to delete a file
            if (!unlink($target_dir . $filename)) {
                printMsg("$filename cannot be deleted due to an error.", "error");
            } else {
                printMsg(lang("$filename has been deleted.", "$filename wurde gelöscht."), "success");
            }
        }

        $osiris->activities->updateOne(
            ['_id' => $mongoid],
            ['$pull' => ["files" => ["filename" => $filename]]]
        );
        // printMsg("File has been deleted from the database.", "success");

        header("Location: " . ROOTPATH . "/activities/view/" . $id . "?msg=file-deleted-successfully");
        die();
    }
});



Route::post('/crud/activities/update-research-data/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['connections'])) {
        $osiris->activities->updateOne(
            ['_id' => $DB::to_ObjectID($id)],
            ['$unset' => ["connections" => '']]
        );
    } else {
        $values = $_POST['connections'];
        $values = validateValues($values, $DB);

        $osiris->activities->updateOne(
            ['_id' => $DB::to_ObjectID($id)],
            ['$set' => ["connections" => $values]]
        );
    }

    header("Location: " . ROOTPATH . "/activities/view/$id?msg=update-success");
});


Route::post('/crud/activities/update-project-data/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['projects'])) {
        $osiris->activities->updateOne(
            ['_id' => $DB::to_ObjectID($id)],
            ['$unset' => ["projects" => '']]
        );
    } else {
        $values = $_POST['projects'];
        $values = validateValues($values, $DB);
        // dump($values);
        // die;


        $osiris->activities->updateOne(
            ['_id' => $DB::to_ObjectID($id)],
            ['$set' => ["projects" => $values]]
        );
    }
    header("Location: " . ROOTPATH . "/activities/view/$id?msg=update-success");
});


Route::post('/crud/activities/update-authors/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    // prepare id
    if (!isset($_POST['authors']) || empty($_POST['authors'])) {
        echo "Error: Author list cannot be empty.";
        die();
    }
    $id = $DB->to_ObjectID($id);

    $authors = [];
    foreach ($_POST['authors'] as $i => $a) {
        $authors[] = [
            'last' => $a['last'],
            'first' => $a['first'],
            'position' => $a['position'] ?? 'middle',
            'aoi' => (boolval($a['aoi'] ?? false)),
            //|| ($_SESSION['username'] == $a['user'] ?? '')
            'user' => empty($a['user']) ? null : $a['user'],
            'approved' => boolval($a['approved'] ?? false),
        ];
    }

    $osiris->activities->updateOne(
        ['_id' => $id],
        ['$set' => ["authors" => $authors]]
    );

    header("Location: " . ROOTPATH . "/activities/view/$id?msg=update-success");
});



Route::post('/crud/activities/approve/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->activities;
    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = $DB->to_ObjectID($id);
    }
    $approval = intval($_POST['approval'] ?? 0);
    $filter = ['_id' => $id, "authors.user" => $_SESSION['username']];

    switch ($approval) {
        case 1:
            # Yes, this is me and I was affiliated to the AFFILIATION
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.approved" => true, 'authors.$.aoi' => true]]
            );
            break;
        case 2:
            # Yes, but I was not affiliated to the AFFILIATION
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.approved" => true, 'authors.$.aoi' => false]]
            );
            break;
        case 3:
            # No, this is not me
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.user" => null, 'authors.$.aoi' => false]]
            );
            break;
        default:
            # code...
            break;
    }

    $updateCount = $updateResult->getModifiedCount();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateCount
    ]);
});


Route::post('/crud/activities/approve-all', function () {
    include_once BASEPATH . "/php/init.php";
    // prepare id
    $user = $_POST['user'] ?? $_SESSION['username'];

    $osiris->activities->updateMany(
        ['authors.user' => $user],
        ['$set' => ["authors.$.approved" => true]]
    );

    header("Location: " . ROOTPATH . "/issues?msg=update-success");
});


Route::post('/crud/activities/fav', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['activity'])) die('Error: no activity given');
    $id = $_POST['activity'];

    // check if user has id already
    $user = $_SESSION['username'];

    $scientist = $osiris->persons->findOne(['username' => $user]);
    if (empty($scientist)) die('Error: No Scientist found');

    $highlighted = DB::doc2Arr($scientist['highlighted'] ?? []);

    if (in_array($id, $highlighted)) {
        $osiris->persons->updateOne(
            ['_id' => $scientist['_id']],
            ['$pull' => ["highlighted" => $id]]
        );
        echo '{"fav": false}';
        // ['$pull' => ["depts" => $group['id']]]['$push' => ['projects' => $values['name']]]
    } else {
        $osiris->persons->updateOne(
            ['_id' => $scientist['_id']],
            ['$push' => ["highlighted" => $id]]
        );
        echo '{"fav": true}';
    }
}, 'login');
