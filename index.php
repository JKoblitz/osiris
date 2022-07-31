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
    if (!isset($_COOKIE['osiris-language'])) return $en;
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
        if ($_SERVER['SERVER_NAME'] == 'testserver' || $_POST['username'] == 'juk20') {
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


Route::get('/(my-publication|my-review|my-poster|my-lecture|my-misc|my-teaching)', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    include_once BASEPATH . "/php/_db.php";

    $user = $_SESSION['username'];
    $path = str_replace('my-', '', $page);
    $breadcrumb = [
        // ['name' => 'Reviews', 'path' => "/browse/review"],
        ['name' => "My " . $path . "s"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/$path.php";
    include BASEPATH . "/footer.php";
}, 'login');

Route::get('/(my-publication)/add', function ($page) {
    include_once BASEPATH . "/php/_config.php";
    $user = $_SESSION['username'];
    $breadcrumb = [
        ['name' => 'My Publications', 'path' => "/my-publication"],
        ['name' => "Add"]
    ];
    include BASEPATH . "/header.php";
    include BASEPATH . "/pages/editor/add-publication.php";
    include BASEPATH . "/footer.php";
}, 'login');


Route::post('/my-publication', function () {
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
}, 'login');


Route::post('/my-(poster|lecture)', function ($table) {
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
        if (isset($_POST['repetition'])) {
            $stmt = $db->prepare(
                "SELECT * FROM `lecture` WHERE lecture_id = ?"
            );
            $stmt->execute([$_POST['repetition']]);
            $lect = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($lect)) {
                header("Location: " . ROOTPATH . "/my-lecture?msg=error");
                die();
            }

            $values = [
                $lect['title'],
                $lect['conference'] ?? null,
                $_POST['date_start'],
                $lect['location'] ?? null,
                $lect['lecture_type'] . " " . 'repetition',
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
}, 'login');


Route::post('/my-misc', function () {
    include_once BASEPATH . "/php/_config.php";
    // var_dump($_POST);
    // calculate time values:
    $startStr = $_POST['date_start'];
    $endStr = empty($_POST['date_end'] ?? null) ? $startStr : $_POST['date_end'];
    $date = strtotime($startStr);
    $year = date("Y", $date);
    $month = date("n", $date);
    $quarter = ceil($month / 3);

    $authors = $_POST['author'] ?? array();

    if (isset($_POST['repetition'])) {
        $stmt = $db->prepare(
            "INSERT INTO `misc_dates` 
            (misc_id, date_start, date_end, q_id) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $_POST['repetition'],
            $startStr,
            $endStr,
            "${year}Q${quarter}"
        ]);
        header("Location: " . ROOTPATH . "/my-misc?=success");
        die();
        // $stmt = $db->prepare(
        //     "SELECT * FROM `lecture` WHERE lecture_id = ?"
        // );
        // $stmt->execute([$_POST['repetition']]);
        // $lect = $stmt->fetch(PDO::FETCH_ASSOC);
        // if (empty($lect)) {
        //     header("Location: " . ROOTPATH . "/my-lecture?msg=error");
        //     die();
        // }

    }
    // TODO:
    if (isset($_POST['end'])) {
        $stmt = $db->prepare(
            "INSERT INTO `misc_dates` 
            (misc_id, date_start, date_end, q_id) 
            VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $_POST['end'],
            null,
            $endStr,
            null
        ]);
        header("Location: " . ROOTPATH . "/my-misc?=success");
        die();
        // $stmt = $db->prepare(
        //     "SELECT * FROM `lecture` WHERE lecture_id = ?"
        // );
        // $stmt->execute([$_POST['repetition']]);
        // $lect = $stmt->fetch(PDO::FETCH_ASSOC);
        // if (empty($lect)) {
        //     header("Location: " . ROOTPATH . "/my-lecture?msg=error");
        //     die();
        // }

    }
    // add misc:
    $stmt = $db->prepare(
        "INSERT INTO `misc` 
            (title, `location`, iteration) 
            VALUES (?, ?, ?)"
    );
    $stmt->execute([
        $_POST['title'],
        $_POST['location'] ?? null,
        $_POST['iteration'] ?? 'once'
    ]);

    $activity_id = $db->lastInsertId();

    // add authors:
    addAuthors($authors, intval($_POST['first_authors'] ?? 1), 'misc', $activity_id);

    $stmt = $db->prepare(
        "INSERT INTO `misc_dates` 
            (misc_id, date_start, date_end, q_id) 
            VALUES (?, ?, ?, ?)"
    );
    // add dates:
    if ($_POST['iteration'] == 'once') {
        $stmt->execute([
            $activity_id,
            $startStr,
            $endStr,
            "${year}Q${quarter}"
        ]);
    } else {
        $endStr = empty($_POST['date_end'] ?? null) ? null : $_POST['date_end'];
        $stmt->execute([
            $activity_id,
            $startStr,
            $endStr,
            null
        ]);

        // // select all relevant quarters since start of the activity:
        // $stmt = $db->prepare("SELECT q_id FROM `quarter` WHERE ((year > ?) OR (year = ? AND quarter >= ?)) AND quarter != 0 ORDER BY year, quarter");
        // $stmt->execute([$year, $year, $quarter]);
        // $quarters = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // // prepare statement:
        // $stmt = $db->prepare(
        //     "INSERT INTO `misc_dates` 
        //     (misc_id, date_start, date_end, q_id) 
        //     VALUES (?, ?, ?, ?)"
        // );

        // $end_date = strtotime($endStr);
        // $end_year = date("Y", $end_date);
        // $end_month = date("n", $end_date);
        // $end_quarter = ceil($end_month / 3);

        // foreach ($quarters as $i => $q_id) {
        //     // insert quarter for each known quarter since date_start
        //     $start = $i == 0 ? $startStr : null;
        //     $Q = explode("Q", $q_id);
        //     if ($Q[0]==$end_year && $Q[1]==$end_quarter){
        //         $stmt->execute([
        //             $activity_id,
        //             $start,
        //             $endStr,
        //             $q_id
        //         ]);
        //         break;
        //     }
        //     $stmt->execute([
        //         $activity_id,
        //         $start,
        //         null,
        //         $q_id
        //     ]);
        // }

    }


    header("Location: " . ROOTPATH . "/my-misc?msg=added-successfully");
}, 'login');



Route::post('/my-teaching', function () {
    include_once BASEPATH . "/php/_config.php";
    // TODO: check if required fields are available
    $startStr = $_POST['date_start'];
    $endStr = $_POST['date_end'];
    $cat = $_POST['category'];
    $status = $_POST['status'] ?? null;
    if ($cat == "Doktorand:in" || $cat == "Master-Thesis" || $cat == "Bachelor-Thesis") {
        if (new DateTime() < new DateTime($endStr)) {
            $status = 'in progress';
        }
    } else {
        $status = null;
    }

    $authors = $_POST['author'] ?? array();

    // add misc:
    $stmt = $db->prepare(
        "INSERT INTO `teaching` 
            (title, `category`, `details`, date_start, date_end, `status`, `name`, affiliation, academic_title) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );


    $guests = $_POST['guest'];
    foreach ($guests['name'] as $i => $name) {
        if (empty($name)) continue;
        $affiliation = trim($guests['institution'][$i]);
        $academic_title = trim($guests['academic_title'][$i]);

        $stmt->execute([
            $_POST['title'],
            $cat,
            empty($_POST['details']) ? null : $_POST['details'],
            $startStr,
            $endStr,
            $status,
            $name,
            $affiliation,
            empty($academic_title) ? null : $academic_title
        ]);

        $activity_id = $db->lastInsertId();

        // add responsible scientists:
        addAuthors($authors, 1, 'teaching', $activity_id);
    }

    header("Location: " . ROOTPATH . "/my-teaching?msg=added-successfully");
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


include_once BASEPATH . "/api.php";
include_once BASEPATH . "/mongo.php";


Route::get('/user/logout', function () {
    unset($_SESSION["username"]);
    $_SESSION['loggedin'] = false;
    header("Location: " . ROOTPATH . "/");
}, 'login');

Route::post('/ajax/(.*)', function ($file) {
    include BASEPATH . "/php/_config.php";
    if (file_exists(BASEPATH . "/ajax/$file")) {
        include BASEPATH . "/ajax/$file";
    } else {
        echo "Error: 404 File does not exist";
    }
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
