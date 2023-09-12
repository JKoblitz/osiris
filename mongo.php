<?php

/**
 * Routing for CRUD
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

function validateValues($values)
{
    include_once BASEPATH . "/php/init.php";
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
                if (is_array($author)) {
                    $author['approved'] = ($author['user'] ?? '') == $_SESSION['username'];
                    $values[$key][] = $author;
                    continue;
                }
                $author = explode(';', $author, 3);
                if (count($author) == 1) {
                    $user = $author[0];
                    $temp = $DB->getPerson($user);
                    $author = [$temp['last'], $temp['first'], true];
                } else {
                    $user = $DB->getUserFromName($author[0], $author[1]);
                }
                $vals = [
                    'last' => $author[0],
                    'first' => $author[1],
                    'aoi' => boolval($author[2]),
                    'user' => $user,
                    'approved' => $user == $_SESSION['username']
                ];
                if ($key == "editors") {
                    $values[$key][] = $vals;
                } else {
                    if ($i < $first) {
                        $pos = 'first';
                    } elseif ($i + $last >= count($value)) {
                        $pos = 'last';
                    } else {
                        $pos = 'middle';
                    }
                    $vals['position'] = $pos;
                    $values[$key][] = $vals;
                }
                $i++;
            }
        } else if ($key == 'sws') {
            foreach ($value as $i => $v) {
                $values['authors'][$i]['sws'] = $v;
            }
            unset($values['sws']);
        } else if ($key == 'user') {
            $user = $DB->getPerson($value);
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
        } else if ($key == 'oa_status') {
            $values['open_access'] = $value != 'closed';
        } else if (in_array($key, ['aoi', 'epub', 'correction'])) {
            // dump($value);
            // $values[$key] = boolval($value);
            $values[$key] = true;
        } else if ($value === '') {
            $values[$key] = null;
        } else if ($key === 'epub-delay' || $key === 'end-delay') {
            // will be converted otherwise
            $values[$key] = endOfCurrentQuarter(true);
        } else if ($key == 'start' || $key == 'end') {
            if (DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
                $values[$key] = valiDate($value);
                if ($key == 'start') {
                    if (!isset($values['year']) && isset($values[$key]['year'])) {
                        $values['year'] = $values[$key]['year'];
                    }
                    if (!isset($values['month']) && isset($values[$key]['month'])) {
                        $values['month'] = $values[$key]['month'];
                    }
                }
            } else {
                $values[$key] = null;
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
            $values['open_access'] = $DB->get_oa($values);
        }
        if (!isset($values['correction'])) $values['correction'] = false;

        $values['impact'] = $DB->get_impact($values);
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
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
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
            // $required = ['title', 'year', 'month'];
            break;
        case 'students':
            // $required = ['title', 'category', 'name', 'affiliation', 'start', 'end'];
            break;
        case 'review':
            // $required = ['role', 'user'];
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

        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['doi' => $values['doi']]);
    }
    if (isset($values['pubmed']) && !empty($values['pubmed'])) {
        $pubmed_exist = $collection->findOne(['pubmed' => $values['pubmed']]);
        if (!empty($pubmed_exist)) {
            header("Location: " . ROOTPATH . "/activities/view/$pubmed_exist[_id]?msg=Pubmed-ID+already+exists");
            die;
        }
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['pubmed' => $values['pubmed']]);
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

    $DB->renderActivities(['_id' => $id]);

    // addUserActivity('create');

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        $red = str_replace("*", $id, $_POST['redirect']);
        header("Location: " . $red . "?msg=add-success");
        die();
    }
    // include_once BASEPATH . "/php/Document.php";
    // $result = $collection->findOne(['_id' => $id]);
    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => $id,
        // 'result' => format($col, $result)
    ]);
});

Route::post('/create-teaching', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
    $collection = $osiris->teaching;

    $values = validateValues($_POST['values']);
    // add information on creating process
    $values['created'] = date('Y-m-d');
    $values['created_by'] = strtolower($_SESSION['username']);


    // check if module already exists:
    if (isset($values['module']) && !empty($values['module'])) {
        $module_exist = $collection->findOne(['module' => $values['module']]);
        if (!empty($module_exist)) {

            $updateResult = $collection->updateOne(
                ['_id' => $module_exist['_id']],
                ['$set' => $values]
            );
            // echo json_encode([
            //     'msg' => "module already existed",
            //     'id' => $module_exist['_id'],
            //     'journal' => $module_exist['journal'],
            //     'module' => $module_exist['module'],
            // ]);
            if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                $red = str_replace("*", $id, $_POST['redirect']);
                header("Location: " . $red . "?msg=updated");
                die();
            }
            die;
        }
    } else {
        echo json_encode([
            'msg' => "Module must be given"
        ]);
        die;
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
});

Route::post('/create-journal', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
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
    // $name = $values['journal'];
    // $oa = $values['oa'] ?? false;
    $issn = $values['issn'];
    // $query = [];
    foreach ($issn as $n => $i) {
        if (empty($i)) {
            unset($values['issn'][$n]);
            continue;
        }

        // $query[] = "issn:" . $i;
    }
    // if ($oa === false && !empty($query)) {
    //     $query = implode(' OR ', $query);
    //     $url = "https://doaj.org/api/search/journals/" . $query;
    //     $response = CallAPI('GET', $url);
    //     $json = json_decode($response, true);

    //     if (!empty($json['results'] ?? null) && isset($json['results'][0]['bibjson'])) {
    //         $n = count($json['results']);
    //         $index = 0;
    //         if ($n > 1) {
    //             $compare_name = strtolower($name);
    //             $compare_name = explode('(', $compare_name)[0];
    //             $compare_name = trim($compare_name);
    //             foreach ($json['results'] as $i => $res) {
    //                 if (isset($res['bibjson']['is_replaced_by'])) continue;
    //                 $index = $i;
    //                 if (strtolower($res['bibjson']['title']) == $compare_name) {
    //                     break;
    //                 }
    //             }
    //         }
    //         $r = $json['results'][$index]['bibjson'];
    //         $oa = $r['oa_start'] ?? false;
    //     }
    // }
    // $values['oa'] = $oa;

    try {
        // try to get impact factor from WoS Journal info
        include_once BASEPATH . "/php/simple_html_dom.php";

        // require_once BASEPATH . '/php/Settings.php';
        // $Settings = new Settings();
        // $settings = $Settings['api'];
        // $settings = file_get_contents(BASEPATH . "/apis.json");
        // $settings = json_decode($settings, true, 512, JSON_NUMERIC_CHECK);
        if (defined('WOS_JOURNAL_INFO') && !empty(WOS_JOURNAL_INFO)) {
            $YEAR = WOS_JOURNAL_INFO ?? 2021;

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

        if (defined('WOS_STARTER_KEY') && !empty(WOS_STARTER_KEY)) {
            $apikey = WOS_STARTER_KEY;
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
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");
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
        $id = $DB->to_ObjectID($id);
    }
    $updateResult = $collection->updateOne(
        ['_id' => $id],
        ['$set' => $values]
    );

    $DB->renderActivities(['_id' => $id]);

    if (isset($values['doi']) && !empty($values['doi'])) {
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['doi' => $values['doi']]);
    }
    if (isset($values['pubmed']) && !empty($values['pubmed'])) {
        // make sure that there is no duplicate entry in the queue
        $osiris->queue->deleteOne(['pubmed' => $values['pubmed']]);
    }
    // cleanFields($id);
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


Route::post('/update-user/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    $values = $_POST['values'];
    $values = validateValues($values);

    // separate personal and account information
    $person = $values;
    $account = [];

    foreach ([
        'is_admin' => 'bool',
        'is_controlling' => 'bool',
        'is_scientist' => 'bool',
        'is_leader' => 'bool',
        'is_active' => 'bool',
        'display_activities' => 'string',
        'show_coins' => 'string',
        'hide_achievements' => 'string',
        'maintenance' => 'string',
    ] as $key => $type) {
        if ($type == 'bool') {
            $account[$key] = boolval($values[$key] ?? false);
        } else {
            $account[$key] = $values[$key] ?? null;
        }
        unset($person[$key]);
    }

    // update name information
    if (isset($values['last']) && isset($values['first'])) {

        $person['displayname'] = "$values[first] $values[last]";
        $person['formalname'] = "$values[last], $values[first]";
        $person['first_abbr'] = "";
        foreach (explode(" ", $values['first']) as $name) {
            $person['first_abbr'] .= " " . $name[0] . ".";
        }
    }

    $updateResult = $osiris->persons->updateOne(
        ['username' => $user],
        ['$set' => $person]
    );
    $updateResult = $osiris->accounts->updateOne(
        ['username' => $user],
        ['$set' => $account]
    );

    if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
        header("Location: " . $_POST['redirect'] . "?msg=update-success");
        die();
    }
    echo json_encode([
        'updated' => $updateResult->getModifiedCount()
    ]);
});


Route::post('/update-profile/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    $target_dir = BASEPATH . "/img/users";
    if (!is_writable($target_dir)) {
        die("User image directory is unwritable. Please contact admin.");
    }
    $target_dir .= "/";
    $filename = "$user.jpg";


    if (isset($_FILES["file"])) {
        if ($_FILES['file']['type'] != 'image/jpeg') die('Wrong extension, only JPEG is allowed.');

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
        } else if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir . $filename)) {
            header("Location: " . ROOTPATH . "/profile/$user?msg=success");
            die;
        } else {
            printMsg(lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload."), "error");
        }
    } else if (isset($_POST['delete'])) {
        $filename = "$user.jpg";
        if (file_exists($target_dir . $filename)) {
            // Use unlink() function to delete a file
            if (!unlink($target_dir . $filename)) {
                printMsg("$filename cannot be deleted due to an error.", "error");
            } else {
                header("Location: " . ROOTPATH . "/profile/$user?msg=deleted");
                die;
            }
        }
        // printMsg("File has been deleted from the database.", "success");
    }
});

Route::post('/update-expertise/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_POST['values'])) die("no values given");

    $values = $_POST['values'];
    $values = validateValues($values);

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

Route::post('/update-journal/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $values = $_POST['values'];
    $values = validateValues($values);

    $collection = $osiris->journals;
    $mongoid = $DB->to_ObjectID($id);

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
    include_once BASEPATH . "/php/init.php";
    // select the right collection

    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = $DB->to_ObjectID($id);
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
    include_once BASEPATH . "/php/init.php";
    // prepare id
    if (!isset($_POST['authors']) || empty($_POST['authors'])) {
        echo "Error: Author list cannot be empty.";
        die();
    }
    $id = $DB->to_ObjectID($id);

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
    include_once BASEPATH . "/php/init.php";
    // prepare id
    $user = $_POST['user'] ?? $_SESSION['username'];

    $osiris->activities->updateMany(
        ['authors.user' => $user],
        ['$set' => ["authors.$.approved" => true]]
    );

    header("Location: " . ROOTPATH . "/issues?msg=update-success");
});

Route::post('/approve', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    if (!isset($_POST['quarter'])) {
        echo "Quarter was not defined";
        die();
    }
    $q = $_POST['quarter'];

    $updateResult = $osiris->account->updateOne(
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


Route::get('/form/user/([A-Za-z0-9]*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    $data = $DB->getPerson($user);
    include BASEPATH . "/pages/user-editor.php";
});


Route::post('/approve/([A-Za-z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $collection = $osiris->activities;
    // prepare id
    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = $DB->to_ObjectID($id);
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
