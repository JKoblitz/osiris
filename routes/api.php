<?php

/**
 * Routing for API
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

function return_rest($data, $count = 0, $status = 200)
{
    $result = array();
    $limit = intval($_GET['limit'] ?? 0);

    if (!empty($limit) && $count > $limit && is_array($data)) {
        $offset = intval($_GET['offset'] ?? 0) || 0;
        $data = array_slice($data, $offset, min($limit, $count - $offset));
        $result += array(
            'limit' => $limit,
            'offset' => $offset
        );
    }
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
    if ($status == 200) {
        $result += array(
            'status' => 200,
            'count' => $count,
            'data' => $data
        );
    } elseif ($status == 400) {
        $result += array(
            'status' => 400,
            'count' => 0,
            'error' => 'WrongCall',
            'msg' => $data
        );
    } else {
        $result += array(
            'status' => $status,
            'count' => 0,
            'error' => 'DataNotFound',
            'msg' => $data
        );
    }
    return json_encode($result, JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
}


/**
 * Internally used API end points
 */

Route::get('/api/activities', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    if (isset($_GET['json'])) {
        $filter = json_decode($_GET['json']);
    }
    $result = $osiris->activities->find($filter)->toArray();


    if (isset($_GET['formatted']) && $_GET['formatted']) {
        include_once BASEPATH . "/php/Document.php";
        $table = [];
        $Format = new Document(true, 'web');

        foreach ($result as $doc) {
            $Format->setDocument($doc);
            $table[] = [
                'id' => strval($doc['_id']),
                'activity' => $Format->format(),
                'icon' => $Format->activity_icon()
            ];
        }

        $result = $table;
    }

    echo return_rest($result, count($result));
});


Route::get('/api/html', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Render.php";
    include_once BASEPATH . "/php/Document.php";
    $Format = new Document(true, 'dsmz.de');
    $Format->full = true;
    // $Format->abbr_journal = true;

    $result = [];
    $docs = $osiris->activities->find([
        'type' => 'publication', 'authors.aoi' => ['$in' => [true, 1, '1']],
        'year' => ['$gte' => 2023]
    ]);


    foreach ($docs as $i => $doc) {
        if (isset($_GET['limit']) && $i >= $_GET['limit']) break;

        if (isset($doc['rendered'])) {
            $rendered = $doc['rendered'];
        } else {
            $rendered = renderActivities(['_id' => $id]);
        }

        $link = null;
        if (!empty($doc['doi'] ?? null)) {
            $link = "https://dx.doi.org/" . $doc['doi'];
        } elseif (!empty($doc['pubmed'] ?? null)) {
            $link = "https://www.ncbi.nlm.nih.gov/pubmed/" . $doc['pubmed'];
        }

        $entry = [
            'id' => strval($doc['_id']),
            'html' => $rendered['print'],
            'year' => $doc['year'] ?? null,
            'departments' => $rendered['depts'],
            'link' => $link
        ];
        $result[] = $entry;
    }

    echo return_rest($result, count($result));
});

