<?php

/**
 * Page to see all concepts
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /concepts
 *
 * @package     OSIRIS
 * @since       1.3.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>

<h1 class="mt-0">
    <i class="ph ph-lightbulb text-osiris" aria-hidden="true"></i>
    <?= lang('Concepts', 'Konzepte') ?>
</h1>

<table class="table dataTable" id="concept-table">
    <thead>
        <th><?=lang('Concept', 'Konzept')?></th>
        <th>Level</th>
        <th>Score</th>
        <th>Wikidata <i class="ph ph-arrow-up-right"></i></th>
    </thead>
        <?php
        $concepts = $osiris->activities->aggregate([
            [ '$match' => ['concepts' => ['$exists'=>true]]],
            ['$project' => ['concepts' => 1]],
            ['$unwind' => '$concepts'],
            [
                '$group' => [
                    '_id' => '$concepts.display_name',
                    'level'=> ['$first'=> '$concepts.level'],
                    'wikidata'=> ['$first'=> '$concepts.wikidata'],
                    'score' => ['$sum' => '$concepts.score']
                ]
            ],
            [ '$match' => ['score' => ['$gte'=> 1]]],
            ['$sort' => ['score' => -1]],
            // [ '$limit' => 100 ]
        ]);
        ?>

       <tbody>
       <?php foreach ($concepts as $concept) { ?>
            <tr>
                <td><a href="<?=ROOTPATH?>/concepts/<?=urlencode($concept['_id'])?>"><?=$concept['_id']?></a></td>
                <td><?=$concept['level']?></td>
                <td><?=round($concept['score'])?></td>
                <td><a href="<?=$concept['wikidata']?>" target="_blank" rel="noopener noreferrer"><?=str_replace('https://www.wikidata.org/wiki/','',$concept['wikidata'])?></a></td>
            </tr>
        <?php } ?>
       </tbody>
        
</table>



<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>


<script>
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control sm d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
    var dataTable;
    $(document).ready(function() {
        dataTable = $('#concept-table').DataTable({
            // searchPanes: {
            //     viewTotal: true,
            //     columns: [6]
            // },
            // searchPanes: true,
            // dom: 'Plfrtip',
            dom: 'frtipP',
            columnDefs: [{
                    targets: [0, 1, 2, 3],
                    searchable: true,
                    sortable: true,
                    visible: true
                },
                // {
                //     targets: [8, 9],
                //     searchable: true,
                //     visible: false
                // },
                // {
                //     targets: [7],
                //     searchable: true,
                //     visible: false,
                // },
            ],
            "order": [
                [2, 'desc'],
            ],
            // "search": {
            //     "search": "1"
            // }
        });
    });
</script>

