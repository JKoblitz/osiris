<?php

/**
 * WARNING: THIS SCRIPT IS NOT USED ANY MORE
 * AND IS JUST KEPT AS REFERENCE !!!
 */

// helper functions

function commalist($array, $sep = "and")
{
    if (empty($array)) return "";
    if (count($array) < 3) return implode(" $sep ", $array);
    $str = implode(", ", array_slice($array, 0, -1));
    return $str . " $sep " . end($array);
}

function abbreviateAuthor($last, $first, $reverse = true)
{
    $fn = " ";
    if ($first) : foreach (preg_split("/(\s| |-|\.)/u", ($first)) as $name) {
            if (empty($name)) continue;
            // echo "<!--";
            // echo "-->";
            $fn .= "" . mb_substr($name, 0, 1) . ".";
        }
    endif;
    if (empty(trim($fn))) return $last;
    if ($reverse) return $last . "," . $fn;
    return $fn . " " . $last;
}




function activity_title($doc)
{
    if (is_string($doc)) {
        $type = strtolower(trim($doc));
    } else {
        $type = strtolower(trim($doc['type'] ?? ''));
    }
    $name = "Undefined";
    switch ($type) {
        case 'publication':
            $pubtype = strtolower(trim($doc['pubtype'] ?? $doc['subtype'] ?? $type));
            switch ($pubtype) {
                case 'journal article':
                case 'journal-article':
                case 'article':
                    $name = "Journal Article";
                    break 2;
                case 'magazine article':
                case 'magazine':
                    $name = lang('Non-Refereed');
                    break 2;
                case 'book-chapter':
                case 'book chapter':
                case 'chapter':
                    $name = lang('Book chapter', 'Buchkapitel');
                    break 2;
                case 'book-editor':
                case 'publication':
                    $name = lang('Book', 'Buch');
                    break 2;
                case 'book':
                    $name = lang('Book', 'Buch');
                    break 2;
                case 'preprint':
                    $name = lang('Preprint');
                    break 2;
                case 'dissertation':
                    $name = lang('Thesis');
                    break 2;
                case 'others':
                default:
                    $name = lang('Others', 'Weiteres');
                    break 2;
            }
        case 'poster':
            $name = "Poster";
            break;
        case 'lecture':
            $name = "Lecture";
            break;
        case 'review':
            switch (strtolower($doc['role'] ?? $doc['subtype'] ?? '')) {
                case 'editorial':
                case 'editor':
                    $name = "Editorial board";
                    break 2;
                case 'grant-rev':
                    $name = lang("Other review", "Sonstiges Review");
                    break 2;
                case 'thesis-rev':
                    $name = "Thesis review";
                    break 2;
                default:
                    $name = "Peer-Review";
                    break 2;
            }

        case 'misc':
            
            $subtype = $doc['iteration'] ?? $doc['subtype'] ?? '';
            if ($subtype == 'once' || $subtype == 'misc-once') {
                $name = lang('Once', 'Einmalig');
            } elseif ($subtype == 'annual' || $subtype == 'misc-annual') {
                $name = lang('Frequently', 'Stetig');
            } else {
                $name = lang("Other", "Sonstiges");
            }
            break;
        case 'students':
            $cat = strtolower(trim($doc['category'] ?? $doc['subtype'] ?? 'thesis'));
            if (str_contains($cat, "thesis") || $cat == 'doktorand:in' || $cat == 'students') {
                $name = lang('Theses', 'Abschlussarbeiten');
                break;
            }
            $name = lang('Guests & interns', 'Gäste & Praktika');
            break;

        case 'teaching':
            $name = "Teaching";
            break;
        case 'software':
            $name = "Software";
            break;
        default:
            break;
    }

    return $name;
}

