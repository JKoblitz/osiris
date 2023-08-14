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
        if ($depts instanceof MongoDB\Model\BSONArray ) {
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
        $sm = intval($doc['month']);
        $sy = intval($doc['year']);
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
            "<a class='btn btn-link btn-square' href='" . ROOTPATH . "/activities/view/$id'>
                <i class='ph ph-regular ph-arrow-fat-line-right'></i>
            </a>";
        $useractivity = $DB->isUserActivity($doc, $user);
        if ($useractivity) {
            $datum['links'] .= " <a class='btn btn-link btn-square' href='" . ROOTPATH . "/activities/edit/$id'>
                <i class='ph ph-regular ph-pencil-simple-line'></i>
            </a>";
        }
        $datum['links'] .= "<button class='btn btn-link btn-square' onclick='addToCart(this, \"$id\")'>
            <i class='" . (in_array($id, $cart) ? 'ph-fill ph-shopping-cart ph-shopping-cart-plus text-success' : 'ph ph-regular ph-shopping-cart ph-shopping-cart-plus') . "'></i>
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

