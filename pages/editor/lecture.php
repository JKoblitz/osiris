<?php
include_once BASEPATH . "/php/Lecture.php";
?>


<div class="content">

    <h1 class=""><i class="fa-regular fa-keynote text-signal fa-lg mr-10"></i> <?= lang('My lectures', 'Meine Vorträge') ?></h1>

    <div class="box box-primary" id="lecture-form" style="display:none">
        <div class="content">
            <p class="text-muted">
                <?=lang('
                <b>Note</b>: If you repeated a previously held talk, please click on <i class="fa-regular fa-lg fa-calendar-plus"></i> 
                at the respective lecture below to create a repetition.', 
                '<b>Hinweis</b>: Falls du einen zuvorgehaltenen Vortrag wiederholt hast, clicke bitte beim entsprechenden Vortrag auf 
                <i class="fa-regular fa-lg fa-calendar-plus"></i>, um eine Wiederholung hinzuzufügen.')?>
            </p>
            <form action="#" method="post">
                <div class="form-group">
                    <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
                    <input type="text" class="form-control" name="title" id="title" required>
                </div>

                <div class="form-group">
                    <div class="float-right">
                        <?= lang('Presenting author:', 'Präsentierender Autor:') ?> #
                        <input type="number" name="first_authors" value="1" class="form-control form-control-sm w-50 d-inline-block">
                    </div>
                    <label for="author" class="required"><?= lang('Author(s)', 'Autor(en)') ?></label>
                    <div class="author-list">
                        <div class="author author-dsmz">
                            <?= $userClass->name('formal') ?><input type="hidden" name="author[]" value="<?=$userClass->last?>;<?=$userClass->first?>;1">
                            <a onclick="removeAuthor(event, this)">&times;</a>
                        </div>
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                </div>
                
                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label class="required" for="lecture_type"><?= lang('Type of lecture', 'Art des Vortrages') ?></label>
                        <select name="lecture_type" id="lecture_type" class="form-control">
                            <option value="short">short (15-25 min.)</option>
                            <option value="long">long (> 30 min.)</option>
                        </select>
                    </div>
                    <div class="col-sm">
                        <label class="required" for="date_start"><?= lang('Date', 'Datum') ?></label>
                        <input type="date" class="form-control" name="date_start" id="date_start" required>
                    </div>
                </div>

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label for="conference"><?= lang('Conference', 'Konferenz') ?></label>
                        <input type="text" class="form-control" name="conference" id="conference" placeholder="VAAM 2022">
                    </div>
                    <div class="col-sm">
                        <label for="location"><?= lang('Location', 'Ort') ?></label>
                        <input type="text" class="form-control" name="location" id="location" placeholder="online">
                    </div>
                </div>



                <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add lecture', 'Füge Lecture hinzu') ?></button>

            </form>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Lecture') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr id="add-btn-row">
                <td colspan="3">
                    <button class="btn" onclick="$('#add-btn-row').hide();$('#lecture-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
                </td>
            </tr>


            <?php
            $lecture = new Lecture;
            $stmt = $db->prepare(
                "SELECT lecture_id, q_id FROM `authors` 
                    INNER JOIN lecture USING (lecture_id) 
                    WHERE user LIKE ? ORDER BY q_id DESC"
            );
            $stmt->execute([$user]);
            $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($activity)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No lectures found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($activity as $act) {
                $selected = ($act['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $act['q_id']) ?>
                    </td>
                    <td>
                        <?php $lecture->print($act['lecture_id']); ?>
                    </td>
                    <td>
                    <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-regular fa-lg fa-calendar-plus"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-center" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <form action="" method="post">
                                        <input type="hidden" name="repetition" value="<?=$act['lecture_id']?>">
                                       <label class="required" for="date_start"><?= lang('Repeated at', 'Wiederholt am') ?></label>
                                        <input type="date" class="form-control" name="date_start" id="date_start" required>
                                        <button class="btn mt-20"><?=lang('Add repetition', 'Wiederholung hinzufügen')?></button>
                                    </form>
                                </div>
                            </div>
                        </div>

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