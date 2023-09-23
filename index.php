<?php

/**
 * Core routing file
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

if (file_exists('CONFIG.php')) {
    require_once 'CONFIG.php';
    require_once 'CONFIG.fallback.php';
} else {
    require_once 'CONFIG.default.php';
}


error_reporting(E_ERROR);

session_start();

define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);
define('OSIRIS_VERSION', '1.1.2');

// set time constants
$year = date("Y");
$month = date("n");
$quarter = ceil($month / 3);
define('CURRENTQUARTER', intval($quarter));
define('CURRENTMONTH', intval($month));
define('CURRENTYEAR', intval($year));

if (isset($_GET['OSIRIS-SELECT-MAINTENANCE-USER'])) {
    // someone tries to switch users
    include_once BASEPATH . "/php/init.php";
    $realusername = $_SESSION['realuser'] ?? $_SESSION['username'];
    $username = $_GET['OSIRIS-SELECT-MAINTENANCE-USER'];

    // check if the user is allowed to do that
    $allowed = $osiris->accounts->count(['username' => $username, 'maintenance' => $realusername]);

    // change username if user is allowed
    if ($allowed == 1 || $realusername == $username) {
        $msg = "User switched!";
        $_SESSION['realuser'] = $realusername;
        $_SESSION['username'] = $username;
        header("Location: " . ROOTPATH . "/profile/$username");
    }

    // do nothing if user is not allowed
}

function lang($en, $de = null)
{
    if ($de === null) return $en;
    // Standard language = DE
    if (!isset($_COOKIE['osiris-language'])) return $de;
    if ($_COOKIE['osiris-language'] == "en") return $en;
    if ($_COOKIE['osiris-language'] == "de") return $de;
    return $en;
}

include_once BASEPATH . "/php/Route.php";

Route::get('/', function () {
    include_once BASEPATH . "/php/init.php";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        include BASEPATH . "/header.php";
        include BASEPATH . "/pages/userlogin.php";
        include BASEPATH . "/footer.php";
    } elseif ($USER['is_controlling']) {
        $path = ROOTPATH . "/dashboard";
        if (!empty($_SERVER['QUERY_STRING'])) $path .= "?" . $_SERVER['QUERY_STRING'];
        header("Location: $path");
    } else {
        $path = ROOTPATH . "/profile/" . $_SESSION['username'];
        if (!empty($_SERVER['QUERY_STRING'])) $path .= "?" . $_SERVER['QUERY_STRING'];
        header("Location: $path");
    }
});


if (defined('USER_MANAGEMENT') && USER_MANAGEMENT == 'AUTH') {
    require_once 'addons/auth/index.php';
}
if (defined('GUEST_FORMS') && GUEST_FORMS) {
    require_once 'addons/guestforms/index.php';
}


Route::get('/test-new-func', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    $db = new DB;
    dump($db->getUser('juk20'));

    include BASEPATH . "/footer.php";
});

Route::get('/dashboard', function () {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/dashboard.php";
    include BASEPATH . "/footer.php";
});

Route::get('/issues', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $breadcrumb = [
        ['name' => lang('Issues', 'Warnungen')]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/issues.php";
    include BASEPATH . "/footer.php";
});

Route::get('/queue/(user|editor)', function ($role) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    if ($role == 'editor' && ($USER['is_controlling'] || $USER['is_admin'])) {
        $filter = ['declined' => ['$ne' => true]];
    } else {
        $filter = ['authors.user' => $user, 'declined' => ['$ne' => true]];
    }
    $n_queue = $osiris->queue->count($filter);
    $queue = $osiris->queue->find($filter, ['sort' => ['duplicate' => 1]]);

    $breadcrumb = [
        ['name' => lang('Queue', 'Warteschlange')]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/queue.php";
    include BASEPATH . "/footer.php";
});


Route::post('/queue/(accept|decline)/([a-zA-Z0-9]*)', function ($type, $id) {
    include_once BASEPATH . "/php/init.php";

    $mongo_id = $DB->to_ObjectID($id);

    if ($type == 'accept') {

        $new = $osiris->queue->findOne(['_id' => $mongo_id]);
        unset($new['_id']);
        foreach ($new['authors'] ?? array() as $i => $a) {
            if ($a['user'] ?? '' == $_SESSION['username']) {
                $new['authors'][$i]['approved'] = true;
            }
        }
        $new['created'] = date('Y-m-d');
        $new['created_by'] = $_SESSION['username'];

        $insertOneResult = $osiris->activities->insertOne($new);
        $new_id = $insertOneResult->getInsertedId();
        $DB->renderActivities(['_id' => $new_id]);

        $osiris->queue->deleteOne(['_id' => $mongo_id]);
        echo $new_id;
    } else {
        $osiris->queue->updateOne(
            ['_id' => $mongo_id],
            [
                '$set' => [
                    'declined' => true, 'declined_by' => $_SESSION['username']
                ]
            ]

        );
    }
});

Route::get('/coins', function () {

    $breadcrumb = [
        ['name' => lang('LOM', 'LOM')]
    ];

    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/lom.php";
    include BASEPATH . "/footer.php";
});

Route::post('/coins', function () {
    $json = json_encode($_POST['json'], JSON_PRETTY_PRINT);
    file_put_contents(BASEPATH . "/matrix.json", $json);
    header("Location: " . ROOTPATH . "/coins?msg=success");
});

Route::get('/achievements/?(.*)', function ($user) {
    if (empty($user)) $user = $_SESSION['username'];

    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/_achievements.php";

    $scientist = $DB->getUser($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => $name, 'path' => "/profile/$user"],
        ['name' => lang('Achievements', 'Errungenschaften')]

    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/achievements.php";
    include BASEPATH . "/footer.php";
});

Route::get('/new-stuff', function () {

    $breadcrumb = [
        ['name' => lang('News', 'Neuigkeiten')]
    ];

    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/MyParsedown.php";

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/news.php";
    include BASEPATH . "/footer.php";
});

Route::get('/docs', function () {

    $breadcrumb = [
        ['name' => lang('Documentation', 'Dokumentation')]
    ];

    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/MyParsedown.php";

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/docs.php";
    include BASEPATH . "/footer.php";
});

Route::get('/license', function () {

    $breadcrumb = [
        ['name' => lang('License', 'Lizenz')]
    ];

    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/license.html";
    include BASEPATH . "/footer.php";
});

Route::get('/user/login', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang('User login', 'Login')]
    ];
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true  && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/profile/$_SESSION[username]?msg=ali");
        die;
    }
    include BASEPATH . "/header.php";

    if (isset($_GET['redirect'])) {
        echo (lang("You need to be logged in to see this page.", "Du musst eingeloggt sein, um diese Seite zu sehen."));
    }
    include BASEPATH . "/pages/userlogin.php";
    include BASEPATH . "/footer.php";
});


Route::post('/user/login', function () {
    include_once BASEPATH . "/php/init.php";
    $page = "userlogin";
    $msg = "?msg=welcome";
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/profile/$_SESSION[username]?msg=ali");
        die;
    }

    if (defined('USER_MANAGEMENT') && USER_MANAGEMENT == 'AUTH') {
        require_once 'addons/auth/_login.php';
    } else {
        include BASEPATH . "/php/_login.php";
    }
    include BASEPATH . "/php/init.php";

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_SERVER['SERVER_NAME'] == 'testserver' && true) {
            // on the test server: log in
            // check if user exists in our database
            $_SESSION['username'] = $_POST['username'];
            $useracc = $DB->getUser($_SESSION['username']);
            $_SESSION['name'] = $useracc['displayname'];

            $_SESSION['loggedin'] = true;

            if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                header("Location: " . $_POST['redirect'] . $msg);
                die();
            }
            header("Location: " . ROOTPATH . "/" . $msg);
            die();
        } else {
            $auth = login($_POST['username'], $_POST['password']);
            if (isset($auth["status"]) && $auth["status"] == true) {

                // check if user exists in our database
                $USER = $DB->getUser($_POST['username']);
                if (empty($USER)) {
                    // create user from LDAP
                    $new_user = newUser($_POST['username']);
                    if (empty($new_user)) {
                        die('Sorry, the user does not exist. Please contact system administrator!');
                    }
                    $osiris->persons->insertOne($new_user['person']);
                    $osiris->accounts->insertOne($new_user['account']);

                    $user = $new_user['account']['username'];

                    $USER = $DB->getUser($user);

                    // try to connect the user with existing authors
                    $updateResult = $osiris->activities->updateMany(
                        [
                            'authors.last' => $USER['last'],
                            'authors.first' => new MongoDB\BSON\Regex('^' . $USER['first'][0] . '.*')
                        ],
                        ['$set' => ["authors.$.user" => ($user)]]
                    );
                    $n = $updateResult->getModifiedCount();
                    $msg .= "&new=$n";
                }

                $_SESSION['username'] = $USER['username'];
                $_SESSION['name'] = $USER['displayname'];

                $updateResult = $osiris->account->updateOne(
                    ['username' => $_POST['username']],
                    ['$set' => ["lastlogin" => date('d.m.Y')]]
                );

                if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                    header("Location: " . $_POST['redirect'] . $msg);
                    die();
                }
                header("Location: " . ROOTPATH . "/" . $msg);
                die();
            }
        }
    }
    $breadcrumb = [
        ['name' => lang('User Login', 'Login')]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/userlogin.php";
    if (isset($auth)) {
        printMsg($auth["msg"], "error", "");
    }
    if (empty($_POST['username'])) {
        printMsg("Username is required!", "error", "");
    }
    if (empty($_POST['password'])) {
        printMsg("Password is required!", "error", "");
    }
    include BASEPATH . "/footer.php";
});

// Route::post('/ldap/synchonize', function () {
//     include_once BASEPATH . "/php/init.php";
//     include_once BASEPATH . "/php/_login.php";
//     $users = getUsers();

//     dump($users, true);

// }, 'login');


/* LOGIN AREA */

