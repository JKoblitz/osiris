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
<ul>
    <?php foreach ($Categories->tree as $type) { ?>
        <li>
            <a href="<?=ROOTPATH?>/admin/categories/1/<?= $type['id'] ?>" class="font-weight-bold" style="color: <?= $type['color'] ?? 'inherit' ?>">
                <?= lang($type['name'], $type['name_de'] ?? null) ?>
            </a>
            <ul>
                <?php foreach ($type['children'] as $subtype) { ?>
                    <li>
                        <a href="<?=ROOTPATH?>/admin/categories/2/<?= $subtype['id'] ?>" class="" style="color: <?= $type['color'] ?? 'inherit' ?>">
                            <?= lang($subtype['name'], $subtype['name_de'] ?? null) ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        </li>
    <?php } ?>
</ul>