function$Format->activity_icon($doc, $tooltip = true)
{
    if (is_string($doc)) {
        $type = strtolower(trim($doc));
    } else {
        $type = strtolower(trim($doc['type'] ?? $doc['subtype'] ?? ''));
    }
    $icon = "<i class='ph text-misc ph-placeholder'></i>";
    switch ($type) {
        case 'publication':
            $pubtype = strtolower(trim($doc['pubtype'] ?? $doc['subtype'] ?? $type));
            switch ($pubtype) {
                case 'journal article':
                case 'journal-article':
                case 'article':
                    $icon = "<i class='ph text-publication ph-file-text'></i>";
                    break 2;
                case 'magazine article':
                case 'magazine':
                    $icon = "<i class='ph text-publication ph-newspaper'></i>";
                    break 2;
                case 'book-chapter':
                case 'book chapter':
                case 'chapter':
                case 'book-editor':
                case 'publication':
                    $icon = "<i class='ph text-publication ph-book-bookmark'></i>";
                    break 2;
                case 'book':
                    $icon = "<i class='ph text-publication ph-book'></i>";
                    break 2;
                case 'preprint':
                    $icon = "<i class='ph text-publication ph-file-text'></i>";
                    break 2;
                case 'dissertation':
                    $icon = "<i class='ph text-publication ph-graduation-cap'></i>";
                    break 2;
                case 'others':
                default:
                    $icon = "<i class='ph text-publication ph-notebook'></i>";
                    break 2;
            }
        case 'poster':
            $icon = "<i class='ph text-poster ph-presentation-chart'></i>";
            break;
        case 'lecture':
            $icon = "<i class='ph text-lecture ph-chalkboard-teacher'></i>";
            break;
        case 'review':
            switch (strtolower($doc['role'] ?? $doc['subtype'] ?? '')) {
                case 'editorial':
                case 'editor':
                    $icon = "<i class='ph text-review ph-user-list'></i>";
                    break 2;
                case 'grant-rev':
                    $icon = "<i class='ph text-review ph-clipboard-text'></i>";
                    break 2;
                case 'thesis-rev':
                    $icon = "<i class='ph text-review ph-graduation-cap'></i>";
                    break 2;
                default:
                    $icon = "<i class='ph text-review ph-article'></i>";
                    break 2;
            }

        case 'misc':
            $subtype = $doc['iteration'] ?? $doc['subtype'] ?? '';
            if ($subtype == 'once' || $subtype == 'misc-once') {
                $icon = "<i class='ph text-misc ph-calendar-check'></i>";
            } elseif ($subtype == 'annual' || $subtype == 'misc-annual') {
                $icon = "<i class='ph text-misc ph-repeat'></i>";
            } else {
                $icon = "<i class='ph text-misc ph-shapes'></i>";
            }
            break;
        case 'students':
            $cat = strtolower(trim($doc['category'] ?? $doc['subtype'] ?? 'thesis'));
            if (str_contains($cat, "thesis") || $cat == 'doktorand:in' || $cat == 'students') {
                $icon = "<i class='ph text-students ph-student'></i>";
                break;
            }
            $icon = "<i class='ph text-students ph-users'></i>";
            break;

        case 'teaching':
            $icon = "<i class='ph text-teaching ph-chalkboard-simple'></i>";
            break;
        case 'software':
            $icon = "<i class='ph text-software ph-desktop-tower'></i>";
            break;
        case 'award':
            $icon = "<i class='ph text-award ph-certificate'></i>";
            break;
        default:
            break;
    }

    if ($tooltip) {
        $name = activity_title($doc);
        return "<span data-toggle='tooltip' data-title='$name'>
            $icon
        </span>";
    }
    return $icon;
}

function activity_badge($doc)
{
    $name = activity_title($doc);
    $icon =$Format->activity_icon($doc, false);
    return "<span class='badge badge-$doc[type]'>$icon $name</span>";
}

// format functions

class Format
{
    private $highlight = true;
    private $appendix = '';

    public $title = "";
    public $subtitle = "";
    public $usecase = "web";
    public $full = false;
    public $abbr_journal = false;


    function __construct($highlight = true, $usecase = 'web')
    {
        $this->highlight = $highlight;
        $this->usecase = $usecase;
    }

