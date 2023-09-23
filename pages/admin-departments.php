<?php

/**
 * Page for admin dashboard to define departments
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /admin/departments
 *
 * @package OSIRIS
 * @since 1.1.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

$departments = $Settings->getDepartments();


if (isset($_GET['type']) && isset($_GET['type']['id'])) {
    $dept = $_GET['type'];
    $id = $dept['id'];
    $departments[$id] = [
        "id" => $id,
        "color" => $dept['color'] ?? '#000000',
        "name" => $dept['name'],
        "new" => true
    ];
}

?>
<style>
    form>.box {
        border-left-width: 4px;
    }
</style>

<div class="modal" id="add-type" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <a href="#close-modal" class="close" role="button" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </a>
            <h5 class="title">
                <?= lang('Add department', 'Abteilung hinzufügen') ?>
            </h5>

            <form action="#" method="get">
                <div class="row row-eq-spacing">

                    <div class="col-sm-3">
                        <label for="id" class="required element-time">ID (<?= lang('Abbreviation', 'Abkürzung') ?>)</label>
                        <input type="text" class="form-control" name="type[id]" required>
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class=""><?= lang('Color', 'Farbe') ?></label>
                        <input type="color" class="form-control" name="type[color]" required>
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name</label>
                        <input type="text" class="form-control" name="type[name]" required>
                    </div>
                </div>
                <button class="btn"><?= lang('Save', 'Speichern') ?></button>
            </form>

            <div class="text-right mt-20">
                <a href="#close-modal" class="btn mr-5" role="button"><?= lang('Close', 'Schließen') ?></a>
            </div>
        </div>
    </div>
</div>


<div class="pills">
    <a href="<?= ROOTPATH ?>/admin/general" class="btn"><?= lang('General', 'Allgemein') ?></a>
    <a href="<?= ROOTPATH ?>/admin/departments" class="btn active"><?= lang('Departments', 'Abteilungen') ?></a>
    <a href="<?= ROOTPATH ?>/admin/activities" class="btn"><?= lang('Activities', 'Aktivitäten') ?></a>
</div>

<form action="#" method="post" id="modules-form">
    <?php foreach ($departments as $t => $dept) {
        $member = $osiris->persons->count(['dept' => $t]);
        $color = $dept['color'] ?? '';
    ?>

        <div class="box type" id="type-<?= $t ?>" style="border-color:<?= $color ?>; <?=isset($dept['new']) ?'opacity:.8;font-style:italic;':''?>">
            <h2 class="header" style="background-color:<?= $color ?>20">
                <?= $dept['id'] ?>: <?= $dept['name'] ?>
                <a class="btn link px-5 text-primary ml-auto" onclick="moveElementUp('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one up.', 'Bewege einen nach oben.') ?>"><i class="ph ph-arrow-line-up"></i></a>
                <a class="btn link px-5 text-primary" onclick="moveElementDown('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Move one down.', 'Bewege einen nach unten.') ?>"><i class="ph ph-arrow-line-down"></i></a>
                <?php if ($member == 0) { ?>
                    <a class="btn link px-5 ml-20 text-danger " onclick="deleteElement('type-<?= $t ?>')" data-toggle="tooltip" data-title="<?= lang('Delete element.', 'Lösche Element.') ?>"><i class="ph ph-trash"></i></a>
                <?php } else { ?>
                    <a class="btn link px-5 ml-20 text-muted " href='<?= ROOTPATH ?>/search/user#{"$and":[{"dept":"<?= $t ?>"}]}' target="_blank" data-toggle="tooltip" data-title="<?= lang("Can\'t delete department: $member users associated.", "Kann Abt. nicht löschen: $member Nutzer zugeordnet.") ?>"><i class="ph ph-trash"></i></a>
                <?php } ?>
            </h2>

            <div class="content">
                <!-- <input type="hidden" name="add" value="type"> -->

                <div class="row row-eq-spacing">
                    <div class="col-sm-2">
                        <label for="icon" class="required">ID</label>
                        <input type="text" class="form-control disabled" name="departments[<?= $t ?>][id]" required value="<?= $dept['id'] ?>" readonly>
                    </div>
                    <div class="col-sm-2">
                        <label for="name_de" class="">Color</label>
                        <input type="color" class="form-control" name="departments[<?= $t ?>][color]" value="<?= $dept['color'] ?? '' ?>">
                    </div>
                    <div class="col-sm">
                        <label for="name" class="required ">Name</label>
                        <input type="text" class="form-control" name="departments[<?= $t ?>][name]" required value="<?= $dept['name'] ?? '' ?>">
                    </div>
                </div>


            </div>

        </div>

    <?php } ?>


    <a class="btn osiris" href="#add-type"><i class="ph ph-plus-circle"></i>
        <?= lang('Add department', 'Neue Abteilung hinzufügen') ?>
    </a>

    <button class="btn osiris">
        <i class="ph ph-floppy-disk"></i>
        Save
    </button>

</form>


<script>
    function deleteElement(selector) {
        const el = $('#' + selector)
        el.remove()
    }

    function moveElementUp(selector) {
        const el = $('#' + selector)
        el.insertBefore(el.prev());
    }

    function moveElementDown(selector) {
        const el = $('#' + selector)
        el.insertAfter(el.next());
    }
</script>