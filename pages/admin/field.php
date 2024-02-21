<?php
$formaction = ROOTPATH;
// if (!empty($form) && isset($form['_id'])) {
//     $formaction .= "/crud/fields/update/" . $form['_id'];
//     $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
//     $url = ROOTPATH . "/admin/fields/" . $form['id'];
//     $title = $name;
// } else {
$formaction .= "/crud/fields/create";
$btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
$url = ROOTPATH . "/admin/fields";
$title = lang('New field', 'Neues Feld');
// }

?>
<style>
    tr.ui-sortable-helper {
        background-color: white;
        border: 1px solid var(--border-color);
    }
</style>

<form action="<?= $formaction ?>" method="post" id="group-form">

    <div class="box">
        <h4 class="header">
            <?= $title ?>
        </h4>

        <div class="content">

            <div class="form-group">
                <label for="id">ID</label>
                <input type="text" class="form-control" name="values[id]" id="id">
                <small class="form-text">
                    <?= lang('Important! The ID will be used in the module list and in templates. Choose sth precise, unique and without spaces.', 'Wichtig! Die ID wird in der Modulliste gezeigt, wähle also etwas genaues, einzigartiges und nutze kein Leerzeichen!') ?>
                </small>
            </div>


            <div class="row row-eq-spacing">
                <div class="col-sm-6">
                    <label for="name" class="required ">Name (en)</label>
                    <input type="text" class="form-control" name="values[name]" required>
                </div>
                <div class="col-sm-6">
                    <label for="name_de" class="">Name (de)</label>
                    <input type="text" class="form-control" name="values[name_de]">
                </div>
            </div>

            <div class="row row-eq-spacing">
                <div class="col-sm-6">
                    <label for="format">Format</label>
                    <select class="form-control" name="values[format]" id="format" onchange="updateFields(this.value)">
                        <option value="string">Text</option>
                        <option value="text">Long text</option>
                        <option value="int">Integer</option>
                        <option value="float">Float</option>
                        <option value="list">List</option>
                        <option value="date">Date</option>
                        <option value="bool">Boolean</option>
                        <!-- <option value="user">User</option> -->
                    </select>
                </div>
                <div class="col-sm-6">

                    <label for="default">Default</label>
                    <input type="text" class="form-control" name="values[default]" id="default">

                </div>
            </div>



            <fieldset id="values-field" style="display: none;">
                <legend><?= lang('Possible values', 'Mögliche Werte') ?></legend>
                <table class="table simple small">
                    <tbody id="possible-values">
                        <tr>
                            <td class="w-50">
                                <i class="ph ph-dots-six-vertical text-muted handle"></i>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="values[values][]">
                            </td>
                            <td>
                                <a onclick="$(this).closest('tr').remove()"><i class="ph ph-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn" type="button" onclick="addValuesRow()"><i class="ph ph-plus-circle"></i></button>
            </fieldset>


            <button type="submit" class="btn success" id="submitBtn"><?= $btntext ?></button>

        </div>


</form>


<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script>
    function addValuesRow() {
        $('#possible-values').append(`
            <tr>
                <td class="w-50">
                    <i class="ph ph-dots-six-vertical text-muted handle"></i>
                </td>
                <td>
                    <input type="text" class="form-control" name="values[values][]">
                </td>
                <td>
                    <a onclick="$(this).closest('tr').remove()"><i class="ph ph-trash"></i></a>
                </td>
            </tr>
        `);
    }

    function updateFields(name) {
        $('#values-field').hide()
        switch (name) {
            case 'string':
                break;
            case 'text':
                break;
            case 'int':
                break;
            case 'float':
                break;
            case 'list':
                $('#values-field').show()
                break;
            case 'date':
                break;
            case 'bool':
                break;
            default:
                break;
        }
    }

    $(document).ready(function() {
        $('#possible-values').sortable({
            handle: ".handle",
            // change: function( event, ui ) {}
        });
    })
</script>