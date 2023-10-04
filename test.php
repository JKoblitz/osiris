<?php


Route::get('/journals-open-access', function () {
    // use the API of DOAJ to get if a journal is open access
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    $journals = $osiris->journals->find([]);
    foreach ($journals as $journal) {
        $name = $journal['journal'];

        // if (isset($journal['oa']) &&$journal['oa'] !== false && $journal['oa'] > 0){
        //     echo "$name ".$journal['oa']." <br>";
        //         $updateResult = $osiris->activities->updateMany(
        //             ['journal_id' => strval($journal['_id']), 'year' => ['$gt' => $journal['oa']], 'type'=> 'publication'],
        //             ['$set' => ['open_access' => true]]
        //         );
        //         echo $updateResult->getModifiedCount(). " activities updated<br>";
        // }

        if (isset($journal['oa'])) continue;
        $oa = $journal['oa'] ?? false;
        // if ($oa !== false) continue;
        echo "<h5>$name</h5>";

        $issn = $journal['issn'];
        // if ($issn instanceof MongoDB\Model\BSONArray) {
        //     $issn = $issn->bsonSerialize();
        // }
        $query = [];
        foreach ($issn as $i) {
            $query[] = "issn:" . $i;
        }
        if (empty($query)) continue;
        $query = implode(' OR ', $query);
        $url = "https://doaj.org/api/search/journals/" . $query;
        // $url = "https://mediadive.dsmz.de/rest/stats";
        // dump($url);

        $response = CallAPI('GET', $url);
        $json = json_decode($response, true);


        if (!empty($json['results'] ?? null) && isset($json['results'][0]['bibjson'])) {

            $n = count($json['results']);
            $index = 0;
            if ($n > 1) {
                $compare_name = strtolower($name);
                $compare_name = explode('(', $compare_name)[0];
                $compare_name = trim($compare_name);
                echo ("$n results.<br>");
                foreach ($json['results'] as $i => $res) {
                    echo $res['bibjson']['title'] . "<br>";
                    if (isset($res['bibjson']['is_replaced_by'])) continue;
                    $index = $i;
                    if (strtolower($res['bibjson']['title']) == $compare_name) {
                        // echo "HIT <br>";
                        break;
                    }
                }
                echo $index;
            }
            $r = $json['results'][$index]['bibjson'];
            $oa = $r['oa_start'];
            dump($oa);
        }

        $updateResult = $osiris->journals->updateOne(
            ['_id' => $journal['_id']],
            ['$set' => ['oa' => $oa]]
        );

        if (!empty($oa)) {
            $updateResult = $osiris->activities->updateMany(
                ['journal_id' => strval($journal['_id']), 'year' => ['$gt' => $oa], 'type' => 'publication'],
                ['$set' => ['open_access' => true]]
            );
            echo $updateResult->getModifiedCount() . " activities updated<br>";
        }

        // $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // // curl_setopt($ch, CURLOPT_HTTPHEADER, [
        // //     'Content-Type: application/json'
        // //   ]);
        // $response = curl_exec($ch);
        // dump($response);
        // curl_close($ch);
        // sleep(1);
    }
    include BASEPATH . "/footer.php";
});


// Route::get('/fix-journals', function () {
//     include_once BASEPATH . "/php/_config.php";
//     include_once BASEPATH . "/php/_db.php";

//     include BASEPATH . "/header.php";

//     if (!$USER['is_admin']) die('You have no permission to be here.');
//     // $updateResult = $osiris->journals->updateMany(
//     //     [],
//     //     ['$set' => ['open_access' => false]]
//     // );
//     // dump($updateResult);

//     $cursor = $osiris->activities->find(['journal' => ['$exists' => true]]);
//     $journalList = [];
//     foreach ($cursor as $doc) {
//         $j_id = $doc['journal_id'] ?? null;
//         $name = $doc['journal'];
//         if (is_ObjectID($j_id)) {
//             continue;
//         }
//         $j_id = null;
//         echo '<br>';
//         echo "<a target='_blank' href='" . ROOTPATH . "/activities/view/$doc[_id]'>" . activity_badge($doc) . "</a>";
//         echo "<br>";
//         echo "Journal: " . $name . "<br>";
//         // echo "ISSN: ".($doc['issn']??'') . "<br>";
//         echo "Journal-ID: " . ($j_id) . "<br>";



