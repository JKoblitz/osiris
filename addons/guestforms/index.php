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

    $values = $_POST['values'];
    // get supervisor first, otherwise users are converted into authors
    $supervisor = $DB->getPerson($values['user']);
    if (empty($supervisor)) die('Supervisor does not exist');
    // remove supervisor from OG dataset
    unset($values['user']);

    // standardize inputs
    $values = validateValues($values, $DB);
    // dump($_POST);
    if (!isset($values['id'])) {
        echo "no id given";
        die;
    }
    $id = $values['id'];

    $finished = false;
    $guest_exist = $collection->findOne(['id' => $id]);
    if (!empty($guest_exist)) {
        $finished = $guest_exist['legal']['general'] ?? false;
    } else {
        // add information on creating process
        $values['created'] = date('Y-m-d');
        $values['created_by'] = strtolower($_SESSION['username']);

        // check if check boxes are checked
        $values['legal']['general'] = $values['legal']['general'] ?? false;
        $values['legal']['data_security'] = $values['legal']['data_security'] ?? false;
        $values['legal']['data_protection'] = $values['legal']['data_protection'] ?? false;
        $values['legal']['safety_instruction'] = $values['legal']['safety_instruction'] ?? false;

        // add supervisor information
        $values['supervisor'] = [
            "user" => $supervisor['username'],
            "name" => $supervisor['displayname']
        ];
    }

    $msg = "success";
    
    if (!$finished && $Settings->featureEnabled('guest-forms')) {

        // check if server and secret key are defined
        $guest_server = $Settings->get('guest-forms-server');
        $guest_secret = $Settings->get('guest-forms-secret-key');
        if (empty($guest_server)) {
            $msg = "Guest+server+is+not+defined.+Please+contact+admin.";
        } else if (empty($guest_secret)) {
            $msg = "Secret+key+is+not+defined.+Please+contact+admin.";
        } else {
            // if server and key is defined:
            // send data to guest server
            $URL = $guest_server . '/api/post';
            $postData = $values;
            $postData['secret'] = $guest_secret;
            $postRes = CallAPI('JSON', $URL, $postData);
            $postRes = json_decode($postRes, true);
            if ($postRes['message'] != 'Success') {
                die($postRes['message']);
            }
        }
    }

    // check if guest already exists:
    if (!empty($guest_exist)) {
        $id = $guest_exist['id'];
        $collection->updateOne(
            ['id' => $id],
            ['$set' => $values]
        );
    } else {
        $insertOneResult  = $collection->insertOne($values);
    }

    header("Location: " . ROOTPATH . "/guests/view/$id?msg=$msg");
}, 'login');





Route::post('/guests/synchronize/([a-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->guests;

    $guest_server = $Settings->get('guest-forms-server');
    if (empty($guest_server)) {
        header("Location: " . ROOTPATH . "/guests?msg=Guest+server+is+not+defined.+Please+contact+admin.");
        die;
    }
    $guest_secret = $Settings->get('guest-forms-secret-key');
    if (empty($guest_secret)) {
        header("Location: " . ROOTPATH . "/guests?msg=Secret+key+is+not+defined.+Please+contact+admin.");
        die;
    }

    // send data to guest server
    $URL = $guest_server . '/api/get/' . $id;
    if (!str_contains($URL, '//')) $URL = "https://" . $URL;
    $postData = [];
    $postData['secret'] = $guest_secret;
    $postRes = CallAPI('GET', $URL, $postData);
    $values = json_decode($postRes, true);

    // check if guest already exists:
    $guest_exist = $collection->findOne(['id' => $id]);
    if (!empty($guest_exist)) {
        $collection->updateOne(
            ['id' => $id],
            ['$set' => $values]
        );

        header("Location: " . ROOTPATH . "/guests/view/$id?msg=success");
        die;
    } else {
        header("Location: " . ROOTPATH . "/guests?msg=guest+not+found");
        die;
    }
}, 'login');


/**
 * Update data points within 
 */
Route::post('/guests/update/([a-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->guests;
    $values = $_POST['values'];

    $collection->updateOne(
        ['id' => $id],
        ['$set' => $values]
    );

    header("Location: " . ROOTPATH . "/guests/view/$id?msg=success");
}, 'login');
