<?php

// helper functions

function commalist($array, $sep = "and")
{
    if (empty($array)) return "";
    if (count($array) < 3) return implode(" $sep ", $array);
    $str = implode(", ", array_slice($array, 0, -1));
    return $str . " $sep " . end($array);
}

function abbreviateAuthor($last, $first)
{
    $fn = "";
    foreach (explode(" ", $first) as $name) {
        $fn .= " " . $name[0] . ".";
    }
    return $last . "," . $fn;
}

function authorForm($a)
{
    return "<div class='author author-aoi'>
        $a[last], $a[first]<input type='hidden' name='values[authors][]' value='$a[last];$a[first];1'>
        <a onclick='removeAuthor(event, this);'>&times;</a>
        </div>";
}

function formatAuthors($raw_authors, $separator = 'and', $first = 1, $last = 1)
{
    $authors = array();
    foreach ($raw_authors as $a) {
        $author = abbreviateAuthor($a['last'], $a['first']);
        if (($a['aoi'] ?? 1) == 1) {
            $author = "<b>$author</b>";
        }
        if ($first > 1 && $a['position'] == 'first') {
            $author .= "<sup>#</sup>";
        }
        if ($last > 1 && $a['position'] == 'last') {
            $author .= "<sup>*</sup>";
        }
        $authors[] = $author;
    }
    return commalist($authors, $separator);
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
        $d = date_create($date);
    }
    return $d;
}

function format_date($date)
{
    // dump($date);
    $d = getDateTime($date);
    return date_format($d, "d.m.Y");
}

