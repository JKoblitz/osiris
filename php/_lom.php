
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

    function get_author($doc){
        $authors = $doc['authors']->bsonSerialize();
        $author = array_filter($authors, function ($author) {
            return $author['user'] == $this->user;
        });
        
        if (empty($author)){
            return array();
        }
        return reset($author);
    }
    
function lom($col, $doc)
{
    switch ($col) {
        case 'students':
            return $this->students($doc);
        case 'poster':
            return $this->poster($doc);
        case 'lecture':
            return $this->lecture($doc);
        case 'publication':
            return $this->publication($doc);
        case 'misc':
            return $this->misc($doc);
        case 'review':
            return $this->review($doc);
        default:
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0",
                'lom' => 0
            );
    }
}

    function publication($doc)
    {
        $prev_year = SELECTEDYEAR - 1;

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
        $pubtype = strtolower(trim($doc['pubtype']));
        if ($pubtype == "article" || $pubtype == "journal-article" || $pubtype == 'journal article') {
            $type = "refereed";
        } elseif ($pubtype == "book") {
            $type = "book";
        }

        $author = $this->get_author($doc);
        $aff = (isset($author['aoi']) && ($author['aoi'] === 0 || $author['aoi'] === false));
        if (empty($author) || $aff){
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)': ''),
                'lom' => 0
            );
        }

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

        $pos = $author['position'] ?? 'middle';

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
        
        $j = new \MongoDB\BSON\Regex('^'.trim($doc['journal']), 'i');
        $journal = $this->osiris->journals->findOne(['journal' => ['$regex' => $j]]);

        $if = 1;
        if (!empty($journal)){
        $impact = $journal['impact']->bsonSerialize();
        if (is_array($impact)) {
            $impact = array_filter($impact, function ($a) use ($prev_year) {
                return $a['year'] == $prev_year;
            });
            if (!empty($impact)) {
                $if = reset($impact)['impact'];
            }
        }}
        $points = $this->matrix['publication']['refereed'][$pos];
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
        if (empty($author) || $aff){
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)': ''),
                'lom' => 0
            );
        }
        $pos = $author['position'] ?? 'middle';

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
        $author = $this->get_author($doc);
        $aff = (isset($author['aoi']) && ($author['aoi'] === 0 || $author['aoi'] === false));
        if (empty($author) || $aff){
            return array(
                'type' => "",
                'id' => 0,
                'title' => "",
                'points' => "0" . ($aff ? ' (not affiliated)': ''),
                'lom' => 0
            );
        }
        $pos = $doc['lecture_type'] ?? 'short';
        $points = $this->matrix['lecture'][$pos];
        return array(
            'type' => "lecture>$pos",
            'id' => $doc['_id'],
            'title' => $doc['title'] ?? '',
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
            // $dates = $doc['dates']->bsonSerialize();
            // $dates = array_filter($dates, function ($date) {
            //     return $date['year'] == SELECTEDYEAR;
            // });
            // $dates = count($dates);
            $points = $this->matrix[$pos];

            return array(
                'type' => "$pos",
                'id' => $doc['_id'],
                'title' => $doc['journal'],
                'points' => "$points",
                'lom' => $points
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

    function students($doc)
    {
        return  array(
            'type' => "students",
            'id' => $doc['_id'],
            'title' => $doc['title'],
            'points' => "TODO",
            'lom' => 0
        );
    }

    // TODO: students

}