Route::get('/api/all-activities', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Render.php";
    include_once BASEPATH . "/php/Document.php";

    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");

    $user = $_GET['user'] ?? $_SESSION['username'] ?? null;
    $page = $_GET['page'] ?? 'all-activities';
    $highlight = true;
    if ($page == 'my-activities') {
        $highlight = $user;
    }
    // $Format = new Document($highlight);

    $filter = [];
    $result = [];
    if ($page == "my-activities") {
        // only own work
        $filter = ['$or' => [['authors.user' => $user], ['editors.user' => $user], ['user' => $user]]];
    }
    if (isset($_GET['type'])) {
        $filter['type'] = $_GET['type'];
    }
    $cursor = $osiris->activities->find($filter);
    $cart = readCart();
    foreach ($cursor as $doc) {
        $id = $doc['_id'];
        if (isset($doc['rendered'])) {
            $rendered = $doc['rendered'];
        } else {
            $rendered = renderActivities(['_id' => $id]);
        }

        // $depts = $Groups->getDeptFromAuthors($doc['authors']??[]);
        $depts = $rendered['depts'];
        if ($depts instanceof MongoDB\Model\BSONArray) {
            $depts = $depts->bsonSerialize();
        }
        $type = $doc['type'];
        $format_full = $rendered['print'];
        if (($_GET['display_activities'] ?? 'web') == 'web') {
            $format = $rendered['web'];
        } else {
            $format = $format_full;
        }
        if ($page == 'portal'){
            $format = str_replace(ROOTPATH."/activities/view", PORTALPATH."/activity", $format);
            $format = str_replace(ROOTPATH."/profile", PORTALPATH."/person", $format);
        }

        $active = false;
        // if (!isset($doc['year'])) {dump($doc, true); die;}
        $sm = intval($doc['month'] ?? 0);
        $sy = intval($doc['year'] ?? 0);
        // die;
        $em = $sm;
        $ey = $sy;

        if (isset($doc['end']) && !empty($doc['end'])) {
            $em = $doc['end']['month'];
            $ey = $doc['end']['year'];
        } elseif (
            (
                ($doc['type'] == 'misc' && ($doc['subtype'] ?? $doc['iteration']) == 'annual') ||
                ($doc['type'] == 'review' && in_array($doc['subtype'] ?? $doc['role'], ['Editor', 'editorial', 'editor']))
            ) && empty($doc['end'])
        ) {
            $em = CURRENTMONTH;
            $ey = CURRENTYEAR;
            $active = true;
        }
        $sq = $sy . 'Q' . ceil($sm / 3);
        $eq = $ey . 'Q' . ceil($em / 3);

        $datum = [
            'quarter' => $sq,
            'icon' => $rendered['icon'] . '<span style="display:none">' . $type . " " . $rendered['type'] . '</span>',
            'activity' => $format,
            'links' => '',
            'search-text' => $format_full,
            'start' => $rendered['start'] ?? '',
            'end' => $rendered['end'] ?? '',
            'departments' => $depts,//implode(', ', $depts),
            'epub' => (isset($doc['epub']) && boolval($doc['epub']) ? 'true' : 'false'),
            'type' => $rendered['type'],
            'subtype' => $rendered['subtype'],
            'year'=> $doc['year'] ?? 0,
            'authors'=>$rendered['authors'] ?? '',
            'title'=>$rendered['title'] ?? '',
        ];

        if ($active) {
            $datum['quarter'] .= ' - today';
        } elseif ($sq != $eq) {
            if ($sy == $ey) {
                $datum['quarter'] .= ' - ' . 'Q' . ceil($em / 3);
            } else {
                $datum['quarter'] .= ' - ' . $eq;
            }
        }

        if (defined('ROOTPATH')){
        $datum['links'] =
            "<a class='btn link square' href='" . ROOTPATH . "/activities/view/$id'>
                <i class='ph ph-arrow-fat-line-right'></i>
            </a>";
        // $useractivity = $DB->isUserActivity($doc, $user);
        // if ($useractivity) {
        //     $datum['links'] .= " <a class='btn link square' href='" . ROOTPATH . "/activities/edit/$id'>
        //         <i class='ph ph-pencil-simple-line'></i>
        //     </a>";
        // }
        $datum['links'] .= "<button class='btn link square' onclick='addToCart(this, \"$id\")'>
            <i class='" . (in_array($id, $cart) ? 'ph ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-shopping-cart ph-shopping-cart-plus') . "'></i>
        </button>";
    }
        $result[] = $datum;
    }
    echo return_rest($result, count($result));
});


Route::get('/api/concept-activities', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Document.php";

    $result = [];
    $name = $_GET['concept'];

    $concepts = $osiris->activities->aggregate(
    [
        ['$match' => ['concepts.display_name' => $name]],
        ['$project' => ['rendered' => 1, 'concepts' => 1]],
        ['$unwind' => '$concepts'],
        ['$match' => ['concepts.display_name' => $name]],
        ['$sort' => ['concepts.score' => -1]],
        ['$project' => [
            '_id'=> 0,
            'score' => '$concepts.score',
            'icon' => '$rendered.icon',
            'activity' => '$rendered.web',
            'type' => '$rendered.type',
            'id' => ['$toString' => '$_id']
        ]]
    ]
    )->toArray();

    echo return_rest($concepts, count($result));
});


Route::get('/api/users', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    if (isset($_GET['json'])) {
        $filter = json_decode($_GET['json']);
    }
    $result = $osiris->persons->find($filter)->toArray();

    echo return_rest($result, count($result));
});


