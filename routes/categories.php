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
    include BASEPATH . "/pages/category-add.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/admin/categories/(\d)/(.*)', function ($lvl, $id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $id = urldecode($id);
    $category = $osiris->categories->findOne(['id' => $id]);
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
    include BASEPATH . "/pages/category-add.php";
    include BASEPATH . "/footer.php";
}, 'login');


?>
