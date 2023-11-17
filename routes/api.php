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
            $rendered = $DB->renderActivities(['_id' => $id]);
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
    include_once BASEPATH . "/php/Document.php";

    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");

    $user = $_GET['user'] ?? $_SESSION['username'];
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
            $rendered = $DB->renderActivities(['_id' => $id]);
        }

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
            'type' => $rendered['icon'] . '<span class="hidden">' . $type . " " . $rendered['title'] . '</span>',
            'activity' => $format,
            'links' => '',
            'search-text' => $format_full,
            'start' => $sy . '-' . ($sm < 10 ? '0' : '') . $sm . '-' . ($doc['day'] ?? '01'),
            'end' => $ey . '-' . ($em < 10 ? '0' : '') . $em . '-' . ($doc['day'] ?? '01'),
            'departments' => implode(', ', $depts),
            'epub' => (isset($doc['epub']) && boolval($doc['epub']) ? 'true' : 'false')
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

        $datum['links'] =
            "<a class='btn link square' href='" . ROOTPATH . "/activities/view/$id'>
                <i class='ph ph-arrow-fat-line-right'></i>
            </a>";
        $useractivity = $DB->isUserActivity($doc, $user);
        if ($useractivity) {
            $datum['links'] .= " <a class='btn link square' href='" . ROOTPATH . "/activities/edit/$id'>
                <i class='ph ph-pencil-simple-line'></i>
            </a>";
        }
        $datum['links'] .= "<button class='btn link square' onclick='addToCart(this, \"$id\")'>
            <i class='" . (in_array($id, $cart) ? 'ph ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-shopping-cart ph-shopping-cart-plus') . "'></i>
        </button>";
        $result[] = $datum;
    }
    echo return_rest($result, count($result));
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
                $color = ($c['role'] == 'Partner' ? '#ECAF00' : '#B61F29');
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
            'backgroundColor' => $group['color'].'95',
            'data' => []
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