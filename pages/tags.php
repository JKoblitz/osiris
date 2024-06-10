<?php

/**
 * Page to see all connected research data
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link        /tags
 *
 * @package     OSIRIS
 * @since       1.2.0
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

$Format = new Document(true);
$form = $form ?? array();

function val($index, $default = '')
{
    $val = $GLOBALS['form'][$index] ?? $default;
    if (is_string($val)) {
        return htmlspecialchars($val);
    }
    return $val;
}

?>

<h1 class="mt-0">
    <i class="ph ph-circles-three-plus text-osiris" aria-hidden="true"></i>
    <?= lang('Tags', 'Schlagwörter') ?>
</h1>

<div class="row row-eq-spacing-md">

    <?php
    // $cons = $osiris->activities->find(['connections'=> ['$ne'=> null]]);

    $cons = $osiris->activities->aggregate([
        [
            '$match' => ['connections' => ['$ne' => null]]
        ],
        ['$project' => ['connections' => 1]],
        ['$unwind' => '$connections'],
        [
            '$group' => [
                '_id' => ['$toLower' => '$connections.entity'],
                'count' => ['$sum' => 1],
                'doc' => ['$push' => '$$ROOT']
            ]
        ],
        ['$sort' => ['count' => -1]],
        // [ '$limit' => 100 ]
    ]);
    foreach ($cons as $con) {
        // dump($con, true);
    ?>
        <div class="col-md-6 col-lg-4">
            <div class="box" id="<?= $con['_id'] ?>">
                <div class="content">
                    <h5 class="mt-0">
                        <a href="<?=ROOTPATH?>/tags/<?= $con['_id'] ?>" class="link"><?= $con['_id'] ?></a>
                        <span class="badge primary float-right"><?= $con['count'] ?></span>
                    </h5>
                </div>
                <hr class="mb-10">
                <div class="content my-10">
                    <a onclick="$(this).hide().next().removeClass('hidden')"><?= lang('Show examples', 'Zeige Beispiele') ?></a>
                    <table class="w-full hidden">
                        <?php foreach ($con['doc'] as $i => $doc) :
                        if ($i >= 5) break;
                        ?>
                            <tr>
                                <td>
                                    <?php if (!empty($doc['connections']['link'])) { ?>
                                        <a href="<?= $doc['connections']['link'] ?>" class="badge " target="_blank">
                                            <i class="ph ph-link text-secondary" style="line-height: 0;"></i>
                                            <?= $doc['connections']['name'] ? $doc['connections']['name'] : $doc['connections']['link'] ?>
                                        </a>
                                    <?php } else { ?>
                                        <span class="badge "><?= $doc['connections']['name'] ?></span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a class="btn link square" href="<?=ROOTPATH?>/activities/view/<?=$doc['_id']?>">
                                        <i class="ph ph-arrow-fat-line-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>

            </div>
        </div>
    <?php } ?>

</div>