function valueFromDateArray($date)
{
    // this function is used to generate a input:date-like string from arrays
    if (empty($date)) return '';
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

    $from = "";
    $from .= $f[0] . ".";
    if ($f[1] != $t[1]) {
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
    if (isset($time['start'])) {
        $time = $time['start'];
    }
    if (isset($time['dates']) && !empty($time['dates'])) {
        $time = reset($time['dates']);
    }
    if (isset($time['month'])) {
        return ceil($time['month'] / 3);
    }
    if (is_int($time)) {
        return ceil($time / 3);
    }
    $date = getDateTime($time);
    $month = date_format($date, 'n');
    return ceil($month / 3);
}

function inSelectedQuarter($start, $end = null)
{
    // check if time period in selected quarter
    if (empty($end)) {
        $end = $start;
    }
    $qstart = new DateTime(SELECTEDYEAR . '-' . (3 * SELECTEDQUARTER - 2) . '-1 00:00:00');
    $qend = new DateTime(SELECTEDYEAR . '-' . (3 * SELECTEDQUARTER) . '-' . (SELECTEDQUARTER == 1 || SELECTEDQUARTER == 4 ? 31 : 30) . ' 23:59:59');

    $start = new DateTime($start);
    $end = new DateTime($end);
    if ($start <= $qstart && $qstart <= $end) {
        return true;
    } elseif ($qstart <= $start && $start <= $qend) {
        return true;
    }
    return false;
}

function format_month($month)
{
    if (empty($month)) return '';
    $month = intval($month);
    $array = [
        1 => lang("January", "Januar"),
        2 => lang("February", "Februar"),
        3 => lang("March", "März"),
        4 => lang("April"),
        5 => lang("May", "Mai"),
        6 => lang("June", "Juni"),
        7 => lang("July", "Juli"),
        8 => lang("August"),
        9 => lang("September"),
        10 => lang("October", "Oktober"),
        11 => lang("November"),
        12 => lang("December", "Dezember")
    ];
    return $array[$month];
}

function publication_icon($type)
{
    $type = strtolower(trim($type));
    switch ($type) {
        case 'journal article':
        case 'journal-article':
        case 'article':
            return "<span data-toggle='tooltip' data-title='Journal article'>
            <i class='far fa-lg text-primary fa-file-lines'></i>
            </span>";

        case 'magazine article':
        case 'magazine':
            return "<span data-toggle='tooltip' data-title='Magazine article'>
            <i class='far fa-lg text-primary fa-newspaper'></i>
            </span>";
        case 'book-chapter':
        case 'book chapter':
        case 'chapter':
            return "<span data-toggle='tooltip' data-title='Book chapter'>
            <i class='far fa-lg text-primary fa-book'></i>
            </span>";
            // case 'book-editor':
            //     return "<span data-toggle='tooltip' data-title='Book'>
            // <i class='far fa-lg text-primary fa-memo'></i>
            // </span>";
        case 'book':
            return "<span data-toggle='tooltip' data-title='Book'>
            <i class='far fa-lg text-primary fa-book-bookmark'></i>
            </span>";
        default:
            # code...
            return '';
    }
}

function activity_icon($doc)
{
    $type = strtolower(trim($doc['type'] ?? ''));
    switch ($type) {
        case 'publication':
            return publication_icon($doc['pubtype'] ?? '');

        case 'poster':
            return "<span data-toggle='tooltip' data-title='Poster'>
                <i class='far fa-lg text-danger fa-presentation-screen'></i>
                </span>";
        case 'lecture':
            return "<span data-toggle='tooltip' data-title='Lecture'>
                <i class='far fa-lg text-signal fa-keynote'></i>
                </span>";
        case 'review':
            return "<span data-toggle='tooltip' data-title='Review'>
                <i class='far fa-lg text-success fa-book-open-cover'></i>
                </span>";
        case 'misc':
            return "<span data-toggle='tooltip' data-title='Misc'>
                <i class='far fa-lg text-muted fa-icons'></i>
                </span>";
        case 'teaching':
            return "<span data-toggle='tooltip' data-title='Teaching'>
                    <i class='far fa-lg text-muted fa-people'></i>
                    </span>";
        case 'software':
            return "<span data-toggle='tooltip' data-title='Software'>
                <i class='far fa-lg text-muted fa-desktop'></i>
                </span>";
        default:
            return '';
    }
}

// format functions
function format($col, $doc)
{
    switch ($col) {
        case 'teaching':
            return format_teaching($doc);
        case 'poster':
            return format_poster($doc);
        case 'lecture':
            return format_lecture($doc);
        case 'publication':
            return format_publication($doc);
        case 'misc':
            return format_misc($doc);
        case 'review':
            if ($doc['role'] == 'Reviewer') {
                return format_review($doc);
            } else {
                return format_editorial($doc);
            }
        default:
            return "";
    }
}

function format_teaching($doc, $verbose = false)
{
    $result = $doc['academic_title'] . ' ' . $doc['name'] . ', ' . $doc['affiliation'] . '. ';
    $result .=  $doc['title'] . '; ' . $doc['category'];

    if (!empty($doc['details'])) {
        $result .= " (" . $doc["details"] . ")";
    }
    $result .= ". ";
    $result .= fromToDate($doc['start'], $doc['end']);

    if (in_array($doc['category'], ["Doktorand:in", "Master-Thesis", "Bachelor-Thesis"]) && !empty($doc['status'])) {

        if ($doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end'])) {
            $result .= " (<b class='text-danger'>" . $doc['status'] . "</b>),";
        } else {
            $result .= " (" . $doc['status'] . "),";
        }
    } else {
        $result .= ",";
    }

    $result .= " betreut von " . formatAuthors($doc['authors']);

    if ($verbose) {
        if ($doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end'])) {
            echo '<p class="text-danger mt-0">' . lang(
                "Attention: the Thesis of $doc[name] has ended. Please confirm if the work was successfully completed or not or extend the time frame.",
                "Achtung: die Abschlussarbeit von $doc[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
            ) . '</p>';
        }
    }

    return $result;
}

function format_poster($doc)
{
    $result = formatAuthors($doc['authors']);
    if (!empty($doc['title'])) {
        $result .= " $doc[title].";
    }
    if (!empty($doc['conference'])) {
        $result .= " $doc[conference]";
    }
    if (!empty($doc['location'])) {
        $result .= ", $doc[location].";
    } else {
        $result .= ".";
    }
    $result .= " " . fromToDate($doc['start'], $doc['end'] ?? null);
    return $result;
}

function format_lecture($doc)
{
    $result = formatAuthors($doc['authors']);
    if (!empty($doc['year'])) {
        $result .= " ($doc[year])";
    }
    if (!empty($doc['title'])) {
        $result .= " $doc[title].";
    }
    if (!empty($doc['conference'])) {
        $result .= " $doc[conference].";
    }
    $result .= " " . fromToDate($doc['start'], null);

    if (!empty($doc['location'])) {
        $result .= ", $doc[location].";
    } else {
        $result .= ".";
    }

    $result .= " (" . $doc['lecture_type'] . ")";
    return $result;
}

function format_publication($doc)
{
    $result = "";

    if (!is_array($doc['authors'])) {
        $doc['authors'] = $doc['authors']->bsonSerialize();
    }

    if (!empty($doc['authors']) && is_array($doc['authors'])) {

        $pos = array_count_values(array_column($doc['authors'], 'position'));
        $first = $pos['first'] ?? 1;
        $last = $pos['last'] ?? 1;
        $result .= formatAuthors($doc['authors'], 'and', $first, $last);
    } else {
        $first = 1;
        $last = 1;
    }

    if (!empty($doc['year'])) {
        $result .= " ($doc[year])";
    }
    if (!empty($doc['correction'])) {
        $result .= " <span class='text-danger'>Correction to:</span>";
    }
    if (!empty($doc['title'])) {
        $result .= " $doc[title].";
    }
    // TODO:
    // if ($doc['type'] == 'book-chapter')
    switch (strtolower(trim($doc['type']))) {
        case 'journal article':
        case 'journal-article':
            if (!empty($doc['journal'])) {
                $result .= " <em>$doc[journal]</em>";

                if (!empty($doc['volume'])) {
                    $result .= " $doc[volume]";
                }
                if (!empty($doc['pages'])) {
                    $result .= ":$doc[pages].";
                }
            }
            break;

        case 'magazine article':
        case 'magazine':
            if (!empty($doc['magazine'])) {
                $result .= " <em>$doc[magazine]</em>.";
            }
            if (!empty($doc['link'])) {
                $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
            }
            break;
        case 'book-chapter':
            break;
        case 'book':
            break;
        default:
            # code...
            break;
    }
    if (!empty($doc['doi'])) {
        $result .= " DOI: <a target='_blank' href='http://dx.doi.org/$doc[doi]'>http://dx.doi.org/$doc[doi]</a>";
    }
    if (!empty($doc['epub'])) {
        $result .= " <span class='text-danger'>[Epub ahead of print]</span>";
    }
    if (!empty($doc['open_access'])) {
        $result .= ' <i class="icon-open-access text-orange" title="Open Access"></i>';
    }

    if ($first > 1 || $last > 1) $result .= "<br>";
    if ($first > 1) {
        $result .= "<span class='text-muted'><sup>#</sup> Shared first authors</span>";
    }
    if ($last > 1) {
        $result .= "<span class='text-muted'><sup>*</sup> Shared last authors</span>";
    }

    return $result;
}


function format_misc($doc)
{
    $result = formatAuthors($doc['authors']);

    if (!empty($doc['title'])) {
        $result .= " $doc[title], ";
    }

    if ($doc['iteration'] == "annual") {

        $dates = $doc['dates'][0];
        $start = format_date($dates['start']);
        if (empty($dates['end'])) {
            $end = lang('today', "heute");
        } else {
            $end = format_date($dates['end']);
        }
        $result .= lang("from $start to $end", "von $start bis $end");
    } else {

        $dbdates = $doc['dates'];
        foreach ($dbdates as $d) {
            $dates[] = fromToDate($d['start'], $d['end']);
        }
        $result .= commalist($dates, lang('and', 'und'));
    }

    if (!empty($doc['location'])) {
        $result .= ", $doc[location].";
    } else {
        $result .= ".";
    }
    return $result;
}



function format_review($doc, $filterYear = false)
{
    $result = "";
    if (!empty($doc['name'] ?? '')) {
        $result .= "<b>$doc[name]</b>";
    } elseif (!empty($doc['authors'] ?? '')) {
        $result.=formatAuthors($doc['authors']);
    } else {
        $userdata = getUserFromId($doc['user']);
        $result .= "<b>$userdata[last], $userdata[first_abbr]</b>";
    }
    
    $result .= " ".lang("Reviewer for ", 'Reviewer für ');
    $result .= '<em>' . $doc['journal'] . '</em>';
    $times = 0;
    $times_current = 0;
    foreach ($doc['dates'] as $date) {
        $times += 1;
        if ($date['year'] == SELECTEDYEAR) {
            $times_current += 1;
        }
    }
    if ($filterYear) {
        $result .= " ($times_current " . lang('times', 'mal') . ")";
    } else {
        $result .= lang(
            " ($times times, $times_current in " . SELECTEDYEAR . ")",
            " ($times mal, davon $times_current in " . SELECTEDYEAR . ")"
        );
    }
    $result .= ".";
    return $result;
}

function format_editorial($doc)
{
    $result = "";
    if (!empty($doc['name'] ?? '')) {
        $result .= "<b>$doc[name]</b> ";
    } elseif (!empty($doc['authors'] ?? '')) {
        $result.=formatAuthors($doc['authors']);
    } else {
        $userdata = getUserFromId($doc['user']);
        $result .= "<b>$userdata[last], $userdata[first_abbr]</b> ";
    }
    $result .= lang("Member of the Editorial board of ", 'Mitglied des Editorial Board von ');
    $result .= '<em>' . $doc['journal'] . '</em>';
    if (!empty($doc['start'])) {
        if (!empty($doc['end'])) {
            $result .= lang(", from ", ", von ");
            $result .= format_month($doc['start']['month']) . ' ' . $doc['start']['year'];
            $result .= lang(" until ", " bis ");
            $result .= format_month($doc['end']['month']) . ' ' . $doc['end']['year'];
        } else {
            $result .= lang(", since ", ", seit ");
            $result .= format_month($doc['start']['month']) . ' ' . $doc['start']['year'];
        }
    }
    $result .= ".";
    return $result;
}
