<?php
/**
 * Page to browse through journals
 * 
 * This file is part of the OSIRIS package.
 * Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * 
 * @link        /journal
 *
 * @package     OSIRIS
 * @since       1.0.0
 * 
 * @copyright	Copyright (c) 2024 Julia Koblitz, OSIRIS Solutions GmbH
 * @author		Julia Koblitz <julia.koblitz@osiris-solutions.de>
 * @license     MIT
 */

if ($Settings->hasPermission('journals.edit')) { ?>
    <a href="<?= ROOTPATH ?>/journal/add" class="btn osiris float-right"><?= lang('Add Journal', 'Journal hinzufügen') ?></a>
<?php } ?>


<h1 class="mt-0">
    <i class="ph ph-book-open-cover text-osiris mr-5"></i>
   <?=lang('Journals', 'Journale')?>
</h1>



<table class="table" id="result-table">
    <thead>
        <th>Journal name</th>
        <th>Abbr</th>
        <th>Publisher</th>
        <th>ISSN</th>
        <th>OA</th>
        <th><span data-toggle="tooltip" data-title="Latest impact factor if available">IF</span></th>
        <th><span data-toggle="tooltip" data-title="Publications, Reviews and Editorials"><?=lang('Activities', 'Aktivitäten')?></span></th>
    </thead>
    <tbody>
    </tbody>
</table>


<script src="<?= ROOTPATH ?>/js/datatables/jquery.dataTables.naturalsort.js"></script>


<script>
    var dataTable;
    $(document).ready(function() {
        // dataTable = $('#result-table').DataTable({
        //     "order": [
        //         [0, 'asc'],
        //     ]
        // });
        $('#result-table').DataTable({
            ajax: ROOTPATH + '/api/journals',
            columnDefs: [{
                    "targets": 0,
                    "data": "name",
                    "render": function(data, type, full, meta) {
                        return `<a href="${ROOTPATH}/journal/view/${full.id}">${data}</a>`;
                    }
                },
                {
                    targets: 1,
                    data: 'abbr'
                },
                {
                    targets: 2,
                    data: 'publisher'
                },
                {
                    targets: 3,
                    data: 'issn'
                },
                {
                    targets: 4,
                    data: 'open_access'
                },
                {
                    type: 'natural',
                    targets: 5,
                    data: 'if'
                },
                {
                    type: 'natural',
                    targets: 6,
                    data: 'count'
                },
            ],
            "order": [
                [6, 'desc'],
            ],
            <?php if (isset($_GET['q'])) { ?> "oSearch": {
                    "sSearch": "<?= $_GET['q'] ?>"
                }
            <?php } ?>
        });

    });
</script>