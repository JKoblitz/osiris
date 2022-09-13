<?php

// can be prefilled with 
// $form = $osiris->miscs->findOne(['_id' => 3]);

$form = $form ?? array();
$preset = $form['authors'] ?? array($USER);
$authors = "";
foreach ($preset as $a) {
    $authors .= authorForm($a);
}

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "update/misc/" . $form['_id'];
    $btntext = '<i class="fas fa-check"></i> ' . lang("Update", "Aktualisieren");
} else {
    $formaction .= "create/misc";
    $btntext = '<i class="fas fa-plus"></i> ' . lang("Add", "Hinzufügen");
}
?>

<form action="<?= $formaction ?>" method="post">
    <!-- <input type="hidden" name="values[id]" value="<?= $form['_id'] ?? '' ?>"> -->
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?? $_SERVER['REDIRECT_URL'] ?? $_SERVER['REQUEST_URI'] ?>">

    <div class="form-group">
        <label for="title" class="required"><?= lang('Title', 'Titel') ?></label>
        <div class="form-group title-editor"><?= $form['title'] ?? '' ?></div>
        <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= $form['title'] ?? '' ?>">
    </div>

    <div class="form-group">
        <!-- <div class="float-right">
            <?= lang('Presenting author:', 'Präsentierender Autor:') ?> #
            <input type="number" name="values[first_authors]" value="1" class="form-control form-control-sm w-50 d-inline-block">
        </div> -->
        <label for="author" class="required"><?= lang('Author(s)', 'Autor(en)') ?></label>
        <div class="author-list">
            <?= $authors ?>
            <input type="text" placeholder="Add author ..." onkeypress="addAuthor(event, this);" id="add-author" list="scientist-list">
        </div>
    </div>
    <div class="alert alert-signal mb-20 affiliation-warning" style="display: none;">
        <h5 class="title">
            <i class="fas fa-exclamation-circle"></i>
            <?= lang('Attention: No ' . AFFILIATION . " authors added.", 'Achtung: Keine ' . AFFILIATION . '-Autoren angegeben.') ?>
        </h5>
        <?= lang(
            'Please click on every ' . AFFILIATION . ' author in the list above, to mark them as affiliated. Only affiliated authors will receive points and are shown in reports.',
            'Bitte klicken Sie auf jeden ' . AFFILIATION . '-Autor in der Liste oben, um ihn als zugehörig zu markieren. Nur zugehörige Autoren erhalten Punkte und werden in Berichten berücksichtigt.'
        ) ?>
    </div>

    <div class="form-row row-eq-spacing">
        <div class="col-sm">
            <label class="required" for="iteration"><?= lang('Iteration', 'Häufigkeit') ?></label>
            <select name="values[iteration]" id="iteration" class="form-control" value="<?= $form['iteration'] ?? '' ?>">
                <option value="once">once</option>
                <option value="annual">annual</option>
            </select>
        </div>
        <div class="col-sm">
            <label for="location"><?= lang('Location', 'Ort') ?></label>
            <input type="text" class="form-control" name="values[location]" id="location" placeholder="online" value="<?= $form['location'] ?? '' ?>">
        </div>
    </div>

    <div class="form-row row-eq-spacing">
        <div class="col-sm">
            <label class="required" for="start"><?= lang('Start', 'Anfang') ?></label>
            <input type="date" class="form-control" name="values[dates][0][start]" id="start" required value="<?= valueFromDateArray($form['start'] ?? '') ?>">

            <span class="text-muted">
                <?= lang('You can add more dates later.', 'Du kannst später weitere Daten hinzufügen.') ?>
            </span>
        </div>
        <div class="col-sm">
            <label for="end"><?= lang('End (leave empty if event was only one day)', 'Ende (leer lassen falls nur ein Tag)') ?></label>
            <input type="date" class="form-control" name="values[dates][0][end]" id="end" value="<?= valueFromDateArray($form['end'] ?? '') ?>">
            <span class="text-muted">
                <?= lang(
                    'Only needed if one-time event is more than one day or if a continous work ended.',
                    'Nur benötigt falls ein einmaliges Event mehr als einen Tag geht oder ein kontinuierliches Event endet.'
                ) ?>
            </span>
        </div>
    </div>



    <button class="btn btn-primary" type="submit"><?= $btntext ?></button>


    <datalist id="scientist-list">
        <?php
        $scientist = $osiris->users->find();
        foreach ($scientist as $s) { ?>
            <option><?= $s['last'] ?>, <?= $s['first'] ?></option>
        <?php } ?>
    </datalist>
</form>