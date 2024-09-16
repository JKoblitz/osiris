<?php

/**
 * Routes for conferences
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @package     OSIRIS
 * @since       1.3.5
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

Route::get('/conferences', function () {

    $breadcrumb = [
        ['name' => lang('Conferences', 'Konferenzen')]
    ];

    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/conferences.php";
    include BASEPATH . "/footer.php";
});


Route::get('/conferences/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $conf_id = DB::to_ObjectID($id);
    // get conference
    $conference = $osiris->conferences->findOne(['_id' => $conf_id]);
    if (!$conference) {
        $_SESSION['msg'] = lang('Conference not found', 'Konferenz nicht gefunden');
        header("Location: " . ROOTPATH . '/conferences');
        die();
    }

    $breadcrumb = [
        ['name' => lang('Conferences', 'Konferenzen'), 'path' => '/conferences'],
        ['name' => $conference['title']]
    ];

    $activities = $osiris->activities->find(['conference_id' => $id])->toArray();

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/conference.php";
    include BASEPATH . "/footer.php";
});


// download conference as ics
Route::get('/conference/ics/(.*)', function ($id) {
    include BASEPATH . '/php/ICS.php';
    include_once BASEPATH . "/php/init.php";

    $conf_id = DB::to_ObjectID($id);
    // get conference
    $conf = $osiris->conferences->findOne(['_id' => $conf_id]);

    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename=invite.ics');

    $ics = new ICS(array(
        'location' => $conf['location'],
        'summary' => $conf['title'],
        'dtstart' => $conf['start'],
        'dtend' => $conf['end'],
        'description' => $conf['title_full'] ?? '',
        'url' => $conf['url'] ?? ''
    ));

    echo $ics->to_string();
});


// crud/conferences/add

Route::post('/crud/conferences/add', function () {
    include_once BASEPATH . "/php/init.php";

    $values = $_POST;
    $redirect = $values['redirect'] ?? '/';
    unset($values['redirect']);
    $values['created'] = date('Y-m-d');
    $values['created_by'] = $_SESSION['username'];

    $start = strtotime($values['start']);
    $values['year'] = intval(date('Y', $start));
    $values['month'] = intval(date('n', $start));
    $values['quarter'] = ceil($values['month'] / 3);
    $values['day'] = intval(date('j', $start));
    $values['interests'] = [];
    $values['participants'] = [];
    $values['activities'] = [];

    $osiris->conferences->insertOne($values);

    header("Location: $redirect");
}, 'login');


Route::post('/crud/conferences/delete/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $osiris->conferences->deleteOne(['_id' => DB::to_ObjectID($id)]);
    header("Location: " . ROOTPATH . '/');
}, 'login');


Route::post('/ajax/conferences/toggle-interest', function () {
    include_once BASEPATH . "/php/init.php";
    // only ajax requests
    if ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
        die('No direct access allowed');
    }
    // required data: conference_id
    if (!isset($_POST['conference'])) {
        die('No conference given');
    }
    $conf_id = DB::to_ObjectID($_POST['conference']);
    // get conference
    $conference = $osiris->conferences->findOne(['_id' => $conf_id]);
    $key = $_POST['type'] ?? 'interests';
    // check if user is already interested
    if (in_array($_SESSION['username'], DB::doc2Arr($conference[$key]))) {
        // remove user from interests
        $osiris->conferences->updateOne(['_id' => $conf_id], ['$pull' => [$key => $_SESSION['username']]]);
    } else {
        // add user to interests
        $osiris->conferences->updateOne(['_id' => $conf_id], ['$push' => [$key => $_SESSION['username']]]);
    }
    // return new interest count
    $conference = $osiris->conferences->findOne(['_id' => $conf_id]);
    echo count($conference[$key]);
}, 'login');
