<?php

// use function PHPSTORM_META\type;

// use function PHPSTORM_META\type;

// mb_internal_encoding('UTF-8');
// mb_http_output('UTF-8');
// mysql connection
global $db;

// if [$_SERVER[]]
// if ($_SERVER['SERVER_NAME'] == "testserver") {
//     $db = new PDO("mysql:host=localhost;dbname=research_report;charset=utf8mb4", 'juk', 'Zees1ius');
// } else {
//     $db = new PDO("mysql:host=172.18.250.6;dbname=research_report;charset=utf8mb4", 'md_library', 'md_library');
// }
$db = new PDO("mysql:host=localhost;dbname=research_report;charset=utf8mb4", 'juk', 'Zees1ius');

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function print_publication($pub_id)
{
    global $db;
    $stmt = $db->prepare(
        "SELECT publication.*, IFNULL(journal_abbr, journal_name) AS journal 
        FROM publication
        LEFT JOIN journal USING (journal_id) 
        WHERE publication_id = ?"
    );
    $stmt->execute([$pub_id]);
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);

    $authors = [];
    $stmt = $db->prepare("SELECT * FROM `authors` WHERE publication_id = ?");
    $stmt->execute([$pub['publication_id']]);
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo formatAuthors($authors);
    if (!empty($pub['year'])) {
        echo " ($pub[year])";
    }
    if (!empty($pub['title'])) {
        echo " $pub[title].";
    }
    if (!empty($pub['journal'])) {
        echo " <em>$pub[journal]</em>";

        if (!empty($pub['volume'])) {
            echo " $pub[volume]";
        }
        if (!empty($pub['pages'])) {
            echo ":$pub[pages].";
        }
    }
    if (!empty($pub['doi'])) {
        echo " DOI: <a target='_blank' href='http://dx.doi.org/$pub[doi]'>http://dx.doi.org/$pub[doi]</a>";
    }
    if (!empty($pub['epub'])) {
        echo " <span class='text-danger'>[Epub ahead of print]</span>";
    }
}

function print_poster($pub_id)
{
    global $db;
    $stmt = $db->prepare(
        "SELECT poster.*
        FROM poster
        WHERE poster_id = ?"
    );
    $stmt->execute([$pub_id]);
    $pub = $stmt->fetch(PDO::FETCH_ASSOC);

    $authors = [];
    $stmt = $db->prepare("SELECT * FROM `authors` WHERE poster_id = ?");
    $stmt->execute([$pub['poster_id']]);
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo formatAuthors($authors);
    if (!empty($pub['year'])) {
        echo " ($pub[year])";
    }
    if (!empty($pub['title'])) {
        echo " $pub[title].";
    }
    if (!empty($pub['conference'])) {
        echo " $pub[conference].";
    }
    echo fromToDate($pub['date_start'], $pub['date_end']);
    // echo date_format($date,"d.m.Y");
    // echo 
}

function fromToDate($from, $to){

    $from=date_create($from);
    $to=date_create($to);
}


function commalist(array $array, $sep = "and")
{
    if (empty($array)) return "";
    if (count($array) < 3) return implode(" $sep ", $array);
    $str = implode(", ", array_slice($array, 0, -1));
    return $str . ", $sep " . end($array);
}

function formatAuthors(array $raw_authors)
{
    $authors = array();
    foreach ($raw_authors as $a) {
        $author = $a['last_name'] . ", " . $a['first_name'][0] . ".";
        if ($a['dsmz_affiliation'] == 1) {
            $author = "<b>$author</b>";
        }
        $authors[] = $author;
    }
    return commalist($authors);
}


function format_date($date)
{
    $d = date_create($date);
    return date_format($d, "d.m.Y");
}


function hiddenFieldsFromGet($exclude = array())
{
    if (empty($_GET)) return;
    if (is_string($exclude)) $exclude = array($exclude);

    foreach ($_GET as $name => $value) {
        if (in_array($name, $exclude) || $name == 'msg') continue;
        if (is_array($value)) {
            foreach ($value as $v) {
                // if (empty($v)) continue;
                echo '<input type="hidden" name="' . $name . '[]" value="' . $v . '">';
            }
        } elseif (!empty($value)) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }
    }
}

function hiddenFieldsFromPost($exclude = array())
{
    if (empty($_POST)) return;
    if (is_string($exclude)) $exclude = array($exclude);

    foreach ($_POST as $name => $value) {
        if (in_array($name, $exclude) || $name == 'msg') continue;
        if (is_array($value)) {
            foreach ($value as $v) {
                // if (empty($v)) continue;
                echo '<input type="hidden" name="' . $name . '[]" value="' . $v . '">';
            }
        } elseif (!empty($value)) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }
    }
}

function sortbuttons(string $colname)
{
    $order = $_GET["order"] ?? "";
    $asc = $_GET["asc"] ?? 1;
    $get = currentGET(['order', 'asc']);
    // $get = $_SERVER['REQUEST_URI'] . $get;
    if ($order == $colname && $asc == 1) {
        echo "<a href='$get&order=$colname&asc=0'><i class='fas fa-sort-up'></i></a>";
    } elseif ($order == $colname && $asc == 0) {
        echo "<a href='$get'><i class='fas fa-sort-down'></i></a>";
    } else {
        echo "<a href='$get&order=$colname&asc=1'><i class='fas fa-sort'></i></a>";
    }
}

function currentGET(array $exclude = [], array $include = [])
{
    if (empty($_GET) && empty($include)) return '?';

    $get = "?";
    foreach (array_merge($_GET, $include) as $name => $value) {
        if (in_array($name, $exclude) || $name == 'msg') continue;
        if (is_array($value)) {
            foreach ($value as $v) {
                // if (empty($v)) continue;
                if ($get !== "?") $get .= "&";
                $get .= $name . "[]=" . $v;
            }
        } elseif (!empty($value)) {
            if ($get !== "?") $get .= "&";
            $get .= $name . "=" . $value;
        }
    }
    return $get;
}


function addAuthors($authors, $first, $table, $id){
    global $db;

    $find = $db->prepare('SELECT `user` FROM scientist WHERE last_name LIKE ? AND first_name LIKE ?');
    $insert = $db->prepare(
        "INSERT INTO `authors` 
        (`${table}_id`, last_name, first_name, dsmz_affiliation, position, `user`) 
        VALUES (?, ?, ?, ?, ?, ?)
        "
    );

    foreach ($authors as $i => $author) {
        $author = explode(';', $author, 3);
        if ($i < $first) {
            $pos = 'first';
        } elseif ($i + 1 == count($authors)) {
            $pos = 'last';
        } else {
            $pos = 'middle';
        }
        $find->execute([
            $author[0],
            $author[1][0] . "%"
        ]);
        $user = $find->fetch(PDO::FETCH_COLUMN);
        if (empty($user)) $user = null;
        $insert->execute([
            $id,
            $author[0],
            $author[1],
            $author[2],
            $pos,
            $user
        ]);
    }
}