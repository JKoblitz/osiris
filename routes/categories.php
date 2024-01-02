<?php

Route::get('/admin/categories', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/categories.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/admin/categories/new', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien"), 'path' => "/admin/categories"],
        ['name' => lang("New", "Neu")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/category.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/admin/categories/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $id = urldecode($id);
    $category = $osiris->adminCategories->findOne(['id' => $id]);
    if (empty($category)) {
        header("Location: " . ROOTPATH . "/categories?msg=not-found");
        die;
    }
    $name = lang($category['name'], $category['name_de']);
    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien"), 'path' => "/admin/categories"],
        ['name' => $name]
    ];

    global $form;
    $form = DB::doc2Arr($category);

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/category.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/admin/types/new', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien"), 'path' => "/admin/categories"],
        ['name' => lang("New Type", "Neuer Typ")]
    ];
    $t = $_GET['parent'] ?? '';
    $st = $t;
    $type = [
        "id" => '',
        "icon" => $type['icon'] ?? 'placeholder',
        "name" => '',
        "name_de" => '',
        "new" => true,
        "modules" => [
            "title",
            "authors",
            "date"
        ],
        "template" => [
            "print" => "{authors} ({year}) {title}.",
            "title" => "{title}",
            "subtitle" => "{authors}, {date}"
        ],
        "coins" => 0,
        "parent" => $t

    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/category-type.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/admin/types/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $id = urldecode($id);
    $type = $osiris->adminTypes->findOne(['id' => $id]);
    if (empty($type)) {
        header("Location: " . ROOTPATH . "/categories?msg=not-found");
        die;
    }
    $name = lang($type['name'], $type['name_de']);

    $t = $type['parent'];
    $parent = $osiris->adminCategories->findOne(['id' => $t]);
    $color = $parent['color'] ?? '#000000';
    $st = $type['id'];
    $submember = $osiris->activities->count(['type' => $t, 'subtype' => $st]);

    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien"), 'path' => "/admin/categories"],
        ['name' => lang($parent['name'], $parent['name_de']), 'path' => "/admin/categories/" . $t],
        ['name' => $name]
    ];

    global $form;
    $form = DB::doc2Arr($type);

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/category-type.php";
    include BASEPATH . "/footer.php";
}, 'login');





/**
 * CRUD routes
 */

Route::post('/crud/(categories|types)/create', function ($col) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    $values = validateValues($_POST['values'], $DB);
    dump($values, true);
    die;

    if ($col == 'categories') {
        $collection = $osiris->adminCategories;
    } else {
        $collection = $osiris->adminTypes;
        if (!isset($values['parent'])) {
            header("Location: " . ROOTPATH . "/types/new?msg=Type must have parent.");
            die();
        }
    }

    // check if category ID already exists:
    $category_exist = $collection->findOne(['id' => $values['id']]);
    if (!empty($category_exist)) {
        header("Location: " . ROOTPATH . "/$col/new?msg=Category ID does already exist.");
        die();
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

Route::post('/crud/(categories|types)/update/([A-Za-z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    if ($col == 'categories') {
        $collection = $osiris->adminCategories;
    } else {
        $collection = $osiris->adminTypes;
    }


    $values = validateValues($_POST['values'], $DB);

    // check if ID has changed
    if (isset($_POST['original_id']) && $_POST['original_id'] != $values['id']) {
        // TODO: update all activities 
    }

    if ($col == 'types') {
        // types need a categorie a.k.a. parent
        if (!isset($values['parent'])) {
            die("Type must have a parent category.");
        }
        // check if parent has changed
        if (isset($_POST['original_parent']) && $_POST['original_parent'] != $values['parent']) {
            // TODO: update all activities 
        }
    }

    dump($values, true);
    die;


    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = strtolower($_SESSION['username']);

    
    $mongo_id = $DB->to_ObjectID($id);
    $updateResult = $collection->updateOne(
        ['_id' => $mongo_id],
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

Route::post('/crud/categories/delete/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    // select the right collection

    // prepare id
    $id = $DB->to_ObjectID($id);

    // remove from all users
    $category = $osiris->adminCategories->findOne(['_id' => $id]);
    $osiris->persons->updateOne(
        ['depts' => $category['id']],
        ['$pull' => ["depts" => $category['id']]]
    );

    $updateResult = $osiris->adminCategories->deleteOne(
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
