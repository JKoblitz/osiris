
<?php if ($USER['is_controlling'] || $USER['is_admin']) { ?>
    <a href="<?= ROOTPATH ?>/journal/add" class="btn btn-osiris float-right"><?= lang('Add Journal', 'Journal hinzufügen') ?></a>
<?php } ?>


<h2 class="mt-0">
    <i class="far fa-book-open-cover text-osiris mr-5"></i>
   <?=lang('Journals', 'Journale')?>
</h2>



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


<script src="<?= ROOTPATH ?>/js/jquery.dataTables.min.js"></script>
<script src="<?= ROOTPATH ?>/js/jquery.dataTables.naturalsort.js"></script>


<script>
    $.extend($.fn.DataTable.ext.classes, {
        sPaging: "pagination mt-10 ",
        sPageFirst: "direction ",
        sPageLast: "direction ",
        sPagePrevious: "direction ",
        sPageNext: "direction ",
        sPageButtonActive: "active ",
        sFilterInput: "form-control form-control-sm d-inline w-auto ml-10 ",
        sLengthSelect: "form-control form-control-sm d-inline w-auto",
        sInfo: "float-right text-muted",
        sLength: "float-right"
    });
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