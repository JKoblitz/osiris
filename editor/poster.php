<?php
include_once BASEPATH . "/php/Poster.php";
?>


<div class="content">

    <h3 class=""><?= lang('My poster', 'Meine Poster') ?></h3>

    <div class="box box-primary" id="poster-form" style="display:none">
        <div class="content">
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
                        <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
                    </div>
                </div>

                <div class="form-row row-eq-spacing">
                    <div class="col-sm">
                        <label class="required" for="date_start"><?= lang('Start', 'Anfang') ?></label>
                        <input type="date" class="form-control" name="date_start" id="date_start" required>
                    </div>
                    <div class="col-sm">
                        <label for="date_end"><?= lang('End (leave empty if event was only one day)', 'Ende (leer lassen falls nur ein Tag)') ?></label>
                        <input type="date" class="form-control" name="date_end" id="date_end">
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


                <button class="btn btn-primary" type="submit"><i class="fas fa-plus"></i> <?= lang('Add poster', 'Füge Poster hinzu') ?></button>

            </form>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <td><?= lang('Quarter', 'Quartal') ?></td>
                <td><?= lang('Poster') ?></td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            <tr id="add-btn-row">
                <td colspan="3">
                    <button class="btn" onclick="$('#add-btn-row').hide();$('#poster-form').slideToggle() "><i class="fas fa-plus"></i> <?= lang('Add activity', 'Füge Aktivität hinzu') ?></button>
                </td>
            </tr>


            <?php
            $poster = new Poster;
            $stmt = $db->prepare(
                "SELECT poster_id, q_id FROM `authors` 
                    INNER JOIN poster USING (poster_id) 
                    WHERE user LIKE ? ORDER BY q_id DESC"
            );
            $stmt->execute([$user]);
            $activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($activity)) {
                echo "<tr class='row-danger'><td colspan='3'>" . lang('No posters found.', 'Keine Publikationen gefunden.') . "</td></tr>";
            } else foreach ($activity as $act) {
                $selected = ($act['q_id'] == SELECTEDQUARTER);
            ?>
                <tr class="<?= !$selected ? 'row-muted' : '' ?>">
                    <td class="quarter">
                        <?= str_replace('Q', ' Q', $act['q_id']) ?>
                    </td>
                    <td>
                        <?php $poster->print($act['poster_id']); ?>
                    </td>
                    <td>
                        <button class="btn btn-sm text-danger ml-20" data-toggle="tooltip" data-title="<?= lang('Remove activity', 'Entferne Aktivität') ?>" onclick="todo()">
                            <i class="fa-regular fa-trash-alt"></i>
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