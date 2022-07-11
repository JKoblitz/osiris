<?php


include_once 'Database.php';

class Poster extends Database
{
    // public $id = null;
    function __construct()
    {
        parent::__construct();
        // $this->id = $id;
    }


    function print($id)
    {
        $stmt = $this->db->prepare(
            "SELECT poster.*
        FROM poster
        WHERE poster_id = ?"
        );
        $stmt->execute([$id]);
        $pub = $stmt->fetch(PDO::FETCH_ASSOC);

        $authors = [];
        $stmt = $this->db->prepare("SELECT * FROM `authors` WHERE poster_id = ?");
        $stmt->execute([$pub['poster_id']]);
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $this->formatAuthors($authors);
        if (!empty($pub['title'])) {
            echo " $pub[title].";
        }
        if (!empty($pub['conference'])) {
            echo " $pub[conference].";
        }
        echo " ". $this->fromToDate($pub['date_start'], $pub['date_end']);
        // echo date_format($date,"d.m.Y");
        // echo 
    }
}
