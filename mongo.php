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
            $i = 0;
            foreach ($value as $author) {
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
                $i++;
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
            if (empty($value)) {
                $values[$key] = array();
            } else {
                $values[$key] = explode(' ', $value);
                $values[$key] = array_unique($values[$key]);
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
            // dump($key);
            // dump($value);
            // die;
            if (str_starts_with($value, "0")) {
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

    if (isset($values['journal']) && !isset($values['role']) && isset($values['year'])) {
        // it is an article
        // since non-checked boxes are not shown in the posted data,
        // it is necessary to get false values
        if (!isset($values['epub'])) $values['epub'] = false;
        else $values['epub-delay'] = endOfCurrentQuarter(true);
        if (!isset($values['open_access']) || !$values['open_access']) {
            $values['open_access'] = get_oa($values);
        }
        if (!isset($values['correction'])) $values['correction'] = false;

        $values['impact'] = get_impact($values);
    }
    if (($values['type'] ?? '') == 'misc' && ($values['iteration'] ?? '') == 'annual') {
        $values['end-delay'] = endOfCurrentQuarter(true);
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
    $values['created_by'] = strtolower($_SESSION['username']);

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

    // addUserActivity('create');

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=add-success");
        die();
    }
    // include_once BASEPATH . "/php/format.php";
    // $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        // 'result' => format($col, $result)
    ]);
});


Route::post('/create-journal', function () {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $collection = $osiris->journals;

    $values = validateValues($_POST['values']);
    $values['impact'] = [];

    $values['abbr'] = $values['abbr'] ?? $values['journal'];

    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = $_SESSION['username'];

    // check if issn already exists:
    if (isset($values['issn']) && !empty($values['issn'])) {
        $issn_exist = $collection->findOne(['issn' => ['$in' => $values['issn']]]);
        if (!empty($issn_exist)) {
            echo json_encode([
                'msg' => "ISSN already existed",
                'id' => $issn_exist['_id'],
                'journal' => $issn_exist['journal'],
                'issn' => $issn_exist['issn'],
            ]);
            die;
        }
    }

    // try to get oa information from DOAJ
    $name = $values['journal'];
    $oa = $values['oa'] ?? false;
    $issn = $values['issn'];
    $query = [];
    foreach ($issn as $n => $i) {
        if (empty($i)) {
            unset($values['issn'][$n]);
            continue;
        }

        $query[] = "issn:" . $i;
    }
    if ($oa === false && !empty($query)) {
        $query = implode(' OR ', $query);
        $url = "https://doaj.org/api/search/journals/" . $query;
        $response = CallAPI('GET', $url);
        $json = json_decode($response, true);

        if (!empty($json['results'] ?? null) && isset($json['results'][0]['bibjson'])) {
            $n = count($json['results']);
            $index = 0;
            if ($n > 1) {
                $compare_name = strtolower($name);
                $compare_name = explode('(', $compare_name)[0];
                $compare_name = trim($compare_name);
                foreach ($json['results'] as $i => $res) {
                    if (isset($res['bibjson']['is_replaced_by'])) continue;
                    $index = $i;
                    if (strtolower($res['bibjson']['title']) == $compare_name) {
                        break;
                    }
                }
            }
            $r = $json['results'][$index]['bibjson'];
            $oa = $r['oa_start'] ?? false;
        }
    }
    $values['oa'] = $oa;

    try {
        // try to get impact factor from WoS Journal info
        include_once BASEPATH . "/php/simple_html_dom.php";
 
        require_once BASEPATH . '/php/Settings.php';
        $Settings = new Settings();
        $settings = $Settings['api'];
        // $settings = file_get_contents(BASEPATH . "/apis.json");
        // $settings = json_decode($settings, true, 512, JSON_NUMERIC_CHECK);
        foreach ($settings as $api) {
            if ($api['id'] == 'wos-journal.info') {
                $YEAR = $api['year'] ?? 2021;

                $html = new simple_html_dom();
                foreach ($values['issn'] as $i) {
                    if (empty($i)) continue;
                    $url = 'https://wos-journal.info/?jsearch=' . $i;
                    $html->load_file($url);
                    foreach ($html->find("div.row") as $row) {
                        $el = $row->plaintext;
                        if (preg_match('/Impact Factor \(IF\):\s+(\d+\.?\d*)/', $el, $match)) {
                            $values['impact'] = [['year' => $YEAR, 'impact' => floatval($match[1])]];
                            break 2;
                        }
                    }
                }
            }

            if ($api['id'] == 'wos-starter') {
                $apikey = $api['key'];
                if (empty($apikey)) {
                    continue;
                }
                foreach ($values['issn'] as $i) {
                    if (empty($i)) continue;


                    $url = "https://api.clarivate.com/apis/wos-starter/v1/journals";
                    $url .= "?issn=" . $i;

                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_HTTPHEADER, [
                        'Accept: application/json',
                        "X-ApiKey: $apikey"
                    ]);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($curl);
                    $result = json_decode($result, true);
                    if (!empty($result['hits'])) {
                        $values['wos'] = $result['hits'][0];
                    }
                }
            }
        }
    } catch (\Throwable $th) {
    }


    $insertOneResult  = $collection->insertOne($values);
    $id = $insertOneResult->getInsertedId();

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=success");
        die();
    }

    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
    ]);
    // $result = $collection->findOne(['_id' => $id]);
});