Route::get('/(activities|my-activities)', function ($page) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $path = $page;
    if ($page == 'activities') {
        $breadcrumb = [
            ['name' => lang("All activities", "Alle Aktivitäten")]
        ];
    } elseif (isset($_GET['user'])) {
        $user = $_GET['user'];
        $breadcrumb = [
            ['name' => lang("Activities of $user", "Aktivitäten von $user")]
        ];
    } else {
        $breadcrumb = [
            ['name' => lang("My activities", "Meine Aktivitäten")]
        ];
    }

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/all-activities.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/search/activities', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Search", "Suche")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/activity-search.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/activities/new', function () {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Add new", "Neu hinzufügen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/activities/new', function () {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    global $form;
    $form = $_POST['form'];
    // dump($form);
    $form = unserialize($form);
    $copy = true;

    $name = $form['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($form['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("New from Import", "Neu aus Import")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/teaching', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Teaching", "Lehrveranstaltungen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/teaching.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/projects', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Projects", "Projekte")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/projects.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/research-data', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Research data", "Forschungsdaten")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/research-data.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/research-data/(.*)', function ($name) {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang("Research data", "Forschungsdaten"), 'path' => "/research-data"],
        ['name' => $name]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/research-data-detail.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/pubmed-search', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Search in Pubmed", "Suche in Pubmed")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/search.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/(doi|pubmed)/(.*)', function ($type, $identifier) {
    include_once BASEPATH . "/php/init.php";

    $form = $osiris->activities->findOne([$type => $identifier]);
    if (!empty($form)) {
        $id = strval($form['_id']);
        header("Location: " . ROOTPATH . "/activities/view/$id");
    }
    echo "$type $identifier not found.";
});
Route::get('/activities/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);
    $activity = $osiris->activities->findOne(['_id' => $id], ['projection' => ['file' => 0]]);

    if (!empty($activity)) {

        $doc = json_decode(json_encode($activity->getArrayCopy()), true);
        $locked = $activity['locked'] ?? false;
        if ($doc['type'] == 'publication' && isset($doc['journal'])) {
            // fix old journal_ids
            if (isset($doc['journal_id']) && !preg_match("/^[0-9a-fA-F]{24}$/", $doc['journal_id'])) {
                $doc['journal_id'] = null;
                $osiris->activities->updateOne(
                    ['_id' => $activity['_id']],
                    ['$unset' => ['journal_id' => '']]
                );
            }
        }
        $DB->renderActivities(['_id' =>  $activity['_id']]);
        $user_activity = $DB->isUserActivity($doc, $user);

        $Format = new Document;
        $Format->setDocument($doc);

        $name = $activity['title'] ?? $id;
        if (strlen($name) > 20)
            $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
        $name = ucfirst($activity['type']) . ": " . $name;
        $breadcrumb = [
            ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
            ['name' => $name]
        ];
        if ($Format->hasSchema()) {
            $additionalHead = $Format->schema();
        }
    }
    include BASEPATH . "/header.php";

    if (empty($activity)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/activity.php";
    }
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/files/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $id = $DB->to_ObjectID($id);

    $doc = $osiris->activities->findOne(['_id' => $id]);

    $name = $doc['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($doc['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => $name, 'path' => "/activities/view/$id"],
        ['name' => lang("Files", "Dateien")]
    ];
    include BASEPATH . "/header.php";
    if (empty($doc)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/files.php";
    }
    include BASEPATH . "/footer.php";
}, 'login');


