<?php
// $fields = array_column($formular['custom_fields'], null, 'id');
$tiles = array_column($formular['step_tiles'], null, 'id');
$steps = array_column($formular['steps'], null, 'id');
// $values = array_column($formular['custom_field_values'], null, 'id');

$json = file_get_contents(IDA_PATH . "/fields.json");
$myFields = json_decode($json, true);

// assign fields to steps
foreach ($formular['custom_fields'] as $field) {
    $steps[$field['step_id']]['fields'][$field['id']] = $field;
}

// foreach ($formular['values'] as $value) {
//     $steps[$value['step_id']]['fields'][$value['custom_field_id']]['value'] = $value;
// }


// foreach ($formular['previous_values'] as $key => $value) {
//     $steps[$value['step_id']]['fields'][$key]['previous_value'] = $value;
// }

$current = $formular['values'];
$previous = $formular['previous_values'];

?>

<style>
    .table tr.irrelevant {
        display: none;
    }

    .table.show-all tr.irrelevant {
        display: table-row;
    }
</style>

<h1>
    <?= $formular['custom_fields'][0]['formular_short_title'] ?>
</h1>
<?php if ($IDA->state !== 'submitted') { ?>
    <button class="btn" onclick="ida_send_block_values(null)">
        Formular an IDA senden
    </button>
<?php } else { ?>
    <em class="text-muted">--submitted--</em>
<?php } ?>
<?php foreach ($steps as $step_id => $step) { ?>
    <h2><?= $step['short_title']['de'] ?? 'unknown title' ?></h2>

    <?php
    $fields = $step['fields'];
    uasort($fields, function ($a, $b) {
        if ($a['step_tile_id'] == $b['step_tile_id']) {
            return $a['position'] < $b['position'] ? -1 : 1;
        }
        return ($a['step_tile_id'] < $b['step_tile_id'] ? -1 : 1);
    });

    ?>

    <div class="custom-switch">
        <input type="checkbox" id="switch-<?= $step_id ?>" value="" onchange="$('#step-<?= $step_id ?>').toggleClass('show-all')">
        <label for="switch-<?= $step_id ?>">Zeige alle Felder</label>
    </div>

    <table class="table" id="step-<?= $step_id ?>">
        <thead>
            <tr>
                <th>Name</th>
                <th>IDA <small>(Vorjahr)</small></th>
                <th>IDA</th>
                <th>OSIRIS</th>
                <th>
                    <?php if ($IDA->state !== 'submitted') { ?>
                        <button class="btn" onclick="ida_send_block_values('<?= $step_id ?>')">
                            Block senden
                        </button>
                    <?php } else { ?>
                        <em class="text-muted">--submitted--</em>
                    <?php } ?>
                </th>
            </tr>
        </thead>
        <?php
        $last_title = '';
        foreach ($fields as $field) {

            $label = [];
            if (!empty($field['step_tile_id'])) {
                $label = $IDA->label($field, $tiles[$field['step_tile_id']]);
            } else {
                $label = $IDA->label($field, null);
            }

            $val = "n.d.";
            if (array_key_exists($field['name'], $myFields)) {
                $f = $myFields[$field['name']];
                $filter = $f['filter'];
                $filter['year'] = 2022;
                $val = $osiris->activities->count($filter);
            }
        ?>
            <tr class="<?= ($val == 'n.d.' ? 'irrelevant' : 'text-success') ?>">
                <td>
                    <?= $label ?>
                    <br>
                    <small class="code text-muted"><?= $field['name'] ?> (<?=str_replace('type', '',  $field['typecast']) ?>)</small>
                </td>
                <td><?= $previous[$field['name']] ?? '-' ?></td>
                <td id="ida-<?= $field['id'] ?>"><?= $current[$field['name']] ?? '-' ?></td>
                <td><?= $val ?></td>
                <td>
                    <?php if ($IDA->state !== 'submitted') { ?>
                        <?php if ($val != 'n.d.') { ?>
                            <button class="btn send-btn" onclick="ida_send_value(
                        '<?= $IDA->dataset_id ?>', '<?= $field['id'] ?>', '<?= $val ?>', '<?= $_SESSION['ida-mail'] ?>', '<?= $_SESSION['ida-token'] ?>'
                        )">
                                Send
                            </button>
                        <?php } ?>
                    <?php } else { ?>
                        <em class="text-muted">--submitted--</em>
                    <?php } ?>


                </td>
            </tr>
        <?php } ?>

    </table>


<?php } ?>


<script>
    function ida_send_value(dataset_id, field_id, value, email, token) {
        /* Send the data using post */
        console.log(field_id);
        var url = "https://mainly.api.ida.leibniz-gemeinschaft.de/incoming/applications/" + dataset_id + "/custom_field_values"
        $.ajax({
            url: url,
            type: "post",
            data: {
                custom_field_value: {
                    value: value,
                },
                custom_field_id: field_id
            },
            headers: {
                "X-USER-EMAIL": email,
                "X-USER-TOKEN": token
            }
        }).done(function(data) {
            $("#ida-" + field_id).html(data.value);
        }).fail(function() {
            console.log("Fehler beim Übertragen");
        });
    }
    /* Function for mass transfer of values of a specific block (formular, step etc.) */
    function ida_send_block_values(step_id) {
        if (step_id === null)
            $('.send-btn').click();
        else
            $("#step-" + step_id + ' .send-btn').click();

    }
</script>