Route::get('/api/reviews', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    $filter['type'] = 'review';
    $result = $osiris->activities->find($filter)->toArray();

    $reviews = [];
    foreach ($result as $doc) {
        if (!array_key_exists($doc['user'], $reviews)) {
            $u = $DB->getNameFromId($doc['user']);
            $reviews[$doc['user']] = [
                'User' => $doc['user'],
                'Name' => $u,
                'Editor' => 0,
                'Editorials' => [],
                'Reviewer' => 0,
                "Reviews" => []
            ];
        }
        switch (strtolower($doc['subtype'] ?? $doc['role'] ?? 'review')) {
            case 'editor':
            case 'editorial':
                $reviews[$doc['user']]['Editor']++;
                $date = format_date($doc['start'] ?? $doc);
                if (isset($doc['end']) && !empty($doc['end'])) {
                    $date .= " - " . format_date($doc['end']);
                } else {
                    $date .= " - today";
                }

                $reviews[$doc['user']]['Editorials'][] = [
                    'id' => strval($doc['_id']),
                    'date' => $date,
                    'details' => $doc['editor_type'] ?? ''
                ];
                break;

            case 'reviewer':
            case 'review':
                $reviews[$doc['user']]['Reviewer']++;
                $reviews[$doc['user']]['Reviews'][] = [
                    'id' => strval($doc['_id']),
                    'date' => format_date($doc)
                ];
                break;
            default:
                $reviews[$doc['user']]['Reviewer']++;
                $reviews[$doc['user']]['Reviews'][] = [
                    'id' => strval($doc['_id']),
                    'date' => format_date($doc)
                ];
                break;
        }
    }

    $table = array_values($reviews);

    echo return_rest($table, count($result));
});


Route::get('/api/journal', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['search'])) {
        $j = new \MongoDB\BSON\Regex(trim($_GET['search']), 'i');
        $filter = ['$or' =>  [
            ['journal' => ['$regex' => $j]],
            ['issn' => $_GET['search']]
        ]];
    }
    $result = $osiris->journals->find($filter)->toArray();
    echo return_rest($result, count($result));
});


Route::get('/api/teaching', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['search'])) {
        $j = new \MongoDB\BSON\Regex(trim($_GET['search']), 'i');
        $filter = ['$or' =>  [
            ['title' => ['$regex' => $j]],
            ['module' => $_GET['search']]
        ]];
    }
    $result = $osiris->teaching->find($filter)->toArray();
    echo return_rest($result, count($result));
});

Route::get('/api/projects', function () {
    include_once BASEPATH . "/php/init.php";
    $filter = [];
    if (isset($_GET['search'])) {
        $j = new \MongoDB\BSON\Regex(trim($_GET['search']), 'i');
        $filter = ['$or' =>  [
            ['title' => ['$regex' => $j]],
            ['id' => $_GET['search']]
        ]];
    }
    $result = $osiris->projects->find($filter)->toArray();
    echo return_rest($result, count($result));
});

Route::get('/api/journals', function () {
    include_once BASEPATH . "/php/init.php";
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");

    $journals = $osiris->journals->find()->toArray();
    $result = ['data' => []];
    // $i = 0;
    $activities = $osiris->activities->find(['journal_id' => ['$exists' => 1, '$ne' => null]], ['projection' => ['journal_id' => 1]])->toArray();
    $activities = array_column($activities, 'journal_id');
    $activities = array_count_values($activities);
    $no = lang('No', 'Nein');
    $yes = lang('Yes', 'Ja');
    $since = lang('since ', 'seit ');
    foreach ($journals as $doc) {
        if (!isset($doc['oa']) || $doc['oa'] === false) {
            $oa = $no;
        } elseif ($doc['oa'] === 0) {
            $oa =  $yes;
        } elseif ($doc['oa'] > 0) {
            $oa =  $since . $doc['oa'];
        }
        $result['data'][] = [
            'id' => strval($doc['_id']),
            'name' => $doc['journal'],
            'abbr' => $doc['abbr'],
            'publisher' => $doc['publisher'] ?? '',
            'open_access' => $oa,
            'issn' => implode(', ', $doc['issn']->bsonSerialize()),
            'if' => $DB->latest_impact($doc) ?? '',
            'count' => $activities[strval($doc['_id'])] ?? 0
        ];
    }

    echo json_encode($result);
});



Route::get('/api/google', function () {
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
    if (!isset($_GET["user"]))
        exit - 1;

    include(BASEPATH . '/php/GoogleScholar.php');
    $user = $_GET["user"];
    $google = new GoogleScholar($user);
    # create and load the HTML

    if (!isset($_GET['doc'])) {
        $result = $google->getAllUserEntries();
    } else {
        $doc = $_GET["doc"];
        $result = $google->getDocumentDetails($doc);
    }

    echo json_encode($result);
});


