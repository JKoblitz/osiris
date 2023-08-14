<?php
/**
 * Page to edit coins
 * 
 * TODO: this should be incorporated into admin/activities.
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /coins
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */
?>

<style>
    .form-div {
        margin-left: .1rem;
        padding-left: 2rem;
        border-left: 1px dashed var(--border-color);
    }

    form > .form-div {
        padding-left: 0;
        border-left: none;
    }
</style>
<?php

function array_to_form($array, $level=2, $parents=array()){
    echo "<div class='form-div'>";
    foreach ($array as $key => $value) {
        if (is_array($value) || $level==2){
            echo "<h$level class='w-full my-0'>
                $key
            </h$level> ";
        }
        if (is_array($value) ) {
            array_to_form($value, $level+1, array_merge($parents, [$key]));
        } else {
            $name = "json";
            foreach ($parents as $p) {
                $name .= "[$p]";
            }
            $name .= "[$key]";
            echo "<div class='form-group'>
                <label for='$name' class='w-100'>$key</label>
                <input type='number' name='$name' id='$name' value='$value' min='0' max='1000' step='0.1' class='form-control' required>
            </div>";
        }
    }
    echo "</div>";
}

?>


<h1>
    <i class="ph ph-regular ph-lg ph-coin text-signal"></i>
    LOM Punktematrix
</h1>


<form action="#" method="post" class="form-inline w-400 mw-full">
    <?php

$matrix_json = file_get_contents(BASEPATH . "/matrix.json");
$matrix = json_decode($matrix_json, true, 512, JSON_NUMERIC_CHECK);

array_to_form($matrix);
?> 

<button type="submit" class="btn btn-primary">Update</button>
</form>