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

Route::get('/activities/new', function () {
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

Route::post('/activities/new', function () {
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
        $DB->renderActivities(['_id' =>  $activity['_id']]);
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


?>