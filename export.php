<?php

$date = date("Y-m-d");
// header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="library_'.$date.'.msl"');
header("Content-type: text/plain");
header("Pragma: no-cache");
header("Expires: 0");


function newline($n = 1)
{
    return str_repeat("\n", $n);
}




$stmt = $db->prepare("SELECT * FROM `Derivative`");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row) {

    $schema = [
        "NAME" => $row["name"],
        "RI" => $row["retention_index"],
        "RT" => "-1.0000",
        "BRENDA_LIGAND_ID" => $row["BRENDA_ligand_id"],
        "COMMENT" => $row["comment"],
        "DERIVATIVE_GROUP" => $row["derivative_group"],
        "GMD_NAME" => $row["gmd_name"],
        "ID" => $row["id"],
        "INCHI_KEY" => $row["inchi_key"],
        // "METABOLITE_NAMES" => $row["metabolite_names"],
        "MOLECULAR_WEIGHT" => $row["molecular_weight"],
        "SUM_FORMULA" => $row["sum_formula"],
        "TYPE" => "EI",
        "QUANTIFICATION" => $row["quantification_ion"] ?? " ",
        "RESOLUTION" => "1.0000",
        "NUM PEAKS" => $row["num_peaks"]
    ];
    foreach ($schema as $key => $value) {
        if (!empty($value)){
            echo "$key: $value\n";
        }
    }
    $peaks = str_replace(array("\n", "\r"), " ", $row["mass_spectrum"]);
    $peaks = explode(", ", $peaks);
    foreach ($peaks as $peak) {
        echo "$peak\n";
    }

    echo newline(1);
}





$stmt = $db->prepare("SELECT * FROM `Unknown`");
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($data as $row) {

    $schema = [
        "NAME" => $row["name"],
        "RI" => $row["retention_index"],
        "RT" => "-1.0000",
        "COMMENT" => $row["comment"],
        "GMD_NAME" => $row["gmd_analyte_id"],
        "ID" => $row["id"],
        "TYPE" => "EI",
        "UNKNOWN_NO" => $row["nr"],
        "QUANTIFICATION" => $row["quantification_ion"] ?? " ",
        "RESOLUTION" => "1.0000",
        "NUM PEAKS" => substr_count($row["mass_spectrum"], "(")
    ];
    foreach ($schema as $key => $value) {
        if (!empty($value)){
            echo "$key: $value\n";
        }
    }
    $peaks = str_replace(array("\n", "\r"), " ", $row["mass_spectrum"]);
    $peaks = explode(", ", $peaks);
    foreach ($peaks as $peak) {
        echo "$peak\n";
    }

    echo newline(1);
}