//         if (str_contains($name, '(MDPI)')) {
//             $name = str_replace('(MDPI)', '', $name);
//         }

//         $j = new \MongoDB\BSON\Regex('^' . trim($name) . '$', 'i');
//         $journal = $osiris->journals->findOne([
//             '$or' => [
//                 ['journal' => ['$regex' => $j]],
//                 ['abbr' => ['$regex' => $j]]
//             ]
//         ]);
//         if (!empty($journal)) {
//             $j_id = strval($journal['_id']);
//             $name = $journal['journal'];
//         }

//         $updateResult = $osiris->activities->updateOne(
//             ['_id' => $doc['_id']],
//             ['$set' => ['journal' => $name, 'journal_id' => $j_id]]
//         );
//         if ($updateResult->getModifiedCount() > 0) {
//             echo "UPDATED Journal: " . $name . "<br>";
//             // echo "ISSN: ".($doc['issn']??'') . "<br>";
//             echo "Journal-ID: " . ($j_id) . "<br>";
//         } else {
//             $journalList[] = trim($name);
//         }
//     }

//     $journalList = array_count_values($journalList);
//     arsort($journalList);
//     dump($journalList, true);

//     include BASEPATH . "/footer.php";
// });


Route::get('/lom-test/([A-Za-z0-9]*)', function ($user) {
    include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/_lom.php";

    $LOM = new LOM($user, $osiris);
    $result = array();

    // publications
    $cursor = $osiris->publications->find([
        '$or' => [
            ['authors.user' => $user], ['editors.user' => $user]
        ],
        'year' => SELECTEDYEAR
    ]);
    foreach ($cursor as $doc) {
        $result[] = $LOM->publication($doc);
    }

    // posters
    $cursor = $osiris->posters->find([
        'authors.user' => $user,
        "start.year" => SELECTEDYEAR
    ]);
    foreach ($cursor as $doc) {
        $result[] = $LOM->poster($doc);
    }

    // lectures
    $cursor = $osiris->lectures->find([
        'authors.user' => $user,
        "start.year" => SELECTEDYEAR
    ]);
    foreach ($cursor as $doc) {
        $result[] = $LOM->lecture($doc);
    }

    // reviews
    $cursor = $osiris->reviews->find([
        'user' => $user,
        '$or' => array(
            [
                "start.year" => array('$lte' => SELECTEDYEAR),
                '$and' => array(
                    ['$or' => array(
                        ['end.year' => array('$gte' => SELECTEDYEAR)],
                        ['end' => null]
                    )],
                    ['$or' => array(
                        ['type' => 'misc', 'iteration' => 'annual'],
                        ['type' => 'review', 'role' =>  ['$in'=> ['Editor', 'editorial']]],
                    )]
                )
            ],
            ["dates.year" => SELECTEDYEAR]
        )
    ]);
    foreach ($cursor as $doc) {
        $result[] = $LOM->review($doc);
    }

    // miscs
    $cursor = $osiris->miscs->find([
        'authors.user' => $user,
        "dates.start.year" => array('$lte' => SELECTEDYEAR),
        '$or' => array(
            ['dates.end.year' => array('$gte' => SELECTEDYEAR)],
            ['dates.end' => null]
        )
    ]);
    foreach ($cursor as $doc) {
        $result[] = $LOM->misc($doc);
    }

    echo json_encode(array("LOM" => array_sum(array_column($result, 'lom')), "details" => $result));
});


