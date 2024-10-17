<?php

/**
 * Routing for the Rest-API
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

function rest($data, $count = 0, $status = 200)
{
    $result = array();
    $limit = intval($_GET['limit'] ?? 0);
    if ($count == 0 && is_countable($data)) {
        $count = count($data);
    }

    if (!empty($limit) && $count > $limit && is_array($data)) {
        $offset = intval($_GET['offset'] ?? 0) || 0;
        $data = array_slice($data, $offset, min($limit, $count - $offset));
        $result += array(
            'limit' => $limit,
            'offset' => $offset
        );
    }

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
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

function help_getProject($osiris, $id)
{
    if (DB::is_ObjectID($id)) {
        $id = DB::to_ObjectID($id);
        $result = $osiris->projects->findOne(
            ['_id' => $id],
        );
    } else {
        $result = $osiris->projects->findOne(
            ['name' => $id],
        );
    }
    return $result;
}

function help_getGroup($osiris, $id)
{
    if ($id == 0)
        $group = $osiris->groups->findOne(['level' => 0]);
    else
        $group = $osiris->groups->findOne(['id' => $id]);
    return $group;
}

function help_groupUsers($osiris, $id, $Groups)
{
    $child_ids = $Groups->getChildren($id);
    $users = $osiris->persons->find(
        ['depts' => ['$in' => $child_ids], 'is_active' => true, 'hide' => ['$ne' => true], 'hide' => ['$ne' => true]],
        ['sort' => ['last' => 1]]
    )->toArray();
    $users = array_column($users, 'username');
    return $users;
}


Route::get('/portfolio/units', function () {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    $result = $osiris->groups->find(
        [],
        // ['hide' => ['$ne' => true]],
        ['projection' => ['_id' => 0, 'id' => 1, 'name' => 1, 'name_de' => 1, 'parent' => 1, 'unit' => 1, 'level' => 1, 'hide' => 1, 'order' => 1]]
    )->toArray();
    echo rest($result);
});


Route::get('/portfolio/unit/([^/]*)', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    if ($id == 0)
        $group = $osiris->groups->findOne(['level' => 0]);
    else
        $group = $osiris->groups->findOne(['id' => $id]);

    $head = $group['head'] ?? [];
    if (is_string($head)) $head = [$head];
    else $head = DB::doc2Arr($head);
    unset($group['head']);


    $unit = $Groups->getUnit($group['unit'] ?? null);
    $group['unit'] = $unit;

    if (!empty($head)) {
        $group['heads'] = [];
        foreach ($head as $h) {
            $p = $DB->getPerson($h);
            if (empty($p) || ($p['hide'] ?? false)) continue;

            if ($p['public_image'] ?? false) {
                $img = $Settings->printProfilePicture($p['username'], 'profile-img small');
            } else {
                $img = $Settings->printProfilePicture(null, 'profile-img small');
            }
            $group['heads'][] = [
                'id' => strval($p['_id']),
                'name' => $p['displayname'],
                'img' => $img,
                'position' => $p['position'],
            ];
        }
    }

    $group['parent_details'] = $osiris->groups->findOne(
        ['id' => $group['parent']],
        ['projection' => ['_id' => 0, 'id' => 1, 'name' => 1, 'name_de' => 1, 'level' => 1, 'hide' => 1]]
    );
    $group['children'] = $osiris->groups->find(
        ['parent' => $group['id'], 'hide' => ['$ne' => true]],
        ['projection' => ['_id' => 0, 'id' => 1, 'name' => 1, 'name_de' => 1, 'level' => 1, 'hide' => 1]]
    )->toArray();

    echo rest($group);
});


Route::get('/portfolio/unit/([^/]*)/research', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    if ($id == 0)
        $group = $osiris->groups->findOne(['level' => 0]);
    else
        $group = $osiris->groups->findOne(['id' => $id]);

    // include(BASEPATH . '/php/MyParsedown.php');
    // $parsedown = new Parsedown();

    $research = [];
    if (isset($group['research'])) foreach ($group['research'] as $key => $value) {
        $res = [
            'title' => $value['title'] ?? '',
            'title_de' => $value['title_de'] ?? '',
            'subtitle' => $value['subtitle'] ?? '',
            'subtitle_de' => $value['subtitle_de'] ?? '',
            // 'info' => (!empty($value['info'] ?? '') ? $parsedown->text($value['info']) : null),
            'info' => $value['info'] ?? '',
            // 'info_de' => (!empty($value['info_de'] ?? '') ? $parsedown->text($value['info_de']) : null)
            'info_de' => $value['info_de'] ?? '',
        ];
        if (!empty($value['activities'])) {
            $res['activities'] = [];
            foreach ($value['activities'] as $a) {
                $doc = $DB->getActivity($a);
                if (empty($doc)) continue;
                $res['activities'][] = [
                    'id' => strval($doc['_id']),
                    'icon' => $doc['rendered']['icon'],
                    'html' => $doc['rendered']['print']
                ];
            }
        }
        $research[] = $res;
    }

    echo rest($research);
});


Route::get('/portfolio/unit/([^/]*)/numbers', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }


    $result = [];
    if ($id == 0) {
        $group = $osiris->groups->findOne(['level' => 0]);
        $id = $group['id'];
    } else
        $group = $osiris->groups->findOne(['id' => $id]);

    $child_ids = $Groups->getChildren($id);
    $users = $osiris->persons->find(['depts' => ['$in' => $child_ids]], ['projection' => ['username' => 1]])->toArray();
    $users = array_column($users, 'username');

    if (isset($group['description']) || isset($group['description_de'])) {
        $result['general'] = 1;
    }
    if (!empty($group['research'] ?? null)) {
        $result['research'] = 1;
    }

    $filter = [
        'depts' => ['$in' => $child_ids],
        'is_active' => true,
        'hide' => ['$ne' => true]
    ];

    $result['persons'] = $osiris->persons->count($filter);

    $publication_filter = [
        'authors.user' => ['$in' => $users],
        'type' => 'publication',
        'hide' => ['$ne' => true]
    ];
    $result['publications'] = $osiris->activities->count($publication_filter);

    $activities_filter = [
        'authors.user' => ['$in' => $users],
        'type' => ['$in' => ['poster', 'lecture', 'award', 'software']],
        'hide' => ['$ne' => true]
    ];
    $result['activities'] = $osiris->activities->count($activities_filter);

    $membership_filter = [
        'authors.user' => ['$in' => $users],
        // 'end' => null,
        '$or' => array(
            ['type' => 'misc', 'subtype' => 'misc-annual'],
            ['type' => 'review', 'subtype' =>  'editorial'],
        )
    ];
    $result['memberships'] = $osiris->activities->count($membership_filter);

    if ($Settings->featureEnabled('projects')) {
        $project_filter = [
            'persons.user' => ['$in' => $users],
            "public" => true,
            "status" => ['$in' => ["approved", 'finished']]
        ];

        $result['projects'] = $osiris->projects->count($project_filter);
    } else {
        $result['projects'] = 0;
    }

    if ($group['level'] == 1) {
        $cooperation_filter = [
            'type' => 'publication',
            'hide' => ['$ne' => true],
            'year' => ['$gte' => CURRENTYEAR - 4],
            'rendered.depts' => $id
        ];
        $coop = $osiris->activities->aggregate([
            ['$match' => $cooperation_filter],
            ['$unwind' => '$rendered.depts'],
            ['$group' => ['_id' => '$rendered.depts', 'count' => ['$sum' => 1]]],
            ['$sort' => ['count' => -1]]
        ])->toArray();

        $result['cooperation'] = max(0, count($coop) - 1);
    }

    echo rest($result);
});

Route::get('/portfolio/(unit|person|project)/([^/]*)/(publications|activities|all-activities)', function ($context, $id, $type) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    if ($context == 'unit') {
        if ($id == 0) {
            $group = $osiris->groups->findOne(['level' => 0]);
            $id = $group['id'];
        }

        $child_ids = $Groups->getChildren($id);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids]], ['sort' => ['last' => 1]])->toArray();
        $users = array_column($persons, 'username');
        $filter = [
            'authors.user' => ['$in' => $users],
            'hide' => ['$ne' => true],
            'authors.aoi' => ['$in' => [1, '1', true, 'true']]
        ];
    } elseif ($context == 'project') {
        if (DB::is_ObjectID($id)) {
            $project = $osiris->projects->findOne(['_id' => DB::to_ObjectID($id)]);
            if (!empty($project)) {
                $id = $project['name'];
            }
        }
        $filter = [
            'projects' => $id,
            'hide' => ['$ne' => true],
            'authors.aoi' => ['$in' => [1, '1', true, 'true']]
        ];
    } else {
        $id = DB::to_ObjectID($id);
        $person = $osiris->persons->findOne(['_id' => $id]);
        if (empty($person)) {
            echo rest('Person not found', 0, 404);
            die;
        }
        $id = $person['username'];
        $filter = [
            'authors.user' => $id,
            'hide' => ['$ne' => true]
        ];
    }
    if ($type == 'publications') {
        $filter['type'] = 'publication';
    } else if ($type == 'activities') {
        $filter['type'] = ['$in' => ['poster', 'lecture', 'award', 'software']];
    } else {
        $filter['type'] = ['$in' => ['poster', 'lecture', 'award', 'software', 'publication']];
    }

    $options = [
        'sort' => ['year' => -1, 'month' => -1, 'day' => -1],
        'projection' => [
            'html' => '$rendered.portfolio',
            'search' => '$rendered.plain',
            'type' => 1,
            'subtype' => 1,
            'year' => 1,
            'month' => 1,
            'day' => 1,
            'icon' => '$rendered.icon',
        ]
    ];

    $result = $osiris->activities->find(
        $filter,
        $options
    )->toArray();

    echo rest($result);
});

Route::get('/portfolio/(unit|person)/([^/]*)/teaching', function ($context, $id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    $filter = ['type' => 'teaching', 'module_id' => ['$ne' => null], 'hide' => ['$ne' => true]];
    if ($context == 'unit') {
        if ($id == 0) {
            $group = $osiris->groups->findOne(['level' => 0]);
            $id = $group['id'];
        }
        $child_ids = $Groups->getChildren($id);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids], 'is_active' => true], ['sort' => ['last' => 1]])->toArray();
        $users = array_column($persons, 'username');
        $filter['authors.user'] = ['$in' => $users];
    } else {
        $id = DB::to_ObjectID($id);
        $person = $osiris->persons->findOne(['_id' => $id]);
        $id = $person['username'];
        $filter['authors.user'] =  $id;
    }

    $teaching = $osiris->activities->aggregate([
        ['$match' => $filter],
        [
            '$group' => [
                '_id' => '$module_id',
                'count' => ['$sum' => 1],
                // 'doc' => ['$push' => '$$ROOT']
            ]
        ],
        ['$sort' => ['count' => -1]]
    ])->toArray();

    $result = [];
    foreach ($teaching as $t) {
        $module = $osiris->teaching->findOne(['_id' => DB::to_ObjectID($t['_id'])]);
        if (empty($module)) continue;
        $result[] = [
            'id' => strval($module['_id']),
            'name' => $module['module'],
            'title' => $module['title'],
            'affiliation' => $module['affiliation'],
            'count' => $t['count']
        ];
    }

    echo rest($result);
});






Route::get('/portfolio/(unit|person)/([^/]*)/projects', function ($context, $id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    $filter = [
        'public' => true,
        "status" => ['$in' => ["approved", 'finished']]
    ];

    if ($context == 'unit') {
        if ($id == 0) {
            $group = $osiris->groups->findOne(['level' => 0]);
            $id = $group['id'];
        }
        $child_ids = $Groups->getChildren($id);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids], 'is_active' => true], ['sort' => ['last' => 1]])->toArray();
        $users = array_column($persons, 'username');
        $filter['persons.user'] = ['$in' => $users];
    } else {
        $id = DB::to_ObjectID($id);
        $person = $osiris->persons->findOne(['_id' => $id]);
        $id = $person['username'];
        $filter['persons.user'] =  $id;
    }

    $options = [
        'sort' => ['year' => -1, 'month' => -1],
        'projection' => [
            'name' => 1,
            'title' => 1,
            'funder' => 1,
            'funding_organization' => 1,
            'funding_number' => 1,
            'role' => 1,
            'start' => 1,
            'end' => 1,
            'type' => 1,
            'teaser_en' => 1,
            'teaser_de' => 1,
        ]
    ];

    $result = $osiris->projects->find(
        $filter,
        $options
    )->toArray();

    echo rest($result);
});

Route::get('/portfolio/unit/([^/]*)/staff', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    // dump($_SERVER, true);

    $filter = [
        'hide' => ['$ne' => true],
        'is_active' => true,
        'hide' => ['$ne' => true]
    ];
    if ($id == 0) {
        $group = $osiris->groups->findOne(['level' => 0]);
        $id = $group['id'];
    }
    $child_ids = $Groups->getChildren($id);
    $filter['depts'] = ['$in' => $child_ids];

    $persons = $osiris->persons->find(
        $filter,
        ['sort' => ['last' => 1]]
    )->toArray();
    $result = [];

    foreach ($persons as $person) {
        $row = [
            'displayname' => ($person['first'] ?? '') . ' ' . $person['last'],
            'academic_title' => $person['academic_title'],
            'position' => $person['position'],
            'depts' => $Groups->personDepts($person['depts'])
        ];
        if ($person['public_image'] ?? false) {
            $row['img'] = $Settings->printProfilePicture($person['username'], 'profile-img');
        } else {
            $row['img'] = $Settings->printProfilePicture(null, 'profile-img');
        }
        $row['id'] = strval($person['_id']);
        $result[] = $row;
    }
    echo rest($result);
});


Route::get('/portfolio/project/([^/]*)/staff', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    include(BASEPATH . '/php/Project.php');
    // dump($_SERVER, true);

    // $filter = [
    //     'hide' => ['$ne' => true],
    //     'is_active' => true, 'hide'=>['$ne'=>true]
    // ];
    $project = help_getProject($osiris, $id);
    if (empty($project)) {
        echo rest('Project not found', 0, 404);
        die;
    }
    if (empty($project['persons'])) {
        echo rest([]);
        die;
    }

    $persons = DB::doc2Arr($project['persons']);
    // sort project team by role (custom order)
    $roles = ['applicant', 'PI', 'Co-PI', 'worker', 'associate', 'student'];
    usort($persons, function ($a, $b) use ($roles) {
        return array_search($a['role'], $roles) - array_search($b['role'], $roles);
    });

    $result = [];

    foreach ($persons as $p) {
        $person = $DB->getPerson($p['user']);
        if (empty($person) || ($person['hide'] ?? false)) continue;
        $row = [
            'displayname' => $person['displayname'],
            'academic_title' => $person['academic_title'],
            'position' => $person['position'],
            'depts' => []
        ];
        if ($person['public_image'] ?? false) {
            $row['img'] = $Settings->printProfilePicture($person['username'], 'profile-img small mr-20');
        } else {
            $row['img'] = $Settings->printProfilePicture(null, 'profile-img small mr-20');
        }
        $row['id'] = strval($person['_id']);
        $row['role'] = Project::personRole($row['role']);

        if (!empty($person['depts'])) {
            foreach ($person['depts'] as $d) {
                $dept = $Groups->getGroup($d);
                if ($dept['level'] !== 1) continue;
                $row['depts'][$d] = [
                    'en' => $dept['name'],
                    'de' => $dept['name_de']
                ];
            }
        }
        $result[] = $row;
    }
    echo rest($result);
});

Route::get('/portfolio/activity/([^/]*)', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    include(BASEPATH . '/php/Modules.php');
    $id = DB::to_ObjectID($id);
    $result = [];
    $doc = $osiris->activities->findOne(
        ['_id' => $id]
    );
    if (empty($doc)) {
        echo rest('Activity not found', 0, 404);
        die;
    }
    $result = [
        'id' => strval($doc['_id']),
        'type' => $doc['type'],
        'subtype' => $doc['subtype'],
        'year' => $doc['year'] ?? null,
        'month' => $doc['month'] ?? null,
        'abstract' => $doc['abstract'] ?? null,
        'doi' => $doc['doi'] ?? null,
        'pubmed' => $doc['pubmed'] ?? null,
        'title' => $doc['rendered']['title'],
        'authors' => [],
        'depts' => [],
        'projects' => [],
    ];

    foreach ($doc['authors'] as $a) {
        $i = null;
        if (!empty($a['user'])) {
            $person = $DB->getPerson($a['user']);
            if (!empty($person) && !($person['hide'] ?? false)) $i = strval($person['_id']);
        }
        $result['authors'][] = [
            'id' => $i,
            'name' => ($a['first'] ?? '') . ' ' . ($a['last'] ?? ''),
        ];
    }

    $depts = [];
    if (!empty($doc['rendered']['depts'])) {
        foreach ($doc['rendered']['depts'] as $d) {
            $dept = $Groups->getGroup($d);
            if ($dept['level'] !== 1) continue;
            $depts[$d] = [
                'en' => $dept['name'],
                'de' => $dept['name_de']
            ];
        }
    }
    $result['depts'] = $depts;

    if (!empty($doc['projects'])) {
        $projects = [];
        foreach ($doc['projects'] as $p) {
            $project = $DB->getProject($p);
            if (empty($project)) continue;
            $projects[] = [
                'id' => strval($project['_id']),
                'name' => $project['name'],
                'title' => $project['title'],
                'funder' => $project['funder'],
                'funding_organization' => $project['funding_organization'],
                'funding_number' => $project['funding_number'],
                'role' => $project['role'],
                'start' => $project['start'],
                'end' => $project['end']
            ];
        }
        $result['projects'] = $projects;
    }

    $Format = new Document;
    $Format->setDocument($doc);
    $selected = $Format->subtypeArr['modules'] ?? array();
    $Modules = new Modules($doc);

    $Format->usecase = "list";

    // TODO: configurable
    $hidden_modules = ['authors', "editors", "semester-select", 'abstract', 'doi', 'pubmed', 'depts', 'projects', 'correction', 'epub', 'title'];
    $fields = [];
    foreach ($selected as $module) {
        if (str_ends_with($module, '*')) $module = str_replace('*', '', $module);
        if (in_array($module, $hidden_modules)) continue;
        if ($module == 'teaching-course' && isset($doc['module_id'])) :
            $module = $DB->getConnected('teaching', $doc['module_id']);
            $fields[] = [
                'key_en' => 'Teaching Module',
                'key_de' => 'Lehrveranstaltung',
                'value' => $module['module']
            ];
        elseif ($module == 'journal' && isset($doc['journal_id'])) :
            $journal = $DB->getConnected('journal', $doc['journal_id']);
            $fields[] = [
                'key_en' => 'Journal',
                'key_de' => 'Journal',
                'value' => $journal['journal']
            ];
        elseif ($Format->get_field($module) != '-') :
            $names = $Modules->all_modules[$module] ?? [];
            $fields[] = [
                'key_en' => $names['name'] ?? ucfirst($module),
                'key_de' => $names['name_de'] ?? ucfirst($module),
                'value' => $Format->get_field($module)
            ];
        endif;
    }
    $result['fields'] = $fields;

    // bibtex format
    $result['print'] = $doc['rendered']['print'];
    $result['bibtex'] = $Format->bibtex();
    $result['ris'] = $Format->ris();

    echo rest($result);
});


Route::get('/portfolio/project/([^/]*)', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    include(BASEPATH . '/php/Project.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    $result = $DB->getProject($id);
    if (empty($result)) {
        echo rest('Project not found', 0, 404);
        die;
    }
    $id = $result['name'];

    $project = [
        'id' => strval($result['_id']),
        'name' => $result['public_title'] ?? $result['name'],
        'name_de' => $result['public_title_de'] ?? null,
        'title' => $result['public_subtitle'] ?? $result['title'] ?? '',
        'title_de' => $result['public_subtitle_de'] ?? null,
        'abstract' => $result['public_abstract'] ?? $result['abstract'] ?? '',
        'abstract_de' => $result['public_abstract_de'] ?? null,
        'funder' => $result['funder'] ?? null,
        'funding_organization' => $result['funding_organization'] ?? null,
        'funding_number' => $result['funding_number'] ?? null,
        'coordinator' => $result['coordinator'] ?? null,
        'scholarship' => $result['scholarship'] ?? null,
        'university' => $result['university'] ?? null,
        'role' => $result['role'] ?? 'partner',
        'start' => $result['start'] ?? '',
        'end' => $result['end'] ?? '',
        'persons' => [],
        'activities' => 0,
        'subprojects' => [],
        'collaborators' => $result['collaborators'] ?? [],
        'website' => $result['website'] ?? null,
        'img' => null
    ];

    if (isset($result['public_image']) && !empty($result['public_image']))
        $project['img'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . ROOTPATH . '/uploads/' . $result['public_image'];

    $project['activities'] = $osiris->activities->count(['projects' => $id, 'hide' => ['$ne' => true]]);

    if (!empty($result['persons'])) {

        $persons = DB::doc2Arr($result['persons']);
        // sort project team by role (custom order)
        $roles = ['applicant', 'PI', 'Co-PI', 'worker', 'associate', 'student'];
        usort($persons, function ($a, $b) use ($roles) {
            return array_search($a['role'], $roles) - array_search($b['role'], $roles);
        });

        foreach ($persons as $row) {
            $person = $DB->getPerson($row['user']);
            if (empty($person) || ($person['hide'] ?? false)) continue;
            if ($person['public_image'] ?? false) {
                $row['img'] = $Settings->printProfilePicture($person['username'], 'profile-img small mr-20');
            } else {
                $row['img'] = $Settings->printProfilePicture(null, 'profile-img small mr-20');
            }
            unset($row['user']);
            $row['id'] = strval($person['_id']);
            $row['role'] = Project::personRoleRaw($row['role']);
            $depts = [];
            if (!empty($person['depts'])) {
                foreach ($Groups->personDepts($person['depts']) as $d) {
                    $dept = $Groups->getGroup($d);
                    if ($dept['level'] !== 1) continue;
                    $depts[$d] = [
                        'en' => $dept['name'],
                        'de' => $dept['name_de']
                    ];
                }
            }
            $row['depts'] = $depts;

            $project['persons'][] = $row;
        }
    }

    // add parent project
    if (!empty($result['parent'] ?? null)) {
        $parent = $DB->getProject($result['parent']);
        if (!empty($parent) && $parent['public'] ?? false) {
            $project['parent'] = [
                'id' => strval($parent['_id']),
                'name' => $parent['name'],
                'title' => $parent['title'] ?? ''
            ];
        }
    }

    // add subprojects
    $subprojects = $osiris->projects->find(['parent' => $id], ['projection' => ['name' => 1, 'title' => 1]])->toArray();
    foreach ($subprojects as $sub) {
        if ($sub['public'] ?? false) continue;
        $project['subprojects'][] = [
            'id' => strval($sub['_id']),
            'name' => $sub['name'],
            'title' => $sub['title'] ?? ''
        ];
    }

    echo rest($project);
});

Route::get('/portfolio/person/([^/]*)', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    include(BASEPATH . '/php/Project.php');
    $Project = new Project;

    $id = DB::to_ObjectID($id);
    $person = $osiris->persons->findOne(
        ['_id' => $id],
        // ['projection' => ['_id' => 0, 'html' => '$rendered.portfolio', 'doc'=> '$$ROOT']]
    );
    if (empty($person)) {
        echo rest('Person not found', 0, 404);
        die;
    }
    if ($person['hide'] ?? false) {
        echo rest('Person not found', 0, 404);
        die;
    }
    $result = [
        'displayname' => $person['displayname'],
        'last' => $person['last'],
        'first' => $person['first'],
        'academic_title' => $person['academic_title'],
        'position' => $person['position'],
        'position_de' => $person['position_de'] ?? null,
        'depts' => [],
        'cv' => $person['cv'] ?? [],
        'contact' => []
    ];

    if (!($person['is_active'] ?? true)) {
        $result['inactive'] = true;
    }

    if ($person['public_email'] ?? true) {
        $result['contact']['mail'] = $person['mail'];
    }
    if ($person['public_phone'] ?? true) {
        $result['contact']['phone'] = $person['telephone'];
    }
    foreach (
        [
            'mail_alternative',
            'mail_alternative_comment',
            'twitter',
            'linkedin',
            'orcid',
            'researchgate',
            'google_scholar',
            'webpage'
        ] as $key
    ) {
        if (isset($person[$key]) && !empty($person[$key])) {
            $result['contact'][$key] = $person[$key];
        }
    }


    if ($person['research'] ?? false) {
        $person['research_de'] = $person['research_de'] ?? [];
        // $person['research_de'] = array_map(
        //     fn($val1, $val2) => empty($val1) ? $val2 : $val1,
        //     DB::doc2Arr($person['research_de'] ?? $person['research']),
        //     DB::doc2Arr($person['research'])
        // );
        $result['research'] = [];
        foreach ($person['research'] as $key => $value) {
            $result['research'][] = [
                'en' => $value,
                'de' => $person['research_de'][$key] ?? null
            ];
        }
    }
    if ($person['public_image'] ?? false) {
        $result['img'] = $Settings->printProfilePicture($person['username'], 'profile-img');
    } else {
        $result['img'] = $Settings->printProfilePicture(null, 'profile-img');
    }
    $result['id'] = strval($person['_id']);
    if (!empty($person['depts'])) {
        $hierarchy = $Groups->getPersonHierarchyTree($person['depts']);
        $result['depts'] = $Groups->readableHierarchy($hierarchy);
    }
    if (isset($person['highlighted']) && !empty($person['highlighted'])) {
        $docs = [];
        foreach ($person['highlighted'] as $id) {
            $doc = $DB->getActivity($id);
            if (!empty($doc)) {
                $docs[] = [
                    'id' => strval($doc['_id']),
                    'icon' => $doc['rendered']['icon'],
                    'html' => str_replace('**PORTAL**', '', $doc['rendered']['portfolio'])
                ];
            }
        }
        $result['highlighted'] = $docs;
    }

    $result['numbers'] = [
        'publications' => $osiris->activities->count(['authors.user' => $person['username'], 'type' => 'publication', 'hide' => ['$ne' => true]]),
        'activities' => $osiris->activities->count(['authors.user' => $person['username'], 'type' => ['$in' => ['poster', 'lecture', 'award', 'software']], 'hide' => ['$ne' => true]]),
        'teaching' => $osiris->activities->count(['authors.user' => $person['username'], 'type' => 'teaching', 'module_id' => ['$ne' => null], 'hide' => ['$ne' => true]]),
        'projects' => $osiris->projects->count(['persons.user' => $person['username'], "public" => true, "status" => ['$in' => ["approved", 'finished']]]),
    ];

    if ($result['numbers']['projects'] > 0) {
        $raw = $osiris->projects->find(['persons.user' => $person['username'], "public" => true, "status" => ['$in' => ["approved", 'finished']]])->toArray();
        $projects = ['current' => [], 'past' => []];
        foreach ($raw as $project) {

            $Project->setProject($project);
            $past = $Project->inPast();
            if ($past) $key = 'past';
            else $key = 'current';
            $projects[$key][] = [
                'id' => strval($project['_id']),
                'name' => $project['name'],
                'title' => $project['title'],
                'funder' => $project['funder'] ?? $project['scholarship'] ?? null,
                'funding_organization' => $project['funding_organization'] ?? null,
                // 'funding_number' => $project['funding_number'] ,
                'role' => $project['role'],
                'start' => $project['start'],
                'end' => $project['end'],
            ];
        }
        $result['projects'] = $projects;
    }

    echo rest($result);
});



Route::get('/portfolio/(unit|project)/([^/]*)/collaborators-map', function ($context, $id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    $result = [];
    if ($context == 'project') {

        if (DB::is_ObjectID($id)) {
            $mongo_id = $DB->to_ObjectID($id);
            $project = $osiris->projects->findOne(['_id' => $mongo_id]);
        } else {
            $project = $osiris->projects->findOne(['name' => $id]);
            $id = strval($project['_id'] ?? '');
        }
        if (empty($project)) {
            rest("Project could not be found.", 0, 404);
        } elseif (empty($project['collaborators'] ?? [])) {
            rest("Project has no collaborators", 0, 404);
        } else {

            $result = [];
            // order by role
            $collabs = DB::doc2Arr($project['collaborators']);
            usort($collabs, function ($a, $b) {
                return $b['role'] <=> $a['role'];
            });
            foreach ($collabs as $c) {
                $result[] = [
                    "_id" => $c['name'],
                    "count" => 1,
                    "data" => $c
                ];
            }
        }
    } else {
        $filter = ['collaborators' => ['$exists' => 1]];
        // only for portal
        $dept = $id;

        $child_ids = $Groups->getChildren($dept);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids], 'is_active' => true, 'hide' => ['$ne' => true]], ['sort' => ['last' => 1]])->toArray();
        $users = array_column($persons, 'username');
        $filter = [
            'persons.user' => ['$in' => $users],
            "public" => true,
            "status" => ['$in' => ["approved", 'finished']],
            'collaborators' => ['$exists' => 1]
        ];
        $result = $osiris->projects->aggregate([
            ['$match' => $filter],
            ['$project' => ['collaborators' => 1]],
            ['$unwind' => '$collaborators'],
            [
                '$group' => [
                    '_id' => '$collaborators.name',
                    'count' => ['$sum' => 1],
                    'data' => [
                        '$first' => '$collaborators'
                    ]
                ]
            ],
        ])->toArray();

        // set all roles to 'partner'
        foreach ($result as $r) {
            $r['data']['role'] = 'partner';
        }
    }

    $institute = $Settings->get('affiliation_details');
    $institute['role'] = $project['role'] ?? 'coordinator';
    $institute['current'] = true;
    if (isset($institute['lat']) && isset($institute['lng'])) {
        $result[] = [
            '_id' => $institute['ror'] ?? '',
            'count' => 1,
            'data' => $institute,
            // 'color' => 'secondary'
        ];
    }
    // if ($institute['role'] == 'coordinator') 
    // $result = array_reverse($result);
    echo rest($result, count($result));
});



Route::get('/portfolio/unit/([^/]*)/cooperation', function ($id) {
    // error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');


    // select publications from the past five years where the department is involved
    $filter = [
        'type' => 'publication',
        'hide' => ['$ne' => true],
        'year' => ['$gte' => CURRENTYEAR - 4],
        'rendered.depts' => $id
    ];
    $options = [
        'projection' => [
            'depts' => '$rendered.depts'
        ]
    ];

    $result = $osiris->activities->find($filter, $options)->toArray();

    // get matrix of shared publications between departments
    function combine($array)
    {
        $result = [];
        $n = count($array);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $result[] = [$array[$i], $array[$j]];
            }
        }
        return $result;
    }

    $arr = [];
    $labels = [];
    foreach ($result as $doc) {
        $depts = $doc['depts'];
        foreach ($depts as $d) {
            if (empty($d)) continue;
            if (!isset($labels[$d])) {
                $g = $Groups->getGroup($d);
                if (empty($g) || ($g['hide'] ?? false)) continue;
                $labels[$d] = [
                    'id' => $d,
                    'name' => $g['name'],
                    'name_de' => $g['name_de'] ?? $g['name'],
                    'color' => $g['color'],
                    'count' => 0,
                    'selected' => ($id == $d)
                ];
            }
            $labels[$d]['count']++;
        }
        if (count($depts) == 1) {
            $d = $depts[0];
            if (!isset($arr[$d])) {
                $arr[$d] = [];
            }
            if (!isset($arr[$d][$d])) {
                $arr[$d][$d] = 0;
            }
            $arr[$d][$d]++;
        }
        $combinations = combine($depts);
        foreach ($combinations as $c) {
            if (empty($c[0]) || empty($c[1])) continue;
            // if ($c[0] == $c[1]) continue;
            if (!array_key_exists($c[0], $labels) || !array_key_exists($c[1], $labels)) continue;

            if (!isset($arr[$c[0]])) {
                $arr[$c[0]] = [];
            }
            if (!isset($arr[$c[1]])) {
                $arr[$c[1]] = [];
            }
            if (!isset($arr[$c[0]][$c[1]])) {
                $arr[$c[0]][$c[1]] = 0;
            }
            if (!isset($arr[$c[1]][$c[0]])) {
                $arr[$c[1]][$c[0]] = 0;
            }
            $arr[$c[0]][$c[1]]++;
            $arr[$c[1]][$c[0]]++;
        }
    }
    $matrix = []; // numberical matrix n x m
    foreach ($arr as $key => $val) {
        $row = [];
        foreach ($arr as $k => $v) {
            $row[] = $val[$k] ?? 0;
        }
        $matrix[] = $row;
    }

    echo rest([
        'matrix' => $matrix,
        'labels' => $labels,
        // 'warnings' => $warnings
    ], count($labels));
});
