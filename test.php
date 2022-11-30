<?php

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
                $result .= $this->formatEditors($doc['editors'], 'and'). " (eds).";
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
        $result .= " DOI: <a target='_blank' href='http://dx.doi.org/$doc[doi]'>http://dx.doi.org/$doc[doi]</a>";
    }
}
if (!empty($doc['epub'])) {
    $result .= " <span style='color:#B61F29;'>[Epub ahead of print]</span>";
}

if ($this->usecase == 'web') {
    if (!empty($doc['open_access'])) {
        $result .= ' <i class="icon-open-access text-success" title="Open Access"></i>';
    } else {
        $result .= ' <i class="icon-closed-access text-orange" title="Open Access"></i>';
    }
}

if ($first > 1 || $last > 1) $result .= "<br>";
if ($first > 1) {
    $result .= "<span style='color:#878787;'><sup>#</sup> Shared first authors</span>";
}
if ($last > 1) {
    $result .= "<span style='color:#878787;'><sup>*</sup> Shared last authors</span>";
}

echo $result;
