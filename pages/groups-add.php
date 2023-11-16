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


$formaction = ROOTPATH . "/";
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "groups/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/groups/view/" . $form['_id'];
} else {
    $formaction .= "groups/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/groups/view/*";
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
<script src="<?= ROOTPATH ?>/js/quill.min.js"></script>

<style>
</style>

<h3 class="title">
    <?= lang('Add new group', 'Neue Gruppe') ?>
</h3>

<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <div class="row row-eq-spacing">
        <div class="col-md-2">
            <label for="id" class="required element-other">
                <?= lang('Acronym', 'Abkürzung') ?>
            </label>
            <input type="text" class="form-control" name="values[id]" id="id" required value="<?= val('id') ?>" maxlength="8">
        </div>

        <div class="col-sm-5">
            <label for="name" class="required element-other">
                <?= lang('Full Name', 'Voller Name') ?> (DE)
            </label>
            <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>">
        </div>
        <div class="col-sm-5">
            <label for="name_en" class="required element-other">
                <?= lang('Full Name', 'Voller Name') ?> (EN)
            </label>
            <input type="text" class="form-control" name="values[name_en]" id="name_en" required value="<?= val('name_en') ?>">
        </div>
    </div>


    <div class="row row-eq-spacing">
        <div class="col-sm-2">
            <label for="name_de" class=""><?= lang('Color', 'Farbe') ?></label>
            <input type="color" class="form-control" name="type[color]" required>
            <span><?= lang('Note that if the parent has a color other than black, it will be overwritten', 'Bitte beachte, dass dieser Wert überschrieben wird, falls die übergeordnete Gruppe eine andere Farbe als schwarz hat.') ?></span>
        </div>

        <div class="col-sm-5">
            <label for="parent">
                <?= lang('Parent group', 'Übergeordnete Gruppe') ?>
            </label>
            <select class="form-control" name="values[parent]" id="parent">
                <option value=""><?= lang('!!!Attention: No parent group chosen', '!!! Achtung: Keine übergeordnete Gruppe gewählt') ?></option>
                <?php foreach ($Groups->groups as $d => $dept) { ?>
                    <option value="<?= $d ?>" <?= sel('parent', $d) ?>><?= $dept['name'] != $d ? "$d: " : '' ?><?= $dept['name'] ?></option>
                <?php } ?>
            </select>
        </div>


        <div class="col-sm-5">
            <label for="unit" class="required element-other">
                <?= lang('Type of group', 'Art der Gruppe') ?>
            </label>
            <input type="text" class="form-control" name="values[unit]" id="unit" required value="<?= val('unit') ?>" placeholder="<?=lang('Double click to see suggestions', 'Doppelklick für Vorschläge')?>" list="unit-list">
        </div>
    </div>


    <datalist id="unit-list">
        <?php
        $units = $osiris->groups->distinct('unit');
        foreach ($units as $u) { ?>
            <option><?= $u ?></option>
        <?php } ?>
    </datalist>

    <button class="btn primary" type="submit" id="submit-btn">
        <i class="ph ph-check"></i> <?= lang("Save", "Speichern") ?>
    </button>

</form>