Route::post('/activities/files/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];

    $mongoid = $DB->to_ObjectID($id);

    $activity = $osiris->activities->findOne(['_id' => $mongoid]);

    $name = $activity['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($activity['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => $name, 'path' => "/activities/view/$id"],
        ['name' => lang("Files", "Dateien")]
    ];
    include BASEPATH . "/header.php";
    if (empty($activity)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/files.php";
    }
    // include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/view/([a-zA-Z0-9]*)/file', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $activity = $osiris->activities->findOne(['_id' => $id]);

    if (empty($activity)) {
        echo "Activity not found!";
    } else if (!isset($activity['file']) || empty($activity['file'])) {
        echo "No file found.";
    } else {
        header('Content-type: application/pdf');
        // header('Content-Disposition: attachment; filename="my.pdf"');
        echo $activity['file']->serialize();
    }
}, 'login');


Route::get('/activities/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $mongoid = $DB->to_ObjectID($id);

    global $form;
    $form = $osiris->activities->findOne(['_id' => $mongoid]);
    $copy = false;
    if (($form['locked'] ?? false) && !$USER['is_controlling']) {
        header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    }


    $name = $form['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($form['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => $name, 'path' => "/activities/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/activities/doublet/([a-zA-Z0-9]*)/([a-zA-Z0-9]*)', function ($id1, $id2) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";

    $Format = new Document(false, 'list');
    $Modules = new Modules();

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Doublet", "Dublette")]
    ];

    $form = [];
    $html = [];

    // first
    $form1 = $DB->getActivity($id1);
    // if (($form1['locked'] ?? false) && !$USER['is_controlling']) {
    //     header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    // }

    // second
    $form2 = $DB->getActivity($id2);


    include BASEPATH . "/header.php";
    if ($form1['type'] != $form2['type']) {
        echo "Error: Activities must be of the same type.";
    } else {

        // $form = array_merge_recursive($form1, $form2);
        $keys = array_keys(array_merge($form1, $form2));
        $ignore = [
            'rendered', 'editor-comment',  'updated', 'updated_by',  'created', 'created_by', 'duplicate'
        ];

        $Format->setDocument($form1);
        foreach ($keys as $key) {
            if (in_array($key, $ignore)) continue;
            $form[$key] = [
                $form1[$key] ?? null,
                $form2[$key] ?? null
            ];

            $html[$key] = [
                $Format->get_field($key),
                null
            ];
        }
        $Format->setDocument($form2);
        foreach ($keys as $key) {
            if (in_array($key, $ignore)) continue;
            $html[$key][1] = $Format->get_field($key);
        }
    }

    // dump($form, true);

    include BASEPATH . "/pages/doublets.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/copy/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);

    global $form;
    $form = $osiris->activities->findOne(['_id' => $id]);
    $copy = true;

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Copy", "Kopieren")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/edit/([a-zA-Z0-9]*)/(authors|editors)', function ($id, $role) {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $id = $DB->to_ObjectID($id);

    $form = $osiris->activities->findOne(['_id' => $id]);
    if (($form['locked'] ?? false) && !$USER['is_controlling']) {
        header("Location: " . ROOTPATH . "/activities/view/$id?msg=locked");
    }

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Edit", "Bearbeiten"), 'path' => "/activities/edit/$id"]
    ];
    if ($role == "authors") {
        $breadcrumb[] = ['name' => lang("Authors", "Autoren")];
    } else {
        $breadcrumb[] = ['name' => lang("Editors", "Editoren")];
    }

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/author-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/import', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Import')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/import.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::post('/import/google', function () {
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
    if (!isset($_POST["user"]) || !isset($_POST['doc']))
        exit - 1;

    include(BASEPATH . '/php/init.php');
    include(BASEPATH . '/php/GoogleScholar.php');
    $user = $_POST["user"];
    $google = new GoogleScholar($user);

    $docid = $_POST["doc"];
    $pub = $google->getDocumentDetails($docid);

    $result = [];

    if (empty($pub['title'])) die('Error: Title was empty.');
    if (empty($pub['Publikationsdatum'])) die('Error: Date was empty.');

    $result['type'] = 'publication';
    $result['title'] = $pub['title'];
    $result['doi'] = empty($pub['doi']) ? null : $pub['doi'];
    $date = explode('/', $pub['Publikationsdatum']);
    $result['year'] = intval($date[0]);
    $result['month'] = isset($date[1]) ? intval($date[1]) : null;
    $result['day'] = isset($date[2]) ? intval($date[2]) : null;

    $result['volume'] = $pub['Band'] ?? null;
    $result['issue'] = $pub['Ausgabe'] ?? null;
    $result['pages'] = $pub['Seiten'] ?? null;

    $result['pubtype'] = 'article';

    if (isset($pub['Zeitschrift']) || isset($pub['Quelle'])) {
        $result['journal'] = $pub['Zeitschrift'] ?? $pub['Quelle'];
        $j = new MongoDB\BSON\Regex('^' . trim($result['journal']) . '$', 'i');
        $journal = $osiris->journals->findOne(['journal' => ['$regex' => $j]]);
        if (!empty($journal)) {
            $result['journal_id'] = strval($journal['_id']);
            $result['journal'] = $journal['journal'];
        }
    } else if (isset($pub['Buch'])) {
        $result['book'] = $pub['Buch'];
        $result['publisher'] = $pub['Verlag'];
        $result['pubtype'] = 'chapter';
    } else {
        $result['publisher'] = $pub['Verlag'];
        $result['pubtype'] = 'book';
    }

    // update authors and check if they are in the database
    $result['authors'] = array();
    foreach ($pub['Autoren'] as $a) {
        $a = explode(' ', $a);
        $last = array_pop($a);
        $first = implode(' ', $a);
        $username = $DB->getUserFromName($last, $first);
        $author = [
            'first' => $first,
            'last' => $last,
            'user' => $username,
            'aoi' => !empty($username)
        ];
        $result['authors'][] = $author;
    }

    // insert document into the database
    $result['created'] = date('Y-m-d');
    $result['created_by'] = $_SESSION['username'];


    if (isset($result['doi']) && !empty($result['doi'])) {
        $doi_exist = $$osiris->activities->findOne(['doi' => $result['doi']]);
        if (!empty($doi_exist)) {
            die('DOI already exists. Publication could not be added.');
        }
    }
    $insertOneResult  = $osiris->activities->insertOne($result);
    $id = $insertOneResult->getInsertedId();
    $result['_id'] = $id;
    $DB->renderActivities(['_id' => $id]);

    $Format = new Document();
    $Format->setDocument($doc);

    echo json_encode([
        'inserted' => $insertOneResult->getInsertedCount(),
        'id' => strval($id),
        'result' => $result,
        'formatted' => $Format->formatShort()
    ]);

    // echo json_encode($result);
});


