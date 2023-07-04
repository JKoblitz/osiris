<?php
// $fields = array_column($formular['custom_fields'], null, 'id');
$tiles = array_column($formular['step_tiles'], null, 'id');
$steps = array_column($formular['steps'], null, 'id');
// $values = array_column($formular['custom_field_values'], null, 'id');

$json = file_get_contents(IDA_PATH . "/fields.json");
$myFields = json_decode($json, true);

dump(json_last_error());
// dump($json);

// assign fields to steps
foreach ($formular['custom_fields'] as $field) {
    $steps[$field['step_id']]['fields'][$field['id']] = $field;
}

foreach ($formular['custom_field_values'] as $value) {
    $steps[$value['step_id']]['fields'][$value['custom_field_id']]['value'] = $value;
}

?>

<h1>
    <?= $formular['custom_fields'][0]['formular_short_title'] ?>
</h1>
<?php foreach ($steps as $step_id => $step) { ?>
    <h2><?= $step['short_title']['de'] ?></h2>

    <?php
    $fields = $step['fields'];
    uasort($fields, function ($a, $b) {
        if ($a['step_tile_id'] == $b['step_tile_id']){
            return $a['position'] < $b['position'] ? -1 : 1;
        }
        return ($a['step_tile_id'] < $b['step_tile_id'] ? -1 : 1);
    });

    ?>


    <table class="table">
        <?php 
            $last_title = '';
            foreach ($fields as $field) {

            $label = [];
            if (!empty($field['step_tile_id'])) {
                $label = $IDA->label($field, $tiles[$field['step_tile_id']]);
            } else {
                if (!empty($field['title']['de']))
                    $label[] = strip_tags($field['title']['de']);
                if (!empty($field['description']['de']))
                    $label[] = strip_tags($field['description']['de']);
                $label = implode('<br>', $label);
            }

            $val = "n.d.";
            if (array_key_exists($field['name'], $myFields)){
                $f = $myFields[$field['name']];
                $val = $osiris->activities->count($f['filter']);
            } 
        ?>
            <tr class="<?=($val == 'n.d.' ? 'row-muted': 'text-success')?>">
                <td><?= $label ?></td>
                <td><?= $field['name'] ?></td>
                <td><?= $field['typecast'] ?></td>
                <td><?= $field['value']['value'] ?? '-' ?></td>
                <td><?=$val?></td>
            </tr>
             <!-- <tr>
               <td>
              <?php
                    
                    dump($field, true);
                                ?>
               </td>
                
            </tr> -->
        <?php } ?>

    </table>

    <?php
    // dump($step, true);
    ?>
<?php }

// $step_tiles = array_column($formular["step_tiles"], null, "id");
die;
?>



<table class="table">
    <tbody>

        <?php foreach ($fields as $i => $field) {
            if (empty($field['step_tile_id'])) {
                // dump($field);
                continue;
            }

            $label = $IDA::ida_label($field, $step_tiles[$field['step_tile_id']])
        ?>
            <tr>
                <td><?= $label ?></td>
                <td><?= $field['name'] ?></td>
                <td><?= $field['typecast'] ?></td>
            </tr>
        <?php } ?>

    </tbody>

</table>