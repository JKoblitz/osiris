<?php


include_once 'Database.php';

class Misc extends Database
{
    // public $id = null;
    public $info = array();
    public $dates = array();
    function __construct()
    {
        parent::__construct();
        // $this->id = $id;
    }


    function print($id)
    {
        $stmt = $this->db->prepare(
            "SELECT *
            FROM misc
            WHERE misc_id = ?"
        );
        $stmt->execute([$id]);
        $pub = $stmt->fetch(PDO::FETCH_ASSOC);

        $authors = [];
        $stmt = $this->db->prepare("SELECT * FROM `authors` WHERE misc_id = ?");
        $stmt->execute([$pub['misc_id']]);
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo $this->formatAuthors($authors);

        if (!empty($pub['title'])) {
            echo " $pub[title], ";
        }

        $this->getDates($id, $pub['iteration']);
        if (!empty($pub['location'])) {
            echo ", $pub[location].";
        } else {
            echo ".";
        }
        // echo " ". $this->fromToDate($pub['date_start'], $pub['date_end']);
        // echo date_format($date,"d.m.Y");
        // echo 
    }

    function getDates($id, $type)
    {
        if ($type == "annual") {
            $stmt = $this->db->prepare(
                "SELECT MIN(date_start) AS `start`, MAX(date_end) AS `end`
                FROM misc_dates
                WHERE misc_id = ?"
            );
            $stmt->execute([$id]);
            $this->dates = $stmt->fetch(PDO::FETCH_ASSOC);
            $start = $this->format_date($this->dates['start']);
            if (empty($this->dates['end'])) {
                $end = "heute";
            } else {
                $end = $this->format_date($this->dates['end']);
            }
            echo "von $start bis $end";
        } else {

            $dates = [];
            $stmt = $this->db->prepare(
                "SELECT date_start AS `start`, date_end AS `end`
            FROM misc_dates
            WHERE misc_id = ?"
            );
            $stmt->execute([$id]);

            $this->dates = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($this->dates as $d) {
                $dates[] = $this->fromToDate($d['start'], $d['end']);
            }
            echo $this->commalist($dates, 'und');
        }
    }
}
