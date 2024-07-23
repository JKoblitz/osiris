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
    $id = urldecode($id);
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
        ['depts' => ['$in' => $child_ids], 'is_active' => true, 'hide' => ['$ne' => true]],
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
        ['hide' => ['$ne' => true]],
        ['projection' => ['_id' => 0, 'id' => 1, 'name' => 1, 'name_de' => 1, 'parent' => 1, 'unit' => 1, 'level' => 1]]
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
    $id = urldecode($id);
    if ($id == 0)
        $group = $osiris->groups->findOne(['level' => 0]);
    else
        $group = $osiris->groups->findOne(['id' => $id]);

    $head = $group['head'] ?? [];
    if (is_string($head)) $head = [$head];
    else $head = DB::doc2Arr($head);

    $unit = $Groups->getUnit($group['unit'] ?? null);
    $group['unit'] = $unit;

    if (!empty($head)) {
        $group['heads'] = [];
        foreach ($head as $h) {
            $p = $DB->getPerson($h);
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

    include(BASEPATH . '/php/MyParsedown.php');
    $parsedown = new Parsedown();
    foreach (['description', 'description_de'] as $key) {
        if (isset($group[$key]) && is_string($group[$key])) {
            $group[$key] = $parsedown->text($group[$key]);
        }
    }
    echo rest($group);
});


