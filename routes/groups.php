<?php

/**
 * Routing file for organizational groups
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

Route::get('/groups', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Groups", "Gruppen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/groups.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/groups/new', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Groups", "Gruppen"), 'path' => "/groups"],
        ['name' => lang("New", "Neu")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/groups-add.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/groups/view/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    if (DB::is_ObjectID($id)) {
        $mongo_id = $DB->to_ObjectID($id);
        $group = $osiris->groups->findOne(['_id' => $mongo_id]);
        $id = $group['id'];
    } else {
        // wichtig für umlaute
        $id = urldecode($id);
        $group = $osiris->groups->findOne(['id' => $id]);
        // $id = strval($group['_id'] ?? '');
    }
    if (empty($group)) {
        header("Location: " . ROOTPATH . "/groups?msg=not-found");
        die;
    }
    $breadcrumb = [
        ['name' => lang("Groups", "Gruppen"), 'path' => "/groups"],
        ['name' => $group['id']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/group.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/groups/edit/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $id = urldecode($id);
    $group = $osiris->groups->findOne(['id' => $id]);
    if (empty($group)) {
        header("Location: " . ROOTPATH . "/groups?msg=not-found");
        die;
    }
    $breadcrumb = [
        ['name' => lang("Groups", "Gruppen"), 'path' => "/groups"],
        ['name' =>  $group['id'], 'path' => "/groups/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    global $form;
    $form = DB::doc2Arr($group);

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/groups-add.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::post('/crud/groups/create', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->groups;

    $values = validateValues($_POST['values'], $DB);

    // check if group name already exists:
    $group_exist = $collection->findOne(['id' => $values['id']]);
    if (!empty($group_exist)) {
        header("Location: " . ROOTPATH . "/groups/new?msg=Group ID does already exist.");
        die();
    }

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = strtolower($_SESSION['username']);

    if (!empty($values['parent'])) {
        $parent = $Groups->getGroup($values['parent']);
        if ($parent['color'] != '#000000') $values['color'] = $parent['color'];
    }

    if (isset($values['head'])) {
        foreach ($values['head'] as $head) {
            $osiris->persons->updateOne(
                ['username' => $head],
                ['$push' => ["depts" => $values['id']]]
            );
        }
    }

    if (!empty($values['parent'])) {
        $parent = $Groups->getGroup($values['parent']);
        if ($parent['color'] != '#000000') $values['color'] = $parent['color'];
        $values['level'] = $parent['level'] + 1;
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

Route::post('/crud/groups/update/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->groups;

    $values = validateValues($_POST['values'], $DB);
    // add information on creating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = strtolower($_SESSION['username']);

    $id = $DB->to_ObjectID($id);

    // check if ID has changes
    $group = $osiris->groups->findOne(['_id' => $id]);
    if ($group['id'] != $values['id']) {
        // change IDs of Members
        $osiris->persons->updateMany(
            ['depts' => $group['id']],
            ['$set' => ['depts.$[elem]' => $values['id']]],
            ['arrayFilters' => [['elem' => ['$eq' => $group['id']]]], 'multi' => true]
        );
    }

    if (!empty($values['parent'])) {
        $parent = $Groups->getGroup($values['parent']);
        if ($parent['color'] != '#000000') $values['color'] = $parent['color'];
        $values['level'] = $parent['level'] + 1;
    }

    // check if head is connected 
    if (isset($values['head'])) {
        foreach ($values['head'] as $head) {
            $N = $osiris->persons->count(['username' => $head, 'depts' => $values['id']]);
            if ($N == 0) {
                $osiris->persons->updateOne(
                    ['username' => $head],
                    ['$push' => ["depts" => $values['id']]]
                );
            }
        }
    }

    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    // dump($updateResult->getModifiedCount(), true);
    // die;

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }

    echo json_encode([
        'inserted' => $updateResult->getModifiedCount(),
        'id' => $id,
    ]);
});

Route::post('/crud/groups/delete/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    // select the right collection

    // prepare id
    $id = $DB->to_ObjectID($id);

    // remove from all users
    $group = $osiris->groups->findOne(['_id' => $id]);
    $osiris->persons->updateOne(
        ['depts' => $group['id']],
        ['$pull' => ["depts" => $group['id']]]
    );

    $updateResult = $osiris->groups->deleteOne(
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