Route::get('/api/levenshtein', function () {
    include(BASEPATH . '/php/init.php');
    include(BASEPATH . '/php/Levenshtein.php');
    $levenshtein = new Levenshtein($osiris);

    $result = [];

    $pubmed = $_GET['pubmed'];
    $title = $_GET['title'];

    // $test pubmed
    $test = $osiris->activities->findOne(['pubmed' => $pubmed]);
    if (!empty($test)) {
        $result = [
            'similarity' => 1.,
            'id' => strval($test['_id']),
            'title' => $test['title']
        ];
    }

    $l = $levenshtein->findDuplicate($title);
    $id = $l[0];
    $sim = round($l[2], 1);
    if ($sim < 50) $sim = 0;
    $result = [
        'similarity' => $sim,
        'id' => $id,
        'title' => $levenshtein->found
    ];


    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");


    echo json_encode($result);
});


// Dashboard interface

Route::get('/api/dashboard/oa-status', function () {
    include(BASEPATH . '/php/init.php');

    $filter = ['oa_status' => ['$ne' => null]];
    if (isset($_GET['year'])) {
        $filter['year'] = $_GET['year'];
    } else {
        $filter['year'] = ['$gte' => $Settings->get('startyear')];
    }

    $result = array();
    $result = $osiris->activities->aggregate([
        ['$match' => $filter],
        [
            '$group' => [
                '_id' => [
                    'status' => '$oa_status',
                    'year' => '$year'
                ],
                'count' => ['$sum' => 1],
            ]
        ],
        ['$project' => ['_id' => 0, 'status' => '$_id.status', 'year' => '$_id.year', 'count' => 1]],
        ['$sort' => ['year' => 1]],
        [
            '$group' => [
                '_id' => '$status',
                'data' => ['$push' => '$$ROOT']
            ]
        ],
    ])->toArray();
    echo return_rest($result, count($result));
});


Route::get('/api/dashboard/collaborators', function () {
    include(BASEPATH . '/php/init.php');
    include(BASEPATH . '/php/Project.php');

    $result = [];
    if (isset($_GET['project'])) {
        $id = $_GET['project'];
        if (DB::is_ObjectID($id)) {
            $mongo_id = $DB->to_ObjectID($id);
            $project = $osiris->projects->findOne(['_id' => $mongo_id]);
        } else {
            $project = $osiris->projects->findOne(['name' => $id]);
            $id = strval($project['_id'] ?? '');
        }
        if (empty($project)) {
            die("Project could not be found.");
        } elseif (empty($project['collaborators'] ?? [])) {
            die("Project has no collaborators");
        } else {
            $P = new Project($project);
            $result = $P->getScope();

            $data = [
                'lon' => [],
                'lat' => [],
                'text' => [],
                'marker' => [
                    'size' => 15,
                    'color' => []
                ]
            ];
            foreach ($project['collaborators'] as $c) {
                // if (empty($c['lng']))
                $data['lon'][] = $c['lng'];
                $data['lat'][] = $c['lat'];
                $data['text'][] = "<b>$c[name]</b><br>$c[location]";
                $color = ($c['role'] == 'Partner' ? '#008083' : '#f78104');
                $data['marker']['color'][] = $color;
            }
            $result['collaborators'] = $data;
        }
    } else {
        $result = $osiris->projects->aggregate([
            ['$match' => ['collaborators' => ['$exists' => 1]]],
            ['$project' => ['collaborators' => 1]],
            ['$unwind' => '$collaborators'],
            [
                '$group' => [
                    '_id' => '$collaborators.ror',
                    'count' => ['$sum' => 1],
                    'data' => [
                        '$first' => '$collaborators'
                    ]
                ]
            ],
            // ['$project' => ['_id' => 0, 'status' => '$_id.status', 'year' => '$_id.year', 'count' => 1]],
            // ['$sort' => ['year' => 1]],
            // [
            //     '$group' => [
            //         '_id' => '$status',
            //         'data' => ['$push' => '$$ROOT']
            //     ]
            // ],
        ])->toArray();
    }



    echo return_rest($result, count($result));
});


