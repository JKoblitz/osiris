<?php


global $db;
$db = new PDO("mysql:host=localhost;dbname=osiris;charset=utf8mb4", 'juk', 'Zees1ius');

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


function printMsg($msg = null, $type = 'info', $header = "default")
{
    if ($msg === null && isset($_SESSION['msg'])) {
        $msg = $_SESSION['message'];
        unset($_SESSION["message"]);
    }
    if ($msg === null && !isset($_GET["msg"])) return;
    $msg = $msg ?? $_GET["msg"];
    $text = "";
    $header = $header;
    $class = "";
    if ($type == 'success') {
        $class = "success";
        if ($header == "default") {
            $header = lang("Success!", "Erfolg!");
        }
    } elseif ($type == 'error') {
        $class = "danger";
        if ($header == "default") {
            $header = lang("Error", "Fehler");
        }
    } elseif ($type == 'info') {
        $class = "primary";
        if ($header == "default") {
            $header = "";
        }
    }
    switch ($msg) {
        
        case 'welcome':
            $header = lang("Welcome,", "Willkommen,") . " " . ($_SESSION["name"] ?? '') . ".";
            $text = lang("You are now logged in.", "Du bist jetzt eingeloggt.");
            $class = "success";
            break;
       
        default:
            $text = str_replace("-", " ", $msg);
            break;
    }
    $get = currentGET(['msg']) ?? "";
    echo "<div class='alert alert-$class alert-block show my-10' role='alert'>
          <a class='close' href='$get' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </a> ";
    if (!empty($header)) {
        echo " <h4 class='alert-title'>$header</h4>";
    }
    echo "$text
      </div>";
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

function addJournal($journal){
    global $db;
    $journal_id = null;
    if (!empty($journal)) {
        $stmt = $db->prepare("SELECT journal_id FROM `journal` WHERE journal LIKE ? OR journal_abbr LIKE ?");
        $stmt->execute([$journal, $journal]);
        $journal_id = $stmt->fetch(PDO::FETCH_COLUMN);
        if (empty($journal_id)) {
            $stmt = $db->prepare("INSERT INTO `journal` (journal, journal_abbr) VALUES (?,?)");
            $stmt->execute([$journal, $journal]);
            $journal_id = $db->lastInsertId();
        }
    }
    return $journal_id;
}

function addAuthors($authors, $first, $table, $id){
    global $db;

    $find = $db->prepare('SELECT `user` FROM users WHERE last_name LIKE ? AND first_name LIKE ?');
    $insert = $db->prepare(
        "INSERT INTO `authors` 
        (`${table}_id`, last_name, first_name, aoi, position, `user`) 
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