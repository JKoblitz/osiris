<?php
require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/php/format.php';
$mongoDB = new MongoDB\Client(
    'mongodb://localhost:27017/osiris?retryWrites=true&w=majority'
);

global $osiris;
$osiris = $mongoDB->osiris;

// global $matrix;
// $matrix_json = file_get_contents(BASEPATH."/matrix.json");
// $matrix = json_decode($matrix_json, true);

global $USER;
$user = $user ?? $_SESSION['username'] ?? null;
$USER = array();

if (!empty($user)) {
    $USER = getUserFromId($user);
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
    $USER['name'] = $USER['first'] . " " . $USER['last'];
    $USER['name_formal'] = $USER['last'] . ", " . $USER['first'];
    $USER['first_abbr'] = "";
    foreach (explode(" ", $USER['first']) as $name) {
        $USER['first_abbr'] .= " " . $name[0] . ".";
    }
    return $USER;
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

function is_approved($document, $user)
{
    if (!isset($document['authors'])) return true;
    $authors = $document['authors'];
    if (isset($document['editors'])) $authors = array_merge($authors->bsonSerialize(), $document['editors']->bsonSerialize());
    return getUserAuthor($authors, $user)['approved'] ?? false;
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
        case 'teaching':
            return $osiris->teachings;
        case 'review':
            return $osiris->reviews;
        default:
            echo "unsupported collection";
            return;
    }
}
