<?php

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
            if (isset($_GET['new'])) {
                $text = lang(
                    '',
                    'Du bist zum ersten Mal hier? Ich habe dir einen neuen Account angelegt. 
                    Bitte überprüfe <a class="link" href="' . ROOTPATH . '/user/edit/' . $_SESSION['username'] . '">dein Profil</a> und ergänze bzw. korrigiere die Angaben.'
                );
                if (!empty($_GET['new'])) {
                    $text .=  '<br/>' . lang('Ich habe außerdem <b>' . $_GET['new'] . ' Aktivitäten</b> gefunden, die vielleicht zu dir gehören. Du kannst sie <a class="link" href="' . ROOTPATH . '/issues">hier</a> überprüfen.');
                }
            }


            $class = "success";
            break;
        case 'approved':
            $header = lang("Quarter approved.", "Quartal freigegeben.");
            $text = lang("Thank you.", "Vielen Dank.");
            $class = "success";
            break;

        case 'account-created':
            $text = lang("Account has been created. Please log in.", "Der Account wurde erstellt. Bitte logge dich ein.");
            // $text = lang("Thank you.", "Vielen Dank.");
            $class = "success";
            break;

        case 'settings-saved':
            $text = lang("Settings saved", "Einstellungen gespeichert.");
            // $text = lang("Thank you.", "Vielen Dank.");
            $class = "success";
            break;
        case 'settings-resetted':
            $text = lang("Settings resetted to the default values.", "Einstellungen wurden auf den Standard zurückgesetzt.");
            // $text = lang("Thank you.", "Vielen Dank.");
            $class = "success";
            break;
        case 'settings-replaced':
            $text = lang("Settings replaced by uploaded file.", "Einstellungen wurden durch den Upload ersetzt.");
            // $text = lang("Thank you.", "Vielen Dank.");
            $class = "success";
            break;

        case 'add-success':
            $header = lang("Success", "Erfolg");
            $text = lang("Data set was added successfully.", "Der Datensatz wurde erfolgreich hinzufügt.");
            $text .= '<br/><a class="btn mt-10" href="' . ROOTPATH . '/add-activity">' . lang('Add another activity', 'Weitere Aktivität hinzufügen') . '</a>';
            $class = "success";
            break;

        case 'update-success':
            $header = lang("Success", "Erfolg");
            $text = lang("Data set was updated successfully.", "Der Datensatz wurde erfolgreich bearbeitet.");
            $class = "success";
            break;

        case 'deleted':
        case 'deleted-1':
            $header = lang("Deleted", "Gelöscht");
            $text = lang("You have deleted an activity.", "Du hast eine Aktivität gelöscht.");
            $class = "danger";
            break;

        case 'locked':
            $header = lang("This activity is locked.", "Diese Aktivität ist gesperrt.");
            $text = lang(
                "You cannot edit or delete this activity because of our reporting rules. Contact the OSIRIS editors if there are any issues.",
                "Du kannst diese Aktivität aufgrund unserer Report-Richtlinien nicht bearbeiten oder löschen. Kontaktiere die OSIRIS-Editoren, falls dadurch irgendwelche Probleme entstehen."
            );
            $class = "danger";
            break;

        case 'ali':
            $header = '';
            $text = lang("You are already logged in.", "Du bist bereits eingeloggt");
            $class = "signal";
            break;

        default:
            $text = str_replace("-", " ", $msg);
            break;
    }
    $get = currentGET(['msg']) ?? "";
    echo "<div class='alert $class block show my-10' role='alert'>
          <a class='close' href='$get' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </a> ";
    if (!empty($header)) {
        echo " <h4 class='title'>$header</h4>";
    }
    echo "$text
      </div>";
}

function readCart()
{
    $cart = $_COOKIE['osiris-cart'] ?? '';
    if (empty($cart)) return array();
    $cart = explode(',', $cart);
    return $cart;
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

function CallAPI($method, $url, $data = [])
{
    $curl = curl_init();

    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;
        case "JSON":
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_PUT, 1);
            break;
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    // curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // curl_setopt($curl, CURLOPT_USERPWD, "username:password");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($curl);
    if ($result === false) {
        throw new Exception(curl_error($curl), curl_errno($curl));
    }
    curl_close($curl);

    return $result;
}

