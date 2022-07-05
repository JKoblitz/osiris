<?php

include_once "analyze.php";

    echo implode(";", $header);
    
    foreach ($head as $value => $bla) {
        echo "mean " . $value . ";" . "sd " . $value . ";" . "se " . $value . ";" . "re " . $value . "\n";
    }

    foreach ($table as $name => $row) {
        
    echo implode(";", $row);
        echo "\n";
    }

?>