    function format($doc)
    {
        $line = "";
        $this->appendix = "";
        $this->title = "";
        $this->subtitle = "";
        switch ($doc['type'] ?? '') {
            case 'students':
                $line = $this->format_students($doc);
                break;
            case 'teaching':
                $line = $this->format_teaching($doc);
                break;
            case 'poster':
                $line = $this->format_poster($doc);
                break;
            case 'lecture':
                $line = $this->format_lecture($doc);
                break;
            case 'publication':
                $line = $this->format_publication($doc);
                break;
            case 'review':
                $line = $this->format_review($doc);
                break;
            case 'software':
                $line = $this->format_software($doc);
                break;
            case 'misc':
            default:
                $line = $this->format_misc($doc);
                break;
        }
        if ($this->usecase == 'web' && isset($doc['files'])) {
            foreach ($doc['files'] as $file) {
                $icon = getFileIcon($file['filetype']);
                $line .= " <a href='$file[filepath]' target='_blank' data-toggle='tooltip' data-title='$file[filetype]: $file[filename]' class='file-link'>
                <i class='ph ph-file ph-$icon'></i>
                </a>";
            }
        }
        if (!empty($this->appendix)) {
            $line .= "<br>";
            $line .= $this->appendix;
        }
        return $line;
    }

    function formatShort($doc, $link = true)
    {
        $this->subtitle = "";
        // init formatting:
        $this->format($doc);

        // format authors
        $author = "";
        if (isset($doc['authors']) && !empty($doc['authors'])) {
            // $a = $doc['authors'][0];
            // $author = abbreviateAuthor($a['last'], $a['first']);
            $authors = array();
            foreach ($doc['authors'] as $n => $a) {
                if ($n > 9) break;
                $author = abbreviateAuthor($a['last'], $a['first']);
                if ($this->highlight === true) {
                    //if (($a['aoi'] ?? 0) == 1) $author = "<b>$author</b>";
                } else if ($this->highlight && $a['user'] == $this->highlight) {
                    $author = "<u>$author</u>";
                }
                $authors[] = $author;
            }
            $author = implode(', ', $authors);
            if (is_countable($doc['authors']) && count($doc['authors']) > 9) $author .= " et al.";
        }

        switch ($doc['type'] ?? '') {
            case 'publication':
            case 'poster':
            case 'lecture':
            case 'software':
            case 'misc':
                $this->subtitle = "<span class='d-block'>$author</span>" . $this->subtitle;
                break;
            case 'students':
            case 'teaching':
                $this->subtitle = "<span class='d-block'>" . lang('supervised by ', 'betreut von ') . "$author" . "</span>" . $this->subtitle;
                break;
                $this->subtitle = "<span class='d-block'>" . lang('supervised by ', 'betreut von ') . "$author" . "</span>" . $this->subtitle;
                break;
            case 'review':
                $this->subtitle = $author . $this->subtitle;
                break;
            default:
                break;
        }

        // $icon =$Format->activity_icon($doc);
        if ($link) {
            $id = strval($doc['_id']);
            $line = "
            <a class='colorless' href='" . ROOTPATH . "/activities/view/$id'>$this->title</a>";
        } else {
            $line = $this->title;
        }

        $files = "";
        if (isset($doc['files'])) {
            foreach ($doc['files'] as $file) {
                $icon = getFileIcon($file['filetype']);
                $files .= " <a href='$file[filepath]' target='_blank' data-toggle='tooltip' data-title='$file[filetype]' class='file-link'>
                <i class='ph ph-file ph-$icon'></i>
                </a>";
            }
        }

        if (!empty($this->subtitle) || !empty($files)) {
            $line .= "<br><small class='text-muted d-block'>
                $this->subtitle
                $files
                </small>";
        }
        return $line;
    }


