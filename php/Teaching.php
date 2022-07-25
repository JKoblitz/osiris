<?php

include_once 'Database.php';

class Teaching extends Database
{

    function __construct($id)
    {
        parent::__construct();
        $this->id = $id;
        $stmt = $this->db->prepare(
            "SELECT *
            FROM teaching
            WHERE teaching_id = ?"
        );
        $stmt->execute([$id]);
        $d = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->data = $d;

        if ($d['status'] == 'in progress' && new DateTime() > new DateTime($d['date_end'])) {
            $this::$msg[] = lang(
                "Attention: the Thesis of $d[name] has ended. Please confirm if the work was successfully completed or not or extend the time frame.",
                "Achtung: die Abschlussarbeit von $d[name] ist zu Ende. Bitte bestätige den Erfolg/Misserfolg der Arbeit oder verlängere den Zeitraum."
            );
        }

        $stmt = $this->db->prepare(
            "SELECT users.first_name, users.last_name, user, dept, IF(position='first', 1, 0) as aoi FROM `authors` 
            LEFT JOIN users USING (`user`) 
            WHERE teaching_id = ?"
        );
        $stmt->execute([$this->id]);
        $this->authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function print()
    {
        $d = $this->data;
        echo $d['academic_title'] . ' ' . $d['name'] . ', ' . $d['affiliation'] . '. ';
        echo  $d['title'] . '; ' . $d['category'];

        if (!empty($d['details'])) {
            echo " (" . $d["details"] . ")";
        }
        echo ". ";
        echo $this->fromToDate($d['date_start'], $d['date_end']);

        if (!empty($d['status'])) {

            if ($d['status'] == 'in progress' && new DateTime() > new DateTime($d['date_end'])) {
                echo " (<b class='text-danger'>" . $d['status'] . "</b>)";
            } else {
                echo " (" . $d['status'] . ")";
            }
        } else {
            echo "";
        }

        echo " betreut von " . $this->formatAuthors($this->authors);
    }

    function inSelectedQuarter()
    {
        // check if in selected quarter
        $qstart = new DateTime(SELECTEDYEAR . '-' . (3 * SELECTEDQUARTER - 2) . '-1 00:00:00');
        $qend = new DateTime(SELECTEDYEAR . '-' . (3 * SELECTEDQUARTER) . '-' . (SELECTEDQUARTER == 1 || SELECTEDQUARTER == 4 ? 31 : 30) . ' 23:59:59');

        $start = new DateTime($this->data['date_start']);
        $end = new DateTime($this->data['date_end']);
        if ($start <= $qstart && $qstart <= $end) {
            return true;
        } elseif ($qstart <= $start && $start <= $qend) {
            return true;
        }
        return false;
    }
}
