<?php
session_start();



if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle)
    {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle)
    {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}

$sn = $_SERVER['SERVER_NAME'];
if ($sn == 'testserver' || $sn == 'localhost' || $sn == 'juk20-dev.dsmz.local') {
    define('ROOTPATH', '/osiris');
} else {
    define('ROOTPATH', '');
}
define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);
define('AFFILIATION', 'DSMZ');

// Language settings and cookies
if (!empty($_GET['language'])) {
    $_COOKIE['osiris-language'] = $_GET['language'] === 'en' ? 'en' : 'de';
    $domain = ($_SERVER['HTTP_HOST'] != 'testserver') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('osiris-language', $_COOKIE['osiris-language'], [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ]);
}

if (!empty($_GET['select-quarter'])) {
    $_COOKIE['osiris-quarter'] = $_GET['select-quarter'];
    $domain = ($_SERVER['HTTP_HOST'] != 'testserver') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('osiris-quarter', $_COOKIE['osiris-quarter'], [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ]);
}
if (!empty($_GET['select-year'])) {
    $_COOKIE['osiris-year'] = $_GET['select-year'];
    $domain = ($_SERVER['HTTP_HOST'] != 'testserver') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('osiris-year', $_COOKIE['osiris-year'], [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ]);
}

$year = date("Y");
$month = date("n");
$quarter = ceil($month / 3);
define('CURRENTQUARTER', intval($quarter));
define('CURRENTMONTH', intval($month));
define('CURRENTYEAR', intval($year));
define('SELECTEDQUARTER', intval($_COOKIE['osiris-quarter'] ?? CURRENTQUARTER));
define('SELECTEDYEAR', intval($_COOKIE['osiris-year'] ?? CURRENTYEAR));


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
    include_once BASEPATH . "/php/_db.php";
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
Route::get('/dashboard', function () {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/dashboard.php";
    include BASEPATH . "/footer.php";
});