    function formatAuthors($raw_authors, $separator = 'and', $first = 1, $last = 1)
    {
        $n = 0;
        $authors = array();
        foreach ($raw_authors as $a) {

            if (!$this->full && $n++ >= 10 && ($a['aoi'] ?? 0) == 0) {
                if (end($authors) != '...')
                    $authors[] = "...";
                continue;
            }

            $author = abbreviateAuthor($a['last'], $a['first']);

            if ($this->highlight === true) {
                if (($a['aoi'] ?? 0) == 1) $author = "<b>$author</b>";
            } else if ($this->highlight && $a['user'] == $this->highlight) {
                $author = "<b>$author</b>";
            }

            if ($first > 1 && $a['position'] == 'first') {
                $author .= "<sup>#</sup>";
            }
            if ($last > 1 && $a['position'] == 'last') {
                $author .= "<sup>*</sup>";
            }
            if (isset($a['position']) && $a['position'] == 'corresponding') {
                $author .= "<sup>§</sup>";
            }

            $authors[] = $author;
        }

        $append = "";
        if (!$this->full && $n > 10 && end($authors) == '...') {
            $append = " et al.";
            $separator = ", ";
            array_pop($authors);
        }
        return commalist($authors, $separator) . $append;
    }

    function formatEditors($raw_editors, $separator = 'and')
    {
        $editors = array();
        foreach ($raw_editors as $a) {
            $editor = abbreviateAuthor($a['last'], $a['first'], false);
            if ($this->highlight === true) {
                if (($a['aoi'] ?? 0) == 1) $editor = "<b>$editor</b>";
            } else if ($this->highlight && $a['user'] ?? '' == $this->highlight) {
                $editor = "<b>$editor</b>";
            }
            $editors[] = $editor;
        }
        return commalist($editors, $separator);
    }


    function format_students($doc)
    {
        $result = "";
        if (!empty($doc['academic_title']) && str_contains($doc['academic_title'], 'Dr')) {
            $result .= $doc['academic_title'] . ' ';
        }
        $result .= $doc['name'] . ', ' . $doc['affiliation'] . '. ';
        $result .=  $doc['title'];

        $this->title = $result;
        $cat = strtolower(trim($doc['category']));

        switch ($cat) {
            case 'doctoral thesis':
                $this->subtitle = "Doktorand:in";
                break;
            case 'master thesis':
                $this->subtitle = "Master-Thesis";
                break;
            case 'bachelor thesis':
                $this->subtitle = "Bachelor-Thesis";
                break;
            case 'guest scientist':
                $this->subtitle = "Gastwissenschaftler:in";
                break;
            case 'lecture internship':
                $this->subtitle = "Pflichtpraktikum im Rahmen des Studium";
                break;
            case 'student internship':
                $this->subtitle = "Schülerpraktikum";
                break;
            case 'other':
                $this->subtitle = "Sonstiges";
                break;
            default:
                $this->subtitle = "$doc[category]";
                break;
        }
        if (!empty($doc['details'])) {
            $this->subtitle .= " (" . $doc["details"] . ")";
        }

        $this->subtitle .= ", " . fromToDate($doc['start'], $doc['end']);
        $result .= "; $this->subtitle";

        if ((str_contains($cat, "thesis") || $cat == 'doktorand:in') && !empty($doc['status'])) {

            if ($this->usecase == 'web' && $doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end'])) {
                $result .= " (<b style='color:#B61F29;'>" . $doc['status'] . "</b>),";
            } else {
                $result .= " (" . $doc['status'] . "),";
            }
        } else {
            $result .= ",";
        }

        $result .= " betreut von " . $this->formatAuthors($doc['authors']);

        return $result;
    }

    function format_teaching($doc)
    {
        $result = $this->formatAuthors($doc['authors']);

        if (isset($doc['module_id'])) {
            // $module_id = new MongoDB\BSON\ObjectId($doc['module_id']);
            // $module = $this->db->journal->findOne(['_id' => $doc]);
            $module = getConnected('teaching', $doc['module_id']);

            switch ($doc['category']) {
                case "lecture":
                    $this->title .= lang('Lecture', 'Vorlesung');
                    break;
                case "practical":
                    $this->title .= lang('Practical course', 'Praktikum');
                    break;
                case "practical-lecture":
                    $this->title .= lang('Lecture and practical course', 'Vorlesung und Praktikum');
                    break;
                case "seminar":
                    $this->title .= lang('Seminar');
                    break;
                case "other":
                default:
                    $this->title .= lang('Other course', 'Sonstige Lehrveranstaltung');
                    break;
            }
            $this->title .= lang(' for ', ' zu ') . $module['module'] . ": <em>" . $module['title'] . "</em>";
            $result .= $this->title;

            $this->subtitle .= $module['affiliation'];
        } else {
            if (!empty($doc['title'])) {

                $this->title = $doc['title'];
                $result .= " <i>$this->title</i>";
            }
            if (!empty($doc['affiliation'])) {
                $this->subtitle .= $doc["affiliation"];
            }
        }

        $this->subtitle .= " (" . fromToDate($doc['start'], $doc['end'] ?? null) . ")";
        $result .= ", $this->subtitle.";
        return $result;
    }

