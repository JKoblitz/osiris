<?php

/**
 * Routing file for admin settings
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

 include_once BASEPATH . "/routes/admin.fields.php";

Route::get('/admin/(general|roles|features)', function ($page) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    $breadcrumb = [
        ['name' => lang("Admin Panel $page")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/admin/$page.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/admin/categories', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/admin/categories.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/admin/categories/new', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang("Categories", "Kategorien"), 'path' => "/admin/categories"],
        ['name' => lang("New", "Neu")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/admin/category.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/admin/categories/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

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
    include BASEPATH . "/pages/admin/category.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/admin/types/new', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

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
    include BASEPATH . "/pages/admin/category-type.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/admin/types/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

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
    include BASEPATH . "/pages/admin/category-type.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/settings/activities', function () {
    include_once BASEPATH . "/php/init.php";

    $t = urldecode($_GET['type']);
    $type = $osiris->adminTypes->findOne(['id' => $t]);
    $parent = $osiris->adminCategories->findone(['id'=>$type['parent']]);
    echo return_rest([
        'category' => DB::doc2Arr($parent),
        'type'=> DB::doc2Arr($type)
    ]);
});

Route::get('/settings/modules', function () {
    include_once BASEPATH . "/php/init.php";

    include_once BASEPATH . "/php/Modules.php";
    $form = array();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $mongoid = $DB->to_ObjectID($_GET['id']);
        $form = $osiris->activities->findOne(['_id' => $mongoid]);
    }
    $Modules = new Modules($form, $_GET['copy'] ?? false);

    if (isset($_GET['modules'])) {
        $Modules->print_modules($_GET['modules']);
    } else {
        $Modules->print_all_modules();
    }
});


/**
 * CRUD routes
 */

Route::post('/crud/admin/general', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    $msg = 'settings-saved';
    if (isset($_POST['general'])) {
        foreach ($_POST['general'] as $key => $value) {
            $osiris->adminGeneral->deleteOne(['key' => $key]);
            $osiris->adminGeneral->insertOne([
                'key' => $key,
                'value' => $value
            ]);
        }
    }

    if (isset($_FILES["logo"])) {
        $filename = htmlspecialchars(basename($_FILES["logo"]["name"]));
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["logo"]["size"];

        if ($_FILES['logo']['error'] != UPLOAD_ERR_OK) {
            $msg = match ($_FILES['logo']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
        } else if ($filesize > 2000000) {
            $msg = lang("File is too big: max 2 MB is allowed.", "Die Datei ist zu groß: maximal 2 MB sind erlaubt.");
        } else {
            $val = new MongoDB\BSON\Binary(file_get_contents($_FILES["logo"]["tmp_name"]), MongoDB\BSON\Binary::TYPE_GENERIC);
            // first: delete logo, then: insert new one
            $osiris->adminGeneral->deleteOne(['key' => 'logo']);
            $updateResult = $osiris->adminGeneral->insertOne([
                'key' => 'logo',
                'value' => $val,
                'ext' => $filetype
            ]);
        }
    }

    header("Location: " . ROOTPATH . "/admin/general?msg=" . $msg);
}, 'login');


Route::post('/crud/admin/roles', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');


    if (isset($_POST['values'])) {
        $osiris->adminRights->deleteMany([]);
        $rights = $_POST['values'];
        foreach ($rights as $right => $roles) {
            foreach ($roles as $role => $perm) {
                $r = [
                    'role' => $role,
                    'right' => $right,
                    'value' => boolval($perm)
                ];
                $osiris->adminRights->insertOne($r);
            }
        }
    }

    $msg = 'settings-saved';

    header("Location: " . ROOTPATH . "/admin/roles?msg=" . $msg);
}, 'login');


Route::post('/crud/admin/features', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');


    if (isset($_POST['values'])) {
        $osiris->adminFeatures->deleteMany([]);
        $features = $_POST['values'];
        foreach ($features as $feature => $enabled) {
            $r = [
                'feature' => $feature,
                'enabled' => boolval($enabled)
            ];
            $osiris->adminFeatures->insertOne($r);
        }
    }

    if (isset($_POST['general'])) {
        foreach ($_POST['general'] as $key => $value) {
            $osiris->adminGeneral->deleteOne(['key' => $key]);
            $osiris->adminGeneral->insertOne([
                'key' => $key,
                'value' => $value
            ]);
        }
    }



    $msg = 'settings-saved';

    header("Location: " . ROOTPATH . "/admin/features?msg=" . $msg);
}, 'login');


Route::post('/crud/(categories|types)/create', function ($col) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    if (!isset($_POST['values'])) die("no values given");

    $values = validateValues($_POST['values'], $DB);

    if ($col == 'categories') {
        $collection = $osiris->adminCategories;
    } else {
        $collection = $osiris->adminTypes;
        if (!isset($values['parent'])) {
            header("Location: " . ROOTPATH . "/types/new?msg=Type must have a parent category.");
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
    // $id = $insertOneResult->getInsertedId();
    $id = $values['id'];

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
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    if (!isset($_POST['values'])) die("no values given");

    if ($col == 'categories') {
        $collection = $osiris->adminCategories;
        $key = 'type';
    } else {
        $collection = $osiris->adminTypes;
        $key = 'subtype';
    }

    $values = validateValues($_POST['values'], $DB);

    // check if ID has changed
    if (isset($_POST['original_id']) && $_POST['original_id'] != $values['id']) {
        // update all connected activities 
        $osiris->activities->updateMany(
            [$key => $_POST['original_id']],
            ['$set' => [$key => $values['id']]]
        );
    }

    if ($col == 'types') {
        // types need a categorie a.k.a. parent
        if (!isset($values['parent'])) {
            die("Type must have a parent category.");
        }
        // check if parent has changed
        if (isset($_POST['original_parent']) && $_POST['original_parent'] != $values['parent']) {
            // update all connected activities 
            $osiris->activities->updateMany(
                ['type' => $_POST['original_parent'], 'subtype' => $values['id']],
                ['$set' => ['type' => $values['parent']]]
            );
        }
        // checkbox default
        $values['disabled'] = $values['disabled'] ?? false;
        
    }

    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = $_SESSION['username'];

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

Route::post('/crud/(categories|types)/delete/(.*)', function ($col, $id) {
    include_once BASEPATH . "/php/init.php";
    if (!$Settings->hasPermission('admin.see')) die('You have no permission to be here.');

    // select the right collection

    if ($col == 'categories') {
        $collection = $osiris->adminCategories;
        $member = $osiris->activities->count(['type' => $id]);
    } else {
        $collection = $osiris->adminTypes;
        $member = $osiris->activities->count(['subtype' => $id]);
    }

    // check that no activities are connected
    if ($member !== 0) die('Cannot delete as long as activities are connected.');

    // prepare id
    $updateResult = $collection->deleteOne(
        ['id' => $id]
    );
    if ($col == 'categories') {
        $osiris->adminTypes->deleteMany(['parent' => $id]);
    }

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


Route::post('/crud/categories/update-order', function () {
    include_once BASEPATH . "/php/init.php";
    // select the right collection

    foreach ($_POST['order'] as $i => $id) {
        $osiris->adminCategories->updateOne(
            ['id' => $id],
            ['$set' => ['order'=>$i]]
        );
        # code...
    }
   
    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=deleted-" . $deletedCount);
        die();
    }
});
