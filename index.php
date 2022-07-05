<?php
session_start();


define('ROOTPATH', '/research-report');
define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);


// Language settings and cookies
if (!empty($_GET['language'])) {
    $_COOKIE['research-report-language'] = $_GET['language'] === 'en' ? 'en' : 'de';
    $domain = ($_SERVER['HTTP_HOST'] != 'testserver') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('research-report-language', $_COOKIE['research-report-language'], [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ]);
}

function lang($en, $de = null)
{
    if ($de === null) return $en;
    if (!isset($_COOKIE['research-report-language'])) return $en;
    if ($_COOKIE['research-report-language'] == "en") return $en;
    if ($_COOKIE['research-report-language'] == "de") return $de;
    return $en;
}

include_once BASEPATH . "/php/Route.php";

Route::add('/', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        include BASEPATH . "/main.php";
    } else {
        $user = $_SESSION['username'];
        $name = $_SESSION['name'];
        include BASEPATH . "/scientist.php";
    }
    include BASEPATH . "/footer.php";
});


Route::add('/index.php', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/main.php";
    include BASEPATH . "/footer.php";
});

Route::add('/todo', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/todo.html";
    include BASEPATH . "/footer.php";
});


Route::add('/(add-publication)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => 'Publications', 'path' => "/browse/publication"],
        ['name' => "Add publication"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/$page.php";
    include BASEPATH . "/footer.php";
});


Route::add('/add-publication', function () {
    include_once BASEPATH . "/php/_config.php";

    // add journal
    $journal = $_POST['journal'];
    $journal_id = null;
    if (!empty($journal)) {
        $stmt = $db->prepare("SELECT journal_id FROM `journal` WHERE journal_name LIKE ? OR journal_abbr LIKE ?");
        $stmt->execute([$journal, $journal]);
        $journal_id = $stmt->fetch(PDO::FETCH_COLUMN);
        if (empty($journal_id)) {
            $stmt = $db->prepare("INSERT INTO `journal` (journal_name, journal_abbr) VALUES (?,?)");
            $stmt->execute([$journal, $journal]);
            $journal_id = $db->lastInsertId();
        }
    }

    // calculate time values
    $dateStr = $_POST['date_publication'];
    $date = strtotime($dateStr);
    $year = date("Y", $date);
    $month = date("n", $date);
    $quarter = ceil($month / 3);

    // check if publication exists
    $stmt = $db->prepare("SELECT publication_id FROM `publication` WHERE doi LIKE ?");
    $stmt->execute([trim($_POST['doi'])]);
    $pub_id = $stmt->fetch(PDO::FETCH_COLUMN);
    if (!empty($pub_id)) {
        header("Location: " . ROOTPATH . "/view/publication/$pub_id?msg=already-exists");
        die();
    }

    // add publication
    $stmt = $db->prepare(
        "INSERT INTO `publication` 
        (title, journal_id, `year`, date_publication, issue, pages, volume, doi, `type`, book_title, open_access, epub, quartal) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
    );
    $stmt->execute([
        $_POST['title'],
        $journal_id,
        $year,
        $_POST['date_publication'],
        $_POST['issue'],
        $_POST['pages'],
        $_POST['volume'],
        trim($_POST['doi']),
        $_POST['type'],
        $_POST['book_title'],
        (isset($_POST['open_access']) ? 1 : 0),
        (isset($_POST['epub']) ? 1 : 0),
        "${year}Q${quarter}"
    ]);
    $pub_id = $db->lastInsertId();

    // add authors
    addAuthors($_POST['author'], intval($_POST['first_authors'] ?? 1), 'publication', $pub_id);

    header("Location: " . ROOTPATH . "/view/publication/$pub_id?msg=added-successfully");
}, 'post');


Route::add('/(add-activity)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => 'Publications', 'path' => "/browse/activity"],
        ['name' => "Add activity"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/$page.php";
    include BASEPATH . "/footer.php";
});

Route::add('/(add-poster)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $breadcrumb = [
        ['name' => 'Publications', 'path' => "/browse/poster"],
        ['name' => "Add poster"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/$page.php";
    include BASEPATH . "/footer.php";
});