    function format_poster($doc)
    {
        $result = $this->formatAuthors($doc['authors']);
        if (!empty($doc['title'])) {
            $this->title = $doc['title'];
            $result .= " $this->title.";
        }

        if (!empty($doc['conference'])) {
            $this->subtitle .= "$doc[conference]";
        }
        if (!empty($doc['location'])) {
        }

        $result .= " $this->subtitle";
        if (!empty($doc['conference']) && !empty($doc['location'])) {
            $result .= ".";
        }
        $date = fromToDate($doc['start'], $doc['end'] ?? null);
        $this->subtitle .= " ($date)";
        $result .= " " . $date;
        $result .= ".";
        return $result;
    }

    function format_lecture($doc)
    {
        $result = $this->format_poster($doc);
        $type = ucfirst($doc['lecture_type']);
        if ($doc['invited_lecture'] ?? false) {
            $type = "Invited Lecture: $type";
        } else {
            $type .= " Lecture";
        }
        $this->subtitle .= ", $type";
        $result .= " ($type)";
        return $result;
    }

    function format_publication($doc)
    {
        $result = "";
        $type = strtolower(trim($doc['pubtype']));
        // $style = "apa6";

        // prepare authors
        $authors = "";
        $first = 1;
        $last = 1;
        $corresponding = false;

        if (!empty($doc['authors']) && $doc['authors'] instanceof BSONArray) {
            $doc['authors'] = $doc['authors']->bsonSerialize();
        }
        if (!empty($doc['authors']) && is_array($doc['authors'])) {
            $pos = array_count_values(array_column($doc['authors'], 'position'));
            $first = $pos['first'] ?? 1;
            $last = $pos['last'] ?? 1;
            $corresponding = array_key_exists('corresponding', $pos);
            $authors = $this->formatAuthors($doc['authors'], 'and', $first, $last);
        }


        $result .= $authors;

        switch ($type) {
            case 'journal article':
            case 'journal-article':
            case 'article':
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['correction'])) {
                    $result .= " <span class='text-danger'>Correction to:</span>";
                }
                if (!empty($doc['title'])) {
                    $this->title = $doc['title'];
                    $result .= " $this->title.";
                }
                if (!empty($doc['journal'])) {
                    $journal = $doc['journal'];
                    if ($this->abbr_journal) {
                        $j = getJournal($doc);
                        if (!empty($j) && !empty($j['abbr'])) {
                            $journal = $j['abbr'];
                        }
                    }
                    $this->subtitle .= " <i>$journal</i>";

                    if (!empty($doc['volume'])) {
                        $this->subtitle .= " $doc[volume]";
                    }
                    if (!empty($doc['issue'])) {
                        $this->subtitle .= "($doc[issue])";
                    }
                    if (!empty($doc['pages'])) {
                        $this->subtitle .= ": $doc[pages]";
                    }

                    $result .= " $this->subtitle.";
                }
                break;

