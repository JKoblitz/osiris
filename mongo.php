<?php

// include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/_config.php";
function validateValues($values)
{
    include_once BASEPATH . "/php/_db.php";
    global $osiris;
    var_dump($values);
    $first = max(intval($values['first_authors'] ?? 1), 1);
    unset($values['first_authors']);
    $last = max(intval($values['last_authors'] ?? 1), 1);
    unset($values['last_authors']);

    foreach ($values as $key => $value) {
        if ($key == 'authors' || $key == "editors") {
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
        } else if (is_array($value)) {
            $values[$key] = validateValues($value);
        } else if ($value === 'true') {
            $values[$key] = true;
        } else if ($value === 'false') {
            $values[$key] = false;
        } else if (in_array($key, ['aoi', 'epub', 'correction', 'open_access', 'presenting'])) {
            $values[$key] = boolval($value);
        } else if ($value === '') {
            $values[$key] = null;
        } else if ($key == 'start' || $key == 'end' || DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
            // $values[$key] = mongo_date($value);
            $values[$key] = valiDate($value);
        } else if (is_numeric($value)) {
            $values[$key] = intval($value);
        } else if (is_float($value)) {
            $values[$key] = floatval($value);
        } else if (is_string($value)){
            $values[$key] = trim($value);
        }
    }
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


Route::post('/create/(lecture|misc|poster|publication|teaching|review)', function ($col) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    // dump($_POST);
    // die();
    switch ($col) {
        case 'lecture':
            $collection = $osiris->lectures;
            $required = [];
            break;
        case 'misc':
            $collection = $osiris->miscs;
            $required = [];
            break;
        case 'poster':
            $collection = $osiris->posters;
            $required = [];
            break;
        case 'publication':
            $collection = $osiris->publications;
            $required = ['title', 'year', 'month'];
            break;
        case 'teaching':
            $collection = $osiris->teachings;
            $required = ['title', 'category', 'name', 'affiliation', 'start', 'end'];
            break;
        case 'review':
            $collection = $osiris->reviews;
            $required = ['role', 'user'];
            break;
        default:
            echo "unsupported collection";
            die;
    }
    $values = validateValues($_POST['values']);
    foreach ($required as $req) {
        if (!isset($values[$req]) || empty($values[$req])) {
            echo "$req is required";
            die;
        }
    }
    // echo json_encode($values);
    // die();

    $insertOneResult  = $collection->insertOne($values);

    $id = $insertOneResult->getInsertedId();

    // if (isset($values['authors'])) {
    //     // add entry to all assigned users
    //     $users = array_values(array_filter(array_column($values['authors'], 'user')));
    //     if (!empty($users)) {
    //         $usercoll = $osiris->users;
    //         $usercoll->updateMany(
    //             ["_id" => ['$in' => $users]],
    //             ['$push' => [$col . "s" => $id]]
    //         );
    //     }
    // }

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=add-success");
        die();
    }
    include_once BASEPATH . "/php/format.php";
    $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        'result' => format($col, $result)
    ]);
});


Route::post('/update/(lecture|misc|poster|publication|teaching)/([A-Za-z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $collection = get_collection($col);

    $values = validateValues($_POST['values']);
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
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


Route::post('/delete/(lecture|misc|poster|publication|teaching|review)/([A-Za-z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_db.php";
    // select the right collection
    $collection = get_collection($col);

    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    $updateResult = $collection->deleteOne(
        ['_id' => $id]
    );

    $deletedCount = $updateResult->getDeletedCount();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=deleted-" . $deletedCount);
        die();
    }
    echo json_encode([
        'deleted' => $deletedCount
    ]);
});


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
    $q = SELECTEDYEAR . "Q" . SELECTEDQUARTER;

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


Route::get('/form/(lecture|misc|poster|publication|teaching)/([A-Za-z0-9]*)', function ($col, $id) {
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



Route::post('/approve/(lecture|misc|poster|publication|teaching)/([A-Za-z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_db.php";

    $collection = get_collection($col);
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
