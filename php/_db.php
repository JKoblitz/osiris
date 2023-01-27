<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/php/format.php';

if (!isset($Settings)) {
    require_once BASEPATH . '/php/Settings.php';
    $Settings = new Settings();
}

$dbname = "osiris";

$mongoDB = new MongoDB\Client(
    "mongodb://localhost:27017/$dbname?retryWrites=true&w=majority"
);

$config = ['username' => 'root', 'password' => 'Zees1ius', 'database' => 'osiris'];

// global $arango;
// $arango = new ArangoClient\ArangoClient($config);

global $osiris;
$osiris = $mongoDB->$dbname;

global $USER;
// $user = $_SESSION['username'] ?? null;
$USER = array();

if (!empty($_SESSION['username'])) {
    $USER = getUserFromId($_SESSION['username']);

    // set standard values
    if (!isset($USER['is_controlling'])) $USER['is_controlling'] = false;
    if (!isset($USER['is_admin'])) $USER['is_admin'] = false;
    if (!isset($USER['is_scientist'])) $USER['is_scientist'] = false;
    if (!isset($USER['is_leader'])) $USER['is_leader'] = false;
    if (!isset($USER['display_activities'])) $USER['display_activities'] = 'web';
}

function is_ObjectID($id)
{
    if (empty($id)) return false;
    if (preg_match("/^[0-9a-fA-F]{24}$/", $id)) {
        return true;
    }
    return false;
}

function mongo_date($date)
{
    $time = (new DateTime($date))->getTimestamp();
    return new MongoDB\BSON\UTCDateTime($time * 1000);
}

function getUserFromName($last, $first)
{
    global $osiris;
    // if (str_ends_with($first, '.')){
    //     $first = new MongoDB\BSON\Regex('^' . $first[0] . '.*');
    // }
    $user = $osiris->users->findOne([
        '$or' => [
            ['last' => $last, 'first' => $first, 'names' => ['$exists' => false]],
            ['names' => "$last, $first"]
        ]
    ]);

    if (empty($user)) return null;
    return $user['_id'];
}

function getUserFromId($user)
{
    global $osiris;
    $USER = $osiris->users->findOne(['_id' => $user ?? $_SESSION['username']]);
    if (empty($USER)) return array();
    $USER['name'] = $USER['first'] . " " . $USER['last'];
    // $USER['name_formal'] = $USER['last'] . ", " . $USER['first'];
    $USER['first_abbr'] = "";
    $first = explode(" ", $USER['first']);
    foreach ($first as $name) {
        $USER['first_abbr'] .= " " . $name[0] . ".";
    }

    return $USER;
}

function getTitleLastname($user)
{
    $u = getUserFromId($user);
    if (empty($u)) return "!!$user!!";
    $n = "";
    if (!empty($u['academic_title'])) $n = $u['academic_title'] . " ";
    $n .= $u['last'];
    return $n;
}

function getUserAuthor($authors, $user)
{
    if (!is_array($authors)) {
        // it is most likely a MongoDB BSON
        $authors = $authors->bsonSerialize();
    }
    $author = array_filter($authors, function ($author) use ($user) {
        return $author['user'] == $user;
    });
    if (empty($author)) return array();
    return reset($author);
}

function getActivity($id)
{
    global $osiris;

    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }
    return $osiris->activities->findOne(['_id' => $id]);
}

function getJournal($doc)
{
    global $osiris;
    if (isset($doc['journal_id']) && !empty($doc['journal_id'])) {
        $id = $doc['journal_id'];
        if (is_numeric($id)) {
            $id = intval($id);
        } else {
            $id = new MongoDB\BSON\ObjectId($id);
        }
        $journal = $osiris->journals->findOne(['_id' => $id]);
        if (!empty($journal)) return $journal;
    }

    if (isset($doc['issn'])) {
        $issn = $doc['issn'];
        if (is_string($issn)) {
            $issn = explode(' ', $issn);
        }
        $journal = $osiris->journals->findOne(['issn' => ['$in' => $issn]]);
        if (!empty($journal)) return $journal;
    }

    $j = new \MongoDB\BSON\Regex('^' . trim($doc['journal']), 'i');
    return $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
}

function impact_from_year($journal, $year = CURRENTYEAR)
{
    $if = null;
    if (!isset($journal['impact'])) return '';
    $impact = $journal['impact'];
    if ($impact instanceof MongoDB\Model\BSONArray) {
        $impact = $impact->bsonSerialize();
    }
    if (empty($impact)) return $if;
    $last = end($impact)['impact'] ?? null;
    if (is_array($impact)) {
        $impact = array_filter($impact, function ($a) use ($year) {
            return $a['year'] == $year;
        });
        if (empty($impact)) {
            $impact = $last;
        } else {
            $if = reset($impact)['impact'];
        }
    }
    return $if;
}

function latest_impact($journal)
{
    $last = null;
    if (!isset($journal['impact'])) return null;
    $impact = $journal['impact'];
    if ($impact instanceof MongoDB\Model\BSONArray) {
        $impact = $impact->bsonSerialize();
    }
    if (empty($impact)) return null;
    $last = end($impact)['impact'] ?? null;
    return $last;
}

function get_journal($doc)
{
    global $osiris;
    if (isset($doc['journal_id']) && is_ObjectID($doc['journal_id'])) {
        $id = new MongoDB\BSON\ObjectId($doc['journal_id']);
        return $osiris->journals->findOne(['_id' => $id]);
    } else if (isset($doc['issn']) && !empty($doc['issn'])) {
        return $osiris->journals->findOne(['issn' => ['$in' => $doc['issn']]]);
    } else if (isset($doc['journal']) && !empty($doc['journal'])) {
        $j = new \MongoDB\BSON\Regex('^' . trim($doc['journal']) . '$', 'i');
        return $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
    } else {
        return array();
    }
}
function get_oa($doc)
{
    $journal = get_journal($doc);
    if (!isset($journal['oa']) || $journal['oa'] === false) {
        return false;
    } elseif ($journal['oa'] > 0) {
        if (intval($doc['year']) > $journal['oa']) return true;
        return false;
    } else {
        return true;
    }
}

