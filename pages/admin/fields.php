<?php

/**
 * Page to see and edit custom fields
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /admin/fields
 *
 * @package     OSIRIS
 * @since       1.3.1
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

?>

<h1>
    <i class="ph ph-textbox"></i>
    Custom fields
</h1>

<div class="btn-toolbar">
    <a class="btn" href="<?= ROOTPATH ?>/admin/fields/new">
        <i class="ph ph-plus-circle"></i>
        <?= lang('Add field', 'Feld hinzufügen') ?>
    </a>
</div>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Format</th>
            <th>Name</th>
            <th></th>
        </tr>
    </thead>
    <?php foreach ($osiris->adminFields->find() as $field) { ?>
        <tr>
            <td>
                <?= $field['id'] ?>
            </td>
            <td>
                <?= $field['format'] ?>
            </td>
            <td>
                <?= lang($field['name'], $field['name_de']) ?>
            </td>
            <td class="unbreakable">
                <form action="<?= ROOTPATH ?>/crud/fields/delete/<?= $field['_id'] ?>" method="post" class="d-inline">
                    <button type="submit" class="btn link"><i class="ph ph-trash text-danger"></i></button>
                </form>
                <a href="<?= ROOTPATH ?>/admin/fields/<?= $field['id'] ?>">
                    <i class="ph ph-pencil"></i>
                </a>
            </td>
        </tr>
    <?php } ?>
</table>