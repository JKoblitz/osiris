<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/php/format.php';
$mongoDB = new MongoDB\Client(
    'mongodb://localhost:27017/osiris?retryWrites=true&w=majority'
);

global $osiris;
$osiris = $mongoDB->osiris;

global $USER;
// $user = $_SESSION['username'] ?? null;
$USER = array();

if (!empty($_SESSION['username'])) {
    $USER = getUserFromId($_SESSION['username']);

    // if (empty($USER)) {
    //     require_once BASEPATH . '/php/_login.php';
    //     $USER = updateUser($username);
    //     $osiris->users->insertOne(
    //         $USER
    //     );
    // }
    // return $this->info['last_name'] . "," . $fn;
}

function mongo_date($date)
{
    $time = (new DateTime($date))->getTimestamp();
    return new MongoDB\BSON\UTCDateTime($time * 1000);
}

function getUserFromName($last, $first)
{
    global $osiris;
    $user = $osiris->users->findOne([
        'last' => $last,
        'first' => new MongoDB\BSON\Regex('^' . $first[0] . '.*')
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

function getJournal($doc)
{
    global $osiris;
    if (isset($doc['journal_id'])) {
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
        if (is_string($issn)){
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
    $impact = $journal['impact']->bsonSerialize();
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

function get_impact($journal_name, $year)
{
    global $osiris;
    if (empty($journal_name)) return null;
    $j = new \MongoDB\BSON\Regex('^' . trim($journal_name), 'i');
    $journal = $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
    if (empty($journal)) return null;
    // $journal = $journal->toArray();

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

    if (($doc['type'] == 'misc' || ($doc['type'] == 'review' && $doc['role'] == 'Editor')) && is_null($doc['end'])) {
        if (isset($doc['end-delay'])) {
            if (new DateTime() > new DateTime($doc['end-delay'])) {
                $issues[] = "openend";
            }
        } else {
            $issues[] = "openend";
        }
    }

    return $issues;
}

function get_collection($col)
{
    global $osiris;
    switch ($col) {
        case 'lecture':
            return $osiris->lectures;
        case 'misc':
            return $osiris->miscs;
        case 'poster':
            return $osiris->posters;
        case 'publication':
            return $osiris->publications;
        case 'students':
            return $osiris->studentss;
        case 'review':
            return $osiris->reviews;
        default:
            echo "unsupported collection";
            return;
    }
}


function addUserActivity($activity = 'create')
{
    global $osiris;
    $update = ['$push' => ['activity' => ['type' => $activity, 'date' => date("Y-m-d")]]];
    $u = $osiris->users->findone(['_id' => $_SESSION['username']]);
    $act = $u['activity'] ?? array();

    $uact = 0;
    foreach ($act as $a) {
        if (!isset($a['type'])) {
            $osiris->users->updateOne(
                ['_id' => $_SESSION['username']],
                ['$set' => ['activity' => [], 'achievements' => []]]
            );
            break;
        }
        if (($a['type'] ?? '') == $activity) {
            $uact++;
        }
    }

    if (empty($uact)) {
        $update['$push']['achievements'] = ['title' => "first-$activity", 'achieved' => date("d.m.Y")];
    } elseif ($uact === 9) {
        $update['$push']['achievements'] = ['title' => "10-$activity", 'achieved' => date("d.m.Y")];
    } elseif ($uact === 49) {
        $update['$push']['achievements'] = ['title' => "50-$activity", 'achieved' => date("d.m.Y")];
    }
    // dump($update, true);

    $osiris->users->updateOne(
        ['_id' => $_SESSION['username']],
        $update
    );
}


function achievementText($title, $person = null)
{
    if ($person) {
        switch ($title) {
            case 'first-create':
                return lang("$person created the first activity", "$person hat die erste Aktivität eingetragen");
                break;

            case 'first-update':
                return lang("$person updated an activity for the first time", "$person hat zum ersten Mal eine Aktivität bearbeitet");
                break;

            case '10-create':
                return lang("$person created 10 activities", "$person hat bereits 10 Aktivität eingetragen");
                break;

            case '10-update':
                return lang("$person updated 10 activities", "$person hat bereits 10 Mal eine Aktivität bearbeitet");
                break;

            case '50-create':
                return lang("$person created 50 activities", "$person hat bereits 50 Aktivität eingetragen");
                break;

            case '50-update':
                return lang("$person updated 50 activities", "$person hat bereits 50 Mal eine Aktivität bearbeitet");
                break;

            case 'first-delete':
                return lang("$person deleted an activity for the first time", "$person hat zum ersten Mal eine Aktivität gelöscht");
                break;

            default:
                return "";
                break;
        }
    }
    switch ($title) {
        case 'first-create':
            return lang("You created your first activity", "Du hast deine erste Aktivität eingetragen");
            break;

        case 'first-update':
            return lang("You updated an activity for the first time", "Du hast zum ersten Mal eine Aktivität bearbeitet");
            break;

        case '10-create':
            return lang("You created 10 activities", "Du hast bereits 10 Aktivität eingetragen");
            break;

        case '10-update':
            return lang("You updated 10 activities", "Du hast bereits 10 Mal eine Aktivität bearbeitet");
            break;

        case '50-create':
            return lang("You created 50 activities", "Du hast bereits 50 Aktivität eingetragen");
            break;

        case '50-update':
            return lang("You updated 50 activities", "Du hast bereits 50 Mal eine Aktivität bearbeitet");
            break;

        case 'first-delete':
            return lang("You deleted an activity for the first time", "Du hast zum ersten Mal eine Aktivität gelöscht");
            break;

        default:
            return "";
            break;
    }
}
