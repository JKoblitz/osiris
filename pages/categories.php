<?php

/**
 * Page to browse all user groups
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /groups
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>

<h1>
    <i class="ph ph-gear"></i>
    <?= lang('Categories', 'Kategorien') ?>
</h1>
<?php foreach ($Categories->categories as $type) { ?>
    <div class="alert mb-10">
        <h3 class="title" style="color: <?= $type['color'] ?? 'inherit' ?>">
            <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> mr-10"></i>
            <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
        </h3>
        <a href="<?= ROOTPATH ?>/admin/categories/<?= $type['id'] ?>">
            <?= lang('Edit', 'Bearbeiten') ?>
        </a>

        <hr>
        <b><?= lang('Subcategories', 'Unterkategorien') ?>:</b>
        <ul class="horizontal">
            <?php foreach ($type['children'] as $subtype) { ?>
                <li>

                    <i class="ph ph-<?= $subtype['icon'] ?? 'placeholder' ?>"></i>
                    <?= lang($subtype['name'], $subtype['name_de'] ?? $subtype['name']) ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>