Route::get('/about', function () {

    $breadcrumb = [
        ['name' => lang('About OSIRIS', 'Über OSIRIS')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/about.php";
    include BASEPATH . "/footer.php";
});

Route::get('/issues', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];

    $breadcrumb = [
        ['name' => lang('Issues', 'Warnungen')]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/issues.php";
    include BASEPATH . "/footer.php";
});

Route::get('/lom', function () {

    $breadcrumb = [
        ['name' => lang('LOM', 'LOM')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/lom.php";
    include BASEPATH . "/footer.php";
});
Route::post('/lom', function () {
    $json = json_encode($_POST['json'], JSON_PRETTY_PRINT);
    file_put_contents(BASEPATH . "/matrix.json", $json);
    header("Location: " . ROOTPATH . "/lom?msg=success");
});


Route::get('/achievements/?([a-z0-9]*)', function ($user) {
    if (empty($user)) $user = $_SESSION['username'];

    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/_achievements.php";

    $scientist = getUserFromId($user);
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

Route::get('/news', function () {

    $breadcrumb = [
        ['name' => lang('News', 'Neuigkeiten')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/MyParsedown.php";

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/news.php";
    include BASEPATH . "/footer.php";
});

Route::get('/docs', function () {

    $breadcrumb = [
        ['name' => lang('Documentation', 'Dokumentation')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/MyParsedown.php";

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/docs.php";
    include BASEPATH . "/footer.php";
});

Route::get('/license', function () {

    $breadcrumb = [
        ['name' => lang('License', 'Lizenz')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/license.html";
    include BASEPATH . "/footer.php";
});

Route::get('/user/login', function () {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => lang('User login', 'Login')]
    ];
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        header("Location: " . ROOTPATH . "");
    }
    include BASEPATH . "/header.php";

    if (isset($_GET['redirect'])) {
        echo (lang("You need to be logged in to see this page.", "Du musst eingeloggt sein, um diese Seite zu sehen."));
    }
    include BASEPATH . "/pages/userlogin.php";
    include BASEPATH . "/footer.php";
});


Route::post('/user/login', function () {
    include_once BASEPATH . "/php/_config.php";
    $page = "userlogin";
    $msg = "?msg=welcome";
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/?msg=ali");
    }
    include BASEPATH . "/php/_login.php";
    include BASEPATH . "/php/_db.php";

    if (isset($_POST['username']) && isset($_POST['password'])) {
        // TODO: remove before live!
        if ($_SERVER['SERVER_NAME'] == 'testserver' || $_POST['username'] == 'test') {

            // check if user exists in our database
            $USER = getUserFromId($_POST['username']);
            if (empty($USER)) {
                // create user from LDAP
                $USER = updateUser($_POST['username']);
                if (empty($USER)) {
                    die('Sorry, the user does not exist. Please contact system administrator!');
                }
                $osiris->users->insertOne($USER);

                // try to connect the user with existing authors
                $updateResult = $osiris->activities->updateMany(
                    [
                        'authors.last' => $USER['last'],
                        'authors.first' => new MongoDB\BSON\Regex('^' . $USER['first'][0] . '.*')
                    ],
                    ['$set' => ["authors.$.user" => $username]]
                );
                $n = $updateResult->getModifiedCount();
                $msg .= "&new=$n";
            }

            if ($_POST['username'] == "test") {
                $_SESSION['username'] = "juk20";
                $_SESSION['name'] = "Julia Koblitz";
            } else {
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['name'] = "unknown";
            }
            $_SESSION['loggedin'] = true;

            if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                header("Location: " . $_POST['redirect'] . $msg);
                die();
            }
            header("Location: " . ROOTPATH . "/" . $msg);
            die();
        } else {
            $auth = login($_POST['username'], $_POST['password']);
            if ($auth["status"] == true) {

                // check if user exists in our database
                $USER = getUserFromId($_POST['username']);
                if (empty($USER)) {
                    // create user from LDAP
                    $USER = updateUser($_POST['username']);
                    if (empty($USER)) {
                        die('Sorry, the user does not exist. Please contact system administrator!');
                    }
                    $osiris->users->insertOne($USER);

                    // try to connect the user with existing authors
                    $updateResult = $osiris->activities->updateMany(
                        [
                            'authors.last' => $USER['last'],
                            'authors.first' => new MongoDB\BSON\Regex('^' . $USER['first'][0] . '.*')
                        ],
                        ['$set' => ["authors.$.user" => $username]]
                    );
                    $n = $updateResult->getModifiedCount();
                    $msg .= "&new=$n";
                }

                $updateResult = $osiris->users->updateOne(
                    [ '_id' => $_POST['username']],
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


/* LOGIN AREA */

Route::get('/(activities|my-activities)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

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


Route::get('/activities/search', function () {
    include_once BASEPATH . "/php/_config.php";
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
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Add new", "Neu hinzufügen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/activities/beta', function () {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("Add new", "Neu hinzufügen")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/add-activity-beta.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/activities/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];

    $id = new MongoDB\BSON\ObjectId($id);

    $activity = $osiris->activities->findOne(['_id' => $id], ['projection' => ['file' => 0]]);

    $name = $activity['title'] ?? $id;
    if (strlen($name) > 20)
        $name = mb_substr(strip_tags($name), 0, 20) . "&hellip;";
    $name = ucfirst($activity['type']) . ": " . $name;
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => $name]
    ];
    include BASEPATH . "/header.php";
    if (empty($activity)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/activity.php";
    }
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/activities/files/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];

    $id = new MongoDB\BSON\ObjectId($id);

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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];

    $mongoid = new MongoDB\BSON\ObjectId($id);

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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $id = new MongoDB\BSON\ObjectId($id);

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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $user = $_SESSION['username'];
    $id = new MongoDB\BSON\ObjectId($id);

    global $form;
    $form = $osiris->activities->findOne(['_id' => $id]);
    $copy = false;


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


Route::get('/activities/copy/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $user = $_SESSION['username'];
    $id = new MongoDB\BSON\ObjectId($id);

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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];
    $id = new MongoDB\BSON\ObjectId($id);

    $form = $osiris->activities->findOne(['_id' => $id]);

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



Route::get('/user/browse', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen')]
    ];
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/users-table.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/user/search', function () {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => lang("Search", "Suche")]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/user-search.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/(profile)/?([a-z0-9]*)', function ($page, $user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/format.php";
    include_once BASEPATH . "/php/_achievements.php";

    $Format = new Format($user);

    $scientist = getUserFromId($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/user/browse"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/profile.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/scientist/?([a-z0-9]*)', function ($user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/format.php";
    $Format = new Format($user);

    $scientist = getUserFromId($user);
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


Route::get('/visualize', function () {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => lang('Visualization', 'Visualisierung')]
    ];
    // include_once BASEPATH . "/php/_db.php";
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
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/visualize-$page.php";
    include BASEPATH . "/footer.php";
});


Route::get('/journal', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale')]
    ];
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journals-overview.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/browse', function () {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang('Table', 'Tabelle')]
    ];
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journals-table.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/journal/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $id = new MongoDB\BSON\ObjectId($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang('Table', 'Tabelle'), 'path' => "/journal/browse"],
        ['name' => $data['journal']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-view.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/journal/edit/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $id = new MongoDB\BSON\ObjectId($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/journal"],
        ['name' => lang('Table', 'Tabelle'), 'path' => "/journal/browse"],
        ['name' => $data['journal'], 'path' => "/journal/view/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/journal-editor.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/user/edit/([a-z0-9]+)', function ($user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    // $id = new MongoDB\BSON\ObjectId($id);

    $data = getUserFromId($user);
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
    include BASEPATH . "/header.php";
    $path    = BASEPATH . '/pages/docs';

    $breadcrumb = [
        ['name' => lang('Documentation', 'Dokumentation')]
    ];

    if (file_exists("$path/$doc.html")) {
        include "$path/$doc.html";
    } elseif (file_exists("$path/$doc.php")) {
        include "$path/$doc.php";
    } elseif (file_exists("$path/$doc.md")) {
        include_once BASEPATH . "/php/MyParsedown.php";
        $text = file_get_contents("$path/$doc.md");
        $parsedown = new Parsedown;
        // $header = $parsedown->header;
        // $header = array_filter($header, function ($v) {
        //     return $v['level'] <= 2;
        // });
        // $headers = array_merge($headers, $header);
        echo "<div class='container'>";
        echo $parsedown->text($text);
        echo "</div>";
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

Route::get('/info', function () {
    include BASEPATH . "/header.php";
    phpinfo();
    include BASEPATH . "/footer.php";
}, 'login');

include_once BASEPATH . "/api.php";
include_once BASEPATH . "/mongo.php";

include_once BASEPATH . "/export.php";
include_once BASEPATH . "/user_management.php";



Route::get('/components/([A-Za-z0-9\-]*)', function ($path) {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/components/$path.php";
});


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
                '$or' => array(
                    ['end.year' => array('$gte' => SELECTEDYEAR)],
                    ['end' => null]
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
    // include BASEPATH . "/pages/error.php";
    echo "Error 404";
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
