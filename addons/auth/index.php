<?php

Route::get('/auth/new-user', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/addons/auth/add-user.php";
    include BASEPATH . "/footer.php";
});


Route::post('/auth/new-user', function () {
    include_once BASEPATH . "/php/_db.php";

    if ($osiris->auth->count(['_id' => $_POST['username']]) > 0) {
        $msg = lang("The username is already taken. Please try again.", "Der Nutzername ist bereits vergeben. Versuche es erneut.");
        include BASEPATH . "/header.php";
        printMsg($msg, 'error');
        include BASEPATH . "/addons/auth/add-user.php";
        include BASEPATH . "/footer.php";
        die;
    }

    $values = [
        '_id' => $_POST['username'],
        'username' => $_POST['username'],
        'password' => $_POST['password'],
        'created' => date('d.m.Y')
    ];
    $osiris->auth->insertOne($values);

    $values = $_POST['values'];
    $values['_id'] = $_POST['username'];
    $values['username'] = $_POST['username'];


    $values['is_controlling'] = false;
    $values['is_scientist'] = boolval($values['is_scientist'] ?? false);
    $values['is_leader'] = false;
    $values['is_active'] = true;

    $values['displayname'] = "$values[first] $values[last]";
    $values['formalname'] = "$values[last], $values[first]";
    $values['first_abbr'] = "";
    foreach (explode(" ", $values['first']) as $name) {
        $values['first_abbr'] .= " " . $name[0] . ".";
    }
    // dump($values);
    $osiris->users->insertOne($values);

    header("Location: " . ROOTPATH . "/user/login?msg=account-created");
});
