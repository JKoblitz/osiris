<?php

/**
 * Page to add and edit categories
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /admin/categories/new
 * @link        /admin/categories/<id>
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$form = $form ?? array();

$color = $form['color'] ?? '#000000';
$member = 0;

$id = $form['id'] ?? '';

$formaction = ROOTPATH;
if (!empty($form) && isset($form['_id'])) {
    $formaction .= "/crud/categories/update/" . $form['_id'];
    $btntext = '<i class="ph ph-check"></i> ' . lang("Update", "Aktualisieren");
    $url = ROOTPATH . "/admin/categories/" . $id;
    $title = $name;

    $member = $osiris->activities->count(['type' => $id]);
} else {
    $formaction .= "/crud/categories/create";
    $btntext = '<i class="ph ph-check"></i> ' . lang("Save", "Speichern");
    $url = ROOTPATH . "/admin/categories/*";
    $title = lang('New category', 'Neue Kategorie');
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

<div class="modal" id="unique" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#/" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title"><?= lang('ID must be unique', 'Die ID muss einzigartig sein.') ?></h5>
            <p>
                <?= lang('Each category and each activity type must have a unique ID with which it is linked to an activity.', 'Jede Kategorie und jeder Aktivitätstyp muss eine einzigartige ID haben, mit der er zu einer Aktivität verknüpft wird.') ?>
            </p>
            <p>
                <?= lang('As the ID must be unique, the following previously used IDs and keywords (new) cannot be used as IDs:', 'Da die ID einzigartig sein muss, können folgende bereits verwendete IDs und Schlüsselwörter (new) nicht als ID verwendet werden:') ?>
            </p>
            <ul class="list" id="IDLIST">
                <?php foreach ($osiris->adminCategories->distinct('id') as $k) { ?>
                    <li><?= $k ?></li>
                <?php } ?>
                <li>new</li>
            </ul>
            <div class="text-right mt-20">
                <a href="#/" class="btn primary" role="button"><?= lang('I understand', 'Ich verstehe') ?></a>
            </div>
        </div>
    </div>
</div>



<form action="<?= $formaction ?>" method="post" id="group-form">
    <input type="hidden" class="hidden" name="redirect" value="<?= $url ?>">

    <?php
    $type = $form;
    $t = $id;
    $color = $type['color'] ?? '';
    $member = $osiris->activities->count(['type' => $t]);
    ?>

    <div class="box type" id="type-<?= $t ?>" style="">

        <h4 class="header" style="background-color:<?= $color ?>20; color:<?= $color ?>">
            <?php if (!empty($type)) { ?>
                <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> mr-10"></i>
                <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
                <?php if ($type['disabled'] ?? false) { ?>
                    <span class="badge danger ml-20">DISABLED</span>
                <?php } ?>

            <?php } else { ?>
                <?= lang('New category of activity types', 'Neue Kategorie von Aktivitätstypen') ?>
            <?php } ?>
        </h4>

        <div class="content">
            <input type="hidden" name="values[id]" value="<?= $t ?>">
            <input type="hidden" name="add" value="type">

            <div class="row row-eq-spacing">
                <?php if (isset($type['id'])) { ?>
                    <input type="hidden" name="original_id" value="<?= $type['id'] ?>">
                <?php } ?>

                <div class="col-sm">
                    <label for="icon" class="required">ID</label>
                    <input type="text" class="form-control" name="values[id]" required value="<?= $type['id'] ?? '' ?>" oninput="sanitizeID(this)">
                    <small><a href="#unique"><i class="ph ph-info"></i> <?= lang('Must be unqiue', 'Muss einzigartig sein') ?></a></small>
                </div>
                <div class="col-sm">
                    <label for="icon" class="required element-time"><a href="https://phosphoricons.com/" class="link" target="_blank" rel="noopener noreferrer">Icon</a> </label>
                    <input type="text" class="form-control" name="values[icon]" required value="<?= $type['icon'] ?? 'placeholder' ?>">
                </div>
                <div class="col-sm">
                    <label for="name_de" class="">Color</label>
                    <input type="color" class="form-control" name="values[color]" value="<?= $type['color'] ?? '' ?>">
                </div>
            </div>

            <div class="row row-eq-spacing">
                <div class="col-sm">
                    <label for="name" class="required ">Name (en)</label>
                    <input type="text" class="form-control" name="values[name]" required value="<?= $type['name'] ?? '' ?>">
                </div>
                <div class="col-sm">
                    <label for="name_de" class="">Name (de)</label>
                    <input type="text" class="form-control" name="values[name_de]" value="<?= $type['name_de'] ?? '' ?>">
                </div>
            </div>

            <?php if (!empty($type)) { ?>

                <hr>
                <h5><?= lang('Types', 'Typen') ?>:</h5>
                <ul class="horizontal">
                    <?php
                    $children = $osiris->adminTypes->find(['parent' => $id]);
                    foreach ($children as $subtype) { ?>
                        <li>
                            <a href="<?= ROOTPATH ?>/admin/types/<?= $subtype['id'] ?>">
                                <i class="ph ph-<?= $subtype['icon'] ?? 'placeholder' ?>"></i>
                                <?= lang($subtype['name'], $subtype['name_de'] ?? $subtype['name']) ?>
                            </a>
                        </li>
                    <?php } ?>
                    <li>
                        <a class="btn" href="<?= ROOTPATH ?>/admin/types/new?parent=<?= $id ?>"><i class="ph ph-plus-circle"></i>
                            <?= lang('Add subtype', 'Neuen Typ hinzufügen') ?>
                        </a>
                    </li>
                </ul>
            <?php } ?>

        </div>

    </div>

    <button class="btn success" id="submitBtn"><?= $btntext ?></button>

</form>

<?php if (!empty($form)) { ?>


    <?php if ($member == 0) { ?>
        <div class="alert danger mt-20">
            <form action="<?= ROOTPATH ?>/crud/categories/delete/<?= $id ?>" method="post">
                <input type="hidden" class="hidden" name="redirect" value="<?= ROOTPATH ?>/admin/categories">
                <button class="btn danger"><i class="ph ph-trash"></i> <?= lang('Delete', 'Löschen') ?></button>
                <span class="ml-20"><?= lang('Warning! Cannot be undone.', 'Warnung, kann nicht rückgängig gemacht werden!') ?></span>
            </form>
        </div>
    <?php } else { ?>

        <div class="alert danger mt-20">
            <?= lang("Can\'t delete category: $member activities associated.", "Kann Kategorie nicht löschen: $member Aktivitäten zugeordnet.") ?><br>
            <a href='<?= ROOTPATH ?>/search/activities#{"$and":[{"type":"<?= $id ?>"}]}' target="_blank" class="text-danger">
                <i class="ph ph-search"></i>
                <?= lang('View activities', 'Aktivitäten zeigen') ?>
            </a>

        </div>
    <?php } ?>


<?php } ?>


<script src="<?= ROOTPATH ?>/js/jquery-ui.min.js"></script>
<script src="<?= ROOTPATH ?>/js/admin-categories.js"></script>