<?php

Route::get('/guests/?', function () {
    include_once BASEPATH . "/php/init.php";

    $breadcrumb = [
        ['name' => lang('Guests', 'Gäste')],
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/guestforms/list.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/guests/new', function () {
    include_once BASEPATH . "/php/init.php";

    // Generate new id
    $id = uniqid();
    $form = [];

    $breadcrumb = [
        ['name' => lang('Guests', 'Gäste'), 'path' => "/guests"],
        ['name' => lang("New", "Erstellen")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/guestforms/form.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/guests/edit/([a-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $form = $osiris->guests->findOne(['id' => $id]);
    $breadcrumb = [
        ['name' => lang('Guests', 'Gäste'), 'path' => "/guests"],
        ['name' => $id]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/guestforms/form.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/guests/view/([a-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $form = $osiris->guests->findOne(['id' => $id]);
    $breadcrumb = [
        ['name' => lang('Guests', 'Gäste'), 'path' => "/guests"],
        ['name' => $id]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/guestforms/view.php";
    include BASEPATH . "/footer.php";
}, 'login');


// POST METHODS
Route::post('/guests/save', function () {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->guests;

    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $values = validateValues($_POST['values'], $DB);
    // dump($_POST);
    if (!isset($values['id'])) {
        echo "no id given";
        die;
    }
    $id = $values['id'];

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = strtolower($_SESSION['username']);

    // check if check boxes are checked
    $values['legal']['general'] = $values['legal']['general'] ?? false;
    $values['legal']['data_security'] = $values['legal']['data_security'] ?? false;
    $values['legal']['data_protection'] = $values['legal']['data_protection'] ?? false;
    $values['legal']['safety_instruction'] = $values['legal']['safety_instruction'] ?? false;


    // add supervisor information
    $supervisor = $DB->getPerson($values['user']);
    if (empty($supervisor)) die('Supervisor does not exist');
    $values['supervisor'] = [
        "user" => $supervisor['_id'],
        "name" => $supervisor['displayname']
    ];

    unset($values['user']);

    // check if guest already exists:
    $guest_exist = $collection->findOne(['id' => $id]);
    if (!empty($guest_exist)) {
        $id = $guest_exist['id'];
        $collection->updateOne(
            ['id' => $id],
            ['$set' => $values]
        );

        header("Location: " . ROOTPATH . "/guests/view/$id?msg=success");
        die;
    }

    // dump($values);
    // die;
    $insertOneResult  = $collection->insertOne($values);

    header("Location: " . ROOTPATH . "/guests/view/$id?msg=success");
}, 'login');
