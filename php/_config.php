<?php


// global $db;
// $db = new PDO("mysql:host=localhost;dbname=osiris;charset=utf8mb4", 'juk', 'Zees1ius');

// $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// global $userClass;
// include_once BASEPATH . "/php/User.php";
// // global $userClass;
// $userClass = new User($_SESSION['username'] ?? null);

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

        case 'add-success':
            $header = lang("Success", "Erfolg");
            $text = lang("Data set was added successfully.", "Der Datensatz wurde erfolgreich hinzufügt.");
            $class = "success";
            break;

        case 'update-success':
            $header = lang("Success", "Erfolg");
            $text = lang("Data set was updated successfully.", "Der Datensatz wurde erfolgreich bearbeitet.");
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
            foreach ($value as $k => $v) {
                // if (empty($v)) continue;
                echo '<input type="hidden" name="' . $name . '['.$k.']" value="' . $v . '">';
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

function addJournal($journal)
{
    global $db;
    $journal_id = null;
    if (!empty($journal)) {
        $stmt = $db->prepare("SELECT journal_id FROM `journal` WHERE journal LIKE ? OR abbr LIKE ?");
        $stmt->execute([$journal, $journal]);
        $journal_id = $stmt->fetch(PDO::FETCH_COLUMN);
        if (empty($journal_id)) {
            $stmt = $db->prepare("INSERT INTO `journal` (journal, abbr) VALUES (?,?)");
            $stmt->execute([$journal, $journal]);
            $journal_id = $db->lastInsertId();
        }
    }
    return $journal_id;
}

function addAuthors($authors, $first, $table, $id)
{
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

if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle)
    {
        return $needle !== '' && strpos($haystack, $needle) !== false;
    }
}

function endOfCurrentQuarter($as_string=false){
    $q = CURRENTYEAR . '-' . (3 * CURRENTQUARTER) . '-' . (CURRENTQUARTER == 1 || CURRENTQUARTER == 4 ? 31 : 30) . ' 23:59:59';
    if ($as_string){
        return $q;
    }
    return new DateTime($q);
}

function dump($element, $as_json = false)
{
    echo '<pre class="code">';
    if ($as_json && is_array($element)) {
        $element = array_merge($element);
        echo json_encode($element, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!empty(json_last_error())) {
            var_dump(json_last_error_msg()) . PHP_EOL;
            var_export($element);
        }
    } else if ($as_json ) {
        $element = $element->bsonSerialize();
        echo json_encode($element, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (!empty(json_last_error())) {
            var_dump(json_last_error_msg()) . PHP_EOL;
            var_export($element);
        }
    } else {
        var_dump($element);
    }
    echo "</pre>";
}

function bool_icon($bool)
{
    if ($bool) {
        return '<i class="fas fa-check text-success"></i>';
    } else {
        return '<i class="fas fa-xmark text-danger"></i>';
    }
}

function typeInfo($type){
    switch ($type) {
        case 'publication':
            return [
                'name'=>lang('Publications', 'Publikationen'),
                'color' => "#006EB7"
                // 'color' => 'var(--primary-color)'
        ];
        case 'poster':
            return [
                'name'=>lang('Poster'),
                'color' => "#B61F29"
                // 'color' => 'var(--danger-color)'
        ];
        case 'lecture':
            return [
                'name'=>lang('Lectures', 'Vorträge'),
                'color' => "#ECAF00"
                // 'color' => 'var(--signal-color)'
        ];
        case 'review':
            return [
                'name'=>lang('Reviews & Editorial boards'),
                'color' => "#1FA138"
                // 'color' => 'var(--success-color)'
        ];
        case 'misc':
            return [
                'name'=>lang('Other activities', 'Sonstige Aktivitäten'),
                'color' => "#b3b3b3"
                // 'color' => 'var(--muted-color)'
        ];
        case 'students':
            return [
                'name'=>lang('Students & Guests', 'Studierende & Gäste'),
                'color' => "#575756"
                // 'color' => 'var(--dark-color)'
        ];
        default:
            return [
                'name'=> $type,
                'color' => '#cccccc'
        ];
    }
}
function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

function type2title($type){
    return typeInfo($type)['name'];
}


function deptInfo($dept){
    switch ($dept) {
        case "MIOS": 
            return [
                "color"=>'#d31e25',
                'name' => 'Microorganisms'
            ];
        case "BIDB": 
            return [
                "color"=>'#5db5b7',
                'name' => 'Bioinformatik &amp; Datenbanken'
            ];
        case "MIG": 
            return [
                "color"=>'#d1c02b',
                'name' => 'Mikrobielle Genomforschung'
            ];
        case "BUG": 
            return [
                "color"=>'#8a3f64',
                'name' => 'Bioökonomie und Gesundheitsforschung'
            ];
        case "MuTZ": 
            return [
                "color"=>'#31407b',
                'name' => 'Menschliche &amp; Tierische Zellkulturen'
            ];
        case "PFVI": 
            return [
                "color"=>'#369e4b',
                'name' => 'Pflanzenviren'
            ];
        case "MÖD": 
            return [
                "color"=>'#d7a32e',
                'name' => 'Mikrobielle Ökologie'
            ];
        case "Services": 
            return [
                "color"=>'#4f2e39',
                'name' => 'Services'
            ];
        case "Patente": 
            return [
                "color"=>'#b2b2b2',
                'name' => 'Patente'
            ];
        case "IT": 
            return [
                "color"=>'#5F272A',
                'name' => 'IT'
            ];
        case "Verwaltung": 
            return [
                "color"=>'#5F272A',
                'name' => 'Verwaltung'
            ];
        case "PuK": 
            return [
                "color"=>'#5F272A',
                'name' => 'Presse und Kommunikation'
            ];
        default:
        return [
            "color"=>'#cccccc',
            'name' => $dept

        ];
    }
}