Route::get('/test/(users|activities|journals)/([a-zA-Z0-9]*)', function ($col, $id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    if ($col == 'users') {
        $collection = $osiris->users;
    } elseif ($col == 'journals') {
        $collection = $osiris->journals;
    } else {
        $collection = $osiris->activities;
    }

    if (is_numeric($id)) {
        $id = intval($id);
    } else if ($col != 'users') {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    $data = $collection->findone(['_id' => $id]);
    dump($data, true);
});

Route::get('/testing', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $updateResult = $osiris->activities->updateMany(
        ['doi' => ['$regex' => '10.1111/1462-2920']],
        ['$set' => ['journal' => 'Environmental microbiology', 'journal_id' => '6364d153f7323cdc825310c0', 'journal_abbr' => 'Environ Microbiol', "issn" => ["1462-2920", "1462-2912"], 'impact' => null, 'open_access' => true]]
    );
    echo "Updated: " . $updateResult->getModifiedCount() . "<br>";
    // $names = [
    //     ['Nubel', 'Nübel', 'uln14'],
    //     ['Ozturk', 'Öztürk', 'bas18'],
    //     ['Goker', 'Göker', 'mgo08'],
    //     ['Sproer', 'Spröer', 'ckc'],
    //     ['Pauker', 'Päuker', 'opa'],
    //     ['Steenpass', 'Steenpaß', 'las20'],
    //     ['Carbasse', 'Sardà Carbasse', 'joc18'],
    // ];

    // foreach ($names as $n) {
    //     # code...
    //     $updateResult = $osiris->activities->updateMany(
    //         ['authors.last' => $n[0]],
    //         ['$set' => ['authors.$.last' => $n[1], 'authors.$.user' => $n[2]]]
    //     );
    //     echo $n[0] . " " . $updateResult->getModifiedCount() . "<br>";
    // }

    // $updateResult = $osiris->users->updateMany(
    //     ['is_controlling' => 1],
    //     ['$set' => ['is_controlling' => true]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_scientist' => 1],
    //     ['$set' => ['is_scientist' => true]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_leader' => 1],
    //     ['$set' => ['is_leader' => true]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_active' => 1],
    //     ['$set' => ['is_active' => true]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_controlling' => 0],
    //     ['$set' => ['is_controlling' => false]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_scientist' => 0],
    //     ['$set' => ['is_scientist' => false]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_leader' => 0],
    //     ['$set' => ['is_leader' => false]]
    // );
    // $updateResult = $osiris->users->updateMany(
    //     ['is_active' => 0],
    //     ['$set' => ['is_active' => false]]
    // );
    // echo "All " . $updateResult->getModifiedCount() . "<br>";

    // $updateResult = $osiris->activities->updateMany(
    //     ['authors.last' => 'Ozturk'],
    //     ['$set' => ['authors.$.last'=>'Öztürk', 'authors.$.user'=>'bas18']]
    // );
    // addUserActivity();
    // $collection = $osiris->users;
    // $data = $collection->findone(['_id'=> $_SESSION['username']]);
    // dump($data, true);

}, 'admin');


Route::get('/mongo', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";

    $levels = '[
        {
            "level": 1,
            "step": 1,
            "de": "* hat das erste Mal eine Aktivität eingetragen.",
            "en": "* has created an activity for the first time."
          },
          {
            "level": 2,
            "step": 50,
            "de": "* hat mehr als 50 Aktivitäten eingetragen.",
            "en": "* has created more than 50 activities."
          },
          {
            "level": 3,
            "step": 200,
            "de": "* hat mehr als 200 Aktivitäten eingetragen.",
            "en": "* has created more than 200 activities."
          },
          {
            "level": 4,
            "step": 500,
            "de": "* hat mehr als 500 Aktivitäten eingetragen.",
            "en": "* has created more than 500 activities."
          }
    ]';
    $levels = json_decode($levels, true);

    $updateResult = $osiris->achievements->updateOne(
        ['id' => 'create'],
        ['$set' => ['levels' => $levels]]
    );

    // $collection = $osiris->publications;
    // $document = $collection->findOne(['_id' => 21768]);
    // var_dump($document);
    echo "<div id='result'></div>";
    echo "<script src='" . ROOTPATH . "/js/osiris.js'></script>";
    include BASEPATH . "/footer.php";
});

