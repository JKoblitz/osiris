<?php

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
    $fn = "";
    foreach (explode(" ", $first) as $name) {
        $fn .= " " . $name[0] . ".";
    }
    if ($reverse) return $last . "," . $fn;
    return $fn . " " . $last;
}

function authorForm($a, $is_editor = false)
{
    $name = $is_editor ? 'editors' : 'authors';
    $aoi = $a['aoi'] ?? 1;
    return "<div class='author " . ($aoi == 1 ? 'author-aoi' : '') . "' onclick='toggleAffiliation(this);'>
        $a[last], $a[first]<input type='hidden' name='values[$name][]' value='$a[last];$a[first];$aoi'>
        <a onclick='removeAuthor(event, this);'>&times;</a>
        </div>";
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
    $date = getDateTime($time);
    $month = date_format($date, 'n');
    return ceil($month / 3);
}

function inSelectedQuarter($start, $end = null, $qarter = CURRENTQUARTER, $year = CURRENTYEAR)
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


function activity_icon($doc, $tooltip = true)
{
    if (is_string($doc)) {
        $type = strtolower(trim($doc));
    } else {
        $type = strtolower(trim($doc['type'] ?? ''));
    }
    $icon = "<i class='far fa-lg text-misc fa-notdef'></i>";
    $name = "Undefined";
    switch ($type) {
        case 'publication':
            $pubtype = strtolower(trim($doc['pubtype'] ?? ''));
            switch ($pubtype) {
                case 'journal article':
                case 'journal-article':
                case 'article':
                    $name = "Journal article";
                    $icon = "<i class='far fa-lg text-publication fa-file-lines'></i>";
                    break 2;

                case 'magazine article':
                case 'magazine':
                    $name = "Magazine article";
                    $icon = "<i class='far fa-lg text-publication fa-newspaper'></i>";
                    break 2;
                case 'book-chapter':
                case 'book chapter':
                case 'chapter':
                    $name = "Book chapter";
                    $icon = "<i class='far fa-lg text-publication fa-book'></i>";
                    break 2;
                case 'book-editor':
                    $name = "Book";
                    $icon = "<i class='far fa-lg text-publication fa-book-bookmark'></i>";
                case 'book':
                    $name = "Book";
                    $icon = "<i class='far fa-lg text-publication fa-book'></i>";
                    break 2;
                case 'others':
                    $name = lang('Others', 'Weiteres');
                    $icon = "<i class='far fa-lg text-publication fa-memo-pad'></i>";
                    break 2;
                default:
                    $name = "Journal article";
                    $icon = "<i class='far fa-lg text-publication fa-file-lines'></i>";
                    break 2;
            }
        case 'poster':
            $name = "Poster";
            $icon = "<i class='far fa-lg text-poster fa-presentation-screen'></i>";
            break;
        case 'lecture':
            $name = "Lecture";
            $icon = "<i class='far fa-lg text-lecture fa-keynote'></i>";
            break;
        case 'review':
            switch (strtolower($doc['role'] ?? '')) {
                case 'editorial':
                case 'editor':
                    $name = "Editorial board";
                    $icon = "<i class='far fa-lg text-review fa-book-open-cover'></i>";
                    break 2;
                case 'grant-rev':
                    $name = "Grant proposal";
                    $icon = "<i class='far fa-lg text-review fa-file-chart-pie'></i>";
                    break 2;
                case 'thesis-rev':
                    $name = "Thesis review";
                    $icon = "<i class='far fa-lg text-review fa-graduation-cap'></i>";
                    break 2;
                default:
                    $name = "Review";
                    $icon = "<i class='far fa-lg text-review fa-file-lines'></i>";
                    break 2;
            }

        case 'misc':
            $name = "Miscellaneous";
            $icon = "<i class='far fa-lg text-misc fa-icons'></i>";
            break;
        case 'students':
            $cat = strtolower(trim($doc['category'] ?? 'thesis'));
            if (str_contains($cat, "thesis") || $cat == 'doktorand:in') {
                $name = "Students (Theses)";
                $icon = "<i class='far fa-lg text-students fa-user-graduate'></i>";
                break;
            }
            $name = "Guests";
            $icon = "<i class='far fa-lg text-students fa-user-tie'></i>";
            break;

        case 'teaching':
            $name = "Teaching";
            $icon = "<i class='far fa-lg text-teaching fa-chalkboard-user'></i>";
            break;
        case 'software':
            $name = "Software";
            $icon = "<i class='far fa-lg text-software fa-desktop'></i>";
            break;
        default:
            break;
    }

    if ($tooltip) {
        return "<span data-toggle='tooltip' data-title='$name'>
            $icon
        </span>";
    }
    return $icon;
}