Route::get('/portfolio/unit/([^/]*)/research', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    $id = urldecode($id);

    if ($id == 0)
        $group = $osiris->groups->findOne(['level' => 0]);
    else
        $group = $osiris->groups->findOne(['id' => $id]);

    include(BASEPATH . '/php/MyParsedown.php');
    $parsedown = new Parsedown();

    $research = [];
    if (isset($group['research'])) foreach ($group['research'] as $key => $value) {
        $res = [
            'title' => $value['title'] ?? '',
            'title_de' => $value['title_de'] ?? '',
            'subtitle' => $value['subtitle'] ?? '',
            'subtitle_de' => $value['subtitle_de'] ?? '',
            'info' => (!empty($value['info'] ?? '') ? $parsedown->text($value['info']) : null),
            'info_de' => (!empty($value['info_de'] ?? '') ? $parsedown->text($value['info_de']) : null)
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


    $id = urldecode($id);
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
            "status" => ['$ne' => "rejected"]
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

        $result['cooperation'] = count($coop) - 1;
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
    $id = urldecode($id);

    if ($context == 'unit') {
        if ($id == 0) {
            $group = $osiris->groups->findOne(['level' => 0]);
            $id = $group['id'];
        }

        $child_ids = $Groups->getChildren($id);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids], 'is_active' => true], ['sort' => ['last' => 1]])->toArray();
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
    $id = urldecode($id);

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
    $id = urldecode($id);

    $filter = [
        'public' => true,
        "status" => ['$ne' => "rejected"]
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
            'end' => 1
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

    $id = urldecode($id);
    $filter = [
        'hide' => ['$ne' => true],
        'is_active' => true
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
            'displayname' => $person['displayname'],
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
    $id = urldecode($id);
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    include(BASEPATH . '/php/Project.php');
    // dump($_SERVER, true);

    // $filter = [
    //     'hide' => ['$ne' => true],
    //     'is_active' => true
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
    $id = urldecode($id);
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

    $result = $doc['rendered'];
    $result['id'] = strval($doc['_id']);
    $result['type'] = $doc['type'];
    $result['subtype'] = $doc['subtype'];
    $result['year'] = $doc['year'] ?? null;
    $result['month'] = $doc['month'] ?? null;
    $result['abstract'] = $doc['abstract'] ?? null;
    $result['doi'] = $doc['doi'] ?? null;
    $result['pubmed'] = $doc['pubmed'] ?? null;

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
    $id = urldecode($id);
    header('Access-Control-Allow-Methods: GET');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
    header('Content-Type: application/json');
    $result = $DB->getProject($id);
    if (empty($result)) {
        echo rest('Project not found', 0, 404);
        die;
    }
    $id = $result['name'];

    include(BASEPATH . '/php/MyParsedown.php');

    if (!empty($project['abstract']) && is_string($project['abstract'])) {
        $result['abstract'] = $parsedown->text($project['abstract']);
    }
    if (!empty($project['abstract_de']) && is_string($project['abstract_de'])) {
        $result['abstract_de'] = $parsedown->text($project['abstract_de']);
    }

    $result['activities'] = $osiris->activities->count(['projects' => $id, 'hide' => ['$ne' => true]]);

    if (!empty($result['persons'])) {

        $persons = DB::doc2Arr($result['persons']);
        // sort project team by role (custom order)
        $roles = ['applicant', 'PI', 'Co-PI', 'worker', 'associate', 'student'];
        usort($persons, function ($a, $b) use ($roles) {
            return array_search($a['role'], $roles) - array_search($b['role'], $roles);
        });

        $result['persons'] = [];

        foreach ($persons as $row) {
            $person = $DB->getPerson($row['user']);
            if ($person['public_image'] ?? false) {
                $row['img'] = $Settings->printProfilePicture($person['username'], 'profile-img small mr-20');
            } else {
                $row['img'] = $Settings->printProfilePicture(null, 'profile-img small mr-20');
            }
            unset($row['user']);
            $row['id'] = strval($person['_id']);
            $row['role'] = Project::personRole($row['role']);
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

            $result['persons'][] = $row;
        }
    }
    echo rest($result);
});

Route::get('/portfolio/person/([^/]*)', function ($id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }
    header('Access-Control-Allow-Origin: *');
    $id = urldecode($id);
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
    $result = [
        'displayname' => $person['displayname'],
        'last' => $person['last'],
        'first' => $person['first'],
        'academic_title' => $person['academic_title'],
        'position' => $person['position'],
        'depts' => [],
        'cv' => $person['cv'] ?? [],
        'contact' => []
    ];

    if ($person['public_email'] ?? true) {
        $result['contact']['mail'] = $person['mail'];
    }
    if ($person['public_phone'] ?? true) {
        $result['contact']['phone'] = $person['telephone'];
    }
    foreach ([
        'mail_alternative',
        'mail_alternative_comment',
        'twitter',
        'linkedin',
        'orcid',
        'researchgate',
        'google_scholar',
        'webpage'
    ] as $key) {
        if (isset($person[$key]) && !empty($person[$key])) {
            $result['contact'][$key] = $person[$key];
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
        'projects' => $osiris->projects->count(['persons.user' => $person['username'], "public" => true, "status" => ['$ne' => "rejected"]]),
    ];

    if ($result['numbers']['projects'] > 0) {
        $raw = $osiris->projects->find(['persons.user' => $person['username'], "public" => true, "status" => ['$ne' => "rejected"]])->toArray();
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
                'funder' => $project['funder'],
                'funding_organization' => $project['funding_organization'],
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


Route::get('/portfolio/test', function () {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    $hierarchy = $Groups->tree;
    // Beispielpersonen und ihre Einheiten
    $personsUnits = [
        'Monika' => ['MÖD-BPGD'],
        'Tim' => ['BID', 'MIOS'],
        'Julia' => ['BID', 'INTEGR']
    ];

    // Funktion zur Auflösung des Hierarchiebaums
    function getHierarchyTree($personUnits, $hierarchy)
    {
        $result = [];

        foreach ($personUnits as $unit) {
            $path = findUnitPath($unit, $hierarchy);
            if ($path) {
                mergePaths($result, $path);
            }
        }

        return $result;
    }

    function findUnitPath($unit, $hierarchy, $currentPath = [])
    {
        $newPath = array_merge($currentPath, [$hierarchy['id']]);

        if ($hierarchy['id'] === $unit) {
            return $newPath;
        }

        if (!empty($hierarchy['children'])) {
            foreach ($hierarchy['children'] as $child) {
                $path = findUnitPath($unit, $child, $newPath);
                if ($path) {
                    return $path;
                }
            }
        }

        return null;
    }

    function mergePaths(&$result, $path)
    {
        $current = &$result;
        foreach ($path as $node) {
            if (!isset($current[$node])) {
                $current[$node] = [];
            }
            $current = &$current[$node];
        }
    }


    // Funktion zum Ausdrucken des Hierarchiebaums mit Pfeilen
    function printHierarchyTree($tree, $indent = 0)
    {
        foreach ($tree as $key => $subTree) {
            echo str_repeat("  ", $indent) . ($indent > 0 ? str_repeat(">", $indent) . " " : "") . "$key\n";
            if (!empty($subTree)) {
                printHierarchyTree($subTree, $indent + 1);
            }
        }
    }

    // Hierarchiebaum für jede Person erstellen
    foreach ($personsUnits as $person => $units) {
        echo "$person:\n";
        $tree = getHierarchyTree($units, $hierarchy);
        printHierarchyTree($tree);
        echo "\n";
    }
});


Route::get('/portfolio/(unit|project)/([^/]*)/collaborators-map', function ($context, $id) {
    error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');
    // if (!apikey_check($_GET['apikey'] ?? null)) {
    //     echo return_permission_denied();
    //     die;
    // }

    include(BASEPATH . '/php/Project.php');

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
            // order by role
            $collabs = DB::doc2Arr($project['collaborators']);
            usort($collabs, function ($a, $b) {
                return $b['role'] <=> $a['role'];
            });
            foreach ($collabs as $c) {
                // if (empty($c['lng']))
                $data['lon'][] = $c['lng'];
                $data['lat'][] = $c['lat'];
                $data['text'][] = "<b>$c[name]</b><br>$c[location]";
                $color = ($c['role'] == 'partner' ? 'primary' : 'secondary');
                $data['marker']['color'][] = $color;
            }
            $institute = $Settings->get('affiliation_details');
            $institute['role'] = $project['role'];
            if (isset($institute['lat']) && isset($institute['lng'])) {

                $data['lon'][] = $institute['lng'];
                $data['lat'][] = $institute['lat'];
                $data['text'][] = "<b>$institute[name]</b><br>$institute[location]";
                $color = ($institute['role'] == 'partner' ? 'primary' : 'secondary');
                $data['marker']['color'][] = $color;
            }

            $result['collaborators'] = $data;
        }
    } else {
        $filter = ['collaborators' => ['$exists' => 1]];
        // only for portal
        $dept = $id;

        $child_ids = $Groups->getChildren($dept);
        $persons = $osiris->persons->find(['depts' => ['$in' => $child_ids], 'is_active' => true], ['sort' => ['last' => 1]])->toArray();
        $users = array_column($persons, 'username');
        $filter = [
            'persons.user' => ['$in' => $users],
            "public" => true,
            "status" => ['$ne' => "rejected"],
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

        $institute = $Settings->get('affiliation_details');
        if (isset($institute['lat']) && isset($institute['lng'])) {
            $result[] = [
                '_id' => $institute['ror'] ?? '',
                'count' => 3,
                'data' => $institute,
                'color' => 'secondary'
            ];
        }
    }

    echo rest($result, count($result));
});



Route::get('/portfolio/unit/([^/]*)/cooperation', function ($id) {
    // error_reporting(E_ERROR | E_PARSE);
    include(BASEPATH . '/php/init.php');

    $id = urldecode($id);

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