function get_impact($doc, $year = null)
{
    $journal = get_journal($doc);

    if (empty($journal)) return null;
    // $journal = $journal->toArray();

    if ($year == null) {
        $year = intval($doc['year'] ?? 1) - 1;
    }
    return impact_from_year($journal, $year);
}

function is_approved($document, $user)
{
    if (!isset($document['authors'])) return true;
    $authors = $document['authors'];
    if (isset($document['editors'])) {
        $editors = $document['editors'];
        if (!is_array($authors)) {
            $authors = $authors->bsonSerialize();
        }
        if (!is_array($editors)) {
            $editors = $editors->bsonSerialize();
        }
        $authors = array_merge($authors, $editors);
    }
    return getUserAuthor($authors, $user)['approved'] ?? false;
}

function has_issues($doc, $user = null)
{
    if ($user === null) $user = $_SESSION['username'];
    $issues = array();

    if (!is_approved($doc, $user)) $issues[] = "approval";

    $epub = ($doc['epub'] ?? false);
    // $doc['epub-delay'] = "2022-08-01";
    if ($epub && isset($doc['epub-delay'])) {
        if (new DateTime() < new DateTime($doc['epub-delay'])) {
            $epub = false;
        }
    }
    if ($epub) $issues[] = "epub";
    if ($doc['type'] == "students" && isset($doc['status']) && $doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end'])) $issues[] = "students";

    if ((($doc['type'] == 'misc' && $doc['iteration'] == 'annual') || ($doc['type'] == 'review' && in_array($doc['role'], ['Editor', 'editorial']))) && is_null($doc['end'])) {
        if (isset($doc['end-delay'])) {
            if (new DateTime() > new DateTime($doc['end-delay'])) {
                $issues[] = "openend";
            }
        } else {
            $issues[] = "openend";
        }
    }

    if (isset($doc['journal']) && (!isset($doc['journal_id']) || empty($doc['journal_id']))) {
        $issues[] = 'journal_id';
    }

    return $issues;
}


function isUserActivity($doc, $user)
{
    if (isset($doc['user']) && $doc['user'] == $user) return true;
    foreach (['authors', 'editors'] as $role) {
        if (!isset($doc[$role])) continue;
        foreach ($doc[$role] as $author) {
            if (isset($author['user']) && !empty($author['user'])) {
                if ($user == $author['user']) return true;
            }
        }
    }
    return false;
}


// function addUserActivity($activity = 'create')
// {
//     global $osiris;
//     return;
//     $update = ['$push' => ['activity' => ['type' => $activity, 'date' => date("Y-m-d")]]];
//     $u = $osiris->users->findone(['_id' => $_SESSION['username']]);
//     $act = $u['activity'] ?? array();

//     $uact = 0;
//     foreach ($act as $a) {
//         if (!isset($a['type'])) {
//             $osiris->users->updateOne(
//                 ['_id' => $_SESSION['username']],
//                 ['$set' => ['activity' => [], 'achievements' => []]]
//             );
//             break;
//         }
//         if (($a['type'] ?? '') == $activity) {
//             $uact++;
//         }
//     }

//     if (empty($uact)) {
//         $update['$push']['achievements'] = ['title' => "first-$activity", 'achieved' => date("d.m.Y")];
//     } elseif ($uact === 9) {
//         $update['$push']['achievements'] = ['title' => "10-$activity", 'achieved' => date("d.m.Y")];
//     } elseif ($uact === 49) {
//         $update['$push']['achievements'] = ['title' => "50-$activity", 'achieved' => date("d.m.Y")];
//     }
//     // dump($update, true);

//     $osiris->users->updateOne(
//         ['_id' => $_SESSION['username']],
//         $update
//     );
// }


function get_reportable_activities($start, $end)
{
    global $osiris;
    $result = [];

    $startyear = intval(explode('-', $start, 2)[0]);
    $endyear = intval(explode('-', $end, 2)[0]);

    $starttime = getDateTime($start . ' 00:00:00');
    $endtime = getDateTime($end . ' 23:59:59');

    $options = ['sort' => ["type" => 1, "year" => 1, "month" => 1]];
    $filter = [
        'year' => ['$gte' => $startyear, '$lte' => $endyear],
    ];
    $cursor = $osiris->activities->find($filter, $options);

    foreach ($cursor as $doc) {

        // check if time of activity ist in the correct time range
        $ds = getDateTime($doc['start'] ?? $doc);
        if (isset($doc['end']) && !empty($doc['end'])) $de = getDateTime($doc['end'] ?? $doc);
        else $de = $ds;
        if (($ds <= $starttime && $starttime <= $de) || ($starttime <= $ds && $ds <= $endtime)) {
        } else {
            continue;
        }

        // the following is only relevant for publications
        if ($doc['type'] == 'publication') {
            // epubs are not reported
            if (isset($doc['epub']) && $doc['epub']) continue;
        }

         // check if any of the authors is affiliated
         $aoi_exists = false;
         foreach ($doc['authors'] as $a) {
             $aoi = boolval($a['aoi'] ?? false);
             $aoi_exists = $aoi_exists || $aoi;
         }
         if (!$aoi_exists) continue;

         $result[] = $doc;
    }

    return $result;
}