// format functions

class Format
{
    private $highlight = true;
    private $usecase = "web";
    private $first = 1;
    private $last = 1;

    function __construct($highlight = true, $usecase = 'web')
    {
        $this->highlight = $highlight;
        $this->usecase = $usecase;
    }

    function format($doc)
    {
        switch ($doc['type'] ?? '') {
            case 'students':
                return $this->format_students($doc);
            case 'teaching':
                return $this->format_teaching($doc);
            case 'poster':
                return $this->format_poster($doc);
            case 'lecture':
                return $this->format_lecture($doc);
            case 'publication':
                return $this->format_publication($doc);
            case 'misc':
                return $this->format_misc($doc);
            case 'review':
                return $this->format_review($doc);
            case 'software':
                return $this->format_software($doc);
                // if (strtolower($doc['role']) == 'reviewer') {

                // } else {
                //     return $this->format_editorial($doc);
                // }
            default:
                return "";
        }
    }


    function formatAuthors($raw_authors, $separator = 'and', $first = 1, $last = 1)
    {
        $authors = array();
        foreach ($raw_authors as $a) {
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
            $authors[] = $author;
        }
        return commalist($authors, $separator);
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


    function format_students($doc, $verbose = false)
    {
        $result = "";
        if (str_contains($doc['academic_title'], 'Dr')) {
            $result .= $doc['academic_title'] . ' ';
        }
        $result .= $doc['name'] . ', ' . $doc['affiliation'] . '. ';
        $result .=  $doc['title'];
        $cat = strtolower(trim($doc['category']));

        switch ($cat) {
            case 'doctoral thesis':
                $result .= "; Doktorand:in";
                break;
            case 'master thesis':
                $result .= "; Master-Thesis";
                break;
            case 'bachelor thesis':
                $result .= "; Bachelor-Thesis";
                break;
            case 'guest scientist':
                $result .= "; Gastwissenschaftler:in";
                break;
            case 'lecture internship':
                $result .= "; Vorlesung und Laborpraktikum";
                break;
            case 'student internship':
                $result .= "; Schülerpraktikum";
                break;
            default:
                $result .= "; $doc[category]";
                break;
        }

        if (!empty($doc['details'])) {
            $result .= " (" . $doc["details"] . ")";
        }
        $result .= ". ";
        $result .= fromToDate($doc['start'], $doc['end']);


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

        // if ($verbose) {
        //     if ($doc['status'] == 'in progress' && new DateTime() > getDateTime($doc['end'])) {
        //         echo '<p class="text-danger mt-0">' . lang(
        //             "Attention: the Thesis of $doc[name] has ended. Please confirm if the work was successfully completed or not or extend the time frame.",
        //             "Achtung: die Abschlussarbeit von $doc[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
        //         ) . '</p>';
        //     }
        // }

        return $result;
    }

    function format_teaching($doc)
    {
        $result = $this->formatAuthors($doc['authors']);
        if (!empty($doc['title'])) {
            $result .= " <em>$doc[title]</em>";
        }
        if (!empty($doc['affiliation'])) {
            $result .= ", $doc[affiliation]";
        }

        $result .= " (" . fromToDate($doc['start'], $doc['end'] ?? null) . ")";
        $result .= ".";
        return $result;
    }

    function format_poster($doc)
    {
        $result = $this->formatAuthors($doc['authors']);
        if (!empty($doc['title'])) {
            $result .= " $doc[title].";
        }
        if (!empty($doc['conference'])) {
            $result .= " $doc[conference]";
        }
        if (!empty($doc['location'])) {
            $result .= ", $doc[location]";
        }
        $result .= ".";

        $result .= " " . fromToDate($doc['start'], $doc['end'] ?? null);
        $result .= ".";
        return $result;
    }

    function format_lecture($doc)
    {
        $result = $this->formatAuthors($doc['authors']);
        // if (!empty($doc['year'])) {
        //     $result .= " ($doc[year])";
        // }
        if (!empty($doc['title'])) {
            $result .= " $doc[title].";
        }
        if (!empty($doc['conference'])) {
            $result .= " $doc[conference]";
        }
        if (!empty($doc['location'])) {
            $result .= ", $doc[location]";
        }
        $result .= ".";

        $result .= " " . fromToDate($doc['start'], null);

        $result .= ".";

        $result .= " (" . $doc['lecture_type'] . ")";
        return $result;
    }

    // function format_publication_CiteProc($doc)
    // {
    //     $first = 1;
    //     $last = 1;
    //     if (!empty($doc['authors']) && !is_array($doc['authors'])) {
    //         $doc['authors'] = $doc['authors']->bsonSerialize();
    //     }
    //     if (!empty($doc['authors']) && is_array($doc['authors'])) {
    //         $pos = array_count_values(array_column($doc['authors'], 'position'));
    //         $first = $pos['first'] ?? 1;
    //         $last = $pos['last'] ?? 1;
    //     }

    //     $types = [
    //         'article' => 'article-journal'
    //     ];

    //     $pub = [
    //         'author' => [],
    //         'id' => 'id' . rand(1000, 9999),
    //         'issued' => ['date-parts' => [[strval($doc['year'])]]],
    //         'title' => $doc['title'] ?? '',
    //         'type' => $types[$doc['pubtype']] ?? $doc['pubtype']
    //     ];
    //     foreach ($doc['authors'] as $a) {
    //         $item = [
    //             'family' => $a['last'],
    //             'given' => $a['first'],
    //             'aoi' => $a['aoi'] ?? false,
    //             'append' => '',
    //             'user' => $a['user'] ?? null
    //         ];
    //         if ($first > 1 && $a->position == 'first') {
    //             $item['append'] = "<sup>#</sup>";
    //         }
    //         if ($last > 1 && $a->position == 'last') {
    //             $item['append'] = "<sup>*</sup>";
    //         }
    //         $pub['author'][] = $item;
    //     }

    //     if (!empty($doc['editors'] ?? null)) {
    //         foreach ($doc['editors'] as $a) {
    //             $item = [
    //                 'family' => $a['last'],
    //                 'given' => $a['first'],
    //                 'aoi' => $a['aoi'] ?? false,
    //                 'append' => '',
    //                 'user' => $a['user'] ?? null
    //             ];
    //             $pub['container-author'][] = $item;
    //         }
    //     }
    //     if (!empty($doc['month'] ?? null)) {
    //         $pub['issued']['date-parts'][0][] = $doc['month'];
    //     }
    //     if (!empty($doc['doi'] ?? null)) {
    //         $pub['DOI'] = $doc['doi'];
    //     }
    //     if (!empty($doc['isbn'] ?? null)) {
    //         $pub['ISBN'] = $doc['isbn'];
    //     }
    //     if (!empty($doc['url'] ?? null)) {
    //         $pub['URL'] = $doc['url'];
    //     }
    //     if (!empty($doc['volume'] ?? null)) {
    //         $pub['volume'] = $doc['volume'];
    //     }
    //     if (!empty($doc['pages'] ?? null)) {
    //         if ($doc['pubtype'] == 'book') {
    //             $pub['number-of-pages'] = $doc['pages'];
    //         } else {
    //             $pub['page'] = $doc['pages'];
    //         }
    //     }
    //     if (!empty($doc['issue'] ?? null)) {
    //         $pub['issue'] = $doc['issue'];
    //     }
    //     if (!empty($doc['journal'] ?? null)) {
    //         $pub['container-title'] = $doc['journal'];
    //     }
    //     if (!empty($doc['city'] ?? null)) {
    //         $pub['publisher-place'] = $doc['city'];
    //     }
    //     if (!empty($doc['publisher'] ?? null)) {
    //         $pub['publisher'] = $doc['publisher'];
    //     }
    //     if (!empty($doc['edition'] ?? null)) {
    //         $pub['edition'] = $doc['edition'];
    //     }

    //     // dump($pub);
    //     //styling functions
    //     // $titleFunction = function ($cslItem, $renderedText) {
    //     //     return '<a href="https://example.org/publication/' . $cslItem->id . '">' . $renderedText . '</a>';
    //     // };
    //     $doiFunction = function ($cslItem, $renderedText) {
    //         return '<a target="_blank" href="https://doi.org/' . $renderedText . '">' . $renderedText . '</a>';
    //     };

    //     $authorFunction = function ($a, $renderedText) {
    //         $author = $renderedText;
    //         if (!$this->highlight) {
    //             if (($a->aoi ?? 0) == 1) $author = "<b>$author</b>";
    //         } else if ($a->user == $this->highlight) {
    //             $author = "<b>$author</b>";
    //         }
    //         if ($a->append) {
    //             $author .= $a->append;
    //         }
    //         // if (isset($a->aoi) && $a->aoi) {
    //         //     return '<a href="https://example.org/author/' . $a->aoi . '">' . $renderedText . '</a>';
    //         // }
    //         return $author;
    //     };

    //     $additionalMarkup = [
    //         // "title" => $titleFunction,
    //         "author" => $authorFunction,
    //         'DOI' => $doiFunction
    //     ];


    //     $dataString = json_encode([$pub]);
    //     $data = json_decode($dataString);

    //     $style = Seboettg\CiteProc\StyleSheet::loadStyleSheet("apa-6th-edition");
    //     $citeProc = new Seboettg\CiteProc\CiteProc($style, "en-US", $additionalMarkup);
    //     $result = $citeProc->render($data, "bibliography", []);
    //     $result = trim($result);

    //     if ($this->usecase == 'web') {
    //         if (!empty($doc['open_access'])) {
    //             $result .= ' <i class="icon-open-access text-orange" title="Open Access"></i>';
    //         }
    //     }


    //     if ($first > 1 || $last > 1) $result .= "<br>";
    //     if ($first > 1) {
    //         $result .= "<span style='color:#878787;'><sup>#</sup> Shared first authors</span>";
    //     }
    //     if ($last > 1) {
    //         $result .= "<span style='color:#878787;'><sup>*</sup> Shared last authors</span>";
    //     }
    //     return $result;
    // }

    function format_publication($doc)
    {


        $result = "";
        $type = strtolower(trim($doc['pubtype']));
        // $style = "apa6";

        // prepare authors
        $authors = "";
        $first = 1;
        $last = 1;
        if (!empty($doc['authors']) && !is_array($doc['authors'])) {
            $doc['authors'] = $doc['authors']->bsonSerialize();
        }
        if (!empty($doc['authors']) && is_array($doc['authors'])) {
            $pos = array_count_values(array_column($doc['authors'], 'position'));
            $first = $pos['first'] ?? 1;
            $last = $pos['last'] ?? 1;
            $authors = $this->formatAuthors($doc['authors'], 'and', $first, $last);
        }



        switch ($type) {
            case 'journal article':
            case 'journal-article':
            case 'article':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['correction'])) {
                    $result .= " <span class='text-danger'>Correction to:</span>";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['journal'])) {
                    $result .= " <em>$doc[journal]</em>";

                    if (!empty($doc['volume'])) {
                        $result .= " $doc[volume]";
                    }
                    if (!empty($doc['issue'])) {
                        $result .= "($doc[issue])";
                    }
                    if (!empty($doc['pages'])) {
                        $result .= ":$doc[pages].";
                    }
                }
                break;

