<?php
session_start();



if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
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
    include BASEPATH . "/header.php";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        include BASEPATH . "/pages/userlogin.php";
    } else {
        include BASEPATH . "/pages/dashboard.php";
    }
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


Route::get('/achievements', function () {

    $breadcrumb = [
        ['name' => lang('Achievements', 'Errungenschaften')]
    ];

    include_once BASEPATH . "/php/_config.php";
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
        header("Location: " . ROOTPATH . "/?msg=welcome");
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
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/?msg=ali");
    }
    include BASEPATH . "/php/_login.php";
    include BASEPATH . "/php/_db.php";

    if (isset($_POST['username']) && isset($_POST['password'])) {
        // TODO: remove before live!
        if ($_SERVER['SERVER_NAME'] == 'testserver' || $_POST['username'] == 'test') {
            if ($_POST['username'] == "test") {
                $_SESSION['username'] = "juk20";
                $_SESSION['name'] = "Julia Koblitz";
            } else {
                $_SESSION['username'] = $_POST['username'];
                $_SESSION['name'] = "unknown";
            }
            $_SESSION['loggedin'] = true;
            if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                header("Location: " . $_POST['redirect'] . "?msg=welcome");
                die();
            }
            header("Location: " . ROOTPATH . "/?msg=welcome");
            die();
        } else {
            $auth = login($_POST['username'], $_POST['password']);
            if ($auth["status"] == true) {

                if (isset($_POST['redirect']) && !str_contains($_POST['redirect'], "//")) {
                    header("Location: " . $_POST['redirect'] . "?msg=welcome");
                    die();
                }
                header("Location: " . ROOTPATH . "/?msg=welcome");
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
    } else {
        $breadcrumb = [
            ['name' => lang("My activities", "Meine Aktivitäten")]
        ];
    }
    
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/all-activities.php";
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

Route::get('/activities/view/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    $user = $_SESSION['username'];

    $id = new MongoDB\BSON\ObjectId($id);

    $activity = $osiris->activities->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
        ['name' => lang("#" . $id)]
    ];
    include BASEPATH . "/header.php";
    if (empty($activity)) {
        echo "Activity not found!";
    } else {
        include BASEPATH . "/pages/activity.php";
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

    $form = $osiris->activities->findOne(['_id' => $id]);
    $copy = false;

    $breadcrumb = [
        ['name' => lang('Activities', "Aktivitäten"), 'path' => "/activities"],
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



Route::get('/(scientist)/?([a-z0-9]*)', function ($page, $user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    if (empty($user)) $user = $_SESSION['username'];
    include_once BASEPATH . "/php/format.php";    
    $Format = new Format($user);

    $scientist = getUserFromId($user);
    $name = $scientist['displayname'];

    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/browse/users"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/scientist.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/browse/(publication|activity|users|journal|poster)', function ($page) {
    // if ($page == 'users') 
    $breadcrumb = [
        ['name' => ucfirst($page)]
    ];
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/browse/$page.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/visualize', function () {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/visualize.php";
    include BASEPATH . "/footer.php";
});

Route::get('/test/journals', function () {
    include_once BASEPATH . "/php/_db.php";
    $journals = $osiris->journals->find()->toArray();
$result = ['data'=>[]];
// $i = 0;
    foreach ($journals as $doc) {
        // if ($i++ > 100) break;
        $result['data'][] = [
            'id' =>strval($doc['_id']),
            'name' =>$doc['journal'],
            'abbr'=> $doc['abbr'],
            'issn'=>implode(', ', $doc['issn']->bsonSerialize()),
            'if'=>impact_from_year($doc,intval(SELECTEDYEAR)-1) ?? ''
        ];
    }
    
    header("Content-Type: application/json");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo json_encode($result);
});

Route::get('/view/journal/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $id = new MongoDB\BSON\ObjectId($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/browse/journal"],
        ['name' => $data['journal']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/view/journal.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/edit/journal/([a-zA-Z0-9]*)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $id = new MongoDB\BSON\ObjectId($id);

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/browse/journal"],
        ['name' => $data['journal'], 'path' => "/view/journal/$id"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/journal.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/edit/user/([a-z0-9]+)', function ($user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    // $id = new MongoDB\BSON\ObjectId($id);

    $data = getUserFromId($user);
    $breadcrumb = [
        ['name' => lang('Users', 'Nutzer:innen'), 'path' => "/browse/users"],
        ['name' => $data['name'], 'path' => "/scientist/$user"],
        ['name' => lang("Edit", "Bearbeiten")]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/user.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/docs/([\w-]+)', function ($doc) {
    // header("HTTP/1.0 $error");
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/docs/$doc.html";
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
