<?php

/**
 * Routing file for users (tables, profiles, searches) and related stuff
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
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
    if (!empty($user) && DB::to_ObjectID($user)) {
        $mongo_id = DB::to_ObjectID($user);
        $scientist = $osiris->persons->findOne(['_id' => $mongo_id]);
        $user = $scientist['username'];
    } else {
        $scientist = $DB->getPerson($user);
    }
    include_once BASEPATH . "/php/Document.php";
    include_once BASEPATH . "/php/_achievements.php";

    $Format = new Document($user);

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


// not in use
Route::get('/user/picture/(.*)', function ($user, $cls = 'profile-img') {
    include_once BASEPATH . "/php/init.php";
    $default = '<img src="' . ROOTPATH . '/img/no-photo.png" alt="Profilbild" class="' . $cls . '">';
    if ($Settings->featureEnabled('db_pictures')) {
        $img = $osiris->userImages->findOne(['user' => $user]);

        image_type_to_mime_type($img['ext']);
        if (empty($img)) {
            echo $default;
            return;
        }
        if ($img['ext'] == 'svg') {
            $img['ext'] = 'svg+xml';
        }
        echo '<img src="data:image/' . $img['ext'] . ';base64,' . base64_encode($img['img']) . ' " class="' . $cls . '" />';
        return;
    } else {
        $img_exist = file_exists(BASEPATH . "/img/users/$user.jpg");
        if (!$img_exist) {
            echo $default;
            return;
        }
        $img = ROOTPATH . "/img/users/$user.jpg";
        echo ' <img src="' . $img . '" alt="Profilbild" class="' . $cls . '">';
    }
});

// Synchronize users

Route::get('/synchronize-users', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/_login.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/synchronize-users.php";
    include BASEPATH . "/footer.php";
});

Route::post('/synchronize-users', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/_login.php";
    include BASEPATH . "/header.php";

    if (isset($_POST['inactivate'])) {
        $keep = [
            '_id',
            'displayname',
            'formalname',
            'first_abbr',
            'updated',
            'updated_by',
            "academic_title",
            "first",
            "last",
            "name",
            "depts",
            "username"
        ];
        foreach ($_POST['inactivate'] as $username) {
            $data = $DB->getPerson($username);
            $name = $data['displayname'] ?? $username;
            $arr = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $keep)) continue;
                $arr[$key] = null;
            }
            $arr['is_active'] = false;
            $osiris->persons->updateOne(
                ['username' => $username],
                ['$set' => $arr]
            );
            if (file_exists(BASEPATH . "/img/users/$username.jpg")) {
                unlink(BASEPATH . "/img/users/$username.jpg");
            }
            echo "<p><i class='ph ph-user-minus text-danger'></i> $name ($username) inactivated and personal data deleted.</p>";
        }
    }


    if (isset($_POST['reactivate'])) {
        foreach ($_POST['reactivate'] as $username) {

            $osiris->persons->updateOne(
                ['username' => $username],
                ['$set' => ['is_active' => true]]
            );
            echo "<p><i class='ph ph-user-check text-danger'></i> $name ($username) reactivated.</p>";
        }
    }


    if (isset($_POST['add'])) {
        foreach ($_POST['add'] as $username) {
            // check if user exists
            $USER = $DB->getPerson($username);
            if (!empty($USER)) {
                echo "<p><i class='ph ph-warning text-warning'></i> $username already exists.</p>";
                continue;
            }
            $new_user = newUser($username);
            if (empty($new_user)) {
                echo "<p><i class='ph ph-warning text-danger'></i> $username did not exist.</p>";
                continue;
            }
            $osiris->persons->insertOne($new_user);
            echo "<p><i class='ph ph-user-plus text-success'></i> New user created: $new_user[displayname] ($new_user[username])</p>";
        }
    }
    if (isset($_POST['blacklist'])) {
        $bl = $Settings->get('ldap-sync-blacklist');
        if (!empty($bl)) {
            $bl = explode(',', $bl);
            $blacklist = array_filter(array_map('trim', $bl));
        } else {
            $blacklist = [];
        }
        foreach ($_POST['blacklist'] as $username) {
            $blacklist[] = $username;
        }
        $Settings->set('ldap-sync-blacklist', implode(',', $blacklist));
        echo "<p>Blacklist updated.</p>";
    }

    echo "User synchronization successful";
    include BASEPATH . "/footer.php";
});


/** 
 * CRUD routes
 */

Route::post('/crud/users/update/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    $values = $_POST['values'];
    $values = validateValues($values, $DB);
    // separate personal and account information
    $person = $values;
    // $account = [];
    // get old value for rendering
    $old = $DB->getPerson($user);

    // update name information
    if (isset($values['last']) && isset($values['first'])) {

        $person['displayname'] = "$values[first] $values[last]";
        $person['formalname'] = "$values[last], $values[first]";
        $person['first_abbr'] = "";
        foreach (explode(" ", $values['first']) as $name) {
            $person['first_abbr'] .= " " . $name[0] . ".";
        }

        // only update public visibility if complete form (user edit) is submitted
        // name is indicating that
        foreach (["public_image", "public_email", "public_phone", "hide"] as $key) {
            $person[$key] = boolval($values[$key] ?? false);
        }
    }


    if (isset($values['cv'])) {
        $cv = $values['cv'];
        foreach ($values['cv'] as $key => $entry) {
            // add time text to entry
            $fromto = $entry['from']['month'] . '/' . $entry['from']['year'];
            $fromto .= " - ";
            if (empty($entry['to']['year'])) {
                $fromto .= "Current";
            } else {
                if (!empty($entry['to']['month'])) {
                    $fromto .= $entry['to']['month'] . '/';
                }
                $fromto .= $entry['to']['year'];
            }
            $cv[$key]['time'] = $fromto;
        }
        // sort cv descending
        usort($cv, function ($a, $b) {
            $a = $a['from']['year'] . '.' . $a['from']['month'];
            $b = $b['from']['year'] . '.' . $b['from']['month'];
            return strnatcmp($b, $a);
        });
        $person['cv'] = $cv;
    }

    $updateResult = $osiris->persons->updateOne(
        ['username' => $user],
        ['$set' => $person]
    );

    if (isset($person['hide'])) {
        // check if hide value changed

        if ($old['hide'] != $person['hide']) {
            // rerender all activities
            include_once BASEPATH . "/php/Render.php";
            renderActivities(['authors.user' => $user]);
        }
    }

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount()
    ]);
});


