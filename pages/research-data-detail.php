<?php

/**
 * Page to see all connected research data
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link        /research-data/<name>
 *
 * @package     OSIRIS
 * @since       1.2.0
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>

<h1 class="mt-0">
    <i class="ph ph-circles-three-plus text-osiris" aria-hidden="true"></i>
    <?= lang('Tags on', 'Schlagwörter zu') ?>
    <span class="badge primary"><?= $name ?></span>
</h1>

<table class="table dataTable" id="result-table" id="data-table">
    <thead>
        <th>Name</th>
        <th>Link</th>
        <th><?=lang('Year', 'Jahr')?></th>
        <th>Doc</th>
    </thead>
    <tbody>
        <?php
        $entityR = new MongoDB\BSON\Regex('^' . $name . '$', 'i');
        $result = $osiris->activities->find(['connections.entity' => $entityR]);
        $result = $DB->doc2Arr($result);
        // $result = $osiris->activities->aggregate([
        //     [
        //         '$match' => ['connections.entity' => $entityR]
        //     ],
        //     ['$project' => ['connections' => 1]],
        //     ['$unwind' => '$connections'],
        //     [
        //         '$group' => [
        //             '_id' => ['$toLower' => '$connections.name'],
        //             'count' => ['$sum' => 1],
        //             'activities' => ['$push' => '$$ROOT']
        //         ]
        //     ],
        //     ['$sort' => ['count' => -1]],
        //     // [ '$limit' => 100 ]
        // ]);
        foreach ($result as $doc) {
            foreach ($doc['connections'] as $con) {
                if (strtolower($con['entity']) != $name) continue;
        ?>
                <tr class="">
                    <td><?= $con['name'] ?></td>
                    <td><a href="<?= $con['link'] ?>" target="_blank" rel="noopener noreferrer"><?= $con['link'] ?></a></td>
                    <td><?=$doc['year']?></td>
                    <td class="unbreakable">
                        <?= $doc['rendered']['icon'] ?>
                        <a class="btn link square" href="<?= ROOTPATH ?>/activities/view/<?= $doc['_id'] ?>">
                            <i class="ph ph-arrow-fat-line-right"></i>
                        </a>
                    </td>
                </tr>
        <?php
            }
        }
        ?>
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
        dataTable = $('#result-table').DataTable({
            // searchPanes: {
            //     viewTotal: true,
            //     columns: [6]
            // },
            // searchPanes: true,
            // dom: 'Plfrtip',
            dom: 'frtipP',
            columnDefs: [{
                    targets: [0, 1, 2],
                    searchable: true,
                    sortable: true,
                    visible: true
                },
                {
                    targets: [3],
                    searchable: false,
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
                [0, 'asc'],
            ],
            // "search": {
            //     "search": "1"
            // }
        });
    });
</script>

