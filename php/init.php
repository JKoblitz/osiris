<?php

// implement newer functions in case they don't exist
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

// helper functions for all CRUD methods
function validateValues($values, $DB)
{
    $first = max(intval($values['first_authors'] ?? 1), 1);
    unset($values['first_authors']);
    $last = max(intval($values['last_authors'] ?? 1), 1);
    unset($values['last_authors']);

    foreach ($values as $key => $value) {
        if ($key == 'doi') {
            if (!str_contains($value, '10.')) $value = null;
            elseif (!str_starts_with($value, '10.')) {
                $value = explode('10.', $value, 2);
                $values[$key] = "10." . $value[1];
            }
            // dump($value);
            // die;
        } else if ($key == 'authors' || $key == "editors") {
            $values[$key] = array();
            $i = 0;
            foreach ($value as $author) {
                if (is_array($author)) {
                    $author['approved'] = ($author['user'] ?? '') == $_SESSION['username'];
                    $values[$key][] = $author;
                    continue;
                }
                $author = explode(';', $author, 3);
                if (count($author) == 1) {
                    $user = $author[0];
                    $temp = $DB->getPerson($user);
                    $author = [$temp['last'], $temp['first'], true];
                } else {
                    $user = $DB->getUserFromName($author[0], $author[1]);
                }
                $vals = [
                    'last' => $author[0],
                    'first' => $author[1],
                    'aoi' => boolval($author[2]),
                    'user' => $user,
                    'approved' => $user == $_SESSION['username']
                ];
                if ($key == "editors") {
                    $values[$key][] = $vals;
                } else {
                    if ($i < $first) {
                        $pos = 'first';
                    } elseif ($i + $last >= count($value)) {
                        $pos = 'last';
                    } else {
                        $pos = 'middle';
                    }
                    $vals['position'] = $pos;
                    $values[$key][] = $vals;
                }
                $i++;
            }
        } else if ($key == 'sws') {
            foreach ($value as $i => $v) {
                $values['authors'][$i]['sws'] = $v;
            }
            unset($values['sws']);
        } else if ($key == 'user') {
            $user = $DB->getPerson($value);
            $values["authors"] = [
                [
                    'last' => $user['last'],
                    'first' => $user['first'],
                    'aoi' => true,
                    'user' => $value,
                    'approved' => $value == $_SESSION['username']
                ]
            ];
        } else if (is_array($value)) {
            $values[$key] = validateValues($value, $DB);
        } else if ($key == 'issn') {
            if (empty($value)) {
                $values[$key] = array();
            } else {
                $values[$key] = explode(' ', $value);
                $values[$key] = array_unique($values[$key]);
            }
        } else if ($value === 'true') {
            $values[$key] = true;
        } else if ($value === 'false') {
            $values[$key] = false;
        } else if ($key == 'invited_lecture' || $key == 'open_access') {
            $values[$key] = boolval($value);
        } else if ($key == 'oa_status') {
            $values['open_access'] = $value != 'closed';
        } else if (in_array($key, ['aoi', 'epub', 'correction'])) {
            // dump($value);
            // $values[$key] = boolval($value);
            $values[$key] = true;
        } else if ($value === '') {
            $values[$key] = null;
        } else if ($key === 'epub-delay' || $key === 'end-delay') {
            // will be converted otherwise
            $values[$key] = endOfCurrentQuarter(true);
        } else if ($key == 'start' || $key == 'end') {
            if (DateTime::createFromFormat('Y-m-d', $value) !== FALSE) {
                $values[$key] = valiDate($value);
                if ($key == 'start') {
                    if (!isset($values['year']) && isset($values[$key]['year'])) {
                        $values['year'] = $values[$key]['year'];
                    }
                    if (!isset($values['month']) && isset($values[$key]['month'])) {
                        $values['month'] = $values[$key]['month'];
                    }
                }
            } else {
                $values[$key] = null;
            }
        } else if ($key == 'month' || $key == 'year') {
            $values[$key] = intval($value);
        } else if (is_numeric($value)) {
            // dump($key);
            // dump($value);
            // die;
            if (str_starts_with($value, "0")) {
                $values[$key] = trim($value);
            } elseif (is_float($value + 0)) {
                $values[$key] = floatval($value);
            } else {
                $values[$key] = intval($value);
            }
        } else if (is_string($value)) {
            $values[$key] = trim($value);
        }
    }

    if (isset($values['journal']) && !isset($values['role']) && isset($values['year'])) {
        // it is an article
        // since non-checked boxes are not shown in the posted data,
        // it is necessary to get false values
        if (!isset($values['epub'])) $values['epub'] = false;
        else $values['epub-delay'] = endOfCurrentQuarter(true);
        if (!isset($values['open_access']) || !$values['open_access']) {
            $values['open_access'] = $DB->get_oa($values);
        }
        if (!isset($values['correction'])) $values['correction'] = false;

        $values['impact'] = $DB->get_impact($values);
    }
    if (($values['type'] ?? '') == 'misc' && ($values['iteration'] ?? '') == 'annual') {
        $values['end-delay'] = endOfCurrentQuarter(true);
    }

    if (isset($values['year']) && ($values['year'] < 1900 || $values['year'] > (CURRENTYEAR ?? 2055) + 5)) {
        echo "The year $values[year] is not valid!";
        die();
    }
    // dump($values, true);
    // die();
    return $values;
}

function valiDate($date)
{
    if (empty($date)) return null;
    $t = explode('-', $date, 3);
    return array(
        'year' => intval($t[0]),
        'month' => intval($t[1] ?? 1),
        'day' => intval($t[2] ?? 1),
    );
}


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
// check if accessibility settings are given
if ($_SERVER['REQUEST_METHOD'] === 'GET' && array_key_exists('accessibility', $_GET)) {
    // define base parameter
    $domain = $_SERVER['HTTP_HOST'];
    $cookie_settings = [
        'expires' => time() + 86400,
        'path' => ROOTPATH . '/',
        'domain' =>  $domain,
        'httponly' => false,
        'samesite' => 'Strict',
    ];

    // set cookies for current sessions
    $_COOKIE['D3-accessibility-contrast'] = $_GET['accessibility']['contrast'] ?? '';
    $_COOKIE['D3-accessibility-transitions'] = $_GET['accessibility']['transitions'] ?? '';
    $_COOKIE['D3-accessibility-dyslexia'] = $_GET['accessibility']['dyslexia'] ?? '';

    // save cookies for persistent use
    setcookie('D3-accessibility-dyslexia', $_COOKIE['D3-accessibility-dyslexia'], $cookie_settings);
    setcookie('D3-accessibility-contrast', $_COOKIE['D3-accessibility-contrast'], $cookie_settings);
    setcookie('D3-accessibility-transitions', $_COOKIE['D3-accessibility-transitions'], $cookie_settings);
}

require_once BASEPATH . '/php/Settings.php';
include_once BASEPATH . "/php/_config.php";
include_once BASEPATH . "/php/DB.php";

global $DB;
$DB = new DB;

global $osiris;
$osiris = $DB->db;

include_once BASEPATH . "/php/Groups.php";
global $Groups;
$Groups = new Groups();
global $Departments;
$Departments = array_column($Groups->tree['children'], 'name', 'id');

include_once BASEPATH . "/php/Categories.php";
global $Categories;
$Categories = new Categories();

global $USER;
$USER = $DB->initUser();


global $Settings;
$Settings = new Settings($USER);
?>