Route::post('/crud/users/delete/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";


    $data = $DB->getPerson($user);

    $keep = [
        '_id',
        'displayname',
        'formalname',
        'first_abbr',
        'updated',
        'updated_by',
        "academic_title",
        "first",
        "last",
        "name",
        "depts",
        "username"
    ];
    $arr = [];
    foreach ($data as $key => $value) {
        if (in_array($key, $keep)) continue;
        $arr[$key] = null;
    }
    $arr['is_active'] = false;
    $updateResult = $osiris->persons->updateOne(
        ['username' => $user],
        ['$set' => $arr]
    );



    if (file_exists(BASEPATH . "/img/users/$user.jpg")) {
        unlink(BASEPATH . "/img/users/$user.jpg");
    }

    header("Location: " . ROOTPATH . "/profile/" . $user . "?msg=user-inactivated");
    die();
});


/**
 * Update profile picture
 */
Route::post('/crud/users/profile-picture/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    $target_dir = BASEPATH . "/img/users";
    if (!is_writable($target_dir)) {
        die("User image directory is unwritable. Please contact admin.");
    }
    $target_dir .= "/";
    $filename = "$user.jpg";

    if (isset($_FILES["file"])) {
        // if ($_FILES['file']['type'] != 'image/jpeg') die('Wrong extension, only JPEG is allowed.');

        if ($_FILES['file']['error'] != UPLOAD_ERR_OK) {
            $errorMsg = match ($_FILES['file']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            printMsg($errorMsg, "error");
        } else if ($_FILES["file"]["size"] > 2000000) {
            printMsg(lang("File is too big: max 2 MB is allowed.", "Die Datei ist zu groß: maximal 2 MB sind erlaubt."), "error");
            // } else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
            //     header("Location: " . ROOTPATH . "/profile/$user?msg=success");
            //     die;
        } else {
            // check image settings
            if ($Settings->featureEnabled('db_pictures')) {
                $img = new MongoDB\BSON\Binary(file_get_contents($_FILES["file"]["tmp_name"]), MongoDB\BSON\Binary::TYPE_GENERIC);
                // first: delete old image, then: insert new one
                $filetype = 'jpg';
                $osiris->userImages->deleteOne(['user' => $user]);
                $updateResult = $osiris->userImages->insertOne([
                    'user' => $user,
                    'img' => $img,
                    'ext' => $filetype
                ]);
            } else {
                // upload to file system
                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
                    header("Location: " . ROOTPATH . "/profile/$user?msg=success");
                    die;
                }
            }
            header("Location: " . ROOTPATH . "/profile/$user?msg=success");
            die;
            // printMsg(lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload."), "error");
        }
    } else if (isset($_POST['delete'])) {
        // $filename = "$user.jpg";
        $osiris->userImages->deleteOne(['user' => $user]);
        // if (file_exists($target_dir . $filename)) {
        //     // Use unlink() function to delete a file
        //     if (!unlink($target_dir . $filename)) {
        //         printMsg("$filename cannot be deleted due to an error.", "error");
        //     } else {
        header("Location: " . ROOTPATH . "/profile/$user?msg=deleted");
        die;
        //     }
        // }
        // printMsg("File has been deleted from the database.", "success");
    }
});


Route::post('/crud/users/update-expertise/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    $values = $_POST['values'];
    $values = validateValues($values, $DB);

    $updateResult = $osiris->persons->updateOne(
        ['username' => $user],
        ['$set' => $values]
    );

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount()
    ]);
});


Route::post('/crud/users/approve', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    if (!isset($_POST['quarter'])) {
        echo "Quarter was not defined";
        die();
    }
    $q = $_POST['quarter'];

    $updateResult = $osiris->persons->updateOne(
        ['username' => $user],
        ['$push' => ["approved" => $q]]
    );

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=approved");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount()
    ]);
});



Route::post('/crud/queries', function () {
    include_once BASEPATH . "/php/init.php";
    // name: name,
    // rules: rules,
    // user: $_SESSION['username'] 
    // created: new Date(),
    // aggregate: $('#aggregate').val()
    if (isset($_POST['id'])) {
        // delete query with _id
        $deleteResult = $osiris->queries->deleteOne(['_id' => DB::to_ObjectID($_POST['id'])]);
        return $deleteResult->getDeletedCount();
        die;
    }
    if (!isset($_POST['name'])) die("no name given");
    if (!isset($_POST['rules'])) die("no rules given");
    if (!isset($_SESSION['username'])) die("no user given");
    $updateResult = $osiris->queries->insertOne([
        'name' => $_POST['name'],
        'rules' => json_encode($_POST['rules']),
        'user' => $_SESSION['username'],
        'created' => date('Y-m-d'),
        'aggregate' => $_POST['aggregate'] ?? null
    ]);
    return $updateResult->getInsertedId();
});
