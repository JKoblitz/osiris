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
<a class="btn" href="<?= ROOTPATH ?>/admin/categories/new">
    <i class="ph ph-plus-circle"></i>
    <?= lang('Add category', 'Kategorie hinzufügen') ?>
</a>

<?php foreach ($Categories->categories as $type) { ?>
    <div class="box px-20 py-10 mb-10">
        <h3 class="title" style="color: <?= $type['color'] ?? 'inherit' ?>">
            <i class="ph ph-<?= $type['icon'] ?? 'placeholder' ?> mr-10"></i>
            <?= lang($type['name'], $type['name_de'] ?? $type['name']) ?>
        </h3>
        <a href="<?= ROOTPATH ?>/admin/categories/<?= $type['id'] ?>">
            <?= lang('Edit', 'Bearbeiten') ?>
        </a>

        <hr>
        <h5><?= lang('Types', 'Typen') ?>:</h5>
        <ul class="horizontal mb-0">
            <?php
            $children = $osiris->adminTypes->find(['parent' => $type['id']]);
            foreach ($children as $subtype) { ?>
                <li>
                    <a href="<?= ROOTPATH ?>/admin/types/<?= $subtype['id'] ?>">
                        <i class="ph ph-<?= $subtype['icon'] ?? 'placeholder' ?>"></i>
                        <?= lang($subtype['name'], $subtype['name_de'] ?? $subtype['name']) ?>
                    </a>
                </li>
            <?php } ?>
            <li>
                <a class="btn text-<?= $type['id'] ?>" href="<?= ROOTPATH ?>/admin/types/new?parent=<?= $type['id'] ?>">
                    <i class="ph ph-plus-circle"></i>
                    <span class="sr-only">
                        <?= lang('Add subtype', 'Neuen Typ hinzufügen') ?>
                    </span>
                </a>
            </li>
        </ul>
    </div>
<?php } ?>