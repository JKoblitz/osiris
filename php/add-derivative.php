<?php
$group = trim($_POST['derivative_group'] ?? "");
$group_id = getDerivativeGroup($group);

$name = $_POST['name'];
$ri = $_POST['retention_index'];
$qi = $_POST['quantification_ion'] ?? null;
if (empty($qi)){
$qi = null;
}
$mass = $_POST['mass_spectrum'] ?? "";
$n_peaks = 0;


// var_dump($_FILES);

if (empty($mass)) {
    try {
        if (
            !isset($_FILES['mass_file']['error']) ||
            is_array($_FILES['mass_file']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
        switch ($_FILES['mass_file']['error']) {
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
        if ($_FILES['mass_file']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }

        // DO NOT TRUST $_FILES['mass_file']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['mass_file']['tmp_name']),
            array(
                'csv' => 'text/plain'
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }

        $result = array();
        if (($handle = fopen($_FILES['mass_file']['tmp_name'], "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
                // $row++;
                if (empty($data[0]) || $data[0][0] === "#") {
                    if (empty($result)) {
                        continue;
                    } else {
                        break;
                    }
                }
                $peak = $data[0];
                $val = floatval($data[1]) * 1000;
                if ($val < 1) {
                    continue;
                }
                $val = round($val, 0);
                $result[] = "($peak $val)";
            }
            fclose($handle);
        }

        $mass = implode(' ', $result);
    } catch (RuntimeException $e) {

        echo $e->getMessage();
    }
} else {
    $mass = trim($mass);
    $mass = str_replace(array("\n", "\r"), " ", $mass);
}

$n_peaks = substr_count($mass, "(");



var_dump(["name" => $name, "retention_index" => $ri, "quantification_ion" => $qi, "group" => $group_id, "mass" => $mass, "n_peaks" => $n_peaks]);

if (empty($group_id)) {
    $stmt = $db->prepare(
        "INSERT INTO `Unknown` (`name`, retention_index, quantification_ion, mass_spectrum) VALUES (?,?,?,?)"
    );
    $stmt->execute([$name, $ri, $qi, $mass]);
    $id = $db->lastInsertId();
    header("Location: ".ROOTPATH."/browse/unknown/".$id);
} else {
    $stmt = $db->prepare(
        "INSERT INTO `Derivative` (`name`, retention_index, quantification_ion, mass_spectrum, num_peaks, derivative_group) VALUES (?,?,?,?,?,?)"
    );
    $stmt->execute([$name, $ri, $qi, $mass, $n_peaks, $group_id]);
    $id = $db->lastInsertId();
    header("Location: ".ROOTPATH."/browse/derivative/".$id);
}
