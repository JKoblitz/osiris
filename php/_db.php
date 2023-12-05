<?php
// require_once BASEPATH . '/vendor/autoload.php';

// use MongoDB\BSON\ObjectId;
// use MongoDB\Client;
// use MongoDB\BSON\Regex;
// use MongoDB\Model\BSONArray;
// use MongoDB\Model\BSONDocument;

// require_once BASEPATH . '/php/Document.php';

// if (!isset($Settings)) {
//     require_once BASEPATH . '/php/Settings.php';
//     $Settings = new Settings();
// }

// if (!defined('DB_STRING')) {
//     // $dbname = $Settings->settings['database']['dbname'] ?? "osiris";
//     // $address = $Settings->settings['database']['ip'] ?? "localhost";
//     // $port = $Settings->settings['database']['port'] ?? "27017";
//     die("DB settings are missing in the CONFIG.php file. Add the DB_STRING constant as defined in the config documentation.");
// }

// $dbname = 'osiris';
// if (defined('DB_NAME') && !empty(DB_NAME)) $dbname = DB_NAME;

// $mongoDB = new Client(DB_STRING);

// global $osiris;
// $osiris = $mongoDB->$dbname;

// global $dataModules;
// $dataModules =
//     [
//         "pubtype" => [
//             "pubtype",
//         ],
//         "title" => [
//             "title",
//         ],
//         "teaching" => [
//             "title",
//             "module",
//             "module_id",
//             "authors",
//             "category",
//         ],
//         "authors" => [
//             "authors"
//         ],
//         "person" => [
//             "name",
//             "affiliation",
//             "academic_title",
//         ],
//         "student" => [
//             "category",
//             "details",
//         ],
//         "guest" => [
//             "category",
//             "details",
//         ],
//         "date" => [
//             "year",
//             "month",
//             "day",
//         ],
//         "lecture" => [
//             "lecture_type",
//             "invited_lecture",
//         ],
//         "date-range" => [
//             "start",
//             "end",
//         ],
//         "software" => [
//             "software_venue",
//             "link",
//             "version",
//             "software_type",
//         ],
//         "misc" => [
//             "iteration",
//         ],
//         "conference" => [
//             "conference",
//             "location",
//         ],
//         "journal" => [
//             "journal",
//             "journal_id",
//         ],
//         "magazine" => [
//             "magazine",
//             "link",
//         ],
//         "chapter" => [
//             "book",
//         ],
//         "book-series" => [
//             "series",
//         ],
//         "edition" => [
//             "edition",
//         ],
//         "issue" => [
//             "issue",
//         ],
//         "volume-pages" => [
//             "volume",
//             "pages",
//         ],
//         "publisher" => [
//             "publisher",
//             "city",
//         ],
//         "university" => [
//             "publisher",
//             "city",
//         ],
//         "editor" => [
//             "editors"
//         ],
//         "doi" => [
//             "doi",
//         ],
//         "pubmed" => [
//             "pubmed",
//         ],
//         "isbn" => [
//             "isbn",
//         ],
//         "doctype" => [
//             "doc_type",
//         ],
//         "openaccess" => [
//             "open_access",
//         ],
//         "online-ahead-of-print" => [
//             "epub",
//         ],
//         "correction" => [
//             "correction",
//         ],
//         "scientist" => [
//             "role",
//             "user",
//         ],
//         "review-description" => [
//             "title",
//         ],
//         "review-type" => [
//             "review-type",
//         ],
//         "editorial" => [
//             "editor_type",
//         ]

//     ];

// global $USER;
// // $user = $_SESSION['username'] ?? null;
// $USER = array();

// if (!empty($_SESSION['username'])) {
//     $USER = $DB->getPerson($_SESSION['username']);

//     // set standard values
//     if (!isset($USER['is_controlling'])) $USER['is_controlling'] = false;

//     $USER['is_admin'] = ($_SESSION['username'] === ADMIN || ($USER['is_admin'] ?? false));

//     if (!isset($USER['is_scientist'])) $USER['is_scientist'] = false;
//     if (!isset($USER['is_leader'])) $USER['is_leader'] = false;
//     if (!isset($USER['display_activities'])) $USER['display_activities'] = 'web';
// }

// function to_ObjectID($id)
// {
//     if (is_ObjectID($id)) {
//         return new ObjectId($id);
//     }
//     return intval($id);
// }