Route::get('/api/dashboard/author-role', function () {
    include(BASEPATH . '/php/init.php');
    $result = array(
        'labels' => [],
        'y' => [],
        'colors' => []
    );

    $filter = ['year' => ['$gte' => $Settings->get('startyear')], 'type' => 'publication'];
    if (isset($_GET['user'])) {
        $user = $_GET['user'];
        $filter['authors.user'] = $user;

        $data = $osiris->activities->aggregate([
            ['$match' => $filter],
            ['$project' => ['authors' => 1]],
            ['$unwind' => '$authors'],
            ['$match' => ['authors.user' => $user, 'authors.aoi' => true]],
            [
                '$group' => [
                    '_id' => '$authors.position',
                    'count' => ['$sum' => 1],
                ]
            ],
            ['$sort' => ['count' => -1]],
            ['$project' => ['_id' => 0, 'x' => '$_id', 'y' => '$count']],
        ])->toArray();

        $editorials = $osiris->activities->count(['editors.user' => $user]);
        if ($editorials !== 0)
            $data[] = [
                'x' => 'editor',
                'y' => $editorials
            ];
    }

    foreach ($data as $el) {
        switch ($el['x']) {
            case 'first':
                $label = lang("First author", "Erstautor");
                $color = '#006EB799';
                break;
            case 'last':
                $label = lang("Last author", "Letztautor");
                $color = '#004d8099';
                break;
            case 'middle':
                $label = lang("Middle author", "Mittelautor");
                $color = '#cce2f099';
                break;
            case 'editor':
                $label = lang("Editorship", "Editorenschaft");
                $color = '#002c4999';
                break;
            case 'corresponding':
                $label = lang("Corresponding", "Korrespondierender Autor");
                $color = '#4c99cc99';
                break;
            default:
                $label = $el['x'];
                $color = '#ffffff';
                break;
        }
        $result['labels'][] = $label;
        $result['y'][] = $el['y'];
        $result['colors'][] = $color;
    }

    echo return_rest($result, count($result));
});



Route::get('/api/dashboard/impact-factor-hist', function () {
    include(BASEPATH . '/php/init.php');

    $filter = ['year' => ['$gte' => $Settings->get('startyear')], 'impact' => ['$ne' => null]];
    if (isset($_GET['user'])) {
        $filter['authors.user'] = $_GET['user'];
    }
    $max = $osiris->activities->find(
        $filter,
        ['sort' => ['impact' => -1], 'limit' => 1, 'projection' => ['impact' => 1]]
    )->toArray();

    if (empty($max)) {
        echo return_rest([], 0);
        die;
    }
    $max_impact = ceil($max[0]['impact']);
    $x = [];
    for ($i = 1; $i <= $max_impact; $i++) {
        $x[] = $i;
    }

    $data = $osiris->activities->aggregate([
        ['$match' => $filter],
        ['$project' => ['_id' => 0, 'impact' => 1]],
        ['$bucket' => [
            'groupBy' => '$impact',
            'boundaries' => $x,
            'default' => 0
        ]],
        ['$project' => ['_id' => 0, 'x' => '$_id', 'y' => '$count']],
    ])->toArray();

    array_unshift($x, 0);

    $result = [
        'x' => $x,
        'y' => array_fill(0, $max_impact + 1, 0),
        'labels' => $x,
    ];
    foreach ($data as $i => $datum) {
        $result['y'][$datum['x']] = $datum['y'];
    }

    echo return_rest($result, count($result));
});



Route::get('/api/dashboard/activity-chart', function () {
    include(BASEPATH . '/php/init.php');

    $filter = ['year' => ['$gte' => $Settings->get('startyear')]];
    if (isset($_GET['user'])) {
        $filter['authors.user'] = $_GET['user'];
    }

    $result = [];
    $years = [];
    for ($i = $Settings->get('startyear'); $i <= CURRENTYEAR; $i++) {
        $years[] = strval($i);
    }
    $result['labels'] = $years;
    $data = $osiris->activities->aggregate([
        ['$match' => $filter],
        [
            '$group' => [
                '_id' => [
                    'type' => '$type',
                    'year' => '$year'
                ],
                'count' => ['$sum' => 1],
            ]
        ],
        ['$project' => ['_id' => 0, 'type' => '$_id.type', 'year' => '$_id.year', 'count' => 1]],
        ['$sort' => ['year' => 1]],
        [
            '$group' => [
                '_id' => '$type',
                'x' => ['$push' => '$year'],
                'y' => ['$push' => '$count'],

            ]
        ],

        // ['$project' => ['_id' => 0, 'data'=>['$arrayToObject' => ['$literal' =>  [
        //     '$x', '$y'
        // ]]]]],
    ])->toArray();

    // dump($data);

    $result['data'] = [];
    foreach ($data as $d) {
        $group = $Settings->getActivities($d['_id']);
        $element = [
            'label' => $group['name'],
            'backgroundColor' => $group['color'] . '95',
            'borderColor'=> '#464646', 
            'borderWidth'=> 1,
            'borderRadius'=> 4,
            'data' => [],
        ];
        foreach ($years as $y) {
            $i = array_search($y, DB::doc2Arr($d['x']));
            if ($i === false) $v = 0;
            else $v = $d['y'][$i];

            $element['data'][] = $v;
        }
        $result['data'][] = $element;
    }

    echo return_rest($result, count($result));
});


