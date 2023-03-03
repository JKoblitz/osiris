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
 * @apiSampleRequest https://osiris.dsmz.de/download/publications
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
        "id": "1",
        "name": "TEST"
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


Route::get('/api/html', function () {
    include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/format.php";
    $Format = new Format(true, 'dsmz.de');
    $Format->full = true;
    $Format->abbr_journal = true;

    $result = [];
    $docs = $osiris->activities->find([
        'type' => 'publication', 'authors.aoi' => ['$in' => [true, 1, '1']],
        'year'=> ['$gte' => 2023]
    ]);


    foreach ($docs as $i => $doc) {
        if (isset($_GET['limit']) && $i >= $_GET['limit']) break;

        $depts = getDeptFromAuthors($doc['authors']);


        $link = null;
        if (!empty($doc['doi'] ?? null)) {
            $link = "https://dx.doi.org/" . $doc['doi'];
        } elseif (!empty($doc['pubmed'] ?? null)) {
            $link = "https://www.ncbi.nlm.nih.gov/pubmed/" . $doc['pubmed'];
        }
        $result[] = [
            'id' => strval($doc['_id']),
            'html' => $Format->format($doc),
            'year' => $doc['year'] ?? null,
            'departments' => $depts,
            'link' => $link
        ];
    }

    echo return_rest($result, count($result));
});

Route::get('/api/all-activities', function () {
    include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/format.php";

    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");

    $user = $_GET['user'] ?? $_SESSION['username'];
    $page = $_GET['page'] ?? 'all-activities';
    $highlight = true;
    if ($page == 'my-activities') {
        $highlight = $user;
    }
    $Format = new Format($highlight);

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
        $type = $doc['type'];
        $q = getQuarter($doc);
        $y = getYear($doc);
        $quarter = $endQuarter = $y . "Q" . $q;

        if (($_GET['display_activities'] ?? 'web') == 'web') {
            $format = $Format->formatShort($doc);
        } else {
            $format = $Format->format($doc);
        }

        $datum = [
            'quarter' => $y . 'Q' . $q,
            'type' => activity_icon($doc) . '<span class="hidden">' . $type . " " . activity_title($doc) . '</span>',
            'activity' => $format,
            'links' => ''
        ];


        $datum['quarter'] .= '<span class="hidden"> ' . $doc['month'] . "M" . $doc['year'] . "Y";
        if (isset($doc['end']) && !empty($doc['end'])) {

            $em = $doc['end']['month'];
            $ey = $doc['end']['year'];
            $sm = $doc['month'];
            $sy = $doc['year'];
            for ($i = $y; $i <= $ey; $i++) {
                $endMonth = $i != $ey ? 11 : $em - 1;
                $startMon = $i === $y ? $sm - 1 : 0;
                for ($j = $startMon; $j <= $endMonth; $j = $j > 12 ? $j % 12 || 11 : $j + 1) {
                    $month = $j + 1;
                    $displayMonth = $month < 10 ? '0' + $month : $month;
                    $datum['quarter'] .= " " . $displayMonth . "M" . $i . "Y ";
                    // QUARTER:
                    $endQuarter = $i . "Q" . ceil($displayMonth / 3);
                }
            }
        }
        $datum['quarter'] .= '</span>';
        if ($quarter != $endQuarter) {
            $datum['quarter'] .= "-" . $endQuarter;
        }

        $datum['links'] =
            "<a class='btn btn-link btn-square' href='" . ROOTPATH . "/activities/view/$id'>
                <i class='icon-activity-search'></i>
            </a>";
        $useractivity = isUserActivity($doc, $user);
        if ($useractivity) {
            $datum['links'] .= " <a class='btn btn-link btn-square' href='" . ROOTPATH . "/activities/edit/$id'>
                <i class='icon-activity-pen'></i>
            </a>";
        }
        $datum['links'] .= "<button class='btn btn-link btn-square' onclick='addToCart(this, \"$id\")'>
            <i class='" . (in_array($id, $cart) ? 'fas fa-cart-plus text-success' : 'far fa-cart-plus') . "'></i>
        </button>";
        $result[] = $datum;
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
        $j = new \MongoDB\BSON\Regex(trim($_GET['search']), 'i');
        $filter = ['$or' =>  [
            ['journal' => ['$regex' => $j]],
            ['issn' => $_GET['search']]
        ]];
    }
    $result = $osiris->journals->find($filter)->toArray();
    echo return_rest($result, count($result));
});

Route::get('/api/journals', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
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
            'if' => latest_impact($doc) ?? '',
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



Route::get('/api/wos-starter', function () {

    if (!isset($_GET['issn'])) die("no issn given");
    $issn = $_GET['issn'];

    $settings = file_get_contents(BASEPATH . "/apis.json");
    $settings = json_decode($settings, true, 512, JSON_NUMERIC_CHECK);
    foreach ($settings as $api) {
        if ($api['id'] == 'wos-starter') {
            $apikey = $api['key'];

            if (empty($apikey)) {
                die("API key is missing.");
            }
            // $filter = [];
            $url = "https://api.clarivate.com/apis/wos-starter/v1/journals";
            $url .= "?issn=" . $issn;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
                "X-ApiKey: $apikey"
            ]);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            $result = json_decode($result, true);
            echo return_rest($result, count($result));
        }
    }
});