// function is_ObjectID($id)
// {
//     if (empty($id)) return false;
//     if (preg_match("/^[0-9a-fA-F]{24}$/", $id)) {
//         return true;
//     }
//     return false;
// }

// function $DB->getConnected($type, $id)
// {
//     global $osiris;
//     if (empty($id) || !is_ObjectID($id)) return [];
//     $id = new ObjectId($id);
//     if ($type == 'journal') {
//         return $osiris->journals->findOne(['_id' => $id]);
//     }
//     if ($type == 'teaching') {
//         return $osiris->teaching->findOne(['_id' => $id]);
//     }
// }


// function cleanFields($id)
// {
//     return true;
//     global $osiris;
//     global $Settings;
//     global $dataModules;

//     $fields = [];
//     foreach ($Settings->activities as $a) {
//         if (!$a['display']) continue;
//         foreach ($a['subtypes'] as $type) {
//             $fields[$type['id']] = [];
//             foreach ($type['modules'] as $m) {
//                 if (isset($dataModules[$m]))
//                     $fields[$type['id']] = $dataModules[$m];
//             }
//         }
//     }
//     $general = [
//         "type", "_id", 'locked',
//         "year", "month", "day",
//         "title", "authors",
//         "created", "created_by", "updated", "updated_by",
//         "comment", "editor-comment",
//         "files"
//     ];


//     $values = $osiris->activities->findOne(['_id' => $id]);

//     $type = $values['type'];
//     if ($type == 'publication') {
//         $type = $values['pubtype'];
//     } elseif ($type == 'review') {
//         $type = $values['role'];
//         if ($type == "Reviewer") {
//             $type = 'review';
//             $osiris->activities->updateOne(
//                 ['_id' => $id],
//                 ['$set' => ["role" => 'review']]
//             );
//         }
//         if ($type == "Editor") {
//             $type = 'editorial';
//             $osiris->activities->updateOne(
//                 ['_id' => $id],
//                 ['$set' => ["role" => 'editorial']]
//             );
//         }
//     } else if ($type == 'misc') {
//         $type = "misc-" . $values['iteration'];
//     } else if ($type == 'students') {
//         if (str_contains($values['category'], "thesis") || $values['category'] == 'doktorand:in') {
//             $type = "students";
//         } else {
//             $type = 'guests';
//         }
//     }

//     if (!array_key_exists($type, $fields)) return false;

//     foreach ($values as $key => $value) {
//         if (!in_array($key, $general) && !in_array($key, $fields[$type])) {
//             // dump([$key, $value], true);

//             $updateResult = $osiris->activities->updateOne(
//                 ['_id' => $id],
//                 ['$unset' => [$key => 1]]
//             );
//             // dump($updateResult->getModifiedCount());
//         }
//     }
//     return true;
// }


// function getUserFromName($last, $first)
// {
//     global $osiris;
//     $last = trim($last);
//     $first = trim($first);
//     if (strlen($first) == 1) $first .= ".";

//     try {
//         $regex = new MongoDB\BSON\Regex('^' . $first[0]);
//         $user = $osiris->persons->findOne([
//             '$or' => [
//                 ['last' => $last, 'first' => $regex],
//                 ['names' => "$last, $first"]
//             ]
//         ]);
//     } catch (\Throwable $th) {
//         $user = $osiris->persons->findOne([
//             '$or' => [
//                 ['last' => $last, 'first' => $first],
//                 ['names' => "$last, $first"]
//             ]
//         ]);
//     }

//     if (empty($user)) return null;
//     return strval($user['_id']);
// }

// function $DB->getPerson($user = null, $simple = false)
// {
//     global $osiris;
//     $USER = $osiris->persons->findOne(['_id' => $user ?? $_SESSION['username']]);
//     if (empty($USER)) return array();
//     if ($simple) return $USER;
//     $USER['name'] = $USER['first'] . " " . $USER['last'];
//     // $USER['name_formal'] = $USER['last'] . ", " . $USER['first'];
//     $USER['first_abbr'] = "";
//     $first = explode(" ", $USER['first']);
//     foreach ($first as $name) {
//         $USER['first_abbr'] .= " " . $name[0] . ".";
//     }

//     return $USER;
// }

// function getTitleLastname($user)
// {
//     $u = $DB->getPerson($user);
//     if (empty($u)) return "!!$user!!";
//     $n = "";
//     if (!empty($u['academic_title'])) $n = $u['academic_title'] . " ";
//     $n .= $u['last'];
//     return $n;
// }


