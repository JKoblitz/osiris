<?php

/**
 * Page to see all activities
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2023, Julia Koblitz
 * 
 * @link /activities
 * @link /my-activities
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2023, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@dsmz.de>
 * @license     MIT
 */

?>

<div class="container">


    <h1 class='m-0'>
        <i class="ph ph-book-open"></i>
        <?= lang("All activities", "Alle Aktivitäten") ?>
    </h1>

    <div class="mt-20">

        <table class="table dataTable responsive" id="result-table">
            <thead>
                <tr>
                    <th><?= lang('Type', 'Typ') ?></th>
                    <th><?= lang('Activity', 'Aktivität') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>

</div>
<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.min.js"></script>

<script>
    const CARET_DOWN = ' <i class="ph ph-caret-down"></i>';
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
    var rootpath = '<?=ROOTPATH?>'
    $(document).ready(function() {
        dataTable = $('#result-table').DataTable({
            "ajax": {
                "url": rootpath + '/api/all-activities',
                "data": {
                    "page": 'portal',
                    'display_activities': 'web',
                    path: '<?=PORTALPATH?>',
                    type: {'$in': ['publication', 'poster', 'lecture', 'software', 'award']}
                },
                dataSrc: 'data'
            },
            deferRender: true,
            columnDefs: [
                {
                    targets: 0,
                    data: 'icon'
                },
                {
                    targets: 1,
                    data: 'activity'
                },
                {
                    targets: 2,
                    data: 'search-text',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 3,
                    data: 'start',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 4,
                    data: 'end',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 5,
                    data: 'departments',
                    searchable: true,
                    visible: false,
                },
                {
                    targets: 6,
                    data: 'epub',
                    searchable: true,
                    visible: false,
                }
            ],
            "order": [
                [3, 'desc'],
                [0, 'asc']
            ],
        });

    });
</script>