Route::get('/api/dashboard/project-timeline', function () {
    include(BASEPATH . '/php/init.php');

    $filter = ['status' => 'approved'];
    if (isset($_GET['user'])) {
        $filter['persons.user'] = $_GET['user'];
    }

    $result = $osiris->projects->aggregate([
        ['$match' => $filter],
        ['$unwind' => '$persons'],
        ['$match' => $filter],
        ['$sort' => ['start' => 1]]
    ])->toArray();
    echo return_rest($result, count($result));
});


// helper function for network chord chards
function combinations($array)
{
    $results = array();
    foreach ($array as $a)
        foreach ($array as $b) {
            $t = [$a, $b];
            sort($t);
            if ($a == $b || in_array($t, $results)) continue;
            $results[] = $t;
        }
    return $results;
}

Route::get('/api/dashboard/department-network', function () {
    include(BASEPATH . '/php/init.php');

    $dept_filter = $_GET['dept'] ?? null;
    $lvl = 1;
    if (isset($_GET['level'])) $lvl = intval($_GET['level']);
    if (!empty($dept_filter)) $lvl = $Groups->getLevel($dept_filter);

    $departments = array_filter($Groups->groups, function ($a) use ($lvl) {
        return true;
        // return ($a['level'] ?? '') == $lvl;
    });

    $dept_users = [];
    foreach (array_column($departments, 'id') as $id) {
        $dept_users[$id] = [];
    }
    $users = [];
    $warnings = [];
    foreach ($osiris->persons->find() as $person) {
        if (!isset($person['depts'])) continue;
        $d = [];
        foreach ($person['depts'] as $key) {
            // get parent dept
            $p = $Groups->getParents($key, true);
            if (!isset($p[$lvl])) $p = end($p);
            else $p = $p[$lvl];
            if (!in_array($p, $d)) {
                if (!empty($d)) $warnings[] =  $person['displayname'] . ' has multiple associations.';
                $d[] = $p;
                $dept_users[$p][] = $person['username'];
                $users[$person['username']] = $p;
            }
        }
    }


    // select activities from database
    $filter = [];
    if (isset($_GET['type']))
        $filter['type'] = $_GET['type'];
    if (!empty($dept_filter)) {
        $filter['authors.user'] = ['$in' => $dept_users[$dept_filter] ?? []];
    }
    if (isset($_GET['year'])) {
        $filter['year'] = $_GET['year'];
    } else {
        // past 5 years is default
        $filter['year'] = ['$gte' => CURRENTYEAR - 4];
    }
    if (isset($_GET['activity'])) {
        //overwrite
        $filter = ['_id' => DB::to_ObjectID($_GET['activity'])];
    }
    $activities = $osiris->activities->find($filter);
    $activities = $activities->toArray();


    // generate graph json
    $combinations = [];

    $labels = $departments;
    foreach ($departments as $dept) {
        $labels[$dept['id']]['count'] = 0;
    }

    foreach ($activities as $doc) {
        $authors = [];
        foreach ($doc['authors'] as $aut) {
            if (!($aut['aoi'] ?? false) || empty($aut['user']) || !array_key_exists($aut['user'], $users)) continue;

            $id = $aut['user'];

            // get top level unit
            $dept = $users[$id];

            if (!empty($dept) && !in_array($dept, $authors)) {
                if (!isset($labels[$dept])) {
                    $labels[$dept] = $Groups->getGroup($dept);
                    $labels[$dept]['count'] = 0;
                }
                $labels[$dept]['count']++;
                $authors[] = $dept;
            }
        }
        if (count($authors) == 1)
            $combinations = array_merge($combinations, [[$authors[0], $authors[0]]]);
        else
            $combinations = array_merge($combinations, combinations($authors));
    }

    // remove depts without publications
    $labels = array_filter($labels, function ($d) {
        return $d['count'] !== 0;
    });

    // add index (needed for following steps)
    $i = 0;
    foreach ($labels as $key => $val) {
        $labels[$key]['index'] = $i++;
    }

    // init matrix of n x n
    $matrix = array_fill(0, count($labels), 0);
    $matrix = array_fill(0, count($labels), $matrix);

    // fill matrix based on all combinations
    foreach ($combinations as $c) {
        $a = $labels[$c[0]]['index'];
        $b = $labels[$c[1]]['index'];

        $matrix[$a][$b] += 1;
        if ($a != $b)
            $matrix[$b][$a] += 1;
    }

    echo return_rest([
        'matrix' => $matrix,
        'labels' => $labels,
        'warnings' => $warnings
    ], count($labels));
});



