<?php

/**
 * Routing file for users (tables, profiles, searches) and related stuff
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

Route::get('/user/browse', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Users', 'Personen')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/users-table.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/search/user', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => lang("Search", "Suche")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-search.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/whats-up', function () {
    $breadcrumb = [
        ['name' => lang('What\'s up?', 'Was ist los?')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/whats-up.php";
    include BASEPATH . "/footer.php";
});

/**
 * Editor routes
 */

Route::get('/user/edit/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Document.php";

    // $id = $DB->to_ObjectID($id);

    $data = $DB->getPerson($user);
    if (empty($data)) {
        header("Location: " . ROOTPATH . "/user/browse");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $data['name'], 'path' => "/profile/$user"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/user/edit-bio/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    // $id = $DB->to_ObjectID($id);

    $data = $DB->getPerson($user);
    if (empty($data)) {
        header("Location: " . ROOTPATH . "/user/browse");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $data['name'], 'path' => "/profile/$user"],
        ['name' => lang("Edit Biography", "Biographie bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-edit-bio.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/user/visibility/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    // include_once BASEPATH . "/php/Document.php";

    $data = $DB->getPerson($user);
    if (empty($data)) {
        header("Location: " . ROOTPATH . "/user/browse");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $data['name'], 'path' => "/profile/$user"],
        ['name' => lang("Configure web view", "Webansicht Konfigurieren")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-webconfigure.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/user/delete/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    $data = $DB->getPerson($user);
    $data = DB::doc2Arr($data);
    if (empty($data)) {
        header("Location: " . ROOTPATH . "/user/browse");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $data['name'], 'path' => "/profile/$user"],
        ['name' => lang("Inactivate", "Inaktivieren")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-delete.php";
    include BASEPATH . "/footer.php";
}, 'login');



// Profile

Route::get('/profile/?(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/Document.php";
    include_once BASEPATH . "/php/_achievements.php";

    $Format = new Document($user);

    $scientist = $DB->getPerson($user);

    if (empty($scientist)) {
        header("Location: " . ROOTPATH . "/user/browse?msg=user-does-not-exist");
        die;
    }
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/profile.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/my-year/?(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/Document.php";
    $Format = new Document($user);

    $scientist = $DB->getPerson($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => lang("$name", "$name"), 'path' => "/profile/$user"],
        ['name' => lang("The Year", "Das Jahr")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/my-year.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/issues', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $breadcrumb = [
        ['name' => lang('Issues', 'Warnungen')]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/issues.php";
    include BASEPATH . "/footer.php";
});


Route::get('/expertise', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang('Expertise search', 'Experten-Suche')]
    ];
    // include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/expertise.php";
    include BASEPATH . "/footer.php";
});


Route::get('/achievements/?(.*)', function ($user) {
    if (empty($user)) $user = $_SESSION['username'];

    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/_achievements.php";

    $scientist = $DB->getPerson($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Personen'), 'path' => "/user/browse"],
        ['name' => $name, 'path' => "/profile/$user"],
        ['name' => lang('Achievements', 'Errungenschaften')]

    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/achievements.php";
    include BASEPATH . "/footer.php";
});




// Synchronize users

Route::get('/synchronize-users', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/_login.php";
    include BASEPATH . "/header.php";


    $blacklist = [
        "webkalender",
        "sequenzer",
        "pvnano",
        "pre19", //presse praktikant
        "pacbio",
        "xcu",
        "guest",
        "hplce35",
        "dsmzmutz",
        "SNA",
        "dsmzplant",
        "jkipv",
        "dsmzpv",
        "root",
        "oxadmin",
        "dau2", //Dummy Ata
        "admin-mas19",
        "dsc20", // DSMZ SCAN
        "femto",
        "robo20",
        "dsmzbug",
        "lagerk16",
        "services",
        "admin-maa21",
        "admin-vig21",
        "bug-hplc",
        "dsmzmebo",
        "mutz-prakt",
        "gramnegative",
        "spi", //Science Policy
        "christian.quast",
        "admin-ols23",
        "pan-test23",
        "admin-pie23",
        "admin-lla16",
        "kodierstation",
        "mi03-prakt1",
        "mi03-prakt2",
        "mi03-prakt3",
        "mi03-prakt4",
        "mi03-prakt5",
        "mi03-prakt6",
        "mi03-prakt7",
        "pan-test24",
        "pan-test25",
        "pan-test26",
        "pan-test27",
        "johnwick",
    ];

    $users = getUsers();
    // dump($users);
    foreach ($users as $username => $active) {
        // check if username is on the blacklist
        if (in_array($username, $blacklist)) continue;

        // first: check if user is in database
        $USER = $DB->getPerson($username);
        // if user does not exists
        if (empty($USER)) {
            // if inactive: do nothing
            if (!$active) continue;

            // else: add new user
            $new_user = newUser($username);
            if (empty($new_user)) {
                // this should never happen
                echo "<p>$username did not exist.</p>";
                continue;
            }
            dump($new_user, true);
            $osiris->persons->insertOne($new_user);
        } else {
            // user is no longer active
            if (!$active && $USER['is_active']) {
                echo ('<p>' . $username . ' is no longer active.</p>');
                $osiris->persons->updateOne(
                    ['username' => $username],
                    ['$set' => ['is_active' => false]]
                );
            }

            // if (empty($USER['dept'])){
            //     $new_user = newUser($username);
            //     if ($new_user['person']['dept']){
            //         $osiris->persons->updateOne(
            //             ['username' => $username],
            //             ['$set' => ['dept' => $new_user['person']['dept']]]
            //         );
            //     }                
            // }
            // else if ($active && !$USER['is_active']) {
            //     echo ('<p>' . $username . ' is active again.</p>');
            //     $osiris->persons->updateOne(
            //         ['username' => $username],
            //         ['$set' => ['is_active' => true]]
            //     );
            // }
        }
    }
    echo "User synchronization successful";
    include BASEPATH . "/footer.php";
});
