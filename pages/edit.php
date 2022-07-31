<h1>Edit <?= ucfirst($page) ?>: #<?= $dataset[$idname] ?></h1>

<p class="text-danger lead">
    <?= lang('Editing does not work yet!', 'Bearbeiten funktioniert noch nicht!') ?>
</p>
 
<div class="p-0" id="meta-form">
    <form action="#" class="" id="edit-form">
        <input type="hidden" name="id" value="<?= $id ?>">
        <input type="hidden" name="page" value="<?= $page ?>">
        <input type="hidden" name="action" value="update">

        <table class="table table-sm" id="input-table">
            <tbody>
                <?php foreach ($dataset as $key => $value) {
                    $schema = $schemata[$key][0];
                    $required = "";
                    if ($schema["IS_NULLABLE"] == "NO") {
                        $required = "required";
                    }
                ?>
                    <tr>
                        <td class="w-200">
                            <label for="<?= $key ?>" class="<?= $required ?>"><?= $key ?></label>
                        </td>
                        <td class="field">
                            <?php if (str_contains($key, '_id')) {
                                echo $value;
                            } else { ?>
                                <div class="form-group with-icon m-0">
                                    <?php
                                    switch ($schema["DATA_TYPE"]) {
                                        case 'boolean':
                                        case 'tinyint':
                                            echo "<div class='custom-switch'>
                                        <input data-value='$value' autocomplete='off' type='checkbox' id='switch-$key' value='1' name='$key' " . ($value == 1 ? 'checked' : '') . " $required >
                                        <label for='switch-$key' class='blank'></label>
                                      </div>";
                                            break;

                                        case 'text':
                                            echo "<textarea class='form-control' name='$key' id='$key' cols='30' rows='5' $required data-value='$value'>$value</textarea>";
                                            break;
                                            // case 'enum':
                                            //     echo "<select name='$key' id='$key'>";
                                            //     $enum = ['', '-', '+', '(+)'];
                                            //     foreach ($enum as $option) {
                                            //         echo "<option value='$option' " . ($value == $option ? 'selected' : '') . ">$option</option>";
                                            //     }
                                            //     echo "</select>";
                                            //     break;
                                        case 'date':
                                            echo " <input class='form-control' autocomplete='off' type='date' name='$key' id='$key' value='$value' $required data-value='$value'>";
                                            break;
                                        case 'int':
                                            echo " <input class='form-control' autocomplete='off' type='number' name='$key' id='$key' value='$value' $required data-value='$value'>";
                                            break;
                                        case 'float':
                                            echo " <input class='form-control' autocomplete='off' type='number' step='0.0001' name='$key' id='$key' value='$value' $required data-value='$value'>";
                                            break;
                                        case 'varchar':
                                            echo " <input class='form-control' autocomplete='off' type='text' maxlength='$schema[CHARACTER_MAXIMUM_LENGTH]' name='$key' id='$key' value='$value' $required data-value='$value'>";
                                            break;
                                        default:
                                            echo " <input class='form-control' autocomplete='off' type='text' name='$key' id='$key' value='$value' $required data-value='$value'>";
                                            break;
                                    }

                                    ?>
                                    <i class="link icon fas fa-arrow-rotate-left hidden" onclick="resetInput(this)"></i>
                                </div>
                            <?php } ?>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- <div class="form-group">
                <label for="editor-comment">Comment for applied changes (recommended):</label>
                <textarea name="editor-comment" id="editor-comment" cols="30" rows="10" class="form-control"></textarea>

            </div> -->
        <?php if ($page == 'publication' || $page == 'poster') {
            $stmt = $db->prepare("SELECT * FROM authors WHERE ${page}_id = ?");
            $stmt->execute([$id]);
            $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <h4><?= lang('Authors', 'Autoren') ?></h4>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <td>Last name</td>
                        <td>First name</td>
                        <td>Position</td>
                        <td><?=AFFILATION?> Affiliation</td>
                    </tr>
                </thead>
                <?php foreach ($authors as $i => $author) { ?>
                    <tr>
                        <td>
                            <input class='form-control' autocomplete='off' type='text' name='last_name' id='last_name-<?= $i ?>' value='<?= $author['last_name'] ?>' required data-value='<?= $author['last_name'] ?>'>
                        </td>
                        <td>
                            <input class='form-control' autocomplete='off' type='text' name='first_name' id='first_name-<?= $i ?>' value='<?= $author['first_name'] ?>' required data-value='<?= $author['first_name'] ?>'>
                        </td>
                        <td>
                            <select class='form-control' autocomplete='off' name='position' id='position-<?= $i ?>' required data-value='<?= $author['position'] ?>'>
                                <option value="first" <?= ($author['position'] == "first" ? 'selected' : '') ?>>first</option>
                                <option value="middle" <?= ($author['position'] == "middle" ? 'selected' : '') ?>>middle</option>
                                <option value="last" <?= ($author['position'] == "last" ? 'selected' : '') ?>>last</option>
                            </select>
                        </td>
                        <td>
                            <div class='custom-switch'>
                                <input data-value='<?= $author['aoi'] ?>' autocomplete='off' type='checkbox' id='switch-aoi-<?= $i ?>' value='1' name='aoi[]' <?= ($author['aoi'] == 1 ? 'checked' : '') ?> required>
                                <label for='switch-aoi-<?= $i ?>' class='blank'></label>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="4"><button class="btn"><i class="fas fa-plus"></i> <?= lang('Add author', 'Füge Autor hinzu') ?></button></td>
                </tr>
            </table>
        <?php } ?>


        <button class="btn btn-primary mt-20" type="submit"><i class="fas fa-check"></i> Submit changes</button>
    </form>
</div>


    <div class="alert alert-danger mt-20">
        <h4 class="alert-title">Delete this dataset</h4>
        <form action="<?= ROOTPATH ?>/update" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="page" value="<?= $page ?>">
            <input type="hidden" name="action" value="delete">

            <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i> Delete</button>
        </form>
    </div>