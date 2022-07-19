<?php

include_once 'Database.php';

class Lecture extends Database
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
            "SELECT *
        FROM lecture
        WHERE lecture_id = ?"
        );
        $stmt->execute([$id]);
        $pub = $stmt->fetch(PDO::FETCH_ASSOC);

        $authors = [];
        $stmt = $this->db->prepare("SELECT * FROM `authors` WHERE lecture_id = ?");
        $stmt->execute([$pub['lecture_id']]);
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $this->formatAuthors($authors);
        if (!empty($pub['year'])) {
            echo " ($pub[year])";
        }
        if (!empty($pub['title'])) {
            echo " $pub[title].";
        }
        if (!empty($pub['conference'])) {
            echo " $pub[conference].";
        }
        echo " ".$this->fromToDate($pub['date_start'], null);
        
        if (!empty($pub['location'])) {
            echo ", $pub[location].";
        } else {
            echo ".";
        }

        echo " (".$pub['lecture_type']. ")";
        // echo date_format($date,"d.m.Y");
        // echo 
    }
}
