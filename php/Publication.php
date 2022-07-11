<?php


include_once 'Database.php';

class Publication extends Database
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
            "SELECT publication.*, IFNULL(journal_abbr, journal) AS journal 
            FROM publication
            LEFT JOIN journal USING (journal_id) 
            WHERE publication_id = ?"
        );
        $stmt->execute([$id]);
        $pub = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $authors = [];
        $stmt = $this->db->prepare("SELECT * FROM `authors` WHERE publication_id = ?");
        $stmt->execute([$pub['publication_id']]);
        $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $this->formatAuthors($authors);
        if (!empty($pub['year'])) {
            echo " ($pub[year])";
        }
        if (!empty($pub['title'])) {
            echo " $pub[title].";
        }
        if (!empty($pub['journal'])) {
            echo " <em>$pub[journal]</em>";
    
            if (!empty($pub['volume'])) {
                echo " $pub[volume]";
            }
            if (!empty($pub['pages'])) {
                echo ":$pub[pages].";
            }
        }
        if (!empty($pub['doi'])) {
            echo " DOI: <a target='_blank' href='http://dx.doi.org/$pub[doi]'>http://dx.doi.org/$pub[doi]</a>";
        }
        if (!empty($pub['epub'])) {
            echo " <span class='text-danger'>[Epub ahead of print]</span>";
        }
    }
}
