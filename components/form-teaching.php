<?php

// can be prefilled with 
// $form = $osiris->lectures->findOne(['_id' => 3]);

$form = $form ?? array();
$preset = $form['authors'] ?? array($USER);
$authors = "";
foreach ($preset as $a) {
    $authors .= authorForm($a);
}

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "update/teaching/" . $form['_id'];
    $btntext = '<i class="fas fa-check"></i> ' . lang("Update", "Aktualisieren");
} else {
    $formaction .= "create/teaching";
    $btntext = '<i class="fas fa-plus"></i> ' . lang("Add", "Hinzufügen");
}
?>

<form action="<?= $formaction ?>" method="post">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">
    <div class="content">
        <h6>
            <?= lang('Add a guest/student', 'Füge einen Gast/Studierenden hinzu') ?>:
        </h6>
        <div class="form-row row-eq-spacing">
            <div class="col-sm-5">
                <label for="guest-name" class="required"><?= lang('Name (last name, given name)', 'Name (Nachname, Vorname)') ?></label>
                <input type="text" class="form-control" name="values[name]" id="guest-name" required value="<?=$form['name'] ?? ''?>">
            </div>
            <div class="col-sm-5">
                <label for="guest-affiliation" class="required"><?= lang('Affiliation (Name, City, Country)', 'Einrichtung (Name, Ort, Land)') ?></label>
                <input type="text" class="form-control" name="values[affiliation]" id="guest-affiliation" required value="<?=$form['affiliation'] ?? ''?>">
            </div>
            <div class="col-sm-2">
                <label for="guest-academic_title"><?= lang('Academ. title', 'Akadem. Titel') ?></label>
                <input type="text" class="form-control" name="values[academic_title]" id="guest-academic_title" value="<?=$form['academic_title'] ?? ''?>">
            </div>
        </div>
    </div>
    <hr>

    <div class="content">
        <h6><?= lang('Enter details about the stay:', 'Füge Details über den Aufenthalt hinzu:') ?></h6>
        <div class="form-row row-eq-spacing">
            <div class="col-sm-6">
                <label for="title" class="required">
                    Titel des Programms/der Arbeit bzw. Grund des Aufenthalts
                </label>
                <input type="text" class="form-control" name="values[title]" id="title" required value="<?=$form['title'] ?? ''?>">
            </div>
            <div class="col-sm">
                <label for="category" class="required"><?= lang('Category', 'Kategorie') ?></label>
                <select name="values[category]" id="category" class="form-control" required onchange="endQuestion()">
                    <option disabled>--- <?=lang('Thesis', 'Abschlussarbeiten')?> ---</option>
                    <option <?=$form['category']??'' == 'Doktorand:in' ? 'selected': ''?>>Doktorand:in</option>
                    <option <?=$form['category']??'' == 'Master-Thesis' ? 'selected': ''?>>Master-Thesis</option>
                    <option <?=$form['category']??'' == 'Bachelor-Thesis' ? 'selected': ''?>>Bachelor-Thesis</option>
                    <option disabled>--- <?=lang('Guests', 'Gäste')?> ---</option>
                    <option <?=$form['category']??'' == 'Gastwissenschaftler:in' ? 'selected': ''?>>Gastwissenschaftler:in</option>
                    <option <?=$form['category']??'' == 'Pflichtpraktikum im Rahmen des Studium' ? 'selected': ''?>>Pflichtpraktikum im Rahmen des Studium</option>
                    <option <?=$form['category']??'' == 'Vorlesung und Laborpraktikum' ? 'selected': ''?>>Vorlesung und Laborpraktikum</option>
                    <option <?=$form['category']??'' == 'Schülerpraktikum' ? 'selected': ''?>>Schülerpraktikum</option>
                </select>
            </div>
            <div class="col-sm">
                <label for="details">Details (Stipendium, etc.)</label>
                <input type="text" class="form-control" name="values[details]" id="details" value="<?=$form['details'] ?? ''?>">
            </div>
        </div>



        <div class="form-row row-eq-spacing">
            <div class="col-sm">
                <label class="required" for="start"><?= lang('Start', 'Anfang') ?></label>
                <input type="date" class="form-control" name="values[start]" id="date_start" required value="<?=valueFromDateArray($form['start'] ?? '')?>">
            </div>
            <div class="col-sm">
                <label for="end" class="required"><?= lang('End', 'Ende') ?></label>
                <input type="date" class="form-control" name="values[end]" id="date_end" onchange="endQuestion()" value="<?=valueFromDateArray($form['end'] ?? '')?>" required>

                <div id="end-question" style="display: none;">
                <div class="custom-radio d-none">
                        <input type="radio" name="values[status]" id="status-in-progress" value="in-progress" checked="checked" value="<?=$form['status'] ?? ''?>">
                        <label for="status-in-progress"><?= lang('Completed', 'Abgeschlossen') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block">
                        <input type="radio" name="values[status]" id="status-completed" value="completed" value="<?=$form['status'] ?? ''?>">
                        <label for="status-completed"><?= lang('Completed', 'Abgeschlossen') ?></label>
                    </div>

                    <div class="custom-radio d-inline-block">
                        <input type="radio" name="values[status]" id="status-aborted" value="aborted" value="<?=$form['status'] ?? ''?>">
                        <label for="status-aborted"><?= lang('Aborted', 'Abgebrochen') ?></label>
                    </div>
                </div>
            </div>
        </div>

        <label for="author" class="required"><?= lang('Responsible scientist', 'Verantwortliche Person') ?></label>
        <div class="author-list">
            <?= $authors ?>
            <input type="text" placeholder="Add responsible person ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
        </div>


        <button class="btn btn-primary mt-20" type="submit"><i class="fas fa-plus"></i> <?= lang('Add stay', 'Füge Aufenthalt hinzu') ?></button>

    </div>
    
<datalist id="scientist-list">
    <?php
    $scientist = $osiris->users->find();
    foreach ($scientist as $s) { ?>
        <option><?= $s['last_name'] ?>, <?= $s['first_name'] ?></option>
    <?php } ?>
</datalist>


</form>
<script>
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
</script>