<?php

include_once "analyze.php";

    echo '<button class="btn btn-primary mt-2" type="button" id="download">Download Result as CSV</button><br>';
    // echo json_encode($result);
    echo "<div class='table-responsive'>";
    echo "<table class='table  '>";

    echo "<thead><th>Name</th>";
    foreach ($header as $value) {
        echo "<th>";
        echo $value;
        echo "</th>";
    }
    foreach ($head as $value => $bla) {
        echo "<th>mean " . $value . "</th>";
        echo "<th>sd " . $value . "</th>";
        echo "<th>se " . $value . "</th>";
        echo "<th>re " . $value . "</th>";
    }
    echo "<th></th></thead>";

    foreach ($table as $name => $row) {
        echo "<tr>";
        echo "<td>$name</td>";
        // print_r($row)
        foreach ($row as $key => $value) {
            echo "<td>";
            echo $value;
            echo "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";

?>