            case 'magazine article':
            case 'magazine':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['magazine'])) {
                    $result .= " <em>$doc[magazine]</em>.";
                }
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
            case 'book-chapter':
            case 'chapter':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['book'])) {
                    // CHICAGO: // Last, First. “Titel.” In Book, edited by First Last. City: Publisher, 2020.
                    // APA 6: Last, F., & Last, F. (2020). Title. In F. Last (Ed.), _Book_ (pp. 1–10). City: Publisher.
                    // APA 7: Last, F., & Last, F. (2020). Title. In F. Last (Ed.), _Book_ (pp. 1–10). Publisher.
                    $result .= " In:";
                    if (!empty($doc['editors'])) {
                        $result .= $this->formatEditors($doc['editors'], 'and') . " (eds).";
                    };
                    $result .= " <em>$doc[book]</em>";
                }
                if (!empty($doc['edition']) || !empty($doc['pages'])) {
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

                    $result .= " (" . implode(', ', $ep) . ")";
                }
                $result .= ".";

                if (!empty($doc['city'])) {
                    $result .= " $doc[city]:";
                }
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher].";
                }
                break;
            case 'book':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " <em>$doc[title]</em>.";
                }
                if (!empty($doc['edition']) || !empty($doc['pages'])) {
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

                    $result .= " (" . implode(', ', $ep) . ").";
                }
                // $result .= ".";

                if (!empty($doc['city'])) {
                    $result .= " $doc[city]:";
                }
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher].";
                }
                break;
            default:
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                
                $result .= " [".($doc["doc_type"] ?? ucfirst($doc['pubtype']))."].";
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
        }

        if ($this->usecase == 'web') {
            if (!empty($doc['doi'])) {
                $result .= " DOI: <a target='_blank' href='https://doi.org/$doc[doi]'>https://doi.org/$doc[doi]</a>";
            }
        }
        if (!empty($doc['epub'])) {
            $result .= " <span style='color:#B61F29;'>[Epub ahead of print]</span>";
        }

        if ($this->usecase == 'web') {
            if (!empty($doc['open_access'])) {
                $result .= ' <i class="icon-open-access text-orange" title="Open Access"></i>';
            }
        }

        if ($first > 1 || $last > 1) $result .= "<br>";
        if ($first > 1) {
            $result .= "<span style='color:#878787;'><sup>#</sup> Shared first authors</span>";
        }
        if ($last > 1) {
            $result .= "<span style='color:#878787;'><sup>*</sup> Shared last authors</span>";
        }

        return $result;
    }

    function format_publication_($doc)
    {

        $result = "";
        $type = strtolower(trim($doc['pubtype']));
        // $style = "apa6";

        // prepare authors
        $authors = "";
        $first = 1;
        $last = 1;
        if (!empty($doc['authors']) && !is_array($doc['authors'])) {
            $doc['authors'] = $doc['authors']->bsonSerialize();
        }
        if (!empty($doc['authors']) && is_array($doc['authors'])) {
            $pos = array_count_values(array_column($doc['authors'], 'position'));
            $first = $pos['first'] ?? 1;
            $last = $pos['last'] ?? 1;
            $authors = $this->formatAuthors($doc['authors'], 'and', $first, $last);
        }



        switch ($type) {
            case 'journal article':
            case 'journal-article':
            case 'article':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['correction'])) {
                    $result .= " <span style='color:#B61F29;'>Correction to:</span>";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['journal'])) {
                    $result .= " <i>$doc[journal]</i>";

                    if (!empty($doc['volume'])) {
                        $result .= " $doc[volume]";
                    }
                    if (!empty($doc['pages'])) {
                        $result .= ": $doc[pages]";
                    }
                    $result .= ".";
                }
                break;

            case 'magazine article':
            case 'magazine':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['magazine'])) {
                    $result .= " <i>$doc[magazine]</i>.";
                }
                if (!empty($doc['link'])) {
                    $result .= " <a target='_blank' href='$doc[link]'>$doc[link]</a>";
                }
                break;
            case 'book-chapter':
            case 'chapter':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " $doc[title].";
                }
                if (!empty($doc['book'])) {
                    // CHICAGO: // Last, First. “Titel.” In Book, edited by First Last. City: Publisher, 2020.
                    // APA 6: Last, F., & Last, F. (2020). Title. In F. Last (Ed.), _Book_ (pp. 1–10). City: Publisher.
                    // APA 7: Last, F., & Last, F. (2020). Title. In F. Last (Ed.), _Book_ (pp. 1–10). Publisher.
                    $result .= " In:";
                    if (!empty($doc['editors'])) {
                        $result .= $this->formatEditors($doc['editors'], 'and') . " (eds).";
                    };
                    $result .= " <i>$doc[book]</i>";
                }
                if (!empty($doc['edition']) || !empty($doc['pages'])) {
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

                    $result .= " (" . implode(', ', $ep) . ")";
                }
                $result .= ".";

                if (!empty($doc['city'])) {
                    $result .= " $doc[city]:";
                }
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher].";
                }
                break;
            case 'book':
                $result .= $authors;
                if (!empty($doc['year'])) {
                    $result .= " ($doc[year])";
                }
                if (!empty($doc['title'])) {
                    $result .= " <i>$doc[title]</i>.";
                }
                if (!empty($doc['edition']) || !empty($doc['pages'])) {
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

                    $result .= " (" . implode(', ', $ep) . ")";
                }
                $result .= ".";

                if (!empty($doc['city'])) {
                    $result .= " $doc[city]:";
                }
                if (!empty($doc['publisher'])) {
                    $result .= " $doc[publisher].";
                }
                break;
            default:
                # code...
                break;
        }

        if ($this->usecase == 'web') {
            if (!empty($doc['doi'])) {
                $result .= " DOI: <a target='_blank' href='https://doi.org/$doc[doi]'>https://doi.org/$doc[doi]</a>";
            }
        }
        if (!empty($doc['epub'])) {
            $result .= " <span style='color:#B61F29;'>[Epub ahead of print]</span>";
        }

        if ($this->usecase == 'web') {
            if (!empty($doc['open_access'])) {
                $result .= ' <i class="icon-open-access text-orange" title="Open Access"></i>';
            }
        }

        if ($first > 1 || $last > 1) $result .= "<br>";
        if ($first > 1) {
            $result .= "<span style='color:#878787;'><sup>#</sup> Shared first authors</span>";
        }
        if ($last > 1) {
            $result .= "<span style='color:#878787;'><sup>*</sup> Shared last authors</span>";
        }

        return $result;
    }


    function format_misc($doc)
    {
        $result = $this->formatAuthors($doc['authors']);

        if (!empty($doc['title'])) {
            $result .= " $doc[title], ";
        }

        if ($doc['iteration'] == "annual") {

            // $dates = $doc['dates'][0];
            $start = format_date($doc['start']);
            if (empty($doc['end'])) {
                $end = lang('today', "heute");
            } else {
                $end = format_date($doc['end']);
            }
            $result .= lang("from $start to $end", "von $start bis $end");
        } else {

            // $dbdates = $doc['dates'];
            // foreach ($dbdates as $d) {
            //     $dates[] = fromToDate($d['start'], $d['end']);
            // }
            // $result .= commalist($dates, lang('and', 'und'));
            $result .= fromToDate($doc['start'], $doc['end']);
        }

        if (!empty($doc['location'])) {
            $result .= ", $doc[location].";
        } else {
            $result .= ".";
        }
        return $result;
    }



    function format_review($doc)
    {
        $result = "";
        if (!empty($doc['name'] ?? '')) {
            $result .= "<b>$doc[name]</b>";
        } elseif (!empty($doc['authors'] ?? '')) {
            $result .= $this->formatAuthors($doc['authors']);
        } else {
            $userdata = getUserFromId($doc['user']);
            $result .= "<b>$userdata[last], $userdata[first_abbr]</b>";
        }


        switch (strtolower($doc['role'])) {
            case 'editorial':
            case 'editor':
                $result .= lang(" Member of the Editorial board of ", ' Mitglied des Editorial Board von ');
                $result .= '<i>' . $doc['journal'] . '</i>';

                if (!empty($doc['editor_type'])) {
                    $result .= " ($doc[editor_type])";
                }

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
                break;
            case 'grant-rev':
                $result .= " " . lang("Reviewer of Grant Proposals: ", 'Begutachtung eines Forschungsantrages:');
                $result .= '<i>' . $doc['title'] . '</i>. ';
                $result .= format_month($doc['month']) . " " . $doc['year'];
                break;
            case 'thesis-rev':
                $result .= " " . lang("Reviewer for Doctoral Thesis: ", 'Begutachtung einer Doktorarbeit: ');
                $result .= '<i>' . $doc['journal'] . '</i>. ';
                $result .= format_month($doc['month']) . " " . $doc['year'];
                break;
            default:
                $result .= " " . lang("Reviewer for ", 'Reviewer für ');
                $result .= '<i>' . $doc['journal'] . '</i>. ';
                $result .= format_month($doc['month']) . " " . $doc['year'];
                break;
        }


        // $times = 0;
        // $times_current = 0;
        // foreach ($doc['dates'] as $date) {
        //     $times += 1;
        //     if ($date['year'] == SELECTEDYEAR) {
        //         $times_current += 1;
        //     }
        // }
        // if ($filterYear) {
        //     $result .= " ($times_current " . lang('times', 'mal') . ")";
        // } else {
        //     $result .= lang(
        //         " ($times times, $times_current in " . SELECTEDYEAR . ")",
        //         " ($times mal, davon $times_current in " . SELECTEDYEAR . ")"
        //     );
        // }
        $result .= ".";
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
            $result .= " <em>$doc[title]</em>";
        }
        if (!empty($doc['version'])) {
            $result .= " (Version $doc[version])";
        }

        switch ($doc['software_type']) {
            case 'software':
                $result .= " [Computer software]";
                break;
            case 'database':
                $result .= " [Database]";
                break;
            case 'webtool':
                $result .= " [Webpage]";
                break;
            case 'dataset':
                $result .= " [Dataset]";
                break;
            default:
                $result .= " [Computer software]";
                break;
        }
        $result .= ".";

        if (!empty($doc['software_venue'])) {
            $result .= " $doc[software_venue].";
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