Route::get('/api/dashboard/author-network', function () {
    include(BASEPATH . '/php/init.php');

    $scientist = $_GET['user'] ?? '';
    $selectedUser = $osiris->persons->findone(['username' => $scientist]);
    // generate graph json
    $labels = [];
    $combinations = [];
    $filter = ['authors.user' => $scientist, 'type' => 'publication'];

    if (isset($_GET['year'])) {
        $filter['year'] = $_GET['year'];
    } else {
        // past 5 years is default
        $filter['year'] = ['$gte' => CURRENTYEAR - 4];
    }

    $activities = $osiris->activities->find($filter);
    $activities = $activities->toArray();
    $N = count($activities);

    foreach ($activities as $doc) {
        $authors = [];
        foreach ($doc['authors'] as $aut) {
            if (empty($aut['user'])) continue;
            //!($aut['aoi'] ?? false) || 

            $id = $aut['user'];
            if (array_key_exists($id, $labels)) {
                // $name = $labels[$id]['name'];
                $labels[$id]['count']++;
            } else {
                $name = $osiris->persons->findone(['username' => $aut['user']]);
                if (empty($name)) continue;
                $abbr_name = Document::abbreviateAuthor($name['last'], $name['first'], true, ' ');

                // get top level unit
                $dept = [];
                if (!empty($name['depts'] ?? []) && count($name['depts']) !== 0) {
                    $d = $Groups->getParents($name['depts'][0]);
                    $dept = $Groups->getGroup($d[0]);
                }

                $labels[$id] = [
                    'name' => $abbr_name,
                    'id' => $id,
                    'user' => $aut['user'],
                    'dept' => $dept,
                    'count' => 1
                ];
            }
            $authors[] = $id;
        }

        $combinations = array_merge($combinations, combinations($authors));
    }
    $departments = array_filter($Groups->groups, function ($a) {
        return ($a['level'] ?? '') == 1;
    });
    $depts = array_column($departments, 'id');
    usort($depts, function ($a, $b) use ($selectedUser) {
        if (in_array($a, DB::doc2Arr($selectedUser['depts']))) return -1;
        return 1;
    });
    // $labels = array_filter($labels, function ($a) {
    //     return !empty($a['dept']);
    // });
    uasort($labels, function ($a, $b) use ($depts) {
        $a = array_search($a['dept']['id'] ?? '', $depts);
        $b = array_search($b['dept']['id'] ?? '', $depts);
        if ($b === false) return -1;
        if ($a === false) return 1;
        return ($a < $b ? -1 : 1);
    });

    $i = 0;
    foreach ($labels as $key => $val) {
        $labels[$key]['index'] = $i++;
    }

    $matrix = array_fill(0, count($labels), 0);
    $matrix = array_fill(0, count($labels), $matrix);

    foreach ($combinations as $c) {
        $a = $labels[$c[0]]['index'];
        $b = $labels[$c[1]]['index'];

        $matrix[$a][$b] += 1;
        $matrix[$b][$a] += 1;
    }


    echo return_rest([
        'matrix' => $matrix,
        'labels' => $labels
    ], count($labels));
});


