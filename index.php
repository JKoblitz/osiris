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
        if ($USER['is_controlling']) {
            include BASEPATH . "/pages/controlling.php";
        } elseif ($USER['is_scientist']) {
            $user = $_SESSION['username'];
            $name = $_SESSION['name'];
            include BASEPATH . "/pages/scientist.php";
        }
    }
    include BASEPATH . "/footer.php";
});


Route::get('/index.php', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/userlogin.php";
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
Route::get('/news', function () {

    $breadcrumb = [
        ['name' => lang('News', 'Neuigkeiten')]
    ];

    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/Parsedown.php";

    include BASEPATH . "/header.php";

    $text = file_get_contents(BASEPATH . "/news.md");
    $parsedown = new Parsedown;
    echo $parsedown->text($text);

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


Route::get('/(publication|review|poster|lecture|misc|teaching)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $user = $_SESSION['username'];
    $path = $page;
    $breadcrumb = [
        // ['name' => 'Reviews', 'path' => "/browse/review"],
        ['name' => $path . "s"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/$path.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/(publication)/add', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => 'Publications', 'path' => "/publication"],
        ['name' => "Add"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/add-publication.php";
    include BASEPATH . "/footer.php";
}, 'login');



Route::get('/browse/(publication|activity|scientist|journal|poster)', function ($page) {
    $idname = $page . '_id';
    $table = $page;
    if ($page == 'scientist') {
        $table = 'users';
        $idname = "user";
    }

    $breadcrumb = [
        ['name' => ucfirst($page)]
    ];
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/browse/$page.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/view/journal/(\d+)', function ($id) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    if (is_numeric($id)) {
        $id = intval($id);
    } else {
        $id = new MongoDB\BSON\ObjectId($id);
    }

    $data = $osiris->journals->findOne(['_id' => $id]);
    $breadcrumb = [
        ['name' => lang('Journals', 'Journale'), 'path' => "/browse/journal"],
        ['name' => $data['journal']]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/view/journal.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::get('/(view)/(scientist)/([a-z0-9]+)', function ($mode, $page, $user) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";
    include_once BASEPATH . "/php/format.php";
    $idname = "user";

    $name = $USER['last'] . ", " . $USER['first'];

    $breadcrumb = [
        ['name' => ucfirst($page), 'path' => "/browse/$page"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/scientist.php";
    include BASEPATH . "/footer.php";
}, 'login');

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



Route::get('/components/([A-Za-z0-9\-]*)', function ($path) {
    include_once BASEPATH . "/php/_db.php";
    include BASEPATH . "/components/$path.php";
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