// function getActivity($id)
// {
//     global $osiris;

//     if (is_numeric($id)) {
//         $id = intval($id);
//     } else {
//         $id = new ObjectId($id);
//     }
//     return $osiris->activities->findOne(['_id' => $id]);
// }

// function getJournalName($doc)
// {
//     $journal = getJournal($doc);
//     return ucname($journal['journal'] ?? '');
// }

// function getJournal($doc)
// {
//     global $osiris;
//     if (isset($doc['journal_id']) && !empty($doc['journal_id'])) {
//         $id = $doc['journal_id'];
//         if (is_numeric($id)) {
//             $id = intval($id);
//         } else {
//             $id = new ObjectId($id);
//         }
//         $journal = $osiris->journals->findOne(['_id' => $id]);
//         if (!empty($journal)) return $journal;
//     }

//     if (isset($doc['issn'])) {
//         $issn = $doc['issn'];
//         if (is_string($issn)) {
//             $issn = explode(' ', $issn);
//         }
//         $journal = $osiris->journals->findOne(['issn' => ['$in' => $issn]]);
//         if (!empty($journal)) return $journal;
//     }

//     $j = new MongoDB\BSON\Regex('^' . trim($doc['journal']), 'i');
//     return $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
// }

// function impact_from_year($journal, $year = CURRENTYEAR)
// {
//     $if = 0;
//     if (!isset($journal['impact']) || empty($journal['impact'])) return 0;

//     // get impact factors from journal
//     $impact = $journal['impact'];
//     if ($impact instanceof MongoDB\Model\BSONArray) {
//         $impact = $impact->bsonSerialize();
//     }
//     // sort ascending by year
//     usort($impact, function ($a, $b) {
//         return $a['year'] - $b['year'];
//     });

//     foreach ($impact as $i) {
//         if ($i['year'] >= $year) break;
//         $if = $i['impact'];
//     }
//     return $if;
// }

// function latest_impact($journal)
// {
//     $last = null;
//     if (!isset($journal['impact'])) return null;
//     $impact = $journal['impact'];
//     if ($impact instanceof MongoDB\Model\BSONArray) {
//         $impact = $impact->bsonSerialize();
//     }
//     if (empty($impact)) return null;
//     $last = end($impact)['impact'] ?? null;
//     return $last;
// }

// function get_journal($doc)
// {
//     global $osiris;
//     if (isset($doc['journal_id']) && is_ObjectID($doc['journal_id'])) {
//         $id = new ObjectId($doc['journal_id']);
//         return $osiris->journals->findOne(['_id' => $id]);
//     } else if (isset($doc['issn']) && !empty($doc['issn'])) {
//         return $osiris->journals->findOne(['issn' => ['$in' => $doc['issn']]]);
//     } else if (isset($doc['journal']) && !empty($doc['journal'])) {
//         $j = new MongoDB\BSON\Regex('^' . trim($doc['journal']) . '$', 'i');
//         return $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
//     } else {
//         return array();
//     }
// }
// function get_oa($doc)
// {
//     $journal = get_journal($doc);
//     if (!isset($journal['oa']) || $journal['oa'] === false) {
//         return false;
//     } elseif ($journal['oa'] > 0) {
//         if (intval($doc['year']) > $journal['oa']) return true;
//         return false;
//     } else {
//         return true;
//     }
// }

// function get_impact($doc, $year = null)
// {
//     $journal = get_journal($doc);

//     if (empty($journal)) return null;

//     if ($year == null) {
//         $year = intval($doc['year'] ?? 1) - 1;
//     }
//     return impact_from_year($journal, $year);
// }

// function isUserActivity($doc, $user)
// {
//     if (isset($doc['user']) && $doc['user'] == $user) return true;
//     foreach (['authors', 'editors'] as $role) {
//         if (!isset($doc[$role])) continue;
//         foreach ($doc[$role] as $author) {
//             if (isset($author['user']) && !empty($author['user'])) {
//                 if ($user == $author['user']) return true;
//             }
//         }
//     }
//     return false;
// }

// function ucname($name)
// {
//     include BASEPATH . "/php/stopwords.php";
//     $result = "";
//     $words = explode(" ", $name);
//     foreach ($words as $word) {
//         if (!ctype_lower($word) || in_array($word, $stopwords))
//             $result .= " " . $word;
//         else
//             $result .= " " . ucfirst($word);
//     }
//     return trim($result);
// }

