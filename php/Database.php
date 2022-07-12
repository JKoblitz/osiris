<?php

class Database
{
    public $db = null;
    function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=osiris;charset=utf8mb4", 'juk', 'Zees1ius');

        if ($_SERVER['SERVER_NAME'] == 'testserver') {
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }


    public function fromToDate($from, $to)
    {

        $from = date_create($from);
        if (empty($to)) {
            return date_format($from, "d.m.Y");
        }
        $to = date_create($to);
    }


    public function commalist(array $array, $sep = "and")
    {
        if (empty($array)) return "";
        if (count($array) < 3) return implode(" $sep ", $array);
        $str = implode(", ", array_slice($array, 0, -1));
        return $str . ", $sep " . end($array);
    }

    public static function abbreviateAuthor($last, $first)
    {
        $fn = "";
        foreach (explode(" ", $first) as $name) {
            $fn .= " " . $name[0] . ".";
        }
        return $last . ", " . $fn;
    }

    public function formatAuthors(array $raw_authors)
    {
        $authors = array();
        foreach ($raw_authors as $a) {
            $author = $this->abbreviateAuthor($a['last_name'], $a['first_name']);
            if ($a['aoi'] == 1) {
                $author = "<b>$author</b>";
            }
            $authors[] = $author;
        }
        return Database::commalist($authors);
    }


    public function format_date($date)
    {
        $d = date_create($date);
        return date_format($d, "d.m.Y");
    }
}