Route::post('/import/file', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Import'), 'path' => '/import'],
        ['name' => lang('From File', 'Aus Datei')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/import-file.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/user/browse', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/users-table.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/search/user', function () {
    include_once BASEPATH . "/php/init.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => lang("Search", "Suche")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-search.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/expertise', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang('Expertise search', 'Experten-Suche')]
    ];
    // include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/expertise.php";
    include BASEPATH . "/footer.php";
});

Route::get('/profile/?(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/Document.php";
    include_once BASEPATH . "/php/_achievements.php";

    $Format = new Document($user);

    $scientist = $DB->getUser($user);

    if (empty($scientist)) {
        header("Location: " . ROOTPATH . "/user/browse?msg=user-does-not-exist");
        die;
    }
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/profile.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/my-year/?(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/Document.php";
    $Format = new Document($user);

    $scientist = $DB->getUser($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => lang("$name", "$name"), 'path' => "/profile/$user"],
        ['name' => lang("The Year", "Das Jahr")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/my-year.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/controlling', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$USER['is_controlling'] && !$USER['is_admin']) die('You have no permission to be here.');
    $breadcrumb = [
        // ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        // ['name' => lang('Table', 'Tabelle'), 'path' => "/journal"],
        ['name' => lang("Controlling")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/controlling.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/controlling', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$USER['is_controlling'] && !$USER['is_admin']) die('You have no permission to be here.');

    $breadcrumb = [
        ['name' => lang("Controlling")]
    ];

    include BASEPATH . "/header.php";

    $changes = 0;
    if (isset($_POST['action']) && isset($_POST['start']) && isset($_POST['end'])) {

        $lock = ($_POST['action'] == 'lock');
        // dump($lock);

        $cursor = $DB->get_reportable_activities($_POST['start'], $_POST['end']);
        foreach ($cursor as $doc) {
            // dump($doc['title'] ?? 'REVIEW');

            if ($lock) {
                // in progress stuff is not locked
                if (
                    (
                        ($doc['type'] == 'misc' && $doc['iteration'] == 'annual') ||
                        ($doc['type'] == 'review' && in_array($doc['role'], ['Editor', 'editorial']))
                    ) && is_null($doc['end'])
                ) {
                    continue;
                }
                if ($doc['type'] == "students" && isset($doc['status']) && $doc['status'] == 'in progress') {
                    continue;
                }
            }

            $updateResult = $osiris->activities->updateOne(
                ['_id' => $doc['_id']],
                ['$set' => ['locked' => $lock]]
            );

            $changes += $updateResult->getModifiedCount();
        }
        // construct output message
        $header = $lock ? lang('Locked activities.', 'Aktivitäten gesperrt.') : lang('Unlocked activities.', 'Aktivitäten entsperrt.');
        $text = lang(
            "Successfully changed the status of $changes activities.",
            "Es wurde erfolgreich der Status von $changes Aktivitäten geändert."
        );
        printMsg($text, 'success', $header);
    } else {
        echo 'Nothing to do.';
    }

    include BASEPATH . "/pages/controlling.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/reset-settings', function () {
    include_once BASEPATH . "/php/init.php";
    if (!$USER['is_controlling'] && !$USER['is_admin']) die('You have no permission to be here.');


    $filename = BASEPATH . '/settings.default.json';
    $msg = 'settings-resetted';
    if (isset($_FILES["settings"])) {

        if ($_FILES['settings']['error'] != UPLOAD_ERR_OK) {
            $msg = match ($_FILES['settings']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            // printMsg($errorMsg, "error");
        } else {
            $filename = $_FILES["settings"]["tmp_name"];
            $msg = 'settings-replaced';
        }
    }
    $json = file_get_contents($filename);
    // $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents(BASEPATH . "/settings.json", $json);
    header("Location: " . ROOTPATH . "/admin/general?msg=$msg");
}, 'login');


Route::get('/admin/(activities|departments|general)', function ($page) {
    include_once BASEPATH . "/php/init.php";
    if (!$USER['is_controlling'] && !$USER['is_admin']) die('You have no permission to be here.');
    $breadcrumb = [
        ['name' => lang("Admin Dashboard $page")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/admin-$page.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::post('/admin/(activities|departments|general)', function ($page) {
    include_once BASEPATH . "/php/init.php";
    if (!$USER['is_controlling'] && !$USER['is_admin']) die('You have no permission to be here.');


    $json = $Settings->settings;

    foreach (['activities', 'departments', 'affiliation', 'startyear'] as $key) {
        if (isset($_POST[$key])) {
            $json[$key] = $_POST[$key];
        }
    }
    $msg = 'settings-saved';


    if (isset($_FILES["logo"])) {
        $filename = htmlspecialchars(basename($_FILES["logo"]["name"]));
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $filesize = $_FILES["logo"]["size"];
        $filepath = BASEPATH . "/img/logo-custom." . $filetype;

        // $filepath = ROOTPATH . "/uploads/$id/$filename";

        if ($_FILES['logo']['error'] != UPLOAD_ERR_OK) {
            $msg = match ($_FILES['logo']['error']) {
                1 => lang('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'Die hochgeladene Datei überschreitet die Richtlinie upload_max_filesize in php.ini'),
                2 => lang("File is too big: max 16 MB is allowed.", "Die Datei ist zu groß: maximal 16 MB sind erlaubt."),
                3 => lang('The uploaded file was only partially uploaded.', 'Die hochgeladene Datei wurde nur teilweise hochgeladen.'),
                4 => lang('No file was uploaded.', 'Es wurde keine Datei hochgeladen.'),
                6 => lang('Missing a temporary folder.', 'Der temporäre Ordner fehlt.'),
                7 => lang('Failed to write file to disk.', 'Datei konnte nicht auf die Festplatte geschrieben werden.'),
                8 => lang('A PHP extension stopped the file upload.', 'Eine PHP-Erweiterung hat den Datei-Upload gestoppt.'),
                default => lang('Something went wrong.', 'Etwas ist schiefgelaufen.') . " (" . $_FILES['file']['error'] . ")"
            };
            // printMsg($errorMsg, "error");
            $json['affiliation']['logo'] = $Settings->affiliation_details['logo'];
        } else if ($filesize > 2000000) {
            $msg = lang("File is too big: max 2 MB is allowed.", "Die Datei ist zu groß: maximal 2 MB sind erlaubt.");
            $json['affiliation']['logo'] = $Settings->affiliation_details['logo'];
        }
        if (file_exists($filepath)) {
            chmod($filepath, 0755); //Change the file permissions if allowed
            unlink($filepath); //remove the file
        }
        if (move_uploaded_file($_FILES["logo"]["tmp_name"], $filepath)) {
            $json['affiliation']['logo'] = "logo-custom." . $filetype;
        } else {
            $msg = lang("Sorry, there was an error uploading your file.", "Entschuldigung, aber es gab einen Fehler beim Dateiupload.");
            $json['affiliation']['logo'] = $Settings->affiliation_details['logo'];
        }
    }
    $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    file_put_contents(BASEPATH . "/settings.json", $json);
    header("Location: " . ROOTPATH . "/admin/$page?msg=settings-saved");
}, 'login');


Route::get('/visualize', function () {
    include_once BASEPATH . "/php/init.php";
    $breadcrumb = [
        ['name' => lang('Visualization', 'Visualisierung')]
    ];
    // include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/visualize.php";
    include BASEPATH . "/footer.php";
});

Route::get('/visualize/(\w*)', function ($page) {
    $names = [
        "coauthors" => lang('Coauthor network', 'Koautoren-Netzwerk'),
        "sunburst" => lang('Department overview', 'Abteilungs-Übersicht'),
        "departments" => lang('Department network', 'Abteilungs-netzwerk'),
    ];
    if (!array_key_exists($page, $names)) {
        die("404");
    }
    $breadcrumb = [
        ['name' => lang('Visualization', 'Visualisierung'), 'path' => "/visualize"],
        ['name' => $names[$page]]
    ];
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Document.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/visualize-$page.php";
    include BASEPATH . "/footer.php";
});



Route::get('/journal', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang('Table', 'Tabelle')]
    ];
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journals-table.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/journal/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => $data['journal']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-view.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/add', function () {
    include_once BASEPATH . "/php/init.php";
    $id = null;
    $data = [];
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang("Add", "Hinzufügen")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/init.php";

    $id = $DB->to_ObjectID($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => $data['journal'], 'path' => "/journal/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/user/edit/(.*)', function ($user) {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Document.php";

    // $id = $DB->to_ObjectID($id);

    $data = $DB->getUser($user);
    if (empty($data)) {
        header("Location: " . ROOTPATH . "/user/browse");
        die;
    }
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => $data['name'], 'path' => "/profile/$user"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/docs/([\w-]+)', function ($doc) {
    // header("HTTP/1.0 $error");

    include BASEPATH . "/php/init.php";
    // SassCompiler::run("scss/", "css/");

    $breadcrumb = [
        ['name' => lang('Documentation', 'Dokumentation'), 'path' => '/docs'],
        ['name' => lang($doc)]
    ];
    include BASEPATH . "/header.php";
    echo '<link href="' . ROOTPATH . '/css/documentation.css" rel="stylesheet">';
    echo '<script src="' . ROOTPATH . '/js/quill.min.js"></script>';
    echo '<script src="' . ROOTPATH . '/js/jquery-ui.min.js"></script>';
    $path    = BASEPATH . '/pages/docs';


    if (file_exists("$path/$doc.html")) {
        include "$path/$doc.html";
    } elseif (file_exists("$path/$doc.php")) {
        include "$path/$doc.php";
    } elseif (file_exists("$path/$doc.md")) {
        include_once BASEPATH . "/php/MyParsedown.php";
        $text = file_get_contents("$path/$doc.md");
        $parsedown = new Parsedown;

        echo '<div class="row">
            <div class="col-lg-9">';
        echo $parsedown->text($text);
        echo '</div>';

        echo '<div class="col-lg-3 d-none d-lg-block">
        <div class="on-this-page-nav" id="on-this-page-nav">
            <div class="content">
                <div class="title">On this page</div>
                ';
        foreach ($parsedown->header as $h) {
            if ($h['level'] == 1 || $h['level'] > 3) continue;
            $m = 10 * ($h['level'] - 2) + 10;
            echo "<a class='pl-$m' href='#$h[id]'>$h[text]</a>";
        }
        echo '</div>'; // content
        echo '</div>'; // on-this-page-nav
        echo '</div>'; // col-lg-3
        echo '</div>'; // row
    } else {
        echo "Doc not found.";
    }
    // include BASEPATH . "/pages/error.php";
    include BASEPATH . "/footer.php";
});

Route::get('/error/([0-9]*)', function ($error) {
    // header("HTTP/1.0 $error");
    http_response_code($error);
    include BASEPATH . "/header.php";
    echo "Error " . $error;
    // include BASEPATH . "/pages/error.php";
    include BASEPATH . "/footer.php";
});

Route::get('/user/logout', function () {
    unset($_SESSION["username"]);
    $_SESSION['loggedin'] = false;
    header("Location: " . ROOTPATH . "/");
}, 'login');

include_once BASEPATH . "/api.php";
include_once BASEPATH . "/mongo.php";
include_once BASEPATH . "/export.php";

if (IDA_INTEGRATION) {
    include_once BASEPATH . "/addons/ida/index.php";
}

Route::get('/components/([A-Za-z0-9\-]*)', function ($path) {
    include_once BASEPATH . "/php/init.php";
    include BASEPATH . "/components/$path.php";
});

Route::get('/check-duplicate-id', function () {
    include_once BASEPATH . "/php/init.php";

    if (!isset($_GET['type']) || !isset($_GET['id'])) die('false');
    if ($_GET['type'] != 'doi' && $_GET['type'] != 'pubmed') die('false');

    $form = $osiris->activities->findOne([$_GET['type'] => $_GET['id']]);
    if (empty($form)) die('false');
    echo 'true';
});

Route::get('/check-duplicate', function () {
    include_once BASEPATH . "/php/init.php";

    $values = $_GET['values'] ?? array();
    if (empty($values)) die('false');

    $search = [];
    if (isset($values['title']) && !empty($values['title'])) $search['title'] = new \MongoDB\BSON\Regex(preg_quote($values['title']), 'i');
    else die('false');

    if (isset($values['year']) && !empty($values['year'])) $search['year'] = intval($values['year']);
    else die('false');

    if (isset($values['month']) && !empty($values['month'])) $search['month'] = intval($values['month']);
    else die('false');

    if (isset($values['type']) && !empty($values['type'])) $search['type'] = trim($values['type']);
    else die('false');

    if (isset($values['subtype']) && !empty($values['subtype'])) $search['subtype'] = trim($values['subtype']);
    else die('false');

    // dump($search, true);
    $doc = $osiris->activities->findOne($search);

    // dump($doc, true);
    if (empty($doc)) die('false');

    // $format = new Document();
    // $format->setDocument($doc);
    // echo $format->format();
    echo $doc['rendered']['web'] ?? '';
});


Route::get('/settings', function () {
    include_once BASEPATH . "/php/init.php";

    $file_name = BASEPATH . "/settings.json";
    if (!file_exists($file_name)) {
        $file_name = BASEPATH . "/settings.default.json";
    }
    $json = file_get_contents($file_name);
    echo $json;
    // $settings = json_decode($json, true, 512, JSON_NUMERIC_CHECK);  
});

// include_once 'user_management.php';

// Route::get('/discover', function () {
//     include_once BASEPATH . "/php/init.php";

//     // user must be set
//     if (!isset($_GET['user']) || empty($_GET['user'])) die('user not set');

//     // get user account
//     $user = $DB->getUser($_GET['user']);

//     // if openalex id is not set: nothing to do
//     if (empty($user['openalex'])) die ('openalex not set');

//     // if checked within 3 days: nothing to do
//     if (isset($user['openalex-check']) && strtotime($user['openalex-check']) < strtotime('-3 days')) die ('time delayed');


//     $openalex = $user['openalex'];
//     $url = 'https://api.openalex.org/works?filter=author.id:' . $openalex . '&mailto=juk20@dsmz.de';

//     $ch = curl_init($url);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $response = curl_exec($ch);
//     $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//     if ($http != 200) die('fetch not possible');

//     // go through all results
//     $json = json_decode($response, TRUE);
//     $results = array();

//     foreach ($json['results'] as $entry) {
//         // check if doi is already in database
//         if (empty($entry['doi'] ?? null)) continue;
//         preg_match('/(10\.\d{4,5}\/[\S]+[^;,.\s])$/', $entry['doi'], $doi);

//         if ($osiris->activities->count(['doi'=>$doi[0]]) > 0) continue;

//         $results[] = $entry;
//     }

//     header("Content-Type: application/json");
//     header("Pragma: no-cache");
//     header("Expires: 0");
//     echo (json_encode($results));

// });



Route::get('/get-modules', function () {
    include_once BASEPATH . "/php/init.php";
    include_once BASEPATH . "/php/Modules.php";

    $form = array();
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $mongoid = $DB->to_ObjectID($_GET['id']);
        $form = $osiris->activities->findOne(['_id' => $mongoid]);
    }

    $Modules = new Modules($form, $_GET['copy'] ?? false);
    if (isset($_GET['modules'])) {
        $Modules->print_modules($_GET['modules']);
    } else {
        $Modules->print_all_modules();
    }
});

Route::get('/rerender', function () {
    include_once BASEPATH . "/php/init.php";

    $DB->renderActivities();
});

// temporary route to restructure users table
Route::get('/migrate', function () {
    include_once BASEPATH . "/php/init.php";
    $osiris->teachings->drop();
    $osiris->miscs->drop();
    $osiris->posters->drop();
    $osiris->publications->drop();
    $osiris->lectures->drop();
    $osiris->reviews->drop();
    $osiris->lecture->drop();

    $users = $osiris->users->find([]);

    $person_keys = [
        "first",
        "last",
        "academic_title",
        "displayname",
        "formalname",
        "names",
        "first_abbr",
        "department",
        "unit",
        "telephone",
        "mail",
        "dept",
        "orcid",
        "gender",
        "google_scholar",
        "researchgate",
        "twitter",
        "webpage",
        "expertise",
        "updated",
        "updated_by",
    ];

    $account_keys = [
        "is_admin",
        "is_controlling",
        "is_scientist",
        "is_leader",
        "is_active",
        "maintenance",
        "hide_achievements",
        "hide_coins",
        "display_activities",
        "lastlogin",
        "created",
        "maintenance",
        "approved",
    ];

    $osiris->persons->deleteMany([]);
    $osiris->accounts->deleteMany([]);
    $osiris->achieved->deleteMany([]);

    foreach ($users as $user) {
        // TODO: create graphic schema of the new structure
        $user = iterator_to_array($user);
        $username = strtolower($user['username']);


        $person = ["username" => $username];
        foreach ($person_keys as $key) {
            if (!array_key_exists($key, $user)) continue;
            $person[$key] = $user[$key];
            unset($user[$key]);
        }
        $osiris->persons->insertOne($person);

        $account = ["username" => $username];
        foreach ($account_keys as $key) {
            if (!array_key_exists($key, $user)) continue;
            $account[$key] = $user[$key];
            unset($user[$key]);
        }
        $osiris->accounts->insertOne($account);

        if (isset($user['achievements'])) {
            foreach ($user['achievements'] as $ac) {
                $ac['username'] = $username;
                $osiris->achieved->insertOne($ac);
            }
            unset($user['achievements']);
        }

        dump($user, true);
        dump($person);
        dump($account);
        echo "<hr>";
    }

    // MongoDB\Collection::createIndexes()
});


Route::get('/test', function () {
});



// Route::get('/calculate-if', function () {
//     include_once BASEPATH . "/php/init.php";
//     include BASEPATH . "/header.php";

//     $counts_by_year = '[{"year":2023,"works_count":1069,"cited_by_count":303430},{"year":2022,"works_count":4101,"cited_by_count":1108272},{"year":2021,"works_count":3778,"cited_by_count":1174045},{"year":2020,"works_count":3715,"cited_by_count":1074783},{"year":2019,"works_count":4085,"cited_by_count":948818},{"year":2018,"works_count":4060,"cited_by_count":898835},{"year":2017,"works_count":3927,"cited_by_count":844962},{"year":2016,"works_count":4166,"cited_by_count":829285},{"year":2015,"works_count":4252,"cited_by_count":822252},{"year":2014,"works_count":4195,"cited_by_count":819429},{"year":2013,"works_count":4302,"cited_by_count":806661},{"year":2012,"works_count":4401,"cited_by_count":744492}]';
//     $counts_by_year = json_decode($counts_by_year, true);

//     dump($counts_by_year, true);

//     $publications = [];
//     $citations = [];
//     $if = [];

//     foreach ($counts_by_year as $y) {
//         $publications[$y['year']] = $y['works_count'];
//         $citations[$y['year']] = $y['cited_by_count'];
//     }
//     foreach ($citations as $year => $cite) {
//         if (isset($publications[$year-1]) && isset($publications[$year-2])){
//             // if = c[y] / (p[y-1] + p[y-2])
//             $if[$year] = $cite / ($publications[$year-1] + $publications[$year-2]);
//         }
//     }

//     dump($if, true);


//     include BASEPATH . "/footer.php";
// });


Route::get('/impress', function () {
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/impressum.html";
    include BASEPATH . "/footer.php";
});



// Add a 404 not found route
Route::pathNotFound(function ($path) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    // header('HTTP/1.0 404 Not Found');
    http_response_code(404);
    $error = 404;
    // header('HTTP/1.0 404 Not Found');
    include BASEPATH . "/header.php";
    // $browser = $_SERVER['HTTP_USER_AGENT'];
    // var_dump($browser);
    include BASEPATH . "/pages/error.php";
    // echo "Error 404";
    include BASEPATH . "/footer.php";
});

// Add a 405 method not allowed route
Route::methodNotAllowed(function ($path, $method) {
    // Do not forget to send a status header back to the client
    // The router will not send any headers by default
    // So you will have the full flexibility to handle this case
    header('HTTP/1.0 405 Method Not Allowed');
    $error = 405;
    include BASEPATH . "/header.php";
    // include BASEPATH . "/pages/error.php";
    echo "Error 405";
    include BASEPATH . "/footer.php";
});


Route::run(ROOTPATH);