// function get_reportable_activities($start, $end)
// {
//     global $osiris;
//     $result = [];

//     $startyear = intval(explode('-', $start, 2)[0]);
//     $endyear = intval(explode('-', $end, 2)[0]);

//     $starttime = getDateTime($start . ' 00:00:00');
//     $endtime = getDateTime($end . ' 23:59:59');

//     $options = ['sort' => ["year" => 1, "month" => 1, "day" => 1, "start.day" => 1]];
//     $filter = [];

//     $filter['$or'] =   array(
//         [
//             "start.year" => array('$lte' => $startyear),
//             '$and' => array(
//                 ['$or' => array(
//                     ['end.year' => array('$gte' => $endyear)],
//                     ['end' => null]
//                 )],
//                 ['$or' => array(
//                     ['type' => 'misc', 'subtype' => 'misc-annual'],
//                     ['type' => 'review', 'subtype' =>  'editorial'],
//                 )]
//             )
//         ],
//         [
//             'year' => ['$gte' => $startyear, '$lte' => $endyear],
//         ]
//     );
//     $cursor = $osiris->activities->find($filter, $options);

//     foreach ($cursor as $doc) {
//         // dump($doc['title'] ?? '');
//         // check if time of activity ist in the correct time range
//         $ds = getDateTime($doc['start'] ?? $doc);
//         if (isset($doc['end']) && !empty($doc['end'])) $de = getDateTime($doc['end'] ?? $doc);
//         elseif (in_array($doc['subtype'], ['misc-annual', 'editorial']) && is_null($doc['end'])) {
//             $end = $endtime;
//         } else $de = $ds;

//         if (($de  >= $starttime) && ($endtime >= $ds)) {
//             //overlap
//             // echo "overlap";
//             // if (($ds <= $starttime && $starttime <= $de) || ($starttime <= $ds && $ds <= $endtime)) {
//         } else {
//             continue;
//         }

//         // the following is only relevant for publications
//         if ($doc['type'] == 'publication') {
//             // epubs are not reported
//             if (isset($doc['epub']) && $doc['epub']) continue;
//         }

//         // check if any of the authors is affiliated
//         $aoi_exists = false;
//         foreach ($doc['authors'] as $a) {
//             $aoi = boolval($a['aoi'] ?? false);
//             $aoi_exists = $aoi_exists || $aoi;
//         }
//         if (!$aoi_exists) continue;

//         $result[] = $doc;
//     }

//     return $result;
// }


// function getDeptFromAuthors($authors)
// {
//     $result = [];
//     if ($authors instanceof BSONArray) {
//         $authors = $authors->bsonSerialize();
//     }
//     if ($authors instanceof BSONDocument) {
//         $authors = iterator_to_array($authors);
//     }
//     $authors = array_filter($authors, function ($a) {
//         return boolval($a['aoi'] ?? false);
//     });
//     if (empty($authors)) return [];
//     $users = array_filter(array_column($authors, 'user'));
//     foreach ($users as $user) {
//         $user = $DB->getPerson($user);
//         if (empty($user) || empty($user['dept'])) continue;
//         if (in_array($user['dept'], $result)) continue;
//         $result[] = $user['dept'];
//     }
//     return $result;
// }

// function renderActivities($filter = [])
// {
//     global $osiris;
//     $Format = new Document(true);
//     $cursor = $osiris->activities->find($filter);
//     $rendered = [
//         'print' => '',
//         'web' => '',
//         'depts' => '',
//         'icon' => '',
//         'title' => '',
//     ];
//     foreach ($cursor as $doc) {
//         $id = $doc['_id'];
//         $Format->setDocument($doc);
//         $rendered = [
//             'print' => $Format->format(),
//             'web' => $Format->formatShort(),
//             'depts' => getDeptFromAuthors($doc['authors']),
//             'icon' => $Format->activity_icon(),
//             'title' => $Format->activity_subtype(),
//         ];
//         $values = ['rendered' => $rendered];

//         if ($doc['type'] == 'publication' && isset($doc['journal'])) {
//             // update impact if necessary
//             $if = get_impact($doc);
//             if (!empty($if) && (!isset($doc['impact']) || $if != $doc['impact'])) {
//                 $values['impact'] = $if;
//             }
//         }

//         $osiris->activities->updateOne(
//             ['_id' => $id],
//             ['$set' => $values]
//         );
//     }
//     // return last element in case that only one id has been rendered
//     return $rendered;
// }
