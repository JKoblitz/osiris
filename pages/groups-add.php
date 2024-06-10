<?php

/**
 * Page to add new groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /groups/new
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

if (!$Settings->hasPermission('guests.add') && (!isset($form['head']) || $form['head'] !== $_SESSION['username'])) {
    echo "You have no right to be here.";
    die;
}

$Format = new Document(true);
$form = $form ?? array();

$level = 0;

$formaction = ROOTPATH;
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "/crud/groups/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/groups/view/" . $form['_id'];
    $title = lang('Edit group: ', 'Gruppe bearbeiten: ') . $id;

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

    <fieldset>
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
            <label for="color" class=""><?= lang('Color', 'Farbe') ?></label>
            <input type="color" class="form-control w-50" name="type[color]" required value="<?= val('color') ?>">
            <span><?= lang('Note that only level 1 groups can have a color.', 'Bitte beachte, dass nur Level 1-Gruppen eine eigene Farbe haben können.') ?></span>
        </div>
    </fieldset>



    <div class="row row-eq-spacing mb-0">
        <div class="col-md-6">
            <fieldset>
                <legend><?= lang('German', 'Deutsch') ?></legend>
                <div class="form-group">
                    <label for="name_de" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (DE)
                    </label>
                    <input type="text" class="form-control" name="values[name_de]" id="name_de" required value="<?= val('name_de') ?>">
                </div>
                <div class="form-group">
                    <label for="description_de"><?= lang('Description', 'Beschreibung') ?> (DE)</label>
                    <textarea name="values[description_de]" id="description_de" cols="30" rows="10" class="form-control"><?= val('description_de') ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col-md-6">
            <fieldset>
                <legend><?= lang('English', 'Englisch') ?></legend>
                <div class="form-group">
                    <label for="name" class="required">
                        <?= lang('Full Name', 'Voller Name') ?> (EN)
                    </label>
                    <input type="text" class="form-control" name="values[name]" id="name" required value="<?= val('name') ?>">
                </div>

                <div class="form-group">
                    <label for="description"><?= lang('Description', 'Beschreibung') ?> (EN)</label>
                    <textarea name="values[description]" id="description" cols="30" rows="10" class="form-control"><?= val('description') ?></textarea>
                </div>
            </fieldset>
        </div>
    </div>


    <fieldset>
        <legend>
            <?= lang('Staff', 'Personal') ?>
        </legend>
        <div class="form-group">
            <label for="head">
                <?= lang('Lead', 'Leitung') ?>
            </label>
            <div class="author-widget">
                <div class="author-list p-10">
                    <?php
                    $heads = $form['head'] ?? [];
                    if (is_string($heads)) $heads = [$heads];
                    else $heads = DB::doc2Arr($heads);

                    foreach ($heads as $h) {
                        $person = $osiris->persons->findOne(['username' => $h]);
                        if (empty($person)) continue;
                        $name = $person['last'] . ', ' . $person['first'];
                    ?>
                        <div class='author'>
                            <?= $name ?>
                            <input type='hidden' name='values[head][]' value='<?= $h ?>'>
                            <a onclick='$(this).parent().remove()'>&times;</a>
                        </div>
                    <?php } ?>

                </div>
                <div class="footer">
                    <div class="input-group sm d-inline-flex w-auto">
                        <select class="head-input form-control">
                            <option value="" disabled selected><?= lang('Add head ...', 'Füge leitende Person hinzu ...') ?></option>
                            <?php
                            $userlist = $osiris->persons->find(['username' => ['$ne' => null]], ['sort' => ["last" => 1]]);
                            foreach ($userlist as $j) {
                                if (in_array($j['username'], $heads) || empty($j['last'])) continue;
                            ?>
                                <option value="<?= $j['username'] ?>"><?= $j['last'] ?>, <?= $j['first'] ?></option>
                            <?php } ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn primary h-full" type="button" onclick="addHead();">
                                <i class="ph ph-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function addHead(t, st) {
                        var sel = $(`.author-widget .head-input`);
                        var val = sel.val();
                        if (val == null) return;
                        var text = sel.find(`option[value='${val}']`).text();
                        var el = `<div class='author'>${text}<input type='hidden' name='values[head][]' value='${val}'><a onclick='$(this).parent().remove()'>&times;</a></div>`;
                        $(`.author-list`).append(el);
                    }
                </script>
            </div>
        </div>
    </fieldset>


    <fieldset>
        <legend id="research">
            <?= lang('Research interest', 'Forschungsinteressen') ?>
        </legend>
        <div id="research-list">
            <?php
            if (isset($form['research']) && !empty($form['research'])) {

                foreach ($form['research'] as $i => $con) { ?>

                    <div class="alert mb-10">

                        <div class="form-group my-10">
                            <input name="values[research][<?= $i ?>][title]" type="text" class="form-control" value="<?= htmlspecialchars($con['title'] ?? '') ?>" placeholder="Title" required>
                        </div>
                        <div class="form-group mb-0">
                            <textarea name="values[research][<?= $i ?>][info]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)" required><?= htmlspecialchars($con['info'] ?? '') ?></textarea>
                        </div>

                        <button class="btn danger small my-10" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
                    </div>
            <?php }
            } ?>

        </div>
        <button class="btn" type="button" onclick="addResearchrow(event, '#research-list')"><i class="ph ph-plus text-success"></i> <?= lang('Add entry', 'Eintrag hinzufügen') ?></button>


        <script>
            var i = <?= $i ?? 0 ?>

            var CURRENTYEAR = <?= CURRENTYEAR ?>;

            function addResearchrow(evt, parent) {
                i++;
                var el = `
            <div class="alert mb-10">
                    
                    <div class="form-group mb-10">
                        <input name="values[research][${i}][title]" type="text" class="form-control" placeholder="title *" required>
                    </div>

                    <div class="form-group mb-0">
                        <textarea name="values[research][${i}][info]" id="" cols="30" rows="5" class="form-control" value="" placeholder="Information (Markdown support)" required></textarea>
                       </div>

                    <button class="btn danger small my-10" type="button" onclick="$(this).closest('.alert').remove()"><i class="ph ph-trash"></i></button>
                </div>
                `;
                $(parent).append(el);
            }
        </script>

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