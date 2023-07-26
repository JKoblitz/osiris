<?php

Route::get('/guests/?', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $breadcrumb = [
        ['name' => lang('Guests', 'Gäste')],
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/guestforms/list.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/guests/new', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
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
    include_once BASEPATH . "/php/_db.php";

    $collection = $osiris->guests;

    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $values = validateValues($_POST['values']);
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
    $values['general'] = $values['general'] ?? false;
    $values['data_security'] = $values['data_security'] ?? false;
    $values['data_protection'] = $values['data_protection'] ?? false;
    $values['safety_instruction'] = $values['safety_instruction'] ?? false;

    // check if module already exists:
    $module_exist = $collection->findOne(['id' => $id]);
    if (!empty($module_exist)) {
        $id = $module_exist['id'];
        $collection->updateOne(
            ['id' => $id],
            ['$set' => $values]
        );

        header("Location: " . ROOTPATH . "/guests/edit/$id?msg=success");
        die;
    }

    // dump($values);
    // die;
    $insertOneResult  = $collection->insertOne($values);

    header("Location: " . ROOTPATH . "/guests/edit/$id?msg=success");
}, 'login');
