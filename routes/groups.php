<?php
    
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


?>
