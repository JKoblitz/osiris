<?php

// include_once BASEPATH . "/php/_db.php";
include_once BASEPATH . "/php/_config.php";
function validateValues($values)
{
    include_once BASEPATH . "/php/_db.php";
    $first = max(intval($values['first_authors'] ?? 1), 1);
    unset($values['first_authors']);
    $last = max(intval($values['last_authors'] ?? 1), 1);
    unset($values['last_authors']);

    foreach ($values as $key => $value) {
        if ($key == 'doi') {
            if (!str_contains($value, '10.')) $value = null;
            elseif (!str_starts_with($value, '10.')) {
                $value = explode('10.', $value, 2);
                $values[$key] = "10." . $value[1];
            }
            // dump($value);
            // die;
        } else if ($key == 'authors' || $key == "editors") {
            $values[$key] = array();
            foreach ($value as $i => $author) {
                $author = explode(';', $author, 3);
                $user = getUserFromName($author[0], $author[1]);
                if ($key == "editors") {
                    $values[$key][] = [
                        'last' => $author[0],
                        'first' => $author[1],
                        'aoi' => $author[2],
                        'user' => $user,
                        'approved' => $user == $_SESSION['username']
                    ];
                } else {
                    if ($i < $first) {
                        $pos = 'first';
                    } elseif ($i + $last >= count($value)) {
                        $pos = 'last';
                    } else {
                        $pos = 'middle';
                    }
                    $values[$key][] = [
                        'last' => $author[0],
                        'first' => $author[1],
                        'aoi' => $author[2],
                        'position' => $pos,
                        'user' => $user,
                        'approved' => $user == $_SESSION['username']
                    ];
                }
            }
        } else if ($key == 'user') {
            $user = getUserFromId($value);
            $values["authors"] = [
                [
                    'last' => $user['last'],
                    'first' => $user['first'],
                    'aoi' => true,
                    'user' => $value,
                    'approved' => $value == $_SESSION['username']
                ]
            ];
        } else if (is_array($value)) {
            $values[$key] = validateValues($value);
        } else if ($key == 'issn') {
            if (empty($value)){
                $values[$key] = array();
            } else {
                $values[$key] = explode(' ',$value);
            }
        } else if ($value === 'true') {
            $values[$key] = true;
        } else if ($value === 'false') {
            $values[$key] = false;
        } else if ($key == 'invited_lecture' || $key == 'open_access') {
            $values[$key] = boolval($value);
        } else if (in_array($key, ['aoi', 'epub', 'correction'])) {
            // dump($value);
            // $values[$key] = boolval($value);
            $values[$key] = true;
        } else if ($value === '') {
            $values[$key] = null;
        } else if ($key === 'epub-delay' || $key === 'end-delay') {
            // will be converted otherwise
            $values[$key] = endOfCurrentQuarter(true);
        } else if ($key == 'start' || $key == 'end' || DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
            // $values[$key] = mongo_date($value);
            $values[$key] = valiDate($value);
            if (!isset($values['year']) && isset($values[$key]['year'])) {
                $values['year'] = $values[$key]['year'];
            }
            if (!isset($values['month']) && isset($values[$key]['month'])) {
                $values['month'] = $values[$key]['month'];
            }
        } else if (is_numeric($value)) {
            if (str_starts_with($value, "0")){
                $values[$key] = trim($value);
            } elseif (is_float($value + 0)) {
                $values[$key] = floatval($value);
            } else {
                $values[$key] = intval($value);
            }
        } else if (is_string($value)) {
            $values[$key] = trim($value);
        }
    }

    if (isset($values['journal']) && !isset($values['role'])) {
        // it is an article
        // since non-checked boxes are not shown in the posted data,
        // it is necessary to get false values
        if (!isset($values['epub'])) $values['epub'] = false;
        if (!isset($values['open_access'])) $values['open_access'] = false;
        if (!isset($values['correction'])) $values['correction'] = false;
    }

    if (isset($values['year']) && ($values['year'] < 1900 || $values['year'] > (CURRENTYEAR ?? 2055) + 5)) {
        echo "The year $values[year] is not valid!";
        die();
    }
    // dump($values, true);
    // die();
    return $values;
}

function valiDate($date)
{
    if (empty($date)) return null;
    $t = explode('-', $date, 3);
    return array(
        'year' => intval($t[0]),
        'month' => intval($t[1] ?? 1),
        'day' => intval($t[2] ?? 1),
    );
}

