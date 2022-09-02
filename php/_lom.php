
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


    function publication($doc)
    {
        $prev_year = SELECTEDYEAR - 1;

        if (isset($doc['correction']) && $doc['correction']) return [];
        $type = "non-refereed";
        if (strtolower(trim($doc['type'])) == "journal article") {
            $type = "refereed";
        } elseif (strtolower(trim($doc['type'])) == "book") {
            $type = "book";
        }

        $authors = $doc['authors']->bsonSerialize();
        $author = array_filter($authors, function ($author) {
            return $author['user'] == $this->user;
        });

        if ($type == "book") {

            if ($author['is_editor']) {
                $pos = 'editor';
            } else {
                $pos = "chapter";
            }

            $points = $this->matrix['publication']['book'][$pos];
            return array(
                'type' => "publication>book>$pos",
                'id' => $doc['_id'],
                'title' => $doc['title'],
                'points' => "$points",
                'lom' => $points
            );
        }

        $pos = $author[0]['position'] ?? 'middle';

        if ($type == "non-refereed") {
            $points = $this->matrix['publication']['non-refereed'][$pos];
            return array(
                'type' => "publication>non-refereed>$pos",
                'id' => $doc['_id'],
                'title' => $doc['title'],
                'points' => "$points",
                'lom' => $points
            );
        }

        $journal = $this->osiris->journals->findOne(['journal' => $doc['journal']]);

        $if = 1;
        $impact = $journal['impact']->bsonSerialize();
        $impact = array_filter($impact, function ($a) use ($prev_year) {
            return $a['year'] == $prev_year;
        });
        if (!empty($impact)) {
            $if = reset($impact)['impact'];
        }
        $points = $this->matrix['publication']['refereed'][$pos];
        return array(
            'type' => "publication>refereed>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points * $if (IF)",
            'lom' => $points * floatval($if)
        );
    }

    function poster($doc)
    {
        $authors = $doc['authors']->bsonSerialize();
        $author = array_filter($authors, function ($author) {
            return $author['user'] == $this->user;
        });
        $pos = $author[0]['position'] ?? 'middle';

        $points = $this->matrix['poster'][$pos];
        return array(
            'type' => "poster>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points",
            'lom' => $points
        );
    }

    function lecture($doc)
    {
        // TODO: filter by presenting author only???
        $pos = $doc['lecture_type'] ?? 'short';
        $points = $this->matrix['lecture'][$pos];
        return array(
            'type' => "lecture>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points",
            'lom' => $points
        );
    }

    function review($doc)
    {
        if ($doc['role'] == "Editor") {
            $pos = "editorial";
            $points = $this->matrix[$pos];

            return array(
                'type' => "$pos",
                'id' => $doc['_id'],
                'title' => $doc['journal'],
                'points' => "$points",
                'lom' => $points
            );
        } else {
            $pos = "review";
            $dates = $doc['dates']->bsonSerialize();
            $dates = array_filter($dates, function ($date) {
                return $date['year'] == SELECTEDYEAR;
            });
            $dates = count($dates);
            $points = $this->matrix[$pos];

            return array(
                'type' => "$pos",
                'id' => $doc['_id'],
                'title' => $doc['journal'],
                'points' => "$points * $dates (count)",
                'lom' => $points * $dates
            );
        }
    }


    function misc($doc)
    {
        $pos = $doc['iteration'] ?? 'once';
        $points = $this->matrix['misc'][$pos];
        return array(
            'type' => "misc>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "$points",
            'lom' => $points
        );
    }

    function teaching($doc)
    {
        return  array(
            'type' => "teaching",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "TODO",
            'lom' => 0
        );
    }

    // TODO: teaching

}
