
<?php
    
class Levenshtein
{

    public $docs = array();
    private $osiris = null;
    public $found = '';

    function __construct($osiris)
    {
        $this->osiris = $osiris;
        $docs = $osiris->activities->find(['type'=>'publication'], ['projection' => ['title'=>1]])->toArray();
        foreach ($docs as $doc) {
            $this->docs[strval($doc['_id'])] = strtolower(strip_tags( $doc['title']));
        }
    }

    // function findDuplicate($title){
    //     $id = "";
    //     $dist = 1000;
    //     foreach ($this->docs as $key => $value) {
    //         $d_new = levenshtein($value,strtolower($title));
    //         if ($d_new < $dist){
    //             $id = $key;
    //             $dist = $d_new;
    //         }
    //         if ($dist == 0) break;
    //     }
    //     $len = strlen($title);
    //     if ($dist > $len) 
    //         $similarity = 0;
    //     else 
    //         $similarity = ($len-$dist)/$len * 100;
    //     return array($id, $dist, round($similarity,2));
    // }

    
    function findDuplicate($title){
        $id = "";
        $sim = 0;
        $percent = 0;
        foreach ($this->docs as $key => $value) {
            $s_new = similar_text(strtolower($title), $value, $p);
            if ($s_new > $sim){
                $id = $key;
                $sim = $s_new;
                $percent = $p;
                $this->found = $value;
            }
            if ($percent == 100) break;
        }
        return array($id, $sim, $percent);
    }

}
?>