Route::post('/update/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";
    if (!isset($_POST['values'])) {
        echo "no values given";
        die;
    }
    $collection = $osiris->activities;
    $values = validateValues($_POST['values']);

    if (isset($_POST['minor']) && $_POST['minor'] == 1) {
        unset($values['authors']);
        unset($values['editors']);
    }

    // add information on updating process
    $values['updated'] = date('Y-m-d');
    $values['updated_by'] = strtolower($_SESSION['username']);

    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );
    
    cleanFields($id);
    // die;

    // addUserActivity('update');
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
    $values = validateValues($values);

    $collection = $osiris->users;



    if (isset($values['last']) && isset($values['first'])) {
        $values['is_controlling'] = boolval($values['is_controlling'] ?? false);
        $values['is_scientist'] = boolval($values['is_scientist'] ?? false);
        $values['is_leader'] = boolval($values['is_leader'] ?? false);
        $values['is_active'] = boolval($values['is_active'] ?? false);

        $values['displayname'] = "$values[first] $values[last]";
        $values['formalname'] = "$values[last], $values[first]";
        $values['first_abbr'] = "";
        foreach (explode(" ", $values['first']) as $name) {
            $values['first_abbr'] .= " " . $name[0] . ".";
        }
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


Route::post('/update-journal/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_db.php";

    $values = $_POST['values'];
    $values = validateValues($values);

    $collection = $osiris->journals;
    $mongoid = new MongoDB\BSON\ObjectId($id);

    if (isset($values['year'])) {
        $year = intval($values['year']);
        $if = $values['if'] ?? null;

        // remove existing year
        $updateResult = $collection->updateOne(
            ['_id' => $mongoid, 'impact.year' => ['$exists' => true]],
            ['$pull' => ['impact' => ['year' => $year]]]
        );
        if (empty($if)) {
            // do nothing more
        } else {
            // add new impact factor
            try {
                $updateResult = $collection->updateOne(
                    ['_id' => $mongoid],
                    ['$push' => ['impact' => ['year' => $year, 'impact' => $if]]]
                );
            } catch (MongoDB\Driver\Exception\BulkWriteException $th) {
                $updateResult = $collection->updateOne(
                    ['_id' => $mongoid],
                    ['$set' => ['impact' => [['year' => $year, 'impact' => $if]]]]
                );
            }

            // dump([$values, $updateResult], true);
            // die;
        }
    } else {

        // // add information on updating process
        $values['updated'] = date('Y-m-d');
        $values['updated_by'] = $_SESSION['username'];

        if (isset($values['oa']) && $values['oa'] !== false) {
            $updateResult = $osiris->activities->updateMany(
                ['journal_id' => $id, 'year' => ['$gt' => $values['oa']]],
                ['$set' => ['open_access' => true]]
            );
        }


        $updateResult = $collection->updateOne(
            ['_id' => $mongoid],
            ['$set' => $values]
        );
    }

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

    $authors = [];
    foreach ($_POST['authors'] as $i => $a) {
        $authors[] = [
            'last' => $a['last'],
            'first' => $a['first'],
            'position' => $a['position'] ?? 'middle',
            'aoi' => (boolval($a['aoi'] ?? false)),
            //|| ($_SESSION['username'] == $a['user'] ?? '')
            'user' => empty($a['user']) ? null : $a['user'],
            'approved' => boolval($a['approved'] ?? false),
        ];
    }

    $osiris->activities->updateOne(
        ['_id' => $id],
        ['$set' => ["authors" => $authors]]
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

Route::post('/approve', function () {
    include_once BASEPATH . "/php/_db.php";
    $id = $_SESSION['username'];
    if (!isset($_POST['quarter'])) {
        echo "Quarter was not defined";
        die();
    }
    $q = $_POST['quarter']; 
    
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


Route::get('/form/user/([A-Za-z0-9]*)', function ($user) {
    include_once BASEPATH . "/php/_db.php";
    $data = getUserFromId($user);
    include BASEPATH . "/pages/user-editor.php";
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