function redirect($url)
{
    header("Location: " . ROOTPATH . $url);
}

function endOfCurrentQuarter($as_string = false)
{
    $q = CURRENTYEAR . '-' . (3 * CURRENTQUARTER) . '-' . (CURRENTQUARTER == 1 || CURRENTQUARTER == 4 ? 31 : 30) . ' 23:59:59';
    if ($as_string) {
        return $q;
    }
    return new DateTime($q);
}

function print_list($list)
{
    if ($list instanceof MongoDB\Model\BSONArray) {
        $list = $list->bsonSerialize();
    }
    return implode(', ', $list);
}


function getDateTime($date)
{
    if ($date instanceof MongoDB\BSON\UTCDateTime) {
        // MongoDB\BSON\UTCDateTime 
        $d = $date->toDateTime();
    } else if (isset($date['year'])) {
        //date instanceof MongoDB\Model\BSONDocument
        $d = new DateTime();
        $d->setDate(
            $date['year'],
            $date['month'] ?? 1,
            $date['day'] ?? 1
        );
    } else {
        try {
            $d = date_create($date);
        } catch (TypeError $th) {
            $d = null;
        }
    }
    return $d;
}



function valueFromDateArray($date)
{
    // this function is used to generate a input:date-like string from arrays
    if (empty($date) || !isset($date['year'])) return '';
    $d = new DateTime();
    $d->setDate(
        $date['year'],
        $date['month'] ?? 1,
        $date['day'] ?? 1
    );
    return date_format($d, "Y-m-d");
}

function fromToDate($from, $to)
{
    if (empty($to) || $from == $to) {
        return format_date($from);
    }
    // $to = date_create($to);
    $from = format_date($from);
    $to = format_date($to);

    $f = explode('.', $from, 3);
    $t = explode('.', $to, 3);

    $from = $f[0] . ".";
    if ($f[1] != $t[1] || $f[2] != $t[2]) {
        $from .= $f[1] . ".";
    }
    if ($f[2] != $t[2]) {
        $from .= $f[2];
    }

    return $from . '-' . $to;
}

function getYear($doc)
{
    if (isset($doc['year'])) return $doc['year'];
    if (isset($doc['start'])) return $doc['start']['year'];
    if (isset($doc['dates'])) {
        if (isset($doc['dates'][0]['start'])) return $doc['dates'][0]['start']['year'];
        if (isset($doc['dates']['start'])) return $doc['dates']['start']['year'];
        // return $doc['start']['year'];
    }
}

function getQuarter($time)
{
    // this function takey either the month, a date string, 
    // or an date array and returns the quarter
    if (empty($time)) {
        return 0;
    }
    if (isset($time['month'])) {
        return ceil($time['month'] / 3);
    }
    if (isset($time['start'])) {
        $time = $time['start'];
    }
    if (isset($time['dates']) && !empty($time['dates'])) {
        $time = reset($time['dates']);
    }
    if (is_int($time)) {
        return ceil($time / 3);
    }

    try {
        $date = getDateTime($time);
        $month = date_format($date, 'n');
    } catch (TypeError $th) {
        $month = 1;
    }

    return ceil($month / 3);
}

function inQuarter($start, $end = null, $qarter = CURRENTQUARTER, $year = CURRENTYEAR)
{
    // check if time period in selected quarter
    if (empty($end)) {
        $end = $start;
    }
    $qstart = new DateTime($year . '-' . (3 * $qarter - 2) . '-1 00:00:00');
    $qend = new DateTime($year . '-' . (3 * $qarter) . '-' . ($qarter == 1 || $qarter == 4 ? 31 : 30) . ' 23:59:59');

    $start = new DateTime($start);
    $end = new DateTime($end);
    if ($start <= $qstart && $qstart <= $end) {
        return true;
    } elseif ($qstart <= $start && $start <= $qend) {
        return true;
    }
    return false;
}