Route::get('/test/betatest', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";

    $achievement = '{
        "id": "betatest",
        "icon": "person-star",
        "visible": false,
        "title": {
          "de": "Beta-Tester",
          "de_f": "Beta-Testerin",
          "en": "Beta Tester"
        },
        "levels": [
          {
            "level": 1,
            "step": 1,
            "de": "* hat am Betatest für OSIRIS teilgenommen. Danke.",
            "en": "* has enganged in the beta-testing for OSIRIS."
          }
        ],
        "maxlvl": 1
      }';
    $achievement = json_decode($achievement, true);

    $updateResult = $osiris->achievements->insertOne(
        $achievement
    );

    $ach =  [
        "id"=> "betatest",
        "level"=> 1,
        "achieved"=> "01.01.2023",
        "new"=> true
    ];

    $tester = ['ayu','cef18','cpo14','daf22','dok21','feb13','hef12','jaw21','jsi06','juk20','kah11','las20','lor15','men17','mip17','ulr22','uln14','yvm19'];

    foreach ($tester as $user) {
        $updateResult = $osiris->users->updateOne(
            ['_id' => $user],
            ['$push' => ['achievements' => $ach]]
        );
        dump([$user, $updateResult], true);
    }
    // $collection = $osiris->publications;
    // $document = $collection->findOne(['_id' => 21768]);
    // var_dump($document);
    // echo "<div id='result'></div>";
    // echo "<script src='" . ROOTPATH . "/js/osiris.js'></script>";
    include BASEPATH . "/footer.php";
});

Route::get('/info', function () {
    include BASEPATH . "/header.php";
    phpinfo();
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/test-scientist', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";

    $scientists = [
        "Overmann",
        "Steenpaß",
        "Pester",
        "Winter",
        "Gronow",
        "Abt",
        "Pukall",
        "Thiel",
        "Rohde",
        "Nouioui",
        "Verbarg",
        "Kirstein",
        "Pradella",
        "Bajerski",
        "Spring",
        "Quentmeier",
        "Hahnke",
        "Pommerenke",
        "Yurkov",
        "Dirks",
        "Menzel",
        "Felsch",
        "Steube",
        "Spröer",
        "Nübel",
        "Eberth",
        "Uphoff",
        "Riedel",
        "Göker",
        "Nagel",
        "Neumann-Schaal",
        "Krenz",
        "Rand",
        "Sikorski",
        "Gomez Escribano",
        "Petersen",
        "Knierim",
        "Margaria",
        "Bunk",
        "Mast",
        "Scholz",
        "Wittmann",
        "Baschien",
        "Huber-Fischer",
        "Boldt",
        "Päuker",
        "Wolf",
        "Dyksma",
        "Ngugi",
        "Gomes Vieira",
        "Marter",
        "Meyer Cifuentes",
        "Öztürk",
        "Kwant",
        "Feldhinkel",
        "Markesch",
        "Bergmann",
        "Witt",
        "Boustaji",
        "Halama",
        "Linnemann",
        "Leike",
        "Lindemann",
        "Gllareva",
        "Haake",
        "Kovalova",
        "Lissin",
        "Schober",
        "Ebeling",
        "Nuñez Vega",
        "Mandip",
        "Rolland",
        "Kolte",
        "Papendorf",
        "Moreira Martins",
        "Flocco",
        "Muñoz García ",
        "Ijaz",
        "Vasconcelos Rissi",
        "Barys",
        "Sett",
        "Sheat",
        "Bosviel",
        "Stickel",
        "Frentrup",
        "Koblitz",
        "Sicking",
        "Gehrke",
        "Lusznat",
        "Omoregbe",
        "Faggionato",
        "Bingert",
        "Degenhardt",
        "Freese",
        "Gierling",
        "Hagendorf",
        "Janßen",
        "Karger",
        "Lilienthal",
        "Meier-Kolthoff",
        "Reimer",
        "Sardà Carbasse",
    ];

    // $res = $osiris->users->find([
    //     'is_scientist' => true,
    //     'is_active' => true,
    //     'last' => ['$nin'=>$scientists]
    // ])->toArray();
    // foreach ($res as $r) {
    //     echo "$r[last], $r[first]<br>";
    // }
    $updateResult = $osiris->users->updateMany(
        [
            'is_scientist' => true,
            'is_active' => true,
            'last' => ['$nin'=>$scientists]
        ],
        ['$set' => ['is_scientist' => false]]
    );
    dump($updateResult);

    $res = $osiris->users->find([
        'is_scientist' => false,
        // 'is_active' => true,
        'last' => ['$in'=>$scientists]
    ])->toArray();
    foreach ($res as $r) {
        echo "<a href='".ROOTPATH."/user/edit/$r[username]'>$r[last], $r[first]</a><br>";
    }

    include BASEPATH . "/footer.php";
});