Route::add('/add-poster', function () {
    include_once BASEPATH . "/php/_config.php";

    // var_dump($_POST);
    // die();
    // calculate time values
    $dateStr = $_POST['date_start'];
    $date = strtotime($dateStr);
    $year = date("Y", $date);
    $month = date("n", $date);
    $quarter = ceil($month / 3);

    // TODO: check if Poster exists
    // $stmt = $db->prepare("SELECT publication_id FROM `publication` WHERE doi LIKE ?");
    // $stmt->execute([trim($_POST['doi'])]);
    // $pub_id = $stmt->fetch(PDO::FETCH_COLUMN);
    // if (!empty($pub_id)) {
    //     header("Location: " . ROOTPATH . "/view/publication/$pub_id?msg=already-exists");
    //     die();
    // }

    // add poster
    $stmt = $db->prepare(
        "INSERT INTO `poster` 
        (title, conference, date_start, date_end, `location`, quartal) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $_POST['title'],
        $_POST['conference'] ?? null,
        $_POST['date_start'],
        (empty($_POST['date_end']) ? $_POST['date_start'] : $_POST['date_end']),
        $_POST['location'] ?? null,
        "${year}Q${quarter}"
    ]);
    $poster_id = $db->lastInsertId();

    // add authors
    addAuthors($_POST['author'], intval($_POST['first_authors'] ?? 1), 'poster', $poster_id);

    header("Location: " . ROOTPATH . "/view/poster/$poster_id?msg=added-successfully");
}, 'post');


Route::add('/browse/(publication|activity|scientist|journal|poster)', function ($page) {
    $idname = $page . '_id';
    if ($page == 'scientist') $idname = "user";

    $breadcrumb = [
        ['name' => ucfirst($page)]
    ];
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/browse.php";
    include BASEPATH . "/footer.php";
});

Route::add('/(view|edit)/(publication|activity|journal|poster)/(\d+)', function ($mode, $page, $id) {
    include_once BASEPATH . "/php/_config.php";
    $idname = $page . '_id';
    if ($page == 'scientist') $idname = "user";

    $stmt = $db->prepare("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA LIKE 'research_report' AND TABLE_NAME LIKE ?");
    $stmt->execute([$page]);
    $schemata = $stmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT * FROM `$page` WHERE `$idname` = ? LIMIT 1");
    $stmt->execute([$id]);
    $dataset = $stmt->fetch(PDO::FETCH_ASSOC);

    $breadcrumb = [
        ['name' => ucfirst($page), 'path' => "/browse/$page"],
        ['name' => $dataset[$idname]]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/$mode.php";
    include BASEPATH . "/footer.php";
});

Route::add('/(view)/(scientist)/([a-z0-9]+)', function ($mode, $page, $user) {
    include_once BASEPATH . "/php/_config.php";
    $idname = "user";

    $stmt = $db->prepare("SELECT * FROM `scientist` WHERE `user` LIKE ? LIMIT 1");
    $stmt->execute([$user]);
    $dataset = $stmt->fetch(PDO::FETCH_ASSOC);

    $name = $dataset['last_name'] . ", " . $dataset['first_name'];

    $breadcrumb = [
        ['name' => ucfirst($page), 'path' => "/browse/$page"],
        ['name' => $name]
    ];

    include BASEPATH . "/header.php";
    include BASEPATH . "/scientist.php";
    include BASEPATH . "/footer.php";
});

Route::add('/error/([0-9]*)', function ($error) {
    // header("HTTP/1.0 $error");
    http_response_code($error);
    include BASEPATH . "/header.php";
    include BASEPATH . "/error.php";
    include BASEPATH . "/footer.php";
});



Route::add('/user/login', function () {
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
    include BASEPATH . "/userlogin.php";
    include BASEPATH . "/footer.php";
}, 'get');


Route::add('/user/login', function () {
    $page = "userlogin";
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        header("Location: " . ROOTPATH . "/?msg=ali");
    }
    include BASEPATH . "/php/_login.php";
    include BASEPATH . "/php/_db.php";

    if (isset($_POST['username']) && isset($_POST['password'])) {
        if ($_SERVER['SERVER_NAME'] == 'testserver') {
            $_SESSION['username'] = "juk20";
            $_SESSION['loggedin'] = true;
            $_SESSION['name'] = "Julia Koblitz";
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
    include BASEPATH . "/userlogin.php";
    if (isset($auth)) {
        print_r($auth["msg"], "error", "");
    }
    if (empty($_POST['username'])) {
        print_r("Username is required!", "error", "");
    }
    if (empty($_POST['password'])) {
        print_r("Password is required!", "error", "");
    }
    include BASEPATH . "/footer.php";
}, 'post');



Route::add('/user/logout', function () {
    unset($_SESSION["username"]);
    $_SESSION['loggedin'] = false;
    header("Location: " . ROOTPATH . "/");
}, 'get');


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
    // include BASEPATH . "/error.php";
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
    // include BASEPATH . "/error.php";
    echo "Error 405";
    include BASEPATH . "/footer.php";
});


Route::run(ROOTPATH);
