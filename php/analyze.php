<?php

function mean(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    return array_sum($a) / $n;
}


function sd(array $a, int $n = null, $sample = false){
    if ($n === null) {
        $n = count($a);
    }
    if ($n === 0) {
        trigger_error("The array has zero elements", E_USER_WARNING);
        return false;
    }
    if ($sample && $n === 1) {
        trigger_error("The array has only 1 element", E_USER_WARNING);
        return false;
    }
    $mean = array_sum($a) / $n;
    $carry = 0.0;
    foreach ($a as $val) {
        $d = ((float) $val) - $mean;
        $carry += $d * $d;
    };
    if ($sample) {
        --$n;
    }
    return sqrt($carry / $n);
}


function se(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    $sd = sd($a);
    return $sd / sqrt($n - 1);
}

function re(array $a, int $n = null){
    if ($n === null) {
        $n = count($a);
    }
    $mean = mean($a, $n);
    if ($mean == 0) {
        return 0;
    }
    return se($a) / $mean * 100;
}

try {
    // var_dump($_POST);
    // var_dump($_FILES);
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['infile']['error']) ||
        is_array($_FILES['infile']['error'])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }
    // Check $_FILES['infile']['error'] value.
    switch ($_FILES['infile']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown errors.');
    }
    // You should also check filesize here.
    if ($_FILES['infile']['size'] > 1000000) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // DO NOT TRUST $_FILES['infile']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['infile']['tmp_name']),
        array(
            'csv' => 'text/plain'
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format.');
    }

    $result = array();
    $head = array();
    // $row = 1;
    $header = array();
    if (($handle = fopen($_FILES['infile']['tmp_name'], "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
            // $row++;
            if (count($header) == 0) {
                $header = array_slice($data, 1);
                foreach ($header as $i => $value) {
                    $value = explode(":", $value)[0];
                    $head[$value][] = $i;
                }
            } else {
                $stmt = $db->prepare("SELECT g.derivative_group_name
                FROM `Derivative` AS d LEFT JOIN `Derivative_Group` AS g ON d.derivative_group = g.id
                WHERE d.name LIKE ? ");
                $stmt->execute([$data[0]]);
                $group = $stmt->fetch(PDO::FETCH_COLUMN);
                
                if (empty($group)) {
                    $group = $data[0];
                }

                $result[$group][] = array_slice($data, 1);
            }
        }
        fclose($handle);
    }
    $table = array();

    foreach ($result as $group_name => $row) {
        $table[$group_name] = array();

        foreach ($header as $i => $group) {
            $table[$group_name][$group] = 0;
            foreach ($row as $derivative) {
                $table[$group_name][$group] += ($derivative[$i] * 1000000);
            }
        }
        foreach ($head as $group => $indizes) {
            $a = array();
            foreach ($row as $derivative) {
                foreach ($indizes as $key => $i) {
                    // print_r($derivative[$i]);
                    $a[] = ($derivative[$i] * 1000000);
                }
            }
            $n = count($indizes);
            $table[$group_name]["mean " . $group] = mean($a, $n);
            $table[$group_name]["sd " . $group] = sd($a, $n);
            $table[$group_name]["se " . $group] = se($a, $n);
            $table[$group_name]["re " . $group] = re($a, $n);
        }
    }


    echo "<div class='card p-0'>";
    echo '<button class="btn btn-primary m-10" type="button" id="download" onclick="tableToCSV()">Download Result as CSV</button><br>';
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
    echo "</div>";

} catch (RuntimeException $e) {

    echo $e->getMessage();
}
?>