function inCurrentQuarter($year, $month)
{
    // check if time period in selected quarter
    $qstart = new DateTime(CURRENTYEAR . '-' . (3 * CURRENTQUARTER - 2) . '-1 00:00:00');
    $qend = new DateTime(CURRENTYEAR . '-' . (3 * CURRENTQUARTER) . '-' . (CURRENTQUARTER == 1 || CURRENTQUARTER == 4 ? 31 : 30) . ' 23:59:59');

    $time = new DateTime();
    $time->setDate($year, $month, 15);
    if ($time <= $qstart && $qstart <= $time) {
        return true;
    } elseif ($qstart <= $time && $time <= $qend) {
        return true;
    }
    return false;
}

function format_date($date)
{
    // dump($date);
    $d = getDateTime($date);
    return date_format($d, "d.m.Y");
}

function dump($element, $as_json = false)
{
    echo '<pre class="code">';
    if ($element instanceof MongoDB\Model\BSONArray) {
        $element = $element->bsonSerialize();
    }
    if ($as_json) {
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
        return '<i class="ph ph-check text-success"></i>';
    } else {
        return '<i class="ph ph-x text-danger"></i>';
    }
}

function flatten(array $array)
{
    $return = array();
    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });
    return $return;
}

function time_elapsed_string($datetime, $full = false, $type = 'str')
{
    $now = new DateTime;
    if ($type == 'str') {
        $ago = new DateTime($datetime);
    } else {
        $ago = new DateTime();
        $ago->setTimestamp($datetime);
    }
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => lang('year', 'Jahre'),
        'm' => lang('month', 'Monate'),
        'w' => lang('week', 'Woche'),
        'd' => lang('day', 'Tage'),
        'h' => lang('hour', 'Stunde'),
        'i' => lang('minute', 'Minute'),
        's' => lang('second', 'Sekunde'),
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? lang('s', 'n') : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? lang('', 'vor ') . implode(', ', $string) . lang(' ago', '') : lang('just now', 'gerade eben');
}


function adjustBrightness($hex, $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0, min(255, $color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}

function getFileIcon($type)
{
    switch ($type) {
        case 'pdf':
        case 'csv':
            return 'file-' . $type;
        case 'xlsx':
        case 'xls':
            return 'file-excel';
        case 'pptx':
        case 'ppt':
            return 'file-powerpoint';
        case 'docx':
        case 'doc':
            return 'file-word';
        case 'zip':
        case 'gz':
            return 'file-zipper';
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpeg':
            return 'file-image';
        case 'mp4':
        case 'mpeg':
            return 'file-video';
        case 'json':
            return 'file-code';
        default:
            return 'file-text';
    }
}

/**
 * Return the last day of the Week/Month/Quarter/Year that the
 * current/provided date falls within
 *
 * @param string   $period The period to find the last day of. ('year', 'quarter', 'month', 'week')
 * @param DateTime $date   The date to use instead of the current date
 *
 * @return DateTime
 * @throws InvalidArgumentException
 */
function lastDayOf($period, DateTime $date = null)
{
    $period = strtolower($period);
    $validPeriods = array('year', 'quarter', 'month', 'week');

    if ( ! in_array($period, $validPeriods))
        throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

    $newDate = ($date === null) ? new DateTime() : clone $date;

    switch ($period)
    {
        case 'year':
            $newDate->modify('last day of december ' . $newDate->format('Y'));
            break;
        case 'quarter':
            $month = $newDate->format('n') ;

            if ($month < 4) {
                $newDate->modify('last day of march ' . $newDate->format('Y'));
            } elseif ($month > 3 && $month < 7) {
                $newDate->modify('last day of june ' . $newDate->format('Y'));
            } elseif ($month > 6 && $month < 10) {
                $newDate->modify('last day of september ' . $newDate->format('Y'));
            } elseif ($month > 9) {
                $newDate->modify('last day of december ' . $newDate->format('Y'));
            }
            break;
        case 'month':
            $newDate->modify('last day of this month');
            break;
        case 'week':
            $newDate->modify(($newDate->format('w') === '0') ? 'now' : 'sunday this week');
            break;
    }

    return $newDate;
}