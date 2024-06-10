<?php

/**
 * Page to see all activities
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024, Julia Koblitz
 * 
 * @link /activities
 * @link /my-activities
 *
 * @package OSIRIS
 * @since 1.0 
 * 
 * @copyright	Copyright (c) 2024, Julia Koblitz
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
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

<script>
    const CARET_DOWN = ' <i class="ph ph-caret-down"></i>';
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