            case 'magazine article':
            case 'magazine':
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $this->title = $doc['title'];
                    $result .= " $this->title.";
                }
                if (!empty($doc['magazine'])) {
                    $this->subtitle .= "<i>$doc[magazine]</i>";
                    $result .= " $this->subtitle.";
                }
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
            case 'book-chapter':
            case 'chapter':
            case 'book':
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $this->title = $doc['title'];
                    $result .= " $this->title.";
                }
                if (!empty($doc['book'])) {
                    $this->subtitle .= " In:";
                    if (!empty($doc['editors'])) {
                        $this->subtitle .= $this->formatEditors($doc['editors'], 'and') . " (eds).";
                    };
                    $this->subtitle .= " <i>$doc[book]</i>";
                }
                if (!empty($doc['edition']) || !empty($doc['pages']) || !empty($doc['volume'])) {
                    $ep = array();
                    if (!empty($doc['edition'])) {
                        $ed = $doc['edition'];
                        if ($ed == 1) $ed .= "st";
                        elseif ($ed == 1) $ed .= "nd";
                        else $ed .= "th";
                        $ep[] = $ed . " ed.";
                    }

                    if (!empty($doc['pages'])) {
                        $ep[] = "pp. $doc[pages]";
                    }
                    if (!empty($doc['volume'])) {
                        $ep[] = "Vol. $doc[volume]";
                    }

                    $this->subtitle .= " (" . implode(', ', $ep) . ")";
                }
                $result .= "$this->subtitle";

                if (!empty($doc['city'])) {
                    $result .= " $doc[city]:";
                }
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher].";
                    // $this->subtitle .= " $doc[publisher].";
                }
                break;
            case 'dissertation':
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $this->title = $doc['title'];
                    $result .= " $this->title";
                }
                $this->subtitle = "Dissertation";
                $result .= " (Dissertation).";
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher]";
                    $this->subtitle .= ", $doc[publisher]";
                    if (!empty($doc['city'])) {
                        $result .= ", $doc[city]";
                        $this->subtitle .= ", $doc[city]";
                    }
                    $result .= ".";
                }
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
            default:
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $this->title = $doc['title'];
                    $result .= " $this->title.";
                }
                $this->subtitle = ($doc["doc_type"] ?? ucfirst($doc['pubtype']));
                $result .= " [$this->subtitle].";
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
        }

        $this->subtitle .= " ($doc[year])";

        if ($this->usecase == 'web' || $this->usecase == 'dsmz.de') {
            if (!empty($doc['doi'])) {
                $result .= " DOI: <a target='_blank' href='https://doi.org/$doc[doi]'>https://doi.org/$doc[doi]</a>";
            }
        }
        if (!empty($doc['epub'])) {
            $result .= " <span style='color:#B61F29;'>[Online ahead of print]</span>";
            $this->subtitle .= " <span style='color:#B61F29;'>[Online ahead of print]</span>";
        }

        if ($this->usecase == 'web' && in_array($type, ['article', 'book', 'chapter'])) {
            if (!empty($doc['open_access'] ?? false)) {
                $access = '<i class="icon-open-access text-success" title="Open Access"></i>';
            } else {
                $access = '<i class="icon-closed-access text-danger" title="Closed Access"></i>';
            }
            $result .= " $access";
            $this->subtitle .= " $access";
        }

        if ($first > 1) {
            $this->appendix .= " <span style='color:#878787;'><sup>#</sup> Shared first authors</span>";
        }
        if ($last > 1) {
            $this->appendix .= " <span style='color:#878787;'><sup>*</sup> Shared last authors</span>";
        }
        if ($corresponding) {
            $this->appendix .= " <span style='color:#878787;'><sup>§</sup> Corresponding author</span>";
        }

        return $result;
    }


    function format_misc($doc)
    {
        $result = $this->formatAuthors($doc['authors']);

        if (!empty($doc['title'])) {
            $this->title = $doc['title'];
            $result .= " $this->title, ";
        }

        if ($doc['iteration']??'' == "annual") {
            $start = format_date($doc['start']);
            if (empty($doc['end'])) {
                $end = lang('today', "heute");
            } else {
                $end = format_date($doc['end']);
            }
            $date = lang("from $start to $end", "von $start bis $end");
        } else {
            $date = fromToDate($doc['start'], $doc['end']);
        }
        $result .= $date;

        if (!empty($doc['location'])) {
            $this->subtitle .= "$doc[location]";
            $result .= ", $doc[location].";
        } else {
            $result .= ".";
        }
        $this->subtitle .= " ($date)";
        return $result;
    }


    function format_review_role($doc)
    {
        $result = "";
        switch (strtolower($doc['role'])) {
            case 'editorial':
            case 'editor':
                $result .= lang("Member of the Editorial board of ", 'Mitglied des Editorial Board von ');
                $result .= '<i>' . $doc['journal'] . '</i>';

                if (!empty($doc['editor_type'])) {
                    $result .= " ($doc[editor_type])";
                }
                break;
            case 'grant-rev':
                if (isset($doc['review-type'])) {
                    $result .= $doc['review-type'] . ": ";
                } else {
                    $result .= lang("Reviewer of Grant Proposals: ", 'Begutachtung eines Forschungsantrages:');
                }
                $result .= ' <i>' . $doc['title'] . '</i>. ';
                break;
            case 'thesis-rev':
                $result .= lang("Reviewer for Doctoral Thesis: ", 'Begutachtung einer Doktorarbeit: ');
                $result .=  '<i>' . $doc['title'] . '</i>. ';
                break;
            default:
                $result .= lang("Reviewer for ", 'Reviewer für ');
                $result .= ' <i>' . $doc['journal'] . '</i>. ';
                break;
        }
        return $result;
    }


    function format_review($doc)
    {
        $result = "";
        if (!empty($doc['authors'] ?? '')) {
            $result .= $this->formatAuthors($doc['authors']);
        } elseif (!empty($doc['name'] ?? '')) {
            $result .= "<b>$doc[name]</b>";
        } else {
            $userdata = getUserFromId($doc['user']);
            $result .= "<b>$userdata[last], $userdata[first_abbr]</b>";
        }

        $this->title = $this->format_review_role($doc);
        $result .= " $this->title";

        switch (strtolower($doc['role'])) {
            case 'editorial':
            case 'editor':
                if (!empty($doc['start'])) {
                    if (!empty($doc['end'])) {
                        $date = lang("from ", "von ");
                        $date .= format_month($doc['start']['month']) . ' ' . $doc['start']['year'];
                        $date .= lang(" until ", " bis ");
                        $date .= format_month($doc['end']['month']) . ' ' . $doc['end']['year'];
                        $result .= ", $date.";
                    } else {
                        $date = lang("since ", "seit ");
                        $date .= format_month($doc['start']['month']) . ' ' . $doc['start']['year'];
                        $result .= ", $date.";
                    }
                }
                break;
            case 'grant-rev':
            case 'thesis-rev':
            case 'review':
            default:
                $date = format_month($doc['month']) . " " . $doc['year'];
                $result .= "$date.";
                break;
        }
        $this->subtitle .= " ($date)";
        return $result;
    }

    function format_software($doc)
    {
        $result = "";
        $result .= $this->formatAuthors($doc['authors']);

        if (!empty($doc['year'])) {
            $result .= " ($doc[year])";
        }

        if (!empty($doc['title'])) {
            $this->title = $doc['title'];
        }
        if (!empty($doc['version'])) {
            $this->title .= " (Version $doc[version])";
        }
        $result .= " <i>$this->title</i>";

        switch ($doc['software_type']) {
            case 'software':
                $this->subtitle .= "Computer software";
                break;
            case 'database':
                $this->subtitle .= "Database";
                break;
            case 'webtool':
                $this->subtitle .= "Webpage";
                break;
            case 'dataset':
                $this->subtitle .= "Dataset";
                break;
            default:
                $this->subtitle .= "Computer software";
                break;
        }
        $result .= " [$this->subtitle].";
        if (!empty($doc['year'])) {
            $this->subtitle .= " ($doc[year])";
        }

        if (!empty($doc['software_venue'])) {
            $result .= " $doc[software_venue].";
            $this->subtitle .= ", $doc[software_venue]";
        }

        if (!empty($doc['link'])) {
            $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
        }
        if (!empty($doc['doi'])) {
            if ($this->usecase == 'web') {
                $result .= " DOI: <a target='_blank' href='https://doi.org/$doc[doi]'>https://doi.org/$doc[doi]</a>";
            } else {
                $result .= " DOI: https://doi.org/$doc[doi]";
            }
        }
        return $result;
    }
}