Route::get('/mongo', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    // $collection = $osiris->publications;
    // $document = $collection->findOne(['_id' => 21768]);
    // var_dump($document);
    echo "<div id='result'></div>";
    echo "<script src='" . ROOTPATH . "/js/osiris.js'></script>";
    include BASEPATH . "/footer.php";
});



Route::get('/test/(users|activities|journals)/([a-zA-Z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    if ($col == 'users') {
        $collection = $osiris->users;
    } elseif ($col == 'journals') {
        $collection = $osiris->journals;
    } else {
        $collection = $osiris->activities;
    }

    if (is_numeric($id)) {
        $id = intval($id);
    } else if ($col != 'users') {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    $data = $collection->findone(['_id' => $id]);
    dump($data, true);
});

Route::get('/testing', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $names = [
        ['Nubel', 'Nübel', 'uln14'],
        ['Ozturk', 'Öztürk', 'bas18'],
        ['Goker', 'Göker', 'mgo08'],
        ['Sproer', 'Spröer', 'ckc'],
        ['Pauker', 'Päuker', 'opa'],
        ['Steenpass', 'Steenpaß', 'las20'],
        ['Carbasse', 'Sardà Carbasse', 'joc18'],
    ];

    foreach ($names as $n) {
        # code...
        $updateResult = $osiris->activities->updateMany(
            ['authors.last' => $n[0]],
            ['$set' => ['authors.$.last' => $n[1], 'authors.$.user' => $n[2]]]
        );
        echo $n[0] . " " . $updateResult->getModifiedCount() . "<br>";
    }

    // $updateResult = $osiris->users->updateMany(
    //     ['unit' => new MongoDB\BSON\Regex('bis.*')],
    //     ['$set' => ['is_active'=>false]]
    // );
    // echo "All ". $updateResult->getModifiedCount()."<br>";

    // $updateResult = $osiris->users->updateMany(
    //     ['unit' => new MongoDB\BSON\Regex('Abmeldung')],
    //     ['$set' => ['is_active'=>false]]
    // );
    // echo "All ". $updateResult->getModifiedCount()."<br>";

    $updateResult = $osiris->users->updateMany(
        ['is_controlling' => 1],
        ['$set' => ['is_controlling' => true]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_scientist' => 1],
        ['$set' => ['is_scientist' => true]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_leader' => 1],
        ['$set' => ['is_leader' => true]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_active' => 1],
        ['$set' => ['is_active' => true]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_controlling' => 0],
        ['$set' => ['is_controlling' => false]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_scientist' => 0],
        ['$set' => ['is_scientist' => false]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_leader' => 0],
        ['$set' => ['is_leader' => false]]
    );
    $updateResult = $osiris->users->updateMany(
        ['is_active' => 0],
        ['$set' => ['is_active' => false]]
    );
    // echo "All " . $updateResult->getModifiedCount() . "<br>";

    // $updateResult = $osiris->activities->updateMany(
    //     ['authors.last' => 'Ozturk'],
    //     ['$set' => ['authors.$.last'=>'Öztürk', 'authors.$.user'=>'bas18']]
    // );
    // addUserActivity();
    // $collection = $osiris->users;
    // $data = $collection->findone(['_id'=> $_SESSION['username']]);
    // dump($data, true);

});


Route::post('/create', function () {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    // dump($_POST);
    // die();
    $collection = $osiris->activities;
    $required = [];
    $col = $_POST['values']['type'];
    switch ($col) {
        case 'lecture':
            break;
        case 'misc':
            break;
        case 'poster':
            break;
        case 'publication':
            $required = ['title', 'year', 'month'];
            break;
        case 'students':
            $required = ['title', 'category', 'name', 'affiliation', 'start', 'end'];
            break;
        case 'review':
            $required = ['role', 'user'];
            break;
        default:
            // echo "unsupported collection";
            break;
    }


    $values = validateValues($_POST['values']);

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = $_SESSION['username'];

    if (isset($values['doi']) && !empty($values['doi'])) {
        $doi_exist = $collection->findOne(['doi' => $values['doi']]);
        if (!empty($doi_exist)) {
            header("Location: " . ROOTPATH . "/activities/view/$doi_exist[_id]?msg=DOI+already+exists");
            die;
        }
    }
    if (isset($values['pubmed']) && !empty($values['pubmed'])) {
        $pubmed_exist = $collection->findOne(['pubmed' => $values['pubmed']]);
        if (!empty($pubmed_exist)) {
            header("Location: " . ROOTPATH . "/activities/view/$pubmed_exist[_id]?msg=Pubmed-ID+already+exists");
            die;
        }
    }


    // if (isset($_FILES["file"]) && $_FILES["file"]['error'] == 0) {
    //     $filecontent = file_get_contents($_FILES["file"]['tmp_name']);

    //     $values['file'] = new MongoDB\BSON\Binary($filecontent, MongoDB\BSON\Binary::TYPE_GENERIC);
    // }
    foreach ($required as $req) {
        if (!isset($values[$req]) || empty($values[$req])) {
            echo "$req is required";
            die;
        }
    }

    // dump($values, true);
    // die();

    $insertOneResult  = $collection->insertOne($values);
    $id = $insertOneResult->getInsertedId();

    addUserActivity('create');

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=add-success");
        die();
    }
    include_once BASEPATH . "/php/format.php";
    $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        // 'result' => format($col, $result)
    ]);
});

// Route::post('/upload-file/([A-Za-z0-9]*)', function ($id) {

//     if (isset($_FILES["file"]) && $_FILES["file"]['error'] == 0) {
//         $filecontent = file_get_contents($_FILES["file"]['tmp_name']);

//         $values['file'] = new MongoDB\BSON\Binary($filecontent, MongoDB\BSON\Binary::TYPE_GENERIC);
//     }

//     if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
//         $red = str_replace("*", $id, $_POST['redirect']);
//         header("Location: " . $red . "?msg=add-success");
//         die();
//     }
// });

Route::post('/update/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $collection = $osiris->activities;
    $values = validateValues($_POST['values']);

    if (isset($_FILES["file"]) && $_FILES["file"]['error'] == 0) {
        $filecontent = file_get_contents($_FILES["file"]['tmp_name']);

        $values['file'] = new MongoDB\BSON\Binary($filecontent, MongoDB\BSON\Binary::TYPE_GENERIC);
    }

    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = $_SESSION['username'];

    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    addUserActivity('update');
    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount(),
        'result' => $collection->findOne(['_id' => $id])
    ]);
});


