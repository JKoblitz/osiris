<?php
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
 * @apiDefine error404 Error 404
 */

/**
 * @apiDefine Medium Media endpoints
 *
 * The following endpoints consider media information.
 * You can request a list of all media, the whole medium recipe containing
 * all solutions, the molecular composition of a medium, or all strains
 * that grow on the medium.
 */

/**
 * @api {get} /media All media
 * @apiName GetAllMedia
 * @apiGroup Medium
 * 
 * @apiParam {Integer} [limit] Max. number of results
 * @apiParam {Integer} [offset] Offset of results
 *
 * @apiSampleRequest https://mediadive.dsmz.de/download/publications
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
 * [
    {
        "id": "119a",
        "name": "METHANOBREVIBACTER MEDIUM",
        "complex_medium": 1,
        "source": "DSMZ",
        "link": "https://www.dsmz.de/microorganisms/medium/pdf/DSMZ_Medium119a.pdf",
        "min_pH": 6.8,
        "max_pH": 7,
        "reference": null,
        "description": null
    },
    ...
]
 */
// Route::get('/api/publications', function () {
//     $result = array();
//     echo return_rest($result, count($result));
// });


Route::get('/api/activities', function () {
    include_once BASEPATH . "/php/_db.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    if (isset($_GET['json'])) {
        $filter = json_decode($_GET['json']);
    }
    $result = $osiris->activities->find($filter)->toArray();


    if (isset($_GET['formatted']) && $_GET['formatted']) {
        include_once BASEPATH . "/php/format.php";
        $table = [];
        $Format = new Format(true, 'web');

        foreach ($result as $doc) {
            $table[] = [
                'id' => strval($doc['_id']),
                'activity' => $Format->format($doc),
                'icon' => activity_icon($doc)
            ];
        }

        $result = $table;
    }

    echo return_rest($result, count($result));
});


Route::get('/api/users', function () {
    include_once BASEPATH . "/php/_db.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    if (isset($_GET['json'])) {
        $filter = json_decode($_GET['json']);
    }
    $result = $osiris->users->find($filter)->toArray();

    echo return_rest($result, count($result));
});


Route::get('/api/reviews', function () {
    include_once BASEPATH . "/php/_db.php";
    $filter = [];
    if (isset($_GET['filter'])) {
        $filter = $_GET['filter'];
    }
    $filter['type'] = 'review';
    $result = $osiris->activities->find($filter)->toArray();

    $reviews = [];
    foreach ($result as $doc) {
        if (!array_key_exists($doc['user'], $reviews)) {
            $u = getUserFromId($doc['user']);
            $reviews[$doc['user']] = [
                'User' => $doc['user'],
                'Name' => $u['displayname'],
                'Editor' => 0,
                'Editorials' => [],
                'Reviewer' => 0,
                "Reviews" => []
            ];
        }
        switch (strtolower($doc['role'] ?? 'review')) {
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
    include_once BASEPATH . "/php/_db.php";
    $filter = [];
    if (isset($_GET['search'])) {
        $j = new \MongoDB\BSON\Regex('^' . trim($_GET['search']), 'i');
        $filter = ['$or' =>  [
            ['journal' => ['$regex' => $j]],
            ['issn' => $_GET['search']]
        ]];
    }
    $result = $osiris->journals->find($filter)->toArray();


    echo return_rest($result, count($result));
});

Route::get('/api/journals', function () {
    include_once BASEPATH . "/php/_db.php";
    $journals = $osiris->journals->find()->toArray();
    $result = ['data' => []];
    // $i = 0;
    $activities = $osiris->activities->find(['journal_id' => ['$exists' => 1]], ['projection' => ['journal_id' => 1]])->toArray();
    $activities = array_column($activities, 'journal_id');
    $activities = array_count_values($activities);
    foreach ($journals as $doc) {
        // if ($i++ > 100) break;
        $result['data'][] = [
            'id' => strval($doc['_id']),
            'name' => $doc['journal'],
            'abbr' => $doc['abbr'],
            'issn' => implode(', ', $doc['issn']->bsonSerialize()),
            'if' => impact_from_year($doc, intval(CURRENTYEAR) - 1) ?? '',
            'count' => $activities[strval($doc['_id'])] ?? 0
        ];
    }

    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
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
