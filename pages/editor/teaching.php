<?php
include_once BASEPATH . "/php/Teaching.php";
?>


<div class="content">

    <h1 class=""><i class="fa-regular fa-people text-muted fa-lg mr-10"></i> <?= lang('Teaching &amp; Guests', 'Lehre &amp; Gäste') ?></h1>

    <div class="box box-primary" id="teaching-form" style="display:none">
        <form action="#" method="post">
            <div class="content">
                <h6>
                    <?= lang('Add one or more guests/students', 'Füge einen oder mehrere Gäste/Studenten hinzu') ?>:
                </h6>
                <table class="table table-simple mt-0 table-aligned">
                    <thead>
                        <tr>
                            <td><?= lang('Name (last name, given name)', 'Name (Nachname, Vorname)') ?></td>
                            <td><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, Land)') ?></td>
                            <td><?= lang('Academ. title', 'Akadem. Titel') ?></td>
                        </tr>
                    </thead>
                    <tbody id="guest-list">
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="guest[name][]">
                            </td>
                            <td>
                                <input type="text" class="form-control" name="guest[institution][]">
                            </td>
                            <td class="w-150">
                                <input type="text" class="form-control" name="guest[academic_title][]">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn" type="button" onclick="addGuestList()"><i class="fas fa-user-plus"></i> <?= lang('Add guest', 'Gast hinzufügen') ?></button>
            </div>
            <hr>

            <div class="content">
                <h6><?= lang('Enter details about the stay:', 'Füge Details über den Aufenthalt hinzu:') ?></h6>
                <div class="form-row row-eq-spacing">
                    <div class="col-sm-6">
                        <label for="title" class="required">
                            Titel des Programms/der Arbeit bzw. Grund des Aufenthalts
                        </label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="col-sm">
                        <label for="category" class="required"><?= lang('Category', 'Kategorie') ?></label>
                        <select name="category" id="category" class="form-control" required>
                            <option disabled>--- Teaching ---</option>
                            <option>Doktorand:in</option>
                            <option>Master-Thesis</option>
                            <option>Bachelor-Thesis</option>
                            <option disabled>--- Guests ---</option>
                            <option>Gastwissenschaftler</option>
                            <option>Pflichtpraktikum im Rahmen des Studium</option>
                            <option>Vorlesung und Laborpraktikum</option>
                            <option>Schülerpraktikum</option>
                        </select>
                        <!-- <input type="text" class="form-control" name="category" id="category" placeholder="Gastwissenschaftler"> -->
                    </div>
                    <div class="col-sm">
                        <label for="details">Details (Stipendium, etc.)</label>
                        <input type="text" class="form-control" name="details" id="details">
                    </div>
                </div>



                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                        <input type="date" class="form-control" name="date_start" id="date_start" required>
                    </div>
                    <div class="col-sm">
                        <label for="date_end"><?= lang('End', 'Ende') ?></label>
                        <input type="date" class="form-control" name="date_end" id="date_end">

                        <div id="end-question" style="display: none;">
                            <div class="custom-radio d-inline-block">
                                <input type="radio" name="status" id="status-completed" value="completed" checked="checked">
                                <label for="status-completed"><?= lang('Completed', 'Abgeschlossen') ?></label>
                            </div>

                            <div class="custom-radio d-inline-block">
                                <input type="radio" name="status" id="status-aborted" value="aborted">
                                <label for="status-aborted"><?= lang('Aborted', 'Abgebrochen') ?></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label for="dept"><?= lang('Department', 'Abteilung') ?></label>
                        <input type="text" class="form-control" name="dept" id="dept" placeholder="MuTZ">
                    </div>
                </div> -->
                <label for="author" class="required"><?= lang('Responsible scientist', 'Verantwortliche Person') ?></label>
                <div class="author-list">
                    <div class="author">
                        <?= $userClass->name('formal') ?><input type="hidden" name="author[]" value="<?= $userClass->last ?>;<?= $userClass->first ?>;1">
                        <a onclick="removeAuthor(event, this)">&times;</a>
                    </div>
                    <input type="text" placeholder="Add responsible person ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                </div>


                <button class="btn btn-primary mt-20" type="submit"><i class="fas fa-plus"></i> <?= lang('Add stay', 'Füge Aufenthalt hinzu') ?></button>

            </div>
        </form>
    </div>


    <button class="btn btn-link" onclick="$('#add-btn-row').hide();$('#teaching-form').slideDown() " id="add-btn-row">
        <i class="fas fa-plus"></i> <?= lang('Add stay', 'Füge Aufenthalt hinzu') ?>
    </button>

    <table class="table">
        <thead>
            <tr>
                <!-- <td>Zuname, Vorname</td>
                <td>Einrichtung (Name, Ort, Land)</td>
                <td>Kategorie</td>
                <td>Titel des Programms/der Arbeit bzw. Grund des Aufenthalts</td>
                <td>Zeit des Aufenthalts</td>
                <td>Verantw. Wissenschaftler</td>
                <td>Abteilung(en)</td> -->
            </tr>
        </thead>
        <tbody>


            <?php
            // $stmt = $db->prepare(
            //     "SELECT teaching.*, 
            //         GROUP_CONCAT(CONCAT(a.last_name, ', ', a.first_name) SEPARATOR ';') AS authors, 
            //         GROUP_CONCAT(DISTINCT dept) AS depts
            //     FROM `authors` a1
            //     INNER JOIN teaching USING (teaching_id) 
            //     LEFT JOIN authors a USING (teaching_id)
            //     LEFT JOIN users ON a.user = users.user
            //     -- LEFT JOIN quarter USING (q_id)
            //     WHERE a1.`user` LIKE ? 
            //     GROUP BY teaching_id
            //     ORDER BY date_start
            //     -- AND quarter.year = ? 
            //     "
            // ); 
            $stmt = $db->prepare(
                "SELECT teaching_id
                FROM `authors`
                INNER JOIN teaching USING (teaching_id) 
                WHERE `user` LIKE ? 
                ORDER BY date_start
                "
            );
            $stmt->execute([$user]);
            $activity = $stmt->fetchAll(PDO::FETCH_COLUMN);
            if (empty($activity)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No teachings found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($activity as $id) {

                $teaching = new Teaching($id);
                $selected = $teaching->inSelectedQuarter();

                $teaching->printMsg();
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td><?= $teaching->print() ?></td>
                    <td class="unbreakable">
                        <div class="dropdown">
                            <button class="btn btn-sm text-primary" data-toggle="dropdown" type="button" id="dropdown-1" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-regular fa-lg fa-calendar-pen"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-1">
                                <div class="content">
                                    <form action="" method="post">
                                        <input type="hidden" name="repetition" value="<?= $id ?>">
                                        <label class="required" for="date_end"><?= lang('Ended at / Extend until', 'Geendet am / Verlängern bis') ?>:</label>
                                        <input type="date" class="form-control" name="date_end" id="date_end" value="<?=$teaching->data['date_end']?>" required>
<br>
                                        <div>
                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-in-progress-<?= $id ?>" value="in-progress" checked="checked">
                                                <label for="status-in-progress-<?= $id ?>"><?= lang('In progress', 'In Arbeit') ?></label>
                                            </div>

                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-completed-<?= $id ?>" value="completed" checked="checked">
                                                <label for="status-completed-<?= $id ?>"><?= lang('Completed', 'Abgeschlossen') ?></label>
                                            </div>

                                            <div class="custom-radio mb-5">
                                                <input type="radio" name="status" id="status-aborted-<?= $id ?>" value="aborted">
                                                <label for="status-aborted-<?= $id ?>"><?= lang('Aborted', 'Abgebrochen') ?></label>
                                            </div>
                                        </div>
                                        <button class="btn mt-20" type="button" onclick="todo()"><?= lang('Submit', 'Bestätigen') ?></button>
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


<script>
    function addGuestList() {
        $('#guest-list').append(`
    <tr>
        <td>
            <input type="text" class="form-control" name="guest[name][]">
        </td>
        <td>
            <input type="text" class="form-control" name="guest[institution][]">
        </td>
        <td class="w-150">
            <input type="text" class="form-control" name="guest[academic_title][]">
        </td>
    </tr>
    `)
    }

    // toggle the end question for thesis that has ended
    function endQuestion() {
        const date = $('#date_end').val()
        if (date.length === 0) {
            $('#end-question').hide()
            return;
        }
        const selecteddate = new Date(date);
        const today = new Date();
        var thesis = $('#category').val()
        thesis = thesis.includes('Thesis') || thesis == "Doktorand:in"
        if (selecteddate.setHours(0, 0, 0, 0) <= today.setHours(0, 0, 0, 0) && thesis) {
            // date is in the past
            $('#end-question').show()
        } else {
            $('#end-question').hide()
        }
    }
    $('#date_end').on('change', endQuestion)
    $('#category').on('change', endQuestion)
</script>