Route::get('/api/dashboard/activity-authors', function () {
    include(BASEPATH . '/php/init.php');

    if (!isset($_GET['activity'])) return [];

    $lvl = 1;

    // select activities from database
    $filter = ['_id' => DB::to_ObjectID($_GET['activity'])];
    $doc = $osiris->activities->findOne($filter);

    $depts = [];

    if (isset($doc['authors']) && !empty($doc['authors'])) {
        // $users = array_column(DB::doc2Arr($doc['authors']), 'user');
        foreach ($doc['authors'] as $a) {
            $user = $a['user'] ?? null;
            $name = Document::abbreviateAuthor($a['last'], $a['first'] ?? null);
            if (empty($user)) {
                $depts['external'][] = $name;
                continue;
            }

            // get person group
            $person = $osiris->persons->findOne(['username' => $user]);
            if (!isset($person['depts'])) continue;
            $d = [];
            foreach ($person['depts'] as $key) {
                // get parent dept
                $p = $Groups->getParents($key, true);
                if (!isset($p[$lvl])) $p = end($p);
                else $p = $p[$lvl];
                if (!in_array($p, $d)) {
                    if (!empty($d)) $warnings[] =  $person['displayname'] . ' has multiple associations.';
                    $d[] = $p;
                    $dept_users[$p][] = $person['username'];
                    $users[$person['username']] = $p;
                }
            }
            $depts[$d[0]][] = $name;
        }
    }

    // $depts = array_count_values($depts);

    $labels = [];
    $y = [];
    $colors = [];
    $persons = [];
    foreach ($depts as $key => $value) {
        if ($key == 'external') {
            $labels[] = 'External partners';
            $colors[] = '#00000095';
        } else {
            $group = $Groups->getGroup($key);
            $labels[] = $group['name'];
            $colors[] = $group['color'] . '95';
        }
        $y[] = count($value);
        $persons[] = $value;
    }
    echo return_rest([
        'y' => $y,
        'colors' => $colors,
        'labels' => $labels,
        'persons' => $persons
    ], count($labels));
});



Route::get('/api/dashboard/concept-search', function () {
    include(BASEPATH . '/php/init.php');

    if (!isset($_GET['concept'])) return return_rest([], 0);
    $name = $_GET['concept'];
    $active_users = $osiris->persons->distinct('username', ['is_active' => true]);
    $concepts = $osiris->activities->aggregate(
        [
            ['$match' => ['concepts.display_name' => $name]],
            ['$project' => ['authors' => 1, 'concepts' => 1]],
            ['$unwind' => '$concepts'],
            ['$match' => ['concepts.display_name' => $name]],
            ['$unwind' => '$authors'],
            ['$match' => ['authors.user' => ['$in' => $active_users]]],
            [
                '$group' => [
                    '_id' => '$authors.user',
                    'total' => ['$sum' => 1],
                    'totalScore' => ['$sum' => '$concepts.score'],
                    'author' => ['$first' => '$authors']
                ]
            ],
            // ['$project' => ['score' => ['$divide' =>], 'concepts' => 1]],
            ['$match' => ['totalScore' => ['$gte' => 1]]],
            ['$sort' => ['author.last' => 1]],
        ]
    )->toArray();

    // $data = [];
    $data = [
        "x" => [],
        "y" => [],
        "mode" => 'markers',
        "marker" => [
            "size" => [],
            "sizemode" => 'area',
            'showlegend'=>true
        ],
        'text' => [],
        'hovertemplate' => '%{x}<br>%{y}<br> Total Score: %{text}'
    ];
    foreach ($concepts as $i => $c) {
        // $author = Document::abbreviateAuthor($c['author']['last'], $c['author']['first'], true, ' ');
        $author = $DB->getNameFromId($c['_id'], true, true);
        // $data[] = [
        //     "x" => $name,
        //     "y" => $author,
        //     "r" => $c['totalScore']
        // ];
        $data['y'][] = $name;
        $data['x'][] = $author;
        $s = round($c['totalScore'], 1);
        $data['text'][] = "$s<br>$c[total] activities";
        $data['marker']['size'][] = $c['totalScore'] * 10;
    }

    echo return_rest($data, count($data['x']));
});

/**
 * Official interface API endpoints
 */

/**
 * @apiDefine error404 Error 404
 */

/**
 * @apiDefine Activity endpoints
 *
 * The following endpoints are used for querying activities.
 */

/**
 * @api {get} /media All media
 * @apiName GetAllMedia
 * @apiGroup Medium
 * 
 * @apiParam {Integer} [limit] Max. number of results
 * @apiParam {Integer} [offset] Offset of results
 *
 * @apiSampleRequest /download/publications
 * 
 * @apiSuccess {String} id Unique ID of the medium.
 * @apiSuccess {String} name  Name of the medium.
 * @apiSuccess {Boolean} complex_medium True if the medium is complex
 * @apiSuccess {String} source Collection where the medium originates from 
 * @apiSuccess {String} link Original URL
 * @apiSuccess {Float} min_pH Min. final pH
 * @apiSuccess {Float} max_pH Max final pH
 * @apiSuccess {String} reference URL for original reference (if available)
 * @apiSuccess {String} description Description or additional information (if available)
 * @apiSuccessExample {json} Example data:
 * {
        "id": "1",
        "name": "TEST"
    }
 */
Route::get('/data/activities', function () {
    $result = array();
    echo return_rest($result, count($result));
});
