<?php
session_start();



if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}


define('ROOTPATH', '/osiris');
define('BASEPATH', $_SERVER['DOCUMENT_ROOT'] . ROOTPATH);

include_once BASEPATH . "/php/User.php";
$user = new User($_SESSION['username'] ?? null);
define('USER', $user);

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

$year = date("Y");
$month = date("n");
$quarter = ceil($month / 3);
define('CURRENTQUARTER', "${year}Q$quarter");
define('SELECTEDQUARTER', $_COOKIE['osiris-quarter'] ?? CURRENTQUARTER);


function lang($en, $de = null)
{
    if ($de === null) return $en;
    if (!isset($_COOKIE['osiris-language'])) return $en;
    if ($_COOKIE['osiris-language'] == "en") return $en;
    if ($_COOKIE['osiris-language'] == "de") return $de;
    return $en;
}

include_once BASEPATH . "/php/Route.php";

Route::add('/', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] === false) {
        include BASEPATH . "/main.php";
    } elseif (USER->is_controlling()) {
        include BASEPATH . "/controlling.php";
    } elseif (USER->is_scientist()) {
        $user = $_SESSION['username'];
        $name = $_SESSION['name'];
        include_once BASEPATH . "/php/Publication.php";
        include_once BASEPATH . "/php/Poster.php";
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
Route::add('/about', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/about.php";
    include BASEPATH . "/footer.php";
});

Route::add('/license', function () {
    include_once BASEPATH . "/php/_config.php";
    include BASEPATH . "/header.php";
    include BASEPATH . "/license.html";
    include BASEPATH . "/footer.php";
});


// Route::add('/(my-publication)', function ($page) {
//     include_once BASEPATH . "/php/_config.php";
//     $breadcrumb = [
//         ['name' => 'Publications', 'path' => "/browse/publication"],
//         ['name' => "Add publication"]
//     ];
//     include BASEPATH . "/header.php";
//     include BASEPATH . "/$page.php";
//     include BASEPATH . "/footer.php";
// });


Route::add('/(my-review|my-poster|my-lecture|my-publication)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $path = str_replace('my-', '', $page);
    $breadcrumb = [
        // ['name' => 'Reviews', 'path' => "/browse/review"],
        ['name' => "My " . $path . "s"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/editor/$path.php";
    include BASEPATH . "/footer.php";
});

Route::add('/(my-publication)/add', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => 'My Publications', 'path' => "/my-publication"],
        ['name' => "Add"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/editor/add-publication.php";
    include BASEPATH . "/footer.php";
});


