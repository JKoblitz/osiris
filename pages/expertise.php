<?php

// $osiris->users->distinct('expertise')

$cursor = $osiris->users->aggregate([
    [
        '$match' => [
            'expertise' => ['$exists' => true]
        ]
    ],
    ['$project' => ['expertise' => 1, 'displayname' => 1, 'dept' => 1]],
    ['$unwind' => '$expertise'],
    [
        '$group' => [
            '_id' => ['$toLower' => '$expertise'],
            'count' => ['$sum' => 1],
            'users' => ['$push' => '$$ROOT']
        ]
    ],
    // [
    //     '$match'=> [
    //         'count'=> [ '$gte'=> 2 ]
    //     ]
    // ],
    ['$sort' => ['count' => -1]],
    // [ '$limit' => 100 ]
]);

// foreach ($cursor as $doc) {
//     dump($doc, true);
// }



?>

<div class="content">
    <div class="form-group with-icon">
        <input class="form-control mb-20" type="search" name="search" id="search" oninput="filterFAQ(this.value);" placeholder="Filter ...">
        <i class="fas fa-xmark" onclick="$(this).prev().val('');filterFAQ('')"></i>
    </div>
</div>

<div class="row row-eq-spacing-md">

    <?php foreach ($cursor as $doc) { ?>
        <div class="col-md-6 col-xl-4 expertise">
            <div class="box">
                <div class="content">
                    <h3 class="title text-monospace"><?= $doc['_id'] ?></h3>
                    <p class="text-muted"><?= $doc['count'] ?> <?= lang('experts found:', 'Experten gefunden:') ?></p>
                    <?php foreach ($doc['users'] as $u) { ?>
                        <a href="<?= ROOTPATH ?>/profile/<?= $u['_id'] ?>" class="badge badge-<?= $u['dept'] ?>"><?= $u['displayname'] ?></a>
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
        input = input.toLowerCase()
        $('.expertise').hide()
        $('.expertise:contains("' + input + '")').show()
    }
</script>