<?php
include_once BASEPATH . "/php/Misc.php";
?>

<div class="content">

    <h1><i class="fa-regular fa-book-open-cover text-success fa-lg mr-10"></i> <?= lang('My other research activities', 'Meine anderen Forschungsaktivitäten') ?></h1>


    <div class="box box-primary" id="misc-form" style="display:none">
        <div class="content">
            <form action="#" method="post">
                <div class="form-group">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

                <div class="form-group">
                    <label for="author" class="required"><?= lang('Author(s)', 'Autor(en)') ?></label>
                    <div class="author-list">
                        <div class="author author-dsmz">
                            <?= $userClass->name('formal') ?><input type="hidden" name="author[]" value="<?= $userClass->last ?>;<?= $userClass->first ?>;1">
                            <a onclick="removeAuthor(event, this)">&times;</a>
                        </div>
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                </div>

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label class="required" for="iteration"><?= lang('Iteration', 'Häufigkeit') ?></label>
                        <select name="iteration" id="iteration" class="form-control">
                            <option value="once">once</option>
                            <option value="annual">annual</option>
                            <!-- <option value="monthly">monthly</option>
                            <option value="quarterly">quarterly</option> -->
                        </select>
                    </div>
                    <div class="col-sm">
                        <label for="location"><?= lang('Location', 'Ort') ?></label>
                        <input type="text" class="form-control" name="location" id="location" placeholder="online">
                    </div>
                </div>

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                        <input type="date" class="form-control" name="date_start" id="date_start" required>

                        <span class="text-muted">
                            <?= lang('You can add more dates later.', 'Du kannst später weitere Daten hinzufügen.') ?>
                        </span>
                    </div>
                    <div class="col-sm">
                        <label for="date_end"><?= lang('End', 'Ende') ?></label>
                        <input type="date" class="form-control" name="date_end" id="date_end">
                        <span class="text-muted">
                            <?= lang(
                                'Only needed if one-time event is more than one day or if a continous work ended.',
                                'Nur benötigt falls ein einmaliges Event mehr als einen Tag geht oder ein kontinuierliches Event endet.'
                            ) ?>
                        </span>
                    </div>
                </div>



                <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>

            </form>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <!-- <td><?= lang('Quarter', 'Quartal') ?></td> -->
                <td><?= lang('Activity', 'Aktivität') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr id="add-btn-row">
                <td colspan="3">
                    <button class="btn" onclick="$('#add-btn-row').hide();$('#misc-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
                </td>
            </tr>


            <?php
            $misc = new Misc;
            $stmt = $db->prepare(
                "SELECT misc_id, iteration FROM `authors` 
                    INNER JOIN misc USING (misc_id) 
                    WHERE user LIKE ?"
            );
            $stmt->execute([$user]);
            $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($activity)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No activities found.', 'Keine Aktivitäten gefunden.') . "</td></tr>";
            } else foreach ($activity as $act) {
                // $selected = ($act['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td>
                        <?php $misc->print($act['misc_id']); ?>
                    </td>
                    <td>
                        <?php if ($act['iteration'] == 'once') { ?>
                            <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-calendar-plus"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <form action="" method="post">
                                        <input type="hidden" name="repetition" value="<?= $act['misc_id'] ?>">

                                        <div class="form-group">
                                            <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                                            <input type="date" class="form-control" name="date_start" id="date_start" required>
                                        </div>


                                        <div class="form-group">
                                            <label for="date_end"><?= lang('End', 'Ende') ?></label>
                                            <input type="date" class="form-control" name="date_end" id="date_end">
                                        </div>

                                        <button class="btn"><?= lang('Add date', 'Datum hinzufügen') ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php } else { ?>
                            <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-calendar-pen"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <form action="" method="post">
                                        <input type="hidden" name="end" value="<?= $act['misc_id'] ?>">

                                        <div class="form-group">
                                            <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                                            <input type="date" class="form-control" name="date_start" id="date_start" required value="<?=$misc->dates['start']??''?>">
                                        </div>


                                        <div class="form-group">
                                            <label for="date_end"><?= lang('End', 'Ende') ?></label>
                                            <input type="date" class="form-control" name="date_end" id="date_end" value="<?=$misc->dates['end']??''?>">
                                        </div>

                                        <button class="btn"><?= lang('Update', 'Aktualisieren') ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php } ?>
                        <button class="btn btn-sm text-danger" data-toggle="tooltip" data-title="<?= lang('Remove activity', 'Entferne Aktivität') ?>" onclick="todo()">
                            <i class="fa-regular fa-lg fa-trash-alt"></i>
                        </button>

                    </td>
                </tr>
            <?php } ?>
        </tbody>

    </table>

</div>



<datalist id="scientist-list">
    <?php
    $stmt = $db->prepare("SELECT CONCAT(last_name, ', ', first_name) FROM `users` ORDER BY last_name ASC");
    $stmt->execute();
    $scientist = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($scientist as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>