Route::post('/update-user/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }

    $values = $_POST['values'];


    $collection = $osiris->users;
    $values = validateValues($values);

    // dump($values, true);
    // die;

    $values['is_controlling'] = boolval($values['is_controlling'] ?? false);
    $values['is_scientist'] = boolval($values['is_scientist'] ?? false);
    $values['is_leader'] = boolval($values['is_leader'] ?? false);
    $values['is_active'] = boolval($values['is_active'] ?? false);
    // if (!isset($values['is_controlling'])) $values['is_controlling'] = false;
    // if (!isset($values['is_scientist'])) $values['is_scientist'] = false;
    // if (!isset($values['is_leader'])) $values['is_leader'] = false;
    // if (!isset($values['is_active'])) $values['is_active'] = false;

    if (isset($values['last']) && isset($values['first']))
        $values['displayname'] = "$values[first] $values[last]";
    $values['formalname'] = "$values[last], $values[first]";
    $values['first_abbr'] = "";
    foreach (explode(" ", $values['first']) as $name) {
        $values['first_abbr'] .= " " . $name[0] . ".";
    }

    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = $_SESSION['username'];


    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount(),
        'result' => $collection->findOne(['_id' => $id])
    ]);
});


Route::post('/delete/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    // select the right collection
    // $collection = get_collection($col);

    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    $updateResult = $osiris->activities->deleteOne(
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

Route::post('/update-authors/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    // prepare id
    if (!isset($_POST['authors']) || empty($_POST['authors'])) {
        echo "Error: Author list cannot be empty.";
        die();
    }
    $id = new MongoDB\BSON\ObjectId($id);

    $osiris->activities->updateOne(
        ['_id' => $id],
        ['$set' => ["authors" => $_POST['authors']]]
    );

    header("Location: " . ROOTPATH . "/activities/view/$id?msg=update-success");
});

Route::post('/approve-all', function () {
    include_once BASEPATH . "/php/_db.php";
    // prepare id
    $user = $_POST['user'] ?? $_SESSION['username'];

    $osiris->activities->updateMany(
        ['authors.user' => $user],
        ['$set' => ["authors.$.approved" => true]]
    );

    header("Location: " . ROOTPATH . "/issues?msg=update-success");
});

// Route::post('/update/delay-epub/([A-Za-z0-9]*)', function ($id) {
//     include_once BASEPATH . "/php/_db.php";

//     $id = new MongoDB\BSON\ObjectId($id);
//     $updateResult = $osiris->activities->updateOne(
//         ['_id' => $id],
//         ['$set' => ["epub-delay" => date('Y-m-d')]]
//     );

//     if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
//         header("Location: " . $_POST['redirect'] . "?msg=Epub+delayed");
//         die();
//     }
//     echo json_encode([
//         'updated' => $updateResult->getModifiedCount(),
//         'result' => $collection->findOne(['_id' => $id])
//     ]);
// });

Route::post('/push-dates/misc/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    // prepare values and id
    $values = validateValues($_POST['values']);
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    // get current iteration
    $collection = $osiris->miscs;
    $current = $collection->findOne(['_id' => $id]);
    if ($current['iteration'] == 'once') {
        $updateResult = $collection->updateOne(
            ['_id' => $id],
            ['$push' => ["dates" => $values]]
        );
    } else {
        $updateResult = $collection->updateOne(
            ['_id' => $id],
            ['$set' => ["dates" => [$values]]]
        );
    }

    $updateCount = $updateResult->getModifiedCount();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateCount
    ]);
});