Route::add('/my-publication', function () {
    include_once BASEPATH . "/php/_config.php";

    // add journal
    $journal = $_POST['journal'];
    $journal_id = addJournal($journal);

    // calculate time values
    $dateStr = $_POST['date_publication'];
    $date = strtotime($dateStr);
    $year = date("Y", $date);
    $month = date("n", $date);
    $day = date("d", $date);
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
        (title, journal_id, `year`, `month`, `day`, issue, pages, volume, doi, `type`, book, open_access, epub, q_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        "
    );
    $stmt->execute([
        $_POST['title'],
        $journal_id,
        $year,
        $month,
        $day,
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


// Route::add('/(my-activity)', function ($page) {
//     include_once BASEPATH . "/php/_config.php";
//     $breadcrumb = [
//         ['name' => 'Activity', 'path' => "/browse/activity"],
//         ['name' => "Add activity"]
//     ];
//     include BASEPATH . "/header.php";
//     include BASEPATH . "/$page.php";
//     include BASEPATH . "/footer.php";
// });

// Route::add('/(my-poster)', function ($page) {
//     include_once BASEPATH . "/php/_config.php";
//     $breadcrumb = [
//         ['name' => 'Poster', 'path' => "/browse/poster"],
//         ['name' => "Add poster"]
//     ];
//     include BASEPATH . "/header.php";
//     include BASEPATH . "/$page.php";
//     include BASEPATH . "/footer.php";
// });

Route::add('/my-(poster|lecture)', function ($table) {
    include_once BASEPATH . "/php/_config.php";

    // var_dump($_POST);
    // die();
    // calculate time values
    $dateStr = $_POST['date_start'];
    $date = strtotime($dateStr);
    $year = date("Y", $date);
    $month = date("n", $date);
    $quarter = ceil($month / 3);

    $authors = $_POST['author'] ?? array();

    // TODO: check if Poster exists
    // $stmt = $db->prepare("SELECT publication_id FROM `publication` WHERE doi LIKE ?");
    // $stmt->execute([trim($_POST['doi'])]);
    // $pub_id = $stmt->fetch(PDO::FETCH_COLUMN);
    // if (!empty($pub_id)) {
    //     header("Location: " . ROOTPATH . "/view/publication/$pub_id?msg=already-exists");
    //     die();
    // }
    if ($table == 'poster') {
        // add poster
        $stmt = $db->prepare(
            "INSERT INTO `poster` 
            (title, conference, date_start, date_end, `location`, q_id) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_POST['title'],
            $_POST['conference'] ?? null,
            $_POST['date_start'],
            (empty($_POST['date_end'] ?? null) ? $_POST['date_start'] : $_POST['date_end']),
            $_POST['location'] ?? null,
            "${year}Q${quarter}"
        ]);
    } elseif ($table == 'lecture') {
        if (isset($_POST['repetition'])){
            $stmt = $db->prepare(
                "SELECT * FROM `lecture` WHERE lecture_id = ?"
            );
            $stmt->execute([$_POST['repetition']]);
            $lect = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($lect)){
                header("Location: " . ROOTPATH . "/my-lecture?msg=error");
                die();
            }
            
            $values = [
                $lect['title'],
                $lect['conference'] ?? null,
                $_POST['date_start'],
                $lect['location'] ?? null,
                $lect['lecture_type']. " ".'repetition',
                "${year}Q${quarter}"
            ];
            
            $stmt = $db->prepare(
                "SELECT CONCAT(last_name, ';', first_name, ';', aoi) FROM `authors` WHERE lecture_id = ?"
            );
            $stmt->execute([$_POST['repetition']]);
            $authors = $stmt->fetchAll(PDO::FETCH_COLUMN);

        } else {
            $values = [
                $_POST['title'],
                $_POST['conference'] ?? null,
                $_POST['date_start'],
                $_POST['location'] ?? null,
                $_POST['lecture_type'] ?? 'short',
                "${year}Q${quarter}"
            ];
        }
        // add poster
        $stmt = $db->prepare(
            "INSERT INTO `lecture` 
            (title, conference, date_start, `location`, lecture_type, q_id) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute($values);
    }
    $activity_id = $db->lastInsertId();

    // add authors
    addAuthors($authors, intval($_POST['first_authors'] ?? 1), $table, $activity_id);

    // header("Location: " . ROOTPATH . "/view/$table/$activity_id?msg=added-successfully");
    header("Location: " . ROOTPATH . "/my-$table?msg=added-successfully");
}, 'post');


Route::add('/browse/(publication|activity|scientist|journal|poster)', function ($page) {
    $idname = $page . '_id';
    $table = $page;
    if ($page == 'scientist') {
        $table = 'users';
        $idname = "user";
    } elseif ($page == 'publication') {

        include_once BASEPATH . "/php/Publication.php";
        $activity = new Publication;
    } elseif ($page == 'poster') {

        include_once BASEPATH . "/php/Poster.php";
        $activity = new Poster;
    }

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

    $stmt = $db->prepare("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, CHARACTER_MAXIMUM_LENGTH FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA LIKE 'osiris' AND TABLE_NAME LIKE ?");
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
    include_once BASEPATH . "/php/Publication.php";
    include_once BASEPATH . "/php/Poster.php";
    $idname = "user";

    $stmt = $db->prepare("SELECT * FROM `users` WHERE `user` LIKE ? LIMIT 1");
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
    include BASEPATH . "/userlogin.php";
    include BASEPATH . "/footer.php";
}, 'get');


Route::add('/user/login', function () {
    include_once BASEPATH . "/php/_config.php";
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
    include BASEPATH . "/main.php";
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
}, 'post');



Route::add('/user/logout', function () {
    unset($_SESSION["username"]);
    $_SESSION['loggedin'] = false;
    header("Location: " . ROOTPATH . "/");
}, 'get');

Route::add('/ajax/(.*)', function ($file) {
    include BASEPATH . "/php/_config.php";
    if (file_exists(BASEPATH . "/ajax/$file")) {
        include BASEPATH . "/ajax/$file";
    } else {
        echo "Error: 404 File does not exist";
    }
}, 'post');


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
