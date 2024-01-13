<?php

/**
 * Page to add new groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /groups/new
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$Format = new Document(true);
$form = $form ?? array();

$level = 0;

$formaction = ROOTPATH ;
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "/crud/groups/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/groups/view/" . $form['_id'];
    $title = lang('Edit group: ', 'Gruppe bearbeiten: '). $id;

    $level = $Groups->getLevel($id);
} else {
    $formaction .= "/crud/groups/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/groups/view/*";
    $title = lang('New group', 'Neue Gruppe');
}

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

function sel($index, $value)
{
    return val($index) == $value ? 'selected' : '';
}

?>

<h3 class="title">
    <?= $title ?>
</h3>

<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <fieldset class="alert">
        <legend><?= lang('General', 'Allgemein') ?></legend>
        <div class="row row-eq-spacing mt-0">
            <div class="col-md-2">
                <label for="id" class="required">
                    <?= lang('Acronym', 'Abkürzung') ?>
                </label>
                <input type="text" class="form-control" name="values[id]" id="id" required value="<?= val('id') ?>" maxlength="8">
            </div>


            <div class="col-sm-5">
                <label for="parent">
                    <?= lang('Parent group', 'Übergeordnete Gruppe') ?>
                </label>
                <select class="form-control" name="values[parent]" id="parent" onchange="deptSelect(this.value)">
                    <option value=""><?= lang('!!!Attention: No parent group chosen', '!!! Achtung: Keine übergeordnete Gruppe gewählt') ?></option>
                    <?php foreach ($Groups->groups as $d => $dept) { ?>
                        <option value="<?= $d ?>" <?= sel('parent', $d) ?> data-level="<?= $dept['level'] ?? $Groups->getLevel($d) ?>">
                            <?= $dept['name'] != $d ? "$d: " : '' ?><?= $dept['name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>


            <div class="col-sm-5">
                <label for="unit" class="required">
                    <?= lang('Type of group', 'Art der Gruppe') ?>
                </label>
                <input type="text" class="form-control" name="values[unit]" id="unit" required value="<?= val('unit') ?>" placeholder="<?= lang('Double click to see suggestions', 'Doppelklick für Vorschläge') ?>" list="unit-list">
            </div>

        </div>
        <div class="form-group" id="color-row" <?= $level != 1 ? 'style="display:none;"' : '' ?>>
            <label for="name_de" class=""><?= lang('Color', 'Farbe') ?></label>
            <input type="color" class="form-control w-50" name="type[color]" required value="<?= val('color') ?>">
            <span><?= lang('Note that only level 1 groups can have a color.', 'Bitte beachte, dass nur Level 1-Gruppen eine eigene Farbe haben können.') ?></span>
        </div>
    </fieldset>



    <div class="row row-eq-spacing mb-0">
        <div class="col-md-6">
            <fieldset class="alert">
                <legend><?= lang('German', 'Deutsch') ?></legend>
                <div class="form-group">
                    <label for="name" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (DE)
                    </label>
                    <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>">
                </div>
                <div class="form-group">
                    <label for="description_de"><?= lang('Description', 'Beschreibung') ?> (DE)</label>
                    <textarea name="values[description_de]" id="description_de" cols="30" rows="10" class="form-control"><?= val('description_de') ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset class="alert">
                <legend><?= lang('English', 'Englisch') ?></legend>
                <div class="form-group">
                    <label for="name_en" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (EN)
                    </label>
                    <input type="text" class="form-control" name="values[name_en]" id="name_en" required value="<?= val('name_en') ?>">
                </div>

                <div class="form-group">
                    <label for="description"><?= lang('Description', 'Beschreibung') ?> (EN)</label>
                    <textarea name="values[description]" id="description" cols="30" rows="10" class="form-control"><?= val('description') ?></textarea>
                </div>
            </fieldset>
        </div>
    </div>


    <fieldset class="alert">
        <legend>
            <?= lang('Staff', 'Personal') ?>
        </legend>
        <div class="form-group">
            <label for="head">
                <?= lang('Lead', 'Leitung') ?>
            </label>

            <select class="form-control" id="head" name="values[head][]" autocomplete="off" multiple="multiple">
                <option value=""><?= lang('None', 'Keiner') ?></option>
                <?php
                $head = $form['head'] ?? [$user];
                if (is_string($head)) $head = [$head];
                else $head = DB::doc2Arr($head);

                $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                foreach ($userlist as $j) { ?>
                    <option value="<?= $j['username'] ?>" <?= in_array($j['username'], $head) ? 'selected' : '' ?>><?= $j['last'] ?>, <?= $j['first'] ?></option>
                <?php } ?>
            </select>
            <small class="text-muted">
                <?= lang('Multiple with <kbd>Ctrl</kbd>', 'Mehrere mit <kbd>Strg</kbd>') ?>
            </small>
        </div>
    </fieldset>

    <button class="btn primary" type="submit" id="submit-btn">
        <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
    </button>

    <datalist id="unit-list">
        <?php
        $units = $osiris->groups->distinct('unit');
        foreach ($units as $u) { ?>
            <option><?= $u ?></option>
        <?php } ?>
    </datalist>
</form>


<?php if (!empty($form) && isset($form['_id'])) { ?>


    <div class="alert danger mt-20">
        <form action="<?= ROOTPATH ?>/crud/groups/delete/<?= $group['_id'] ?>" method="post">
            <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH ?>/groups">
            <button class="btn danger"><i class="ph ph-trash"></i> <?= lang('Delete', 'Löschen') ?></button>
            <span class="ml-20"><?= lang('Warning! Cannot be undone.', 'Warnung, kann nicht rückgängig gemacht werden!') ?></span>
        </form>
    </div>
<?php } ?>

<script>
    function deptSelect(val) {
        console.log(val);
        var opt = $('#parent').find('[value=' + val + ']')
        console.log(opt.attr('data-level'));
        if (opt.attr('data-level') != '0') {
            $('#color-row').hide()
        } else {
            $('#color-row').show()
        }
    }
</script>