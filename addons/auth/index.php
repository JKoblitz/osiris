<?php

Route::get('/auth/new-user', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/auth/add-user.php";
    include BASEPATH . "/footer.php";
});


Route::post('/auth/new-user', function () {
    include_once BASEPATH . "/php/init.php";

    if ($osiris->persons->count(['username' => $_POST['username']]) > 0) {
        $msg = lang("The username is already taken. Please try again.", "Der Nutzername ist bereits vergeben. Versuche es erneut.");
        include BASEPATH . "/header.php";
        printMsg($msg, 'error');
        include BASEPATH . "/addons/auth/add-user.php";
        include BASEPATH . "/footer.php";
        die;
    }

    $person = $_POST['values'];
    $person['username'] = $_POST['username'];
    $person['password'] = $_POST['password'];
    $person['displayname'] = "$person[first] $person[last]";
    $person['formalname'] = "$person[last], $person[first]";
    $person['first_abbr'] = "";
    foreach (explode(" ", $person['first']) as $name) {
        $person['first_abbr'] .= " " . $name[0] . ".";
    }
    $person['created'] = date('d.m.Y');
    $person['roles'] = [];
    if (boolval($person['is_scientist'] ?? false)) $person['roles'][] = 'scientist';

    $person['is_active'] = true;
    $osiris->persons->insertOne($person);

    header("Location: " . ROOTPATH . "/user/login?msg=account-created");
});
