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
        $values['created_by'] = $_SESSION['username'];

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


/**
 * Cancel guest 
 */
Route::post('/guests/cancel/([a-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->guests;

    $cancel = boolval($_POST['cancel'] ?? true);

    if ($cancel){
        $values = [
            'cancelled' => true,
            'cancelled_by' => $_SESSION['username'],
            'cancelled_date' => date('Y-m-d')
        ];
    } else {
        $values = [
            'cancelled' => false,
            'cancelled_by' => null,
            'cancelled_date' => null
        ];
    }
    $collection->updateOne(
        ['id' => $id],
        ['$set' => $values]
    );

    header("Location: " . ROOTPATH . "/guests/view/$id");
}, 'login');



Route::post('/guests/upload-files/(.*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $target_dir = BASEPATH . "/uploads/";
    if (!is_writable($target_dir)) {
        die("Upload directory $target_dir is unwritable. Please contact admin.");
    }
    $target_dir .= "$id/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777);
        echo "<!-- The directory $target_dir was successfully created.-->";
    } else {
        echo "<!-- The directory $target_dir exists.-->";
    }


    if (isset($_FILES["file"])) {

        $filename = htmlspecialchars(basename($_FILES["file"]["name"]));
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["file"]["size"];
        $filepath = ROOTPATH . "/uploads/$id/$filename";

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
        } else if ($filesize > 16000000) {
            printMsg(lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."), "error");
        } else if (file_exists($target_dir . $filename)) {
            printMsg(lang("Sorry, file already exists.", "Die Datei existiert bereits. Um sie zu überschreiben, muss sie zunächst gelöscht werden."), "error");
        } else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
            printMsg(lang("The file $filename has been uploaded.", "Die Datei <q>$filename</q> wurde hochgeladen."), "success");
            $values = [
                "filename" => $filename,
                "filetype" => $filetype,
                "filesize" => $filesize,
                "filepath" => $filepath,
            ];

            $osiris->guests->updateOne(
                ['id' => $id],
                ['$push' => ["files" => $values]]
            );
            // $files[] = $values;
        } else {
            printMsg(lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload."), "error");
        }
    
        header("Location: " . ROOTPATH . "/guests/view/" . $id . "?msg=upload-successful");
        die();
    } else if (isset($_POST['delete'])) {
        $filename = $_POST['delete'];
        if (file_exists($target_dir . $filename)) {
            // Use unlink() function to delete a file
            if (!unlink($target_dir . $filename)) {
                printMsg("$filename cannot be deleted due to an error.", "error");
            } else {
                printMsg(lang("$filename has been deleted.", "$filename wurde gelöscht."), "success");
            }
        }

        $osiris->guests->updateOne(
            ['id' => $id],
            ['$pull' => ["files" => ["filename" => $filename]]]
        );
        // printMsg("File has been deleted from the database.", "success");

        header("Location: " . ROOTPATH . "/guests/view/" . $id . "?msg=file-deleted-successfully");
        die();
    }
});