Route::post('/push-dates/review/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    // prepare values and id
    $values = validateValues($_POST['values']);
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    // get current iteration
    $collection = $osiris->reviews;
    // $current = $collection->findOne(['_id' => $id]);
    if (isset($values['dates'])) {
        $updateResult = $collection->updateOne(
            ['_id' => $id],
            ['$push' => ["dates" => $values['dates']]]
        );
    } else {
        $updateResult = $collection->updateOne(
            ['_id' => $id],
            ['$set' => $values]
        );
    }

    $updateCount = $updateResult->getModifiedCount();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateCount
    ]);
});


Route::post('/add-repetition/lecture/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['date'])) {
        echo "no date given";
        die;
    }
    // prepare values and id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    // get current iteration
    $collection = $osiris->lectures;
    $duplicate = $collection->findOne(['_id' => $id]);
    if (empty($duplicate)) {
        echo "element was not found.";
        die;
    }

    unset($duplicate['_id']);
    $duplicate['start'] = valiDate($_POST['date']);
    $duplicate['lecture_type'] = 'repetition';
    // echo json_encode($duplicate);
    // die;
    $insertOneResult  = $collection->insertOne($duplicate);

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=add-success");
        die();
    }
    $id = $insertOneResult->getInsertedId();
    include_once BASEPATH . "/php/format.php";
    $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        'result' => format('lecture', $result)
    ]);
});

Route::post('/approve', function () {
    include_once BASEPATH . "/php/_db.php";
    $id = $_SESSION['username'];
    if (!isset($_POST['quarter'])) {
        echo "Quarter was not defined";
        die();
    }
    $q = $_POST['quarter']; //SELECTEDYEAR . "Q" . SELECTEDQUARTER;

    $updateResult = $osiris->users->updateOne(
        ['_id' => $id],
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


Route::get('/form/(lecture|misc|poster|publication|students)/([A-Za-z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_db.php";

    $collection = get_collection($col);
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
    $url = ROOTPATH . "/$col";
    $form = $collection->findOne(['_id' => $id]);
    include BASEPATH . "/components/form-$col.php";
});

Route::get('/form/user/([A-Za-z0-9]*)', function ($user) {
    include_once BASEPATH . "/php/_db.php";
    $data = getUserFromId($user);
    include BASEPATH . "/pages/editor/user.php";
});



Route::post('/approve/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";

    $collection = $osiris->activities;
    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
    $approval = intval($_POST['approval'] ?? 0);
    $filter = ['_id' => $id, "authors.user" => $_SESSION['username']];

    switch ($approval) {
        case 1:
            # Yes, this is me and I was affiliated to the AFFILIATION
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.approved" => true, 'authors.$.aoi' => true]]
            );
            break;
        case 2:
            # Yes, but I was not affiliated to the AFFILIATION
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.approved" => true, 'authors.$.aoi' => false]]
            );
            break;
        case 3:
            # No, this is not me
            $updateResult = $collection->updateOne(
                $filter,
                ['$set' => ["authors.$.user" => null, 'authors.$.aoi' => false]]
            );
            break;
        default:
            # code...
            break;
    }



    $updateCount = $updateResult->getModifiedCount();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateCount
    ]);
});
