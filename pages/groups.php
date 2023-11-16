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

include_once BASEPATH . "/php/Groups.php";
$Groups = new Groups;
?>
<style>
    
    .badge {
        background: var(--department-color);
        color: white;
        opacity: .7;
    }
</style>

<h1>
    <i class="ph ph-student"></i>
    <?= lang('Organisational Units', 'Struktureinheiten') ?>
</h1>
<a href="<?=ROOTPATH?>/groups/new"><i class="ph ph-plus"></i> <?=lang('New unit', 'Neue Einheit')?></a>


<?php if (isset($_GET['hirarchy'])) { ?>
    <a href="?" class="btn float-right active">
        <?= lang('Card View', 'Kartenansicht') ?>
    </a>
    <?php
    echo $Groups->getHirarchy();
    ?>
<?php } else { ?>
    <a href="?hirarchy" class="btn float-right">
        <?= lang('Hirarchy View', 'Hirarchische Ansicht') ?>
    </a>

    <div class="row row-eq-spacing">
        <?php foreach ($Groups->groups as $group) { ?>
            <div class="col-md-6 col-lg-4 mb-20">
                <div class="alert h-full" id="<?= $group['id'] ?>" <?= $Groups->cssVar($group['id']) ?>>
                    <a class="title link" href="<?=ROOTPATH?>/groups/view/<?=$group['id']?>">
                        <span class="badge"><?= $group['id'] ?></span>
                        <?= $group['name'] ?>
                    </a>

                    <?php if (!empty($group['parent'])) { ?>
                        <p>
                            <a href="#<?= $group['parent'] ?>"><?= $Groups->getName($group['parent']) ?></a>
                        </p>
                    <?php } ?>

                    <p class="text-muted">
                        <?= $group['unit'] ?>
                    </p>
                    <?=$osiris->persons->count(['depts'=>$group['id']])?> <?=lang('Members', 'Mitglieder')?>
                </div>
            </div>
        <?php } ?>
    </div>

<?php } ?>