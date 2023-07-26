
<?php




class LOM
{

    public $matrix = array();
    private $user = null;
    private $osiris = null;

    function __construct($user = null, $osiris = null)
    {
        // parent::__construct($mod_id);
        $this->user = $user;

        $matrix_json = file_get_contents(BASEPATH . "/matrix.json");
        $this->matrix = json_decode($matrix_json, true, 512, JSON_NUMERIC_CHECK);

        // needed for journal IF
        $this->osiris = $osiris;
    }

    function get_author($doc)
    {
        $authors = $doc['authors'];
        if (!is_array($authors))
            $authors = $authors->bsonSerialize();
        $author = array_filter($authors, function ($author) {
            return $author['user'] == $this->user;
        });

        if (empty($author) && isset($doc['editors'])) {
            $authors = $doc['authors']->bsonSerialize();
            $author = array_filter($authors, function ($author) {
                return $author['user'] == $this->user;
            });
            if (!empty($author)) {
                $author = reset($author);
                $author['is_editor'] = true;
                return $author;
            }
        }
        if (empty($author)) {
            return array();
        }
        return reset($author);
    }

    function lom($doc)
    {
        $type = $doc['type'] ?? 'unknown';
        if (method_exists($this, $type)) {
            return $this->$type($doc);
        } else {
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0 (not implemented yet)",
                'lom' => 0
            );
        }
    }

    function publication($doc)
    {
        $prev_year = ($doc['year'] ?? CURRENTYEAR) - 1;

        if (isset($doc['correction']) && $doc['correction']) return array(
            'type' => "",
            'id' => 0,
            'title' => "",
            'points' => "0 (correction)",
            'lom' => 0
        );
        if (isset($doc['epub']) && $doc['epub']) return array(
            'type' => "",
            'id' => 0,
            'title' => "",
            'points' => "0 (epub)",
            'lom' => 0
        );
        $type = "non-refereed";
        $pubtype = strtolower(trim($doc['subtype'] ?? $doc['pubtype']));
        if ($pubtype == "article" || $pubtype == "journal-article" || $pubtype == 'journal article') {
            $type = "refereed";
        } elseif ($pubtype == "book" || $pubtype == "chapter") {
            $type = "book";
        }

        $author = $this->get_author($doc);
        $aff = (!isset($author['aoi']) || ($author['aoi'] == 0 || $author['aoi'] === false));
        if (empty($author) || $aff) {
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)' : ''),
                'lom' => 0
            );
        }

        if ($type == "book") {
            if ($author['is_editor'] ?? false) {
                $pos = 'editor';
            } else {
                $pos = "chapter";
            }

            $points = $this->matrix['publication']['book'][$pos] ?? 0;
            return array(
                'type' => "publication>book>$pos",
                'id' => $doc['_id'],
                'title' => $doc['title'],
                'points' => "$points ($pos)",
                'lom' => $points
            );
        }

        $pos = $author['position'] ?? 'middle';

        $posKey = $pos;
        if ($pos == 'corresponding') $posKey = 'last';


        if ($type == "non-refereed") {
            $points = $this->matrix['publication']['non-refereed'][$posKey] ?? 0;
            return array(
                'type' => "publication>non-refereed>$pos",
                'id' => $doc['_id'],
                'title' => $doc['title'],
                'points' => "$points (non-refereed, $pos)",
                'lom' => $points
            );
        }

        if (isset($doc['impact'])) {
            $if = $doc['impact'];
        } else {
            $if = get_impact($doc);
        }
        if (empty($if)) $if = 1;
        $points = $this->matrix['publication']['refereed'][$posKey] ?? 0;
        return array(
            'type' => "publication>refereed>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points ($pos) * $if (IF)",
            'lom' => round($points * floatval($if))
        );
    }

    function poster($doc)
    {
        $author = $this->get_author($doc);
        $aff = (isset($author['aoi']) && ($author['aoi'] === 0 || $author['aoi'] === false));
        if (empty($author) || $aff) {
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)' : ''),
                'lom' => 0
            );
        }
        $pos = $author['position'] ?? 'middle';

        $points = $this->matrix['poster'][$pos] ?? 0;
        return array(
            'type' => "poster>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points ($pos)",
            'lom' => $points
        );
    }

    function lecture($doc)
    {
        $author = $this->get_author($doc);
        if (!isset($author['position']) || $author['position'] !== 'first') {
            $authors = $doc['authors']->bsonSerialize();
            $author = array_shift($authors);
            if ($author['user'] != $this->user) {
                return array(
                    'type' => "",
                    'id' => 0,
                    'title' => "",
                    'points' => "0 (not presenting)",
                    'lom' => 0
                );
            }
        }

        $aff = (isset($author['aoi']) && ($author['aoi'] === 0 || $author['aoi'] === false));
        if (empty($author) || $aff) {
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)' : ''),
                'lom' => 0
            );
        }
        $pos = $doc['lecture_type'] ?? 'short';
        $points = $this->matrix['lecture'][$pos] ?? 0;
        return array(
            'type' => "lecture>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'] ?? '',
            'points' => "$points ($pos)",
            'lom' => $points
        );
    }

    function review($doc)
    {
        $role = strtolower($doc['subtype'] ?? $doc['role']);
        if ($role == "editor" || $role == 'editorial') {
            $pos = "editorial";
            $points = $this->matrix[$pos] ?? 0;

            return array(
                'type' => "$pos",
                'id' => $doc['_id'],
                'title' => $doc['journal'],
                'points' => "$points (Editorial)",
                'lom' => $points
            );
        } else {
            $pos = "review";
            $points = $this->matrix[$pos];

            return array(
                'type' => "$pos",
                'id' => $doc['_id'],
                'title' => $doc['journal'] ?? '',
                'points' => "$points (Review)",
                'lom' => $points
            );
        }
    }


    function misc($doc)
    {
        $pos = $doc['iteration'] ?? 'once';
        $points = $this->matrix['misc'][$pos] ?? 0;
        if ($pos !== 'once'){
            $pos = 'frequently';
        }
        return array(
            'type' => "misc>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points ($pos)",
            'lom' => $points
        );
    }

    function students($doc)
    {
        $cat = strtolower(trim($doc['subtype'] ?? $doc['category'] ?? 'thesis'));
        if (str_contains($cat, "thesis") || $cat == 'doktorand:in') {
            $cat = "thesis";
        } else {
            $cat = 'guests';
        }
        $points = $this->matrix['students'][$cat] ?? 0;
        return  array(
            'type' => "students>$cat",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points ($cat)",
            'lom' => $points
        );
    }

    function teaching($doc)
    {

        $points = $this->matrix['teaching'] ?? 0;
        $author = $this->get_author($doc);
        $sws = floatval($author['sws'] ?? 0);
        // $sws = $doc['sws'];
        return  array(
            'type' => "teaching",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points * $sws (SWS)",
            'lom' => $points * $sws
        );
    }


    function software($doc)
    {

        $points = $this->matrix['software'] ?? 0;
        return  array(
            'type' => "software",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points",
            'lom' => $points
        );
    }


}
