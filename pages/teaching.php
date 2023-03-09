<?php

$form = $form ?? array();
$copy = $copy ?? false;
$preset = $form['authors'] ?? array(
    [
        'last' => $USER['last'],
        'first' => $USER['first'],
        'aoi' => true,
        'user' => strtolower($USER['username'])
    ]
);

$first = 1;
$last = 1;
if (!empty($form) && $form['type'] == 'publication' && !empty($form['authors'])) {
    if (!is_array($form['authors'])) {
        $form['authors'] = $form['authors']->bsonSerialize();
    }
    if (is_array($form['authors'])) {
        $pos = array_count_values(array_column($form['authors'], 'position'));
        $first = $pos['first'] ?? 1;
        $last = $pos['last'] ?? 1;
    }
}

$authors = "";
foreach ($preset as $a) {
    $authors .= authorForm($a);
}

$preset_editors = $form['editors'] ?? array();
$editors = "";
foreach ($preset_editors as $a) {
    $editors .= authorForm($a, true);
}

$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id']) && !$copy) {
    $formaction .= "update/" . $form['_id'];
    $btntext = '<i class="fas fa-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/activities/view/" . $form['_id'];
} else {
    $formaction .= "create";
    $btntext = '<i class="fas fa-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/activities/view/*";
}

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}
?>
<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>




<div class="modal" id="add-teaching" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a data-dismiss="modal" href="#" class="btn float-right" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>

        </div>
    </div>
</div>


<div class="content">
    <!-- <a target="_blank" href="<?= ROOTPATH ?>/docs/add-activities" class="btn btn-tour float-right ml-5" id="docs-btn">
        <i class="far fa-lg fa-book-sparkles mr-5"></i>
        <?= lang('Read the Docs', 'Zur Hilfeseite') ?>
    </a> -->

    <div class="box box-primary add-form" id="teaching-form">
        <div class="content">


            <form action="<?= $formaction ?>" method="post" enctype="multipart/form-data" id="activity-form">
                <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">
                <input type="hidden" class="form-control disabled" name="values[type]" id="type" value="teaching" readonly>



                <div class="form-group lang-<?= lang('en', 'de') ?>" data-visible="article,preprint,magazine,book,chapter,lecture,poster,dissertation,others,misc-once,misc-annual,students,guests,teaching,software">
                    <label for="title" class="required element-title">
                        <?= lang('Topic / Title / Description', 'Thema / Titel / Beschreibung') ?>
                    </label>

                    <div class="form-group title-editor"><?= $form['title'] ?? '' ?></div>
                    <input type="text" class="form-control hidden" name="values[title]" id="title" required value="<?= val('title') ?>">
                </div>


                <div class="col-sm-3">
                    <label class="required element-author" for="username">
                        <?= lang('Contact person', 'Ansprechpartner:in') ?>
                    </label>
                    <select class="form-control" id="username" name="values[user]" required autocomplete="off">
                        <?php
                        $userlist = $osiris->users->find([], ['sort' => ["last" => 1]]);
                        foreach ($userlist as $j) { ?>
                            <option value="<?= $j['_id'] ?>" <?= $j['_id'] == ($form['user'] ?? $user) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                        <?php } ?>
                    </select>
                </div>



                <button class="btn btn-primary" type="submit" id="submit-btn" onclick="verifyForm(event, '#activity-form')"><?= $btntext ?></button>

            </form>
        </div>
    </div>

</div>


<datalist id="scientist-list">
    <?php
    foreach ($osiris->users->distinct('formalname') as $s) { ?>
        <option><?= $s ?></option>
    <?php } ?>
</datalist>