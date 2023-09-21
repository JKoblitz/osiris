<?php
/**
 * Page to search for experts
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /expertise
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

 $users = $osiris->accounts->distinct('username', ['is_active' => true]);
// dump($users);
$cursor = $osiris->persons->aggregate([
    [
        '$match' => [
            'expertise' => ['$exists' => true],
            'username' => ['$in' => $users]
        ]
    ],
    ['$project' => ['expertise' => 1, 'displayname' => 1, 'dept' => 1, 'username'=>1]],
    ['$unwind' => '$expertise'],
    [
        '$group' => [
            '_id' => ['$toLower' => '$expertise'],
            'count' => ['$sum' => 1],
            'users' => ['$push' => '$$ROOT']
        ]
    ],
    ['$sort' => ['count' => -1]],
    // [ '$limit' => 100 ]
]);

?>

<div class="content">
    <h1 class="mt-0">
        <i class="fal ph-lg ph-barbell text-osiris"></i>
        <?=lang('Expertise search', 'Experten-Suche')?>
    </h1>

    <div class="form-group with-icon">
        <input class="form-control mb-20" type="search" name="search" id="search" oninput="filterFAQ(this.value);" placeholder="Filter ...">
        <i class="ph ph-x" onclick="$(this).prev().val('');filterFAQ('')"></i>
    </div>
</div>

<div class="row row-eq-spacing-md">

    <?php foreach ($cursor as $doc) { ?>
        <div class="col-md-6 col-xl-4 expertise">
            <div class="box mt-0">
                <div class="content">
                    <h3 class="title"><?= strtoupper($doc['_id']) ?></h3>
                    <p class="text-muted"><?= $doc['count'] ?> <?= lang('experts found:', 'Experten gefunden:') ?></p>
                    <?php foreach ($doc['users'] as $u) { ?>
                        <a href="<?= ROOTPATH ?>/profile/<?= $u['username'] ?>" class="badge badge-<?= $u['dept'] ?>"><?= $u['displayname'] ?></a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>


<script>
    function filterFAQ(input) {
        if (input == "") {
            $('.expertise').show()
            return
        }
        input = input.toUpperCase()
        console.log(input);
        $('.expertise').hide()
        $('.expertise:contains("' + input + '")').show()
    }
</script>